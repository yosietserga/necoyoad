<?php

require_once(DIR_CONTROLLER . "module/modulecontroller.php");

class ControllerModuleManufacturerImage extends ControllerModuleModuleController
{
    protected string $moduleName = 'manufacturer_image';
    protected array $defaults = [];

    public function init()
    {
        $this->defaults['width'] = $this->config->get('config_image_category_width');
        $this->defaults['height'] = $this->config->get('config_image_category_height');

        $this->addFilter("module:settings", function ($data) {
            $settings = $data['settings'];
            $widget   = $data['widget'];
            $render   = $data['render'];

            $query_data = [];
            if ($this->request->hasQuery('manufacturer_id') || $this->request->hasPost('manufacturer_id')) {
                $query_data['manufacturer_id'] = $this->request->hasPost('manufacturer_id') ? $this->request->getPost('manufacturer_id') : $this->request->getQuery('manufacturer_id');
            }

            if (!is_callable([$this, 'modelManufacturer'])) $this->load->model('store/manufacturer');
            $results = $this->modelManufacturer->getAll($query_data);

            if (isset($results[0])) {
                if ($results[0]['image']) {
                    $image = $results[0]['image'];
                } else {
                    $image = 'no_image.jpg';
                }

                $this->data['thumb'] = NTImage::resizeAndSave($image, $settings['width'], $settings['height']);
                $this->data['image'] = $image;
            }


            return [
                'widget'   => $widget,
                'render'   => $render,
                'settings' => $settings,
            ];
        });
    }
}