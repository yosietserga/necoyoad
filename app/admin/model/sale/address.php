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
class ModelSaleAddress extends Model {

    protected string $table        = "address";
    protected string $pkey         = "address_id";
    protected string $object_type  = "address";

    protected array $fields = [
        "customer_id" => [
            "name"      => "customer_id",
            "required"  => true,
            "type"      => "integer",
        ],
        "country_id" => [
            "name"      => "country_id",
            "required"  => true,
            "type"      => "integer",
        ],
        "zone_id" => [
            "name"      => "zone_id",
            "required"  => true,
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
        "address_1" => [
            "name"      => "address_1",
            "type"      => "string",
        ],
        "address_2" => [
            "name"      => "address_2",
            "type"      => "string",
        ],
        "city" => [
            "name"      => "city",
            "type"      => "string",
        ],
        "street" => [
            "name"      => "city",
            "type"      => "string",
        ],
        "postcode" => [
            "name"      => "postcode",
            "type"      => "string",
        ],
    ];

    public function init() {

        $this->addFilter("select", function ($args) {
            $sql = $args['sql'];
            $data = $args['data'];
            $sql = " *, zd.title AS zone, cod.title AS country, z.code AS zone_code ";
            return ["sql" => $sql, "data" => $data];
        });

        $this->addFilter("join", function ($args) {
            $sql = $args['sql'];
            $data = $args['data'];

            $sql .= "LEFT JOIN " . DB_PREFIX . "country co ON (co.country_id = t.country_id) ";
            $sql .= "LEFT JOIN " . DB_PREFIX . "description cod ON (co.country_id = cod.object_id) ";

            $sql .= "LEFT JOIN " . DB_PREFIX . "zone z ON (z.zone_id = t.zone_id) ";
            $sql .= "LEFT JOIN " . DB_PREFIX . "description zd ON (z.zone_id = zd.object_id) ";

            return ["sql" => $sql, "data" => $data];
        });

        $this->addFilter("where", function ($args) {
            $criteria = $args['criteria'];
            $data = $args['data'];

            if (isset($data['address']) && !empty($data['address'])) {
                $criteria[] = " LCASE(CONCAT(t.address_1, ' ', t.address_2)) LIKE '%" . $this->db->escape(strtolower($data['address'])) . "%' collate utf8_general_ci ";
            }

            if (isset($data['name']) && !empty($data['name'])) {
                $criteria[] = " LCASE(CONCAT(t.firstname, ' ', t.lastname)) LIKE '%" . $this->db->escape(strtolower($data['name'])) . "%' collate utf8_general_ci ";
            }

            if (isset($data['zone']) && !empty($data['zone'])) {
                $criteria[] = " LCASE(zd.`title`) LIKE '%" . $this->db->escape(strtolower($data['zone'])) . "%' collate utf8_general_ci ";
                $criteria[] = " LCASE(zd.`object_type`) = 'zone' ";
            }

            if (isset($data['country']) && !empty($data['country'])) {
                $criteria[] = " LCASE(cod.`title`) LIKE '%" . $this->db->escape(strtolower($data['country'])) . "%' collate utf8_general_ci ";
                $criteria[] = " LCASE(cod.`object_type`) = 'country' ";
            }

            return ["criteria" => $criteria, "data" => $data];
        });
    }

    public function deleteByCustomerId($id) {
        $addresses = $this->getAll(["customer_id"=>$id]);
        foreach ($addresses as $address) {
            $this->delete($address['address_id']);
        }
    }

  	public function getTotalAddressesByCustomerId($customer_id) {
        return $this->getAllTotal(array(
            'customer_id' => $customer_id
        ));
  	}
  	
  	public function getTotalAddressesByCountryId($country_id) {
        return $this->getAllTotal(array(
            'country_id' => $country_id
        ));
  	}	
  	
  	public function getTotalAddressesByZoneId($zone_id) {
        return $this->getAllTotal(array(
            'zone_id' => $zone_id
        ));
  	}
}
