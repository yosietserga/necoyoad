<?php

class ModelStoreManufacturer extends Model
{
    public function getManufacturer($manufacturer_id)
    {
        $results = $this->getAll(array('manufacturer_id' => $manufacturer_id));
        return $results[0];
    }

    public function getManufacturers($data = array())
    {
        return $this->getAll($data);
    }

    public function getAll(array $data = [], array $options = [])
    {
            $cache_prefix = "shop.manufacturers";
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
            $sql = "SELECT DISTINCT *, m.name AS mname, m.image AS mimage FROM " . DB_PREFIX . "manufacturer m ";

            if (!isset($sort_data)) {
                $sort_data = array(
                    'm.sort_order',
                    'm.name',
                    'm.viewed'
                );
            }

            $sql .= $this->buildSQLQuery($data, $sort_data);
            $query = $this->db->query($sql);
            $this->cache->set($cachedId, $query->rows);
            return $query->rows;
        } else {
            return $cached;
        }
    }

    public function getAllTotal(array $data = []) {
            $cache_prefix = "shop.manufacturers.total";
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
            $sql = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "manufacturer m ";

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

        $sql .= " LEFT JOIN " . DB_PREFIX . "object_to_store m2s ON (m.manufacturer_id = m2s.object_id AND m2s.object_type = 'manufacturer') ";

        if (isset($data['id'])) {
            $data['manufacturer_id'] = !is_array($data['id']) && !empty($data['id']) ? array($data['id']) : $data['id'];
        } elseif (isset($data['manufacturer_id'])) {
            $data['manufacturer_id'] = !is_array($data['manufacturer_id']) && !empty($data['manufacturer_id']) ? array($data['manufacturer_id']) : $data['manufacturer_id'];
        }
        if (isset($data['product_id'])) $data['product_id'] = !is_array($data['product_id']) && !empty($data['product_id']) ? array($data['product_id']) : $data['product_id'];
        if (isset($data['category_id'])) $data['category_id'] = !is_array($data['category_id']) && !empty($data['category_id']) ? array($data['category_id']) : $data['category_id'];

        if (isset($data['manufacturer_id']) && !empty($data['manufacturer_id'])) {
            $criteria[] = " m.manufacturer_id IN (" . implode(', ', $data['manufacturer_id']) . ") ";
        }

        if (!empty($data['category_id']) || !empty($data['product_id'])) {
            $sql .= " LEFT JOIN " . DB_PREFIX . "product p ON (p.manufacturer_id = m.manufacturer_id) ";
        }

        if (!empty($data['category_id'])) {
            $sql .= " LEFT JOIN " . DB_PREFIX . "object_to_category p2c ON (p.product_id = p2c.object_id AND p2c.object_type = 'product') ";
            $sql .= " LEFT JOIN " . DB_PREFIX . "category c ON (p2c.category_id = c.category_id) ";
            $criteria[] = " c.category_id IN (" . implode(', ', $data['category_id']) . ") ";
        }

        if (!empty($data['product_id'])) {
            $criteria[] = " p.product_id IN (" . implode(', ', $data['product_id']) . ") ";
        }

        if (isset($data['properties']) && !empty($data['properties'])) {
            $sql .= " LEFT JOIN " . DB_PREFIX . "property mp ON (m.manufacturer_id = mp.object_id) ";
            $criteria[] = " mp.object_type = 'manufacturer' ";

            foreach ($data['properties'] as $key => $value) {
                $criteria[] = " LCASE(mp.`key`)  LIKE '%" . $this->db->escape(strtolower(str_replace('-',' ',$value['key']))) . "%' collate utf8_general_ci ";
                $criteria[] = " CONVERT(LCASE(mp.`value`) USING utf8) LIKE '%" . $this->db->escape(strtolower(str_replace('-',' ',$value['value']))) . "%' ";
            }
        }

        if (!empty($data['properties'])) {
            $sql .= " LEFT JOIN " . DB_PREFIX . "manufacturer_property mp ON (m.manufacturer_id = mp.manufacturer_id) ";
            foreach ($data['properties'] as $key => $value) {
                $criteria[] = " LCASE(cp.`key`)  LIKE '%" . $this->db->escape(strtolower(str_replace('-',' ',$value['key']))) . "%' collate utf8_general_ci ";
                $criteria[] = " CONVERT(LCASE(cp.`value`) USING utf8) LIKE '%" . $this->db->escape(strtolower(str_replace('-',' ',$value['value']))) . "%' ";
            }
        }

        if (!empty($data['store_id']) && is_numeric($data['store_id'])) {
            $criteria[] = " m2s.store_id = '". intval($data['store_id']) ."' ";
        } elseif (!empty($data['store_id']) && is_array($data['store_id'])) {
            $criteria[] = " m2s.store_id IN ('" . implode("','", $data['store_id']) . "') ";
        } else {
            $criteria[] = " m2s.store_id = '". (int)STORE_ID ."' ";
        }

        if ($criteria) {
            $sql .= " WHERE " . implode(" AND ",$criteria);
        }

        if (!$countAsTotal) {
            if (isset($sort_data)) {
                $sql .= " GROUP BY m.manufacturer_id";
                $sql .= (isset($data['sort']) && in_array($data['sort'], $sort_data)) ? " ORDER BY " . $data['sort'] : " ORDER BY m.sort_order";
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

    public function updateStats($id) {
        $this->db->query("UPDATE " . DB_PREFIX . "manufacturer SET viewed = (viewed + 1) WHERE manufacturer_id = '" . (int) $id . "'");
    }
    public function getProperty($id, $group, $key) {
        return $this->__getProperty('manufacturer', $id, $group, $key);
    }
    public function getAllProperties($id, $group = '*') {
        return $this->__getProperties('manufacturer', $id, $group);
    }
}
