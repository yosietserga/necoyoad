<?php

class ControllerShippingPickup extends Controller {

    private $error = [];

    public function index() {
        $this->load->language('shipping/pickup');
        $this->load->auto('setting/setting');
        $this->load->auto('localisation/geozone');
        $this->load->auto('localisation/taxclass');

        $this->document->title = $this->language->get('heading_title');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && ($this->validate())) {
            $this->modelSetting->update('pickup', $this->request->post);

            $this->session->set('success', $this->language->get('text_success'));

            if ($_POST['to'] == "saveAndKeep") {
                $this->redirect(Url::createAdminUrl('shipping/pickup'));
            } else {
                $this->redirect(Url::createAdminUrl('extension/shipping'));
            }
        }

        if (isset($this->error['warning'])) {
            $this->data['error_warning'] = $this->error['warning'];
        } else {
            $this->data['error_warning'] = '';
        }

        $this->document->breadcrumbs = [];
        $this->document->breadcrumbs[] = array(
            'href' => Url::createAdminUrl('common/home'),
            'text' => $this->language->get('text_home'),
            'separator' => false
        );
        $this->document->breadcrumbs[] = array(
            'href' => Url::createAdminUrl('extension/shipping'),
            'text' => $this->language->get('text_shipping'),
            'separator' => ' :: '
        );
        $this->document->breadcrumbs[] = array(
            'href' => Url::createAdminUrl('shipping/pickup'),
            'text' => $this->language->get('heading_title'),
            'separator' => ' :: '
        );

        $this->data['action'] = Url::createAdminUrl('shipping/pickup');
        $this->data['cancel'] = Url::createAdminUrl('extension/shipping');

        if (isset($this->request->post['pickup_geo_zone_id'])) {
            $this->data['pickup_geo_zone_id'] = $this->request->post['pickup_geo_zone_id'];
        } else {
            $this->data['pickup_geo_zone_id'] = $this->config->get('pickup_geo_zone_id');
        }

        if (isset($this->request->post['pickup_status'])) {
            $this->data['pickup_status'] = $this->request->post['pickup_status'];
        } else {
            $this->data['pickup_status'] = $this->config->get('pickup_status');
        }

        if (isset($this->request->post['pickup_sort_order'])) {
            $this->data['pickup_sort_order'] = $this->request->post['pickup_sort_order'];
        } else {
            $this->data['pickup_sort_order'] = $this->config->get('pickup_sort_order');
        }

        $this->data['geo_zones'] = $this->modelGeozone->getAll();

        $template = ($this->config->get('default_admin_view_shipping_pickup')) ? $this->config->get('default_admin_view_shipping_pickup') : 'shipping/pickup.tpl';
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

    private function validate() {
        if (!$this->user->hasPermission('modify', 'shipping/pickup')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }

}
