<?php

require_once(DIR_CONTROLLER . "module/modulecontroller.php");

class ControllerModuleProductOrderForm extends ControllerModuleModuleController
{
    protected string $moduleName = 'product_order_form';
    protected array $defaults = [];

    public function init()
    {
        $this->addFilter("module:settings", function ($data) {
            $settings = $data['settings'];
            $widget   = $data['widget'];
            $render   = $data['render'];

            $query_data = [];
            $Url = new Url($this->registry);
            if ($this->request->hasQuery('product_id') || $this->request->hasPost('product_id')) {
                $query_data['product_id'] = $this->request->hasPost('product_id') ? $this->request->getPost('product_id') : $this->request->getQuery('product_id');
            } else {
                $query_data['product_id'] = (!empty($settings['products'])) ? $settings['products'] : null;
            }

            if (!is_callable([$this, 'modelProduct'])) $this->load->model('store/product');
            $results = $this->modelProduct->getAll($query_data);

            if (isset($results[0])) {
                if ($results[0]['minimum']) {
                    $this->data['minimum'] = $results[0]['minimum'];
                } else {
                    $this->data['minimum'] = 1;
                }
                $this->data['text_minimum'] = sprintf($this->language->get('text_minimum'), $this->data['minimum']);

                $this->data['action'] = $Url::createUrl('checkout/cart');
                $this->data['redirect'] = $Url::createUrl('store/product', '&product_id=' . $results[0]['product_id']);
                $this->data['product_id'] = $results[0]['product_id'];
                $this->data['options'] = [];

                $options = $this->modelProduct->getProductOptions($results[0]['product_id']);

                foreach ($options as $option) {
                    $option_value_data = [];
                    foreach ($option['option_value'] as $option_value) {
                        $option_value_data[] = array(
                            'option_value_id' => $option_value['product_option_value_id'],
                            'name' => $option_value['name'],
                            'price' => (float) $option_value['price'] ? $this->currency->format($this->tax->calculate($option_value['price'], $results[0]['tax_class_id'], $this->config->get('config_tax'))) : false,
                            'prefix' => $option_value['prefix']
                        );
                    }

                    $this->data['options'][] = array(
                        'option_id' => $option['product_option_id'],
                        'name' => $option['name'],
                        'option_value' => $option_value_data
                    );
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