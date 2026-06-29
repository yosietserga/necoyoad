<?php

/**
 * ControllerSaleCustomer
 * 
 * @package NecoTienda
 * @author Yosiet Serga
 * @copyright Inversiones Necoyoad, C.A.
 * @version 1.0.0
 * @access public
 * @see Controller
 */
class ControllerStyleEditor extends Controller {

    private $error = [];

    /**
     * ControllerStyleBackgrounds::index()
     * 
     * @see Load
     * @see Document
     * @see Language
     * @see getList
     * @return void 
     */
    public function index() {
        $this->load->library('url');
        $this->data['Url'] = new Url;

        $f = $this->request->getQuery('f');

        if (isset($this->request->get['tpl'])) {
            $this->data['template'] = $template = $this->request->get['tpl'];
        } else {
            $this->data['template'] = $template = $this->config->get('config_template');
        }

        if ($this->request->get['t'] == 'css') {
            if (is_dir(DIR_THEME_ASSETS . $template . '/css/')) {
                $folder = DIR_THEME_ASSETS . $template . '/css/';
            } else {
                $folder = DIR_THEME_ASSETS . 'choroni/css/';
            }
        } elseif ($this->request->get['t'] == 'tpl') {
            if (file_exists(DIR_CATALOG . 'view/theme/' . $template . '/common/home.tpl')) {
                $folder = DIR_CATALOG . 'view/theme/' . $template . '/';
            } else {
                $folder = DIR_CATALOG . 'view/theme/choroni/';
            }
        }

        if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
            $fopen = fopen($folder . $f, 'w+');
            fputs($fopen, html_entity_decode($this->request->post['code']));
            fclose($fopen);
            $this->session->data['success'] = $this->language->get('text_success');
        }

        if (file_exists(DIR_CATALOG . 'view/theme/' . $this->config->get('config_template') . '/common/home.tpl')) {
            $folderTPL = DIR_CATALOG . 'view/theme/' . $this->config->get('config_template') . '/';
        } else {
            $folderTPL = DIR_CATALOG . 'view/theme/choroni/';
        }
        $directories = glob($folderTPL . "*", GLOB_ONLYDIR);
        $this->data['templates'] = [];
        foreach ($directories as $key => $directory) {
            $this->data['views'][$key]['folder'] = basename($directory);
            $files = glob($directory . "/*.tpl", GLOB_NOSORT);
            foreach ($files as $k => $file) {
                $this->data['views'][$key]['files'][$k] = str_replace("\\", "/", $file);
            }
        }

        unset($directories, $files);
        if (is_dir(DIR_THEME_ASSETS . $this->config->get('config_template') . '/css/')) {
            $folderCSS = DIR_THEME_ASSETS . $this->config->get('config_template') . '/css/';
        } else {
            $folderCSS = DIR_THEME_ASSETS . 'choroni/css/';
        }
        if (file_exists($folderCSS . 'theme.css')) {
            $files = glob($folderCSS . "*.css");
            $this->data['css_files'] = [];
            foreach ($files as $key => $file) {
                $this->data['css_files'][] = basename($file);
            }

            unset($directories, $files);
            $directories = glob($folderCSS . "*", GLOB_ONLYDIR);
            if ($directories) {
                foreach ($directories as $ky => $directory) {
                    $files = glob($directory . "/*.css", GLOB_NOSORT);
                    if ($files) {
                        foreach ($files as $k => $file) {
                            $this->data['css_files'][] = basename($directory) . "/" . basename($file);
                        }
                    }
                }
            }
        }

        $this->data['action'] = Url::createAdminUrl("style/editor", array(
                    't' => $this->request->getQuery('t'),
                    'tpl' => $this->request->getQuery('tpl'),
                    'f' => $this->request->getQuery('f'),
                    'menu' => 'apariencia'
        ));
        $this->data['cancel'] = Url::createAdminUrl("style/editor") . "&menu=apariencia";
        $this->data['msg'] = "<b>Haz click sobre un archivo de la izquierda para comenzar a editar</b>";

        if ($this->request->get['f']) {
            if (file_exists($folder . $f)) {
                $this->data['code'] = file_get_contents($folder . $f);
                $this->data['filename'] = $f;
            } else {
                $this->data['msg'] = "<b>El archivo no existe.</b>";
                $this->data['error'] = true;
            }
        } else {
            $this->data['error'] = true;
        }

        $template = ($this->config->get('default_admin_view_style_editor')) ? $this->config->get('default_admin_view_style_editor') : 'style/editor.tpl';
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

    public function file() {
        $data = [];
        $f = urldecode($this->request->getQuery('f'));
        if ($f && file_exists($f)) {
            $data['code'] = file_get_contents($f);
            $data['syntax'] = htmlentities($data['code']);
            $data['syntax'] = str_replace("&gt;", ">", $data['code']);
            $data['syntax'] = str_replace("&quote;", "\"", $data['code']);
            $data['filename'] = basename($f);
            $data['ext'] = pathinfo($f, PATHINFO_EXTENSION);
        }
        $this->load->auto('json');
        $this->response->setOutput(Json::encode($data), $this->config->get('config_compression'));
    }

    public function save() {
        $data = [];
        $f = urldecode($this->request->getQuery('f'));
        $folder = realpath(dirname($f));
        $filename = basename($f);
        $file = $folder .DIRECTORY_SEPARATOR. $filename;

        if (strpos($file, realpath(DIR_THEME_ASSETS)) >= 0 || strpos($file, realpath(DIR_CATALOG . 'view'. DIRECTORY_SEPARATOR .'theme')) >= 0) {
            $fopen = fopen($f, 'w+');
            $code = $this->request->hasPost('code') ? $this->request->getPost('code') : '';
            fputs($fopen, html_entity_decode($code));
            fclose($fopen);
            $data['success'] = $this->language->get('text_success');
            $this->load->auto('json');
            $this->response->setOutput(Json::encode($data), $this->config->get('config_compression'));
        }
    }

}
