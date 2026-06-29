<?php

class ControllerStoreCategory extends Controller {

    public function index() {
        $Url = new Url($this->registry);
        
        $this->document->breadcrumbs = [];
        $this->document->breadcrumbs[] = array(
            'href' => $Url::createUrl('common/home'),
            'text' => $this->language->get('text_home'),
            'separator' => false
        );

        if (isset($this->request->get['path'])) {
            $path = '';
            $parts = explode('_', $this->request->get['path']);
            foreach ($parts as $path_id) {
                $category_info = $this->modelCategory->getById($path_id);
                if ($category_info) {
                    if (!$path) {
                        $path = $path_id;
                    } else {
                        $path .= '_' . $path_id;
                    }
                    $this->document->breadcrumbs[] = array(
                        'href' => $Url::createUrl('store/category', array('path' => $path)),
                        'text' => $category_info['name'],
                        'separator' => $this->language->get('text_separator')
                    );
                }
            }
            $this->data['category_id'] = array_pop($parts);
        } else {
            $this->data['category_id'] = 0;
        }

        $this->session->clear('object_type');
        $this->session->clear('object_id');
        $this->session->clear('landing_page');
        $this->session->set('object_type', 'category');
        $this->session->set('object_id', $this->data['category_id']);

        $cacheId = 'html-category.' .
            $this->request->getQuery('path') .
            $this->data['category_id'] .
            serialize($this->request->get).
            $this->config->get('config_language_id') . "." .
            $this->request->getQuery('hl') . "." .
            $this->request->getQuery('cc') . "." .
            $this->customer->getId() . "." .
            $this->config->get('config_currency') . "." .
            (int) $this->config->get('config_store_id');

        $category_info = $this->modelCategory->getById($this->data['category_id']);

        if ($category_info) {
            $this->request->get['category_id'] = $this->data['category_id'];

                //tracker
            $this->tracker->track($category_info['category_id'], 'category');

            if ($this->session->has('ref_email') && !$this->session->has('ref_cid')) {
                $this->data['show_register_form_invitation'] = true;
            }

            $this->session->set('redirect', $Url::createUrl('store/category', array('path' => $this->data['category_id'])));

            $this->modelCategory->updateStats($this->request->getQuery('path'));

            $cached = $this->cache->get($cacheId);
            $this->load->library('user');
            if ($cached && !$this->user->isLogged()) {
                $this->response->setOutput($cached, $this->config->get('config_compression'));
            } else {
                $this->document->title = $this->data['heading_title'] = $category_info['title'];
                $this->document->description = $category_info['meta_description'];
                $this->document->keywords = $category_info['meta_keywords'];
                $this->data['breadcrumbs'] = $this->document->breadcrumbs;

                $this->session->set('landing_page','store/category/index');
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

                $template = $this->modelCategory->getProperty($this->data['category_id'], 'style', 'view');
                $default_template = ($this->config->get('default_view_product_category')) ? $this->config->get('default_view_product_category') : 'store/category.tpl';
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

        if (isset($this->request->get['path'])) {
            $this->document->breadcrumbs[] = array(
                'href' => $Url::createUrl('store/category', array("path" => $this->request->get['path'])) . $url,
                'text' => $this->language->get('text_error'),
                'separator' => $this->language->get('text_separator')
            );
        }

        $this->session->clear('object_type');
        $this->session->clear('object_id');
        $this->session->set('object_type', 'category');

        $this->data['breadcrumbs'] = $this->document->breadcrumbs;
        $this->document->title = $this->data['heading_title'] = $this->language->get('text_error');

        $this->session->set('landing_page','store/category/error404');
        $this->loadWidgets('featuredContent');
        $this->loadWidgets('main');
        $this->loadWidgets('featuredFooter');

        $this->addChild('common/column_left');
        $this->addChild('common/column_right');
        $this->addChild('common/header');
        $this->addChild('common/footer');

        $template = ($this->config->get('default_view_product_category_error')) ? $this->config->get('default_view_product_category_error') : 'error/not_found.tpl';
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/' . $template)) {
            $this->template = $this->config->get('config_template') . '/' . $template;
        } else {
            $this->template = 'choroni/' . $template;
        }

        $this->response->setOutput($this->render(true), $this->config->get('config_compression'));
    }

    public function all() {
        $Url = new Url($this->registry);

        $this->language->load('store/category');
        $this->document->breadcrumbs = [];
        $this->document->breadcrumbs[] = array(
            'href' => $Url::createUrl('common/home'),
            'text' => $this->language->get('text_home'),
            'separator' => false
        );
        $this->document->breadcrumbs[] = array(
            'href' => $Url::createUrl('store/category/all'),
            'text' => $this->language->get('All Categories'),
            'separator' => false
        );
        $this->data['breadcrumbs'] = $this->document->breadcrumbs;

        $this->document->title = $this->data['heading_title'] = $this->language->get('heading_title');
        $this->document->description = $this->language->get('meta_description');
        $this->document->keywords = $this->language->get('meta_keywords');

        $this->session->clear('object_type');
        $this->session->clear('object_id');
        $this->session->clear('landing_page');
        $this->session->set('object_type', 'category');

        $cacheId = 'html-categories.' .
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
            $this->session->set('landing_page', 'store/category/all');
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

            $template = ($this->config->get('default_view_product_category_all')) ? $this->config->get('default_view_product_category_all') : 'store/categories.tpl';
            if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/' . $template)) {
                $this->template = $this->config->get('config_template') . '/' . $template;
            } else {
                $this->template = 'choroni/' . $template;
            }

            $this->response->setOutput($this->render(true), $this->config->get('config_compression'));
        }
    }
}
