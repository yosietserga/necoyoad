<?php

class ControllerShippingFree extends Controller {

    private $error = [];

    public function index() {
        $this->load->language('shipping/free');
        $this->load->auto('setting/setting');
        $this->load->auto('localisation/geozone');
        $this->load->auto('localisation/taxclass');

        $this->document->title = $this->language->get('heading_title');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && ($this->validate())) {
            $this->modelSetting->update('free', $this->request->post);

            $this->session->set('success', $this->language->get('text_success'));

            if ($_POST['to'] == "saveAndKeep") {
                $this->redirect(Url::createAdminUrl('shipping/free'));
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
            'href' => Url::createAdminUrl('shipping/free'),
            'text' => $this->language->get('heading_title'),
            'separator' => ' :: '
        );

        $this->data['action'] = Url::createAdminUrl('shipping/free');
        $this->data['cancel'] = Url::createAdminUrl('extension/shipping');

        if (isset($this->request->post['free_total'])) {
            $this->data['free_total'] = $this->request->post['free_total'];
        } else {
            $this->data['free_total'] = $this->config->get('free_total');
        }

        if (isset($this->request->post['free_geo_zone_id'])) {
            $this->data['free_geo_zone_id'] = $this->request->post['free_geo_zone_id'];
        } else {
            $this->data['free_geo_zone_id'] = $this->config->get('free_geo_zone_id');
        }

        if (isset($this->request->post['free_status'])) {
            $this->data['free_status'] = $this->request->post['free_status'];
        } else {
            $this->data['free_status'] = $this->config->get('free_status');
        }

        if (isset($this->request->post['free_sort_order'])) {
            $this->data['free_sort_order'] = $this->request->post['free_sort_order'];
        } else {
            $this->data['free_sort_order'] = $this->config->get('free_sort_order');
        }

        $this->data['geo_zones'] = $this->modelGeozone->getAll();

        $template = ($this->config->get('default_admin_view_shipping_free')) ? $this->config->get('default_admin_view_shipping_free') : 'shipping/free.tpl';
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
        if (!$this->user->hasPermission('modify', 'shipping/free')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }

}
