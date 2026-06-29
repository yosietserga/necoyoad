<?php

/**
 * ControllerModuleRoomsInstall
 * 
 * @package NecoTienda
 * @author Yosiet Serga
 * @copyright Inversiones Necoyoad, C.A.
 * @version 1.0.0
 * @access public
 * @see Controller
 */
class ControllerModuleRoomsInstall extends Controller {

    private $error = [];
    private $module = 'rooms';

    /**
     * ControllerModuleRoomsInstall::index()
     * 
     * @return
     */
    public function index() {
        if (!$this->user->hasPermission('modify', 'module/'. $this->module .'/install')) {
            $this->session->set('error', $this->language->get('error_permission'));
            $this->redirect(Url::createAdminUrl('extension/module'));
        } else {
            $this->load->auto('setting/extension');
            if (count($this->modelExtension->isInstalled($this->module)) < 1) {
                $this->load->auto('user/usergroup');
                $this->modelExtension->install('module', $this->module);

                //add links and submenus to Admin Panel Menu
                $this->modelExtension->addMenu('admin', $this->module, 'store', 'admin_menu/store');
                $this->modelExtension->addMenu('admin', $this->module, 'custom', 'admin_menu/custom');

                //add links and submenus to Account Panel Menu
                $this->modelExtension->addMenu('account', $this->module, 'orders', 'account_menu/orders');
                $this->modelExtension->addMenu('account', $this->module, 'custom', 'account_menu/custom');

                //inject partial template inside of login_form module       
                $this->modelExtension->addPartialTemplate('login_form', $this->module, 'login_form', 'account_menu/login_form');
                $this->modelExtension->addPartialTemplate('login_form', $this->module, 'account_box', 'account_menu/account_box');

                $this->modelUsergroup->addPermission($this->user->getId(), 'modify', 'module/'. $this->module .'/install');
                $this->modelUsergroup->addPermission($this->user->getId(), 'modify', 'module/'. $this->module .'/uninstall');

                $this->modelUsergroup->addPermission($this->user->getId(), 'create', 'module/'. $this->module .'/widget');
                $this->modelUsergroup->addPermission($this->user->getId(), 'access', 'module/'. $this->module .'/widget');
                $this->modelUsergroup->addPermission($this->user->getId(), 'modify', 'module/'. $this->module .'/widget');
                $this->modelUsergroup->addPermission($this->user->getId(), 'delete', 'module/'. $this->module .'/widget');

                $this->modelUsergroup->addPermission($this->user->getId(), 'create', 'module/'. $this->module .'/plugin');
                $this->modelUsergroup->addPermission($this->user->getId(), 'access', 'module/'. $this->module .'/plugin');
                $this->modelUsergroup->addPermission($this->user->getId(), 'modify', 'module/'. $this->module .'/plugin');
                $this->modelUsergroup->addPermission($this->user->getId(), 'delete', 'module/'. $this->module .'/plugin');

            }
            $this->redirect(Url::createAdminUrl('extension/module'));
        }
    }

    /**
     * ControllerModuleRoomsInstall::validate()
     * 
     * @return
     */
    private function validate() {
        if (!$this->user->hasPermission('modify', 'module/'. $this->module .'/install')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }

}
