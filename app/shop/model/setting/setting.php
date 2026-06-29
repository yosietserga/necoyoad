<?php

/**
 * ModelSettingSetting
 * 
 * @package   
 * @author NecoTienda
 * @copyright Inversiones Necoyoad, C.A.
 * @version 2010
 * @access public
 */
class ModelSettingSetting extends Model {

    public function getSetting($group, $store_id = 0) {
        $data = [];
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "setting ".
            "WHERE ".

            (
                !empty($group) ? 
                "`group` = '" . $this->db->escape($group) . "' " : 
                ""
            ).

            (
                !empty($group) ? 
                "AND store_id = '" . (int) $store_id . "' " : 
                " store_id = '" . (int) $store_id . "' "
            )
        );

        foreach ($query->rows as $result) {
            $data[$result['key']] = $result['value'];
        }
        return $data;
    }

    public function getProperty($group, $key, $store_id = 0) {
        $data = [];
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "setting ".
            "WHERE `group` = '" . $this->db->escape($group) . "' ".
            "AND `key` = '" . $this->db->escape($key) . "' ".
            "AND store_id = '" . (int) $store_id . "'");
        return $query->row['value'];
    }

    public function update($group, $data, $store_id = 0) {
        $this->db->query("DELETE FROM " . DB_PREFIX . "setting ".
            "WHERE `group` = '" . $this->db->escape($group) . "' ".
            "AND store_id = '" . (int) $store_id . "'");

        foreach ($data as $key => $value) {
            if ($key == 'config_bounce_password' && !empty($value))
                $value = base64_encode($value);
            if ($key == 'config_smtp_password' && !empty($value))
                $value = base64_encode($value);

            $this->db->query("INSERT INTO " . DB_PREFIX . "setting SET ".
            "`group` = '" . $this->db->escape($group) . "', ".
            "`key` = '" . $this->db->escape($key) . "', ".
            "`value` = '" . $this->db->escape($value) . "',".
            "`store_id` = '" . (int) $store_id . "'");
        }
    }

    public function updateProperty($group, $key, $data, $store_id = 0) {
        $this->db->query("DELETE FROM " . DB_PREFIX . "setting ".
            "WHERE `group` = '" . $this->db->escape($group) . "' ".
            "AND `key`   = '" . $this->db->escape($key) . "' ".
            "AND store_id = '" . (int) $store_id . "'");
        
        $this->db->query("REPLACE INTO " . DB_PREFIX . "setting SET ".
            "`group` = '" . $this->db->escape($group) . "',".
            "`key`   = '" . $this->db->escape($key) . "',".
            "`value` = '" . $this->db->escape($data) . "',".
            "`store_id` = '" . (int) $store_id . "'");
    }

    public function editMaintenance($data, $group = 'config', $store_id = 0) {
        $this->db->query("UPDATE " . DB_PREFIX . "setting SET ".
            "`value` = '" . $this->db->escape($data) . "' ".
            "WHERE `key` = 'config_maintenance' ".
            "AND store_id = '" . (int) $store_id . "'");
    }

    public function delete($group, $store_id = 0) {
        $this->db->query("DELETE FROM " . DB_PREFIX . "setting ".
            "WHERE `group` = '" . $this->db->escape($group) . "' ".
            "AND store_id = '" . (int) $store_id . "'");
    }

    public function deleteProperty($group, $key, $store_id = 0) {
        $this->db->query("DELETE FROM " . DB_PREFIX . "setting ".
            "WHERE `group` = '" . $this->db->escape($group) . "' ".
            "AND `key` = '" . $this->db->escape($key) . "' ".
            "AND store_id = '" . (int) $store_id . "'");
    }
}
