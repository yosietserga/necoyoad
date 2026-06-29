<?php
/**
 * ModelContentMenu
 * 
 * @package NecoTienda
 * @author NecoTienda
 * @copyright Inversiones Necoyoad, C.A.
 * @version 1.0.0
 * @access public
 */
class ModelContentMenu extends Model {

    protected string $table        = "menu";
    protected string $pkey         = "menu_id";
    protected string $object_type  = "menu";
    protected string $description_object_type  = "menu_link";

    protected array $fields = [
        "store_id" => [
            "name"      => "store_id",
            "default"   => 0,
            "type"      => "integer",
        ],
        "name" => [
            "name"      => "name",
            "type"      => "string",
        ],
        "status" => [
            "name"      => "status",
            "default"   => 1,
            "type"      => "boolean",
        ],
        "date_added" => [
            "name"      => "date_added",
            "default"   => "NOW()",
            "type"      => "sql",
        ],
        "date_modified" => [
            "name"      => "date_modified",
            "default"   => "NOW()",
            "type"      => "sql",
            //TODO: add events dynamic, onInsert, onUpdate, onDelete, ...
        ],
    ];

    protected array $relations = ["stores"];


    public function init()
    {
        $this->on("save", function($args) {
            $data = $args[0];
            if ($data["action"] == "update") $this->deleteItems($data['id']);
            if ($data['id']) $this->setItems($data['id'], $data['data']['link']);
        });

        $this->on("delete", function ($args) {
            $data = $args[0];
            $this->deleteItems($data['id']);
        });
    }

    public function getAllItems($data=null) {
        $cache_prefix = "admin.menu_links";
        $cachedId = $cache_prefix.
            (int)STORE_ID ."_".
            serialize($data).
            $this->config->get('config_language_id') . "." .
            $this->request->getQuery('hl') . "." .
            $this->request->getQuery('cc') . "." .
            $this->config->get('config_currency') . "." .
            (int)$this->config->get('config_store_id');

        $cached = $this->cache->get($cachedId, $cache_prefix);
        if (!$cached || (bool)$this->user->getId()) {
            $sql = "SELECT * FROM " . DB_PREFIX . "menu_link ml ";

            if (!isset($sort_data)) {
                $sort_data = array(
                    'tag',
                    'sort_order'
                );
            }

            $sql .= $this->buildSQLQueryItems($data, $sort_data);
            $query = $this->db->query($sql);
            $links = [];

            foreach ($query->rows as $k => $v) {
                $keyword = $this->db->query("SELECT `keyword` FROM " . DB_PREFIX . "url_alias WHERE `query` = '" . $this->db->escape($v['link']) . "'");

                $links[$k] = array(
                    'menu_link_id'  => $v['menu_link_id'],
                    'menu_id'       => $v['menu_id'],
                    'parent_id'     => $v['parent_id'],
                    'link'          => $v['link'],
                    'tag'           => $v['tag'],
                    'sort_order'    => $v['sort_order'],
                    'keyword'       => isset($keyword->row['keyword']) ? $keyword->row['keyword'] : ''
                );
                $links[$k]['icon'] = $this->getProperty($v['menu_link_id'], 'menu_link', 'icon');
                $links[$k]['submenu_type'] = $this->getProperty($v['menu_link_id'], 'menu_link', 'submenu_type');
                $links[$k]['class_css'] = $this->getProperty($v['menu_link_id'], 'menu_link', 'class_css');
                $links[$k]['html_content'] = $this->getProperty($v['menu_link_id'], 'menu_link', 'html_content');
                $links[$k]['page_id'] = $this->getProperty($v['menu_link_id'], 'menu_link', 'page_id');
                $links[$k]['descriptions'] = $this->getDescriptions($v['menu_link_id']);
            }
            $this->cache->set($cachedId, $links);
            return $links;
        } else {
            return $cached;
        }
    }

    public function getAllItemsTotal($data=null) {
        $cache_prefix = "admin.menu_links.total";
        $cachedId = $cache_prefix.
            (int)STORE_ID ."_".
            serialize($data).
            $this->config->get('config_language_id') . "." .
            $this->request->getQuery('hl') . "." .
            $this->request->getQuery('cc') . "." .
            $this->config->get('config_currency') . "." .
            (int)$this->config->get('config_store_id');

        $cached = $this->cache->get($cachedId, $cache_prefix);
        if (!$cached || (bool)$this->user->getId()) {
            $sql = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "menu_link ml ";
            $sql .= $this->buildSQLQueryItems($data, null, true);
            $query = $this->db->query($sql);

            $this->cache->set($cachedId, $query->row['total']);

            return $query->row['total'];
        } else {
            return $cached;
        }
    }

    private function buildSQLQueryItems($data, $sort_data = null, $countAsTotal = false) {
        $criteria = [];
        $sql = "";

        if (isset($data['menu_id'])) $data['menu_id'] = !is_array($data['menu_id']) && !empty($data['menu_id']) ? array($data['menu_id']) : $data['menu_id'];
        if (isset($data['parent_id'])) $data['parent_id'] = !is_array($data['parent_id']) && (!empty($data['parent_id']) || $data['parent_id'] === 0) ? array($data['parent_id']) : $data['parent_id'];
        if (isset($data['menu_link_id'])) $data['menu_link_id'] = !is_array($data['menu_link_id']) && !empty($data['menu_link_id']) ? array($data['menu_link_id']) : $data['menu_link_id'];

        if (isset($data['menu_id']) && !empty($data['menu_id'])) {
            $criteria[] = " ml.menu_id IN (" . implode(', ', $data['menu_id']) . ") ";
        }

        if (isset($data['parent_id']) && (!empty($data['parent_id']) || $data['parent_id'] === 0)) {
            $criteria[] = " ml.parent_id IN (" . implode(', ', $data['parent_id']) . ") ";
        }

        if (isset($data['menu_link_id']) && !empty($data['menu_link_id'])) {
            $criteria[] = " ml.menu_link_id IN (" . implode(', ', $data['menu_link_id']) . ") ";
        }

        if (!empty($data['link'])) {
            $criteria[] = " LCASE(ml.`link`) LIKE '%" . $this->db->escape(strtolower($data['link'])) . "%' collate utf8_general_ci ";
        }

        if (!empty($data['tag'])) {
            $criteria[] = " LCASE(ml.`tag`) LIKE '%" . $this->db->escape(strtolower($data['tag'])) . "%' collate utf8_general_ci ";
        }

        if (!empty($data['keyword'])) {
            $criteria[] = " LCASE(ml.`keyword`) LIKE '%" . $this->db->escape(strtolower($data['keyword'])) . "%' collate utf8_general_ci ";
        }

        if (!empty($data['properties'])) {
            $sql .= " LEFT JOIN " . DB_PREFIX . "property lp ON (ml.menu_link_id = lp.object_id) ";
            foreach ($data['properties'] as $key => $value) {
                $criteria[] = " LCASE(lp.`key`)  LIKE '%" . $this->db->escape(strtolower(str_replace('-',' ',$value['key']))) . "%' collate utf8_general_ci ";
                $criteria[] = " CONVERT(LCASE(lp.`value`) USING utf8) LIKE '%" . $this->db->escape(strtolower(str_replace('-',' ',$value['value']))) . "%' ";
                $criteria[] = " lp.object_type = 'menu_link' ";
            }
        }

        if ($criteria) {
            $sql .= " WHERE " . implode(" AND ",$criteria);
        }

        if (!$countAsTotal) {
            if (isset($sort_data)) {
                $sql .= " GROUP BY ml.menu_link_id";
                $sql .= isset($data['sort']) && in_array($data['sort'], $sort_data) ? " ORDER BY " . $data['sort'] : " ORDER BY ml.sort_order";
                $sql .= isset($data['order']) && $data['order'] == 'DESC' ? " DESC" : " ASC";
            }

            if (isset($data['start']) && isset($data['limit'])) {
                if ($data['start'] < 0) $data['start'] = 0;
                if (!$data['limit']) $data['limit'] = 24;

                $sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
            } elseif (isset($data['limit'])) {
                if (!$data['limit']) $data['limit'] = 24;

                $sql .= " LIMIT " . (int)$data['limit'];
            }
        }
        return $sql;
    }

	public function setItems($menu_id, $links) {
        if ($menu_id==0) return false;
        $parent = [];
        $sort_order = 0;
        foreach ($links as $key => $link) {
            if (empty($link['link'])) continue;
            
            $index = explode("_",$key);
            $parent_id = 0;
            if (count($index) == 2) {
                $parent_id = $parent[$index[0]];
             } elseif (count($index) == 3) {
                $parent_id = $parent[$index[0] . "_" . $index[1]];
            }

            $this->db->query("INSERT INTO " . DB_PREFIX . "menu_link SET ".
                "menu_id     = '" . (int)$menu_id . "',".
                "parent_id   = '" . (int)$parent_id . "',".
                "link        = '" . $this->db->escape($link['link']) . "',".
                "sort_order  = '" . (int)$sort_order . "', ".
                "tag         = '" . $this->db->escape($link['tag']) . "'");
            
            $parent[$key] = $this->db->getLastId();
            
            $sort_order++;
            
            if (isset($link['icon']) && !empty($link['icon'])) {
                $this->setProperty($parent[$key], 'menu_link', 'icon', $link['icon']);
            }

            if (isset($link['class_css']) && !empty($link['class_css'])) {
                $this->setProperty($parent[$key], 'menu_link', 'class_css', $link['class_css']);
            }

            if (isset($link['submenu_type']) && !empty($link['submenu_type'])) {
                $this->setProperty($parent[$key], 'menu_link', 'submenu_type', $link['submenu_type']);
            }

            if (isset($link['page_id']) && !empty($link['page_id'])) {
                $this->setProperty($parent[$key], 'menu_link', 'page_id', $link['page_id']);
            }

            if (isset($link['descriptions']) && !empty($link['descriptions'])) {
                $this->setDescriptions($parent[$key], $link['descriptions']);
            }
        }
    }

    public function deleteItems($id) {
        $this->db->query("DELETE FROM " . DB_PREFIX . "property ".
            "WHERE object_id IN (".
            "SELECT menu_link_id FROM " . DB_PREFIX . "menu_link ".
            "WHERE menu_id = ". (int)$id
            .") ".
            "AND object_type = 'menu_link'");
        
        $this->db->query("DELETE FROM " . DB_PREFIX . "description ".
            "WHERE object_id IN (".
            "SELECT menu_link_id FROM " . DB_PREFIX . "menu_link ".
            "WHERE menu_id = ". (int)$id
            .") ".
            "AND object_type = 'menu_link'");

        $this->db->query("DELETE FROM " . DB_PREFIX . "menu_link ".
            "WHERE menu_id = '" . (int)$id . "'");

        $this->deleteProperty($id, 'menu_link');
    }
}
