<?php

class ControllerAccountEdit extends Controller {

    private $error = [];

    public function index() {
        $this->session->clear('object_type');
        $this->session->clear('object_id');
        $this->session->clear('landing_page');

        $Url = new Url($this->registry);
        if (!$this->customer->isLogged()) {
            $this->session->set('redirect', Url::createUrl("account/edit"));
            $this->redirect(Url::createUrl("account/login"));
        }

        $this->language->load('account/edit');

        $this->document->title = $this->language->get('heading_title');

        $this->load->model('account/customer');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->request->post['birthday'] = $this->request->post['bday'] . "/" . $this->request->post['bmonth'] . "/" . $this->request->post['byear'];
            $this->modelCustomer->editCustomer($this->request->post);
            $this->session->set('success', $this->language->get('text_success'));
            $this->redirect($Url::createUrl("account/account"));
        }

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
            'href' => $Url::createUrl("account/edit"),
            'text' => $this->language->get('text_edit'),
            'separator' => $this->language->get('text_separator')
        );

        $this->data['heading_title'] = $this->language->get('heading_title');

        $this->data['error_warning'] = isset($this->error['warning']) ? $this->error['warning'] : '';
        $this->data['error_firstname'] = isset($this->error['firstname']) ? $this->error['firstname'] : '';
        $this->data['error_lastname'] = isset($this->error['lastname']) ? $this->error['lastname'] : '';
        $this->data['error_sexo'] = isset($this->error['sexo']) ? $this->error['sexo'] : '';
        $this->data['error_birthday'] = isset($this->error['birthday']) ? $this->error['birthday'] : '';
        $this->data['error_telephone'] = isset($this->error['telephone']) ? $this->error['telephone'] : '';
        $this->data['error_company'] = isset($this->error['company']) ? $this->error['company'] : '';
        $this->data['error_rif'] = isset($this->error['rif']) ? $this->error['rif'] : '';
        $this->data['error_twitter'] = isset($this->error['twitter']) ? $this->error['twitter'] : '';
        $this->data['error_facebook'] = isset($this->error['facebook']) ? $this->error['facebook'] : '';
        $this->data['error_warning'] = isset($this->error['warning']) ? $this->error['warning'] : '';
        $this->data['error_warning'] = isset($this->error['warning']) ? $this->error['warning'] : '';

        $this->data['action'] = Url::createUrl("account/edit");

        if ($this->request->server['REQUEST_METHOD'] != 'POST') {
            $customer_info = $this->modelCustomer->getCustomer($this->customer->getId());
        }

        $this->setvar('firstname', $customer_info);
        $this->setvar('lastname', $customer_info);
        $this->setvar('company', $customer_info);
        $this->setvar('sexo', $customer_info);
        $this->setvar('birthday', $customer_info);
        list($this->data['bday'], $this->data['bmonth'], $this->data['byear']) = explode("/", $this->data['birthday']);
        $this->setvar('telephone', $customer_info);
        $this->setvar('rif', $customer_info);
        $this->setvar('twitter', $customer_info);
        $this->setvar('facebook', $customer_info);
        $this->setvar('msn', $customer_info);
        $this->setvar('gmail', $customer_info);
        $this->setvar('yahoo', $customer_info);
        $this->setvar('skype', $customer_info);
        $this->setvar('blog', $customer_info);
        $this->setvar('website', $customer_info);
        $this->setvar('profesion', $customer_info);
        $this->setvar('titulo', $customer_info);

        // scripts
        $scripts[] = array('id' => 'scriptsEdit', 'method' => 'ready', 'script' =>
            "$('#form').ntForm();
            $('#form textarea').ntTextArea();
            $('#form select').ntSelect();");

        $this->scripts = array_merge($this->scripts, $scripts);

        // javascript files
        $jspath = defined("CDN_JS") ? CDN_JS : HTTP_JS;
        $javascripts[] = $jspath . "necojs/neco.form.js";
        $javascripts[] = $jspath . "vendor/jquery-ui.min.js";
        $this->javascripts = array_merge($this->javascripts, $javascripts);

        // style files
        $csspath = defined("CDN_CSS") ? CDN_CSS : HTTP_CSS;
        $styles[] = array('media' => 'all', 'href' => $csspath . 'jquery-ui/jquery-ui.min.css');
        $styles[] = array('media' => 'all', 'href' => $csspath . 'neco.form.css');
        $this->styles = array_merge($this->styles, $styles);

        if ($scripts)
            $this->scripts = array_merge($this->scripts, $scripts);



        $this->session->set('landing_page','account/edit');
        $this->loadWidgets('featuredContent');
        $this->loadWidgets('main');
        $this->loadWidgets('featuredFooter');

        $this->addChild('account/column_left');
            $this->addChild('common/column_left');
            $this->addChild('common/column_right');
            $this->addChild('common/header');
            $this->addChild('common/footer');


        $template = ($this->config->get('default_view_account_edit')) ? $this->config->get('default_view_account_edit') : 'account/edit.tpl';
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/' . $template)) {
            $this->template = $this->config->get('config_template') . '/' . $template;
        } else {
            $this->template = 'choroni/' . $template;
        }

        $this->response->setOutput($this->render(true), $this->config->get('config_compression'));
    }

    private function validate() {
        if (empty($this->request->post['telephone'])) {
            $this->error['telephone'] = $this->language->get('error_telephone');
        }

        if (empty($this->request->post['rif'])) {
            $this->error['rif'] = $this->language->get('error_rif');
        }

        if (empty($this->request->post['company'])) {
            $this->error['company'] = $this->language->get('error_company');
        }

        if (empty($this->request->post['firstname'])) {
            $this->error['firstname'] = $this->language->get('error_firstname');
        }

        if (empty($this->request->post['lastname'])) {
            $this->error['lastname'] = $this->language->get('error_lastname');
        }

        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }

}
