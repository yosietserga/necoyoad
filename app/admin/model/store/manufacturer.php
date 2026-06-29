<?php

/**
 * ModelStoreManufacturer
 * 
 * @package NecoTienda
 * @author Yosiet Serga
 * @copyright Inversiones Necoyoad, C.A.
 * @version 1.0.0
 * @access public
 * @see Model
 */
class ModelStoreManufacturer extends Model {

    protected string $object_type  = "manufacturer";
    protected string $table        = "manufacturer";
    protected string $pkey         = "manufacturer_id";

    protected array $fields = [
        "name" => [
            "name"      => "name",
            "required"  => true,
            "type"      => "string",
        ],
        "image" => [
            "name"      => "image",
            "type"      => "string",
        ],
        "viewed" => [
            "name"      => "viewed",
            "type"      => "integer",
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
    ];

    protected array $relations = ["stores"];

    public function init()
    {
        $this->on("save", function ($args) {
            $d = $args[0];
            $id = $d['id'];
            $data = $d['data'];

            if (!empty($data['keyword'])) {
                $languages = $this->db->query("SELECT * FROM " . DB_PREFIX . "language");
                foreach ($languages->rows as $language) {
                    $this->db->query("INSERT INTO " . DB_PREFIX . "url_alias SET 
                        `language_id` = '" . (int) $language['language_id'] . "', 
                        `object_id`   = '" . (int) $id . "', 
                        `object_type` = 'manufacturer', 
                        `query`       = 'manufacturer_id=" . (int) $id . "', 
                        `keyword`     = '" . $this->db->escape($data['keyword']) . "'");
                }
            }

            foreach ($data['Products'] as $product_id => $value) {
                if ($value == 0)
                    continue;
                $this->db->query("UPDATE `" . DB_PREFIX . "product` SET `manufacturer_id` = '" . (int) $id . "' WHERE `product_id` = '" . (int) $product_id . "'");
            }

        });

        $this->addFilter("join", function ($args) {
            $sql = $args['sql'];
            $data = $args['data'];

            if (isset($data['product_id']) || isset($data['product'])) {
                $sql .= "LEFT JOIN " . DB_PREFIX . "product p ON (t.manufacturer_id = p.manufacturer_id) ";
            }

            if (isset($data['product']) && !empty($data['product'])) {
                $sql .= "LEFT JOIN `" . DB_PREFIX . "description` pd ON (pd.object_id = p.product_id) ";
            }

            return ["sql" => $sql, "data" => $data];
        });

        $this->addFilter("where", function ($args) {
            $criteria = $args['criteria'];
            $data = $args['data'];

            if (isset($data['product_id'])) $data['product_id'] = !is_array($data['product_id']) && !empty($data['product_id']) ? array($data['product_id']) : $data['product_id'];

            if (isset($data['product_id']) && !empty($data['product_id'])) {
                $criteria[] = " p.product_id IN (" . implode(', ', $data['product_id']) . ") ";
            }

            if (isset($data['product']) && !empty($data['product'])) {
                $criteria[] = " LCASE(CONCAT(pd.`title`, ' ', p.model)) LIKE '%" . $this->db->escape(strtolower($data['product'])) . "%' ";
                $criteria[] = " pd.object_type = 'product' ";
            }

            return ["criteria" => $criteria, "data" => $data];
        });
    }

    public function getSeoUrlRating() {
        $query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "manufacturer` WHERE manufacturer_id NOT IN (SELECT `object_id` FROM `" . DB_PREFIX . "url_alias` WHERE `object_type` = 'manufacturer')");
        $query2 = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "manufacturer`");
        return $query->row['total'] * 100 / $query2->row['total'];
    }
}
