<?php

require_once(DIR_CONTROLLER . "module/modulecontroller.php");

class ControllerModulePostCategoryImage extends ControllerModuleModuleController
{
    protected string $moduleName = 'post_category_image';
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
            if ($this->request->hasQuery('category_id') || $this->request->hasPost('category_id')) {
                $query_data['category_id'] = $this->request->hasPost('category_id') ? $this->request->getPost('category_id') : $this->request->getQuery('category_id');
            } else {
                $query_data['category_id'] = (!empty($settings['categories'])) ? $settings['categories'] : null;
            }

            if (!is_callable([$this, 'modelCategory'])) $this->load->model('content/category');
            $results = $this->modelCategory->getAll($query_data);

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