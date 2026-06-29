<?php

/**
 * ControllerExtensionPayment
 * 
 * @package NecoTienda
 * @author Yosiet Serga
 * @copyright Inversiones Necoyoad, C.A.
 * @version 1.0.0
 * @access public
 * @see Controller
 */
class ControllerExtensionPayment extends Controller {

    /**
     * ControllerExtensionPayment::index()
     * 
     * @see Load
     * @see Language
     * @see Document
     * @see Session
     * @see Response
     * @return void
     */
    public function index() {
        $this->load->language('extension/payment');

        $this->document->title = $this->data['heading_title'] = $this->language->get('heading_title');

        $this->document->breadcrumbs = [];

        $this->document->breadcrumbs[] = array(
            'href' => Url::createAdminUrl('common/home'),
            'text' => $this->language->get('text_home'),
            'separator' => false
        );

        $this->document->breadcrumbs[] = array(
            'href' => Url::createAdminUrl('extension/payment'),
            'text' => $this->language->get('heading_title'),
            'separator' => ' :: '
        );

        if ($this->session->has('success')) {
            $this->data['success'] = $this->session->get('success');

            $this->session->clear('success');
        } else {
            $this->data['success'] = '';
        }

        if ($this->session->has('error')) {
            $this->data['error'] = $this->session->get('error');

            $this->session->clear('success');
        } else {
            $this->data['error'] = '';
        }

        // SCRIPTS
        $scripts[] = array('id' => 'sortable', 'method' => 'ready', 'script' =>
            "$('#gridWrapper').load('" . Url::createAdminUrl("extension/payment/grid") . "',function(e){
                $('#gridPreloader').hide();
                $('#list tbody').sortable({
                    opacity: 0.6, 
                    cursor: 'move',
                    handle: '.move',
                    update: function() {
                        $.ajax({
                            'type':'post',
                            'dateType':'json',
                            'url':'" . Url::createAdminUrl("extension/payment/sortable") . "',
                            'data': $(this).sortable('serialize'),
                            'success': function(data) {
                                if (data > 0) {
                                    var msj = '<div class=\"success\">Se han ordenado los objetos correctamente</div>';
                                } else {
                                    var msj = '<div class=\"warning\">Hubo un error al intentar ordenar los objetos, por favor intente m&aacute;s tarde</div>';
                                }
                                $('#msg').append(msj).delay(3600).fadeOut();
                            }
                        });
                    }
                }).disableSelection();
                $('.move').css('cursor','move');
            });
                
            $('#formFilter').ntForm({
                lockButton:false,
                ajax:true,
                type:'get',
                dataType:'html',
                url:'" . Url::createAdminUrl("extension/payment/grid") . "',
                beforeSend:function(){
                    $('#gridWrapper').hide();
                    $('#gridPreloader').show();
                },
                success:function(data){
                    $('#gridPreloader').hide();
                    $('#gridWrapper').html(data).show();
                }
            });");

        $this->scripts = array_merge($this->scripts, $scripts);

        $template = ($this->config->get('default_admin_view_payment')) ? $this->config->get('default_admin_view_payment') : 'extension/payment.tpl';
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
     * ControllerExtensionPayment::grid()
     * 
     * @see Load
     * @see Language
     * @see Document
     * @see Session
     * @see Response
     * @return void
     */
    public function grid() {
        $this->load->language('extension/payment');

        $this->data['text_no_results'] = $this->language->get('text_no_results');
        $this->data['text_confirm'] = $this->language->get('text_confirm');

        $this->data['column_name'] = $this->language->get('column_name');
        $this->data['column_status'] = $this->language->get('column_status');
        $this->data['column_sort_order'] = $this->language->get('column_sort_order');
        $this->data['column_action'] = $this->language->get('column_action');

        $extensions = $this->modelExtension->getInstalled('payment');

        $this->data['extensions'] = [];

        $files = glob(DIR_APPLICATION . 'controller/payment/*.php');

        if ($files) {
            $i = str_replace('%theme%',$this->config->get('config_admin_template'),HTTP_ADMIN_THEME_IMAGE);
            foreach ($files as $file) {
                $extension = basename($file, '.php');

                $this->load->language('payment/' . $extension);

                $action = [];

                if (!in_array($extension, $extensions)) {
                    $action[] = array(
                        'action' => 'install',
                        'img' => $i.'install.png',
                        'text' => $this->language->get('text_install'),
                        'href' => Url::createAdminUrl('extension/payment/install') . '&extension=' . $extension
                    );
                } else {
                    $action[] = array(
                        'action' => 'edit',
                        'img' =>  $i .'edit.png',
                        'text' => $this->language->get('text_edit'),
                        'href' => Url::createAdminUrl('payment/' . $extension . '')
                    );
                    $action[] = array(
                        'action' => 'install',
                        'img' => $i.'uninstall.png',
                        'text' => $this->language->get('text_uninstall'),
                        'href' => Url::createAdminUrl('extension/payment/uninstall') . '&extension=' . $extension
                    );
                }

                $text_link = $this->language->get('text_' . $extension);

                if ($text_link != 'text_' . $extension) {
                    $link = $this->language->get('text_' . $extension);
                } else {
                    $link = '';
                }

                $this->data['extensions'][] = array(
                    'extension' => $extension,
                    'name' => $this->language->get('heading_title'),
                    'link' => $link,
                    'status' => $this->config->get($extension . '_status') ? $this->language->get('text_enabled') : $this->language->get('text_disabled'),
                    'sort_order' => $this->config->get($extension . '_sort_order'),
                    'action' => $action
                );
            }
        }

        $template = ($this->config->get('default_admin_view_payment_grid')) ? $this->config->get('default_admin_view_payment_grid') : 'extension/payment_grid.tpl';
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_admin_template') . '/'. $template)) {
            $this->template = $this->config->get('config_admin_template') . '/' . $template;
        } else {
            $this->template = 'default/' . $template;
        }

        $this->response->setOutput($this->render(true), $this->config->get('config_compression'));
    }

    /**
     * ControllerExtensionPayment::install()
     * 
     * @see Load
     * @see Language
     * @see Document
     * @see Session
     * @see Response
     * @return void
     */
    public function install() {
        if (!$this->user->hasPermission('modify', 'extension/payment')) {
            $this->session->set('error', $this->language->get('error_permission'));
            ;

            $this->redirect(Url::createAdminUrl('extension/payment'));
        } else {
            $this->modelExtension->install('payment', $this->request->get['extension']);

            $this->load->auto('user/usergroup');

            $this->modelUsergroup->addPermission($this->user->getId(), 'access', 'payment/' . $this->request->get['extension']);
            $this->modelUsergroup->addPermission($this->user->getId(), 'modify', 'payment/' . $this->request->get['extension']);

            $this->redirect(Url::createAdminUrl('extension/payment'));
        }
    }

    /**
     * ControllerExtensionPayment::uninstall()
     * 
     * @see Load
     * @see Language
     * @see Document
     * @see Session
     * @see Response
     * @return void
     */
    public function uninstall() {
        if (!$this->user->hasPermission('modify', 'extension/payment')) {
            $this->session->set('error', $this->language->get('error_permission'));
            ;

            $this->redirect(Url::createAdminUrl('extension/payment'));
        } else {
            $this->modelExtension->uninstall('payment', $this->request->get['extension']);

            $this->modelSetting->delete($this->request->get['extension']);

            $this->redirect(Url::createAdminUrl('extension/payment'));
        }
    }

    /**
     * ControllerCatalogCategory::sortable()
     * ordenar el listado actualizando la posici�n de cada objeto
     * @return boolean
     * */
    public function sortable() {
        $this->load->auto('setting/setting');
        $data = [];
        $i = 0;
        foreach ($_POST as $key => $value) {
            if ($key != "tr") {
                $config_key = str_replace("tr_", "", $key);
                $config_key .= $value;
                $data[$i]['group'] = $config_key;
            } else {
                foreach ($_POST['tr'] as $v) {
                    $data[$v]['group'] = $v;
                }
            }
            $i++;
        }

        $result = $this->modelSetting->sortExtensions($data);
        if ($result) {
            echo 1;
        } else {
            echo 0;
        }
    }

}
