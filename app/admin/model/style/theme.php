<?php
/**
 * ModelStyleTheme
 * 
 * @package NecoTienda
 * @author Yosiet Serga
 * @copyright Inversiones Necoyoad, C.A.
 * @version 1.0.0
 * @access public
 * @see Model
 */
class ModelStyleTheme extends Model {

    protected string $object_type  = "theme";
    protected string $table        = "theme";
    protected string $pkey         = "theme_id";

    protected array $fields = [
        "template_id" => [
            "name"      => "template_id",
            "type"      => "integer",
        ],
        "user_id" => [
            "name"      => "user_id",
            "type"      => "integer",
        ],
        "store_id" => [
            "name"      => "store_id",
            "type"      => "integer",
        ],
        "template" => [
            "name"      => "template",
            "type"      => "string",
        ],
        "name" => [
            "name"      => "name",
            "type"      => "string",
        ],
        "date_publish_start" => [
            "name"      => "date_publish_start",
            "type"      => "date",
        ],
        "date_publish_end" => [
            "name"      => "date_publish_end",
            "type"      => "date",
        ],
        "default" => [
            "name"      => "default",
            "default"   => 0,
            "type"      => "boolean",
        ],
        "sort_order" => [
            "name"      => "sort_order",
            "type"      => "integer",
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


    public function init()
    {
        $this->on("save", function ($args) {
            $d = $args[0];
            $id = $d['id'];
            $data = $d['data'];
            $action = $d['action'];

            if (isset($data['styles'])) {
                foreach ($data['styles'] as $k => $value) {
                    if ($value == 0) continue;
                    $value['theme_id'] = $id;
                    $this->setStyle($value);
                }
            }
        });

        $this->addFilter("copy", function ($args) {
            $id = $args['id'];
            $data = $args['data'];

            $data['name'] = $data['name'] . " - copy";
            $data = array_merge($data, array('style' => $this->getStyles($id)));

            return ["id" => $id, "data" => $data];
        });

        $this->on("delete", function ($args) {
            $d = $args[0];
            $id = $d['id'];

            $this->db->query("DELETE FROM " . DB_PREFIX . "theme_style WHERE theme_id = '" . (int)$id . "'");
            $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "setting WHERE `key` = 'theme_default_id'");
            if ($query->row['value'] == $id) $this->db->query("DELETE FROM " . DB_PREFIX . "setting WHERE `key` = 'theme_default_id'");
        });

        $this->addFilter("join", function ($args) {
            $sql = $args['sql'];
            $data = $args['data'];

            if (isset($data['user_name']) || isset($data['user_email'])) {
                $sql .= "LEFT JOIN " . DB_PREFIX . "user c ON (t.user_id = u.user_id) ";
            }

            return ["sql" => $sql, "data" => $data];
        });

        $this->addFilter("where", function ($args) {
            $criteria = $args['criteria'];
            $data = $args['data'];

            if (isset($data['user_name']) && !empty($data['user_name'])) {
                $criteria[] = " LCASE(CONCAT(u.firstname, ' ', u.lastname)) LIKE '%" . $this->db->escape(strtolower($data['user_name'])) . "%' collate utf8_general_ci ";
            }

            if (isset($data['user_email']) && !empty($data['user_email'])) {
                $criteria[] = " LCASE(u.`email`) LIKE '%" . $this->db->escape(strtolower($data['user_email'])) . "%' collate utf8_general_ci ";
            }

            if (!empty($data['publish_date_start'])) {
                $criteria[] = "date_publish_start <= '" . date('Y-m-d h:i:s', strtotime($data['publish_date_start'])) . "'";
            }

            if (!empty($data['publish_date_end'])) {
                $criteria[] = "date_publish_end >= '" . date('Y-m-d h:i:s', strtotime($data['publish_date_end'])) . "'";
            }

            return ["criteria" => $criteria, "data" => $data];
        });
    }
    
	public function setStyle($data) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "theme_style SET ".
	        "`theme_id`    = '" . intval($data['theme_id']) . "',".
	        "`selector`    = '" . $this->db->escape($data['selector']) . "', ".
	        "`property`    = '" . $this->db->escape($data['property']) . "', ".
	        "`value`       = '" . $this->db->escape($data['value']) . "'");

		return $this->db->getLastId();
	}

	public function deleteStyle($theme_id) {
        $this->db->query("DELETE " . DB_PREFIX . "theme_style WHERE theme_id = '" . intval($theme_id) . "'");
	}

	public function saveStyle($theme_id, $data) {
	   if (!$theme_id && empty($data)) return false;

        $this->deleteStyle($theme_id);

       $sql = "INSERT INTO " . DB_PREFIX . "theme_style (theme_id, selector, `property`, `value`) VALUES ";
        foreach ($data as $selector => $properties) {
            foreach ($properties as $property => $value) {
                if (empty($value)) continue;
                $sql .= "(". (int)$theme_id .",'". $this->db->escape($selector) ."','". $this->db->escape($property) ."','". $this->db->escape($value) ."'),";
            }
        }
        $sql = substr($sql,0,strlen($sql) - 1);
        $this->db->query($sql);
	}
	
	public function ntSort($data) {
	   if (!is_array($data)) return false;
       $pos = 1;
       foreach ($data as $id) {
            $this->db->query("UPDATE " . DB_PREFIX . "theme SET sort_order = '" . (int)$pos . "' WHERE theme_id = '" . (int)$id . "'");
            $pos++;
       }
	   return true;
	}
	
    public function getAllStyles($data=null) {
        $cache_prefix = "admin.theme_styles";
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
            $sql = "SELECT * FROM " . DB_PREFIX . "theme_style t ";

            $sql .= $this->buildStyleSQLQuery($data);
            $query = $this->db->query($sql);
            $this->cache->set($cachedId, $query->rows);
            return $query->rows;
        } else {
            return $cached;
        }
    }

    private function buildStyleSQLQuery($data) {
        $criteria = [];
        $sql = "";

        $data['theme_id'] = !is_array($data['theme_id']) && !empty($data['theme_id']) ? array($data['theme_id']) : $data['theme_id'];
        $data['theme_style_id'] = !is_array($data['theme_style_id']) && !empty($data['theme_style_id']) ? array($data['theme_style_id']) : $data['theme_style_id'];

        if (isset($data['theme_id']) && !empty($data['theme_id'])) {
            $criteria[] = " t.theme_id IN (" . implode(', ', $data['theme_id']) . ") ";
        }

        if (isset($data['theme_style_id']) && !empty($data['theme_style_id'])) {
            $criteria[] = " t.theme_style_id IN (" . implode(', ', $data['theme_style_id']) . ") ";
        }

        if (isset($data['selector']) && !empty($data['selector'])) {
            $criteria[] = " LCASE(t.`selector`) LIKE '%" . $this->db->escape(strtolower($data['selector'])) . "%' collate utf8_general_ci ";
        }

        if (isset($data['property']) && !empty($data['property'])) {
            $criteria[] = " LCASE(t.`property`) LIKE '%" . $this->db->escape(strtolower($data['property'])) . "%' collate utf8_general_ci ";
        }

        if (isset($data['value']) && !empty($data['value'])) {
            $criteria[] = " LCASE(t.`value`) LIKE '%" . $this->db->escape(strtolower($data['value'])) . "%' collate utf8_general_ci ";
        }

        if ($criteria) {
            $sql .= " WHERE " . implode(" AND ",$criteria);
        }

        $sql .= " GROUP BY t.theme_style_id ";
        $sql .= " ORDER BY t.selector ";
        $sql .= ($data['order'] == 'DESC') ? " DESC" : " ASC";
        

        if ($data['start'] && $data['limit']) {
            if ($data['start'] < 0) $data['start'] = 0;
            if (!$data['limit']) $data['limit'] = 24;

            $sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
        } elseif ($data['limit']) {
            if (!$data['limit']) $data['limit'] = 24;

            $sql .= " LIMIT ". (int)$data['limit'];
        }
        
        return $sql;
    }
}