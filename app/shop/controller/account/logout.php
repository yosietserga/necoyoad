<?php

class ControllerAccountLogout extends Controller {

    public function index() {
        $this->session->clear('object_type');
        $this->session->clear('object_id');
        $this->session->clear('landing_page');

        $Url = new Url($this->registry);
        if ($this->customer->isLogged()) {
            $this->customer->logout();
            $this->cart->clear();

            $this->session->clear('shipping_address_id');
            $this->session->clear('shipping_method');
            $this->session->clear('shipping_methods');
            $this->session->clear('payment_address_id');
            $this->session->clear('payment_method');
            $this->session->clear('payment_methods');
            $this->session->clear('comment');
            $this->session->clear('order_id');
            $this->session->clear('coupon');

            $this->tax->setZone($this->config->get('config_country_id'), $this->config->get('config_zone_id'));

            $this->redirect($Url::createUrl("account/logout"));
        } else {
            $this->redirect(HTTP_HOME);
        }

        $this->language->load('account/logout');

        $this->document->title = $this->data['heading_title'] = $this->language->get('heading_title');

        $this->document->breadcrumbs = [];
        $this->document->breadcrumbs[] = array(
            'href' => $Url::createUrl("common/home"),
            'text' => $this->language->get('text_home'),
            'separator' => false
        );
        $this->document->breadcrumbs[] = array(
            'href' => $Url::createUrl("account/account"),
            'text' => $this->language->get('text_account'),
            'separator' => $this->language->get('text_separator')
        );
        $this->document->breadcrumbs[] = array(
            'href' => $Url::createUrl("account/logout"),
            'text' => $this->language->get('text_logout'),
            'separator' => $this->language->get('text_separator')
        );
        $this->data['breadcrumbs'] = $this->document->breadcrumbs;



        $this->session->set('landing_page','account/logout');
        $this->loadWidgets('featuredContent');
        $this->loadWidgets('main');
        $this->loadWidgets('featuredFooter');

            $this->addChild('common/column_left');
            $this->addChild('common/column_right');
            $this->addChild('common/header');
            $this->addChild('common/footer');



        $template = ($this->config->get('default_view_account_logout')) ? $this->config->get('default_view_account_logout') : 'account/logout.tpl';
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/' . $template)) {
            $this->template = $this->config->get('config_template') . '/' . $template;
        } else {
            $this->template = 'choroni/' . $template;
        }

        $this->response->setOutput($this->render(true), $this->config->get('config_compression'));
    }

}
