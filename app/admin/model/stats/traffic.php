<?php
class ModelStatsTraffic extends Model
{
    public function getAll(array $data = [], array $options = [])
    {
	   $cache_file = "admin.visits.all.". 
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
            $sql = "SELECT *, MONTH(date_added) AS month, COUNT(*) AS total FROM ". DB_PREFIX ."stat ";
            
            $criteria = [];
            
            if ($data['object']) {
                $criteria[] = " object_type = '". $this->db->escape($data['object']) ."' ";
            }
            
            if ($data['object_id']) {
                $criteria[] = " object_id = '". (int)$data['object_id'] ."' ";
            }
            
            
            if (!empty($data['filter_date_start']) && !empty($data['filter_date_end'])) {
                $criteria[] = " date_added BETWEEN '". $this->db->escape($data['filter_date_start']) ."' AND '". $this->db->escape($data['filter_date_end']) ."' ";
            } elseif (!empty($data['filter_date_start'])) {
                $criteria[] = " date_added BETWEEN '". $this->db->escape($data['filter_date_start']) ."' AND NOW() ";
            }
            if ($criteria) {
                $sql .= " WHERE " . implode("AND",$criteria);
            }
            
            $sql .= "GROUP BY MONTH(date_added) ORDER BY MONTH(date_added) ASC";
            
            
            $results = $this->db->query($sql);
            
            $this->cache->set($cache_file,$results->rows);
            $return_data = $results->rows;
        }
        return $return_data;
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
            $sql = "SELECT *, MONTH(date_added) AS month, COUNT(*) AS total FROM ". DB_PREFIX ."stat ";
            
            $criteria = [];
            
            if ($data['object']) {
                $criteria[] = " object_type = '". $this->db->escape($data['object']) ."' ";
            }
            
            if ($data['object_id']) {
                $criteria[] = " object_id = '". (int)$data['object_id'] ."' ";
            }
            
            if (!empty($data['filter_date_start']) && !empty($data['filter_date_end'])) {
                $criteria[] = " date_added BETWEEN '". $this->db->escape($data['filter_date_start']) ."' AND '". $this->db->escape($data['filter_date_end']) ."' ";
            } elseif (!empty($data['filter_date_start'])) {
                $criteria[] = " date_added BETWEEN '". $this->db->escape($data['filter_date_start']) ."' AND NOW() ";
            }
            
            if ($criteria) {
                $sql .= " WHERE " . implode("AND",$criteria);
            }
            
            $sql .= "GROUP BY MONTH(date_added) ORDER BY MONTH(date_added) ASC";
            
            $results = $this->db->query($sql);
            $this->cache->set($cache_file,$results->rows);
            $return_data = $results->rows;
        }
        return $return_data;
	}
    
	public function getAllForHS($data) {
	   $cache_file = "admin.visits.hs.all.". 
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
            $sql = "SELECT *,UNIX_TIMESTAMP(CONVERT_TZ(date_added,'+00:00','-4:30'))*1000 AS dateAdded, COUNT(*) AS total 
            FROM ". DB_PREFIX ."stat R";
            
            $criteria = [];
            
            if ($data['object']) {
                $criteria[] = " object_type = '". $this->db->escape($data['object']) ."' ";
            }
            
            if ((int)$data['object_id']) {
                $criteria[] = " object_id = '". (int)$data['object_id'] ."' ";
            }
            
            if (!empty($data['filter_date_start']) && !empty($data['filter_date_end'])) {
                $criteria[] = " date_added BETWEEN '". $this->db->escape($data['filter_date_start']) ."' AND '". $this->db->escape($data['filter_date_end']) ."' ";
            } elseif (!empty($data['filter_date_start'])) {
                $criteria[] = " date_added BETWEEN '". $this->db->escape($data['filter_date_start']) ."' AND NOW() ";
            }
            
            if ($criteria) {
                $sql .= " WHERE " . implode("AND",$criteria);
            }
            
            $sql .= "GROUP BY YEAR(date_added),MONTH(date_added),DAY(date_added),HOUR(date_added)
            ORDER BY date_added ASC";
            
            $results = $this->db->query($sql);
            
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
            
            $sql = "SELECT *,UNIX_TIMESTAMP(CONVERT_TZ(date_added,'+00:00','-4:30'))*1000 AS dateAdded, COUNT(*) AS total FROM ". DB_PREFIX ."stat ";
            
            $criteria = [];
            
            if ($data['object']) {
                $criteria[] = " object_type = '". $this->db->escape($data['object']) ."' ";
            }
            
            if ($data['object_id']) {
                $criteria[] = " object_id = '". (int)$data['object_id'] ."' ";
            }
            
            if (!empty($data['filter_date_start']) && !empty($data['filter_date_end'])) {
                $criteria[] = " date_added BETWEEN '". $this->db->escape($data['filter_date_start']) ."' AND '". $this->db->escape($data['filter_date_end']) ."' ";
            } elseif (!empty($data['filter_date_start'])) {
                $criteria[] = " date_added BETWEEN '". $this->db->escape($data['filter_date_start']) ."' AND NOW() ";
            }
            
            if ($criteria) {
                $sql .= " WHERE " . implode("AND",$criteria);
            }
            
            $sql .= " GROUP BY YEAR(date_added),MONTH(date_added),DAY(date_added),HOUR(date_added) ORDER BY date_added ASC ";
            
            $results = $this->db->query($sql);
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
            
            $sql = "SELECT *,UNIX_TIMESTAMP(CONVERT_TZ(date_added,'+00:00','-4:30'))*1000 AS dateAdded, COUNT(*) AS total FROM ". DB_PREFIX ."stat ";
            
            $criteria = [];
            
            if ($data['object']) {
                $criteria[] = " object_type = '". $this->db->escape($data['object']) ."' ";
            }
            
            if ($data['object_id']) {
                $criteria[] = " object_id = '". (int)$data['object_id'] ."' ";
            }
            
            if (!empty($data['filter_date_start']) && !empty($data['filter_date_end'])) {
                $criteria[] = " date_added BETWEEN '". $this->db->escape($data['filter_date_start']) ."' AND '". $this->db->escape($data['filter_date_end']) ."' ";
            } elseif (!empty($data['filter_date_start'])) {
                $criteria[] = " date_added BETWEEN '". $this->db->escape($data['filter_date_start']) ."' AND NOW() ";
            }
            
            if ($criteria) {
                $sql .= " WHERE " . implode("AND",$criteria);
            }
            
            $sql .= " GROUP BY YEAR(date_added),MONTH(date_added),DAY(date_added),HOUR(date_added) ORDER BY date_added ASC ";
            
            $results = $this->db->query($sql);
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
            
            if (!empty($data['filter_date_start']) && !empty($data['filter_date_end'])) {
                $criteria[] = " date_added BETWEEN '". $this->db->escape($data['filter_date_start']) ."' AND '". $this->db->escape($data['filter_date_end']) ."' ";
            } elseif (!empty($data['filter_date_start'])) {
                $criteria[] = " date_added BETWEEN '". $this->db->escape($data['filter_date_start']) ."' AND NOW() ";
            }
            /*
            if (!empty($data['filter_date_start']) && !strpos($data['filter_date_start'],'0000-00-00') && !strpos($data['filter_date_end'],'0000-00-00') && !empty($data['filter_date_end'])) {
                $criteria[] = " UNIX_TIMESTAMP(CONVERT_TZ(date_added,'+00:00','-4:30'))*1000 
                BETWEEN '". $this->db->escape($data['filter_date_start']) ."' 
                AND '". $this->db->escape($data['filter_date_end']) ."' ";
            } elseif (!empty($data['filter_date_start']) && !strpos($data['filter_date_start'],'0000-00-00')) {
                $criteria[] = " UNIX_TIMESTAMP(CONVERT_TZ(date_added,'+00:00','-4:30'))*1000 
                BETWEEN '". $this->db->escape($data['filter_date_start']) ."' 
                AND UNIX_TIMESTAMP(CONVERT_TZ(NOW(),'+00:00','-4:30'))*1000 ";
            }
            */
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
            
            $this->cache->set($cache_file,$return_data);
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
            
            if (!empty($data['filter_date_start']) && !empty($data['filter_date_end'])) {
                $criteria[] = " date_added BETWEEN '". $this->db->escape($data['filter_date_start']) ."' AND '". $this->db->escape($data['filter_date_end']) ."' ";
            } elseif (!empty($data['filter_date_start'])) {
                $criteria[] = " date_added BETWEEN '". $this->db->escape($data['filter_date_start']) ."' AND NOW() ";
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
            
            $this->cache->set($cache_file,$return_data);
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
            
            if (!empty($data['filter_date_start']) && !empty($data['filter_date_end'])) {
                $criteria[] = " date_added BETWEEN '". $this->db->escape($data['filter_date_start']) ."' AND '". $this->db->escape($data['filter_date_end']) ."' ";
            } elseif (!empty($data['filter_date_start'])) {
                $criteria[] = " date_added BETWEEN '". $this->db->escape($data['filter_date_start']) ."' AND NOW() ";
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
            
            $this->cache->set($cache_file,$return_data);
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
            
            if (!empty($data['filter_date_start']) && !empty($data['filter_date_end'])) {
                $criteria[] = " s.date_added BETWEEN '". $this->db->escape($data['filter_date_start']) ."' AND '". $this->db->escape($data['filter_date_end']) ."' ";
            } elseif (!empty($data['filter_date_start'])) {
                $criteria[] = " s.date_added BETWEEN '". $this->db->escape($data['filter_date_start']) ."' AND NOW() ";
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
            
            $this->cache->set($cache_file,$return_data);
        }
        return $return_data;
	}
}