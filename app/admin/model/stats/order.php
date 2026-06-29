<?php
class ModelStatsOrder extends Model {
	public function getAllForHS($data) {       
	   $cache_file = "admin.orders.stats.hs.all.". 
       (int)$data['object_id'] .".". 
       $data['object'] .".". 
       strtotime($data['filter_date_start']) .".". 
       strtotime($data['filter_date_end']) .".". 
       (int)$data['store_id'] .".". 
       date('d.m.Y');
       $return_data = $this->cache->get($cache_file);
       
       if ($return_data) {
            return $return_data;
       } else {
            $dt = new DateTime;
            $dt->sub(new DateInterval('P12M'));
            
            $sql = "SELECT *,UNIX_TIMESTAMP(CONVERT_TZ(o.date_added,'+00:00','-4:30')) AS dateAdded, COUNT(*) AS cant_total, SUM(o.total) AS total 
            FROM ". DB_PREFIX ."order o";
            
            $criteria = [];
            
            if (!empty($data['filter_date_start']) && !strpos($data['filter_date_start'],'0000-00-00') && !strpos($data['filter_date_end'],'0000-00-00') && !empty($data['filter_date_end'])) {
                $criteria[] = " UNIX_TIMESTAMP(CONVERT_TZ(o.date_added,'+00:00','-4:30'))*1000 
                BETWEEN '". $this->db->escape($data['filter_date_start']) ."' 
                AND '". $this->db->escape($data['filter_date_end']) ."' ";
            } elseif (!empty($data['filter_date_start']) && !strpos($data['filter_date_start'],'0000-00-00')) {
                $criteria[] = " UNIX_TIMESTAMP(CONVERT_TZ(o.date_added,'+00:00','-4:30'))*1000 
                BETWEEN '". $this->db->escape($data['filter_date_start']) ."' 
                AND UNIX_TIMESTAMP(CONVERT_TZ(NOW(),'+00:00','-4:30'))*1000 ";
            }
            
            if ($data['object_id'] && $data['object'] == 'product') {
                $criteria[] = "o.order_id IN (SELECT order_id FROM ". DB_PREFIX ."order_product op  WHERE op.product_id = '". (int)$data['object_id'] ."')";
            }
            
            if ($criteria) {
                $sql .= " WHERE " . implode(" AND ",$criteria);
            }
            
            $sql .= " GROUP BY YEAR(o.date_added),MONTH(o.date_added) ";
            $sql .= " ORDER BY o.date_added ASC ";
            
            $results = $this->db->query($sql);
            foreach ($results->rows as $row) {
                $return_data[] = array(
                    'date_added'=>$row['dateAdded'],
                    'cant_total'=>$row['cant_total'],
                    'total'=>$row['total']
                );
            }
            $this->cache->set($cache_file,$return_data);
        }
        return $return_data;
	}

    public function getAll(array $data = [], array $options = [])
    {
	   $cache_file = "admin.stats.orders.all.". 
       (int)$data['object_id'] .".". 
       $data['object'] .".". 
       strtotime($data['filter_date_start']) .".". 
       strtotime($data['filter_date_end']) .".". 
       (int)$data['store_id'] .".". 
       date('d.m.Y');
           
       $return_data = $this->cache->get($cache_file);
       
        if ($return_data) {
            return $return_data;
        } else {
            $results = $this->db->query("SELECT *, COUNT(*) AS total 
            FROM `". DB_PREFIX ."order` o
            WHERE YEAR(date_added) = '". date('Y') ."' 
            GROUP BY MONTH(date_added)
            ORDER BY MONTH(date_added) ASC");
            $this->cache->set($cache_file,$results->rows);
            $return_data = $results->rows;
        }
        return $return_data;
	}
    
	public function getSaleReport($data = array()) {
		$sql = "SELECT MIN(date_added) AS date_start, MAX(date_added) AS date_end, COUNT(*) AS orders, SUM(total) AS total 
        FROM `" . DB_PREFIX . "order`"; 

		if (isset($data['filter_order_status_id']) && $data['filter_order_status_id']) {
			$sql .= " WHERE order_status_id = '" . (int)$data['filter_order_status_id'] . "'";
		} else {
			$sql .= " WHERE order_status_id> '0'";
		}
		
		if (isset($data['filter_date_start'])) {
			$date_start = $data['filter_date_start'];
		} else {
			$date_start = date('Y-m-d', strtotime('-7 day'));
		}

		if (isset($data['filter_date_end'])) {
			$date_end = $data['filter_date_end'];
		} else {
			$date_end = date('Y-m-d', time());
		}
		
		$sql .= " AND (DATE(date_added)>= '" . $this->db->escape($date_start) . "' AND DATE(date_added) <= '" . $this->db->escape($date_end) . "')";
		
		if (isset($data['filter_group'])) {
			$group = $data['filter_group'];
		} else {
			$group = 'week';
		}
		
		switch($group) {
			case 'day';
				$sql .= " GROUP BY DAY(date_added)";
				break;
			default:
			case 'week':
				$sql .= " GROUP BY WEEK(date_added)";
				break;	
			case 'month':
				$sql .= " GROUP BY MONTH(date_added)";
				break;
			case 'year':
				$sql .= " GROUP BY YEAR(date_added)";
				break;									
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
		
		return $query->rows;
	}	
	
	public function getAllByProducts($data) {
	   $cache_file = "admin.visits.products.". 
       (int)$data['object_id'] .".". 
       $data['object'] .".". 
       strtotime($data['filter_date_start']) .".". 
       strtotime($data['filter_date_end']) .".". 
       (int)$data['store_id'] .".". 
       date('d.m.Y');
           
       $return_data = $this->cache->get($cache_file);
        if ($return_data) {
            return $return_data;
        } else {
            $results = $this->db->query("SELECT *, MONTH(date_added) AS month, COUNT(*) AS total 
            FROM ". DB_PREFIX ."stat 
            WHERE YEAR(date_added) = '". date('Y') ."' 
                AND object_type = 'product' 
            GROUP BY MONTH(date_added)
            ORDER BY MONTH(date_added) ASC");
            $this->cache->set($cache_file,$results->rows);
            $return_data = $results->rows;
        }
        return $return_data;
	}
    
	public function getAllProductsForHS($data) {
	   $cache_file = "admin.visits.hs.product.". 
       (int)$data['object_id'] .".". 
       $data['object'] .".". 
       strtotime($data['filter_date_start']) .".". 
       strtotime($data['filter_date_end']) .".". 
       (int)$data['store_id'] .".". 
       date('d.m.Y');
           
       $return_data = $this->cache->get($cache_file);
        if ($return_data) {
            return $return_data;
        } else {
            $results = $this->db->query("SELECT *,UNIX_TIMESTAMP(CONVERT_TZ(date_added,'+00:00','-4:30'))*1000 AS dateAdded, COUNT(*) AS total 
            FROM ". DB_PREFIX ."stat 
            WHERE object_type = 'product' 
            GROUP BY YEAR(date_added),MONTH(date_added),DAY(date_added),HOUR(date_added)
            ORDER BY date_added ASC");
            foreach ($results->rows as $row) {
                $return_data[] = array(
                    'date_added'=>$row['dateAdded'],
                    'total'=>$row['total']
                );
            }
            $this->cache->set($cache_file,$return_data);
        }
        return $return_data;
	}
    
	public function getAllCategoriesForHS($data) {
	   $cache_file = "admin.visits.hs.category.". 
       (int)$data['object_id'] .".". 
       $data['object'] .".". 
       strtotime($data['filter_date_start']) .".". 
       strtotime($data['filter_date_end']) .".". 
       (int)$data['store_id'] .".". 
       date('d.m.Y');
           
       $return_data = $this->cache->get($cache_file);
        if ($return_data) {
            return $return_data;
        } else {
            $results = $this->db->query("SELECT *,UNIX_TIMESTAMP(CONVERT_TZ(date_added,'+00:00','-4:30'))*1000 AS dateAdded, COUNT(*) AS total 
            FROM ". DB_PREFIX ."stat 
            WHERE object_type = 'category' 
            GROUP BY YEAR(date_added),MONTH(date_added),DAY(date_added),HOUR(date_added)
            ORDER BY date_added ASC");
            foreach ($results->rows as $row) {
                $return_data[] = array(
                    'date_added'=>$row['dateAdded'],
                    'total'=>$row['total']
                );
            }
            $this->cache->set($cache_file,$return_data);
        }
        return $return_data;
	}
    
	public function getAllByBrowser($data) {
	   $cache_file = "admin.visits.browser.". 
       (int)$data['object_id'] .".". 
       $data['object'] .".". 
       strtotime($data['filter_date_start']) .".". 
       strtotime($data['filter_date_end']) .".". 
       (int)$data['store_id'] .".". 
       date('d.m.Y');
       
       $return_data = $this->cache->get($cache_file);
       
        if ($return_data) {
            return $return_data;
        } else {
            $sql = "SELECT *, COUNT(*) AS total 
            FROM ". DB_PREFIX ."stat 
            WHERE object_type = '". $this->db->escape($data['object']) ."'";
            
            $criteria = [];
            
            if ($data['object_id']) {
                $criteria[] = " object_id = '". (int)$data['object_id'] ."' ";
            }
            
            if ($data['filter_date_start'] && $data['filter_date_end']) {
                $criteria[] = " UNIX_TIMESTAMP(CONVERT_TZ(date_added,'+00:00','-4:30'))*1000 
                BETWEEN '". $this->db->escape($data['filter_date_start']) ."' 
                AND '". $this->db->escape($data['filter_date_end']) ."' ";
            } elseif ($data['filter_date_start']) {
                $criteria[] = " UNIX_TIMESTAMP(CONVERT_TZ(date_added,'+00:00','-4:30'))*1000 
                BETWEEN '". $this->db->escape($data['filter_date_start']) ."' 
                AND UNIX_TIMESTAMP(CONVERT_TZ(NOW(),'+00:00','-4:30'))*1000 ";
            }
            
            if ($criteria) {
                $sql .= " AND " . implode("AND",$criteria);
            }
            
            $sql .= "GROUP BY browser";
            
            $results = $this->db->query($sql);
            
            foreach ($results->rows as $row) {
                $return_data[] = array(
                    'name'=>$row['browser'],
                    'total'=>$row['total']
                );
            }
            
            $this->cache->set($cache_file,$sql);
        }
        return $return_data;
	}
    
	public function getAllByOS($data) {
	   $cache_file = "admin.visits.os.". 
       (int)$data['object_id'] .".". 
       $data['object'] .".". 
       strtotime($data['filter_date_start']) .".". 
       strtotime($data['filter_date_end']) .".". 
       (int)$data['store_id'] .".". 
       date('d.m.Y');
       
       $return_data = $this->cache->get($cache_file);
       
        if ($return_data) {
            return $return_data;
        } else {
            $sql = "SELECT *, COUNT(*) AS total 
            FROM ". DB_PREFIX ."stat 
            WHERE object_type = '". $this->db->escape($data['object']) ."'";
            
            $criteria = [];
            
            if ($data['object_id']) {
                $criteria[] = " object_id = '". (int)$data['object_id'] ."' ";
            }
            
            if ($data['filter_date_start'] && $data['filter_date_end']) {
                $criteria[] = " UNIX_TIMESTAMP(CONVERT_TZ(date_added,'+00:00','-4:30'))*1000 
                BETWEEN '". $this->db->escape($data['filter_date_start']) ."' 
                AND '". $this->db->escape($data['filter_date_end']) ."' ";
            } elseif ($data['filter_date_start']) {
                $criteria[] = " UNIX_TIMESTAMP(CONVERT_TZ(date_added,'+00:00','-4:30'))*1000 
                BETWEEN '". $this->db->escape($data['filter_date_start']) ."' 
                AND UNIX_TIMESTAMP(CONVERT_TZ(NOW(),'+00:00','-4:30'))*1000 ";
            }
            
            if ($criteria) {
                $sql .= " AND " . implode("AND",$criteria);
            }
            
            $sql .= "GROUP BY os";
            
            $results = $this->db->query($sql);
            
            foreach ($results->rows as $row) {
                $return_data[] = array(
                    'name'=>$row['os'],
                    'total'=>$row['total']
                );
            }
            
            $this->cache->set($cache_file,$sql);
        }
        return $return_data;
	}
    
	public function getAllByIP($data) {
	   $cache_file = "admin.visits.ip.". 
       (int)$data['object_id'] .".". 
       $data['object'] .".". 
       strtotime($data['filter_date_start']) .".". 
       strtotime($data['filter_date_end']) .".". 
       (int)$data['store_id'] .".". 
       date('d.m.Y');
       
       $return_data = $this->cache->get($cache_file);
       
        if ($return_data) {
            return $return_data;
        } else {
            $sql = "SELECT *, COUNT(*) AS total 
            FROM ". DB_PREFIX ."stat 
            WHERE object_type = '". $this->db->escape($data['object']) ."'";
            
            $criteria = [];
            
            if ($data['object_id']) {
                $criteria[] = " object_id = '". (int)$data['object_id'] ."' ";
            }
            
            if ($data['filter_date_start'] && $data['filter_date_end']) {
                $criteria[] = " UNIX_TIMESTAMP(CONVERT_TZ(date_added,'+00:00','-4:30'))*1000 
                BETWEEN '". $this->db->escape($data['filter_date_start']) ."' 
                AND '". $this->db->escape($data['filter_date_end']) ."' ";
            } elseif ($data['filter_date_start']) {
                $criteria[] = " UNIX_TIMESTAMP(CONVERT_TZ(date_added,'+00:00','-4:30'))*1000 
                BETWEEN '". $this->db->escape($data['filter_date_start']) ."' 
                AND UNIX_TIMESTAMP(CONVERT_TZ(NOW(),'+00:00','-4:30'))*1000 ";
            }
            
            if ($criteria) {
                $sql .= " AND " . implode("AND",$criteria);
            }
            
            $sql .= " GROUP BY ip";
            $sql .= " ORDER BY total DESC";
            $sql .= " LIMIT 10";
            
            $results = $this->db->query($sql);
            
            foreach ($results->rows as $row) {
                $return_data[] = array(
                    'name'=>$row['ip'],
                    'total'=>$row['total']
                );
            }
            
            $this->cache->set($cache_file,$sql);
        }
        return $return_data;
	}
    
	public function getAllByCustomer($data) {
	   $cache_file = "admin.visits.customer.". 
       (int)$data['object_id'] .".". 
       $data['object'] .".". 
       strtotime($data['filter_date_start']) .".". 
       strtotime($data['filter_date_end']) .".". 
       (int)$data['store_id'] .".". 
       date('d.m.Y');
       
       $return_data = $this->cache->get($cache_file);
       
        if ($return_data) {
            return $return_data;
        } else {
            $sql = "SELECT *, COUNT(*) AS total 
            FROM ". DB_PREFIX ."stat s
            LEFT JOIN ". DB_PREFIX ."customer c ON (s.customer_id = c.customer_id)
            WHERE object_type = '". $this->db->escape($data['object']) ."'";
            
            $criteria = [];
            
            if ($data['object_id']) {
                $criteria[] = " object_id = '". (int)$data['object_id'] ."' ";
            }
            
            if ($data['filter_date_start'] && $data['filter_date_end']) {
                $criteria[] = " UNIX_TIMESTAMP(CONVERT_TZ(s.date_added,'+00:00','-4:30'))*1000 
                BETWEEN '". $this->db->escape($data['filter_date_start']) ."' 
                AND '". $this->db->escape($data['filter_date_end']) ."' ";
            } elseif ($data['filter_date_start']) {
                $criteria[] = " UNIX_TIMESTAMP(CONVERT_TZ(s.date_added,'+00:00','-4:30'))*1000 
                BETWEEN '". $this->db->escape($data['filter_date_start']) ."' 
                AND UNIX_TIMESTAMP(CONVERT_TZ(NOW(),'+00:00','-4:30'))*1000 ";
            }
            
            if ($criteria) {
                $sql .= " AND " . implode("AND",$criteria);
            }
            
            $sql .= " GROUP BY s.customer_id";
            $sql .= " ORDER BY total DESC";
            $sql .= " LIMIT 10";
            
            $results = $this->db->query($sql);
            
            foreach ($results->rows as $row) {
                $return_data[] = array(
                    'customer_id'=>(int)$row['customer_id'],
                    'name'=>($row['customer_id']) ? $row['firstname'] ." ". $row['lastname'] ." (". $row['email'] .")" : "An&oacute;nimo",
                    'total'=>$row['total']
                );
            }
            
            $this->cache->set($cache_file,$sql);
        }
        return $return_data;
	}
}