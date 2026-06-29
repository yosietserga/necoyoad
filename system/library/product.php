<?php

final class Product {
    private $data = [];

    public function __construct($registry) {
        $this->registry = $registry;
        $this->load = $registry->get('load');
        $this->db = $registry->get('db');
        $this->config = $registry->get('config');
        $this->language = $registry->get('language');
        $this->currency = $registry->get('currency');
        $this->tax = $registry->get('tax');
        $this->cache = $registry->get('cache');
        $this->request = $registry->get('request');
        $this->customer = $registry->get('customer');

        $this->load->auto('store/product');
        $this->load->auto('store/review');
        $this->load->auto('store/attribute');

        $this->modelProduct = $registry->get('modelProduct');
        $this->modelReview = $registry->get('modelReview');
        $this->modelAttribute = $registry->get('modelAttribute');
    }

    public function getProductsArray($results, $renderFull = null, $prefix = "") {
        $Url = new Url($this->registry);

        $products = [];

        list($dia, $mes, $ano) = explode('-', date('d-m-Y'));
        $l = ((int)$this->config->get('config_new_days') > 30) ? 30 : $this->config->get('config_new_days');
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
                $add = $Url::createUrl('checkout/cart') . '?product_id=' . $result['product_id'];
            }

            if ($this->config->get('config_store_mode') === 'store' && $this->config->get('config_customer_price')) {
                $discounts = $this->modelProduct->getProductDiscounts($result['product_id']);
                if ($discounts) {
                    $products[$k]['discounts'] = [];
                    foreach ($discounts as $discount) {
                        $products[$k]['discounts'][] = array(
                            'quantity' => $discount['quantity'],
                            'price' => $this->currency->format($this->tax->calculate($discount['price'], $result['tax_class_id'], $this->config->get('config_tax')))
                        );
                    }
                    $products[$k]['discounts'] = $discounts;
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
            }

            list($pdia, $pmes, $pano) = explode('-', date('d-m-Y', strtotime($result['created'])));

            if (isset($special)) {
                $sticker = '<b class="oferta"></b>';
            } elseif (isset($discount)) {
                $sticker = '<b class="descuento"></b>';
            } elseif (strtotime($dia . "-" . $mes . "-" . $ano) <= strtotime($pdia . "-" . $pmes . "-" . $pano)) {
                $sticker = '<b class="nuevo"></b>';
            } else {
                $sticker = "";
            }

            $this->load->auto('image');
            if ($this->config->get('config_show_watermark')) {
                $watermark = $this->config->get('config_watermark_file');
                $watermark = !empty($watermark) ? $watermark : $this->config->get('config_logo');
                NTImage::setWatermark($watermark);
            }
            $products[$k] = array(
                'product_id'=> $result['product_id'],
                'name'      => $result['name'],
                'model'     => $result['model'],
                'overview'  => $result['meta_description'],
                'rating'    => $rating,
                'stars'     => sprintf($this->language->get('text_stars'), $rating),
                'sticker'   => $sticker,
                'options'   => $options,
                'image'     => NTImage::resizeAndSave($image, 38, 38),
                'lazyImage' => NTImage::resizeAndSave('no_image.jpg', $this->config->get('config_image_product_width'), $this->config->get('config_image_product_height')),
                'thumb'     => NTImage::resizeAndSave($image, $this->config->get('config_image_product_width'), $this->config->get('config_image_product_height')),
                'href'      => $Url::createUrl('store/product', array('product_id' => $result['product_id'])),
                'add'       => $add,
                'attributes'=> isset($renderFull) ? $this->getAttributes($result['product_id']) : null,
                'images'    => $this->getImages($result['product_id'], $imageP),
                'created'   => $result['created']
            );

        }

        return $products;
    }

    public function getImages($id, $imageP = null) {
        $this->load->auto('image');

        $images = $this->modelProduct->getProductImages($id);
        $imgs = [];
        foreach ($images as $j => $image) {
            $imgs[$j] = array(
                'popup'   => NTImage::resizeAndSave($image['image'], $this->config->get('config_image_popup_width'), $this->config->get('config_image_popup_height')),
                'preview' => NTImage::resizeAndSave($image['image'], $this->config->get('config_image_thumb_width'), $this->config->get('config_image_thumb_height')),
                'thumb'   => NTImage::resizeAndSave($image['image'], $this->config->get('config_image_additional_width'), $this->config->get('config_image_additional_height'))
            );
        }
        if ($imageP) {
            $j = count($imgs) + 1;
            $imgs[$j] = array(
                'popup'   => NTImage::resizeAndSave($imageP, $this->config->get('config_image_popup_width'), $this->config->get('config_image_popup_height')),
                'preview' => NTImage::resizeAndSave($imageP, $this->config->get('config_image_thumb_width'), $this->config->get('config_image_thumb_height')),
                'thumb'   => NTImage::resizeAndSave($imageP, $this->config->get('config_image_additional_width'), $this->config->get('config_image_additional_height'))
            );
            $imgs = array_reverse($imgs);
        }
        return $imgs;
    }

    public function getAttributes($id) {
        if (!(int)$id) return [];
        $attributes = [];
        $attrValues = [];
        $_attributes = $this->modelProduct->getAllProperties($id, 'attribute') ?? [];
        foreach ($_attributes as $attribute) {
            list($name, $attribute_id, $attribute_group_id) = explode(':', $attribute['key']);
            $attrValues[$attribute_group_id][$attribute_id] = $attribute['value'];
        }

        foreach ($attrValues as $k => $attr) {
            $rows = $this->modelAttribute->getAll(array(
                'product_attribute_group_id'=>$k
            ));

            $attributes[$k] = $rows[0];
            $attributes[$k]['categoriesAttributes'] = array_unique($this->modelProduct->getCategoriesByAttributeGroupId($k));

            foreach ($attributes[$k]['attributes'] as $key => $item) {
                $attributes[$k]['attributes'][$key]['value'] = ($attr[$item['product_attribute_id']]) ? $attr[$item['product_attribute_id']] : $item['default'];
            }
        }
        return $attributes;
        /* /product attributes */
    }

    public function __set($key, $value) {
        $this->data[$key] = $value;
    }

    public function __get($key) {
        return $this->data[$key];
    }

    public function __isset($key) {
        return isset($this->data[$key]);
    }

}
