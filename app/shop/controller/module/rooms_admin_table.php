<?php

require_once(DIR_CONTROLLER . "module/modulecontroller.php");

class ControllerModuleRoomsAdminTable extends ControllerModuleModuleController
{
    protected string $moduleName = 'rooms_admin_table';

    public function init()
    {
        $this->addFilter("module:settings", function ($data) {
            $settings = $data['settings'];
            $widget   = $data['widget'];
            $render   = $data['render'];

            $query_data = [];

            $Url = new Url($this->registry);
            $url = $Url::createUrl("rooms/account");

            if (isset($settings['dynamic']) && ($this->request->hasQuery('manufacturer_id') || $this->request->hasPost('manufacturer_id'))) {
                $data['manufacturer_id'] = $this->request->hasPost('manufacturer_id') ? $this->request->getPost('manufacturer_id') : $this->request->getQuery('manufacturer_id');
                $url = $Url::createUrl("store/manufacturer", array('manufacturer_id' => $data['manufacturer_id']));
            } else {
                $data['manufacturer_id'] = (!empty($settings['manufacturers'])) ? $settings['manufacturers'] : null;
            }

            if (isset($settings['dynamic']) && ($this->request->hasQuery('category_id') || $this->request->hasPost('category_id'))) {
                $data['category_id'] = $this->request->hasPost('category_id') ? $this->request->getPost('category_id') : $this->request->getQuery('category_id');
                $url = $Url::createUrl("store/category", array('category_id' => $data['category_id']));
            } else {
                $data['category_id'] = (!empty($settings['categories'])) ? $settings['categories'] : null;
            }

            $query_data['limit'] = $this->request->hasQuery('limit') ?
                $this->request->getQuery('limit') : 
                (
                    (
                        isset($settings['limit']) && (int)$settings['limit'] ? 
                            (int)$settings['limit'] : 
                            (
                                (int)$this->config->get('config_catalog_limit') ? (int)$this->config->get('config_catalog_limit') : 24
                            )
                    )
                );

            $query_data['product_type'] = "room";

            $query_data['product_id'] = $this->request->hasPost('product_id') ?
                $this->request->getPost('product_id') :
                ($this->request->hasQuery('product_id') ? $this->request->getQuery('product_id') : null);
            
            $query_data['page'] = $this->request->hasQuery('page') ? $this->request->getQuery('page') : 1;
            $query_data['start'] = ($query_data['page'] - 1) * $query_data['limit'];
            
            $query_data['image_popup_width']  = (!empty($settings['image_popup_width'])) ? $settings['image_popup_width'] : $this->config->get('config_image_popup_width');
            $query_data['image_popup_height'] = (!empty($settings['image_popup_height'])) ? $settings['image_popup_height'] : $this->config->get('config_image_popup_height');
            $query_data['image_thumb_width']  = (!empty($settings['image_thumb_width'])) ? $settings['image_thumb_width'] : $this->config->get('config_image_thumb_width');
            $query_data['image_thumb_height'] = (!empty($settings['image_thumb_height'])) ? $settings['image_thumb_height'] : $this->config->get('config_image_thumb_height');

            $this->data['rooms'] = [];

            $func = $settings['module'];
            if (!$func || !in_array($func, array('random', 'latest', 'featured', 'recommended', 'related', 'popular'))) $func = 'random';
            
            $this->prefetch($query_data, $settings, $func);

            if (isset($settings['show_pagination']) && $settings['show_pagination'] && $this->data['total_posts']) {
                if (!is_callable('Pagination')) $this->load->library('pagination');
                $pagination = new Pagination(true);
                $pagination->total = $this->data['total_products'];
                $pagination->text = $this->language->get('text_pagination');

                if ($this->cache->get('url_products_searched') && $settings['show_search_results']) {
                    $pagination->page = $this->data['criteria']['page'];
                    $pagination->limit = $this->data['criteria']['limit'];
                    $pagination->url = $this->cache->get('url_products_searched');
                } else {
                    $pagination->page = $data['page'];
                    $pagination->limit = $data['limit'];
                    $pagination->url = $url . '&page={page}';
                }

                if ($settings['endless_scroll']) {
                    $pagination->ajax = true;
                    $pagination->ajaxTarget = isset($settings['endless_scroll_target']) ? $settings['endless_scroll_target'] : "#{$widget['name']}_results";
                }

                $this->data['pagination'] = $pagination->render();
            }

            return [
                'widget'   => $widget,
                'render'   => $render,
                'settings' => $settings,
            ];
        });
    }

    protected function prefetch($data, $settings, $func = 'random') {
        $p = $this->cache->get('products_searched');
        if ($p && $settings['show_search_results']) {
            $results = $p;
            $this->data['total_products'] = $this->cache->get('total_products_searched');
            $this->data['criteria'] = $this->cache->get('criteria_products_searched');
        } else {

            $this->load->model('store/product');
            $results = [];
            switch ($func) {
                case 'random':
                default:
                    $results = $this->modelProduct->getRandomProducts($data);
                    if (isset($settings['show_pagination']) && $settings['show_pagination'])
                        $this->data['total_products'] = $this->modelProduct->getAllTotal($data);
                    break;
                case 'latest':
                    $results = $this->modelProduct->getLatestProducts($data);
                    if (isset($settings['show_pagination']) && $settings['show_pagination'])
                        $this->data['total_products'] = $this->modelProduct->getAllTotal($data);
                    break;
                case 'featured':
                    $results = $this->modelProduct->getFeaturedProducts($data);
                    if (isset($settings['show_pagination']) && $settings['show_pagination'])
                        $this->data['total_products'] = $this->modelProduct->getTotalFeaturedProducts($data);
                    break;
                case 'bestseller':
                    $results = $this->modelProduct->getBestSellerProducts($data);
                    if (isset($settings['show_pagination']) && $settings['show_pagination'])
                        $this->data['total_products'] = $this->modelProduct->getTotalBestSellerProducts($data);
                    break;
                case 'recommended':
                    $results = $this->modelProduct->getRecommendedProducts($data);
                    if (isset($settings['show_pagination']) && $settings['show_pagination'])
                        $this->data['total_products'] = $this->modelProduct->getTotalRecommendedProducts($data);
                    break;
                case 'related':
                    $results = $this->modelProduct->getProductRelated($this->request->getQuery('product_id'), $data);
                    if (isset($settings['show_pagination']) && $settings['show_pagination'])
                        $this->data['total_products'] = $this->modelProduct->getTotalProductRelated($this->request->getQuery('product_id'), $data);
                    break;
                case 'popular':
                    $results = $this->modelProduct->getPopularProducts($data);
                    if (isset($settings['show_pagination']) && $settings['show_pagination'])
                        $this->data['total_products'] = $this->modelProduct->getAllTotal($data);
                    break;
                case 'special':
                    $data['sort'] = 'pd.name';
                    $data['order'] = 'ASC';
                    $results = $this->modelProduct->getProductSpecials($data);
                    if (isset($settings['show_pagination']) && $settings['show_pagination'])
                        $this->data['total_products'] = $this->modelProduct->getAllTotal($data);
                    break;
            }
        }
        $this->load->library('product');
        $Product = new Product($this->registry);
        $this->data['rooms'] = $Product->getProductsArray($results, true);

        if (!$this->config->get('config_customer_price') || $this->customer->isLogged()) {
            $this->data['display_price'] = true;
        } else {
            $this->data['display_price'] = false;
        }
    }
}