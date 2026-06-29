<?php
/**
 * ModelStyleTheme
 * 
 * @package NecoTienda
 * @author Yosiet Serga
 * @copyright Inversiones Necoyoad, C.A.
 * @version 1.0.0
 * @access public
 * @see Model
 */
class ModelStyleTheme extends Model {
	public function getStores($id) {
		$data = [];
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "theme_to_store WHERE theme_id = '" . (int)$id . "'");
		foreach ($query->rows as $result) {
            $data[] = $result['store_id'];
		}
		return $data;
	}	
	
	public function getStyles($id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "theme_style WHERE theme_id = '" . (int)$id . "'");
		return $query->rows;
	}
	
	/**
	 * ModelStyleTheme::getTheme()
	 * 
	 * @param int $theme_id
     * @see DB
     * @see Cache
	 * @return array sql record 
	 */
	public function getTheme($theme_id) {
		$query = $this->db->query("SELECT * FROM ". DB_PREFIX ."theme t 
        WHERE t.theme_id = '" . (int)$theme_id . "'");
		return $query->row;
	}

	/**
	 * ModelStyleTheme::getAll()
	 *
	 * @see DB
	 * @see Cache
	 * @return array sql records
	 */
	public function getAll(array $data = [], array $options = []) {
		$data_cached = $this->cache->get('theme.all.active.for.store.'. STORE_ID);
		if ($data_cached) {
			return $data_cached;
		} else {
			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "theme
            WHERE store_id = '". (int)STORE_ID ."'
            AND status = 1
            AND date_publish_start <= NOW()
            AND (date_publish_end >= NOW() OR date_publish_end = '0000-00-00 00:00:00')
            ORDER BY `default` DESC, sort_order ASC");
			$this->cache->set('theme.all.active.for.store.'. STORE_ID, $query->rows);
			return $query->rows;
		}
	}

	/**
	 * ModelStyleTheme::getById()
	 *
	 * @see DB
	 * @see Cache
	 * @return array sql records
	 */
	public function getById($id) {
		$data_cached = $this->cache->get("theme.$id.active.for.store.". STORE_ID);
		if ($data_cached) {
			return $data_cached;
		} else {
			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "theme
            WHERE theme_id = ". (int)$id ."
            AND status = 1
            AND date_publish_start <= NOW()
            AND (date_publish_end >= NOW() OR date_publish_end = '0000-00-00 00:00:00')");
			$this->cache->set("theme.$id.active.for.store.". STORE_ID, $query->row);
			return $query->row;
		}
	}
}