<?php

class ControllerStoreProduct extends Controller {

    public $product_id;

    public function index() {
        $Url = new Url($this->registry);
        $this->product_id = $product_id = $this->request->hasQuery('product_id') ? $this->request->getQuery('product_id') : 0;
        if (!(int)$this->product_id) {
            return $this->error404();
        }
        $product_info = $this->modelProduct->getById($this->product_id);
        $this->object_id = $product_id;
        
        $this->session->clear('object_type');
        $this->session->clear('object_id');
        $this->session->clear('landing_page');

        $this->session->set('object_type', 'product');
        $this->session->set('object_id', $product_id);

        $cacheId = 'html-product.' .
            $product_id .
            $this->config->get('config_language_id') . "." .
            $this->request->getQuery('hl') . "." .
            $this->request->getQuery('cc') . "." .
            $this->customer->getId() . "." .
            $this->config->get('config_currency') . "." .
            (int) $this->config->get('config_store_id');

        if ($product_info) {
            //tracker
            $this->tracker->track($product_info['product_id'], 'product');

            if ($this->session->has('ref_email') && !$this->session->has('ref_cid')) {
                $this->data['show_register_form_invitation'] = true;
            }
            
            $customerGroups = $this->modelProduct->getProperty($product_id, 'customer_groups', 'customer_groups');
            if (
                is_array($customerGroups) && 
                (
                    ($this->customer->isLogged() && in_array($this->customer->getCustomerGroupId(), $customerGroups)) 
                    || in_array(0, $customerGroups)
                )
            ) {
                $cached = $this->cache->get($cacheId);
                $this->load->library('user');
                if ($cached && (!$this->user->isLogged() || $this->request->hasQuery('np'))) {
                    $this->response->setOutput($cached, $this->config->get('config_compression'));
                } else {
                    //Languages
                    $this->language->load('store/product');

                    //Models
                    $this->load->auto('store/product');
                    $this->load->auto('store/category');
                    $this->load->auto('store/manufacturer');

                    $this->document->breadcrumbs = [];
                    $this->document->breadcrumbs[] = array(
                        'href' => $Url::createUrl('common/home'),
                        'text' => $this->language->get('text_home'),
                        'separator' => false
                    );

                    if (isset($this->request->get['path'])) {
                        $path = '';
                        foreach (explode('_', $this->request->get['path']) as $path_id) {
                            $category_info = $this->modelCategory->getById($path_id);
                            $path .= (!$path) ? $path_id : '_' . $path_id;
                            if ($category_info) {
                                $this->document->breadcrumbs[] = array(
                                    'href' => $Url::createUrl('store/category', array('path' => $path)),
                                    'text' => $category_info['name'],
                                    'separator' => $this->language->get('text_separator')
                                );
                            }
                        }
                    }

                    if (isset($this->request->get['manufacturer_id'])) {
                        $manufacturer_info = $this->modelManufacturer->getById($this->request->get['manufacturer_id']);
                        if ($manufacturer_info) {
                            $this->document->breadcrumbs[] = array(
                                'href' => $Url::createUrl('store/manufacturer', array('manufacturer_id' => $this->request->get['manufacturer_id'])),
                                'text' => $manufacturer_info['name'],
                                'separator' => $this->language->get('text_separator')
                            );
                        }
                    }

                    if (isset($this->request->get['keyword'])) {
                        $url = '';
                        if (isset($this->request->get['category_id'])) {
                            $url .= '&category_id=' . $this->request->get['category_id'];
                        }
                        if (isset($this->request->get['description'])) {
                            $url .= '&description=' . $this->request->get['description'];
                        }
                        $this->document->breadcrumbs[] = array(
                            'href' => $Url::createUrl('store/search', '&keyword=' . $this->request->get['keyword'] . $url),
                            'text' => $this->language->get('text_search'),
                            'separator' => $this->language->get('text_separator')
                        );
                    }

                    $url = '';
                    if (isset($this->request->get['path'])) {
                        $url .= '&path=' . $this->request->get['path'];
                    }
                    if (isset($this->request->get['manufacturer_id'])) {
                        $url .= '&manufacturer_id=' . $this->request->get['manufacturer_id'];
                    }
                    if (isset($this->request->get['keyword'])) {
                        $url .= '&keyword=' . $this->request->get['keyword'];
                    }
                    if (isset($this->request->get['category_id'])) {
                        $url .= '&category_id=' . $this->request->get['category_id'];
                    }
                    if (isset($this->request->get['description'])) {
                        $url .= '&description=' . $this->request->get['description'];
                    }
                    $this->document->breadcrumbs[] = array(
                        'href' => $Url::createUrl('store/product', $url . '&product_id=' . $product_id),
                        'text' => $product_info['name'],
                        'separator' => $this->language->get('text_separator')
                    );

                    $this->data['breadcrumbs'] = $this->document->breadcrumbs;

                    $this->document->title = $product_info['name'];
                    $this->document->keywords = $product_info['meta_keywords'];
                    $this->document->description = $product_info['meta_description'];
                    $this->document->links = [];
                    $this->document->links[] = array(
                        'href' => $Url::createUrl('store/product', array('product_id' => $product_id)),
                        'rel' => 'canonical'
                    );

                    $this->data['heading_title'] = $product_info['name'];

                    $this->data['redirect'] = $Url::createUrl('store/product', $url . '&product_id=' . $product_id);

                    $this->session->set('promote_product_id', $this->product_id);
                    $this->session->set('redirect', $this->data['redirect']);

                    $this->data['product_info'] = $product_info;
                    $this->data['product_id'] = $product_id;

                    $this->modelProduct->updateStats($this->request->getQuery('product_id'), (int) $this->customer->getId());

                    $this->session->set('landing_page','store/product/index');
                    $this->loadWidgets('featuredContent');
                    $this->loadWidgets('main');
                    $this->loadWidgets('featuredFooter');

                    $this->addChild('common/column_left');
                    $this->addChild('common/column_right');
                    $this->addChild('common/header');
                    $this->addChild('common/footer');

                    if (!$this->user->isLogged()) {
                        $this->cacheId = $cacheId;
                    }

                    $template = $this->modelProduct->getProperty($product_id, 'style', 'view');
                    $default_template = ($this->config->get('default_view_product')) ? $this->config->get('default_view_product') : 'store/product.tpl';
                    $template = empty($template) ? $default_template : $template;
                    if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/' . $template)) {
                        $this->template = $this->config->get('config_template') . '/' . $template;
                    } else {
                        $this->template = 'choroni/' . $template;
                    }

                    $this->response->setOutput($this->render(true), $this->config->get('config_compression'));
                }
            } else {
                $this->error404();
            }
        } else {
            $this->error404();
        }
    }

    protected function error404() {
        $Url = new Url($this->registry);
        $url = '';
        if (isset($this->request->get['path'])) {
            $url .= '&path=' . $this->request->get['path'];
        }
        if (isset($this->request->get['manufacturer_id'])) {
            $url .= '&manufacturer_id=' . $this->request->get['manufacturer_id'];
        }
        if (isset($this->request->get['keyword'])) {
            $url .= '&keyword=' . $this->request->get['keyword'];
        }
        if (isset($this->request->get['category_id'])) {
            $url .= '&category_id=' . $this->request->get['category_id'];
        }
        if (isset($this->request->get['description'])) {
            $url .= '&description=' . $this->request->get['description'];
        }

        $this->document->breadcrumbs[] = array(
            'href' => $Url::createUrl('store/product', $url . '&product_id=' . $this->request->getQuery('product_id')),
            'text' => $this->language->get('text_error'),
            'separator' => $this->language->get('text_separator')
        );
        $this->data['breadcrumbs'] = $this->document->breadcrumbs;
        $this->document->title = $this->data['heading_title'] = $this->language->get('text_error');
        $this->data['continue'] = $Url::createUrl('common/home');

        $this->session->set('landing_page','store/product/error404');
        $this->loadWidgets('featuredContent');
        $this->loadWidgets('main');
        $this->loadWidgets('featuredFooter');

            $this->addChild('common/column_left');
            $this->addChild('common/column_right');
            $this->addChild('common/header');
            $this->addChild('common/footer');

        $template = ($this->config->get('default_view_product_error')) ? $this->config->get('default_view_product_error') : 'error/not_found.tpl';
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/' . $template)) {
            $this->template = $this->config->get('config_template') . '/' . $template;
        } else {
            $this->template = 'choroni/' . $template;
        }

        $this->response->setOutput($this->render(true), $this->config->get('config_compression'));
    }

    public function all() {
        $cacheId = 'html-products.' .
            serialize($this->request->get).
            $this->config->get('config_language_id') . "." .
            $this->request->getQuery('hl') . "." .
            $this->request->getQuery('cc') . "." .
            $this->customer->getId() . "." .
            $this->config->get('config_currency') . "." .
            (int) $this->config->get('config_store_id');

        $cached = $this->cache->get($cacheId);
        $this->load->library('user');
        if ($cached && !$this->user->isLogged()) {
            $this->response->setOutput($cached, $this->config->get('config_compression'));
        } else {
            $Url = new Url($this->registry);
            $this->language->load('store/product');
            $this->document->title = $this->language->get('heading_title');

            $this->session->clear('object_type');
            $this->session->clear('object_id');
            $this->session->clear('landing_page');

            $this->document->breadcrumbs = [];

            $this->document->breadcrumbs[] = array(
                'href' => $Url::createUrl("common/home"),
                'text' => $this->language->get('text_home'),
                'separator' => false
            );
            $this->document->breadcrumbs[] = array(
                'href' => $Url::createUrl("store/product/all"),
                'text' => $this->language->get('text_products'),
                'separator' => false
            );
            $this->data['breadcrumbs'] = $this->document->breadcrumbs;

            $this->session->set('landing_page', 'store/product/all');
            $this->loadWidgets('featuredContent');
            $this->loadWidgets('main');
            $this->loadWidgets('featuredFooter');

            $this->addChild('common/column_left');
            $this->addChild('common/column_right');
            $this->addChild('common/header');
            $this->addChild('common/footer');

            if (!$this->user->isLogged()) {
                $this->cacheId = $cacheId;
            }

            $template = ($this->config->get('default_view_product_all')) ? $this->config->get('default_view_product_all') : 'store/products_all.tpl';
            if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/' . $template)) {
                $this->template = $this->config->get('config_template') . '/' . $template;
            } else {
                $this->template = 'choroni/' . $template;
            }

            $this->response->setOutput($this->render(true), $this->config->get('config_compression'));
        }
    }

    //TODO: crear servicio para comparar productos dentro de library
    public function addProductToCompare() {
        $Url = new Url($this->registry);
        $this->load->auto('store/product');
        $this->load->auto('json');
        $json = [];
        $product_id = ($this->request->hasPost('product_id')) ? $this->request->getPost('product_id') : $this->request->getQuery('product_id');
        $result = $this->modelProduct->getProduct($product_id);
        if (is_numeric($product_id) && $product_id && $result) {
            $compare = $this->session->get('products_to_compare');
            if ($compare && !in_array($product_id, $compare)) {
                array_push($compare, $product_id);
            } else {
                $compare = array($product_id);
            }
            $this->session->set('products_to_compare', $compare);
        } else {
            $json['error'] = 1;
            $json['message'] = $this->language->get('error_product_not_exists');
        }
        $this->response->setOutput(Json::encode($json), $this->config->get('config_compression'));
    }
    
    //TODO: crear servicio para comparar productos dentro de library
    public function removeProductToCompare() {
        $this->load->auto('store/product');
        $this->load->auto('json');
        $json = [];
        $product_id = ($this->request->hasPost('product_id')) ? $this->request->getPost('product_id') : $this->request->getQuery('product_id');
        
        $compare = $this->session->get('products_to_compare');
        if ($compare) {
            if(($key = array_search($product_id, $compare)) !== false) {
                unset($compare[$key]);
            }
        }
    }

    //TODO: crear servicio para comparar productos dentro de library
    public function getProductsToCompare() {
        $this->load->auto('store/product');
        $this->load->auto('json');
        $json = [];
        
        $results = $this->modelProduct->getProductsToCompare(array_unique($this->session->get('products_to_compare')));

        $this->load->library('product');
        $Product = new Product($this->registry);
        $this->data['products'] = $Product->getProductsArray($results, true);

        $json['results'] = $this->data['products'];
        $json['display_price'] = $this->data['display_price'];
        
        $this->response->setOutput(Json::encode($json), $this->config->get('config_compression'));
    }

    public function relatedJson() {
        $json = [];
        $this->load->auto("store/product");
        $this->load->auto('image');
        $this->load->auto('json');
        $Url = new Url($this->registry);

        $json['results'] = $this->modelProduct->getProductRelated($this->request->get['product_id']);
        $width = isset($_GET['width']) ? $_GET['width'] : 80;
        $height = isset($_GET['height']) ? $_GET['height'] : 80;
        foreach ($json['results'] as $k => $v) {
            if (!file_exists(DIR_IMAGE . $v['image']))
                $json['results'][$k]['image'] = HTTP_IMAGE . "no_image.jpg";
            $json['results'][$k]['thumb'] = NTImage::resizeAndSave($v['image'], $width, $height);
            $json['results'][$k]['price'] = $this->currency->format($this->tax->calculate($v['price'], $v['tax_class_id'], $this->config->get('config_tax')));
            $json['results'][$k]['href'] = $Url::createUrl('store/product', array('product_id' => $v['product_id']));

            $json['results'][$k]['images'] = [];
            $images = $this->modelProduct->getProductImages($v['product_id']);
            foreach ($images as $j => $image) {
                $json['results'][$k]['images'][$j] = array(
                    'popup' => NTImage::resizeAndSave($image['image'], $width, $height),
                    'preview' => NTImage::resizeAndSave($image['image'], $width, $height),
                    'thumb' => NTImage::resizeAndSave($image['image'], $width, $height)
                );
            }
            $j = count($json['results'][$k]['images']) + 1;
            $json['results'][$k]['images'][$j] = array(
                'popup' => NTImage::resizeAndSave($v['image'], $width, $height),
                'preview' => NTImage::resizeAndSave($v['image'], $width, $height),
                'thumb' => NTImage::resizeAndSave($v['image'], $width, $height)
            );
            $json['results'][$k]['images'] = array_reverse($json['results'][$k]['images']);
            
            $json['results'][$k]['attributes'] = $this->getAttributes($v['product_id']);
        }

        if (!count($json['results']))
            $json['error'] = 1;

        $this->response->setOutput(Json::encode($json), $this->config->get('config_compression'));
    }

    public function quickViewJson() {
        $this->load->auto('store/product');

        $this->product_id = $product_id = isset($this->request->get['product_id']) ? (int) $this->request->get['product_id'] : $product_id = 0;
        $product_info = $this->modelProduct->getProduct($product_id);

        $this->object_id = $product_id;
        
        $this->session->clear('object_type');
        $this->session->clear('object_id');
        $this->session->clear('landing_page');

        $this->session->set('object_type', 'product');
        $this->session->set('object_id', $product_id);

        $cacheId = 'json-product.' .
            $product_id .
            $this->config->get('config_language_id') . "." .
            $this->request->getQuery('hl') . "." .
            $this->request->getQuery('cc') . "." .
            $this->customer->getId() . "." .
            $this->config->get('config_currency') . "." .
            (int) $this->config->get('config_store_id');

        if ($product_info) {
            $this->tracker->track($product_info['product_id'], 'product');

            $customerGroups = $this->modelProduct->getProperty($product_id, 'customer_groups', 'customer_groups');
            if (
                ($this->customer->isLogged() && in_array($this->customer->getCustomerGroupId(), $customerGroups)) 
                || in_array(0, $customerGroups)
            ) {

                $cached = $this->cache->get($cacheId);
                $this->load->library('user');
                if ($cached && !$this->user->isLogged()) {
                    $this->response->setOutput($cached, $this->config->get('config_compression'));
                } else {
                    $this->load->auto('store/category');
                    $this->load->auto('store/manufacturer');
                    $this->load->auto('tool/image');
                    $this->load->auto('store/review');
                    $this->load->auto('currency');
                    $this->load->auto('tax');
                    $this->load->auto('json');
                    $this->load->auto('url');
                    $this->language->load('store/product');

                    $Url = new Url($this->registry);
                    $average = ($this->config->get('config_review')) ? $this->modelReview->getAverageRating($product_id) : false;

                    $image = isset($product_info['image']) ? $product_info['image'] : $image = 'no_image.jpg';
                    $this->data['popup'] = NTImage::resizeAndSave($image, $this->config->get('config_image_popup_width'), $this->config->get('config_image_popup_height'));
                    $this->data['thumb'] = NTImage::resizeAndSave($image, $this->config->get('config_image_thumb_width'), $this->config->get('config_image_thumb_height'));

                    $imgProduct = array(
                        'popup' => NTImage::resizeAndSave($image, $this->config->get('config_image_popup_width'), $this->config->get('config_image_popup_height')),
                        'preview' => NTImage::resizeAndSave($image, $this->config->get('config_image_thumb_width'), $this->config->get('config_image_thumb_height')),
                        'thumb' => NTImage::resizeAndSave($image, $this->config->get('config_image_additional_width'), $this->config->get('config_image_additional_height'))
                    );

                    $this->data['productInfo'] = $product_info;

                    $discount = $this->modelProduct->getProductDiscount($product_id);

                    if ($discount) {
                        $this->data['price'] = $this->currency->format($this->tax->calculate($discount, $product_info['tax_class_id'], $this->config->get('config_tax')));
                        $this->data['special'] = false;
                    } else {
                        $this->data['price'] = $this->currency->format($this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax')));
                        $special = $this->modelProduct->getProductSpecial($product_id);

                        if ($special) {
                            $this->data['special'] = $this->currency->format($this->tax->calculate($special, $product_info['tax_class_id'], $this->config->get('config_tax')));
                        } else {
                            $this->data['special'] = false;
                        }
                    }

                    $discounts = $this->modelProduct->getProductDiscounts($product_id);

                    if ($discounts) {
                        $this->data['discounts'] = [];
                        foreach ($discounts as $discount) {
                            $this->data['discounts'][] = array(
                                'quantity' => $discount['quantity'],
                                'price' => $this->currency->format($this->tax->calculate($discount['price'], $product_info['tax_class_id'], $this->config->get('config_tax')))
                            );
                        }
                    }

                    if ($product_info['quantity'] <= 0) {
                        $this->data['stock'] = $product_info['stock'];
                    } else {
                        if ($this->config->get('config_stock_display')) {
                            $this->data['stock'] = $product_info['quantity'];
                        } else {
                            $this->data['stock'] = $this->language->get('text_instock');
                        }
                    }

                    if ($product_info['minimum']) {
                        $this->data['minimum'] = $product_info['minimum'];
                    } else {
                        $this->data['minimum'] = 1;
                    }

                    $this->data['attributes'] = $this->modelProduct->getAllProperties($product_info['product_id'], 'attribute');
                    $this->data['model'] = $product_info['model'];
                    $this->data['href'] = $Url::createUrl('store/product', array('product_id' => $product_info['product_id']));
                    $this->data['description'] = html_entity_decode($product_info['description'], ENT_QUOTES, 'UTF-8');
                    $this->data['product_id'] = $product_id;
                    $this->data['average'] = $average;
                    $this->data['button_add_to_cart'] = $this->language->get('button_add_to_cart');
                    $this->data['button_see_product'] = $this->language->get('button_see_product');

                    $this->data['images'] = [];
                    $results = $this->modelProduct->getProductImages($product_id);

                    foreach ($results as $k => $result) {
                        $this->data['images'][$k] = array(
                            'popup' => NTImage::resizeAndSave($result['image'], $this->config->get('config_image_popup_width'), $this->config->get('config_image_popup_height')),
                            'preview' => NTImage::resizeAndSave($result['image'], $this->config->get('config_image_thumb_width'), $this->config->get('config_image_thumb_height')),
                            'thumb' => NTImage::resizeAndSave($result['image'], $this->config->get('config_image_additional_width'), $this->config->get('config_image_additional_height'))
                        );
                    }
                    $k = count($this->data['images']) + 1;
                    $this->data['images'][$k] = $imgProduct;

                    if (!$this->config->get('config_customer_price')) {
                        $this->data['display_price'] = true;
                    } elseif ($this->customer->isLogged()) {
                        $this->data['display_price'] = true;
                    } else {
                        $this->data['display_price'] = false;
                    }

                    list($dia, $mes, $ano) = explode('-', date('d-m-Y'));
                    list($pdia, $pmes, $pano) = explode('-', date('d-m-Y', strtotime($product_info['created'])));
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

                    if ($special && $this->data['display_price']) {
                        $this->data['sticker'] = "<div class='oferta'></div>";
                    } elseif ($discount && $this->data['display_price']) {
                        $this->data['sticker'] = "<div class='descuento'></div>";
                    } elseif (strtotime($dia . "-" . $mes . "-" . $ano) <= strtotime($pdia . "-" . $pmes . "-" . $pano)) {
                        $this->data['sticker'] = "<div class='nuevo'></div>";
                    } else {
                        $this->data['sticker'] = "";
                    }

                    $this->data['config_image_popup_width'] = $this->config->get('config_image_popup_width');
                    $this->data['config_image_popup_height'] = $this->config->get('config_image_popup_height');
                    $this->data['config_image_thumb_width'] = $this->config->get('config_image_thumb_width');
                    $this->data['config_image_thumb_height'] = $this->config->get('config_image_thumb_height');
                    $this->data['config_image_additional_width'] = $this->config->get('config_image_additional_width');
                    $this->data['config_image_additional_height'] = $this->config->get('config_image_additional_height');

                    $this->modelProduct->updateStats($this->request->getQuery('product_id'), (int) $this->customer->getId());

                    if ($this->request->hasQuery('resp') && $this->request->getQuery('resp') == 'html') {

                        $default_template = ($this->config->get('default_view_product_quickview')) ? $this->config->get('default_view_product_quickview') : 'store/product_quickview.tpl';
                        $template = empty($template) ? $default_template : $template;
                        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/' . $template)) {
                            $this->template = $this->config->get('config_template') . '/' . $template;
                        } else {
                            $this->template = 'choroni/' . $template;
                        }

                        $this->session->set('landing_page','store/product/quickviewjson');
                        $this->loadWidgets('featuredContent');
                        $this->loadWidgets('main');
                        $this->loadWidgets('featuredFooter');

                        $this->javascripts = [];
                        $this->scripts = [];
                        $this->styles = [];

                        $this->data['html'] = $this->render(true);
                    }

                    if (!$this->user->isLogged()) {
                        $this->cacheId = $cacheId;
                    }
                    $this->response->setOutput(Json::encode($this->data), $this->config->get('config_compression'));
                }
            }
        } else {
            $this->data = [];
            $this->data['error'] = 1;
            $this->load->auto('json');
            $this->response->setOutput(Json::encode($this->data), $this->config->get('config_compression'));
        }
    }
}
