<?php
class ModelReportCPurchased extends Model {
	public function getCategoryPurchasedReport($start = 0, $limit = 20) {
		if ($start < 0) {
			$start = 0;
		}
		
		if ($limit < 1) {
			$limit = 20;
		}
		
		$query = $this->db->query("SELECT cd.name AS cname, SUM(op.quantity) AS quantity, SUM(op.total + op.tax) AS total FROM `" . DB_PREFIX . "order` o LEFT JOIN " . DB_PREFIX . "order_product op ON (op.order_id = o.order_id) LEFT JOIN " . DB_PREFIX . "object_to_category p2c ON (op.product_id = p2c.object_id AND p2c.object_type = 'product')  LEFT JOIN " . DB_PREFIX . "category c ON (c.category_id = p2c.category_id)  LEFT JOIN " . DB_PREFIX . "category_description cd ON (c.category_id = cd.category_id) WHERE o.order_status_id > '0' GROUP BY cd.name ORDER BY total DESC LIMIT " . (int)$start . "," . (int)$limit);
	
		return $query->rows;
	}
	
	public function getTotalOrderedProducts() {
      	$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order_product` GROUP BY model");
		
		return $query->num_rows;
	}
}
