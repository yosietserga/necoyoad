<?php

/**
 * ControllerToolUpdate
 * 
 * @package NecoTienda
 * @author Yosiet Serga
 * @copyright Inversiones Necoyoad, C.A.
 * @version 1.0.0
 * @access public
 * @see Controller
 */
class ControllerToolUpdate extends Controller {

    private $error = [];

    /**
     * ControllerToolUpdate::index()
     * 
     * @see Load
     * @see Document
     * @see Language
     * @see Session
     * @see Response
     * @return void
     */
    public function index() {
        $this->language->load('tool/update');
        $this->document->title = $this->language->get('heading_title');
        $this->data['heading_title'] = $this->language->get('heading_title');

        $this->load->library('backup');
        $this->load->library('update');
        $this->load->model('setting/setting');

        $update = new Update($this->registry);

        $this->data['update_info'] = $update->getInfo();

        if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
            $this->modelSetting->editMaintenance(1);
            $backup = new Backup($this->registry);
            //TODO: respaldar solamente los archivos y las tablas que se van a actualizar
            $backup->run();
            $result = $update->run();
            $this->modelSetting->editMaintenance(0);
            if ($result) {
                //actualizado
            } else {
                if (isset($result['requirements_error'])) {
                    unset($result['requirements_error']);
                    $this->data['error'] = '<ul><li>'. implode('</li><li>', $result) .'</ul>';
                }
                //error, no se actualizo
            }
        }

        $this->document->breadcrumbs = [];
        $this->document->breadcrumbs[] = array(
            'href' => Url::createAdminUrl('common/home'),
            'text' => $this->language->get('text_home'),
            'separator' => false
        );
        $this->document->breadcrumbs[] = array(
            'href' => Url::createAdminUrl('tool/update') . $url,
            'text' => $this->language->get('heading_title'),
            'separator' => ' :: '
        );

        $this->data['action'] = Url::createAdminUrl('tool/update') . $url;

        $template = ($this->config->get('default_admin_view_tool_update')) ? $this->config->get('default_admin_view_tool_update') : 'tool/update.tpl';
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

    public function update() {

        $backup = new Backup($this->registry);
        $update = new Update($this->registry);

        //$backup->run();
        $update->run();
    }

}
