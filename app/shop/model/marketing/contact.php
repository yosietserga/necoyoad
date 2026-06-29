<?php
class ModelMarketingContact extends Model {
	public function add($data) {
      	$this->db->query("INSERT INTO " . DB_PREFIX . "contact SET 
          name          = '" . $this->db->escape($data['name']) . "',
          email         = '" . $this->db->escape($data['email']) . "', 
          customer_id   = '" . (int)$this->customer->getId() . "',
          date_added    = NOW()");
          
       return $this->db->getLastId();
	}
	
	public function update($contact_id, $data) {
		$this->db->query("UPDATE " . DB_PREFIX . "contact SET 
          name = '" . $this->db->escape($data['name']) . "',
          email = '" . $this->db->escape($data['email']) . "', 
          customer_id = '" . (int)$this->customer->getId() . "',
          date_modified = NOW()
        WHERE contact_id = '" . (int)$contact_id . "'");
	}
	
	public function getByEmail($email) {
		$this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "contact WHERE email = '" . $this->db->escape($email) . "'");
	}
}