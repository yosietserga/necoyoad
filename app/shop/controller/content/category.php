<?php

class ControllerContentCategory extends Controller {

    public function index() {
        $this->language->load('content/category');
        $this->load->model('content/category');
        $this->load->model('content/post');

        $Url = new Url($this->registry);

        $this->document->breadcrumbs = [];
        $this->document->breadcrumbs[] = array(
            'href' => $Url::createUrl("common/home"),
            'text' => $this->language->get('text_home'),
            'separator' => false
        );
        $this->document->breadcrumbs[] = array(
            'href' => $Url::createUrl("content/category/all"),
            'text' => $this->language->get('Blog'),
            'separator' => false
        );

        $this->document->title = $this->language->get('heading_title') . " - " . $this->config->get('config_title');
        $this->data['heading_title'] = $this->language->get('heading_title');

        $this->setvar('category_id', null, 0);
        if ($this->data['category_id']) {
            $category_info = $this->modelCategory->getById($this->data['category_id']);
        }

        $this->session->clear('object_type');
        $this->session->clear('object_id');
        $this->session->set('object_type', 'post_category');
        $this->session->set('object_id', $this->data['category_id']);
        $this->session->clear('landing_page');

        if ($category_info) {
            //tracker
            $this->tracker->track($category_info['category_id'], 'post_category');

            if ($this->session->has('ref_email') && !$this->session->has('ref_cid')) {
                $this->data['show_register_form_invitation'] = true;
            }

            $this->request->get['category_id'] = $this->data['category_id'];

            $this->document->title = $category_info['seo_title'];
            $this->document->description = $category_info['meta_description'];
            $this->document->keywords = $category_info['meta_keywords'];

            $this->document->breadcrumbs[] = array(
                'href' => $Url::createUrl("content/category") . '&category_id=' . $this->request->get['category_id'],
                'text' => $category_info['title'],
                'separator' => $this->language->get('text_separator')
            );

            $this->data['breadcrumbs'] = $this->document->breadcrumbs;
            $this->document->title = $this->data['heading_title'] = $category_info['title'];
            $this->data['description'] = html_entity_decode($category_info['description']);
            $this->data['keywords'] = explode(";", $category_info['meta_keywords']);

            $this->session->set('landing_page','content/category/index');
            $this->loadWidgets('featuredContent');
            $this->loadWidgets('main');
            $this->loadWidgets('featuredFooter');

            $this->addChild('common/column_left');
            $this->addChild('common/column_right');
            $this->addChild('common/header');
            $this->addChild('common/footer');

            $template = $this->modelCategory->getProperty($this->data['category_id'], 'style', 'view');
            $default_template = ($this->config->get('default_view_post_category')) ? $this->config->get('default_view_post_category') : 'content/category.tpl';
            $template = empty($template) ? $default_template : $template;
            if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/' . $template)) {
                $this->template = $this->config->get('config_template') . '/' . $template;
            } else {
                $this->template = 'choroni/' . $template;
            }

            $this->response->setOutput($this->render(true), $this->config->get('config_compression'));
        } else {
            $this->error404();
        }
    }

    protected function error404() {
        $Url = new Url($this->registry);

        $this->document->title = $this->language->get('text_error');

        $this->data['heading_title'] = $this->language->get('text_error');

        $this->data['text_error'] = $this->language->get('text_error');

        $this->session->set('landing_page','content/category/error404');
        $this->loadWidgets('featuredContent');
        $this->loadWidgets('main');
        $this->loadWidgets('featuredFooter');

        $this->addChild('common/column_left');
        $this->addChild('common/column_right');
        $this->addChild('common/footer');
        $this->addChild('common/header');

        $template = ($this->config->get('default_view_page_error')) ? $this->config->get('default_view_page_error') : 'error/not_found.tpl';
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
            'href' => $Url::createUrl('content/category/all'),
            'text' => $this->language->get('Blog'),
            'separator' => false
        );
        $this->data['breadcrumbs'] = $this->document->breadcrumbs;

        $this->document->title = $this->data['heading_title'] = $this->language->get('heading_title');
        $this->document->description = $this->language->get('meta_description');
        $this->document->keywords = $this->language->get('meta_keywords');

        $this->session->clear('object_type');
        $this->session->clear('object_id');
        $this->session->clear('landing_page');
        $this->session->set('object_type', 'post_category');

        $cacheId = 'html-post_categories.' .
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
            $this->session->set('landing_page', 'content/category/all');
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

            $template = ($this->config->get('default_view_post_category_all')) ? $this->config->get('default_view_post_category_all') : 'content/categories.tpl';
            if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/' . $template)) {
                $this->template = $this->config->get('config_template') . '/' . $template;
            } else {
                $this->template = 'choroni/' . $template;
            }

            $this->response->setOutput($this->render(true), $this->config->get('config_compression'));
        }
    }
}
