<?php

require_once(DIR_CONTROLLER . "module/modulecontroller.php");

class ControllerModuleProductAttributes extends ControllerModuleModuleController
{
    protected string $moduleName = 'product_attributes';
    protected array $defaults = [];

    public function init()
    {
        $this->addFilter("module:settings", function ($data) {
            $settings = $data['settings'];
            $widget   = $data['widget'];
            $render   = $data['render'];

            $query_data = [];
            if ($this->request->hasQuery('product_id') || $this->request->hasPost('product_id')) {
                $query_data['product_id'] = $this->request->hasPost('product_id') ? $this->request->getPost('product_id') : $this->request->getQuery('product_id');
            } else {
                $query_data['product_id'] = (!empty($settings['products'])) ? $settings['products'] : null;
            }

            if (isset($query_data['product_id'])) {
                $this->load->library('product');
                $Product = new Product($this->registry);
                $this->data['attributes'] = $Product->getAttributes($query_data['product_id']);
            }

            return [
                'widget'   => $widget,
                'render'   => $render,
                'settings' => $settings,
            ];
        });
    }
}