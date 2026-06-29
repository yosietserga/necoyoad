<?php  
/**
 * ModelSaleOrder
 * 
 * @package NecoTienda
 * @author Yosiet Serga
 * @copyright Inversiones Necoyoad, C.A.
 * @version 1.0.0
 * @access public
 */
class ModelSaleOrder extends Model {

    protected string $table        = "order";
    protected string $pkey         = "order_id";
    protected string $object_type  = "order";

    protected array $fields = [
        "invoice_id" => [
            "name"      => "invoice_id",
            "type"      => "integer",
        ],
        "invoice_prefix" => [
            "name"      => "invoice_prefix",
            "type"      => "string",
        ],
        "customer_id" => [
            "name"      => "customer_id",
            "required"  => true,
            "type"      => "integer",
        ],
        "order_status_id" => [
            "name"      => "order_status_id",
            "required"  => true,
            "type"      => "integer",
        ],
        "language_id" => [
            "name"      => "language_id",
            "type"      => "integer",
        ],
        "coupon_id" => [
            "name"      => "coupon_id",
            "type"      => "integer",
        ],
        "currency_id" => [
            "name"      => "currency_id",
            "type"      => "integer",
        ],
        "currency" => [
            "name"      => "currency",
            "type"      => "string",
        ],
        "value" => [
            "name"      => "value",
            "type"      => "string",
        ],
        "store_name" => [
            "name"      => "store_name",
            "type"      => "string",
        ],
        "store_url" => [
            "name"      => "store_url",
            "type"      => "string",
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
            "type"      => "string",
        ],
        "shipping_firstname" => [
            "name"      => "shipping_firstname",
            "type"      => "string",
        ],
        "shipping_lastname" => [
            "name"      => "shipping_lastname",
            "type"      => "string",
        ],
        "shipping_company" => [
            "name"      => "shipping_company",
            "type"      => "string",
        ],
        "shipping_address_1" => [
            "name"      => "shipping_address_1",
            "type"      => "string",
        ],
        "shipping_address_2" => [
            "name"      => "shipping_address_2",
            "type"      => "string",
        ],
        "shipping_city" => [
            "name"      => "shipping_city",
            "type"      => "string",
        ],
        "shipping_postcode" => [
            "name"      => "shipping_postcode",
            "type"      => "string",
        ],
        "shipping_zone" => [
            "name"      => "shipping_zone",
            "type"      => "string",
        ],
        "shipping_zone_id" => [
            "name"      => "shipping_zone_id",
            "type"      => "string",
        ],
        "shipping_country" => [
            "name"      => "shipping_country",
            "type"      => "string",
        ],
        "shipping_country_id" => [
            "name"      => "shipping_country_id",
            "type"      => "string",
        ],
        "shipping_address_format" => [
            "name"      => "shipping_address_format",
            "type"      => "string",
        ],
        "shipping_method" => [
            "name"      => "shipping_method",
            "type"      => "string",
        ],
        "payment_firstname" => [
            "name"      => "payment_firstname",
            "type"      => "string",
        ],
        "payment_lastname" => [
            "name"      => "payment_lastname",
            "type"      => "string",
        ],
        "payment_company" => [
            "name"      => "payment_company",
            "type"      => "string",
        ],
        "payment_address_1" => [
            "name"      => "payment_address_1",
            "type"      => "string",
        ],
        "payment_address_2" => [
            "name"      => "payment_address_2",
            "type"      => "string",
        ],
        "payment_city" => [
            "name"      => "payment_city",
            "type"      => "string",
        ],
        "payment_postcode" => [
            "name"      => "payment_postcode",
            "type"      => "string",
        ],
        "payment_zone" => [
            "name"      => "payment_zone",
            "type"      => "string",
        ],
        "payment_zone_id" => [
            "name"      => "payment_zone_id",
            "type"      => "string",
        ],
        "payment_country" => [
            "name"      => "payment_country",
            "type"      => "string",
        ],
        "payment_country_id" => [
            "name"      => "payment_country_id",
            "type"      => "string",
        ],
        "payment_address_format" => [
            "name"      => "payment_address_format",
            "type"      => "string",
        ],
        "payment_method" => [
            "name"      => "payment_method",
            "type"      => "string",
        ],
        "total" => [
            "name"      => "total",
            "type"      => "float",
        ],
        "comment" => [
            "name"      => "comment",
            "type"      => "text",
        ],
        "ip" => [
            "name"      => "ip",
            "type"      => "string",
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
        $this->on("save", function ($args) {
            $d = $args[0];
            $id = $d['id'];
            $data = $d['data'];
            $action = $d['action'];

            if ($action == "update") {
                $this->db->query("DELETE FROM " . DB_PREFIX . "order_total WHERE order_id = '" . (int)$d['id'] . "'");
                $this->db->query("DELETE FROM " . DB_PREFIX . "order_product WHERE order_id = '" . (int)$d['id'] . "'");
            }

            foreach ((array)$data['products'] as $product) {
                if (!$product['product_id']) continue;
                $product['order_id'] = $data['id'];
                $this->setProducts($data);
            }

            foreach ($data['totals'] as $key => $value) {
                $this->db->query("REPLACE INTO " . DB_PREFIX . "order_total SET text = '" . $this->db->escape($value) . "' WHERE order_total_id = '" . (int)$key . "'");
            }
        });

        $this->on("delete", function ($args) {
            $d = $args[0];
            $id = $d['id'];

            //TODO: improve this, check if the order has returned stock yet, if order has valid status to return stock, this should be into controller events handler
            if ($this->config->get('config_stock_subtract')) {
                $order_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order` " .
                    "WHERE order_status_id > '0' " .
                    "AND order_id = '" . (int)$id . "'");

                if ($order_query->num_rows) {
                    $product_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_product WHERE order_id = '" . (int)$id . "'");

                    foreach ($product_query->rows as $product) {
                        $this->db->query("UPDATE `" . DB_PREFIX . "product` SET " .
                        "quantity = (quantity + " . (int)$product['quantity'] . ") " .
                        "WHERE product_id = '" . (int)$product['product_id'] . "'");

                        $option_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_option " .
                        "WHERE order_id = '" . (int)$id . "' " .
                            "AND order_product_id = '" . (int)$product['order_product_id'] . "'");

                        foreach ($option_query->rows as $option) {
                            $this->db->query("UPDATE " . DB_PREFIX . "product_option_value SET " .
                            "quantity = (quantity + " . (int)$product['quantity'] . ") " .
                            "WHERE product_option_value_id = '" . (int)$option['product_option_value_id'] . "' " .
                            "AND subtract = '1'");
                        }
                    }
                }
            }

            $this->db->query("DELETE FROM " . DB_PREFIX . "order_history WHERE order_id = '" . (int)$id . "'");
            $this->db->query("DELETE FROM " . DB_PREFIX . "order_product WHERE order_id = '" . (int)$id . "'");
            $this->db->query("DELETE FROM " . DB_PREFIX . "order_option WHERE order_id = '" . (int)$id . "'");
            $this->db->query("DELETE FROM " . DB_PREFIX . "order_download WHERE order_id = '" . (int)$id . "'");
            $this->db->query("DELETE FROM " . DB_PREFIX . "order_total WHERE order_id = '" . (int)$id . "'");
        });

        $this->addFilter("select", function ($args) {
            $sql = $args['sql'];
            $data = $args['data'];
            $sql = " *, " .
                "os.ref AS status, " .

                (isset($data['shipping_zone']) ? "zosd.title AS shipping_zone, " : "") .
                (isset($data['payment_zone']) ? "zopd.title AS payment_zone, " : "") .
                (isset($data['shipping_country']) ? "cosd.title AS shipping_country, " : "") .
                (isset($data['payment_country']) ? "copd.title AS payment_country, " : "") .

                "zos.code AS shipping_zone_code, " .
                "cos.iso_code_2 AS shipping_iso_code_2, " .
                "cos.iso_code_3 AS shipping_iso_code_3, " .

                "zop.code AS payment_zone_code, " .
                "cop.iso_code_2 AS payment_iso_code_2, " .
                "cop.iso_code_3 AS payment_iso_code_3 ";
            return ["sql" => $sql, "data" => $data];
        });

        $this->addFilter("join", function ($args) {
            $sql = $args['sql'];
            $data = $args['data'];

            $sql .= "LEFT JOIN `" . DB_PREFIX . "country` cos ON (cos.country_id = t.shipping_country_id) ";
            $sql .= "LEFT JOIN `" . DB_PREFIX . "country` cop ON (cop.country_id = t.payment_country_id) ";
            $sql .= "LEFT JOIN `" . DB_PREFIX . "zone` zos ON (zos.zone_id = t.shipping_zone_id) ";
            $sql .= "LEFT JOIN `" . DB_PREFIX . "zone` zop ON (zop.zone_id = t.payment_zone_id) ";

            if (isset($data['shipping_country']) && !empty($data['shipping_country'])) {
                $sql .= "LEFT JOIN `" . DB_PREFIX . "description` cosd ON (cos.country_id = cosd.object_id) ";
            }

            if (isset($data['shipping_zone']) && !empty($data['shipping_zone'])) {
                $sql .= "LEFT JOIN `" . DB_PREFIX . "description` zosd ON (zos.zone_id = zosd.object_id) ";
            }
            
            if (isset($data['payment_country']) && !empty($data['payment_country'])) {
                $sql .= "LEFT JOIN `" . DB_PREFIX . "description` copd ON (cop.country_id = copd.object_id) ";
            }

            if (isset($data['payment_zone']) && !empty($data['payment_zone'])) {
                $sql .= "LEFT JOIN `" . DB_PREFIX . "description` zopd ON (zos.zone_id = zopd.object_id) ";
            }
            
            $sql .= "LEFT JOIN `" . DB_PREFIX . "customer` c ON (t.customer_group_id = c.customer_id) ";
            $sql .= "LEFT JOIN `" . DB_PREFIX . "customer_group` cg ON (t.customer_group_id = cg.customer_group_id) ";
            $sql .= "LEFT JOIN `" . DB_PREFIX . "status` os ON (t.order_status_id = os.status_id AND os.object_type = 'order_status') ";
            $sql .= "LEFT JOIN `" . DB_PREFIX . "description` dos ON (os.status_id = dos.object_id AND dos.object_type = 'order_status') ";
            $sql .= "LEFT JOIN `" . DB_PREFIX . "currency` cur ON (t.currency_id = cur.currency_id) ";

            return ["sql" => $sql, "data" => $data];
        });

        $this->addFilter("where", function ($args) {
            $criteria = $args['criteria'];
            $data = $args['data'];

            $criteria[] = " dos.language_id = '" . (int)$this->config->get('config_language_id') . "'";

            if (isset($data['customer_name']) && !empty($data['customer_name'])) {
                $criteria[] = " LCASE(CONCAT(t.firstname, ' ', t.lastname)) LIKE '%" . $this->db->escape(strtolower($data['name'])) . "%' collate utf8_general_ci ";
            }

            if (isset($data['from_total']) || isset($data['to_total'])) {

                if (isset($data['from_total']) && !empty($data['from_total'])) {
                    $criteria[] = " t.`total` >= '" . $this->db->escape((float)$data['from_total']) . "' ";
                }

                if (isset($data['to_total']) && !empty($data['to_total'])) {
                    $criteria[] = " t.`total` <= '" . $this->db->escape((float)$data['to_total']) . "' ";
                }
            } elseif (isset($data['total']) && !empty($data['total'])) {
                $criteria[] = " t.`total` = '" . $this->db->escape((float)$data['total']) . "' ";
            }

            return ["criteria" => $criteria, "data" => $data];
        });
    }

	public function setProducts($data) {
        //TODO: use Product model class
		$product_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product p ".
        	"LEFT JOIN " . DB_PREFIX . "description pd ON (p.product_id = pd.object_id) ".
        "WHERE p.product_id='" . (int)$data['product_id'] . "' ".
        	"AND pd.object_type = 'product' ".
        	"AND pd.language_id = '". $this->config->get('config_language_id') ."' ");
							
		$this->db->query("INSERT INTO " . DB_PREFIX . "order_product SET ".
			"order_id 	= '" . (int)$data['order_id'] . "', ".
			"product_id = '" . (int)$data['product_id'] . "', ".
			"name 		= '" . $this->db->escape($product_query->row['title']) . "', ".
			"model 		= '" . $this->db->escape($product_query->row['model']) . "', ".
			"price 		= '" . $this->db->escape(preg_replace("/[^0-9.]/",'', $data['price'])) . "', ".
	        "total 		= '" . $this->db->escape(preg_replace("/[^0-9.]/",'', $data['total'])) . "', ".
	        "quantity 	= '" . $this->db->escape($data['quantity']) . "'");
	}
	
	public function addHistory($order_id, $data) {
		$this->db->query("UPDATE `" . DB_PREFIX . "order` SET 
        order_status_id = '" . (int)$data['order_status_id'] . "', 
        date_modified = NOW() 
        WHERE order_id = '" . (int)$order_id . "'");

		if ($data['append']) {
      		$this->db->query("INSERT INTO " . DB_PREFIX . "order_history SET 
              order_id = '" . (int)$order_id . "', 
              order_status_id = '" . (int)$data['order_status_id'] . "', 
              notify = '" . (isset($data['notify']) ? (int)$data['notify'] : 0) . "', 
              comment = '" . $this->db->escape(strip_tags($data['comment'])) . "', 
              date_added = NOW()");
		}
	}

    public function getAllSum($data=[]) {
        
        $cache_prefix = "admin.orders.sum";
        $cachedId = $cache_prefix.
            (int)STORE_ID ."_".
            serialize($data).
            $this->config->get('config_language_id') . "." .
            $this->request->getQuery('hl') . "." .
            $this->request->getQuery('cc') . "." .
            $this->config->get('config_currency') . "." .
            (int)$this->config->get('config_store_id');
            
        
        $cached = $this->cache->get($cachedId, $cache_prefix);

        if (!$cached || (bool)$this->user->getId()) {
            $sql = "SELECT SUM(t.total) AS total FROM " . DB_PREFIX . "order t ";
            $sql .= $this->buildSQLQuery($data, null, true);
            $query = $this->db->query($sql);
            
            $this->cache->set($cachedId, $query->row['total']);
            return $query->row['total'];
        } else {
            
            return $cached;
        }
    }

	public function generateInvoiceId($order_id) {
		$query = $this->db->query("SELECT MAX(invoice_id) AS invoice_id FROM `" . DB_PREFIX . "order`");
		
		if ($query->row['invoice_id']) {
			$invoice_id = (int)$query->row['invoice_id'] + 1;
		} elseif ($this->config->get('config_invoice_id')) {
			$invoice_id = $this->config->get('config_invoice_id');
		} else {
			$invoice_id = 1;
		}
		
		$this->db->query("UPDATE `" . DB_PREFIX . "order` SET ".
				"invoice_id = '" . (int)$invoice_id . "', ".
				"invoice_prefix = '" . $this->db->escape($this->config->get('config_invoice_prefix')) . "', ".
				"date_modified = NOW() ".
			"WHERE order_id = '" . (int)$order_id . "'");
		
		return $this->config->get('config_invoice_prefix') . $invoice_id;
	}
	
	public function getProducts($order_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_product WHERE order_id = '" . (int)$order_id . "'");
	
		return $query->rows;
	}

	public function getOptions($order_id, $order_product_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_option WHERE order_id = '" . (int)$order_id . "' AND order_product_id = '" . (int)$order_product_id . "'");
	
		return $query->rows;
	}
	
	public function getTotals($order_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_total WHERE order_id = '" . (int)$order_id . "' ORDER BY sort_order");
	
		return $query->rows;
	}	

	public function getHistory($order_id) { 
		$query = $this->db->query("SELECT ".
				"oh.date_added, ".
				"os.ref AS status, ".
				"oh.comment, ".
				"oh.notify ".
			"FROM " . DB_PREFIX . "order_history oh ".
				"LEFT JOIN " . DB_PREFIX . "status os ON (oh.order_status_id = os.status_id AND os.object_type = 'order_status') ".
				"LEFT JOIN " . DB_PREFIX . "description dos ON (os.status_id = dos.object_id AND dos.object_type = 'order_status') ".
			"WHERE oh.order_id = '" . (int)$order_id . "' ".
				"AND dos.language_id = '" . (int)$this->config->get('config_language_id') . "' ".
			"ORDER BY oh.date_added");
        
		return $query->rows;
	}	

	public function getDownloads($order_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_download WHERE order_id = '" . (int)$order_id . "' ORDER BY name");

		return $query->rows; 
	}	
	
	public function getHistoryTotalByOrderStatusId($order_status_id) {
	  	$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "order_history oh ".
			"LEFT JOIN `" . DB_PREFIX . "order` o ON (oh.order_id = o.order_id) ".
		"WHERE oh.order_status_id = '" . (int)$order_status_id . "' ".
			"AND o.order_status_id > '0' ".
		"GROUP BY order_id");

		return $query->row['total'];
	}

	public function getAllTotalByLanguageId($language_id) {
		return $this->getAllTotal(array(
			'language_id' => (int)$language_id
		));
	}	
	
	public function getAllTotalByCurrencyId($currency_id) {
		return $this->getAllTotal(array(
			'currency_id' => (int)$currency_id
		));
	}
}
