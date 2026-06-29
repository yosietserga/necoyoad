<?php
/**
 * ModelSettingExtension
 * 
 * @package   
 * @author NecoTienda
 * @copyright Inversiones Necoyoad, C.A.
 * @version 2010
 * @access public
 */
class ModelSettingExtension extends Model {
	/**
	 * ModelSettingExtension::getInstalled()
	 * 
	 * @param string $type
     * @see DB
	 * @return array sql records
	 */
	public function getInstalled($type) {
		$extension_data = [];
		
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "extension WHERE `type` = '" . $this->db->escape($type) . "'");
		
		foreach ($query->rows as $result) {
			$extension_data[] = $result['key'];
		}
		
		return $extension_data;
	}

	/**
	 * ModelSettingExtension::isInstalled()
	 *
	 * @param string $ext
	 * @param string $type
     * @see DB
	 * @return array sql record
	 */
	public function isInstalled($ext, $type='module') {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "extension WHERE `key` = '" . $this->db->escape($ext) . "' AND `type` = '" . $this->db->escape($type) . "'");

		return $query->row;
	}

	/**
	 * ModelSettingExtension::install()
	 * 
	 * @param string $type
	 * @param string $key
     * @see DB
	 * @return void
	 */
	public function install($type, $key) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "extension SET `type` = '" . $this->db->escape($type) . "', `key` = '" . $this->db->escape($key) . "'");
	}
	
	/**
	 * ModelSettingExtension::uninstall()
	 * 
	 * @param string $type
	 * @param string $key
     * @see DB
	 * @return void
	 */
	public function uninstall($type, $key) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "extension WHERE `type` = '" . $this->db->escape($type) . "' AND `key` = '" . $this->db->escape($key) . "'");
	}

	public function getPartialTemplates($section, $position, $template = null) {
        $result = [];
        $module_links = $this->getMenus($section, $position);
        $__template = $template ? $template : $this->config->get('config_template');
        foreach ($module_links as $v) {
            $data = $v['value'];
            $tpl_file = DIR_TEMPLATE . "{$__template}/module/{$v['key']}/{$data['template']}.tpl";
            if (file_exists($tpl_file)) {
                $result[] = $tpl_file;
            }
        }
        return $result;
    }

	public function getPartials($section, $position) {
		return $this->getAllProperties(0, $section.":".$position);
	}

	public function addPartialTemplate($section, $module_name, $position, $custom_view = null) {
		$this->setProperty(0, $section.":".$position, $module_name, array(
			'module'=>$module_name,
			'position'=>$position,
			'template'=>$custom_view
		));
	}

	public function removePartialTemplate($section, $position, $module_name) {
		$this->deleteProperty(0, "{$section}:{$position}", $module_name);
	}

	public function getMenuTemplates($section, $position, $template = null) {
        return $this->getPartialTemplates($section, $position, $template);
    }

	public function getMenus($section, $position) {
		return $this->getPartials($section .'_menu', $position);
	}

	public function addMenu($section, $module_name, $position, $custom_view = null) {
		$this->addPartialTemplate($section .'_menu', $module_name, $position, $custom_view);
	}

	public function removeMenu($section, $position, $module_name) {
		$this->removePartialTemplate($section .'_menu', $position, $module_name);
	}

    public function setDescriptions($id, $data) {
        return $this->__setDescriptions('extension', $id, $data);
    }

    public function getProperty($id, $group, $key) {
        return $this->__getProperty('extension', $id, $group, $key);
    }

    public function setProperty($id, $group, $key, $value) {
        return $this->__setProperty('extension', $id, $group, $key, $value);
    }

    public function deleteProperty($id, $group='*', $key='*') {
        return $this->__deleteProperties('extension', $id, $group, $key);
    }

    public function getAllProperties($id, $group = '*') {
        return $this->__getProperties('extension', $id, $group);
    }

    public function setAllProperties($id, $group, $data) {
        if (is_array($data) && !empty($data)) {
            $this->deleteProperty($id, $group);
            foreach ($data as $key => $value) {
                $this->setProperty($id, $group, $key, $value);
            }
        }
    }
}
