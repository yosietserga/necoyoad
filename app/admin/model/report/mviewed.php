<?php
/**
 * ModelReportMViewed
 * 
 * @package NecoTienda 
 * @author Yosiet Serga
 * @copyright Inversiones Necoyoad, C.A.
 * @version 1.0.0
 * @access public
 * @see Model
 */
class ModelReportMViewed extends Model {
	/**
	 * ModelReportViewed::getManufacturerViewedReport()
	 * 
	 * @param array $data
	 * @param integer $start
	 * @param integer $limit
     * @see DB
	 * @return array sql records
	 */
	public function getManufacturerViewedReport($data = array(),$start = 0, $limit = 20) {
		$total = 0;
		$manufacturer_data = [];
        $s = "SELECT SUM(viewed) AS total FROM " . DB_PREFIX . "manufacturer_stats ";
        if (isset($data['filter_sdate']) && !is_null($data['filter_sdate'])) {
            $s .= "WHERE date_added BETWEEN DATE('".$data['filter_sdate']."') AND DATE('".$data['filter_fdate']."')";  
		}
        
		$query = $this->db->query($s);
		$total = $query->row['total'];

		$sql = "SELECT * FROM " . DB_PREFIX . "manufacturer ";
		
        if (isset($data['filter_name']) && !is_null($data['filter_name'])) {
            $sql .= " WHERE name LIKE '%".$this->db->escape($data['filter_name'])."%'"; 
            $sql .= " OR name LIKE '%".$this->db->escape(ucwords($data['filter_name']))."%'"; 
            $sql .= " OR name LIKE '%".$this->db->escape(ucfirst($data['filter_name']))."%'";  
            $sql .= " OR name LIKE '%".$this->db->escape(strtolower($data['filter_name']))."%'"; 
            $sql .= " OR name LIKE '%".$this->db->escape(strtoupper($data['filter_name']))."%'";  
		}
        
        $sort_data = array(
				'name'
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
            $s = "SELECT SUM(viewed) AS total FROM " . DB_PREFIX . "manufacturer_stats ";
            if (isset($data['filter_sdate']) && !is_null($data['filter_sdate'])) {
                $s .= "WHERE manufacturer_id = '".$result['manufacturer_id']."' AND date_added BETWEEN DATE('".$data['filter_sdate']."') AND DATE('".$data['filter_fdate']."')";  
    		} else {
    		  $s .= "WHERE manufacturer_id = '".$result['manufacturer_id']."'";
    		}
            $query = $this->db->query($s);
            if ($query->row['total']) {
                $viewed = $query->row['total'];
            } else {
                $viewed = 0;
            }
            $manufacturer_data[] = array(
                'manufacturer_id' => $result['manufacturer_id'],
				'name'    => $result['name'],
				'viewed'  => $viewed,
				'tviewed'  => $result['viewed'],
				'percent' => $percent                
			     );
		}
		return $manufacturer_data;
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
		$this->db->query("UPDATE " . DB_PREFIX . "manufacturer SET viewed = '0'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "manufacturer_stats");
	}
}
