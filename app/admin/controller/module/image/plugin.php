<?php

/**
 * ControllerModuleImagePlugin
 * 
 * @package   
 * @author NecoTienda
 * @copyright Inversiones Necoyoad, C.A.
 * @version 2010
 * @access public
 */
class ControllerModuleImagePlugin extends Controller {

    private $error = [];
    private $module = 'image';

    /**
     * ControllerModuleImagePlugin::index()
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
                $this->redirect(Url::createAdminUrl('module/'. $this->module));
            } else {
                $this->redirect(Url::createAdminUrl('extension/module'));
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

        if (isset($this->request->post['campo1'])) {
            $this->data['campo1'] = $this->request->post['campo1'];
        } else {
            $this->data['campo1'] = $this->config->get('campo1');
        }

        if (isset($this->request->post['status'])) {
            $this->data['status'] = $this->request->post['status'];
        } else {
            $this->data['status'] = $this->config->get('status');
        }

        $scripts[] = array('id' => 'jsForm', 'method' => 'ready', 'script' =>
            "$('#form').ntForm({
                submitButton:false,
                cancelButton:false,
                lockButton:false
            });
            $('textarea').ntTextArea();
            
            var form_clean = $('#form').serialize();  
            
            window.onbeforeunload = function (e) {
                var form_dirty = $('#form').serialize();
                if(form_clean != form_dirty) {
                    return 'There is unsaved form data.';
                }
            };
            
            $('.sidebar .tab').on('click',function(){
                $(this).closest('.sidebar').addClass('show').removeClass('hide').animate({'right':'0px'});
            });
            $('.sidebar').mouseenter(function(){
                clearTimeout($(this).data('timeoutId'));
            }).mouseleave(function(){
                var e = this;
                var timeoutId = setTimeout(function(){
                    if ($(e).hasClass('show')) {
                        $(e).removeClass('show').addClass('hide').animate({'right':'-400px'});
                    }
                }, 600);
                $(this).data('timeoutId', timeoutId); 
            });");

        $scripts[] = array('id' => 'jsFunctions', 'method' => 'function', 'script' =>
            "function saveAndExit() { 
                window.onbeforeunload = null;
                $('#form').append(\"<input type='hidden' name='to' value='saveAndExit'>\").submit(); 
            }
            
            function saveAndKeep() { 
                window.onbeforeunload = null;
                $('#form').append(\"<input type='hidden' name='to' value='saveAndKeep'>\").submit(); 
            }");

        $template = ($this->config->get('default_admin_view_module_'. $this->module .'_plugin')) ? $this->config->get('default_admin_view_module_'. $this->module .'_plugin') : 'module/'. $this->module .'/plugin.tpl';
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
     * ControllerModuleImagePlugin::validate()
     * 
     * @return
     */
    private function validate() {
        if (!$this->user->hasPermission('modify', 'module/'. $this->module)) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }

}
