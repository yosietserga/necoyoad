<?php
/**
 * ModelReportCViewed
 * 
 * @package NecoTienda 
 * @author Yosiet Serga
 * @copyright Inversiones Necoyoad, C.A.
 * @version 1.0.0
 * @access public
 * @see Model
 */
class ModelReportMViewedByCustomer extends Model {
	/**
	 * ModelReportMViewedByCustomer::getCategoryViewedReport()
	 * 
	 * @param array $data
	 * @param integer $start
	 * @param integer $limit
     * @see DB
	 * @return array sql records
	 */
	public function getCategoryViewedByCustomerReport($manufacturer_id,$data = array(),$start = 0, $limit = 20) {
		$total = 0;
		$manufacturer_data = [];
        $s = "SELECT SUM(viewed) AS total FROM " . DB_PREFIX . "manufacturer_stats 
                WHERE manufacturer_id = ".(int)$manufacturer_id;
        if (isset($data['filter_sdate']) && !is_null($data['filter_sdate'])) {
            $s .= " AND date_added BETWEEN DATE('".$data['filter_sdate']."') AND DATE('".$data['filter_fdate']."')";  
		}
        
		$query = $this->db->query($s);
		$total = $query->row['total'];

		$sql = "SELECT *, CONCAT(cu.firstname,' ',cu.lastname) AS name, m.name AS manufacturer, SUM(c.viewed) AS views FROM " . DB_PREFIX . "manufacturer_stats c 
        LEFT JOIN " . DB_PREFIX . "customer cu ON (c.customer_id = cu.customer_id) 
        LEFT JOIN " . DB_PREFIX . "manufacturer m ON (m.manufacturer_id = c.manufacturer_id) 
        WHERE c.manufacturer_id = ".(int)$manufacturer_id." ";
		
        if (isset($data['filter_name']) && !is_null($data['filter_name'])) {
            $sql .= " AND CONCAT(cu.firstname,' ',cu.lastname) LIKE '%".ucwords($this->db->escape($data['filter_name']))."%'"; 
		}
        
        if (isset($data['filter_email']) && !is_null($data['filter_email'])) {
            $sql .= " AND cu.email LIKE '%".$this->db->escape($data['filter_email'])."%'"; 
		}
        
        $sql .= " GROUP BY c.customer_id";	
        
        $sort_data = array(
				'name',
				'email'
			);	
			
			if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
				$sql .= " ORDER BY " . $data['sort'];	
			} else {
				$sql .= " ORDER BY views";	
			}
			
			if (isset($data['order']) && ($data['order'] == 'DESC')) {
				$sql .= " DESC";
			} else {
				$sql .= " ASC";
			}
            
            if (isset($data['start']) || isset($data['limit'])) {
				if ($data['start'] < 0) {
					$data['start'] = 0;
				}				

				if ($data['limit'] < 1) {
					$data['limit'] = 20;
				}	
			
				$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
			}	
            
		$query = $this->db->query($sql); 

		foreach ($query->rows as $result) {
			if ($result['views']) {
				$percent = @round(($result['views'] / $total) * 100, 2) . '%';
			} else {
				$percent = '0%';
			}
            $manufacturer_data[] = array(
                'manufacturer_id' => $result['manufacturer_id'],
                'customer_id' => $result['customer_id'],
                'manufacturer' => $result['manufacturer'],
				'name'    => $result['name'],
				'email'    => $result['email'],
				'tviewed'  => $result['views'],
				'percent' => $percent                
			     );
            
		}
        
            $sql = "SELECT *,cs.date_added AS added,cs.ip AS pip, CONCAT(c.firstname,' ',c.lastname) AS name FROM " . DB_PREFIX . "manufacturer_stats cs 
            INNER JOIN " . DB_PREFIX . "customer c ON (cs.customer_id = c.customer_id) 
            WHERE cs.manufacturer_id = '".(int)$manufacturer_id."'";
            
            if (isset($data['filter_name']) && !is_null($data['filter_name'])) {
                $sql .= " AND CONCAT(c.firstname,' ',c.lastname) LIKE '%".ucwords($this->db->escape($data['filter_name']))."%'"; 
    		}
            
            if (isset($data['filter_email']) && !is_null($data['filter_email'])) {
                $sql .= " AND c.email LIKE '%".$this->db->escape($data['filter_email'])."%'"; 
    		}
            
            if (isset($data['filter_sdate']) && !is_null($data['filter_sdate'])) {
                $sql .= "  AND cs.date_added BETWEEN DATE('".$data['filter_sdate']."') AND DATE('".$data['filter_fdate']."')";  
    		} 
            
            $sort_data = array(
				'name',
				'email',
				'store_name',
				'ip'
			);	
			
			if (isset($data['dsort']) && in_array($data['dsort'], $sort_data)) {
				$sql .= " ORDER BY " . $data['dsort'];	
			} else {
				$sql .= " ORDER BY added";	
			}
			
			if (isset($data['dorder']) && ($data['dorder'] == 'DESC')) {
				$sql .= " ASC";
			} else {
				$sql .= " DESC";
			}            
            
            if (isset($data['start']) || isset($data['limit'])) {
				if ($data['start'] < 0) {
					$data['start'] = 0;
				}				

				if ($data['limit'] < 1) {
					$data['limit'] = 20;
				}	
			
				$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
			}	
            $q = $this->db->query($sql);
            
            foreach($q->rows as $detail) {                
                $manufacturer_data['detail'][] = array(
                    'manufacturer_stats_id' => $detail['manufacturer_stats_id'],
                    'manufacturer_id' => $detail['manufacturer_id'],
                    'customer_id' => $detail['customer_id'],
                    'name' => $detail['name'],
                    'email' => $detail['email'],
                    'store_name' => $detail['store_name'],
                    'store_url' => $detail['store_url'],
                    'ip' => $detail['pip'],
                    'viewed' => $detail['viewed'],
                    'added' => date('d-m-Y h:i:s',strtotime($detail['added'])));
            }
		return $manufacturer_data;
	}
    	
	/**
	 * ModelReportMViewedByCustomer::getTotalCategoriesByCustomerViewed()
	 * 
     * @see DB
	 * @return int Count sql records
	 */
	public function getTotalCategoriesByCustomerViewed($manufacturer_id) {
      	$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "manufacturer_stats cs 
          LEFT JOIN " . DB_PREFIX . "customer c ON (cs.customer_id = c.customer_id) 
          WHERE manufacturer_id = ".(int)$manufacturer_id." 
          GROUP BY email");
		
		return (int)count($query->rows);
	}	
}
