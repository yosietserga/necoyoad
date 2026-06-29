<?php

require_once(DIR_CONTROLLER . "module/modulecontroller.php");

class ControllerModuleProductTags extends ControllerModuleModuleController
{
    protected string $moduleName = 'product_tags';
    protected array $defaults = [];

    public function init()
    {
        $this->addFilter("module:settings", function ($data) {
            $settings = $data['settings'];
            $widget   = $data['widget'];
            $render   = $data['render'];

            $this->load->model('store/product');
            $this->load->model('store/category');
            $Url = new Url($this->registry);
            $query_data = [];
            
            if ($this->request->hasQuery('product_id') || $this->request->hasPost('product_id')) {
                $query_data['product_id'] = $this->request->hasPost('product_id') ? $this->request->getPost('product_id') : $this->request->getQuery('product_id');
            } else {
                $query_data['product_id'] = (!empty($settings['products'])) ? $settings['products'] : null;
            }

            if (!is_callable([$this, 'modelProduct'])) $this->load->model('store/product');
            $results = $this->modelProduct->getAll($query_data);

            if (isset($results[0])) {
                $this->data['manufacturer_name'] = $results[0]['manufacturer'];
                $this->data['manufacturer_link'] = $Url::createUrl('store/manufacturer', array('manufacturer_id' => $results[0]['manufacturer_id']));
                $this->data['categories'] = $this->modelCategory->getAll(array('product_id' => $results[0]['product_id']));
                $this->data['tags'] = [];

                $r = $this->modelProduct->getProductTags($results[0]['product_id']);

                foreach ($r as $result) {
                    if ($result['tag']) {
                        $this->data['tags'][] = array(
                            'tag' => $result['tag'],
                            'href' => $Url::createUrl('store/search', array('q' => $result['tag']))
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