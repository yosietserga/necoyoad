<?php

require_once(DIR_CONTROLLER . "module/modulecontroller.php");

class ControllerModuleSearch extends ControllerModuleModuleController
{
    protected string $moduleName = 'search';
    protected array $defaults = [];

    public function init()
    {
        $this->addFilter("module:settings", function ($data) {
            $settings = $data['settings'];
            $widget   = $data['widget'];
            $render   = $data['render'];

            // style files
            $csspath = defined("CDN_CSS") ? CDN_CSS : HTTP_THEME_CSS;
            $csspath = (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/common/header.tpl')) ? str_replace("%theme%", $this->config->get('config_template'), $csspath) : str_replace("%theme%", "default", $csspath);

            if (file_exists($csspath . str_replace('controller', '', strtolower(__CLASS__) . '.css')))
            $styles[] = array('media' => 'all', 'href' => $csspath . str_replace('controller', '', strtolower(__CLASS__) . '.css'));
            if (isset($styles))
            $this->styles = array_merge($styles, $this->styles);

            $this->getCategories3();
            $this->getZones();

            return [
                'widget'   => $widget,
                'render'   => $render,
                'settings' => $settings,
            ];
        });
    }
    
    protected function getCategories3() {
        $output = $this->cache->get('category_select.tpl');

        if (!$output) {
            $this->load->model('store/category');

            $output = '';
            $results = $this->modelCategory->getAll(['parent_id' => 0]);
            if ($results) {
                foreach ($results as $result) {
                    $output .= '<option value="' . ($result['name']??"") . '">' . ($result['name']??"") . '</option>';
                }
            }
            $this->cache->set('category_select.tpl', $output);
        }
        $this->data['categories'] = $output;
    }

    protected function getZones() {
        $output = $this->cache->get('zone_select.tpl');

        if (!$output) {
            $this->load->model('localisation/zone');

            $output = '';
            $results = $this->modelZone->getZonesByCountryId(229);
            if ($results) {
                foreach ($results as $result) {
                    $output .= '<option value="' . $result['name'] . '">' . $result['name'] . '</option>';
                }
            }
            $this->cache->set('zone_select.tpl', $output);
        }
        $this->data['zones'] = $output;
    }
}
