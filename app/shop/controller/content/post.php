<?php

class ControllerContentPost extends Controller {

    public function index() {
        $this->language->load('content/post');
        $this->load->model('content/post');

        $Url = new Url($this->registry);

        $cacheId = 'html-post.' .
            $this->request->getQuery('post_id') .
            $this->config->get('config_language_id') . "." .
            $this->request->getQuery('hl') . "." .
            $this->request->getQuery('cc') . "." .
            $this->customer->getId() . "." .
            $this->config->get('config_currency') . "." .
            (int) $this->config->get('config_store_id');

        $this->document->breadcrumbs = [];
        $this->document->breadcrumbs[] = array(
            'href' => $Url::createUrl("common/home"),
            'text' => $this->language->get('text_home'),
            'separator' => false
        );

        if ($this->request->hasQuery('post_id')) {
            $post_id = $this->request->getQuery('post_id');
        } else {
            $post_id = 0;
        }

        $this->session->clear('object_type');
        $this->session->clear('object_id');
        $this->session->clear('landing_page');
        $this->session->set('object_type', 'post');
        $this->session->set('object_id', $post_id);

        $this->session->set('redirect', $Url::createUrl('content/post', array('post_id' => $post_id)));

        $post_info = $this->modelPost->getById($post_id);

        if ($post_info && ($post_info['publish'] || $this->request->hasQuery('preview'))) {
            //tracker
            $this->tracker->track($post_info['post_id'], 'post');

            if ($this->session->has('ref_email') && !$this->session->has('ref_cid')) {
                $this->data['show_register_form_invitation'] = true;
            }

            $customerGroups = $this->modelPost->getProperty($post_id, 'customer_groups', 'customer_groups');
            if (($this->customer->isLogged() && in_array($this->customer->getCustomerGroupId(), $customerGroups)) || in_array(0, $customerGroups)) {
                $cached = $this->cache->get($cacheId);
                $this->load->library('user');
                if ($cached && !$this->user->isLogged()) {
                    $this->response->setOutput($cached, $this->config->get('config_compression'));
                } else {
                    $this->document->title = $post_info['title'];
                    $this->document->breadcrumbs[] = array(
                        'href' => $Url::createUrl("content/post", array('post_id' => $this->request->get['post_id'])),
                        'text' => $post_info['title'],
                        'separator' => $this->language->get('text_separator')
                    );

                    $this->data['breadcrumbs'] = $this->document->breadcrumbs;

                    $this->setvar('template', $post_info, false);
                    $this->setvar('post_id', $post_info, 0);

                    $this->session->set('landing_page','content/post/index');
                    $this->loadWidgets('featuredContent');
                    $this->loadWidgets('main');
                    $this->loadWidgets('featuredFooter');

            $this->addChild('common/column_left');
            $this->addChild('common/column_right');
            $this->addChild('common/header');
            $this->addChild('common/footer');

                    $template = $this->modelPost->getProperty($post_id, 'style', 'view');
                    $default_template = ($this->config->get('default_view_post')) ? $this->config->get('default_view_post') : 'content/post.tpl';
                    $template = empty($template) ? $default_template : $template;
                    if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/' . $template)) {
                        $this->template = $this->config->get('config_template') . '/' . $template;
                    } else {
                        $this->template = 'choroni/' . $template;
                    }

                    if (!$this->user->isLogged()) {
                        $this->cacheId = $cacheId;
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

        $this->document->breadcrumbs[] = array(
            'href' => $Url::createUrl("content/post") . '&post_id=' . $this->request->get['post_id'],
            'text' => $this->language->get('text_error'),
            'separator' => $this->language->get('text_separator')
        );
        $this->data['breadcrumbs'] = $this->document->breadcrumbs;
        $this->document->title = $this->language->get('text_error');
        $this->data['heading_title'] = $this->language->get('text_error');
        $this->data['text_error'] = $this->language->get('text_error');

        $this->session->clear('object_type');
        $this->session->clear('object_id');
        $this->session->set('object_type', 'post');

        $this->session->set('landing_page','content/post/error404');
        $this->loadWidgets('featuredContent');
        $this->loadWidgets('main');
        $this->loadWidgets('featuredFooter');

            $this->addChild('common/column_left');
            $this->addChild('common/column_right');
            $this->addChild('common/header');
            $this->addChild('common/footer');

        $template = ($this->config->get('default_view_post_error')) ? $this->config->get('default_view_post_error') : 'error/not_found.tpl';
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/' . $template)) {
            $this->template = $this->config->get('config_template') . '/' . $template;
        } else {
            $this->template = 'choroni/' . $template;
        }

        $this->response->setOutput($this->render(true), $this->config->get('config_compression'));
    }

    public function all() {
        $this->language->load('content/post');
        $this->load->model('content/post');

        $Url = new Url($this->registry);

        $this->document->title = $this->language->get('heading_title') . " - " . $this->config->get('config_title');
        $this->data['heading_title'] = $this->language->get('heading_title');

        $this->session->clear('object_type');
        $this->session->clear('object_id');
        $this->session->clear('landing_page');
        $this->session->set('object_type', 'post');

        $this->document->breadcrumbs = [];
        $this->document->breadcrumbs[] = array(
            'href' => $Url::createUrl("common/home"),
            'text' => $this->language->get('text_home'),
            'separator' => false
        );
        $this->document->breadcrumbs[] = array(
            'href' => $Url::createUrl("content/post/all"),
            'text' => $this->language->get('text_posts'),
            'separator' => false
        );

        $this->data['breadcrumbs'] = $this->document->breadcrumbs;

        $cacheId = 'html-posts.' .
            serialize($this->request->get).
            $this->request->getQuery('page') . "." .
            $this->request->getQuery('category_id') . "." .
            $this->request->getQuery('category_id') . "." .
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
            $this->session->set('redirect', $Url::createUrl('content/post/all'));

            $this->session->set('landing_page', 'content/post/all');
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

            $template = ($this->config->get('default_view_post_all')) ? $this->config->get('default_view_post_all') : 'content/posts.tpl';
            if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/' . $template)) {
                $this->template = $this->config->get('config_template') . '/' . $template;
            } else {
                $this->template = 'choroni/' . $template;
            }

            $this->response->setOutput($this->render(true), $this->config->get('config_compression'));
        }
    }
}
