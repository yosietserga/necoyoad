<?php

/**
 * ModelContentPostCategory
 * 
 * @package NecoTienda
 * @author NecoTienda
 * @copyright Inversiones Necoyoad, C.A.
 * @version 1.0.0
 * @access public
 */
class ModelContentPostCategory extends Model {
    public string $object_type  = "post_category";
    
    protected string $table        = "category";
    protected string $pkey         = "category_id";

    protected array $fields = [
        "parent_id" => [
            "name"      => "parent_id",
            "default"   => 0,
            "type"      => "integer",
        ],
        "object_type" => [
            "name"      => "object_type",
            "default"   => "post_category",
            "type"      => "string",
        ],
        "image" => [
            "name"      => "image",
            "type"      => "string",
        ],
        "status" => [
            "name"      => "status",
            "default"   => 1,
            "type"      => "boolean",
        ],
        "sort_order" => [
            "name"      => "sort_order",
            "default"   => 0,
            "type"      => "integer",
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

    protected array $relations = ["descriptions", "stores"];

    public function init()
    {
        $this->on("save", function ($args) {
            $d = $args[0];
            $id = $d['id'];
            $data = $d['data'];

            if (!empty($data['Posts'])) {
                $this->db->query("DELETE FROM " . DB_PREFIX . "object_to_category WHERE category_id='" . (int) $id . "' AND object_type = 'post'");
                foreach ($data['Posts'] as $post_id => $value) {
                    if ($value == 0) continue;
                    $this->db->query("REPLACE INTO " . DB_PREFIX . "object_to_category (object_id, category_id, object_type) VALUES ('" . (int) $post_id . "','" . (int)$id . "', 'post')");
                }
            }
        });

        $this->addFilter("join", function ($args) {
            $sql = $args['sql'];
            $data = $args['data'];

            if (isset($data['post_id']) || isset($data['post'])) {
                $sql .= "LEFT JOIN " . DB_PREFIX . "object_to_category p2c ON (t.category_id = p2c.category_id) ";
                $sql .= "LEFT JOIN " . DB_PREFIX . "post p ON (p2c.object_id = p.post_id) ";
            }

            if (isset($data['post']) && !empty($data['post'])) {
                $sql .= "LEFT JOIN `" . DB_PREFIX . "description` pd ON (pd.object_id = p.post_id) ";
            }

            return ["sql" => $sql, "data" => $data];
        });

        $this->addFilter("where", function ($args) {
            $criteria = $args['criteria'];
            $data = $args['data'];

            if (isset($data['post_id'])) $data['post_id'] = !is_array($data['post_id']) && !empty($data['post_id']) ? array($data['post_id']) : $data['post_id'];

            if (isset($data['post_id']) || isset($data['post'])) {
                $criteria[] = " p2c.object_type = 'post' ";
            }

            if (isset($data['post_id']) && !empty($data['post_id'])) {
                $criteria[] = " p2c.object_id IN (" . implode(', ', $data['post_id']) . ") ";
            }

            if (isset($data['post']) && !empty($data['post'])) {
                $criteria[] = " LCASE(pd.`title`) LIKE '%" . $this->db->escape(strtolower($data['post'])) . "%' ";
                $criteria[] = " pd.object_type = 'post' ";
            }

            return ["criteria" => $criteria, "data" => $data];
        });
    }

    /**
     * ModelStoreCategory::getPath()
     * 
     * @param int $category_id
     * @return string 
     */
    public function getPath($category_id) {
        $query = $this->db->query("SELECT title, parent_id 
        FROM " . DB_PREFIX . "category c 
            LEFT JOIN " . DB_PREFIX . "description cd ON (c.category_id = cd.object_id) 
        WHERE c.category_id = '" . (int) $category_id . "' 
            AND cd.object_type = 'post_category' 
            AND cd.language_id = '" . (int) $this->config->get('config_language_id') . "' 
        ORDER BY c.sort_order, cd.title ASC");

        $category_info = $query->row;

        if ($category_info['parent_id']) {
            return $this->getPath($category_info['parent_id'], $this->config->get('config_language_id')) . $this->language->get('text_separator') . $category_info['title'];
        } else {
            return $category_info['title'];
        }
    }

    //TODO: create module or plugin to measure seo ranks and health
    public function getSeoTitleRating() {
        $query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "description` WHERE CHAR_LENGTH(`title`) NOT BETWEEN 8 AND 55 AND object_type = 'post_category' ");
        $query2 = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "description` WHERE object_type = 'post_category'");
        return $query->row['total'] * 100 / $query2->row['total'];
    }

    public function getSeoMetaDescripionRating() {
        $query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "description` WHERE CHAR_LENGTH(`meta_description`) NOT BETWEEN 8 AND 155 AND object_type = 'post_category'");
        $query2 = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "description` WHERE object_type = 'post_category'");
        return $query->row['total'] * 100 / $query2->row['total'];
    }

    public function getSeoDescriptionRating() {
        $query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "description` WHERE CHAR_LENGTH(`description`) < 150 AND object_type = 'post_category'");
        $query2 = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "description` WHERE object_type = 'post_category'");
        return $query->row['total'] * 100 / $query2->row['total'];
    }

    public function getSeoUrlRating() {
        $query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "category` WHERE category_id NOT IN (SELECT `object_id` FROM `" . DB_PREFIX . "url_alias` WHERE `object_type` = 'post_category') ");
        $query2 = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "category` WHERE `object_type` = 'post_category'");
        return $query->row['total'] * 100 / $query2->row['total'];
    }
}
