<?php

class ControllerShippingItem extends Controller {

    private $error = [];

    public function index() {
        $this->load->language('shipping/item');
        $this->load->auto('setting/setting');
        $this->load->auto('localisation/geozone');
        $this->load->auto('localisation/taxclass');

        $this->document->title = $this->language->get('heading_title');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && ($this->validate())) {
            $this->modelSetting->update('item', $this->request->post);

            $this->session->set('success', $this->language->get('text_success'));

            if ($_POST['to'] == "saveAndKeep") {
                $this->redirect(Url::createAdminUrl('shipping/item'));
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
            'href' => Url::createAdminUrl('shipping/item'),
            'text' => $this->language->get('heading_title'),
            'separator' => ' :: '
        );

        $this->data['action'] = Url::createAdminUrl('shipping/item');
        $this->data['cancel'] = Url::createAdminUrl('extension/shipping');

        if (isset($this->request->post['item_cost'])) {
            $this->data['item_cost'] = $this->request->post['item_cost'];
        } else {
            $this->data['item_cost'] = $this->config->get('item_cost');
        }

        if (isset($this->request->post['item_tax_class_id'])) {
            $this->data['item_tax_class_id'] = $this->request->post['item_tax_class_id'];
        } else {
            $this->data['item_tax_class_id'] = $this->config->get('item_tax_class_id');
        }

        if (isset($this->request->post['item_geo_zone_id'])) {
            $this->data['item_geo_zone_id'] = $this->request->post['item_geo_zone_id'];
        } else {
            $this->data['item_geo_zone_id'] = $this->config->get('item_geo_zone_id');
        }

        if (isset($this->request->post['item_status'])) {
            $this->data['item_status'] = $this->request->post['item_status'];
        } else {
            $this->data['item_status'] = $this->config->get('item_status');
        }

        if (isset($this->request->post['item_sort_order'])) {
            $this->data['item_sort_order'] = $this->request->post['item_sort_order'];
        } else {
            $this->data['item_sort_order'] = $this->config->get('item_sort_order');
        }

        $this->data['tax_classes'] = $this->modelTaxclass->getAll();
        $this->data['geo_zones'] = $this->modelGeozone->getAll();

        $template = ($this->config->get('default_admin_view_shipping_item')) ? $this->config->get('default_admin_view_shipping_item') : 'shipping/item.tpl';
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
        if (!$this->user->hasPermission('modify', 'shipping/item')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }

}
