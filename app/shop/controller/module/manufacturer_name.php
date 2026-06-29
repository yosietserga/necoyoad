<?php

require_once(DIR_CONTROLLER . "module/modulecontroller.php");

class ControllerModuleManufacturerName extends ControllerModuleModuleController {
    protected string $moduleName = 'manufacturer_name';
    protected array $defaults = [];

    public function init() {
        $this->addFilter("module:settings", function ($data) {
            $settings = $data['settings'];
            $widget   = $data['widget'];
            $render   = $data['render'];

            $query_data = [];
            if ($this->request->hasQuery('manufacturer_id') || $this->request->hasPost('manufacturer_id')) {
                $query_data['manufacturer_id'] = $this->request->hasPost('manufacturer_id') ? $this->request->getPost('manufacturer_id') : $this->request->getQuery('manufacturer_id');
            } else {
                $query_data['manufacturer_id'] = (!empty($settings['manufacturers'])) ? $settings['manufacturers'] : null;
            }

            if (!is_callable([$this, 'modelManufacturer'])) $this->load->model('store/manufacturer');
            $results = $this->modelManufacturer->getAll($query_data);

            if (isset($results[0])) {
                $this->data['title'] = $results[0]['name'];
                $this->data['heading_title'] = isset($results[0]['name']) ? $results[0]['name'] : (isset($settings['name']) ? $settings['name'] : $this->language->get('heading_title'));
            }

            return [
                'widget'   => $widget,
                'render'   => $render,
                'settings' => $settings,
            ];
        });
    }
}