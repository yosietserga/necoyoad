<?php

/**
 * ControllerStyleTemplate
 * 
 * @package NecoTienda
 * @author Yosiet Serga
 * @copyright Inversiones Necoyoad, C.A.
 * @version 1.0.0
 * @access public
 * @see Controller
 */
class ControllerStyleTemplate extends Controller {

    private $error = [];

    /**
     * ControllerStyleTemplate::index()
     * 
     * @see Load
     * @see Document
     * @see Language
     * @see getList
     * @return void
     */
    public function index() {
        $this->load->auto('json');
        $this->load->auto('xhttp/xhttp');
        $handler = new xhttp();
        $resp = $handler->fetch('https://www.necotienda.org/api/index.php?r=style/template/get');
        $response = Json::decode($resp['body']);
        if ($response['response_code']===200) {
            $this->data['templates'] = $response['data']['data'];
        }
        
        $template = ($this->config->get('default_admin_view_style_template_list')) ? $this->config->get('default_admin_view_style_template_list') : 'style/template_list.tpl';
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
    
    public function install() {}
    public function uninstall() {}
    public function update() {}
    public function buy() {}
    public function download() {}
}
