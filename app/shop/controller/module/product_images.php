<?php

require_once(DIR_CONTROLLER . "module/modulecontroller.php");

class ControllerModuleProductImages extends ControllerModuleModuleController
{
    protected string $moduleName = 'product_images';
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

                $this->load->library('image');

                $settings['popup_width'] = isset($settings['popup_width']) ? $settings['popup_width'] : $this->config->get('config_image_popup_width');
                $settings['popup_height'] = isset($settings['popup_height']) ? $settings['popup_height'] : $this->config->get('config_image_popup_height');

                $settings['preview_width'] = isset($settings['preview_width']) ? $settings['preview_width'] : $this->config->get('config_image_thumb_width');
                $settings['preview_height'] = isset($settings['preview_height']) ? $settings['preview_height'] : $this->config->get('config_image_thumb_height');

                $settings['thumb_width'] = isset($settings['thumb_width']) ? $settings['thumb_width'] : $this->config->get('config_image_additional_width');
                $settings['thumb_height'] = isset($settings['thumb_height']) ? $settings['thumb_height'] : $this->config->get('config_image_additional_height');

                if (isset($settings['show_watermark'])) {
                    $watermark = !empty($settings['watermark_file']) ? $settings['watermark_file'] : $this->config->get('config_logo');
                    NTImage::setWatermark($watermark);
                }

                $image = isset($product_info['image']) && !empty($product_info['image']) ? $product_info['image'] : 'no_image.jpg';
                
                $imgProduct = array(
                    'popup' => NTImage::resizeAndSave($image, $settings['popup_width'], $settings['popup_height']),
                    'preview' => NTImage::resizeAndSave($image, $settings['preview_width'], $settings['preview_height']),
                    'thumb' => NTImage::resizeAndSave($image, $settings['thumb_width'], $settings['thumb_height'])
                );

                $images = $this->modelProduct->getProductImages($results[0]['product_id']);
                $imgs = [];
                foreach ($images as $j => $image) {
                    $imgs[$j] = array(
                        'popup' => NTImage::resizeAndSave($image['image'], $settings['popup_width'], $settings['popup_height']),
                        'preview' => NTImage::resizeAndSave($image['image'], $settings['preview_width'], $settings['preview_height']),
                        'thumb' => NTImage::resizeAndSave($image['image'], $settings['thumb_width'], $settings['thumb_height'])
                    );
                }

                array_push($imgs, $imgProduct);
                $this->data['images'] = array_reverse($imgs);
            }

            return [
                'widget'   => $widget,
                'render'   => $render,
                'settings' => $settings,
            ];
        });
    }
}