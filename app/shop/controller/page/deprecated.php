<?php

class ControllerPageDeprecated extends Controller {

    private $error = [];

    public function index() {
        $this->session->clear('object_type');
        $this->session->clear('object_id');
        $this->session->clear('landing_page');

        $Url = new Url($this->registry);

        $this->language->load('page/deprecated');
        $this->document->title = $this->data['heading_title'] = $this->language->get('heading_title');
        $this->document->breadcrumbs = [];
        $this->document->breadcrumbs[] = array(
            'href' => $Url::createUrl("common/home"),
            'text' => $this->language->get('text_home'),
            'separator' => false
        );
        $this->document->breadcrumbs[] = array(
            'href' => $Url::createUrl("page/deprecated"),
            'text' => $this->language->get('heading_title'),
            'separator' => $this->language->get('text_separator')
        );
        $this->data['breadcrumbs'] = $this->document->breadcrumbs;

        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/page/deprecated.tpl')) {
            $this->template = $this->config->get('config_template') . '/page/deprecated.tpl';
        } else {
            $this->template = 'choroni/page/deprecated.tpl';
        }

        $this->session->set('landing_page','page/deprecated');
        $this->loadWidgets('featuredContent');
        $this->loadWidgets('main');
        $this->loadWidgets('featuredFooter');

            $this->addChild('common/column_left');
            $this->addChild('common/column_right');
            $this->addChild('common/header');
            $this->addChild('common/footer');

        $this->response->setOutput($this->render(true), $this->config->get('config_compression'));
    }

}
