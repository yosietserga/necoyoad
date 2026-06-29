<?php

/**
 * ModelObject
 * 
 * @package NecoTienda
 * @author Yosiet Serga
 * @copyright Inversiones Necoyoad, C.A.
 * @version 1.0.0
 * @access public
 * @see Model
 */
class ModelObject extends Model {

    public string $object_type;
    public $parent_type;
    public $default;

    public function __construct($registry) {
        parent::__construct($registry);

        $this->cacheConstant =
            (int)STORE_ID ."_".
            $this->config->get('config_language_id') . "." .
            $this->request->getQuery('hl') . "." .
            $this->request->getQuery('cc') . "." .
            $this->customer->getId() . "." .
            $this->config->get('config_currency') . "." .
            (int)$this->config->get('config_store_id');
    }

    public function getStat() {}
    public function getStatus() {}
    public function getGroup() {}

    public function setDescription() {}
    public function setStat() {}
    public function setStatus() {}
    public function setGroup() {}

    public function deleteDescription() {}
    public function deleteStat() {}
    public function deleteStatus() {}
    public function deleteGroup() {}

    public function getDescription($data) {
        $cached = $this->getCache('descriptions', $data);

        if (!$cached || (bool)$this->user->getId()) {
            $description_data = [];

            $data['object_id'] = !is_array($data['object_id']) && !empty($data['object_id']) ? array($data['object_id']) : $data['object_id'];
            $data['language_id'] = !is_array($data['language_id']) && !empty($data['language_id']) ? array($data['language_id']) : $data['language_id'];

            $sql = "SELECT * FROM " . DB_PREFIX . "description ".
                "WHERE object_id IN (" . implode(', ', $data['object_id']) . ") ".
                "AND object_type = '" . $this->db->escape($this->object_type) . "' ";

            if (!empty($data['language_id'])) {
                $sql .= "AND language_id IN (" . implode(', ', $data['language_id']) . ") ";
            }

            $query = $this->db->query($sql);

            foreach ($query->rows as $result) {
                $description_data[$result['language_id']]['title'] = $result['title'];
                $description_data[$result['language_id']]['description'] = $result['description'];
                $description_data[$result['language_id']]['seo_title'] = $result['seo_title'];
                $description_data[$result['language_id']]['meta_keywords'] = $result['meta_keywords'];
                $description_data[$result['language_id']]['meta_description'] = $result['meta_description'];
            }

            $keywords = $this->db->query("SELECT * FROM " . DB_PREFIX . "url_alias ".
                "WHERE object_id IN (" . implode(', ', $data['object_id']) . ") ".
                "AND object_type = '" . $this->db->escape($this->object_type) . "'");

            foreach ($keywords->rows as $result) {
                $description_data[$result['language_id']]['keyword'] = $result['keyword'];
            }

            $this->setCache('descriptions', $data, $description_data);
            return $description_data;
        } else {
            return $cached;
        }
    }

    public function sortOrder($data) {
        if (!is_array($data) || !$this->validate())
            return false;

        $this->clearCache("rows");
        
        $pos = 1;
        foreach ($data as $id) {
            $this->db->query("UPDATE " . DB_PREFIX . "object SET ".
                "sort_order = '" . (int) $pos . "' ".
                "WHERE object_id = '" . (int) $id . "' ".
                "AND object_type = '" . $this->db->escape($this->object_type) . "'");
            $pos++;
        }
        return true;
    }

    public function recycle($id) {
        $this->updateStatus($id, -1);
    }

    public function activate($id) {
        $this->updateStatus($id, 1);
    }

    public function deactivate($id) {
        $this->updateStatus($id, 0);
    }

    public function getProperty($id, $group = '*', $key = '*') {
        if (!$this->validate())
            return false;

        $cached = $this->getCache("properties", array($id, $group, $key));

        if (!$cached || (bool)$this->user->getId()) {
            $sql = "SELECT * FROM " . DB_PREFIX . "property ".
                "WHERE `object_id` = '" . (int)$id . "' ".
                "AND `object_type` = '" . $this->db->escape($this->object_type) . "' ";

            if ($group !== '*')
                $sql .= "AND `group` = '" . $this->db->escape($group) . "' ";

            if ($group !== '*' && $key !== '*')
                $sql .= "AND `key` = '" . $this->db->escape($key) . "' ";

            $query = $this->db->query($sql);

            $r = unserialize(str_replace("\'", "'", $query->row['value']));
            $this->setCache('properties', array($id, $group, $key), $r);
            return $r;
        } else {
            return $cached;
        }
    }

    public function setProperty($id, $group, $key, $value) {
        if (!$this->validate())
            return false;

        $this->deleteProperty($id, $group, $key);
        $this->db->query("INSERT INTO " . DB_PREFIX . "property SET ".
            "`object_id` = '" . (int) $id . "', ".
            "`group`     = '" . $this->db->escape($group) . "', ".
            "`key`       = '" . $this->db->escape($key) . "', ".
            "`value`     = '" . $this->db->escape(str_replace("'", "\'", serialize($value))) . "'");
    }

    public function deleteProperty($id, $group = '*', $key = '*') {
        if (!$this->validate())
            return false;

        $this->clearCache("properties");

        $sql = "DELETE FROM " . DB_PREFIX . "property ".
            "WHERE `object_id` = '" . (int)$id . "' ".
            "AND `object_type` = '" . $this->db->escape($this->object_type) . "' ";

        if ($group !== '*')
            $sql .= "AND `group` = '" . $this->db->escape($group) . "' ";

        if ($group !== '*' && $key !== '*')
            $sql .= "AND `key` = '" . $this->db->escape($key) . "' ";

        $this->db->query($sql);
    }

    private function setCache($key, $data, $value) {
        $cachedId =
            $this->object_type .".". $key.
            serialize($data).
            $this->cacheConstant;

        $this->cache->set($cachedId, $value, $this->object_type .".". $key);
    }

    private function getCache($key, $data) {
        $cachedId =
            $this->object_type .".". $key.
            serialize($data).
            $this->cacheConstant;

        $this->cache->get($cachedId, $this->object_type .".". $key);
    }

    private function clearCache($k) {
        $this->cache->delete($this->object_type.".".$k);
    }

    private function updateStatus($id, $status) {
        if (!$this->validate())
            return false;

        $this->clearCache("rows");

        $this->db->query("UPDATE `" . DB_PREFIX . "object` SET ".
            "`status` = '" . (int)$status . "' ".
            "WHERE `object_id` = '" . (int)$id . "' ".
            "AND `object_type` = '" . $this->db->escape($this->object_type) . "' ");
    }

    private function validate() {
        return !empty($this->object_type);
    }

}
