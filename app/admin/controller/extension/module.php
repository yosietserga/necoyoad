<?php

/**
 * ControllerExtensionModule
 * 
 * @package NecoTienda
 * @author Yosiet Serga
 * @copyright Inversiones Necoyoad, C.A.
 * @version 1.0.0
 * @access public
 * @see Controller
 */
class ControllerExtensionModule extends Controller {

    /**
     * ControllerExtensionModule::index()
     * 
     * @see Load
     * @see Document
     * @see Response
     * @see Session
     * @see Language
     * @see Load
     * @see Load
     * @return void
     */
    public function index() {
        $this->load->language('extension/module');

        $this->document->title = $this->data['heading_title'] = $this->language->get('heading_title');

        $this->data['button_insert'] = $this->language->get('button_insert');
        $this->data['button_import'] = $this->language->get('button_import');

        $this->data['insert'] = Url::createAdminUrl("extension/module/insert");
        $this->data['import'] = Url::createAdminUrl("extension/module/import");

        $this->document->breadcrumbs = [];

        $this->document->breadcrumbs[] = array(
            'href' => Url::createAdminUrl('common/home'),
            'text' => $this->language->get('text_home'),
            'separator' => false
        );

        $this->document->breadcrumbs[] = array(
            'href' => Url::createAdminUrl('extension/module'),
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
            "$('#gridWrapper').load('" . Url::createAdminUrl("extension/module/grid") . "',function(e){
                $('#gridPreloader').hide();
                $('#list tbody').sortable({
                    opacity: 0.6, 
                    cursor: 'move',
                    handle: '.move',
                    update: function() {
                        $.ajax({
                            'type':'post',
                            'dateType':'json',
                            'url':'" . Url::createAdminUrl("extension/shipping/sortable") . "',
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
                url:'" . Url::createAdminUrl("extension/module/grid") . "',
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

        $template = ($this->config->get('default_admin_view_module')) ? $this->config->get('default_admin_view_module') : 'extension/module.tpl';
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
     * ControllerExtensionModule::grid()
     * 
     * @see Load
     * @see Document
     * @see Response
     * @see Session
     * @see Language
     * @see Load
     * @see Load
     * @return void
     */
    public function grid() {
        $this->load->language('extension/module');

        $filter_name = !empty($this->request->get['filter_name']) ? $this->request->get['filter_name'] : "";

        $extensions = $this->modelExtension->getInstalled('module');
        $this->data['extensions'] = [];
        $modules = glob(DIR_APPLICATION . "controller/module/$filter_name*", GLOB_ONLYDIR);
        if ($modules) {
            $i = str_replace('%theme%',$this->config->get('config_admin_template'),HTTP_ADMIN_THEME_IMAGE);
            foreach ($modules as $module) {
                $extension = basename($module, 'plugin.php');
                $this->load->language('module/' . $extension);
                $action = [];

                if (!in_array($extension, $extensions)) {
                    $action[] = array(
                        'action' => 'install',
                        'img' => $i .'install.png',
                        'text' => $this->language->get('text_install'),
                        'href' => Url::createAdminUrl("extension/module/install", array('module'=>$extension))
                    );
                } else {
                    if (file_exists(DIR_APPLICATION . "controller/module/$extension/plugin.php")) {
                        $action[] = array(
                            'action' => 'edit',
                            'img' =>  $i .'edit.png',
                            'text' => $this->language->get('text_edit'),
                            'href' => Url::createAdminUrl('module/' . $extension . '/plugin')
                        );
                        $status = $this->config->get($extension . '_status') ? 'activate' : 'deactivate';
                        $action[] = array(
                            'action' => $status,
                            'img' => $i .$status . '.png',
                            'text' => $this->config->get($extension . '_status') ? $this->language->get('text_enabled') : $this->language->get('text_disabled'),
                            'href' => Url::createAdminUrl('extension/module/' . $status) . '&extension=' . $extension
                        );
                    }
                    $action[] = array(
                        'action' => 'install',
                        'img' => $i .'uninstall.png',
                        'text' => $this->language->get('text_uninstall'),
                        'href' => Url::createAdminUrl("extension/module/uninstall", array('module'=>$extension))
                    );
                }

                $this->data['modules'][] = array(
                    'module' => $module,
                    'name' => $this->language->get('heading_title'),
                    'status' => $this->config->get($extension . '_status') ? $this->language->get('text_enabled') : $this->language->get('text_disabled'),
                    'action' => $action
                );
            }
        }

        $template = ($this->config->get('default_admin_view_module_grid')) ? $this->config->get('default_admin_view_module_grid') : 'extension/module_grid.tpl';
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_admin_template') . '/'. $template)) {
            $this->template = $this->config->get('config_admin_template') . '/' . $template;
        } else {
            $this->template = 'default/' . $template;
        }

        $this->response->setOutput($this->render(true), $this->config->get('config_compression'));
    }

    /**
     * ControllerExtensionModule::install()
     * 
     * @see Load
     * @see Redirect
     * @see Session
     * @see Language
     * @see Load
     * @return void
     */
    public function install() {
        $this->load->auto('setting/extension');
        $this->load->auto('user/usergroup');

        $this->modelUsergroup->addPermission($this->user->getId(), 'access', 'module/'. $this->request->getQuery('module'));
        $this->modelUsergroup->addPermission($this->user->getId(), 'modify', 'module/'. $this->request->getQuery('module'));

        $this->modelUsergroup->addPermission($this->user->getId(), 'access', 'module/'. $this->request->getQuery('module') .'/install');
        $this->modelUsergroup->addPermission($this->user->getId(), 'modify', 'module/'. $this->request->getQuery('module') .'/install');

        $this->modelUsergroup->addPermission($this->user->getId(), 'access', 'module/'. $this->request->getQuery('module') .'/uninstall');
        $this->modelUsergroup->addPermission($this->user->getId(), 'modify', 'module/'. $this->request->getQuery('module') .'/uninstall');

        $this->modelUsergroup->addPermission($this->user->getId(), 'access', 'module/'. $this->request->getQuery('module') .'/widget');
        $this->modelUsergroup->addPermission($this->user->getId(), 'modify', 'module/'. $this->request->getQuery('module') .'/widget');
        $this->modelUsergroup->addPermission($this->user->getId(), 'delete', 'module/'. $this->request->getQuery('module') .'/widget');

        $this->modelUsergroup->addPermission($this->user->getId(), 'access', 'module/'. $this->request->getQuery('module') .'/plugin');
        $this->modelUsergroup->addPermission($this->user->getId(), 'modify', 'module/'. $this->request->getQuery('module') .'/plugin');
        $this->modelUsergroup->addPermission($this->user->getId(), 'delete', 'module/'. $this->request->getQuery('module') .'/plugin');

        $this->session->set('success', $this->language->get('Module installed succesfully!'));
        $this->redirect(Url::createAdminUrl('module/'. $this->request->getQuery('module') .'/install'));
    }

    /**
     * ControllerExtensionModule::uninstall()
     * 
     * @see Load
     * @see Redirect
     * @see Session
     * @see Language
     * @see Load
     * @return void
     */
    public function uninstall() {
        $this->load->auto('setting/extension');
        $this->load->auto('setting/setting');
        $this->load->auto('style/widget');

        $this->modelExtension->uninstall('module', $this->request->getQuery('module'));
        $this->modelSetting->delete($this->request->getQuery('module'));
        $this->modelWidget->deleteAll($this->request->getQuery('module'));

        $this->redirect(Url::createAdminUrl('module/'. $this->request->getQuery('module') .'/uninstall'));

    }

    /**
     * ControllerExtensionModule::generate()
     *
     * @see Load
     * @see Redirect
     * @see Session
     * @see Language
     * @see Load
     * @return void
     */
    public function generate() {
        if (!$this->user->hasPermission('modify', 'extension/module')) {
            $this->session->set('error', $this->language->get('error_permission'));
            $this->redirect(Url::createAdminUrl('extension/module'));
        } else {
            $this->load->auto('setting/extension');

            $this->data['cancel'] = Url::createAdminUrl('extension/module');
            if ($this->request->hasPost('module')) {
                $module = trim(strtolower($this->request->getPost('module')));

                if($module !== mb_convert_encoding( mb_convert_encoding($module, 'UTF-32', 'UTF-8'), 'UTF-8', 'UTF-32') )
                    $module = mb_convert_encoding($module, 'UTF-8', mb_detect_encoding($module));
                $module = htmlentities($module, ENT_NOQUOTES, 'UTF-8');
                $module = preg_replace('`&([a-z]{1,2})(acute|uml|circ|grave|ring|cedil|slash|tilde|caron|lig);`i', '\1', $module);
                $module = html_entity_decode($module, ENT_NOQUOTES, 'UTF-8');
                $module = preg_replace(array('`[^a-z0-9]`i','`[-]+`'), '_', $module);
                $module = strtolower( trim($module, '-') );
                
                if (!empty($module) && count($this->modelExtension->isInstalled($module)) < 1) {
                    $admin_controller = DIR_CONTROLLER . 'module/'. $module .'/';
                    $admin_language = DIR_LANGUAGE . 'spanish/module/' . $module .'.php';
                    $admin_view = DIR_TEMPLATE . $this->config->get('config_admin_template') . '/module/' . $module;

                    $this->copyFiles(DIR_CONTROLLER . 'module/plugin_template/', $admin_controller);
                    $this->copyFiles(DIR_LANGUAGE . 'spanish/module/plugin_template.php', $admin_language);
                    $this->copyFiles(DIR_TEMPLATE . $this->config->get('config_admin_template') . '/module/plugin_template', $admin_view);

                    $shop_controller = DIR_CATALOG . 'controller/module/'. $module .'.php';
                    $shop_language = DIR_CATALOG . 'language/spanish/module/'. $module .'.php';
                    $shop_view = DIR_CATALOG . 'view/theme/' . $this->config->get('config_template') . '/module/' . $module;

                    $this->copyFiles(DIR_CATALOG . 'controller/module/plugin_template.php', $shop_controller);
                    $this->copyFiles(DIR_CATALOG . 'language/spanish/module/plugin_template.php', $shop_language);
                    $this->copyFiles(DIR_CATALOG . 'view/theme/' . $this->config->get('config_template') . '/module/plugin_template*.tpl', $shop_view);
                    
                    $sources = array(
                        $admin_controller,
                        $shop_controller,
                        $admin_view,
                        $shop_view,
                        $admin_language,
                        $shop_language,
                    );

                    $classname = ucwords(str_replace(array('_', '-'), ' ', $module));
                    
                    $this->findAndReplace('plugin_template', $module, $sources);
                    $this->findAndReplace('Plugin Template', $classname, $sources);
                    $this->findAndReplace('PluginTemplate', str_replace(' ', '', $classname), $sources);

                    $this->redirect(Url::createAdminUrl('extension/module'));
                }
            }

            $this->children[] = 'common/header';
            $this->children[] = 'common/nav';
            $this->children[] = 'common/footer';

            $template = ($this->config->get('default_admin_view_module_generate_module_form')) ? $this->config->get('default_admin_view_module_generate_module_form') : 'extension/module_generate_form.tpl';
            if (file_exists(DIR_TEMPLATE . $this->config->get('config_admin_template') . '/'. $template)) {
                $this->template = $this->config->get('config_admin_template') . '/' . $template;
            } else {
                $this->template = 'default/' . $template;
            }

            $this->response->setOutput($this->render(true), $this->config->get('config_compression'));
        }
    }

    private array $memo = [];
    private function findAndReplace($needle, $haystack, $source) {

        if (is_array($source)) {
            foreach ($source as $src) {
                if (in_array($src, $this->memo)) continue;
                $this->memo[] = $src;

                $this->findAndReplace($needle, $haystack, $src);
            }
        } else {
            if (is_dir($source)) {
                if (!strrpos($source, '/')) $source .= '/';
                
                $files = glob($source .'*', GLOB_NOSORT);
                foreach ($files as $file) {
                    if (in_array($file, $this->memo)) continue;
                    $this->memo[] = $file;

                    if (is_dir($file)) {
                        $this->findAndReplace($needle, $haystack, $source);
                    } elseif (file_exists($file)) {
                        $text = file_get_contents($file);
                        $text = str_replace($needle, $haystack, $text);
                        $f = fopen($file, 'w+');
                        fwrite($f, $text);
                        fclose($f);
                    }
                }
            } elseif (file_exists($source)) {
                $text = file_get_contents($source);
                $text = str_replace($needle, $haystack, $text);
                $f = fopen($source, 'w+');
                fwrite($f, $text);
                fclose($f);
            }
        }
    }

    private function copyFiles($src = null, $dest = null) {
        if (!isset($src) || !isset($dest) || !file_exists($src))
            return false;
        
        if (is_dir($src)) {
            mkdir($dest, 0755);
            foreach (scandir($src) as $item) {
                if ($item == '.' || $item == '..') continue;
                
                if(!$this->copyFiles($src . DIRECTORY_SEPARATOR . $item, $dest . DIRECTORY_SEPARATOR . $item))
                    return false;
            }
        } else {
            if (strpos($src, '*') !== false) {
                $files = glob($src);
                foreach ($files as $file) {
                    $filename = pathinfo($file, PATHINFO_BASENAME );
                    if(!$this->copyFiles($file, $dest . $filename))
                        return false;
                    //return $this->copyFiles($file, $dest . DIRECTORY_SEPARATOR . $filename);
                }
            } else {
                copy($src, $dest);
            }
        }
        return true;
    }
}
