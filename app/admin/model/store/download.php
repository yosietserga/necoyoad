<?php
/**
 * ModelStoreDownload
 * 
 * @package NecoTienda
 * @author Yosiet Serga
 * @copyright Inversiones Necoyoad, C.A.
 * @version 1.0.0
 * @access public
 */
class ModelStoreDownload extends Model {

    protected string $object_type  = "download";
    protected string $table        = "download";
    protected string $pkey         = "download_id";

    protected array $fields = [
        "remaining" => [
            "name"      => "remaining",
            "type"      => "integer",
        ],
        "filename" => [
            "name"      => "filename",
            "type"      => "string",
        ],
        "mask" => [
            "name"      => "mask",
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

    protected array $relations = ["descriptions", "stores"];

    public function init() {
        $this->on("save", function($args) {
            $d = $args[0];
            $id = $d['id'];
            $data = $d['data'];
            $action = $d['action'];

            if ($action == "update") {
                $query = $this->db->query("SELECT filename from " . DB_PREFIX . "download WHERE download_id = '" . (int)$id . "'");
                $filename = $query->row['filename'];
                $this->db->query("UPDATE " . DB_PREFIX . "order_download SET " .
                    "`filename` = '" . $this->db->escape($filename) . "', " .
                    "`mask` = '" . $this->db->escape(basename($data['mask'])) . "' " .
                    "WHERE `filename` = '" . $this->db->escape($filename) . "'");
            } else {
                $data["mask"] = md5(mt_rand(111111, 99999) . time());
                $this->db->query("UPDATE " . DB_PREFIX . "download SET " .
                "`mask` = '" . $this->db->escape(basename($data['mask'])) . "' " .
                    "WHERE `download_id` = '" . (int)$id . "'");
            }

            if (!empty($data['Products'])) {
                $this->db->query("DELETE FROM " . DB_PREFIX . "product_to_download WHERE download_id='" . (int) $id . "'");
                foreach ($data['Products'] as $product_id => $value) {
                    if ($value == 0) continue;
                    $this->db->query("REPLACE INTO " . DB_PREFIX . "product_to_download (product_id, download_id) VALUES ('" . (int) $product_id . "','" . (int)$id . "')");
                }
            }
        });

        $this->addFilter("join", function ($args) {
            $sql = $args['sql'];
            $data = $args['data'];

            if (isset($data['product_id']) || isset($data['product'])) {
                $sql .= "LEFT JOIN " . DB_PREFIX . "product_to_download p2d ON (t.download_id = p2d.download_id) ";
                $sql .= "LEFT JOIN " . DB_PREFIX . "product p ON (p2d.download_id = p.product_id) ";
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
}