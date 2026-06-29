<?php

/**
 * ControllerModuleGoogleAnalyticsPlugin
 * 
 * @package NecoTienda
 * @author Yosiet Serga
 * @copyright Inversiones Necoyoad, C.A.
 * @version 1.0.0
 * @access public
 * @see Controller
 */
class ControllerModuleGoogleAnalyticsPlugin extends Controller {

    private $error = [];
    private $module = 'google_analytics';

    /**
     * ControllerModuleGoogleAnalyticsPlugin::index()
     * 
     * @return
     */
    public function index() {
        $this->load->language('module/'. $this->module);

        $this->document->title = $this->language->get('heading_title');

        $this->load->auto('setting/setting');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && ($this->validate())) {
            $this->modelSetting->update($this->module, $this->request->post);

            $this->session->set('success', $this->language->get('text_success'));

            if ($_POST['to'] == "saveAndKeep") {
                $this->redirect(Url::createAdminUrl('module/'. $this->module .'/plugin'));
            } else {
                $this->redirect(Url::createAdminUrl('extension/module'));
            }
        }

        if (isset($this->error['warning'])) {
            $this->data['error_warning'] = $this->error['warning'];
        } else {
            $this->data['error_warning'] = '';
        }

        if (isset($this->error['code'])) {
            $this->data['error_code'] = $this->error['code'];
        } else {
            $this->data['error_code'] = '';
        }

        $this->document->breadcrumbs = [];

        $this->document->breadcrumbs[] = array(
            'href' => Url::createAdminUrl('common/home'),
            'text' => $this->language->get('text_home'),
            'separator' => false
        );

        $this->document->breadcrumbs[] = array(
            'href' => Url::createAdminUrl('extension/module'),
            'text' => $this->language->get('text_module'),
            'separator' => ' :: '
        );

        $this->document->breadcrumbs[] = array(
            'href' => Url::createAdminUrl('module/'. $this->module),
            'text' => $this->language->get('heading_title'),
            'separator' => ' :: '
        );

        $this->data['action'] = Url::createAdminUrl('module/'. $this->module);

        $this->data['cancel'] = Url::createAdminUrl('extension/module');

        if (isset($this->request->post['google_analytics_code'])) {
            $this->data['google_analytics_code'] = $this->request->post['google_analytics_code'];
        } else {
            $this->data['google_analytics_code'] = $this->config->get('google_analytics_code');
        }

        if (isset($this->request->post['google_analytics_status'])) {
            $this->data['google_analytics_status'] = $this->request->post['google_analytics_status'];
        } else {
            $this->data['google_analytics_status'] = $this->config->get('google_analytics_status');
        }

        $this->data['positions'] = [];

        $this->data['positions'][] = array(
            'position' => 'left',
            'title' => $this->language->get('text_left'),
        );

        $this->data['positions'][] = array(
            'position' => 'right',
            'title' => $this->language->get('text_right'),
        );

        $this->data['positions'][] = array(
            'position' => 'home',
            'title' => $this->language->get('text_home'),
        );

        $this->template = 'module/.tpl';
        $template = ($this->config->get('default_admin_view_module_google_analytics_plugin')) ? $this->config->get('default_admin_view_module_google_analytics_plugin') : 'module/'. $this->module .'/plugin.tpl';
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

    /**
     * ControllerModuleGoogleAnalyticsPlugin::validate()
     * 
     * @return
     */
    private function validate() {
        if (!$this->user->hasPermission('modify', 'module/'. $this->module .'/plugin')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (!$this->request->post['google_analytics_code']) {
            $this->error['code'] = $this->language->get('error_code');
        }

        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }

}
