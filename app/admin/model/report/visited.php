<?php
/**
 * ModelReportVisited
 * 
 * @package NecoTienda 
 * @author Yosiet Serga
 * @copyright Inversiones Necoyoad, C.A.
 * @version 1.0.0
 * @access public
 * @see Model
 */
class ModelReportVisited extends Model {
	/**
	 * ModelReportVisited::getCustomerVisitedReport()
	 * 
	 * @param array $data
	 * @param integer $start
	 * @param integer $limit
     * @see DB
	 * @return array sql records
	 */
	public function getCustomerVisitedReport($data = array(),$start = 0, $limit = 20) {
		$total = 0;
		$category_data = [];
        $s = "SELECT SUM(visited) AS total FROM " . DB_PREFIX . "customer_stats ";
        if (isset($data['filter_sdate']) && !is_null($data['filter_sdate'])) {
            $s .= "WHERE date_added BETWEEN DATE('".$data['filter_sdate']."') AND DATE('".$data['filter_fdate']."')";  
		}
        
		$query = $this->db->query($s);
		$total = $query->row['total'];

		$sql = "SELECT *, CONCAT(firstname, ' ', lastname) AS name FROM " . DB_PREFIX . "customer c";
		
        if (isset($data['filter_name']) && !is_null($data['filter_name'])) {
            $sql .= " AND CONCAT(firstname, ' ', lastname) LIKE '%".$this->db->escape(ucwords($data['filter_name']))."%'";
		}
        if (isset($data['filter_email']) && !is_null($data['filter_email'])) {
            $sql .= " AND email LIKE '%".$this->db->escape($data['filter_email'])."%'"; 
		}
        
        $sort_data = array(
				'name',
                'email'
			);	
			
			if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
				$sql .= " ORDER BY " . $data['sort'];	
			} else {
				$sql .= " ORDER BY visits";	
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
			if ($result['visits']) {
				$percent = @round(($result['visits'] / $total) * 100, 2) . '%';
			} else {
				$percent = '0%';
			}
            $s = "SELECT SUM(visited) AS total FROM " . DB_PREFIX . "customer_stats ";
            if (isset($data['filter_sdate']) && !is_null($data['filter_sdate'])) {
                $s .= "WHERE customer_id = '".$result['customer_id']."' AND date_added BETWEEN DATE('".$data['filter_sdate']."') AND DATE('".$data['filter_fdate']."')";  
    		} else {
    		  $s .= "WHERE customer_id = '".$result['customer_id']."'";
    		}
            $query = $this->db->query($s);
            if ($query->row['total']) {
                $visited = $query->row['total'];
            } else {
                $visited = 0;
            }
            $customer_data[] = array(
                'customer_id' => $result['customer_id'],
				'name'    => $result['name'],
				'email'    => $result['email'],
				'visited'  => $visited,
				'tvisited'  => $result['visits'],
				'percent' => $percent                
			     );
            $sql = "SELECT *,cs.date_added AS added,cs.ip AS pip, CONCAT(c.firstname,' ',c.lastname) AS name FROM " . DB_PREFIX . "customer_stats cs
            INNER JOIN " . DB_PREFIX . "customer c ON (cs.customer_id = c.customer_id)";
            if (isset($data['filter_sdate']) && !is_null($data['filter_sdate'])) {
                $sql .= " AND cs.customer_id = '".$result['customer_id']."' AND cs.date_added BETWEEN DATE('".$data['filter_sdate']."') AND DATE('".$data['filter_fdate']."')";  
    		} else {
    		  $sql .= " AND cs.customer_id = '".$result['customer_id']."'";
    		}
            $sql .= " GROUP BY email";
            $sort_data = array(
				'name',
				'email',
				'ip'
			);	
			
			if (isset($data['dsort']) && in_array($data['dsort'], $sort_data)) {
				$sql .= " ORDER BY " . $data['dsort'];	
			} else {
				$sql .= " ORDER BY email";	
			}
			
			if (isset($data['dorder']) && ($data['dorder'] == 'DESC')) {
				$sql .= " ASC";
			} else {
				$sql .= " DESC";
			}
            
            $q = $this->db->query($sql);
            foreach($q->rows as $detail) {                
                $customer_data['detail'][] = array(
                    'customer_stats_id' => $detail['customer_stats_id'],
                    'customer_id' => $detail['customer_id'],
                    'name' => $detail['name'],
                    'email' => $detail['email'],
                    'ip' => $detail['pip'],
                    'visited' => $detail['visited'],
                    'added' => date('d-m-Y h:i:s',strtotime($detail['added'])));
            }
		}
		return $customer_data;
	}	
	
	/**
	 * ModelReportVisited::reset()
	 * 
	 * @param integer $start
	 * @param integer $limit
     * @see DB
	 * @return void
	 */
	public function reset($start = 0, $limit = 20) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "customer_stats");
	}
}
