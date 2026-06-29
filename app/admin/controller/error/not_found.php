<?php

class ControllerErrorNotFound extends Controller {

    public function index() {
        $this->load->language('error/not_found');

        $this->document->title = $this->language->get('heading_title');

        $this->data['heading_title'] = $this->language->get('heading_title');

        $this->data['text_not_found'] = $this->language->get('text_not_found');

        $this->document->breadcrumbs = [];

        $this->document->breadcrumbs[] = array(
            'href' => Url::createAdminUrl('common/home'),
            'text' => $this->language->get('text_home'),
            'separator' => false
        );

        $this->document->breadcrumbs[] = array(
            'href' => Url::createAdminUrl('error/not_found'),
            'text' => $this->language->get('heading_title'),
            'separator' => ' :: '
        );

        $template = ($this->config->get('default_admin_view_not_found')) ? $this->config->get('default_admin_view_not_found') : 'error/not_found.tpl';
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_admin_template') . '/'. $template)) {
            $this->template = $this->config->get('config_admin_template') . '/' . $template;
        } else {
            $this->template = 'default/' . $template;
        }

        $this->children[] = 'common/header';
        $this->children[] = 'common/nav';
        $this->children[] = 'common/footer';
        
        $this->response->setOutput($this->render(true), $this->config->get('config_compression'));
    }

}
