<?php

require_once(DIR_CONTROLLER . "module/modulecontroller.php");

class ControllerModuleImage extends ControllerModuleModuleController
{
    protected string $moduleName = 'image';
    protected array $defaults = [];

    public function init()
    {
        $this->defaults['width'] = $this->config->get('config_image_category_width');
        $this->defaults['height'] = $this->config->get('config_image_category_height');

        $this->addFilter("module:settings", function ($data) {
            $settings = $data['settings'];
            $widget   = $data['widget'];
            $render   = $data['render'];

            $this->load->library('image');

            $image = 'no_image.jpg';
            if (isset($settings['image']) && is_file(DIR_IMAGE . $settings['image'])) {
                $image = $settings['image'];
            }

            $width = $settings['width'] ?? $this->config->get('config_image_category_width');
            $height = $settings['height'] ?? $this->config->get('config_image_category_height');

            $this->data['thumb'] = NTImage::resizeAndSave($image, $width, $height);
            $this->data['image'] = HTTP_IMAGE . $image;
            $this->data['width'] = $width;
            $this->data['height']= $height;

            return [
                'widget'   => $widget,
                'render'   => $render,
                'settings' => $settings,
            ];
        });
    }
}