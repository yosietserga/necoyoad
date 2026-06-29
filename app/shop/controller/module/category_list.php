<?php

require_once(DIR_CONTROLLER . "module/modulecontroller.php");

class ControllerModuleCategoryList extends ControllerModuleModuleController {
    protected string $moduleName = 'category_list';
    protected array $defaults = [];

    public function init() {

        $this->defaults['width'] = $this->config->get('config_image_category_width');
        $this->defaults['height'] = $this->config->get('config_image_category_height');
        $this->defaults['thumb_width'] = $this->config->get('config_image_thumb_width');
        $this->defaults['thumb_height'] = $this->config->get('config_image_thumb_height');

        $this->addFilter("module:settings", function ($data) {
            $settings = $data['settings'];
            $widget   = $data['widget'];
            $render   = $data['render'];

            $query_data = [];
            if ($this->request->hasQuery('category_id') || $this->request->hasPost('category_id')) {
                $query_data['category_id'] = $this->request->hasPost('category_id') ? $this->request->getPost('category_id') : $this->request->getQuery('category_id');
            } else {
                $query_data['category_id'] = (!empty($settings['categories'])) ? $settings['categories'] : null;
            }

            if (!is_callable([$this, 'modelCategory'])) $this->load->model('store/category');
            $this->data['categories'] = $this->getCategories($query_data);
        
            return [
                'widget'   => $widget,
                'render'   => $render,
                'settings' => $settings,
            ];
        });
    }
    
    protected function getCategories($data) {
        $_data = [];
        $_data['parent_id'] = isset($data['parent_id']) ? (int)$data['parent_id'] : (int)$data['category_id'];

        static $ready = [];
        if (in_array($_data['parent_id'], $ready)) return false;
        $ready[] = $_data['parent_id'];

        $this->load->auto('store/category');
        $results = $this->modelCategory->getAll($_data);
        if (count($results)==0) return false;

        $c_data = $_data;
        unset($c_data['parent_id']);
        foreach ($results as $k=>$result) {
            $c_data['parent_id'] = $result['category_id'];
            $results[$k]['children'] = $this->getCategories($c_data);
        }
        return $results;
    }
}
