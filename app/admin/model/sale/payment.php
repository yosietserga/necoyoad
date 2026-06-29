<?php
/**
 * ModelSalePayment
 * 
 * @package NecoTienda
 * @author Yosiet Serga
 * @copyright Inversiones Necoyoad, C.A.
 * @version 1.0.0
 * @access public
 * @see Model
 */
class ModelSalePayment extends Model {

    protected string $table        = "order_payment";
    protected string $pkey         = "order_payment_id";
    protected string $object_type  = "order_payment";

    protected array $fields = [
        "order_id" => [
            "name"      => "order_id",
            "required"  => true,
            "type"      => "integer",
        ],
        "customer_id" => [
            "name"      => "customer_id",
            "required"  => true,
            "type"      => "integer",
        ],
        "store_id" => [
            "name"      => "store_id",
            "required"  => true,
            "type"      => "integer",
        ],
        "order_payment_status_id" => [
            "name"      => "order_payment_status_id",
            "required"  => true,
            "type"      => "integer",
        ],
        "bank_account_id" => [
            "name"      => "bank_account_id",
            "type"      => "integer",
        ],
        "transac_number" => [
            "name"      => "transac_number",
            "type"      => "string",
        ],
        "transac_date" => [
            "name"      => "transac_date",
            "type"      => "date",
        ],
        "bank_from" => [
            "name"      => "bank_from",
            "type"      => "string",
        ],
        "payment_method" => [
            "name"      => "payment_method",
            "type"      => "string",
        ],
        "amount" => [
            "name"      => "amount",
            "type"      => "float",
        ],
        "comment" => [
            "name"      => "comment",
            "type"      => "text",
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

    public function init()
    {
        $this->addFilter("select", function ($args) {
            $sql = $args['sql'];
            $data = $args['data'];
            $sql = " *, ops.name AS status, op.date_added AS dateAdded, bk.name AS bank ";
            return ["sql" => $sql, "data" => $data];
        });

        $this->addFilter("join", function ($args) {
            $sql = $args['sql'];
            $data = $args['data'];

            $sql .= "LEFT JOIN `" . DB_PREFIX . "customer` c ON (t.customer_group_id = c.customer_group_id) ";
            $sql .= "LEFT JOIN `" . DB_PREFIX . "order_payment_status ops ON (ops.order_payment_status_id=op.order_payment_status_id) ";
            $sql .= "LEFT JOIN `" . DB_PREFIX . "bank_account` ba ON (ba.bank_account_id=op.bank_account_id) ";
            $sql .= "LEFT JOIN `" . DB_PREFIX . "bank` bk ON (ba.bank_id=bk.bank_id) ";
            $sql .= "LEFT JOIN `" . DB_PREFIX . "order` o ON (o.order_id=op.order_id) ";

            return ["sql" => $sql, "data" => $data];
        });

        $this->addFilter("where", function ($args) {
            $criteria = $args['criteria'];
            $data = $args['data'];

            if (isset($data['customer_name']) && !empty($data['customer_name'])) {
                $criteria[] = " LCASE(CONCAT(c.firstname, ' ', c.lastname)) LIKE '%" . $this->db->escape(strtolower($data['customer_name'])) . "%' collate utf8_general_ci ";
            }

            if (isset($data['customer_email']) && !empty($data['customer_email'])) {
                $criteria[] = " LCASE(c.email) LIKE '%" . $this->db->escape(strtolower($data['customer_email'])) . "%' collate utf8_general_ci ";
            }

            if (isset($data['bank']) && !empty($data['bank'])) {
                $criteria[] = " LCASE(bk.name) LIKE '%" . $this->db->escape(strtolower($data['bank'])) . "%' collate utf8_general_ci ";
            }

            return ["criteria" => $criteria, "data" => $data];
        });
    }
}
