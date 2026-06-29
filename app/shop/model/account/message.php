<?php
class ModelAccountMessage extends Model {
    
    /**
     * @param $from string customer_id del remitente
     * */
    private $from = "";
    
    /**
     * @param $to array todas las direcciones destino
     * */
    private $to = [];
    
    /**
     * @param $message string
     * */
    private $message = "";
    
    /**
     * @param $subject string
     * */
    private $subject = "";
    
    /**
     * @param $parent_id integer
     * */
    private $parent_id = 0;
    
    public function setFrom($from) {
        $this->from = (int)$from;
    }
    
    public function setTo($to) {
        $result = $this->db->query("SELECT COUNT(*) AS total FROM ". DB_PREFIX ."customer WHERE customer_id = '". (int)$to ."'");
        if ($result->row['total']) {
            array_push($this->to,$to);
        }
        
    }
    
    public function setMessage($message) {
        $this->message = $message;
    }
    
    public function setSubject($subject) {
        $this->subject = $subject;
    }
    
    public function setParentId($parent_id) {
        $this->parent_id = $parent_id;
    }
    
	public function save() {
        $result = $this->db->query("INSERT INTO " . DB_PREFIX . "message SET 
        `customer_id` = '" . (int)$this->from . "',
        `parent_id` = '" . $this->db->escape($this->parent_id) . "',
        `subject` = '" . $this->db->escape($this->subject) . "',
        `message` = '" . $this->db->escape($this->message) . "',
        date_added = NOW()");
      	
		$id = $this->db->getLastId();
        
        foreach ($this->to as $to) {
            $this->db->query("INSERT INTO " . DB_PREFIX . "message_to_customer SET 
            `message_id` = '" . (int)$id . "',
            `customer_id` = '" . (int)$to . "',
            `status` = '0'");
        }
        
        return $id;	
	}
	
    public function addReply($parent_id) {
        $result = $this->db->query("INSERT INTO " . DB_PREFIX . "message SET 
        `customer_id` = '" . (int)$this->from . "',
        `parent_id` = '" . (int)$parent_id . "',
        `message` = '" . $this->db->escape($this->message) . "',
        `subject` = '" . $this->db->escape($data['email']) . "',
        date_added = NOW()");
      	
		$id = $this->db->getLastId();
        
        return $id;	
    }
    
	public function getMessage($id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "message WHERE message_id = '" . (int)$id . "'");
		return $query->row;
	}
			
	public function getInbounceMessagesByCustomerId($id,$data=array()) {
		$sql = "SELECT *, m.date_added AS created, m2c.status AS mstatus FROM " . DB_PREFIX . "message_to_customer m2c 
        LEFT JOIN " . DB_PREFIX . "message m ON (m.message_id=m2c.message_id)
        LEFT JOIN " . DB_PREFIX . "customer c ON (c.customer_id=m.customer_id)
        WHERE m2c.customer_id = '". (int)$id ."'";
        
        if ($data) {
            
            $implode = [];
               
            if ($data['letter']) {
                $implode[] = " LCASE(c.email) LIKE '" . $this->db->escape(strtolower($data['letter'])) . "%' 
                OR LCASE(c.company) LIKE '" . $this->db->escape(strtolower($data['letter'])) . "%'
                OR LCASE(c.firstname) LIKE '" . $this->db->escape(strtolower($data['letter'])) . "%'
                OR LCASE(c.lastname) LIKE '" . $this->db->escape(strtolower($data['letter'])) . "%'";
            }
            
            if ($data['keyword']) {
                $implode[] = " LCASE(m.subject) LIKE '%" . $this->db->escape(strtolower($data['keyword'])) . "%' ";
            }
            $implode[] = " m.`status` > 0 ";
            if ($implode) {
                $sql .= " AND " . implode(" AND ",$implode);
            }
            
            $sql .= "GROUP BY m.message_id";
    		$sql .= " ORDER BY m.date_added DESC";	
    			
    		if ($start < 0) {
    			$start = 0;
    		}
    		
    		$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
            
        }
        
		$query = $this->db->query($sql);
		return $query->rows;
	}
		
	public function getOutbounceMessagesByCustomerId($id,$data=array()) {
		$sql = "SELECT *, m.date_added AS created FROM " . DB_PREFIX . "message m
        LEFT JOIN " . DB_PREFIX . "customer c ON (c.customer_id=m.customer_id)
        WHERE m.customer_id = '". (int)$id ."'";
        
        if ($data) {
            
            $implode = [];
               
            if ($data['letter']) {
                $implode[] = " LCASE(c.email) LIKE '" . $this->db->escape(strtolower($data['letter'])) . "%' 
                OR LCASE(c.company) LIKE '" . $this->db->escape(strtolower($data['letter'])) . "%'
                OR LCASE(c.firstname) LIKE '" . $this->db->escape(strtolower($data['letter'])) . "%'
                OR LCASE(c.lastname) LIKE '" . $this->db->escape(strtolower($data['letter'])) . "%'";
            }
            
            if ($data['keyword']) {
                $implode[] = " LCASE(m.subject) LIKE '%" . $this->db->escape(strtolower($data['keyword'])) . "%' ";
            }
            $implode[] = " m.`status` > 0 ";
            if ($implode) {
                $sql .= " AND " . implode(" AND ",$implode);
            }
            
            $sql .= "GROUP BY m.message_id";
    		$sql .= " ORDER BY m.date_added DESC";	
    			
    		if ($start < 0) {
    			$start = 0;
    		}
    		
    		$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
            
        }
        
		$query = $this->db->query($sql);
		return $query->rows;
	}
			
	public function getInbounceMessagesById($id) {
		$sql = "SELECT *, m.date_added AS created, m2c.status AS mstatus, m2c.customer_id AS `to` FROM " . DB_PREFIX . "message m 
        LEFT JOIN " . DB_PREFIX . "message_to_customer m2c ON (m.message_id=m2c.message_id)
        LEFT JOIN " . DB_PREFIX . "customer c ON (c.customer_id=m.customer_id)
        WHERE m.`status` > 0 AND m.message_id = '". (int)$id ."'";
        
		$query = $this->db->query($sql);
        
        if ($query->row) {
            $this->db->query("UPDATE ". DB_PREFIX ."message_to_customer SET status = 1 WHERE customer_id = '". (int)$query->row['to'] ."' AND message_id = '". (int)$id ."'");
        }
        
		return $query->row;
	}
			
	public function getTotalInbounceMessagesByCustomerId($id,$data=array()) {
		$sql = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "message_to_customer m2c 
        LEFT JOIN " . DB_PREFIX . "message m ON (m.message_id=m2c.message_id)
        LEFT JOIN " . DB_PREFIX . "customer c ON (c.customer_id=m.customer_id)
        WHERE m2c.customer_id = '". (int)$id ."'";
        
        if ($data) {
            
            if ($data['letter']) {
                $implode[] = " LCASE(c.email) LIKE '" . $this->db->escape(strtolower($data['letter'])) . "%' 
                OR LCASE(c.company) LIKE '" . $this->db->escape(strtolower($data['letter'])) . "%'
                OR LCASE(c.firstname) LIKE '" . $this->db->escape(strtolower($data['letter'])) . "%'
                OR LCASE(c.lastname) LIKE '" . $this->db->escape(strtolower($data['letter'])) . "%'";
            }
            
            if ($data['keyword']) {
                $implode[] = " LCASE(m.subject) LIKE '%" . $this->db->escape(strtolower($data['keyword'])) . "%' ";
            }
            
            $implode[] = " m.`status` > 0 ";
            
            if ($implode) {
                $sql .= " AND " . implode(" AND ",$implode);
            }
            
        }
        
		$query = $this->db->query($sql);
		return $query->row['total'];
	}
			
	public function getTotalOutbounceMessagesByCustomerId($id,$data=array()) {
		$sql = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "message m
        LEFT JOIN " . DB_PREFIX . "customer c ON (c.customer_id=m.customer_id)
        WHERE m.customer_id = '". (int)$id ."'";
        
        if ($data) {
            
            if ($data['letter']) {
                $implode[] = " LCASE(c.email) LIKE '" . $this->db->escape(strtolower($data['letter'])) . "%' 
                OR LCASE(c.company) LIKE '" . $this->db->escape(strtolower($data['letter'])) . "%'
                OR LCASE(c.firstname) LIKE '" . $this->db->escape(strtolower($data['letter'])) . "%'
                OR LCASE(c.lastname) LIKE '" . $this->db->escape(strtolower($data['letter'])) . "%'";
            }
            
            if ($data['keyword']) {
                $implode[] = " LCASE(m.subject) LIKE '%" . $this->db->escape(strtolower($data['keyword'])) . "%' ";
            }
            
            $implode[] = " m.`status` > 0 ";
            
            if ($implode) {
                $sql .= " AND " . implode(" AND ",$implode);
            }
            
        }
        
		$query = $this->db->query($sql);
		return $query->row['total'];
	}
			
	public function getTotalMessagesByEmail($email) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "customer WHERE email = '" . $this->db->escape($email) . "'");
		return (int)$query->row['total'];
	}
    
	/**
	 * ModelStoreManufacturer::deleteManufacturer()
	 * 
	 * @param int $manufacturer_id
     * @see DB
     * @see Cache
	 * @return void 
	 */
	public function delete($id,$customer_id) {
		$this->db->query("UPDATE " . DB_PREFIX . "message SET `status` = -1 WHERE message_id = '" . (int)$id . "'");
		$this->db->query("UPDATE " . DB_PREFIX . "message_to_customer SET `status` = -1 WHERE message_id = '" . (int)$id . "' AND customer_id = '" . (int)$customer_id . "'");
	}	
	
}