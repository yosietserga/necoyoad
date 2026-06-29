<?php

class ControllerStyleViews extends Controller {

    private $error = [];

    public function index() {
        $this->language->load('style/views');
        $this->document->title = $this->language->get('heading_title');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->load->auto('setting/setting');
            $this->modelSetting->update('views', $this->request->post);
            $this->session->set('success', $this->language->get('text_success'));
        }

        if ($this->session->has('success')) {
            $this->data['success'] = $this->session->get('success');
            $this->session->clear('success');
        } else {
            $this->data['success'] = '';
        }

        // general views
        $this->setvar('default_view_search_home');
        $this->setvar('default_view_not_found');
        $this->setvar('default_view_home');
        $this->setvar('default_view_maintenance');
        $this->setvar('default_view_contact');
        $this->setvar('default_view_sitemap');

        // content views
        $this->setvar('default_view_page');
        $this->setvar('default_view_page');
        $this->setvar('default_view_page_all');
        $this->setvar('default_view_page_error');
        $this->setvar('default_view_page_review');
        $this->setvar('default_view_page_comment');
        $this->setvar('default_view_post');
        $this->setvar('default_view_post_all');
        $this->setvar('default_view_post_error');
        $this->setvar('default_view_post_review');
        $this->setvar('default_view_post_comment');
        $this->setvar('default_view_post_category');

        // store views
        $this->setvar('default_view_search');
        $this->setvar('default_view_special');
        $this->setvar('default_view_special_home');
        $this->setvar('default_view_special_error');
        $this->setvar('default_view_product_category');
        $this->setvar('default_view_product_category_all');
        $this->setvar('default_view_product_category_home');
        $this->setvar('default_view_product_category_error');
        $this->setvar('default_view_product');
        $this->setvar('default_view_product_all');
        $this->setvar('default_view_product_error');
        $this->setvar('default_view_product_review');
        $this->setvar('default_view_product_comment');
        $this->setvar('default_view_product_related');
        $this->setvar('default_view_manufacturer');
        $this->setvar('default_view_manufacturer_all');
        $this->setvar('default_view_manufacturer_home');
        $this->setvar('default_view_manufacturer_error');

        // account views
        $this->setvar('default_view_account_login');
        $this->setvar('default_view_account_logout');
        $this->setvar('default_view_account_message');
        $this->setvar('default_view_account_message_sent');
        $this->setvar('default_view_account_message_create');
        $this->setvar('default_view_account_message_read');
        $this->setvar('default_view_account_addresses');
        $this->setvar('default_view_account_address');
        $this->setvar('default_view_account_balance');
        $this->setvar('default_view_account_balance_receipt');
        $this->setvar('default_view_account_order_balance');
        $this->setvar('default_view_account_download');
        $this->setvar('default_view_account_edit');
        $this->setvar('default_view_account_forgotten');
        $this->setvar('default_view_account_history');
        $this->setvar('default_view_account_newsletter');
        $this->setvar('default_view_account_order');
        $this->setvar('default_view_account_password');
        $this->setvar('default_view_account_payment');
        $this->setvar('default_view_account_payment_receipt');
        $this->setvar('default_view_account_order_payment');
        $this->setvar('default_view_account_register');
        $this->setvar('default_view_account_review');
        $this->setvar('default_view_account_review_read');
        $this->setvar('default_view_account_review_read_error');
        $this->setvar('default_view_account_success');
        $this->setvar('default_view_account_account');

        $this->document->breadcrumbs = [];
        $this->document->breadcrumbs[] = array(
            'href' => Url::createAdminUrl('common/home'),
            'text' => $this->language->get('text_home'),
            'separator' => false
        );
        $this->document->breadcrumbs[] = array(
            'href' => Url::createAdminUrl('marketing/message'),
            'text' => $this->language->get('heading_title'),
            'separator' => ' :: '
        );
        $this->document->title = $this->data['heading_title'] = $this->language->get('heading_title');

        if (file_exists(DIR_CATALOG . 'view/theme/' . $this->config->get('config_template') . '/common/home.tpl')) {
            $folderTPL = DIR_CATALOG . 'view/theme/' . $this->config->get('config_template') . '/';
        } else {
            $folderTPL = DIR_CATALOG . 'view/theme/default/';
        }

        $directories = glob($folderTPL . "*", GLOB_ONLYDIR);
        $this->data['templates'] = [];
        foreach ($directories as $key => $directory) {
            $this->data['views'][$key]['folder'] = basename($directory);
            $files = glob($directory . "/*.tpl", GLOB_NOSORT);
            foreach ($files as $k => $file) {
                $this->data['views'][$key]['files'][$k] = str_replace("\\", "/", $file);
            }
        }

        $template = ($this->config->get('default_admin_view_style_views')) ? $this->config->get('default_admin_view_style_views') : 'style/views.tpl';
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_admin_template') . '/'. $template)) {
            $this->template = $this->config->get('config_admin_template') . '/' . $template;
        } else {
            $this->template = 'default/' . $template;
        }


        $this->children[] = 'common/header';
        $this->children[] = 'common/nav';
        $this->children[] = 'common/footer';
        
        $this->response->setOutput($this->render(true), $this->config->get('marketing_compression'));
    }

    private function validate() {
        if (!$this->user->hasPermission('modify', 'style/views')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }

}
