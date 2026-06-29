<?php

require_once(DIR_CONTROLLER . "module/modulecontroller.php");

class ControllerModuleManufacturerList extends ControllerModuleModuleController
{
    protected string $moduleName = 'manufacturer_list';
    protected array $defaults = [];

    public function init()
    {
        $this->addFilter("module:settings", function ($data) {
            $settings = $data['settings'];
            $widget   = $data['widget'];
            $render   = $data['render'];

            $this->load->auto('store/manufacturer');
            $this->load->auto('image');

            $query_data = [];
            if ($this->request->hasQuery('manufacturer_id') || $this->request->hasPost('manufacturer_id')) {
                $query_data['manufacturer_id'] = $this->request->hasPost('manufacturer_id') ? $this->request->getPost('manufacturer_id') : $this->request->getQuery('manufacturer_id');
            } else {
                $query_data['manufacturer_id'] = (!empty($settings['manufacturers'])) ? $settings['manufacturers'] : null;
            }

            if (!is_callable([$this, 'modelManufacturer'])) $this->load->model('store/manufacturer');
            $this->data['manufacturers'] = $this->modelManufacturer->getAll($query_data);

            return [
                'widget'   => $widget,
                'render'   => $render,
                'settings' => $settings,
            ];
        });
    }
}