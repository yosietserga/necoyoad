<?php

/**
 * ControllerToolBackup
 * 
 * @package NecoTienda
 * @author Yosiet Serga
 * @copyright Inversiones Necoyoad, C.A.
 * @version 1.0.0
 * @access public
 * @see Controller
 */
class ControllerToolBackup extends Controller {

    private $error = [];

    /**
     * ControllerToolBackup::index()
     * 
     * @see Load
     * @see Document
     * @see Language
     * @see Session
     * @see Response
     * @return void
     */
    public function index() {
        $this->document->title = $this->language->get('heading_title');
        $this->data['heading_title'] = $this->language->get('heading_title');

        $this->data['text_select_all'] = $this->language->get('text_select_all');
        $this->data['text_unselect_all'] = $this->language->get('text_unselect_all');

        $this->data['entry_backup'] = $this->language->get('entry_backup');

        $this->data['help_backup'] = $this->language->get('help_backup');

        $this->data['button_backup'] = $this->language->get('button_backup');

        $this->data['tab_general'] = $this->language->get('tab_general');

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

        $this->document->breadcrumbs = [];

        $this->document->breadcrumbs[] = array(
            'href' => Url::createAdminUrl('common/home'),
            'text' => $this->language->get('text_home'),
            'separator' => false
        );

        $this->document->breadcrumbs[] = array(
            'href' => Url::createAdminUrl('tool/backup'),
            'text' => $this->language->get('heading_title'),
            'separator' => ' :: '
        );

        $this->data['backup'] = Url::createAdminUrl('tool/backup/backup');
        $this->data['tables'] = $this->modelBackup->getTables();

        $template = ($this->config->get('default_admin_view_tool_backup')) ? $this->config->get('default_admin_view_tool_backup') : 'tool/backup.tpl';
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
     * ControllerToolBackup::backup()
     * 
     * @see Load
     * @see Response
     * @return mixed
     */
    public function backup() {
        if ($this->request->server['REQUEST_METHOD'] == 'POST' && $this->validate()) {
            $this->response->addheader('Pragma: public');
            $this->response->addheader('Expires: 0');
            $this->response->addheader('Content-Description: File Transfer');
            $this->response->addheader('Content-Type: application/octet-stream');
            $this->response->addheader('Content-Disposition: attachment; filename=backup_' . date('d') . '_' . date('m') . '_' . date('Y') . '_' . date('h') . date('i') . date('s') . '.sql');
            $this->response->addheader('Content-Transfer-Encoding: binary');
            $this->response->setOutput($this->modelBackup->backup($this->request->post['backup']));
        } else {
            return $this->forward('error/permission');
        }
    }

    /**
     * ControllerToolBackup::validate()
     * 
     * @see User
     * @see Language
     * @return bool
     */
    private function validate() {
        if (!$this->user->hasPermission('modify', 'tool/backup')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }
}
