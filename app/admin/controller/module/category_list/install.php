<?php

/**
 * ControllerModuleCategoryListInstall
 * 
 * @package NecoTienda
 * @author Yosiet Serga
 * @copyright Inversiones Necoyoad, C.A.
 * @version 1.0.0
 * @access public
 * @see Controller
 */
class ControllerModuleCategoryListInstall extends Controller {

    private $error = [];
    private $module = 'category_list';

    /**
     * ControllerModuleCategoryListInstall::index()
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
     * ControllerModuleCategoryListInstall::validate()
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
