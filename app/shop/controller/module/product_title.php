<?php

require_once(DIR_CONTROLLER . "module/modulecontroller.php");

class ControllerModuleProductTitle extends ControllerModuleModuleController
{
    protected string $moduleName = 'product_title';
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

            if (!is_callable([$this, 'modelProduct'])) $this->load->model('store/product');
            $results = $this->modelProduct->getAll($query_data);

            if (isset($results[0])) {
                $this->data['title'] = $results[0]['title'];
                $this->data['heading_title'] = isset($results[0]['title']) ? $results[0]['title'] : (isset($settings['title']) ? $settings['title'] : $this->language->get('heading_title'));
            }

            return [
                'widget'   => $widget,
                'render'   => $render,
                'settings' => $settings,
            ];
        });
    }
}