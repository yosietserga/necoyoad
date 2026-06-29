<?php

class ControllerStoreManufacturer extends Controller {

    public function index() {
        $this->language->load('store/manufacturer');
        $this->load->model('store/manufacturer');

        $Url = new Url($this->registry);

        $this->document->breadcrumbs = [];
        $this->document->breadcrumbs[] = array(
            'href' => $Url::createUrl("store/home"),
            'text' => $this->language->get('text_home'),
            'separator' => false
        );

        if (isset($this->request->get['manufacturer_id'])) {
            $this->data['manufacturer_id'] = $this->request->get['manufacturer_id'];
        } else {
            $this->data['manufacturer_id'] = 0;
        }

        $this->session->clear('object_type');
        $this->session->clear('object_id');
        $this->session->clear('landing_page');
        $this->session->set('object_type', 'manufacturer');
        $this->session->set('object_id', $this->data['manufacturer_id']);

        $manufacturer_info = $this->modelManufacturer->getManufacturer($this->data['manufacturer_id']);
        $this->object_id = $this->data['manufacturer_id'];

        $cacheId = 'html-manufacturer.' .
            $this->data['manufacturer_id'] .
            $this->config->get('config_language_id') . "." .
            $this->request->getQuery('hl') . "." .
            $this->request->getQuery('cc') . "." .
            $this->customer->getId() . "." .
            $this->config->get('config_currency') . "." .
            (int) $this->config->get('config_store_id');

        if ($manufacturer_info) {
            //tracker
            $this->tracker->track($manufacturer_info['manufacturer_id'], 'manufacturer');

            if ($this->session->has('ref_email') && !$this->session->has('ref_cid')) {
                $this->data['show_register_form_invitation'] = true;
            }

            $this->session->set('redirect', $Url::createUrl("store/manufacturer", array('manufacturer_id' => $this->data['manufacturer_id'])));

            $this->modelManufacturer->updateStats($this->request->getQuery('manufacturer_id'), $this->customer->getId());

            $cached = $this->cache->get($cacheId);
            $this->load->library('user');
            if ($cached && !$this->user->isLogged()) {
                $this->response->setOutput($cached, $this->config->get('config_compression'));
            } else {
                $this->document->breadcrumbs[] = array(
                    'href' => $Url::createUrl("store/manufacturer", array("manufacturer_id" => $this->request->get['manufacturer_id'])),
                    'text' => $manufacturer_info['name'],
                    'separator' => $this->language->get('text_separator')
                );
                $this->data['breadcrumbs'] = $this->document->breadcrumbs;

                $this->document->title = $manufacturer_info['name'];

                $this->session->set('landing_page','store/manufacturer/index');
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

                $template = $this->modelManufacturer->getProperty($this->data['manufacturer_id'], 'style', 'view');
                $default_template = ($this->config->get('default_view_manufacturer')) ? $this->config->get('default_view_manufacturer') : 'store/manufacturer.tpl';
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
    }

    protected function error404() {
        $Url = new Url($this->registry);

        $url = '';

        if ($this->request->hasQuery('sort')) {
            $url .= '&sort=' . $this->request->getQuery('sort');
        }
        if ($this->request->hasQuery('order')) {
            $url .= '&order=' . $this->request->getQuery('order');
        }
        if ($this->request->hasQuery('v')) {
            $url .= '&v=' . $this->request->getQuery('v');
        }
        if ($this->request->hasQuery('page')) {
            $url .= '&page=' . $this->request->getQuery('page');
        }

        $this->document->breadcrumbs[] = array(
            'href' => $Url::createUrl("store/manufacturer", array("manufacturer_id" => $this->request->getQuery('manufacturer_id') . $url)),
            'text' => $this->language->get('text_error'),
            'separator' => $this->language->get('text_separator')
        );

        $this->data['breadcrumbs'] = $this->document->breadcrumbs;

        $this->document->title = $this->data['heading_title'] = $this->language->get('text_error');

        $this->session->set('landing_page','store/manufacturer/error404');
        $this->loadWidgets('featuredContent');
        $this->loadWidgets('main');
        $this->loadWidgets('featuredFooter');

            $this->addChild('common/column_left');
            $this->addChild('common/column_right');
            $this->addChild('common/header');
            $this->addChild('common/footer');

        $template = ($this->config->get('default_view_manufacturer_error')) ? $this->config->get('default_view_manufacturer_error') : 'error/not_found.tpl';
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/' . $template)) {
            $this->template = $this->config->get('config_template') . '/' . $template;
        } else {
            $this->template = 'choroni/' . $template;
        }

        $this->response->setOutput($this->render(true), $this->config->get('config_compression'));
    }

    public function all() {
        $Url = new Url($this->registry);

        $this->language->load('store/manufacturer');
        $this->document->breadcrumbs = [];
        $this->document->breadcrumbs[] = array(
            'href' => $Url::createUrl('common/home'),
            'text' => $this->language->get('text_home'),
            'separator' => false
        );
        $this->document->breadcrumbs[] = array(
            'href' => $Url::createUrl('store/manufacturer/all'),
            'text' => $this->language->get('All Brands'),
            'separator' => false
        );
        $this->data['breadcrumbs'] = $this->document->breadcrumbs;

        $this->document->title = $this->data['heading_title'] = $this->language->get('All Brands');
        $this->document->description = $this->language->get('meta_description');
        $this->document->keywords = $this->language->get('meta_keywords');

        $this->session->clear('object_type');
        $this->session->clear('object_id');
        $this->session->clear('landing_page');
        $this->session->set('object_type', 'manufacturer');

        $cacheId = 'html-manufacturers.' .
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
            $this->session->set('landing_page', 'store/manufacturer/all');
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

            $template = ($this->config->get('default_view_product_manufacturer_all')) ? $this->config->get('default_view_product_manufacturer_all') : 'store/manufacturers.tpl';
            if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/' . $template)) {
                $this->template = $this->config->get('config_template') . '/' . $template;
            } else {
                $this->template = 'choroni/' . $template;
            }

            $this->response->setOutput($this->render(true), $this->config->get('config_compression'));
        }
    }
}
