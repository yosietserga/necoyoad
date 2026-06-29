<?php

class ModelStoreProduct extends Model {

    protected string $object_type  = "product";
    protected string $table        = "product";
    protected string $pkey         = "product_id";

    protected array $fields = [
        "model" => [
            "name"      => "model",
            "required"  => true,
            "type"      => "string",
        ],
        "weight_class_id" => [
            "name"      => "weight_class_id",
            "type"      => "integer",
        ],
        "length_class_id" => [
            "name"      => "length_class_id",
            "type"      => "integer",
        ],
        "tax_class_id" => [
            "name"      => "tax_class_id",
            "type"      => "integer",
        ],
        "owner_id" => [
            "name"      => "owner_id",
            "type"      => "integer",
        ],
        "manufacturer_id" => [
            "name"      => "manufacturer_id",
            "type"      => "integer",
        ],
        "stock_status_id" => [
            "name"      => "stock_status_id",
            "type"      => "integer",
        ],
        "sku" => [
            "name"      => "sku",
            "type"      => "string",
        ],
        "location" => [
            "name"      => "location",
            "type"      => "string",
        ],
        "quantity" => [
            "name"      => "quantity",
            "type"      => "integer",
        ],
        "type" => [
            "name"      => "type",
            "type"      => "string",
        ],
        "image" => [
            "name"      => "image",
            "type"      => "string",
        ],
        "shipping" => [
            "name"      => "shipping",
            "type"      => "integer",
        ],
        "price" => [
            "name"      => "price",
            "type"      => "float",
        ],
        "weight" => [
            "name"      => "weight",
            "type"      => "float",
        ],
        "length" => [
            "name"      => "length",
            "type"      => "float",
        ],
        "width" => [
            "name"      => "width",
            "type"      => "float",
        ],
        "height" => [
            "name"      => "height",
            "type"      => "float",
        ],
        "viewed" => [
            "name"      => "viewed",
            "type"      => "integer",
        ],
        "subtract" => [
            "name"      => "subtract",
            "type"      => "integer",
        ],
        "minimum" => [
            "name"      => "minimum",
            "type"      => "integer",
        ],
        "cost" => [
            "name"      => "cost",
            "type"      => "float",
        ],
        "sort_order" => [
            "name"      => "sort_order",
            "type"      => "integer",
        ],
        "date_available" => [
            "name"      => "date_available",
            "type"      => "date",
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

    protected array $relations = ["categories", "descriptions", "stores"];

    
    public function getAll(array $data = [], array $options = [])
    {
        $cache_prefix = "shop.products";
        $cachedId = $cache_prefix.
            (int)STORE_ID ."_".
            serialize($data).
            $this->config->get('config_language_id') . "." .
            $this->request->getQuery('hl') . "." .
            $this->request->getQuery('cc') . "." .
            $this->customer->getId() . "." .
            $this->config->get('config_currency') . "." .
            (int)$this->config->get('config_store_id');

        $cached = $this->cache->get($cachedId, $cache_prefix);
        if (!$cached || (bool)$this->user->getId()) {
            $sql = "SELECT DISTINCT *, ".
            "(SELECT AVG(r.rating) FROM " . DB_PREFIX . "review r WHERE p.product_id = r.object_id AND r.`object_type` = 'product' GROUP BY r.object_id) AS rating,  ".
            "p.date_added AS created,  ".
            "pd.title AS name,  ".
            "pd.title AS title,  ".
            "pd.description AS description,  ".
            "pd.meta_description AS meta_description,  ".
            "pd.meta_keywords AS meta_keywords,  ".
            "pd.seo_title AS seo_title,  ".
            "p.image AS image,  ".
            "p.image AS pimage,  ".
            "m.name AS manufacturer,  ".
            "ss.ref AS stock,  ".
            //"lcd.title AS length_class, ".
            "wcd.title AS weight_class ".
                "FROM " . DB_PREFIX . "product p ".
                "LEFT JOIN " . DB_PREFIX . "status ss ON (p.stock_status_id = ss.status_id AND ss.object_type = 'stock_status') ";

            if (!isset($options['sort_data'])) {
                $options['sort_data'] = array(
                    'pd.title',
                    'p.sort_order',
                    'p.viewed',
                    'p.mailed',
                    'p.called',
                    'p.buyed',
                    'p.price'
                );
            }

            $sql .= $this->buildSQLQuery($data, $options['sort_data']);
            $query = $this->db->query($sql);
            $this->cache->set($cachedId, $query->rows, $cache_prefix);
            return $query->rows;
        } else {
            return $cached;
        }
    }

    public function getAllTotal(array $data = []) {
        $cache_prefix = "shop.products.total";
        $cachedId = $cache_prefix.
            (int)STORE_ID ."_".
            serialize($data).
            $this->config->get('config_language_id') . "." .
            $this->request->getQuery('hl') . "." .
            $this->request->getQuery('cc') . "." .
            $this->customer->getId() . "." .
            $this->config->get('config_currency') . "." .
            (int)$this->config->get('config_store_id');

        $cached = $this->cache->get($cachedId, $cache_prefix);
        if (!$cached || (bool)$this->user->getId()) {
            $sql = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product p ";

            $sql .= $this->buildSQLQuery($data, null, true);
            $query = $this->db->query($sql);

            $this->cache->set($cachedId, $query->row['total'], $cache_prefix);

            return $query->row['total'];
        } else {
            return $cached;
        }
    }

    protected function buildSQLQuery(array $data, $sort_data = null, $countAsTotal = false):string {
        $criteria = [];
        $sql = "";

        $sql .= "LEFT JOIN " . DB_PREFIX . "description pd ON (p.product_id = pd.object_id) ";
        $sql .= "LEFT JOIN " . DB_PREFIX . "manufacturer m ON (p.manufacturer_id = m.manufacturer_id) ";
        $sql .= "LEFT JOIN " . DB_PREFIX . "object_to_store p2s ON (p.product_id = p2s.object_id AND p2s.object_type = 'product') ";
        $sql .= "LEFT JOIN " . DB_PREFIX . "store s ON (s.store_id = p2s.store_id) ";

        if (isset($data['id'])) {
            $data['product_id'] = !is_array($data['id']) && !empty($data['id']) ? array($data['id']) : $data['id'];
        } elseif (isset($data['product_id'])) {
            $data['product_id'] = !is_array($data['product_id']) && !empty($data['product_id']) ? array($data['product_id']) : $data['product_id'];
        }
        if (isset($data['category_id'])) $data['category_id'] = !is_array($data['category_id']) && !empty($data['category_id']) ? array($data['category_id']) : $data['category_id'];
        if (isset($data['manufacturer_id'])) $data['manufacturer_id'] = !is_array($data['manufacturer_id']) && !empty($data['manufacturer_id']) ? array($data['manufacturer_id']) : $data['manufacturer_id'];

        $criteria[] = " pd.object_type = 'product' ";

        if (!$countAsTotal) {
            $sql .= "LEFT JOIN " . DB_PREFIX . "weight_class wc ON (p.weight_class_id = wc.weight_class_id) ";
            $sql .= "LEFT JOIN " . DB_PREFIX . "description wcd ON (p.weight_class_id = wcd.object_id) ";
            $criteria[] = " wcd.object_type = 'weight_class' ";

            //$sql .= "LEFT JOIN " . DB_PREFIX . "length_class lc ON (p.length_class_id = lc.length_class_id) ";
            //$sql .= "LEFT JOIN " . DB_PREFIX . "description lcd ON (p.weight_class_id = lcd.object_id) ";
            //$criteria[] = " lcd.object_type = 'length_class' ";
        }

        if (!empty($data['language_id']) && is_numeric($data['language_id'])) {
            $criteria[] = " pd.language_id = '". intval($data['language_id']) ."' ";   
            if (!$countAsTotal) $criteria[] = " wcd.language_id = '". intval($data['language_id']) ."' ";                 
        } else {
            $criteria[] = " pd.language_id = '" . (int)$this->config->get('config_language_id') . "' ";
            if (!$countAsTotal) $criteria[] = " wcd.language_id = '" . (int)$this->config->get('config_language_id') . "' ";
        }

        if (!empty($data['status']) && is_numeric($data['status'])) {
            $criteria[] = " p.status = '". intval($data['status']) ."' ";
        } else {
            $criteria[] = " p.status = '1' ";
        }

        if (!empty($data['date_available'])) {
            $criteria[] = " p.date_available <= '". $this->db->escape($data['date_available']) ."' ";
        } else {
            $criteria[] = " p.date_available <= NOW()";
        }

        if (!empty($data['product_id']) && empty($data['related']) && empty($data['suggested']) && empty($data['xsell'])) {
            $criteria[] = " p.product_id IN (" . implode(', ', $data['product_id']) . ") ";
        }

        if (isset($data['queries'])) {
            $search =  $search2 = '';
            foreach ($data['queries'] as $key => $value) {
                if (empty($value)) continue;
                if ($value !== mb_convert_encoding( mb_convert_encoding($value, 'UTF-32', 'UTF-8'), 'UTF-8', 'UTF-32') )
                    $value = mb_convert_encoding($value, 'UTF-8', mb_detect_encoding($value));
                $value = htmlentities($value, ENT_NOQUOTES, 'UTF-8');
                $value = preg_replace('`&([a-z]{1,2})(acute|uml|circ|grave|ring|cedil|slash|tilde|caron|lig);`i', '\1', $value);
                $value = html_entity_decode($value, ENT_NOQUOTES, 'UTF-8');
                $search .= " LCASE(pd.title) LIKE '%" . $this->db->escape(strtolower($value)) . "%' collate utf8_general_ci OR";

                if (isset($data['search_in_description'])) {
                    $search2 .= " LCASE(pd.description) LIKE '%" . $this->db->escape(strtolower($value)) . "%' collate utf8_general_ci OR";
                }
            }
            if (!empty($search)) {
                $criteria[] = " (". rtrim($search,'OR') .")";
            }
            if (!empty($search2)) {
                $criteria[] = " (". rtrim($search2,'OR') .")";
            }
        }

        if (!empty($data['category']) || !empty($data['category_id'])) {
            $sql .= " LEFT JOIN " . DB_PREFIX . "object_to_category p2c ON (p.product_id = p2c.object_id AND p2c.object_type = 'product') ";
        }

        if (!empty($data['category'])) {
            $sql .= " LEFT JOIN " . DB_PREFIX . "description cd ON (cd.object_id = p2c.category_id) ";
            $criteria[] = " LCASE(cd.title) LIKE '%" . $this->db->escape(strtolower($data['category'])) . "%' collate utf8_general_ci ";
            $criteria[] = " cd.object_type = 'category' ";
        }

        if (!empty($data['category_id'])) {
            $sql .= " LEFT JOIN " . DB_PREFIX . "object_to_store c2s ON (p2c.category_id = c2s.object_id AND c2s.object_type = 'category') ";
            $criteria[] = " c2s.store_id = '" . (int) STORE_ID . "' ";
            $criteria[] = " p2c.category_id IN (" . implode(', ', $data['category_id']) . ") ";
        }

        if (!empty($data['manufacturer'])) {
            $criteria[] = " LCASE(m.name) LIKE '%" . $this->db->escape(strtolower($data['manufacturer'])) . "%' collate utf8_general_ci ";
        }

        if (!empty($data['manufacturer_id'])) {
            $sql .= " LEFT JOIN " . DB_PREFIX . "object_to_store m2s ON (m2s.object_id = m.manufacturer_id AND m2s.object_type = 'manufacturer') ";
            $criteria[] = " m.manufacturer_id IN (" . implode(', ', $data['manufacturer_id']) . ") ";
        }

        if (!empty($data['zone'])) {
            $sql .= " LEFT JOIN " . DB_PREFIX . "product_to_zone p2z ON (p.product_id = p2z.product_id) ";
            $sql .= " LEFT JOIN " . DB_PREFIX . "zone z ON (z.zone_id = p2z.zone_id) ";
            $sql .= " LEFT JOIN " . DB_PREFIX . "description zd ON (zd.object_id = z.zone_id) ";

            $criteria[] = " LCASE(zd.title) = '" . $this->db->escape(strtolower($data['zone'])) . "' ";
            $criteria[] = " zd.object_type = 'zone'";
            $criteria[] = " zd.language_id = '" . (int)$this->config->get('config_language_id') . "'";
        }

        if (!empty($data['stock_status'])) {
            $sql .= " LEFT JOIN " . DB_PREFIX . "status ss ON (p.stock_status_id = ss.status_id AND ss.object_type = 'stock_status') ";
            $criteria[] = " ss.language_id = '". (int)$this->config->get('config_language_id') ."'";
            $criteria[] = " LCASE(ss.name) LIKE '%" . $this->db->escape($data['stock_status']) . "%' collate utf8_general_ci ";
        }

        if (!empty($data['seller'])) {
            $sql .= " LEFT JOIN " . DB_PREFIX . "customer cu ON (p.owner_id = cu.customer_id) ";
            $search = '';
            $search .= " CONVERT(LCASE(CONCAT(cu.`firstname`,' ', cu.`lastname`,' ', cu.`company`)) USING utf8) LIKE '%" . $this->db->escape(strtolower($data['seller'])) . "%' OR";
            //$search .= " CONVERT(LCASE(cu.`lastname`) USING utf8) LIKE '%" . $this->db->escape(strtolower($data['seller'])) . "%' OR";
            //$search .= " CONVERT(LCASE(cu.`company`) USING utf8) LIKE '%" . $this->db->escape(strtolower($data['seller'])) . "%' OR";
            $criteria[] = " cu.status = '1'";
            if (!empty($search)) {
                $criteria[] = " (". rtrim($search,'OR') .")";
            }
        }

        if (!empty($data['store'])) {
            $criteria[] = " LCASE(s.name) = '" . $this->db->escape(strtolower($data['store'])) . "' collate utf8_general_ci ";
        }

        if (!empty($data['store_id']) && is_numeric($data['store_id'])) {
            $criteria[] = " p2s.store_id = '". intval($data['store_id']) ."' ";
        } elseif (!empty($data['store_id']) && is_array($data['store_id'])) {
            $criteria[] = " p2s.store_id IN ('" . implode("','", $data['store_id']) . "') ";
        } else {
            $criteria[] = " p2s.store_id = '". (int)STORE_ID ."' ";
        }

        if (!empty($data['properties']) || !empty($data['shipping_method']) || !empty($data['payment_method']) || !empty($data['product_status'])) {
            $sql .= " LEFT JOIN " . DB_PREFIX . "property pp ON (p.product_id = pp.object_id) ";
            $criteria[] = " pp.object_type = 'product' ";
        }

        if (!empty($data['shipping_method']) || !empty($data['payment_method']) || !empty($data['product_status'])) {
            if (!empty($data['shipping_method'])) {
                foreach ($data['shipping_methods'] as $key => $value) {
                    $criteria[] = " `group` = 'shipping_methods' ";
                    $criteria[] = " LCASE(pp.`key`) LIKE '%" . $this->db->escape(strtolower($value)) . "%' collate utf8_general_ci ";
                }
            }

            if (!empty($data['payment_method'])) {
                foreach ($data['payment_methods'] as $key => $value) {
                    $criteria[] = " `group` = 'payment_methods' ";
                    $criteria[] = " LCASE(pp.`key`) LIKE '%" . $this->db->escape(strtolower($value)) . "%' collate utf8_general_ci ";
                }
            }

            if (!empty($data['product_status'])) {
                foreach ($data['product_status'] as $key => $value) {
                    $criteria[] = " `group` = 'product_status' ";
                    $criteria[] = " LCASE(pp.`key`) LIKE '%" . $this->db->escape(strtolower($value)) . "%' collate utf8_general_ci ";
                }
            }
        }

        if (!empty($data['suggested']) && !empty($data['product_id'])) {
            $search = '';

            $search .= " p.product_id IN ( " .
                "SELECT object_id FROM `" . DB_PREFIX . "object_to_category` WHERE category_id IN ( " .
                "SELECT category_id FROM `" . DB_PREFIX . "object_to_category` WHERE object_id IN (". implode(', ', $data['product_id']) .") AND object_type = 'product' "
                ." )) OR";

            $search .= " p.product_id IN ( " .
                "SELECT product_id FROM `" . DB_PREFIX . "product` WHERE manufacturer_id = ( " .
                "SELECT manufacturer_id FROM `" . DB_PREFIX . "product` WHERE product_id IN (". implode(', ', $data['product_id']) .") "
                ." )) OR";

            $search .= " p.product_id IN ( " .
                "SELECT product_id FROM `" . DB_PREFIX . "order_product` WHERE order_id IN ( " .
                "SELECT order_id FROM `" . DB_PREFIX . "order_product` WHERE product_id IN (". implode(', ', $data['product_id']) .") "
                ." )) OR";

            foreach ($data['queries'] as $key => $value) {
                if (empty($value)) continue;
                if ($value !== mb_convert_encoding( mb_convert_encoding($value, 'UTF-32', 'UTF-8'), 'UTF-8', 'UTF-32') )
                    $value = mb_convert_encoding($value, 'UTF-8', mb_detect_encoding($value));
                $value = htmlentities($value, ENT_NOQUOTES, 'UTF-8');
                $value = preg_replace('`&([a-z]{1,2})(acute|uml|circ|grave|ring|cedil|slash|tilde|caron|lig);`i', '\1', $value);
                $value = html_entity_decode($value, ENT_NOQUOTES, 'UTF-8');
                $search .= "  p.product_id IN ( ".
                    " SELECT product_id FROM `" . DB_PREFIX . "product_tags` WHERE `tag` IN (" .
                    " SELECT `tag` FROM `" . DB_PREFIX . "product_tags` WHERE LCASE(tag) LIKE '%" . $this->db->escape(strtolower($value)) . "%' collate utf8_general_ci".
                    " )) OR";
            }

            if (!empty($search)) {
                $criteria[] = " " . rtrim($search, 'OR') . " ";
                $criteria[] = " p.product_id NOT IN (". implode(', ', $data['product_id']) .") ";
                if ($data['limit'] > 12 || (int)$data['limit'] == 0) $data['limit'] = 12;
            }
        } elseif (!empty($data['related']) && !empty($data['product_id'])) {
            $search .= " p.product_id IN ( " .
                "SELECT related_id FROM `" . DB_PREFIX . "product_related` WHERE product_id IN (". implode(', ', $data['product_id']) .") "
                ." ) OR";

            if (!empty($search)) {
                $criteria[] = " " . rtrim($search, 'OR') . " ";
                $criteria[] = " p.product_id NOT IN (". implode(', ', $data['product_id']) .") ";
                if ($data['limit'] > 12 || (int)$data['limit'] == 0) $data['limit'] = 12;
            }

        } elseif (!empty($data['xsell']) && !empty($data['product_id'])) {
            $search = '';
            //TODO: filter by order status
            $search .= " p.product_id IN ( " .
                "SELECT product_id FROM `" . DB_PREFIX . "order_product` WHERE order_id IN ( " .
                "SELECT order_id FROM `" . DB_PREFIX . "order_product` WHERE product_id IN (". implode(', ', $data['product_id']) .") "
                ." )) OR";

            if (!empty($search)) {
                $criteria[] = " (" . rtrim($search, 'OR') . ")";
                $criteria[] = " p.product_id NOT IN (". implode(', ', $data['product_id']) .") ";
                if ($data['limit'] > 4 || (int)$data['limit'] == 0) $data['limit'] = 4;
            }
        }

        if (!empty($data['date_start']) && !empty($data['date_end'])) {
            $criteria[] = " p.date_added BETWEEN '". date('Y-m-d',strtotime($data['date_start'])) ."' AND '". date('Y-m-d',strtotime($data['date_end'])) ."'";
        } elseif (!empty($data['date_start'])) {
            $criteria[] = " p.date_added BETWEEN '". date('Y-m-d',strtotime($data['date_start'])) ."' AND NOW()";
        } elseif (!empty($data['date_end'])) {
            $criteria[] = " p.date_added BETWEEN NOW() AND '". date('Y-m-d',strtotime($data['date_end'])) ."'";
        }

        if (!empty($data['price_start']) && !empty($data['price_end'])) {
            $criteria[] = " p.price BETWEEN '". (float)$data['price_start'] ."' AND '". (float)$data['price_end'] ."'";
        } elseif (!empty($data['price_start'])) {
            $criteria[] = " p.price >= '". (float)$data['price_start'] ."'";
        } elseif (!empty($data['price_end'])) {
            $criteria[] = " p.price <= '". (float)$data['price_end'] ."'";
        }

        if ($criteria) {
            $sql .= " WHERE " . implode(" AND ",$criteria);
        }

        if (!$countAsTotal) {
            if (isset($sort_data)) {
                $sql .= " GROUP BY p.product_id";
                if (isset($data['sort'])) $sql .= (in_array($data['sort'], $sort_data)) ? " ORDER BY " . $data['sort'] : " ORDER BY pd.title";
                if (isset($data['order'])) $sql .= ($data['order'] == 'DESC') ? " DESC" : " ASC";
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

    public function getProduct($id) {
        return $this->getById($id);
    }

    public function getById($id) {
        $results = $this->getAll(array('product_id'=>$id));
        return $results[0] ?? false;
    }

    public function getProducts($data) {
        return $this->getAll($data);
    }

    public function getTotalProducts($data) {
        return $this->getAllTotal($data);
    }

    public function getProductsToCompare($ids) {
        return $this->getAll(array('product_id'=>$ids));
    }

    public function getProductRelated($id, $data) {
        $data['product_id'] = $id;
        $data['related'] = true;
        return $this->getAll($data);
    }

    public function getTotalProductRelated($id, $data) {
        $data['product_id'] = $id;
        $data['related'] = true;
        return $this->getAllTotal($data);
    }

    public function getRandomProducts($data) {
        $sort_data = array(
            'RAND()'
        );
        $data['rand'] = mt_rand();
        $data['sort'] = 'RAND()';
        return $this->getAll($data, ['sort_data' => $sort_data]);
    }

    public function getRecommendedProducts($data) {
        $query = $this->db->query("SELECT DISTINCT object_id ".
            "FROM " . DB_PREFIX . "stat s ".
            "WHERE s.object_type = 'product' ".
            "AND s.customer_id = '" . (int) $this->customer->getId() . "' ".
            "GROUP BY object_id, object_type ".
            "ORDER BY s.date_added DESC ".
            "LIMIT " . (int)$data['limit']);

        foreach ($query->rows as $k=>$v) {
            $data['product_id'][$k] = $v['object_id'];
        }

        return $this->getAll($data);
    }

    public function getTotalRecommendedProducts($data) {
        $query = $this->db->query("SELECT DISTINCT object_id ".
            "FROM " . DB_PREFIX . "stat s ".
            "WHERE s.object_type = 'product' ".
            "AND s.customer_id = '" . (int) $this->customer->getId() . "' ");

        foreach ($query->rows as $k=>$v) {
            $data['product_id'][$k] = $v['object_id'];
        }

        return $this->getAllTotal($data);
    }

    public function getLatestProducts($data) {
        $sort_data = array(
            'p.date_added'
        );
        $data['sort'] = 'p.date_added';
        $data['order'] = 'DESC';
        return $this->getAll($data, $sort_data);
    }

    public function getPopularProducts($data) {
        $sort_data = array(
            'p.viewed, p.date_added'
        );
        $data['sort'] = 'p.viewed, p.date_added';
        $data['order'] = 'DESC';
        return $this->getAll($data, $sort_data);
    }

    public function getFeaturedProducts($data) {
        $query = $this->db->query("SELECT DISTINCT product_id ".
            "FROM " . DB_PREFIX . "product_featured ".
            "LIMIT " . (int) $data['limit']);

        foreach ($query->rows as $k=>$v) {
            $data['product_id'][$k] = $v['product_id'];
        }

        return $this->getAll($data);
    }

    public function getTotalFeaturedProducts($data) {
        $query = $this->db->query("SELECT DISTINCT product_id ".
            "FROM " . DB_PREFIX . "product_featured ");

        foreach ($query->rows as $k=>$v) {
            $data['product_id'][$k] = $v['product_id'];
        }

        return $this->getAllTotal($data);
    }

    public function getBestSellerProducts($data) {
        $query = $this->db->query("SELECT product_id, SUM(op.quantity) AS total ".
            "FROM " . DB_PREFIX . "order_product op ".
            "LEFT JOIN " . DB_PREFIX . "order o ON (op.order_id = o.order_id) ".
            "WHERE o.order_status_id> '0' ".
            "GROUP BY op.product_id ".
            "ORDER BY total DESC ".
            "LIMIT " . (int) $data['limit']);

        foreach ($query->rows as $k=>$v) {
            $data['product_id'][$k] = $v['product_id'];
        }

        return $this->getAll($data);
    }

    public function getTotalBestSellerProducts($data) {
        $query = $this->db->query("SELECT product_id, SUM(op.quantity) AS total ".
            "FROM " . DB_PREFIX . "order_product op ".
            "LEFT JOIN " . DB_PREFIX . "order o ON (op.order_id = o.order_id) ".
            "WHERE o.order_status_id> '0' ");

        foreach ($query->rows as $k=>$v) {
            $data['product_id'][$k] = $v['product_id'];
        }

        return $this->getAllTotal($data);
    }

    public function getProductOptions($product_id) {
        $product_option_data = [];

        $product_option_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_option WHERE product_id = '" . (int) $product_id . "' ORDER BY sort_order");

        foreach ($product_option_query->rows as $product_option) {
            $product_option_value_data = [];

            $product_option_value_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_option_value WHERE product_option_id = '" . (int) $product_option['product_option_id'] . "' ORDER BY sort_order");

            foreach ($product_option_value_query->rows as $product_option_value) {
                $product_option_value_description_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_option_value_description WHERE product_option_value_id = '" . (int) $product_option_value['product_option_value_id'] . "' AND language_id = '" . (int) $this->config->get('config_language_id') . "'");

                $product_option_value_data[] = array(
                    'product_option_value_id' => $product_option_value['product_option_value_id'],
                    'name' => $product_option_value_description_query->row['name'],
                    'price' => $product_option_value['price'],
                    'prefix' => $product_option_value['prefix']
                );
            }

            $product_option_description_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_option_description WHERE product_option_id = '" . (int) $product_option['product_option_id'] . "' AND language_id = '" . (int) $this->config->get('config_language_id') . "'");

            $product_option_data[] = array(
                'product_option_id' => $product_option['product_option_id'],
                'name' => $product_option_description_query->row['name'],
                'option_value' => $product_option_value_data,
                'sort_order' => $product_option['sort_order']
            );
        }

        return $product_option_data;
    }

    public function getProductImages($product_id) {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_image WHERE product_id = '" . (int) $product_id . "'");

        return $query->rows;
    }

    public function getProductTags($product_id) {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_tags WHERE product_id = '" . (int) $product_id . "' AND language_id = '" . (int) $this->config->get('config_language_id') . "'");

        return $query->rows;
    }

    public function getProductDiscount($product_id) {
        if ($this->customer->isLogged()) {
            $customer_group_id = $this->customer->getCustomerGroupId();
        } else {
            $customer_group_id = $this->config->get('config_customer_group_id');
        }

        $query = $this->db->query("SELECT price
        FROM " . DB_PREFIX . "product_discount
        WHERE product_id = '" . (int) $product_id . "'
        AND customer_group_id = '" . (int) $customer_group_id . "'
        AND quantity = '1'
        AND ((date_start = '0000-00-00' OR date_start < NOW()) AND (date_end = '0000-00-00' OR date_end > NOW()))
        ORDER BY priority ASC, price ASC
        LIMIT 1");

        if ($query->num_rows) {
            return $query->row['price'];
        } else {
            return false;
        }
    }

    public function getProductDiscounts($product_id) {
        if ($this->customer->isLogged()) {
            $customer_group_id = $this->customer->getCustomerGroupId();
        } else {
            $customer_group_id = $this->config->get('config_customer_group_id');
        }

        $query = $this->db->query("SELECT *
        FROM " . DB_PREFIX . "product_discount
        WHERE product_id = '" . (int) $product_id . "'
        AND customer_group_id = '" . (int) $customer_group_id . "'
        AND quantity > 1
        AND ((date_start = '0000-00-00' OR date_start < NOW()) AND (date_end = '0000-00-00' OR date_end> NOW()))
        ORDER BY quantity ASC, priority ASC, price ASC");

        return $query->rows;
    }

    public function getProductSpecial($product_id) {
        if ($this->customer->isLogged()) {
            $customer_group_id = $this->customer->getCustomerGroupId();
        } else {
            $customer_group_id = $this->config->get('config_customer_group_id');
        }

        $query = $this->db->query("SELECT price FROM " . DB_PREFIX . "product_special WHERE product_id = '" . (int) $product_id . "' AND customer_group_id = '" . (int) $customer_group_id . "' AND ((date_start = '0000-00-00' OR date_start < NOW()) AND (date_end = '0000-00-00' OR date_end> NOW())) ORDER BY priority ASC, price ASC LIMIT 1");

        if ($query->num_rows) {
            return $query->row['price'];
        } else {
            return false;
        }
    }

    public function getProductSpecials($sort = 'p.sort_order', $order = 'ASC', $start = 0, $limit = 20) {
        if ($this->customer->isLogged()) {
            $customer_group_id = $this->customer->getCustomerGroupId();
        } else {
            $customer_group_id = $this->config->get('config_customer_group_id');
        }

        $sql = "SELECT *, p.date_added AS created, pd.name AS name, p.price,
                (
                    SELECT ps2.price
                        FROM " . DB_PREFIX . "product_special ps2
                        WHERE p.product_id = ps2.product_id
                            AND ps2.customer_group_id = '" . (int) $customer_group_id . "'
                            AND 
                            ( 
                                (ps2.date_start = '0000-00-00' OR ps2.date_start < NOW())
                                AND (ps2.date_end = '0000-00-00' OR ps2.date_end> NOW())
                            )
                    ORDER BY ps2.priority ASC, ps2.price ASC LIMIT 1
                ) AS special,
                p.image,
                m.name AS manufacturer,
                ss.name AS stock,
                (
                    SELECT AVG(r.rating)
                        FROM " . DB_PREFIX . "review r
                        WHERE p.product_id = r.object_id AND r.`object_type` = 'product'
                        GROUP BY r.object_id
                ) AS rating
            FROM " . DB_PREFIX . "product p
                LEFT JOIN " . DB_PREFIX . "description pd ON (p.product_id = pd.object_id)
                LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id)
                LEFT JOIN " . DB_PREFIX . "product_special ps ON (p.product_id = ps.product_id)
                LEFT JOIN " . DB_PREFIX . "manufacturer m ON (p.manufacturer_id = m.manufacturer_id)
                LEFT JOIN " . DB_PREFIX . "status ss ON (p.stock_status_id = ss.status_id AND ss.object_type = 'stock_status')
                WHERE p.status = '1'
                    AND p.date_available <= NOW()
                    AND pd.object_type = 'product'
                    AND pd.language_id = '" . (int) $this->config->get('config_language_id') . "'
                    AND ss.language_id = '" . (int) $this->config->get('config_language_id') . "'
                    AND p2s.store_id = '" . (int) STORE_ID . "'
                    AND ps.customer_group_id = '" . (int) $customer_group_id . "'
                    AND ((ps.date_start = '0000-00-00' OR ps.date_start < NOW())
                    AND (ps.date_end = '0000-00-00' OR ps.date_end> NOW()))
                    AND ps.product_id NOT IN
                        (SELECT pd2.product_id
                            FROM " . DB_PREFIX . "product_discount pd2
                            WHERE p.product_id = pd2.product_id
                                AND pd2.customer_group_id = '" . (int) $customer_group_id . "'
                                AND pd2.quantity = '1'
                                AND ((pd2.date_start = '0000-00-00' OR pd2.date_start < NOW())
                                AND (pd2.date_end = '0000-00-00' OR pd2.date_end> NOW())))
                GROUP BY p.product_id";

        $sort_data = array(
            'pd.name',
            'p.sort_order',
            'special',
            'rating'
        );

        if (in_array($sort, $sort_data)) {
            if ($sort == 'pd.name') {
                $sql .= " ORDER BY LCASE(" . $sort . ")";
            } else {
                $sql .= " ORDER BY " . $sort;
            }
        } else {
            $sql .= " ORDER BY p.sort_order";
        }

        if ($order == 'DESC') {
            $sql .= " DESC";
        } else {
            $sql .= " ASC";
        }

        if ($start < 0) {
            $start = 0;
        }

        $sql .= " LIMIT " . (int) $start . "," . (int) $limit;

        $query = $this->db->query($sql);

        return $query->rows;
    }

    public function getTotalProductSpecials() {
        if ($this->customer->isLogged()) {
            $customer_group_id = $this->customer->getCustomerGroupId();
        } else {
            $customer_group_id = $this->config->get('config_customer_group_id');
        }

        $query = $this->db->query("SELECT COUNT(DISTINCT ps.product_id) AS total
        FROM " . DB_PREFIX . "product p
            LEFT JOIN " . DB_PREFIX . "product_special ps ON (p.product_id = ps.product_id)
            LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id)
        WHERE p.status = '1'
            AND p.date_available <= NOW()
            AND p2s.store_id = '" . (int) STORE_ID . "'
            AND ((ps.date_start = '0000-00-00' OR ps.date_start < NOW())
            AND (ps.date_end = '0000-00-00' OR ps.date_end> NOW()))
            AND ps.product_id NOT IN
                (SELECT pd2.product_id FROM " . DB_PREFIX . "product_discount pd2
                    WHERE p.product_id = pd2.product_id
                        AND pd2.customer_group_id = '" . (int) $customer_group_id . "'
                        AND pd2.quantity = '1'
                        AND ((pd2.date_start = '0000-00-00' OR pd2.date_start < NOW())
                        AND (pd2.date_end = '0000-00-00' OR pd2.date_end> NOW())))");

        if (isset($query->row['total'])) {
            return $query->row['total'];
        } else {
            return 0;
        }
    }

    public function getCategoriesByAttributeGroupId($id) {
        if (is_array($id)) {
            $query = $this->db->query("SELECT category_id FROM " . DB_PREFIX . "object_to_category ".
                "WHERE object_id IN ('" . implode("','", $id) . "') AND object_type = 'attribute'");
        } else {
            $query = $this->db->query("SELECT category_id FROM " . DB_PREFIX . "object_to_category ".
            "WHERE object_id = '" . (int)$id . "' AND object_type = 'attribute'");
        }

        foreach ($query->rows as $row) {
            $return[] = $row['category_id'];
        }

        return $return;
    }

    public function getCategories($product_id) {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "object_to_category WHERE object_id = '" . (int) $product_id . "' AND object_type = 'product'");

        return $query->rows;
    }

    public function updateStats($id) {
        $this->db->query("UPDATE " . DB_PREFIX . "product SET viewed = (viewed + 1) WHERE product_id = '" . (int) $id . "'");
    }
    
}
