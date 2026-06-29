<?php

class ControllerErrorNotFound extends Controller {

    public function index() {
        $this->session->clear('object_type');
        $this->session->clear('object_id');
        $this->session->clear('landing_page');

        $this->response->addHeader($this->request->server['SERVER_PROTOCOL'] . '/1.1 404 Not Found');
        $this->language->load('error/not_found');
        $this->document->title = $this->data['heading_title'] = $this->language->get('heading_title');

        $Url = new Url($this->registry);

        $this->document->breadcrumbs = [];
        $this->document->breadcrumbs[] = array(
            'href' => $Url::createUrl("common/home"),
            'text' => $this->language->get('text_home'),
            'separator' => false
        );
        if (isset($this->request->get['r'])) {
            $this->document->breadcrumbs[] = array(
                'href' => $Url::createUrl($this->request->get['r']),
                'text' => $this->language->get('text_error'),
                'separator' => $this->language->get('text_separator')
            );
        }
        $this->data['breadcrumbs'] = $this->document->breadcrumbs;

            $this->addChild('common/column_left');
            $this->addChild('common/column_right');
            $this->addChild('common/header');
            $this->addChild('common/footer');

        $template = ($this->config->get('default_view_not_found')) ? $this->config->get('default_view_not_found') : 'error/not_found.tpl';
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/' . $template)) {
            $this->template = $this->config->get('config_template') . '/' . $template;
        } else {
            $this->template = 'choroni/' . $template;
        }
                    
        $this->response->setOutput($this->render(true), $this->config->get('config_compression'));
    }
}
