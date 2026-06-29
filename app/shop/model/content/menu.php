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
    /**
     * ModelContentMenu::getLinks()
     * 
     * @param int $menu_id
     * @see DB
     * @return array sql record
     */
    public function getLinks($menu_id,$parent_id) {
        return $this->getAllItems(array(
            'menu_id'=>$menu_id,
            'parent_id'=>$parent_id
        ));;
    }

    public function getAllItems($data=null) {
        $cache_prefix = "menu_links";
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
                    'keyword'       => ($keyword->row['keyword']??'')
                );
                $links[$k]['class_css'] = $this->getProperty($v['menu_link_id'], 'menu_link', 'class_css');
                $links[$k]['descriptions'] = $this->getDescriptions($v['menu_link_id']);
            }
            $this->cache->set($cachedId, $links);
            return $links;
        } else {
            return $cached;
        }
    }

    public function getAllItemsTotal($data=null) {
        $cache_prefix = "menu_links.total";
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
                $sql .= (isset($data['sort']) && in_array($data['sort'], $sort_data)) ? " ORDER BY " . $data['sort'] : " ORDER BY ml.sort_order";
                $sql .= (isset($data['order']) && $data['order'] == 'DESC') ? " DESC" : " ASC";
            }

            if (isset($data['start']) && isset($data['limit'])) {
                if ($data['start'] < 0) $data['start'] = 0;
                if (!$data['limit']) $data['limit'] = 24;

                $sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
            } elseif (isset($data['limit'])) {
                if (!$data['limit']) $data['limit'] = 24;

                $sql .= " LIMIT ". (int)$data['limit'];
            }
        }
        return $sql;
    }

    /**
     * ModelContentMenu::getMenu()
     * 
     * @param int $menu_id
     * @see DB
     * @return array sql record
     */
    public function getMenu($menu_id) {
        $query = $this->db->query("SELECT * 
        FROM " . DB_PREFIX . "menu_link ml 
        LEFT JOIN " . DB_PREFIX . "menu_to_store m2s ON (ml.menu_id = m2s.menu_id) 
        WHERE  menu_id = '" . (int)$menu_id . "' 
        AND m2s.store_id = '". (int)STORE_ID ."'");
        
        foreach ($query->rows as $value) {
            $keyword = $this->db->query("SELECT keyword 
            FROM " . DB_PREFIX . "url_alias 
            WHERE query = '" . $this->db->escape($value['link']) . "'
            AND language_id = '". (int)$this->config->get('config_language_id') ."'");
            $links[] = array(
                'menu_id'   =>$value['menu_id'],
                'parent_id' =>$value['parent_id'],
                'link'      =>$value['link'],
                'sort_order'=>$value['sort_order'],
                'keyword'   => $keyword->row['keyword']
            );
        }
        
        $query2 = $this->db->query("SELECT * 
        FROM " . DB_PREFIX . "menu m 
        LEFT JOIN " . DB_PREFIX . "menu_to_store m2s ON (m.menu_id = m2s.menu_id) 
        WHERE  m.menu_id = '" . (int)$menu_id . "' 
        AND m2s.store_id = '". (int)STORE_ID ."'");
        
        $return = array(
            'menu_id'   =>$query2->row['menu_id'],
            'position'  =>$query2->row['position'],
            'route'     =>$query2->row['route'],
            'name'      =>$query2->row['name'],
            'sort_order'=>$query2->row['sort_order'],
            'links'     =>$links
        );
        
        return $return;
    } 
    
    /**
     * ModelContentMenu::getMainMenu()
     * 
     * @param int $menu_id
     * @see DB
     * @return array sql record
     */
    public function getMainMenu() {
        $query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "menu m
        LEFT JOIN " . DB_PREFIX . "menu_to_store m2s ON (m.menu_id = m2s.menu_id) 
        WHERE `default` = '1'
        AND m2s.store_id = '". (int)STORE_ID ."' ");
        return $query->row;
    }

    public function getDescriptions($id, $language_id=null) {
        return $this->__getDescriptions('menu_link', $id, $language_id);
    }

    public function setDescriptions($id, $data) {
        $descriptions = [];
        foreach ($data as $language_id => $v) {
            if (empty($v['description'])) continue;
            $descriptions[$language_id] = array(
                'description'=>$v['description']
            );
        }
        return $this->__setDescriptions('menu_link', $id, $descriptions);
    }

    public function getProperty($id, $group, $key) {
        return $this->__getProperty('menu', $id, $group, $key);
    }

    public function setProperty($id, $group, $key, $value) {
        return $this->__setProperty('menu', $id, $group, $key, $value);
    }

    public function deleteProperties($id, $group='*', $key='*') {
        return $this->__deleteProperties('menu', $id, $group, $key);
    }

    public function getAllProperties($id, $group = '*') {
        return $this->__getProperties('menu', $id, $group);
    }

    public function setAllProperties($id, $group, $data) {
        if (is_array($data) && !empty($data)) {
            $this->deleteProperties($id, $group);
            foreach ($data as $key => $value) {
                $this->setProperty($id, $group, $key, $value);
            }
        }
    }
}
