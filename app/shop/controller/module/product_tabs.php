<?php

require_once(DIR_CONTROLLER . "module/modulecontroller.php");

class ControllerModuleProductTabs extends ControllerModuleModuleController
{
    protected string $moduleName = 'product_tabs';
    protected array $defaults = [];

    public function init()
    {
        $this->addFilter("module:settings", function ($data) {
            $settings = $data['settings'];
            $widget   = $data['widget'];
            $render   = $data['render'];

            $query_data = [];

            $query_data['limit'] = ((int) $this->data['settings']['limit']) ? (int) $this->data['settings']['limit'] : 24;

            foreach ($settings['tabs'] as $k => $tab) {
                $query_data['category_id'] = (!empty($tab['categories'])) ? $tab['categories'] : null;
                $query_data['manufacturer_id'] = (!empty($tab['manufacturers'])) ? $tab['manufacturers'] : null;
                $this->data['tabs'][$k]['name'] = $tab['name'];

                if (!$tab['view'] || !in_array($tab['view'], array('list', 'grid', 'carousel', 'slider')))
                $tab['view'] = 'carousel';

                $this->data['tabs'][$k]['view'] = $tab['view'];

                if (!isset($loaded[$tab['view']])) {
                    $loaded[$tab['view']] = true;
                    $filename = str_replace('controller', '', strtolower(__CLASS__)) . $tab['view'];
                    $route = 'module/' . str_replace('controllermodule', '', strtolower(__CLASS__)) . '/' . $tab['view'];
                    $this->loadDeps($route);
                    $this->loadWidgetAssets($filename);
                }

                $func = $tab['module'];
                if (!$func || !in_array($func, array('random', 'latest', 'featured', 'bestseller', 'recommended', 'related', 'popular', 'special'))) $func = 'random';

                $this->data['tabs'][$k]['products'] = $this->prefetch($query_data, $func);
            }

            return [
                'widget'   => $widget,
                'render'   => $render,
                'settings' => $settings,
            ];
        });
    }

    protected function prefetch($data, $func = 'random') {
        $Url = new Url($this->registry);

        $this->load->model('store/product');

        switch ($func) {
            case 'random':
            default:
                $results = $this->modelProduct->getRandomProducts($data);
                break;
            case 'latest':
                $results = $this->modelProduct->getLatestProducts($data);
                break;
            case 'featured':
                $results = $this->modelProduct->getFeaturedProducts($data);
                break;
            case 'bestseller':
                $results = $this->modelProduct->getBestSellerProducts($data);
                break;
            case 'recommended':
                $results = $this->modelProduct->getRecommendedProducts($data);
                break;
            case 'related':
                $results = $this->modelProduct->getProductRelated($this->request->getQuery('product_id'), $data);
                break;
            case 'popular':
                $results = $this->modelProduct->getPopularProducts($data);
                break;
            case 'special':
                $data['sort'] = 'td.title';
                $data['order'] = 'ASC';
                $results = $this->modelProduct->getProductSpecials($data);
                break;
        }

        $this->load->auto('store/review');

        $products = [];

        list($dia, $mes, $ano) = explode('-', date('d-m-Y'));
        $l = ((int) $this->config->get('config_new_days') > 30) ? 30 : $this->config->get('config_new_days');
        if (($dia = $dia - $l) <= 0) {
            $dia = $dia + 30;
            if ($dia <= 0)
                $dia = 1;
            $mes = $mes - 1;
            if ($mes <= 0) {
                $mes = $mes + 12;
                $ano = $ano - 1;
            }
        }
        
        foreach ($results as $k => $result) {
            $image = $imageP = !empty($result['image']) ? $result['image'] : 'no_image.jpg';

            if ($this->config->get('config_review')) {
                $rating = $this->modelReview->getAverageRating($result['product_id']);
            } else {
                $rating = false;
            }

            $options = $this->modelProduct->getProductOptions($result['product_id']);

            if ($options) {
                $add = $Url::createUrl('store/product', array('product_id' => $result['product_id']));
            } else {
                $add = $Url::createUrl('checkout/cart', array('product_id' => $result['product_id']));
            }

            list($pdia, $pmes, $pano) = explode('-', date('d-m-Y', strtotime($result['created'])));

            $this->load->auto('image');
            $products[$k] = array(
                'product_id' => $result['product_id'],
                'name' => $result['name'],
                'model' => $result['model'],
                'overview' => $result['meta_description'],
                'rating' => $rating,
                'stars' => sprintf($this->language->get('text_stars'), $rating),
                'options' => $options,
                'image' => NTImage::resizeAndSave($image, 38, 38),
                'lazyImage' => NTImage::resizeAndSave('no_image.jpg', $this->config->get('config_image_product_width'), $this->config->get('config_image_product_height')),
                'thumb' => NTImage::resizeAndSave($image, $this->config->get('config_image_product_width'), $this->config->get('config_image_product_height')),
                'href' => $Url::createUrl('store/product', array('product_id' => $result['product_id'])),
                'add' => $add,
                'created' => $result['created']
            );
            
            if ($this->config->get('config_store_mode') === 'store' && (!$this->config->get('config_customer_price') || $this->customer->isLogged())) {
                $discounts = $this->modelProduct->getProductDiscounts($result['product_id']);
                if ($discounts) {
                    $products[$k]['discounts'] = [];
                    foreach ($discounts as $discount) {
                        $products[$k]['discounts'][] = array(
                            'quantity' => $discount['quantity'],
                            'price' => $this->currency->format($this->tax->calculate($discount['price'], $result['tax_class_id'], $this->config->get('config_tax')))
                        );
                    }
                }

                $special = false;
                $discount = $this->modelProduct->getProductDiscount($result['product_id']);
                
                if ($discount) {
                    $price = $this->currency->format($this->tax->calculate($discount, $result['tax_class_id'], $this->config->get('config_tax')));
                } else {
                    $price = $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax')));
                    $special = $this->modelProduct->getProductSpecial($result['product_id']);
                    if ($special) {
                        $special = $this->currency->format($this->tax->calculate($special, $result['tax_class_id'], $this->config->get('config_tax')));
                    }
                }
                
                $products[$k]['price'] = $price;
                $products[$k]['special'] = $special;
                
                if ($special) {
                    $sticker = '<b class="oferta"></b>';
                } elseif ($discount) {
                    $sticker = '<b class="descuento"></b>';
                } elseif (strtotime($dia . "-" . $mes . "-" . $ano) <= strtotime($pdia . "-" . $pmes . "-" . $pano)) {
                    $sticker = '<b class="nuevo"></b>';
                } else {
                    $sticker = "";
                }
                $products[$k]['sticker'] = $sticker;
            }
            
            $products[$k]['images'] = [];
            $images = $this->modelProduct->getProductImages($result['product_id']);
            foreach ($images as $j => $image) {
                $products[$k]['images'][$j] = array(
                    'popup' => NTImage::resizeAndSave($image['image'], $this->config->get('config_image_popup_width'), $this->config->get('config_image_popup_height')),
                    'preview' => NTImage::resizeAndSave($image['image'], $this->config->get('config_image_thumb_width'), $this->config->get('config_image_thumb_height')),
                    'thumb' => NTImage::resizeAndSave($image['image'], $this->config->get('config_image_additional_width'), $this->config->get('config_image_additional_height'))
                );
            }
            $j = count($products[$k]['images']) + 1;
            $products[$k]['images'][$j] = array(
                'popup' => NTImage::resizeAndSave($imageP, $this->config->get('config_image_popup_width'), $this->config->get('config_image_popup_height')),
                'preview' => NTImage::resizeAndSave($imageP, $this->config->get('config_image_thumb_width'), $this->config->get('config_image_thumb_height')),
                'thumb' => NTImage::resizeAndSave($imageP, $this->config->get('config_image_additional_width'), $this->config->get('config_image_additional_height'))
            );
            $products[$k]['images'] = array_reverse($products[$k]['images']);
        }

        if (!$this->config->get('config_customer_price') || $this->customer->isLogged()) {
            $this->data['display_price'] = true;
        } else {
            $this->data['display_price'] = false;
        }
        return $products;
    }
}