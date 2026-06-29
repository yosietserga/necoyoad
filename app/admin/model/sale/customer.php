<?php
/**
 * ModelSaleCustomer
 * 
 * @package NecoTienda
 * @author Yosiet Serga
 * @copyright Inversiones Necoyoad, C.A.
 * @version 1.0.0
 * @access public
 * @see Model
 */
class ModelSaleCustomer extends Model {

    protected string $table        = "customer";
    protected string $pkey         = "customer_id";
    protected string $object_type  = "customer";

    protected array $fields = [
        "customer_group_id" => [
            "name"      => "customer_group_id",
            "type"      => "integer",
        ],
        "address_id" => [
            "name"      => "address_id",
            "type"      => "integer",
        ],
        "store_id" => [
            "name"      => "store_id",
            "type"      => "integer",
        ],
        "firstname" => [
            "name"      => "firstname",
            "type"      => "string",
        ],
        "lastname" => [
            "name"      => "lastname",
            "type"      => "string",
        ],
        "company" => [
            "name"      => "company",
            "type"      => "string",
        ],
        "rif" => [
            "name"      => "rif",
            "type"      => "string",
        ],
        "email" => [
            "name"      => "email",
            "required"  => true,
            "type"      => "string",
        ],
        "referenced_by" => [
            "name"      => "referenced_by", //relation to customer table referenced_by = customer_id
            "type"      => "integer",
        ],
        "sex" => [
            "name"      => "sex",
            "type"      => "string",
        ],
        "cart" => [
            "name"      => "cart",
            "type"      => "text",
        ],
        "telephone" => [
            "name"      => "telephone",
            "type"      => "string",
        ],
        "password" => [
            "name"      => "password",
            "type"      => "string",
            //TODO: add password field type to auto-encrypt
        ],
        "activation_code" => [
            "name"      => "activation_code",
            "type"      => "string",
        ],
        "birthday" => [
            "name"      => "birthday",
            "type"      => "date",
        ],
        "photo" => [
            "name"      => "photo",
            "type"      => "string",
        ],
        "ip" => [
            "name"      => "ip",
            "type"      => "string",
        ],
        "visits" => [
            "name"      => "visits",
            "type"      => "integer",
        ],
        "congrats" => [
            "name"      => "congrats",
            "default"   => 0,
            "type"      => "boolean",
        ],
        "newsletter" => [
            "name"      => "newsletter",
            "default"   => 1,
            "type"      => "boolean",
        ],
        "approved" => [
            "name"      => "approved",
            "default"   => 1,
            "type"      => "boolean",
        ],
        "banned" => [
            "name"      => "banned",
            "default"   => 0,
            "type"      => "boolean",
        ],
        "complete" => [
            "name"      => "complete",
            "default"   => 0,
            "type"      => "boolean",
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
        $this->on("delete", function ($args) {
            $d = $args[0];
            $id = $d['id'];
            //TODO: trigger action to delete related records
            //TODO: set conditional owner_id and author_id to zero instead of delete
            $this->db->query("DELETE FROM " . DB_PREFIX . "address WHERE customer_id = '" . (int)$id . "'");
            $this->db->query("DELETE FROM " . DB_PREFIX . "balance WHERE customer_id = '" . (int)$id . "'");
            $this->db->query("DELETE FROM " . DB_PREFIX . "order_payment WHERE customer_id = '" . (int)$id . "'");
            $this->db->query("DELETE FROM " . DB_PREFIX . "post WHERE author_id = '" . (int)$id . "'");
            $this->db->query("DELETE FROM " . DB_PREFIX . "product WHERE owner_id = '" . (int)$id . "'");
            $this->db->query("DELETE FROM " . DB_PREFIX . "store WHERE owner_id = '" . (int)$id . "'");
        });

        $this->addFilter("select", function ($args) {
            $sql = $args['sql'];
            $data = $args['data'];
            $sql = " *, " .
                "t.customer_id AS cid, " .
                "t.customer_id AS customer_id, " .
                "t.address_id AS address_id, " .
                "a.country_id AS country_id, " .
                "a.zone_id AS zone_id, " .
                "t.firstname AS firstname, " .
                "t.lastname AS lastname, " .
                "t.company AS company, " .
                "CONCAT(t.firstname, ' ', t.lastname) AS name ";
            return ["sql" => $sql, "data" => $data];
        });

        $this->addFilter("join", function ($args) {
            $sql = $args['sql'];
            $data = $args['data'];

            $sql .= "LEFT JOIN " . DB_PREFIX . "customer_group cg ON (t.customer_group_id = cg.customer_group_id) ";
            $sql .= "LEFT JOIN " . DB_PREFIX . "address a ON (t.address_id = a.address_id) ";
            $sql .= "LEFT JOIN " . DB_PREFIX . "country co ON (co.country_id = a.country_id) ";
            $sql .= "LEFT JOIN " . DB_PREFIX . "zone z ON (z.zone_id = a.zone_id) ";

            return ["sql" => $sql, "data" => $data];
        });

        $this->addFilter("where", function ($args) {
            $criteria = $args['criteria'];
            $data = $args['data'];
            
            if (isset($data['address_id'])) $data['address_id'] = !is_array($data['address_id']) && !empty($data['address_id']) ? array($data['address_id']) : $data['address_id'];
            if (isset($data['country_id'])) $data['country_id'] = !is_array($data['country_id']) && !empty($data['country_id']) ? array($data['country_id']) : $data['country_id'];
            if (isset($data['zone_id'])) $data['zone_id'] = !is_array($data['zone_id']) && !empty($data['zone_id']) ? array($data['zone_id']) : $data['zone_id'];

            if (isset($data['address_id']) && !empty($data['address_id'])) {
                $criteria[] = " a.address_id IN (" . implode(', ', $data['address_id']) . ") ";
            }

            if (isset($data['country_id']) && !empty($data['country_id'])) {
                $criteria[] = " co.country_id IN (" . implode(', ', $data['country_id']) . ") ";
            }

            if (isset($data['zone_id']) && !empty($data['zone_id'])) {
                $criteria[] = " z.zone_id IN (" . implode(', ', $data['zone_id']) . ") ";
            }

            if (isset($data['name']) && !empty($data['name'])) {
                $criteria[] = " LCASE(CONCAT(t.firstname, ' ', t.lastname)) LIKE '%" . $this->db->escape(strtolower($data['name'])) . "%' collate utf8_general_ci ";
            }

            return ["criteria" => $criteria, "data" => $data];
        });
    }

	public function getAllTotalAwaitingApproval() {
		return $this->getAllTotal(array(
			'status' => 0,
			'approved' => 0
		));
	}
	
	public function getAllTotalByCustomerGroupId($customer_group_id) {
		return $this->getAllTotal(array(
			'customer_group_id' => $customer_group_id
		));
	}
    
	public function approve($id)
    {
        $this->db->query("UPDATE `" . DB_PREFIX . "customer` SET `approved` = '1' WHERE `customer_id` = '" . (int)$id . "'");
	}
	
    public function desapprove($id) {
        $this->db->query("UPDATE `" . DB_PREFIX . "customer` SET `approved` = '0' WHERE `customer_id` = '" . (int)$id . "'");
    }
}