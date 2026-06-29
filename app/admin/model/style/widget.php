<?php
/**
 * ModelStoreWidget
 * 
 * @package NecoTienda
 * @author Yosiet Serga
 * @copyright Inversiones Necoyoad, C.A.
 * @version 1.0.0
 * @access public
 * @see Model
 */
class ModelStyleWidget extends Model {

    protected string $table        = "widget";
    protected string $pkey         = "widget_id";
    protected string $object_type  = "widget";

    protected array $fields = [
        "code" => [
            "name"      => "code",
            "type"      => "string",
        ],
        "name" => [
            "name"      => "name",
            "type"      => "string",
        ],
        "extension" => [
            "name"      => "extension",
            "type"      => "string",
        ],
        "position" => [
            "name"      => "position",
            "type"      => "string",
        ],
        "app" => [
            "name"      => "app",
            "type"      => "string",
        ],
        "settings" => [
            "name"      => "settings",
            "type"      => "text",
        ],
        "status" => [
            "name"      => "status",
            "default"   => 1,
            "type"      => "boolean",
        ],
        "order" => [
            "name"      => "order",
            "default"   => 0,
            "type"      => "integer",
        ],
    ];

    protected array $relations = ["stores"];

	/**
	 * ModelStoreWidget::deleteWidget()
	 * 
	 * @param int $widget_id
     * @see DB
     * @see Cache
	 * @return void 
	 */
	public function delete($name) {
        $row = $this->getByName($name);
        if (isset($row['widget_id'])) {
            parent::delete($row['widget_id']);

            $this->db->query("DELETE FROM " . DB_PREFIX . "widget_landing_page WHERE widget_id = (SELECT DISTINCT widget_id FROM " . DB_PREFIX . "widget WHERE `name` = '" . $this->db->escape($name) . "')");

            $this->cache->delete('widgets');
            $this->cache->delete('admin-widgets-widgets');
        }
	}
	
	/**
	 * ModelStoreWidget::deleteRow()
	 *
     * @param string $id
     * @see DB
     * @see Cache
	 * @return void
	 */
	public function deleteRow($id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "widget_landing_page ".
            "WHERE widget_id IN (".
                "SELECT DISTINCT widget_id FROM " . DB_PREFIX . "widget ".
                "WHERE `settings` LIKE '%" . $this->db->escape($id) . "%' ".
            ")");

		$this->db->query("DELETE FROM " . DB_PREFIX . "widget ".
            "WHERE `settings` LIKE '%" . $this->db->escape($id) . "%'");

		$this->db->query("DELETE FROM " . DB_PREFIX . "property ".
            "WHERE `object_type` = 'widget_cols' ".
            "AND `value` LIKE '%" . $this->db->escape($id) . "%'");

		$this->db->query("DELETE FROM " . DB_PREFIX . "property ".
            "WHERE `object_type` = 'widget_rows' ".
            "AND `key` = '" . $this->db->escape($id) . "'");
        $this->cache->delete('widgets');
	}

	/**
	 * ModelStoreWidget::deleteRow()
	 *
	 * @param string $id
     * @see DB
     * @see Cache
	 * @return void
	 */
	public function deleteColumn($id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "widget_landing_page ".
            "WHERE widget_id IN (".
                "SELECT DISTINCT widget_id FROM " . DB_PREFIX . "widget ".
                "WHERE `settings` LIKE '%" . $this->db->escape($id) . "%' ".
            ")");

        $this->db->query("DELETE FROM " . DB_PREFIX . "widget ".
            "WHERE `settings` LIKE '%" . $this->db->escape($id) . "%'");

		$this->db->query("DELETE FROM " . DB_PREFIX . "property ".
            "WHERE `object_type` = 'widget_cols' ".
            "AND `key` = '" . $this->db->escape($id) . "'");

		$this->db->query("DELETE FROM " . DB_PREFIX . "property ".
            "WHERE `object_type` = 'widget_cols' ".
            "AND `key` = '" . $this->db->escape($id) . "'");

        $this->cache->delete('widgets');
    }

	/**
	 * ModelStoreWidget::deleteAll()
	 * 
	 * @param int $widget_id
     * @see DB
     * @see Cache
	 * @return void 
	 */
	public function deleteAll($name) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "widget_landing_page WHERE widget_id IN 
        (SELECT widget_id FROM " . DB_PREFIX . "widget WHERE `extension` = '" . $this->db->escape($name) . "')");
		$this->db->query("DELETE FROM " . DB_PREFIX . "widget WHERE `extension` = '" . $this->db->escape($name) . "'");
        $this->cache->delete('widgets-widgets');
	}
	
	/**
	 * ModelStoreWidget::getWidget()
	 * 
	 * @param int $widget_id
     * @see DB
     * @see Cache
	 * @return array sql record 
	 */
	public function getWidget($widget_id) {
	    $prefix = 'widgets.widget.'. $widget_id;

        $cached = $this->cache->get($prefix, $prefix);
        if ($cached) {
            return $cached;
        } else {
            $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "widget t ".
                "WHERE t.widget_id = '" . (int)$widget_id . "'");
            $this->cache->set($prefix, $query->row, $prefix);
            return $query->row;
        }
	}
	
	/**
	 * ModelStoreWidget::getWidget()
	 * 
	 * @param int $widget_id
     * @see DB
     * @see Cache
	 * @return array sql record 
	 */
	public function getByName($name) {
        $prefix = 'widgets.widget.'. $name;

        $cached = $this->cache->get($prefix, $prefix);
        if ($cached) {
            return $cached;
        } else {
            $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "widget WHERE `name` = '" . $this->db->escape($name) . "'");
            $return = array(
                'widget_id' => $query->row['widget_id'],
                'code' => $query->row['code'],
                'name' => $query->row['name'],
                'position' => $query->row['position'],
                'extension' => $query->row['extension'],
                'status' => $query->row['status'],
                'app' => $query->row['app'],
                'order' => $query->row['order'],
                'settings' => $query->row['settings'],
                'landing_pages' => $this->getLandingPages($query->row['widget_id']),
            );
            $this->cache->set($prefix, $return, $prefix);
            return $return;
        }
	}

    /**
     * ModelStoreWidget::getWidgets()
     * 
     * @param mixed $data
     * @see DB
     * @see Cache
     * @return array sql records 
     */
    public function getAll(array $data = [], array $options = [])
    {
        $prefix = "widgets.admin.widgets.";
                $data['app'] .' '.
                $data['position'] .' '.
                $data['landing_page'] .' '.
                $data['store_id'] .' '.
                $data['object_type'] .' '.
                $data['object_id'];

        $cachedId = $prefix .".".
            (int)STORE_ID ."_".
            serialize($data).
            (int)$data['store_id'];

        $cached = $this->cache->get($cachedId, $prefix);
        if ($cached && !(bool)$this->user->getId()) {
            return $cached;
        } else {
            $sql = "SELECT * FROM `" . DB_PREFIX . "widget` ";

            if (isset($data['store_id']) && !empty($data['store_id'])) {
                $criteria[] = " store_id = '" . (int)$data['store_id'] . "'";
            }

            if (isset($data['row_id']) && !empty($data['row_id'])) {
                $criteria[] = " `settings` LIKE '%row_id=" . $this->db->escape($data['row_id']) . "%' ";
            }

            if (isset($data['col_id']) && !empty($data['col_id'])) {
                $criteria[] = " `settings` LIKE '%col_id=" . $this->db->escape($data['col_id']) . "%' ";
            }

            if (isset($data['show_in_mobile']) && !empty($data['show_in_mobile'])) {
                $criteria[] = " `settings` LIKE '%showonmobile%' ";
            }

            if (isset($data['show_in_tablet']) && !empty($data['show_in_tablet'])) {
                $criteria[] = " `settings` LIKE '%showontablet%' ";
            }

            if (isset($data['show_in_facebook']) && !empty($data['show_in_facebook'])) {
                $criteria[] = " `settings` LIKE '%showonfacebook%' ";
            }

            if (isset($data['show_in_desktop']) && !empty($data['show_in_desktop'])) {
                $criteria[] = " `settings` LIKE '%showondesktop%' ";
            }

            if (isset($data['async']) && !empty($data['async'])) {
                $criteria[] = " `settings` LIKE '%async=on%' ";
            }

            if (isset($data['object_type']) && !empty($data['object_type']) && $data['object_type'] !== 'widget') {
                $criteria[] = " `settings` LIKE '%object_type=" . $this->db->escape($data['object_type']) . "%' ";
            }

            if (isset($data['object_id']) && !empty($data['object_id']) && $data['object_type'] !== 'widget') {
                $criteria[] = " `settings` LIKE '%object_id=" . intval($data['object_id']) . "%' ";
            } else {
                $criteria[] = " `settings` NOT LIKE '%object_id%' ";
            }

            if (isset($criteria)) {
                $sql .= " WHERE " . implode(" AND ", $criteria);
            }

            $sql .= " ORDER BY `order` ASC";

            $widgets = $this->db->query($sql);
            $return = [];
            foreach ($widgets->rows as $widget) {
                $return[$widget['position']][] = array(
                    'widget_id' => $widget['widget_id'],
                    'code' => $widget['code'],
                    'name' => $widget['name'],
                    'position' => $widget['position'],
                    'extension' => $widget['extension'],
                    'status' => $widget['status'],
                    'app' => $widget['app'],
                    'order' => $widget['order'],
                    'settings' => $widget['settings'],
                    'landing_pages' => $this->getLandingPages($widget['widget_id']),
                );
            }
            return $return;
        }
	}

    public function getWidgets($position = array()) {
        $data = is_array($position) ? $position : array('position' => $position);

        $data['landing_page'] = isset($data['landing_page']) ? $data['landing_page'] : $this->landing_page;
        $data['store_id'] = isset($data['store_id']) ? $data['store_id'] : STORE_ID;
        $data['object_type'] = isset($data['object_type']) ? $data['object_type'] : $this->object_type;
        $data['object_id'] = isset($data['object_id']) ? $data['object_id'] : $this->object_id;
        $data['app'] = isset($data['app']) ? $data['app'] : $this->app;

        $cache_prefix = "widgets.widgets.".
                $data['app'] .' '.
                $data['position'] .' '.
                $data['landing_page'] .' '.
                $data['store_id'] .' '.
                $data['object_type'] .' '.
                $data['object_id'];

        $cachedId = $cache_prefix.
            (int)STORE_ID ."_".
            serialize($data).
            (int)$data['store_id'];

        $cached = $this->cache->get($cachedId, $cache_prefix);
        if (!$cached || (bool)$this->user->getId()) {
            $sql = "SELECT * FROM `" . DB_PREFIX . "widget` w ";

            $position = isset($data['position']) && !empty($data['position']) ? $data['position'] : "main";

            $criteria[] = " w.`position` = '" . $this->db->escape($position) . "' ";
            $criteria[] = " w.`store_id` = '" . (int)$data['store_id'] . "' ";

            if (isset($this->landing_page) && !empty($this->landing_page)) {
                $criteria[] = " w.`widget_id` IN (SELECT widget_id FROM " . DB_PREFIX . "widget_landing_page WHERE landing_page = '" . $this->db->escape($this->landing_page) . "' OR landing_page = 'all') ";
            } elseif (isset($data['landing_page']) && !empty($data['landing_page'])) {
                $criteria[] = " w.`widget_id` IN (SELECT widget_id FROM " . DB_PREFIX . "widget_landing_page WHERE landing_page = '" . $this->db->escape($data['landing_page']) . "' OR landing_page = 'all') ";
            }

            if (isset($data['app']) && !empty($data['app'])) {
                $criteria[] = " w.`app` = '" . $this->db->escape($this->app) . "' ";
            } else {
                $criteria[] = " w.`app` = 'shop' ";
            }

            if (isset($data['row_id']) && !empty($data['row_id'])) {
                $criteria[] = " `settings` LIKE '%" . $this->db->escape($data['row_id']) . "%' ";
            }

            if (isset($data['col_id']) && !empty($data['col_id'])) {
                $criteria[] = " `settings` LIKE '%" . $this->db->escape($data['col_id']) . "%' ";
            }

            if (isset($data['show_in_mobile']) && !empty($data['show_in_mobile'])) {
                $criteria[] = " `settings` LIKE '%show_in_mobile=on%' ";
            }

            if (isset($data['show_in_tablet']) && !empty($data['show_in_tablet'])) {
                $criteria[] = " `settings` LIKE '%show_in_tablet=on%' ";
            }

            if (isset($data['show_in_facebook']) && !empty($data['show_in_facebook'])) {
                $criteria[] = " `settings` LIKE '%show_in_facebook=on%' ";
            }

            if (isset($data['show_in_desktop']) && !empty($data['show_in_desktop'])) {
                $criteria[] = " `settings` LIKE '%show_in_desktop=on%' ";
            }

            if (isset($data['async']) && !empty($data['async'])) {
                $criteria[] = " `settings` LIKE '%async=on%' ";
            }

            if (isset($data['object_type']) && !empty($data['object_type']) && $data['object_type'] !== 'widget') {
                $criteria[] = " `settings` LIKE '%object_type=" . $this->db->escape($data['object_type']) . "%' ";
            } else {
                $criteria[] = " `settings` NOT LIKE '%object_type%' ";
            }

            if (isset($data['object_type']) && !empty($data['object_type']) && isset($data['object_id']) && !empty($data['object_id']) && $data['object_type'] !== 'widget') {
                $criteria[] = " `settings` LIKE '%object_id=" . intval($data['object_id']) . "%' ";
            } else {
                $criteria[] = " `settings` NOT LIKE '%object_id%' ";
            }

            if (isset($criteria)) {
                $sql .= " WHERE " . implode(" AND ", $criteria);
            }

            $sql .= " ORDER BY `order` ASC";

            $result = $this->db->query($sql);

            $this->widgets = $result->rows;
            $this->cache->set($cachedId, $this->widgets, $cache_prefix);
            return $this->widgets;
        } else {
            return $cached;
        }
    }

    public function getRoutes() {
        $this->load->helper('widgets');
        $w = new NecoWidget($this->registry);
        return $w->getRoutes();
    }

    public function getRows($data, $useCache=true) {
        $data['landing_page'] = isset($data['landing_page']) ? $data['landing_page'] : $this->landing_page;
        $data['object_type'] = isset($data['object_type']) ? $data['object_type'] : $this->object_type;
        $data['object_id'] = isset($data['object_id']) ? $data['object_id'] : $this->object_id;
        $data['store_id'] = isset($data['store_id']) ? $data['store_id'] : STORE_ID;
        $data['app'] = isset($data['app']) ? $data['app'] : $this->app;

        $cache_prefix = "widgets.rows.".
                $data['app'] .' '.
                $data['landing_page'] .' '.
                $data['store_id'] .' '.
                $data['object_type'] .' '.
                $data['object_id'];

        $cachedId = $cache_prefix .'.'.
            (int)STORE_ID ."_".
            serialize($data).
            (int)$data['store_id'];

        $cached = $this->cache->get($cachedId, $cache_prefix);
        if (!$cached || !$useCache || (bool)$this->user->getId()) {
            $sql = "SELECT * FROM `" . DB_PREFIX . "property` p ";

            $criteria[] = " p.`object_type` = 'widget_rows' ";
            $criteria[] = " p.`store_id` = '" . intval($data['store_id']) . "' ";

            if (isset($data['position']) && !empty($data['position'])) {
                $criteria[] = " p.`group` = '" . $this->db->escape($data['position']) . "' ";
            }

            if (isset($data['row_id']) && !empty($data['row_id'])) {
                $criteria[] = " p.`key` = '" . $this->db->escape($data['row_id']) . "' ";
            }

            if (isset($data['object_type']) && !empty($data['object_type']) && $data['object_type'] !== 'widget') {
                $criteria[] = " p.`value` LIKE '%object_type=" . $this->db->escape($data['object_type']) . "%' ";
            } else {
                $criteria[] = " p.`value` NOT LIKE '%object_type%' ";
            }

            if (isset($data['object_type']) && !empty($data['object_type']) && isset($data['object_id']) && !empty($data['object_id']) && $data['object_type'] !== 'widget') {
                $criteria[] = " p.`value` LIKE '%object_id=" . intval($data['object_id']) . "%' ";
            } else {
                $criteria[] = " p.`value` NOT LIKE '%object_id%' ";
            }

            if (isset($data['landing_page']) && !empty($data['landing_page'])) {
                $criteria[] = " p.`value` LIKE '%landing_page=" . $this->db->escape($data['landing_page']) . "%' ";
            }

            if (isset($data['app']) && !empty($data['app'])) {
                $criteria[] = " p.`value` LIKE '%app=" . $this->db->escape($data['app']) . "%' ";
            }

            if (isset($data['show_in_mobile']) && !empty($data['show_in_mobile'])) {
                $criteria[] = " p.`value` LIKE '%show_in_mobile=on%' ";
            }

            if (isset($data['show_in_tablet']) && !empty($data['show_in_tablet'])) {
                $criteria[] = " p.`value` LIKE '%show_in_tablet=on%' ";
            }

            if (isset($data['show_in_facebook']) && !empty($data['show_in_facebook'])) {
                $criteria[] = " p.`value` LIKE '%show_in_facebook=on%' ";
            }

            if (isset($data['show_in_desktop']) && !empty($data['show_in_desktop'])) {
                $criteria[] = " p.`value` LIKE '%show_in_desktop=on%' ";
            }

            if (isset($data['async']) && !empty($data['async'])) {
                $criteria[] = " p.`value` LIKE '%async=on%' ";
            }

            if (isset($criteria)) {
                $sql .= " WHERE " . implode(" AND ", $criteria);
            }

            $sql .= " ORDER BY `order` ASC";

            $result = $this->db->query($sql);
            if (isset($data['full_tree'])) {
                foreach ($result->rows as $k => $row) {
                    $col_data = [];
                    $col_data['row_id'] = $row['key'];
                    $settings = unserialize($row['value']);

                    if (isset($data['position']) && !empty($data['position'])) $col_data['position'] = $data['position'];
                    else $col_data['position'] = $settings['position'] ?? "";

                    if (isset($data['store_id'])) $col_data['store_id'] = $data['store_id'];
                    if (isset($data['object_type']) && !empty($data['object_type'])) $col_data['object_type'] = $data['object_type'];
                    if (isset($data['object_id']) && !empty($data['object_id'])) $col_data['object_id'] = $data['object_id'];
                    if (isset($data['landing_page']) && !empty($data['landing_page'])) $col_data['landing_page'] = $data['landing_page'];
                    if (isset($data['app']) && !empty($data['app'])) $col_data['app'] = $data['app'];
                    if (isset($data['show_in_mobile']) && !empty($data['show_in_mobile'])) $col_data['show_in_mobile'] = $data['show_in_mobile'];
                    if (isset($data['show_in_tablet']) && !empty($data['show_in_tablet'])) $col_data['show_in_tablet'] = $data['show_in_tablet'];
                    if (isset($data['show_in_facebook']) && !empty($data['show_in_facebook'])) $col_data['show_in_facebook'] = $data['show_in_facebook'];
                    if (isset($data['show_in_desktop']) && !empty($data['show_in_desktop'])) $col_data['show_in_desktop'] = $data['show_in_desktop'];
                    if (isset($data['async']) && !empty($data['async'])) $col_data['async'] = $data['async'];
                    if (isset($data['full_tree']) && !empty($data['full_tree'])) $col_data['full_tree'] = $data['full_tree'];

                    $result->rows[$k]['columns'] = $this->getCols($col_data);
                }
            }

            $this->cache->set($cachedId, $result->rows, $cache_prefix);
            return $result->rows;
        } else {
            return $cached;
        }
    }

    public function getCols($data) {
        $data['landing_page'] = isset($data['landing_page']) ? $data['landing_page'] : $this->landing_page;
        $data['store_id'] = isset($data['store_id']) ? $data['store_id'] : STORE_ID;
        $data['object_type'] = isset($data['object_type']) ? $data['object_type'] : $this->object_type;
        $data['object_id'] = isset($data['object_id']) ? $data['object_id'] : $this->object_id;
        $data['app'] = isset($data['app']) ? $data['app'] : $this->app;
        
        $cache_prefix = "widgets.cols.".
                $data['app'] .' '.
                $data['landing_page'] .' '.
                $data['store_id'] .' '.
                $data['object_type'] .' '.
                $data['object_id'];

        $cachedId = $cache_prefix .'.'.
            (int)STORE_ID ."_".
            serialize($data).
            (int)$data['store_id'];

        $cached = $this->cache->get($cachedId, $cache_prefix);
        if (!$cached || (bool)$this->user->getId()) {
            $sql = "SELECT * FROM `" . DB_PREFIX . "property` p ";

            $criteria[] = " p.`store_id` = '" . intval($data['store_id']) . "' ";
            $criteria[] = " p.`object_type` = 'widget_cols' ";

            if (isset($data['position']) && !empty($data['position'])) {
                $criteria[] = " p.`group` = '" . $this->db->escape($data['position']) . "' ";
            }

            if (isset($data['col_id']) && !empty($data['col_id'])) {
                $criteria[] = " p.`key` = '" . $this->db->escape($data['col_id']) . "' ";
            }

            if (isset($data['row_id']) && !empty($data['row_id'])) {
                $criteria[] = " p.`value` LIKE '%row_id=" . $this->db->escape($data['row_id']) . "%' ";
            }

            if (isset($data['object_type']) && !empty($data['object_type']) && $data['object_type'] !== 'widget') {
                $criteria[] = " p.`value` LIKE '%object_type=" . $this->db->escape($data['object_type']) . "%' ";
            } else {
                $criteria[] = " p.`value` NOT LIKE '%object_type%' ";
            }

            if (isset($data['object_type']) && !empty($data['object_type']) && isset($data['object_id']) && !empty($data['object_id']) && $data['object_type'] !== 'widget') {
                $criteria[] = " p.`value` LIKE '%object_id=" . intval($data['object_id']) . "%' ";
            } else {
                $criteria[] = " p.`value` NOT LIKE '%object_id%' ";
            }

            if (isset($data['landing_page']) && !empty($data['landing_page'])) {
                $criteria[] = " p.`value` LIKE '%landing_page=" . $this->db->escape($data['landing_page']) . "%' ";
            }

            if (isset($data['app']) && !empty($data['app'])) {
                $criteria[] = " p.`value` LIKE '%app=" . $this->db->escape($data['app']) . "%' ";
            }

            if (isset($data['show_in_mobile']) && !empty($data['show_in_mobile'])) {
                $criteria[] = " p.`value` LIKE '%show_in_mobile=on%' ";
            }

            if (isset($data['show_in_tablet']) && !empty($data['show_in_tablet'])) {
                $criteria[] = " p.`value` LIKE '%show_in_tablet=on%' ";
            }

            if (isset($data['show_in_facebook']) && !empty($data['show_in_facebook'])) {
                $criteria[] = " p.`value` LIKE '%show_in_facebook=on%' ";
            }

            if (isset($data['show_in_desktop']) && !empty($data['show_in_desktop'])) {
                $criteria[] = " p.`value` LIKE '%show_in_desktop=on%' ";
            }

            if (isset($data['async']) && !empty($data['async'])) {
                $criteria[] = " p.`value` LIKE '%async=on%' ";
            }

            if (isset($criteria)) {
                $sql .= " WHERE " . implode(" AND ", $criteria);
            }

            $sql .= " ORDER BY `order` ASC";
            $result = $this->db->query($sql);

            if (isset($data['full_tree'])) {
                foreach ($result->rows as $k => $col) {
                    $widget_data = [];
                    $widget_data['col_id'] = $col['key'];
                    if (isset($data['position']) && !empty($data['position'])) $widget_data['position'] = $data['position'];
                    if (isset($data['store_id'])) $widget_data['store_id'] = $data['store_id'];
                    if (isset($data['object_type']) && !empty($data['object_type'])) $widget_data['object_type'] = $data['object_type'];
                    if (isset($data['object_id']) && !empty($data['object_id'])) $widget_data['object_id'] = $data['object_id'];
                    if (isset($data['landing_page']) && !empty($data['landing_page'])) $widget_data['landing_page'] = $data['landing_page'];
                    if (isset($data['app']) && !empty($data['app'])) $widget_data['app'] = $data['app'];
                    if (isset($data['show_in_mobile']) && !empty($data['show_in_mobile'])) $widget_data['show_in_mobile'] = $data['show_in_mobile'];
                    if (isset($data['show_in_tablet']) && !empty($data['show_in_tablet'])) $widget_data['show_in_tablet'] = $data['show_in_tablet'];
                    if (isset($data['show_in_facebook']) && !empty($data['show_in_facebook'])) $widget_data['show_in_facebook'] = $data['show_in_facebook'];
                    if (isset($data['show_in_desktop']) && !empty($data['show_in_desktop'])) $widget_data['show_in_desktop'] = $data['show_in_desktop'];
                    if (isset($data['async']) && !empty($data['async'])) $widget_data['async'] = $data['async'];
                    if (isset($data['full_tree']) && !empty($data['full_tree'])) $widget_data['full_tree'] = $data['full_tree'];

                    $result->rows[$k]['widgets'] = $this->getWidgets($widget_data);
                }
            }

            $this->cache->set($cachedId, $result->rows, $cache_prefix);
            return $result->rows;
        } else {
            return $cached;
        }
    }

	/**
	 * ModelStoreWidget::getWidgets()
	 * 
	 * @param mixed $data
     * @see DB
     * @see Cache
	 * @return array sql records 
	 */
    public function getLandingPages($id) {
        $widgets = $this->db->query("SELECT * FROM `" . DB_PREFIX . "widget_landing_page` WHERE widget_id = '". (int)$id ."'");
        return $widgets->rows;
	}

	/**
	 * ModelStoreWidget::getTotalWidgets()
	 * 
     * @see DB
	 * @return int Count sql records 
	 */
	public function getTotalWidgets($data = null) {
        $sql = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "widget m";
			
        $implode = [];
    		
    	if (!empty($data['filter_name'])) {
    	   $implode[] = "LCASE(name) LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
    	}
    		
    	if (!empty($data['filter_template'])) {
    	   $implode[] = "template_id IN (SELECT template_id FROM " . DB_PREFIX . "template WHERE LCASE(name) LIKE '%" . $this->db->escape(strtolower($data['filter_template'])) . "%')";
    	}
    		
    	if (!empty($data['filter_date_start']) && !empty($data['filter_date_end'])) {
            $implode[] = " date_added BETWEEN '" . date('Y-m-d h:i:s',strtotime($data['filter_date_start'])) . "' AND '" . date('Y-m-d h:i:s',strtotime($data['filter_date_end'])) . "'";
   		} elseif (!empty($data['filter_date_start'])) {
            $implode[] = " date_added BETWEEN '" . date('Y-m-d h:i:s',strtotime($data['filter_date_start'])) . "' AND '" . date('Y-m-d h:i:s') . "'";
   		}

        if (isset($data['row_id']) && !empty($data['row_id'])) {
            $criteria[] = " `settings` LIKE '%row_id=" . $this->db->escape($data['row_id']) . "%' ";
        }

        if (isset($data['col_id']) && !empty($data['col_id'])) {
            $criteria[] = " `settings` LIKE '%col_id=" . $this->db->escape($data['col_id']) . "%' ";
        }

        if (isset($data['show_in_mobile']) && !empty($data['show_in_mobile'])) {
            $criteria[] = " `settings` LIKE '%show_in_mobile=on%' ";
        }

        if (isset($data['show_in_tablet']) && !empty($data['show_in_tablet'])) {
            $criteria[] = " `settings` LIKE '%show_in_tablet=on%' ";
        }

        if (isset($data['show_in_facebook']) && !empty($data['show_in_facebook'])) {
            $criteria[] = " `settings` LIKE '%show_in_facebook=on%' ";
        }

        if (isset($data['show_in_desktop']) && !empty($data['show_in_desktop'])) {
            $criteria[] = " `settings` LIKE '%show_in_desktop=on%' ";
        }

        if (isset($data['async']) && !empty($data['async'])) {
            $criteria[] = " `settings` LIKE '%async=on%' ";
        }

        if (isset($data['object_type']) && !empty($data['object_type'])) {
            $criteria[] = " `settings` LIKE '%object_type=". $this->db->escape($data['object_type']) ."%' ";
        }
        
        if (isset($data['object_id']) && !empty($data['object_id'])) {
            $criteria[] = " `settings` LIKE '%object_id=". intval($data['object_id']) ."%' ";
        }
        
        if (isset($data['store_id']) && !empty($data['store_id'])) {
            $criteria[] = " `store_id` = '". intval($data['object_id']) ."' ";
        }
        
    	if ($implode) {
    	   $sql .= " WHERE " . implode(" AND ", $implode);
    	}
            
    	$query = $this->db->query($sql);
            
		return $query->row['total'];
	}

	/**
	 * ModelStoreProduct::sortWidget()
	 * @param array $data
     * @see DB
     * @see Cache
	 * @return void
	 */
	public function sortWidget($data) {
	   if (!is_array($data)) return false;
       foreach ($data as $widgetName => $value) {
           $result = $this->db->query("SELECT * FROM `". DB_PREFIX ."widget` WHERE `name` = '" . $this->db->escape($value['name']) . "'");
           if (isset($result->row['settings'])) {
                $settings = unserialize($result->row['settings']);
                $settings->row_id = $value['row_id'];
                $settings->col_id = $value['col_id'];

                $this->db->query("UPDATE " . DB_PREFIX . "widget SET 
                `order` = '" . (int)$value['order'] . "',
                `position` = '" . $this->db->escape($value['position']) . "',
                `settings` = '" . $this->db->escape(serialize($settings)) . "'
                WHERE `name` = '" . $this->db->escape($value['name']) . "'");
           }
       }

        $this->cache->delete("widgets-widgets");
        $this->cache->delete("widgets-rows");
        $this->cache->delete("widgets-cols");

	   return true;
	}

	/**
	 * ModelStoreProduct::sortWidget()
	 * @param array $data
     * @see DB
     * @see Cache
	 * @return void
	 */
	public function sortRow($data) {
	    if (!is_array($data)) return false;
        foreach ($data as $k => $v) {
            $this->db->query("UPDATE ". DB_PREFIX ."property SET 
            `order` = '". (int)$v['order'] ."',
            `group` = '". $this->db->escape($v['position']) ."'
             WHERE object_type = 'widget_rows' AND `key` = '" . $this->db->escape($v['id']) . "'");
        }
        $this->cache->delete("widgets-rows");
	    return true;
	}

	/**
	 * ModelStoreProduct::sortWidget()
	 * @param array $data
     * @see DB
     * @see Cache
	 * @return void
	 */
	public function sortCol($data) {
	   if (!is_array($data)) return false;
       foreach ($data as $k => $v) {
            $this->db->query("UPDATE ". DB_PREFIX ."property SET 
            `order` = '". (int)$v['order'] ."',
            `group` = '". $this->db->escape($v['position']) ."'
             WHERE object_type = 'widget_cols' AND `key` = '" . $this->db->escape($v['id']) . "'");
       }

        $this->cache->delete("widgets-rows");
        $this->cache->delete("widgets-cols");
	   return true;
	}
}