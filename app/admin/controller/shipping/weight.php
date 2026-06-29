<?php

class ControllerShippingWeight extends Controller {

    private $error = [];

    public function index() {
        $this->load->language('shipping/weight');
        $this->load->auto('setting/setting');
        $this->load->auto('localisation/geozone');
        $this->load->auto('localisation/taxclass');

        $this->document->title = $this->data['heading_title'] = $this->language->get('heading_title');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && ($this->validate())) {
            $this->modelSetting->update('weight', $this->request->post);
            $this->session->set('success', $this->language->get('text_success'));
            if ($_POST['to'] == "saveAndKeep") {
                $this->redirect(Url::createAdminUrl('shipping/weight'));
            } else {
                $this->redirect(Url::createAdminUrl('extension/shipping'));
            }
        }

        $this->data['error_warning'] = isset($this->error['warning']) ? $this->error['warning'] : '';

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
            'href' => Url::createAdminUrl('shipping/weight'),
            'text' => $this->language->get('heading_title'),
            'separator' => ' :: '
        );

        $this->data['action'] = Url::createAdminUrl('shipping/weight');
        $this->data['cancel'] = Url::createAdminUrl('extension/shipping');

        $geo_zones = $this->modelGeozone->getAll();

        foreach ($geo_zones as $geo_zone) {
            if (isset($this->request->post['weight_' . $geo_zone['geo_zone_id'] . '_rate'])) {
                $this->data['weight_' . $geo_zone['geo_zone_id'] . '_rate'] = $this->request->post['weight_' . $geo_zone['geo_zone_id'] . '_rate'];
            } else {
                $this->data['weight_' . $geo_zone['geo_zone_id'] . '_rate'] = $this->config->get('weight_' . $geo_zone['geo_zone_id'] . '_rate');
            }

            if (isset($this->request->post['weight_' . $geo_zone['geo_zone_id'] . '_status'])) {
                $this->data['weight_' . $geo_zone['geo_zone_id'] . '_status'] = $this->request->post['weight_' . $geo_zone['geo_zone_id'] . '_status'];
            } else {
                $this->data['weight_' . $geo_zone['geo_zone_id'] . '_status'] = $this->config->get('weight_' . $geo_zone['geo_zone_id'] . '_status');
            }
        }

        $this->data['geo_zones'] = $geo_zones;

        if (isset($this->request->post['weight_tax_class_id'])) {
            $this->data['weight_tax_class_id'] = $this->request->post['weight_tax_class_id'];
        } else {
            $this->data['weight_tax_class_id'] = $this->config->get('weight_tax_class_id');
        }

        if (isset($this->request->post['weight_status'])) {
            $this->data['weight_status'] = $this->request->post['weight_status'];
        } else {
            $this->data['weight_status'] = $this->config->get('weight_status');
        }

        if (isset($this->request->post['weight_sort_order'])) {
            $this->data['weight_sort_order'] = $this->request->post['weight_sort_order'];
        } else {
            $this->data['weight_sort_order'] = $this->config->get('weight_sort_order');
        }

        $this->data['tax_classes'] = $this->modelTaxclass->getAll();

        $scripts[] = array('id' => 'scriptForm', 'method' => 'ready', 'script' =>
            "$('.vtabs_page').hide();
            $('#tab_general').show();");

        $scripts[] = array('id' => 'scriptFunctions', 'method' => 'function', 'script' =>
            "function showTab(a) {
                $('.vtabs_page').hide();
                $($(a).attr('data-target')).show();
                console.log(a);
            }
            ");

        $this->scripts = array_merge($this->scripts, $scripts);

        $template = ($this->config->get('default_admin_view_shipping_weight')) ? $this->config->get('default_admin_view_shipping_weight') : 'shipping/weight.tpl';
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
        if (!$this->user->hasPermission('modify', 'shipping/weight')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }

}
