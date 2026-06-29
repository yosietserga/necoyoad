<?php

require_once(DIR_CONTROLLER . "module/modulecontroller.php");

class ControllerModuleProductStock extends ControllerModuleModuleController
{
    protected string $moduleName = 'product_stock';
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
            //TODO: improve to include warehouses managment
            if (isset($results[0])) {
                if ($results[0]['quantity'] <= 0) {
                    $this->data['stock'] = $results[0]['stock'];
                } else {
                    if ($this->config->get('config_stock_display')) {
                        $this->data['stock'] = $results[0]['quantity'];
                    } else {
                        $this->data['stock'] = $this->language->get('text_instock');
                    }
                }
            }

            return [
                'widget'   => $widget,
                'render'   => $render,
                'settings' => $settings,
            ];
        });
    }
}