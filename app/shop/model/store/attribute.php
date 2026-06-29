<?php /**
 * ModelStoreAttribute
 * 
 * @package NecoTienda
 * @author Yosiet Serga
 * @copyright Inversiones Necoyoad, C.A.
 * @version 1.0.0
 * @access public
 * @see Model
 */
class ModelStoreAttribute extends Model {

    /**
     * ModelStoreAttribute::getCategoriesByGroupId()
     * 
     * @param int $id
     * @see DB
     * @see Cache
     * @return array sql record
     */
    public function getCategoriesByGroupId($id) {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "object_to_category WHERE object_id = '" . (int)$id . "' AND object_type = 'attribute'");
        return $query->rows;
    }
    
    public function getAllAttributes($data=null) {
        $cache_prefix = "admin.product_attributes";
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
            $sql = "SELECT * FROM " . DB_PREFIX . "product_attribute t ";

            if (!isset($sort_data)) {
                $sort_data = array(
                    't.`group`',
                    't.label',
                    't.name',
                    't.date_added'
                );
            }
            $sql .= $this->buildSQLQueryAttributes($data, $sort_data);
            $query = $this->db->query($sql);
            $this->cache->set($cachedId, $query->rows);
            return $query->rows;
        } else {
            return $cached;
        }
    }

    public function getAllAttributesTotal($data=null) {
        $cache_prefix = "admin.product_attributes.total";
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
            $sql = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product_attribute t ";
            $sql .= $this->buildSQLQueryAttributes($data, null, true);
            $query = $this->db->query($sql);

            $this->cache->set($cachedId, $query->row['total']);

            return $query->row['total'];
        } else {
            return $cached;
        }
    }

    private function buildSQLQueryAttributes($data, $sort_data = null, $countAsTotal = false) {
        $criteria = [];
        $sql = "";

        $data['product_attribute_group_id'] = !is_array($data['product_attribute_group_id']) && !empty($data['product_attribute_group_id']) ? array($data['product_attribute_group_id']) : $data['product_attribute_group_id'];
        $data['product_attribute_id'] = !is_array($data['product_attribute_id']) && !empty($data['product_attribute_id']) ? array($data['product_attribute_id']) : $data['product_attribute_id'];
        $data['store_id'] = !is_array($data['store_id']) && !empty($data['store_id']) ? array($data['store_id']) : $data['store_id'];

        if (isset($data['product_attribute_id']) && !empty($data['product_attribute_id'])) {
            $criteria[] = " t.product_attribute_id IN (" . implode(', ', $data['product_attribute_id']) . ") ";
        }

        if (isset($data['product_attribute_group_id']) && !empty($data['product_attribute_group_id'])) {
            $criteria[] = " t.product_attribute_group_id IN (" . implode(', ', $data['product_attribute_group_id']) . ") ";
        }

        if (isset($data['group']) && !empty($data['group'])) {
            $criteria[] = " LCASE(t.`group`) LIKE '%" . $this->db->escape(strtolower($data['group'])) . "%' collate utf8_general_ci ";
        }

        if (isset($data['name']) && !empty($data['name'])) {
            $criteria[] = " LCASE(t.`name`) LIKE '%" . $this->db->escape(strtolower($data['name'])) . "%' collate utf8_general_ci ";
        }

        if (isset($data['label']) && !empty($data['label'])) {
            $criteria[] = " LCASE(t.`label`) LIKE '%" . $this->db->escape(strtolower($data['label'])) . "%' collate utf8_general_ci ";
        }

        if (isset($data['type']) && !empty($data['type'])) {
            $criteria[] = " LCASE(t.`type`) LIKE '%" . $this->db->escape(strtolower($data['type'])) . "%' collate utf8_general_ci ";
        }

        if (!empty($data['date_start']) && !empty($data['date_end'])) {
            $criteria[] = " t.date_added BETWEEN '". $this->db->escape($data['date_start']) ."' AND '". $this->db->escape($data['date_end']) ."'";
        } elseif (!empty($data['date_start']) && empty($data['date_end'])) {
            $criteria[] = " t.date_added BETWEEN '". $this->db->escape($data['date_start']) ."' AND '". $this->db->escape(date('Y-m-d h:i:s')) ."'";
        }

        if ($criteria) {
            $sql .= " WHERE " . implode(" AND ",$criteria);
        }

        if (!$countAsTotal) {
            if (isset($sort_data)) {
                $sql .= " GROUP BY t.product_attribute_id";
                $sql .= (in_array($data['sort'], $sort_data)) ? " ORDER BY " . $data['sort'] : " ORDER BY t.name";
                $sql .= ($data['order'] == 'DESC') ? " DESC" : " ASC";
            }

            if ($data['start'] && $data['limit']) {
                if ($data['start'] < 0) $data['start'] = 0;
                if (!$data['limit']) $data['limit'] = 24;

                $sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
            } elseif ($data['limit']) {
                if (!$data['limit']) $data['limit'] = 24;

                $sql .= " LIMIT ". (int)$data['limit'];
            }
        }
        return $sql;
    }

    public function getByID($id) {
        $result = $this->getAll(array(
            'product_attribute_group_id'=>$id,
        ));
        return $result[0];
    }

    public function getAll(array $data = [], array $options = []) {
        $cache_prefix = "admin.product_attribute_groups";
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
            $sql = "SELECT * FROM " . DB_PREFIX . "product_attribute_group t ";

            if (!isset($sort_data)) {
                $sort_data = array(
                    't.name',
                    't.date_added'
                );
            }
            $sql .= $this->buildSQLQuery($data, $sort_data);
            $query = $this->db->query($sql);
            $return = [];
            foreach ($query->rows as $row) {
                $row['categories'] = $this->getCategoriesByGroupId($row['product_attribute_group_id']);
                $row['attributes'] = $this->getAllAttributes(array(
                    'product_attribute_group_id'=>$row['product_attribute_group_id']
                ));
                $return[] = $row;
            }
            $this->cache->set($cachedId, $return);
            return $return;
        } else {
            return $cached;
        }
    }

    public function getAllTotal($data=null) {
        $cache_prefix = "admin.product_attribute_groups.total";
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
            $sql = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product_attribute_group t ";
            $sql .= $this->buildSQLQuery($data, null, true);
            $query = $this->db->query($sql);

            $this->cache->set($cachedId, $query->row['total']);

            return $query->row['total'];
        } else {
            return $cached;
        }
    }

    protected function buildSQLQuery(array $data, $sort_data = null, $countAsTotal = false):string {
        $criteria = [];
        $sql = "";

        if (isset($data['id'])) {
            $data['product_attribute_group_id'] = !is_array($data['id']) && !empty($data['id']) ? array($data['id']) : $data['id'];
        } else {
            $data['product_attribute_group_id'] = !is_array($data['product_attribute_group_id']) && !empty($data['product_attribute_group_id']) ? array($data['product_attribute_group_id']) : $data['product_attribute_group_id'];
        }
        $data['category_id'] = !is_array($data['category_id']) && !empty($data['category_id']) ? array($data['category_id']) : $data['category_id'];

        if (isset($data['category_id']) || isset($data['category'])) {
            $sql .= "LEFT JOIN " . DB_PREFIX . "product_attribute_to_category a2c ON (a2c.product_attribute_group_id = t.product_attribute_group_id) ";
        }

        if (isset($data['product_attribute_group_id']) && !empty($data['product_attribute_group_id'])) {
            $criteria[] = " t.product_attribute_group_id IN (" . implode(', ', $data['product_attribute_group_id']) . ") ";
        }

        if (isset($data['category_id']) && !empty($data['category_id'])) {
            $criteria[] = " a2c.category_id IN (" . implode(', ', $data['category_id']) . ") ";
        }

        if (isset($data['name']) && !empty($data['name'])) {
            $criteria[] = " LCASE(t.`name`) LIKE '%" . $this->db->escape(strtolower($data['name'])) . "%' collate utf8_general_ci ";
        }

        if (!empty($data['status']) && is_numeric($data['status'])) {
            $criteria[] = " t.status = '". intval($data['status']) ."' ";
        }

        if (isset($data['category']) && !is_null($data['category'])) {
            $sql .= "LEFT JOIN " . DB_PREFIX . "description cd ON (a2c.category_id = cd.object_id) ";
            $criteria[] = " LCASE(cd.`name`) LIKE '%" . $this->db->escape(strtolower($data['category'])) . "%' ";
            $criteria[] = " cd.object_type = 'category' ";
        }
        
        if (!empty($data['properties'])) {
            $sql .= " LEFT JOIN " . DB_PREFIX . "property pp ON (t.product_attribute_group_id = pp.object_id) ";
            foreach ($data['properties'] as $key => $value) {
                $criteria[] = " LCASE(pp.`key`)  LIKE '%" . $this->db->escape(strtolower(str_replace('-',' ',$value['key']))) . "%' collate utf8_general_ci ";
                $criteria[] = " CONVERT(LCASE(pp.`value`) USING utf8) LIKE '%" . $this->db->escape(strtolower(str_replace('-',' ',$value['value']))) . "%' ";
                $criteria[] = " pp.object_type = 'product_attribute_group' ";
            }
        }

        if (!empty($data['date_start']) && !empty($data['date_end'])) {
            $criteria[] = " t.date_added BETWEEN '". $this->db->escape($data['date_start']) ."' AND '". $this->db->escape($data['date_end']) ."'";
        } elseif (!empty($data['date_start']) && empty($data['date_end'])) {
            $criteria[] = " t.date_added BETWEEN '". $this->db->escape($data['date_start']) ."' AND '". $this->db->escape(date('Y-m-d h:i:s')) ."'";
        }

        if ($criteria) {
            $sql .= " WHERE " . implode(" AND ",$criteria);
        }

        if (!$countAsTotal) {
            if (isset($sort_data)) {
                $sql .= " GROUP BY t.product_attribute_group_id";
                $sql .= (in_array($data['sort'], $sort_data)) ? " ORDER BY " . $data['sort'] : " ORDER BY t.name";
                $sql .= ($data['order'] == 'DESC') ? " DESC" : " ASC";
            }

            if ($data['start'] && $data['limit']) {
                if ($data['start'] < 0) $data['start'] = 0;
                if (!$data['limit']) $data['limit'] = 24;

                $sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
            } elseif ($data['limit']) {
                if (!$data['limit']) $data['limit'] = 24;

                $sql .= " LIMIT ". (int)$data['limit'];
            }
        }
        return $sql;
    }

    public function getProperty($id, $group, $key) {
        return $this->__getProperty('product_attribute_group', $id, $group, $key);
    }

    public function getAllProperties($id, $group = '*') {
        return $this->__getProperties('product_attribute_group', $id, $group);
    }
}
