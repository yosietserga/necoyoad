<?php

/**
 * ModelStoreCategory
 * 
 * @package NecoTienda
 * @author NecoTienda
 * @copyright Inversiones Necoyoad, C.A.
 * @version 1.0.0
 * @access public
 */
class ModelStoreCategory extends Model
{
    protected string $object_type  = "category";
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
            "default"   => "category",
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

    public function init() {

        $this->addFilter("select", function ($args) {
            $sql = $args['sql'];
            $data = $args['data'];
            $sql = " DISTINCT *, 
            td.title AS title, 
            td.description AS description, 
            t.image AS cimage ";
            return ["sql" => $sql, "data" => $data];
        });
        
        $this->addFilter("join", function ($args) {
            $sql = $args['sql'];
            $data = $args['data'];

            if (isset($data['product_id']) || isset($data['manufacturer_id']) || isset($data['product'])) {
                $sql .= "LEFT JOIN " . DB_PREFIX . "object_to_category p2c ON (t.category_id = p2c.category_id) ";
                $sql .= "LEFT JOIN " . DB_PREFIX . "product p ON (p2c.object_id = p.product_id) ";
            }

            if (isset($data['product']) && !empty($data['product'])) {
                $sql .= "LEFT JOIN `" . DB_PREFIX . "description` pd ON (pd.object_id = p.product_id) ";
            }

            if (isset($data['manufacturer_id']) && !empty($data['manufacturer_id'])) {
                $sql .= " LEFT JOIN " . DB_PREFIX . "manufacturer m ON (p.manufacturer_id = m.manufacturer_id) ";
            }

            return ["sql" => $sql, "data" => $data];
        });

        $this->addFilter("where", function ($args) {
            $criteria = $args['criteria'];
            $data = $args['data'];

            if (isset($data['product_id'])) $data['product_id'] = !is_array($data['product_id']) && !empty($data['product_id']) ? array($data['product_id']) : $data['product_id'];
            if (isset($data['manufacturer_id'])) $data['manufacturer_id'] = !is_array($data['manufacturer_id']) && !empty($data['manufacturer_id']) ? array($data['manufacturer_id']) : $data['manufacturer_id'];

            if (isset($data['product_id']) || isset($data['product'])) {
                $criteria[] = " p2c.object_type = 'product' ";
            }

            if (isset($data['manufacturer_id']) && !empty($data['manufacturer_id'])) {
                $criteria[] = " m.manufacturer_id IN (" . implode(', ', $data['manufacturer_id']) . ") ";
            }

            if (isset($data['product_id']) && !empty($data['product_id'])) {
                $criteria[] = " p2c.object_id IN (" . implode(', ', $data['product_id']) . ") ";
            }

            if (isset($data['product']) && !empty($data['product'])) {
                $criteria[] = " LCASE(CONCAT(pd.`title`, ' ', p.model)) LIKE '%" . $this->db->escape(strtolower($data['product'])) . "%' ";
                $criteria[] = " pd.object_type = 'product' ";
            }

            return ["criteria" => $criteria, "data" => $data];
        });
    }

    public function getPath($category_id) {
        $query = $this->db->query("SELECT title, parent_id 
        FROM " . DB_PREFIX . "category c 
            LEFT JOIN " . DB_PREFIX . "description cd ON (c.category_id = cd.object_id) 
        WHERE c.category_id = '" . (int) $category_id . "' 
            AND cd.object_type = 'category' 
            AND cd.language_id = '" . (int) $this->config->get('config_language_id') . "' 
        ORDER BY c.sort_order, cd.title ASC");

        $category_info = $query->row;

        if ($category_info['parent_id']) {
            return $this->getPath($category_info['parent_id'], $this->config->get('config_language_id')) . $this->language->get('text_separator') . $category_info['title'];
        } else {
            return $category_info['title'];
        }
    }

    public function getTotalCategoriesByCategoryId($parent_id = 0) {
        $query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "category c 
        LEFT JOIN " . DB_PREFIX . "category_to_store c2s ON (c.category_id = c2s.category_id) 
        WHERE c.parent_id = '" . (int) $parent_id . "' 
        AND c2s.store_id = '" . (int) STORE_ID . "' 
        AND c.status = '1'");
        return $query->row['total'];
    }

    public function updateStats($id) {
        $this->db->query("UPDATE " . DB_PREFIX . "category SET viewed = viewed + 1 WHERE category_id = '" . (int) $id . "'");
    }
}