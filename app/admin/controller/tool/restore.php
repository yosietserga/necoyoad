<?php

/**
 * ControllerToolRestore
 * 
 * @package NecoTienda
 * @author Yosiet Serga
 * @copyright Inversiones Necoyoad, C.A.
 * @version 1.0.0
 * @access public
 * @see Controller
 */
class ControllerToolRestore extends Controller {

    private $error = [];

    /**
     * ControllerToolRestore::index()
     * 
     * @see Load
     * @see Document
     * @see Response
     * @see Session
     * @see Request
     * @see Language
     * @return void 
     */
    public function index() {
        $this->document->title = $this->language->get('heading_title');
        if ($this->request->server['REQUEST_METHOD'] == 'POST' && $this->validate()) {
            if (is_uploaded_file($this->request->files['import']['tmp_name'])) {
                $content = file_get_contents($this->request->files['import']['tmp_name']);
            } else {
                $content = false;
            }

            if ($content) {
                $this->modelBackup->restore($content);

                $this->session->set('success', $this->language->get('text_success'));

                $this->redirect(Url::createAdminUrl('tool/restore'));
            } else {
                $this->error['warning'] = $this->language->get('error_empty');
            }
        }

        $this->data['heading_title'] = $this->language->get('heading_title');

        $this->data['entry_restore'] = $this->language->get('entry_restore');

        $this->data['help_restore'] = $this->language->get('help_restore');

        $this->data['button_restore'] = $this->language->get('button_restore');

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
            'href' => Url::createAdminUrl('tool/restore'),
            'text' => 'Restaurar',
            'separator' => ' :: '
        );

        $this->data['restore'] = Url::createAdminUrl('tool/restore');

        $template = ($this->config->get('default_admin_view_tool_restore')) ? $this->config->get('default_admin_view_tool_restore') : 'tool/restore.tpl';
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
     * ControllerToolRestore::validate()
     * 
     * @return
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
