<?php
/**
 * ModelSaleBank
 * 
 * @package NecoTienda
 * @author Yosiet Serga
 * @copyright Inversiones Necoyoad, C.A.
 * @version 1.0.0
 * @access public
 * @see Model
 */
class ModelSaleBankAccount extends Model {

    protected string $table        = "bank_account";
    protected string $pkey         = "bank_account_id";
    protected string $object_type  = "bank_account";

    protected array $fields = [
        "bank_id" => [
            "name"      => "bank_id",
            "required"  => true,
            "type"      => "integer",
        ],
        "type" => [
            "name"      => "type",
            "type"      => "string",
        ],
        "number" => [
            "name"      => "number",
            "type"      => "string",
        ],
        "accountholder" => [
            "name"      => "amount_available",
            "type"      => "string",
        ],
        "rif" => [
            "name"      => "rif",
            "type"      => "string",
        ],
        "email" => [
            "name"      => "email",
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

    protected array $relations = ["stores"];

    public function init()
    {
        $this->addFilter("select", function ($args) {
            $sql = $args['sql'];
            $data = $args['data'];
            $sql = " *, zd.title AS zone, cod.title AS country, z.code AS zone_code ";
            return ["sql" => $sql, "data" => $data];
        });

        $this->addFilter("join", function ($args) {
            $sql = $args['sql'];
            $data = $args['data'];

            $sql .= "LEFT JOIN `" . DB_PREFIX . "bank` b ON (b.bank_id=t.bank_id) ";

            return ["sql" => $sql, "data" => $data];
        });

        $this->addFilter("where", function ($args) {
            $criteria = $args['criteria'];
            $data = $args['data'];

            if (isset($data['bank']) && !empty($data['bank'])) {
                $criteria[] = " LCASE(b.`name`) LIKE '%" . $this->db->escape(strtolower($data['bank'])) . "%' collate utf8_general_ci ";
            }

            return ["criteria" => $criteria, "data" => $data];
        });
    }
}
