<?php

class ControllerPageSitemap extends Controller {

    public function index() {
        $this->session->clear('object_type');
        $this->session->clear('object_id');
        $this->session->clear('landing_page');

        $cacheId = 'html-sitemap.' .
            $this->config->get('config_language_id') . "." .
            $this->request->hasQuery('hl') . "." .
            $this->request->hasQuery('cc') . "." .
            $this->customer->getId() . "." .
            $this->config->get('config_currency') . "." .
            $this->config->get('config_store_id');

        $cached = $this->cache->get($cacheId);

        $this->load->library('user');
        if ($cached && !$this->user->isLogged()) {
            $this->response->setOutput($cached, $this->config->get('config_compression'));
        } else {
            $Url = new Url($this->registry);

            $this->language->load('page/sitemap');
            $this->document->title = $this->language->get('heading_title');

            $this->document->breadcrumbs = [];
            $this->document->breadcrumbs[] = array(
                'href' => $Url::createUrl("common/home"),
                'text' => $this->language->get('text_home'),
                'separator' => false
            );
            $this->document->breadcrumbs[] = array(
                'href' => $Url::createUrl("page/sitemap"),
                'text' => $this->language->get('heading_title'),
                'separator' => $this->language->get('text_separator')
            );
            $this->data['breadcrumbs'] = $this->document->breadcrumbs;
            $this->data['heading_title'] = $this->language->get('heading_title');

            $this->load->model('store/category');
            $this->load->model('content/page');

            $this->data['special'] = $Url::createUrl("store/special");
            $this->data['account'] = $Url::createUrl("account/account");
            $this->data['edit'] = $Url::createUrl("account/edit");
            $this->data['password'] = $Url::createUrl("account/password");
            $this->data['address'] = $Url::createUrl("account/address");
            $this->data['history'] = $Url::createUrl("account/history");
            $this->data['download'] = $Url::createUrl("account/download");
            $this->data['cart'] = $Url::createUrl("checkout/cart");
            $this->data['checkout'] = $Url::createUrl("checkout/shipping");
            $this->data['search'] = $Url::createUrl("store/search");
            $this->data['contact'] = $Url::createUrl("page/contact");

            $this->data['category'] = $this->getCategories(0);
            $this->data['pages'] = $this->modelPage->getAll();

            $this->session->set('landing_page','page/sitemap');
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

            $template = ($this->config->get('default_view_sitemap')) ? $this->config->get('default_view_sitemap') : 'page/sitemap.tpl';
            if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/' . $template)) {
                $this->template = $this->config->get('config_template') . '/' . $template;
            } else {
                $this->template = 'choroni/' . $template;
            }

            $this->response->setOutput($this->render(true), $this->config->get('config_compression'));
        }
    }

    protected function getCategories($parent_id, $current_path = '') {
        $Url = new Url($this->registry);

        $output = '';

        $results = $this->modelCategory->getAll(['parent_id' => $parent_id]);

        if ($results) {
            $output .= '<ul>';
        }

        foreach ($results as $result) {
            if (!$current_path) {
                $new_path = $result['category_id'];
            } else {
                $new_path = $current_path . '_' . $result['category_id'];
            }

            $output .= '<li>';

            $output .= '<a href="' . $Url::createUrl("store/category", array("path" => $new_path)) . '">' . $result['name'] . '</a>';

            $output .= $this->getCategories($result['category_id'], $new_path);

            $output .= '</li>';
        }

        if ($results) {
            $output .= '</ul>';
        }

        return $output;
    }
}
