<?php

/**
 * ModelContentBanner
 * 
 * @package NecoTienda
 * @author Yosiet Serga
 * @copyright Inversiones Necoyoad, C.A.
 * @version 1.0.0
 * @access public
 * @see Model
 */
class ModelContentBanner extends Model {

    protected string $table        = "banner";
    protected string $pkey         = "banner_id";
    protected string $object_type  = "banner";
    protected string $description_object_type  = "banner_item";

    protected array $fields = [
        "jquery_plugin" => [
            "name"      => "jquery_plugin",
            "type"      => "string",
        ],
        "name" => [
            "name"      => "name",
            "type"      => "string",
        ],
        "params" => [
            "name"      => "params",
            "type"      => "text",
        ],
        "status" => [
            "name"      => "status",
            "default"   => 1,
            "type"      => "boolean",
        ],
        "publish_date_start" => [
            "name"      => "publish_date_start",
            "type"      => "date",
        ],
        "publish_date_end" => [
            "name"      => "publish_date_end",
            "type"      => "date",
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

    public function init() {
        $this->on("save", function ($args) {
            $data = $args[0];
            //TODO: re-check this workflow
            //if ($data["action"] == "update") $this->deleteItems($data['id']);

            if ($data['items']) {
                foreach ($data['items'] as $key => $item) {
                    $item['banner_id'] = $data['id'];
                    $item['sort_order'] = $key;
                    $this->setItem($item);
                }
            }
        });

        //before delete banner
        $this->addHook("delete", function($args) {
            $data = $args[0];
            $this->deleteItems($data['id']);
        });

        //after delete banner
        $this->on("delete", function ($args) {
            $data = $args[0];
            $this->deleteItems($data['id']);
        });

    }

    /**
     * ModelContentBanner::getById()
     * 
     * @param int $id
     * @see DB
     * @see Cache
     * @return array sql record
     */
    public function getById($id) {
        $results = parent::getAll(array('banner_id'=>$id));
        $results[0]['banner_items'] = $this->getItems(array('banner_id'=>$id));
        $results[0]['banner_stores'] = $this->getStores($id);
        $results[0]['banner_properties'] = $this->getAllProperties($id);
        return $results[0];
    }

    /**
     * ModelContentCategory::getItems()
     * 
     * @param int $id
     * @see DB
     * @return array sql records
     */
    public function getItems($data = array()) {
        $rows = $this->getAllItems($data);
        $return = [];
        foreach ($rows as $key => $result) {
            $return[$key] = $result;
            $return[$key]['descriptions'] = $this->getDescriptions($result['banner_item_id']);
            $return[$key]['properties'] = $this->getAllItemProperties($result['banner_item_id']);
        }
        return $return;
    }

    public function setItem($data) {
        if ($data['banner_item_id']) {
            $query = $this->db->query("SELECT * FROM ". DB_PREFIX ."banner_item WHERE banner_item_id = '". (int)$data['banner_item_id'] ."'"); 
            if ($query->rows) {
                $this->db->query("UPDATE " . DB_PREFIX . "banner_item SET ".
                    "`banner_id`  = '" . intval($data['banner_id']) . "', ".
                    "`sort_order` = '" . intval($data['sort_order']) . "', ".
                    (isset($data['status']) && ($data['status']===0 || $data['status']===1) ? "`status`     = '" . intval($data['status']) . "', " : "").
                    "`image`      = '" . $this->db->escape($data['image']) . "',".
                    "`link`       = '" . $this->db->escape($data['link']) . "' ".
                    "WHERE banner_item_id = '". (int)$data['banner_item_id'] ."'");
                $banner_item_id = $data['banner_item_id'];
            } else {
                $this->db->query("INSERT INTO " . DB_PREFIX . "banner_item SET ".
                    "`banner_id`  = '" . intval($data['banner_id']) . "', ".
                    "`sort_order` = '" . intval($data['sort_order']) . "', ".
                    "`status`     = '1', ".
                    "`image`      = '" . $this->db->escape($data['image']) . "',".
                    "`link`       = '" . $this->db->escape($data['link']) . "' ");
                
                $banner_item_id = $this->db->getLastId();
            }
        } else {
            $this->db->query("INSERT INTO " . DB_PREFIX . "banner_item SET ".
                "`banner_id`  = '" . intval($data['banner_id']) . "', ".
                "`sort_order` = '" . intval($data['sort_order']) . "', ".
                "`status`     = '1', ".
                "`image`      = '" . $this->db->escape($data['image']) . "',".
                "`link`       = '" . $this->db->escape($data['link']) . "' ");
            
            $banner_item_id = $this->db->getLastId();
        }

        
        $this->setDescriptions($banner_item_id, $data['descriptions']);
        $this->setAllItemProperties($banner_item_id, 'settings', $data['properties']);
        
        return $banner_item_id;
    }

    public function deleteItems($banner_id)
    {
        $this->db->query("DELETE FROM " . DB_PREFIX . "description WHERE object_id IN (SELECT banner_item_id FROM " . DB_PREFIX . "banner_item WHERE banner_id = '" . (int) $banner_id . "') AND object_type = 'banner_item' ");
        $this->db->query("DELETE FROM " . DB_PREFIX . "property WHERE object_id IN (SELECT banner_item_id FROM " . DB_PREFIX . "banner_item WHERE banner_id = '" . (int) $banner_id . "') AND object_type = 'banner_item'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "banner_item WHERE banner_id = '" . $banner_id . "'");
    }

    public function deleteItem($id) {
        $this->db->query("DELETE FROM " . DB_PREFIX . "description WHERE object_id = '" . (int) $id . "' AND object_type = 'banner_item'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "property WHERE object_id = '" . (int) $id . "' AND object_type = 'banner_item'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "banner_item WHERE banner_item_id = '" . $id . "'");
    }

    public function getDescriptions($id, $language_id=null) {
        return $this->__getDescriptions('banner_item', $id, $language_id);
    }

    public function setDescriptions($id, $data) {
        return $this->__setDescriptions('banner_item', $id, $data);
    }

    /**
     * ModelContentBanner::getAll()
     * 
     * @param mixed $data
     * @see DB
     * @see Cache
     * @return array sql records
     */
    public function getAllItems($data = array()) {

        $cache_prefix = "admin.banner_items";
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
            $sql = "SELECT * FROM " . DB_PREFIX . "banner_item t ";

            if (!isset($sort_data)) {
                $sort_data = array(
                    'sort_order'
                );
            }

            $sql .= $this->buildItemSQLQuery($data, $sort_data);
            $query = $this->db->query($sql);
            $this->cache->set($cachedId, $query->rows);
            return $query->rows;
        } else {
            return $cached;
        }
    }

    public function getAllItemsTotal($data=null) {
        $cache_prefix = "admin.banner_items.total";
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
            $sql = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "banner_item t ";
            $sql .= $this->buildItemSQLQuery($data, null, true);
            $query = $this->db->query($sql);
            $this->cache->set($cachedId, $query->row['total']);
            return $query->row['total'];
        } else {
            return $cached;
        }
    }

    private function buildItemSQLQuery($data, $sort_data = null, $countAsTotal = false) {
        $criteria = [];
        $sql = "";
        if (isset($data['banner_id'])) $data['banner_id'] = !is_array($data['banner_id']) && !empty($data['banner_id']) ? array($data['banner_id']) : $data['banner_id'];
        if (isset ($data['banner_item_id'])) $data['banner_item_id'] = !is_array($data['banner_item_id']) && !empty($data['banner_item_id']) ? array($data['banner_item_id']) : $data['banner_item_id'];

        if (isset($data['banner_item_id']) && !empty($data['banner_item_id'])) {
            $criteria[] = " t.banner_item_id IN (" . implode(', ', $data['banner_item_id']) . ") ";
        }

        if (isset($data['banner_id']) && !empty($data['banner_id'])) {
            $criteria[] = " t.banner_id IN (" . implode(', ', $data['banner_id']) . ") ";
        }

        if (!empty($data['status']) && is_numeric($data['status'])) {
            $criteria[] = " t.status = '". intval($data['status']) ."' ";
        }

        if (!empty($data['properties'])) {
            $sql .= " LEFT JOIN " . DB_PREFIX . "property pp ON (t.banner_item_id = pp.object_id) ";
            foreach ($data['properties'] as $key => $value) {
                $criteria[] = " LCASE(pp.`key`)  LIKE '%" . $this->db->escape(strtolower(str_replace('-',' ',$value['key']))) . "%' collate utf8_general_ci ";
                $criteria[] = " CONVERT(LCASE(pp.`value`) USING utf8) LIKE '%" . $this->db->escape(strtolower(str_replace('-',' ',$value['value']))) . "%' ";
                $criteria[] = " pp.object_type = 'banner_item' ";
            }
        }

        if ($criteria) {
            $sql .= " WHERE " . implode(" AND ",$criteria);
        }

        if (!$countAsTotal) {
            if (isset($sort_data)) {
                $sql .= " GROUP BY t.banner_item_id";
                if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
                    $sql .= $data['sort'] ? " ORDER BY " . $data['sort'] : " ORDER BY t.sort_order";
                }
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

    public function getItemProperty($id, $group, $key) {
        return $this->__getProperty('banner_item', $id, $group, $key);
    }

    public function setItemProperty($id, $group, $key, $value) {
        return $this->__setProperty('banner_item', $id, $group, $key, $value);
    }

    public function deleteItemProperty($id, $group='*', $key='*') {
        return $this->__deleteProperties('banner_item', $id, $group, $key);
    }

    public function getAllItemProperties($id, $group = '*') {
        return $this->__getProperties('banner_item', $id, $group);
    }

    public function setAllItemProperties($id, $group, $data) {
        if (is_array($data) && !empty($data)) {
            $this->deleteItemProperty($id, $group);
            foreach ($data as $key => $value) {
                $this->setItemProperty($id, $group, $key, $value);
            }
        }
    }
}
