<?php

class ControllerPaymentDebit extends Controller {

    private $error = [];

    public function index() {
        $this->load->language('payment/debit');

        $this->document->title = $this->language->get('heading_title');

        $this->load->auto('setting/setting');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && ($this->validate())) {
            $this->load->auto('setting/setting');

            $this->modelSetting->update('debit', $this->request->post);
            $this->session->set('success', $this->language->get('text_success'));

            if ($_POST['to'] == "saveAndKeep") {
                $this->redirect(Url::createAdminUrl('payment/debit'));
            } else {
                $this->redirect(Url::createAdminUrl('extension/payment'));
            }
        }

        $this->data['heading_title'] = $this->language->get('heading_title');

        if (isset($this->error['warning'])) {
            $this->data['error_warning'] = $this->error['warning'];
        } else {
            $this->data['error_warning'] = '';
        }

        if (isset($this->error['payable'])) {
            $this->data['error_payable'] = $this->error['payable'];
        } else {
            $this->data['error_payable'] = '';
        }

        $this->document->breadcrumbs = [];

        $this->document->breadcrumbs[] = array(
            'href' => Url::createAdminUrl('common/home'),
            'text' => $this->language->get('text_home'),
            'separator' => false
        );

        $this->document->breadcrumbs[] = array(
            'href' => Url::createAdminUrl('extension/payment'),
            'text' => $this->language->get('text_payment'),
            'separator' => ' :: '
        );

        $this->document->breadcrumbs[] = array(
            'href' => Url::createAdminUrl('payment/debit'),
            'text' => $this->language->get('heading_title'),
            'separator' => ' :: '
        );

        $this->data['action'] = Url::createAdminUrl('payment/debit');
        $this->data['cancel'] = Url::createAdminUrl('extension/payment');

        $this->setvar('debit_payable');
        $this->setvar('debit_order_status_id');
        $this->setvar('debit_email_template');
        $this->setvar('debit_geo_zone_id');
        $this->setvar('debit_status');
        $this->setvar('debit_sort_order');

        $this->load->auto('localisation/orderstatus');
        $this->data['order_statuses'] = $this->modelOrderstatus->getAll();

        $this->load->auto('localisation/geozone');
        $this->data['geo_zones'] = $this->modelGeozone->getAll();

        $this->load->model('marketing/newsletter');
        $this->data['newsletters'] = $this->modelNewsletter->getAll();

        $this->template = 'payment/debit.tpl';
        
        $this->children[] = 'common/header';
        $this->children[] = 'common/nav';
        $this->children[] = 'common/footer';
        
        $this->response->setOutput($this->render(true), $this->config->get('config_compression'));
    }

    private function validate() {
        if (!$this->user->hasPermission('modify', 'payment/debit')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }

}
