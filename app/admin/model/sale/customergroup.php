<?php
/**
 * ModelSaleCustomerGroup
 * 
 * @package   NecoTienda
 * @author Yosiet Serga
 * @copyright Inversiones Necoyoad, C.A.
 * @version 1.0.0
 * @access public
 * @see Model
 */
class ModelSaleCustomerGroup extends Model {

    protected string $table        = "customer_group";
    protected string $pkey         = "customer_group_id";
    protected string $object_type  = "customer_group";

    protected array $fields = [
        "name" => [
            "name"      => "name",
            "required"  => true,
            "type"      => "string",
        ],
        "params" => [
            "name"      => "params",
            "type"      => "text",
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

    protected array $relations = ["stores"];

    public function init() {
        $this->on("delete", function($args) {
            $d = $args[0];
            $id = $d['id'];
            $this->db->query("DELETE FROM " . DB_PREFIX . "product_discount WHERE customer_group_id = '" . (int)$d['id'] . "'");
            $this->db->query("UPDATE " . DB_PREFIX . "customer SET customer_group_id = 0 WHERE customer_group_id = '" . (int)$d['id'] . "'");
        });

        $this->addFilter("join", function ($args) {
            $sql = $args['sql'];
            $data = $args['data'];

            if (isset($data['customer_id']) || isset($data['customer_name'])  || isset($data['customer_email'])) {
                $sql .= "LEFT JOIN " . DB_PREFIX . "customer c ON (t.customer_group_id = c.customer_group_id) ";
            }

            return ["sql" => $sql, "data" => $data];
        });

        $this->addFilter("where", function ($args) {
            $criteria = $args['criteria'];
            $data = $args['data'];

            if (isset($data['customer_id'])) $data['customer_id'] = !is_array($data['customer_id']) && !empty($data['customer_id']) ? array($data['customer_id']) : $data['customer_id'];

            if (isset($data['customer_id']) && !empty($data['customer_id'])) {
                $criteria[] = " c.customer_id IN (" . implode(', ', $data['customer_id']) . ") ";
            }

            if (isset($data['customer_name']) && !empty($data['customer_name'])) {
                $criteria[] = " LCASE(CONCAT(c.firstname, ' ', c.lastname)) LIKE '%" . $this->db->escape(strtolower($data['customer_name'])) . "%' collate utf8_general_ci ";
            }

            if (isset($data['customer_email']) && !empty($data['customer_email'])) {
                $criteria[] = " LCASE(c.email) LIKE '%" . $this->db->escape(strtolower($data['customer_email'])) . "%' collate utf8_general_ci ";
            }

            return ["criteria" => $criteria, "data" => $data];
        });
    }
}
