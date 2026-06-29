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

    protected string $table        = "product_attribute_group";
    protected string $pkey         = "product_attribute_group_id";
    protected string $object_type  = "attribute";

    protected array $fields = [
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

    protected array $relations = ["categories"];

    public function init()
    {
        $this->on("save", function ($args) {
            $d = $args[0];
            $id = $d['id'];
            $data = $d['data'];
            $action = $d['action'];

            if ($action == "update")
                $this->db->query("DELETE FROM " . DB_PREFIX . "product_attribute WHERE product_attribute_group_id = '" . (int)$id . "'");

            $memoProps = [];
            foreach ($data['Properties'] as $row => $property) {
                $property['group'] = $data['name'];
                $property['product_attribute_group_id'] = $id;
                if (!in_array($id.$property['name'], $memoProps)) {
                    $this->setAttributes($property);
                    $memoProps[] = $id . $property['name'];
                }
            }
        });

        $this->on("delete", function ($args) {
            $d = $args[0];
            $id = $d['id'];
            $this->db->query("DELETE FROM " . DB_PREFIX . "product_attribute WHERE product_attribute_group_id = '" . (int)$id . "'");
        });

        $this->addFilter("copy", function ($args) {
            $id = $args['id'];
            $data = $args['data'];

            $data['Properties'] = $this->model->getAllAttributes([
                'product_attribute_group_id' => $id
            ]);

            return ["id" => $id, "data" => $data];
        });

        $this->addFilter("join", function ($args) {
            $sql = $args['sql'];
            $data = $args['data'];

            if (isset($data['category_id']) || isset($data['category'])) {
                $sql .= "LEFT JOIN " . DB_PREFIX . "object_to_category a2c ON (a2c.object_id = t.product_attribute_group_id AND a2c.object_type = 'attribute') ";
            }

            if (isset($data['category']) && !is_null($data['category'])) {
                $sql .= "LEFT JOIN `" . DB_PREFIX . "description` cd ON (a2c.category_id = cd.object_id AND a2c.object_type = 'category') ";
            }

            return ["sql" => $sql, "data" => $data];
        });

        $this->addFilter("where", function ($args) {
            $criteria = $args['criteria'];
            $data = $args['data'];

            if (isset($data['category_id'])) $data['category_id'] = !is_array($data['category_id']) && !empty($data['category_id']) ? array($data['category_id']) : $data['category_id'];

            if (isset($data['category_id']) && !empty($data['category_id'])) {
                $criteria[] = " a2c.category_id IN (" . implode(', ', $data['category_id']) . ") ";
            }

            if (isset($data['category']) && !is_null($data['category'])) {
                $criteria[] = " LCASE(cd.`name`) LIKE '%" . $this->db->escape(strtolower($data['category'])) . "%' ";
                $criteria[] = " cd.object_type = 'category' ";
            }
		
            return ["criteria" => $criteria, "data" => $data];
        });

        $this->addFilter("query_result", function ($results) {
            foreach ($results as $k => $row) {
                $results[$k]['categories'] = $this->getCategoriesByGroupId($row['product_attribute_group_id']);
                $results[$k]['attributes'] = $this->getAllAttributes([
                    'product_attribute_group_id' => $row['product_attribute_group_id']
                ]);
            }

            return $results;
        });
    }

	public function setAttributes($data) {
        if (empty($data['name']) || empty($data['label'])) return false;
		$this->db->query("INSERT INTO " . DB_PREFIX . "product_attribute SET ".
            "`product_attribute_group_id` = '" . (int)$data['product_attribute_group_id'] . "',".
            "`store_id`  = '" . (int)STORE_ID . "',".
            "`group`     = '" . $this->db->escape($data['group']) . "',".
            "`name`      = '" . $this->db->escape($data['name']) . "',".
            "`label`     = '" . $this->db->escape($data['label']) . "',".
            "`type`      = '" . $this->db->escape($data['type']) . "',".
            "`pattern`   = '" . $this->db->escape($data['pattern']) . "',".
            "`required`  = '" . $this->db->escape($data['required']) . "',".
            "`default`   = '" . $this->db->escape($data['default']) . "',".
            "date_added  = NOW()");

	}
	
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

        if (isset($data['product_attribute_group_id'])) $data['product_attribute_group_id'] = !is_array($data['product_attribute_group_id']) && !empty($data['product_attribute_group_id']) ? array($data['product_attribute_group_id']) : $data['product_attribute_group_id'];
        if (isset($data['product_attribute_id'])) $data['product_attribute_id'] = !is_array($data['product_attribute_id']) && !empty($data['product_attribute_id']) ? array($data['product_attribute_id']) : $data['product_attribute_id'];
        if (isset($data['store_id'])) $data['store_id'] = !is_array($data['store_id']) && !empty($data['store_id']) ? array($data['store_id']) : $data['store_id'];

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
                $sql .= isset($data['sort']) && in_array($data['sort'], $sort_data) ? " ORDER BY " . $data['sort'] : " ORDER BY t.name";
                $sql .= isset($data['sort']) && $data['order'] == 'DESC' ? " DESC" : " ASC";
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
}
