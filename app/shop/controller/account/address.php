<?php

class ControllerAccountAddress extends Controller {

    private $error = [];

    public function index() {
        $this->session->clear('object_type');
        $this->session->clear('object_id');
        $this->session->clear('landing_page');
        $this->session->set('landing_page','account/address');

        $Url = new Url($this->registry);
        if (!$this->customer->isLogged()) {
            $this->session->set('redirect', Url::createUrl("account/address"));
            $this->redirect(Url::createUrl("account/login"));
        }
        $this->language->load('account/address');
        $this->document->title = $this->language->get('heading_title');
        $this->load->model('account/address');

        $this->document->breadcrumbs[] = array(
            'href' => Url::createUrl("common/home"),
            'text' => $this->language->get('text_home'),
            'separator' => false
        );

        $this->document->breadcrumbs[] = array(
            'href' => Url::createUrl("account/account"),
            'text' => $this->language->get('text_account'),
            'separator' => $this->language->get('text_separator')
        );

        $this->document->breadcrumbs[] = array(
            'href' => Url::createUrl("account/address"),
            'text' => $this->language->get('heading_title'),
            'separator' => $this->language->get('text_separator')
        );

        $this->data['heading_title'] = $this->language->get('heading_title');

        if (isset($this->error['warning'])) {
            $this->data['error_warning'] = $this->error['warning'];
        } else {
            $this->data['error_warning'] = '';
        }

        if ($this->session->has('success')) {
            $this->data['success'] = $this->session->get('success');
            $this->session->clear('success');
        } else {
            $this->data['success'] = '';
        }

        $this->data['addresses'] = [];

        $results = $this->modelAddress->getAddresses();

        foreach ($results as $result) {
            if ($result['address_format']) {
                $format = $result['address_format'];
            } else {
                $format = '{firstname} {lastname}' . "\n" . '{company}' . "\n" . '{address_1}' . "\n" . '{address_2}' . "\n" . '{city} {postcode}' . "\n" . '{zone}' . "\n" . '{country}';
            }

            $find = array(
                '{firstname}',
                '{lastname}',
                '{company}',
                '{address_1}',
                '{address_2}',
                '{city}',
                '{postcode}',
                '{zone}',
                '{zone_code}',
                '{country}'
            );

            $replace = array(
                'firstname' => $result['firstname'],
                'lastname' => $result['lastname'],
                'company' => $result['company'],
                'address_1' => $result['address_1'],
                'address_2' => $result['address_2'],
                'city' => $result['city'],
                'postcode' => $result['postcode'],
                'zone' => $result['zone'],
                'zone_code' => $result['zone_code'],
                'country' => $result['country']
            );

            if (isset($this->request->post['default'])) {
                $this->data['default'] = $this->request->post['default'];
            } elseif (isset($this->request->get['address_id'])) {
                $this->data['default'] = $this->customer->getAddressId() == $this->request->get['address_id'];
            } else {
                $this->data['default'] = false;
            }

            $this->data['addresses'][] = array(
                'address_id' => $result['address_id'],
                'default' => ($this->customer->getAddressId() == $result['address_id']) ? $this->customer->getAddressId() : null,
                'address' => str_replace(array("\r\n", "\r", "\n"), '<br />', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '<br />', trim(str_replace($find, $replace, $format)))),
                'update' => Url::createUrl("account/address/update", array("address_id" => $result['address_id'])),
                'delete' => Url::createUrl("account/address/delete", array("address_id" => $result['address_id']))
            );
        }

        $this->session->set('landing_page','account/address');
        $this->loadWidgets('featuredContent');
        $this->loadWidgets('main');
        $this->loadWidgets('featuredFooter');

        $this->addChild('account/column_left');
        $this->addChild('common/column_left');
        $this->addChild('common/column_right');
        $this->addChild('common/header');
        $this->addChild('common/footer');


        $template = ($this->config->get('default_view_account_addresses')) ? $this->config->get('default_view_account_addresses') : 'account/addresses.tpl';
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/' . $template)) {
            $this->template = $this->config->get('config_template') . '/' . $template;
        } else {
            $this->template = 'choroni/' . $template;
        }

        $this->response->setOutput($this->render(true), $this->config->get('config_compression'));
    }

    public function insert() {
        $this->session->clear('object_type');
        $this->session->clear('object_id');
        $this->session->clear('landing_page');
        $this->session->set('landing_page','account/address/insert');

        if (!$this->customer->isLogged()) {
            $this->session->set('redirect', Url::createUrl("account/address"));
            $this->redirect(Url::createUrl("account/login"));
        }

        $this->language->load('account/address');

        $this->document->title = $this->language->get('heading_title');

        $this->load->model('account/address');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $this->modelAddress->addAddress($this->request->post);
            $this->session->set('success', $this->language->get('text_insert'));
            $this->redirect(Url::createUrl("account/address"));
        }

        $this->getForm();
    }

    public function update() {
        $this->session->clear('object_type');
        $this->session->clear('object_id');
        $this->session->clear('landing_page');
        $this->session->set('landing_page','account/address/update');

        if (!$this->customer->isLogged()) {
            $this->session->set('redirect', Url::createUrl("account/address"));
            $this->redirect(Url::createUrl("account/login"));
        }

        $this->language->load('account/address');

        $this->document->title = $this->language->get('heading_title');

        $this->load->model('account/address');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {

            $this->modelAddress->editAddress($this->request->get['address_id'], $this->request->post);

            if ($this->session->has('shipping_address_id') && $this->request->get['address_id'] == $this->session->get('shipping_address_id')) {
                $this->session->clear('shipping_methods');
                $this->session->clear('shipping_method');
                $this->tax->setZone($this->request->post['country_id'], $this->request->post['zone_id']);
            }

            if ($this->session->has('payment_address_id') && $this->request->get['address_id'] == $this->session->get('payment_address_id')) {
                $this->session->clear('payment_methods');
                $this->session->clear('payment_method');
            }

            $this->session->set('success', $this->language->get('text_update'));
            $this->redirect(Url::createUrl("account/address"));
        }

        $this->getForm();
    }

    public function delete() {
        if (!$this->customer->isLogged()) {
            $this->session->set('redirect', Url::createUrl("account/address"));
            $this->redirect(Url::createUrl("account/login"));
        }

        $this->language->load('account/address');

        $this->document->title = $this->language->get('heading_title');

        $this->load->model('account/address');

        if (isset($this->request->get['address_id']) && $this->validateDelete()) {
            $this->modelAddress->deleteAddress($this->request->get['address_id']);

            if ($this->session->has('shipping_address_id') && $this->request->get['address_id'] == $this->session->get('shipping_address_id')) {
                $this->session->clear('shipping_address_id');
                $this->session->clear('shipping_methods');
                $this->session->clear('shipping_method');
            }

            if ($this->session->has('payment_address_id') && $this->request->get['address_id'] == $this->session->get('payment_address_id')) {
                $this->session->clear('payment_address_id');
                $this->session->clear('payment_methods');
                $this->session->clear('payment_method');
            }
            $this->session->set('success', $this->language->get('text_delete'));
        }

        $this->redirect(Url::createUrl("account/address"));
    }

    private function getForm() {
        $this->document->breadcrumbs = [];

        $this->document->breadcrumbs[] = array(
            'href' => Url::createUrl("common/home"),
            'text' => $this->language->get('text_home'),
            'separator' => false
        );

        $this->document->breadcrumbs[] = array(
            'href' => Url::createUrl("account/account"),
            'text' => $this->language->get('text_account'),
            'separator' => $this->language->get('text_separator')
        );

        $this->document->breadcrumbs[] = array(
            'href' => Url::createUrl("account/address"),
            'text' => $this->language->get('heading_title'),
            'separator' => $this->language->get('text_separator')
        );

        if (!isset($this->request->get['address_id'])) {
            $this->document->breadcrumbs[] = array(
                'href' => Url::createUrl("account/address/insert"),
                'text' => $this->language->get('text_edit_address'),
                'separator' => $this->language->get('text_separator')
            );
        } else {
            $this->document->breadcrumbs[] = array(
                'href' => Url::createUrl("account/address/update", array("address_id" => $this->request->get['address_id'])),
                'text' => $this->language->get('text_edit_address'),
                'separator' => $this->language->get('text_separator')
            );
        }

        $this->data['heading_title'] = $this->language->get('heading_title');

        $this->data['error_firstname'] = isset($this->error['firstname']) ? $this->error['firstname'] : '';
        $this->data['error_lastname'] = isset($this->error['lastname']) ? $this->error['lastname'] : '';
        $this->data['error_address_1'] = isset($this->error['address_1']) ? $this->error['address_1'] : '';
        $this->data['error_city'] = isset($this->error['city']) ? $this->error['city'] : '';
        $this->data['error_country'] = isset($this->error['country']) ? $this->error['country'] : '';
        $this->data['error_zone'] = isset($this->error['zone']) ? $this->error['zone'] : '';
        $this->data['error_captcha'] = isset($this->error['captcha']) ? $this->error['captcha'] : '';

        if (!isset($this->request->get['address_id'])) {
            $this->data['action'] = Url::createUrl("account/address/insert");
        } else {
            $this->data['action'] = Url::createUrl("account/address/update", array("address_id" => $this->request->get['address_id']));
        }

        if (isset($this->request->get['address_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
            $address_info = $this->modelAddress->getAddress($this->request->get['address_id']);
        }

        $this->setvar('firstname', $address_info, '');
        $this->setvar('lastname', $address_info, '');
        $this->setvar('company', $address_info, '');
        $this->setvar('address_1', $address_info, '');
        $this->setvar('address_2', $address_info, '');
        $this->setvar('postcode', $address_info, '');
        $this->setvar('city', $address_info, '');
        $this->setvar('city', $address_info, $this->config->get('config_country_id'));
        $this->setvar('zone_id', $address_info, 'false');
        $this->setvar('captcha', null, 'false');

        $this->load->model('localisation/country');

        $this->data['countries'] = $this->modelCountry->getCountries();

        if (isset($this->request->post['default'])) {
            $this->data['default'] = $this->request->post['default'];
        } elseif (isset($this->request->get['address_id'])) {
            $this->data['default'] = $this->customer->getAddressId() == $this->request->get['address_id'];
        } else {
            $this->data['default'] = false;
        }

        $this->loadWidgets('featuredContent');
        $this->loadWidgets('main');
        $this->loadWidgets('featuredFooter');

        $this->addChild('account/column_left');
            $this->addChild('common/column_left');
            $this->addChild('common/column_right');
            $this->addChild('common/header');
            $this->addChild('common/footer');


        $template = ($this->config->get('default_view_account_address')) ? $this->config->get('default_view_account_address') : 'account/address.tpl';
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/' . $template)) {
            $this->template = $this->config->get('config_template') . '/' . $template;
        } else {
            $this->template = 'choroni/' . $template;
        }

        $this->response->setOutput($this->render(true), $this->config->get('config_compression'));
    }

    private function validateForm() {
        if (empty($this->request->post['address_1'])) {
            $this->error['address_1'] = $this->language->get('error_address_1');
        }

        if (empty($this->request->post['city'])) {
            $this->error['city'] = $this->language->get('error_city');
        }

        if ($this->request->post['country_id'] == 'false') {
            $this->error['country'] = $this->language->get('error_country');
        }

        if ($this->request->post['zone_id'] == 'false') {
            $this->error['zone'] = $this->language->get('error_zone');
        }

        $this->data['mostrarError'] = $this->validar->mostrarError();

        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }

    private function validateDelete() {
        if ($this->modelAddress->getTotalAddresses() == 1) {
            $this->error['warning'] = $this->language->get('error_delete');
        }

        if ($this->customer->getAddressId() == $this->request->get['address_id']) {
            $this->error['warning'] = $this->language->get('error_default');
        }

        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }

    public function zone() {
        $output = '<option value="false">' . $this->language->get('text_select') . '</option>';

        $this->load->model('localisation/zone');

        $results = $this->modelZone->getZonesByCountryId($this->request->get['country_id']);

        foreach ($results as $result) {
            $output .= '<option value="' . $result['zone_id'] . '"';

            if (isset($this->request->get['zone_id']) && ($this->request->get['zone_id'] == $result['zone_id'])) {
                $output .= ' selected="selected"';
            }

            $output .= '>' . $result['name'] . '</option>';
        }

        if (!$results) {
            if (!$this->request->get['zone_id']) {
                $output .= '<option value="0" selected="selected">' . $this->language->get('text_none') . '</option>';
            } else {
                $output .= '<option value="0">' . $this->language->get('text_none') . '</option>';
            }
        }

        $this->response->setOutput($output, $this->config->get('config_compression'));
    }
}
