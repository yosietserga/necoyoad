<?php
/**
 * ModelSaleCoupon
 * 
 * @package NecoTienda
 * @author Yosiet Serga
 * @copyright Inversiones Necoyoad, C.A.
 * @version 1.0.0
 * @access public
 * @see Model
 */
class ModelSaleCoupon extends Model {

    protected string $table        = "coupon";
    protected string $pkey         = "coupon_id";
    protected string $object_type  = "coupon";

    protected array $fields = [
        "code" => [
            "name"      => "code",
            "type"      => "string",
        ],
        "type" => [
            "name"      => "type",
            "type"      => "string",
        ],
        "logged" => [
            "name"      => "logged",
            "default"   => 1,
            "type"      => "boolean",
        ],
        "shipping" => [
            "name"      => "shipping",
            "default"   => 0,
            "type"      => "boolean",
        ],
        "total" => [
            "name"      => "total",
            "type"      => "float",
        ],
        "discount" => [
            "name"      => "discount",
            "type"      => "float",
        ],
        "uses_customer" => [
            "name"      => "uses_customer",
            "type"      => "integer",
        ],
        "uses_total" => [
            "name"      => "uses_total",
            "type"      => "integer",
        ],
        "date_start" => [
            "name"      => "date_start",
            "type"      => "date",
        ],
        "date_end" => [
            "name"      => "date_end",
            "type"      => "date",
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

    public function init()
    {
        $this->on("save", function ($args) {
            $d = $args[0];
            $id = $d['id'];
            $data = $d['data'];

            if ($data["action"] == "update") $this->db->query("DELETE FROM " . DB_PREFIX . "coupon_product WHERE coupon_id = '" . (int)$d['id'] . "'");
            foreach ((array)$data['Products'] as $product_id => $value) {
                if ($value == 0) continue;
                $this->db->query("INSERT INTO " . DB_PREFIX . "coupon_product SET " .
                "product_id = '" . (int)$product_id . "', " .
                "coupon_id  = '" . (int)$d['id'] . "' ");
            }
        });

        $this->addFilter("copy", function ($args) {
            $id = $args['id'];
            $data = $args['data'];

            $data['code'] = uniqid();
            $data = array_merge($data, array('Products' => $this->getProducts($id)));

            return ["id" => $id, "data" => $data];
        });

        $this->on("delete", function($args) {
            $id = $args['id'];
            $this->db->query("DELETE FROM " . DB_PREFIX . "coupon_product WHERE coupon_id = '" . (int)$id . "'");
        });
    }
	/**
	 * ModelSaleCoupon::getCouponProducts()
	 * 
	 * @param int $coupon_id
     * @see DB
	 * @return array sql records
	 */
	public function getProducts($coupon_id) {
		$coupon_product_data = [];
		
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "coupon_product WHERE coupon_id = '" . (int)$coupon_id . "'");
		
		foreach ($query->rows as $result) {
			$coupon_product_data[] = $result['product_id'];
		}
		
		return $coupon_product_data;
	}
}
