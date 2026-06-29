<?php
/**
 * ModelReportViewed
 * 
 * @package NecoTienda 
 * @author Yosiet Serga
 * @copyright Inversiones Necoyoad, C.A.
 * @version 1.0.0
 * @access public
 * @see Model
 */
class ModelReportViewed extends Model {
	/**
	 * ModelReportViewed::getProductViewedReport()
	 * 
	 * @param array $data
	 * @param integer $start
	 * @param integer $limit
     * @see DB
	 * @return array sql records
	 */
	public function getProductViewedReport($data = array(),$start = 0, $limit = 20) {
		$total = 0;
		$product_data = [];
        $s = "SELECT SUM(viewed) AS total FROM " . DB_PREFIX . "product_stats ";
        if (isset($data['filter_sdate']) && !is_null($data['filter_sdate'])) {
            $s .= "WHERE date_added BETWEEN DATE('".$data['filter_sdate']."') AND DATE('".$data['filter_fdate']."')";  
		}
        
		$query = $this->db->query($s);
		$total = $query->row['total'];

		$sql = "SELECT * FROM " . DB_PREFIX . "product p 
        LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) 
        WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "'";
		
        if (isset($data['filter_name']) && !is_null($data['filter_name'])) {
            $sql .= " AND pd.name LIKE '%".$this->db->escape($data['filter_name'])."%'"; 
            $sql .= " OR pd.name LIKE '%".$this->db->escape(ucwords($data['filter_name']))."%'"; 
            $sql .= " OR pd.name LIKE '%".$this->db->escape(ucfirst($data['filter_name']))."%'";  
            $sql .= " OR pd.name LIKE '%".$this->db->escape(strtolower($data['filter_name']))."%'"; 
            $sql .= " OR pd.name LIKE '%".$this->db->escape(strtoupper($data['filter_name']))."%'";  
		}
        
        $sort_data = array(
				'name',
				'model'
			);	
			
			if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
				$sql .= " ORDER BY " . $data['sort'];	
			} else {
				$sql .= " ORDER BY viewed";	
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
			if ($result['viewed']) {
				$percent = @round(($result['viewed'] / $total) * 100, 2) . '%';
			} else {
				$percent = '0%';
			}
            $s = "SELECT SUM(viewed) AS total FROM " . DB_PREFIX . "product_stats ";
            if (isset($data['filter_sdate']) && !is_null($data['filter_sdate'])) {
                $s .= "WHERE product_id = '".$result['product_id']."' AND date_added BETWEEN DATE('".$data['filter_sdate']."') AND DATE('".$data['filter_fdate']."')";  
    		} else {
    		  $s .= "WHERE product_id = '".$result['product_id']."'";
    		}
            $query = $this->db->query($s);
            if ($query->row['total']) {
                $viewed = $query->row['total'];
            } else {
                $viewed = 0;
            }
            $product_data[] = array(
                'product_id' => $result['product_id'],
				'name'    => $result['name'],
				'model'   => $result['model'],
				'viewed'  => $viewed,
				'tviewed'  => $result['viewed'],
				'percent' => $percent                
			     );
		}
		return $product_data;
	}	
	
	/**
	 * ModelReportViewed::reset()
	 * 
	 * @param integer $start
	 * @param integer $limit
     * @see DB
	 * @return void
	 */
	public function reset($start = 0, $limit = 20) {
		$this->db->query("UPDATE " . DB_PREFIX . "product SET viewed = '0'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_stats");
	}
}
