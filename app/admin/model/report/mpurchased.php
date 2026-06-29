<?php
class ModelReportMPurchased extends Model {
	public function getManufacturerPurchasedReport($start = 0, $limit = 20) {
		if ($start < 0) {
			$start = 0;
		}
		
		if ($limit < 1) {
			$limit = 20;
		}
		
		$query = $this->db->query("SELECT m.name AS mname, SUM(op.quantity) AS quantity, SUM(op.total + op.tax) AS total FROM `" . DB_PREFIX . "order` o LEFT JOIN " . DB_PREFIX . "order_product op ON (op.order_id = o.order_id) LEFT JOIN " . DB_PREFIX . "product p ON (op.product_id = p.product_id)  LEFT JOIN " . DB_PREFIX . "manufacturer m ON (m.manufacturer_id = p.manufacturer_id) WHERE o.order_status_id > '0' GROUP BY m.name ORDER BY total DESC LIMIT " . (int)$start . "," . (int)$limit);
	
		return $query->rows;
	}
	
	public function getTotalOrderedProducts() {
      	$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order_product` GROUP BY model");
		
		return $query->num_rows;
	}
}
