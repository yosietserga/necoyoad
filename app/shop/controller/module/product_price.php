<?php

require_once(DIR_CONTROLLER . "module/modulecontroller.php");

class ControllerModuleProductPrice extends ControllerModuleModuleController
{
    protected string $moduleName = 'product_price';
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

            if (!$this->config->get('config_customer_price') || $this->customer->isLogged()) {
                $this->data['display_price'] = true;
            } else {
                $this->data['display_price'] = false;
            }
            
            if (isset($results[0])) {

                if ($this->data['display_price'] && $this->config->get('config_store_mode') == 'store') {
                    $this->load->auto('currency');
                    $this->load->auto('tax');

                    $product_id = $results[0]['product_id'];
                    $discount = $this->modelProduct->getProductDiscount($product_id);

                    if ($discount) {
                        $this->data['price'] = $this->currency->format($this->tax->calculate($discount, $results[0]['tax_class_id'], $this->config->get('config_tax')));
                        $this->data['original_price'] = $this->tax->calculate($discount, $results[0]['tax_class_id'], $this->config->get('config_tax'));
                        $this->data['special'] = false;
                    } else {
                        $this->data['original_price'] = $this->tax->calculate($results[0]['price'], $results[0]['tax_class_id'], $this->config->get('config_tax'));
                        $this->data['price'] = $this->currency->format($this->tax->calculate($results[0]['price'], $results[0]['tax_class_id'], $this->config->get('config_tax')));
                        $special = $this->modelProduct->getProductSpecial($product_id);
                        
                        if ($special) {
                            $this->data['special'] = $this->currency->format($this->tax->calculate($special, $results[0]['tax_class_id'], $this->config->get('config_tax')));
                            $this->data['original_price'] = $this->tax->calculate($special, $results[0]['tax_class_id'], $this->config->get('config_tax'));
                        } else {
                            $this->data['special'] = false;
                        }
                    }
                    $discounts = $this->modelProduct->getProductDiscounts($product_id);

                    $this->data['discounts'] = [];
                    foreach ($discounts as $discount) {
                        $this->data['discounts'][] = array(
                            'quantity' => $discount['quantity'],
                            'price' => $this->currency->format($this->tax->calculate($discount['price'], $results[0]['tax_class_id'], $this->config->get('config_tax')))
                        );
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