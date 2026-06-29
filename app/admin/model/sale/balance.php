<?php
/**
 * ModelSaleBalance
 * 
 * @package NecoTienda
 * @author Yosiet Serga
 * @copyright Inversiones Necoyoad, C.A.
 * @version 1.0.0
 * @access public
 * @see Model
 */
class ModelSaleBalance extends Model {

    protected string $table        = "balance";
    protected string $pkey         = "balance_id";
    protected string $object_type  = "balance";

    protected array $fields = [
        "customer_id" => [
            "name"      => "customer_id",
            "required"  => true,
            "type"      => "integer",
        ],
        "currency_id" => [
            "name"      => "currency_id",
            "required"  => true,
            "type"      => "integer",
        ],
        "type" => [
            "name"      => "type",
            "type"      => "string",
        ],
        "amount" => [
            "name"      => "amount",
            "type"      => "float",
        ],
        "amount_available" => [
            "name"      => "amount_available",
            "type"      => "float",
        ],
        "amount_deferred" => [
            "name"      => "amount_deferred",
            "type"      => "float",
        ],
        "amount_blocked" => [
            "name"      => "amount_blocked",
            "type"      => "float",
        ],
        "amount_total" => [
            "name"      => "amount_total",
            "type"      => "float",
        ],
        "description" => [
            "name"      => "description",
            "type"      => "text",
        ],
        "currency_code" => [
            "name"      => "currency_code",
            "type"      => "string",
        ],
        "currency_value" => [
            "name"      => "currency_value",
            "type"      => "float",
        ],
        "currency_title" => [
            "name"      => "currency_title",
            "type"      => "string",
        ],
        "date_added" => [
            "name"      => "date_added",
            "default"   => "NOW()",
            "type"      => "sql",
        ],
    ];

    public function init() {
        $this->addFilter("join", function ($args) {
            $sql = $args['sql'];
            $data = $args['data'];

            if (isset($data['customer_name']) || isset($data['customer_email'])) {
                $sql .= " LEFT JOIN " . DB_PREFIX . "customer c ON (t.customer_id = c.customer_id) ";
            }

            return ["sql" => $sql, "data" => $data];
        });

        $this->addFilter("where", function ($args) {
            $criteria = $args['criteria'];
            $data = $args['data'];

            if (isset($data['from_amount']) || isset($data['to_amount'])) {

                if (isset($data['from_amount']) && !empty($data['from_amount'])) {
                    $criteria[] = " t.`amount` >= '" . $this->db->escape((float)$data['from_amount']) . "' ";
                }

                if (isset($data['to_amount']) && !empty($data['to_amount'])) {
                    $criteria[] = " t.`amount` <= '" . $this->db->escape((float)$data['to_amount']) . "' ";
                }
            } elseif (isset($data['amount']) && !empty($data['amount'])) {
                $criteria[] = " t.`amount` = '" . $this->db->escape((float)$data['amount']) . "' ";
            }

            if (isset($data['customer_name']) && !empty($data['customer_name'])) {
                $criteria[] = " LCASE(CONCAT(c.`firstname`, ' ', c.`lastname`)) LIKE '%" . $this->db->escape(strtolower($data['customer_name'])) . "%' collate utf8_general_ci ";
            }

            if (isset($data['customer_email']) && !empty($data['customer_email'])) {
                $criteria[] = " LCASE(c.`email`) LIKE '%" . $this->db->escape(strtolower($data['customer_email'])) . "%' collate utf8_general_ci ";
            }

            return ["criteria" => $criteria, "data" => $data];
        });
    }
}
