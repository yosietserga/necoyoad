<?php

class ControllerRoomsAccount extends Controller {

    private function __prefetch() {

        $this->session->clear('object_type');
        $this->session->clear('object_id');
        $this->session->clear('landing_page');

        $Url = new Url($this->registry);
        if (!$this->customer->isLogged()) {
            $this->session->set('redirect', $Url::createUrl("rooms/account"));
            $this->redirect($Url::createUrl("account/login"));
        }

        $this->language->load('rooms/account');

        $this->document->breadcrumbs = [];
        $this->document->breadcrumbs[] = array(
            'href' => $Url::createUrl("common/home"),
            'text' => $this->language->get('text_home'),
            'separator' => false
        );
        $this->document->breadcrumbs[] = array(
            'href' => $Url::createUrl("rooms/account"),
            'text' => $this->language->get('text_account'),
            'separator' => $this->language->get('text_separator')
        );

        $this->document->title = $this->data['heading_title'] = $this->language->get('heading_title');

        if ($this->session->has('success')) {
            $this->data['success'] = $this->session->get('success');
            $this->session->clear('success');
        } else {
            $this->data['success'] = '';
        }

        $this->session->set('landing_page','account/account');
        $this->loadWidgets('featuredContent');
        $this->loadWidgets('main');
        $this->loadWidgets('featuredFooter');

        $this->addChild('account/column_left');
        $this->addChild('common/column_left');
        $this->addChild('common/column_right');
        $this->addChild('common/header');
        $this->addChild('common/footer');
    }

    public function index() {
        $this->__prefetch();

        //TODO: add to module install the possibility to add custom default views parameters to View Settings in admin
        $template = $this->config->get('default_view_rooms_account') ?? 'module/rooms/account.tpl';
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/' . $template)) {
            $this->template = $this->config->get('config_template') . '/' . $template;
        } else {
            $this->template = 'choroni/' . $template;
        }

        $this->response->setOutput($this->render(true), $this->config->get('config_compression'));
    }

    public function create() {        
        $this->__prefetch();

        $Url = new Url($this->registry);
        $this->document->breadcrumbs[] = array(
            'href' => $Url::createUrl("rooms/account/create"),
            'text' => $this->language->get('Create Room'),
            'separator' => $this->language->get('text_separator')
        );

        //TODO: add to module install the possibility to add custom default views parameters to View Settings in admin
        $template = $this->config->get('default_view_rooms_account_create_form') ?? 'module/rooms/create_form.tpl';
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/' . $template)) {
            $this->template = $this->config->get('config_template') . '/' . $template;
        } else {
            $this->template = 'choroni/' . $template;
        }

        $this->response->setOutput($this->render(true), $this->config->get('config_compression'));
    }
}
