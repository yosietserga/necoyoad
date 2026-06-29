<?php

/**
 * ControllerModulePostListInstall
 * 
 * @package NecoTienda
 * @author Yosiet Serga
 * @copyright Inversiones Necoyoad, C.A.
 * @version 1.0.0
 * @access public
 * @see Controller
 */
class ControllerModulePostListInstall extends Controller {

    private $error = [];
    private $module = 'post_list';

    /**
     * ControllerModulePostListInstall::index()
     * 
     * @return
     */
    public function index() {
        if (!$this->user->hasPermission('modify', 'module/post_list/install')) {
            $this->session->set('error', $this->language->get('error_permission'));
            $this->redirect(Url::createAdminUrl('extension/module'));
        } else {
            /*
              if (file_exists('config.php')) {
              require();
              }
             */
            $this->load->auto('setting/extension');
            $this->load->auto('user/usergroup');
            $this->modelExtension->install('module', 'post_list');

            $this->modelUsergroup->addPermission($this->user->getId(), 'access', 'module/post_list/install');
            $this->modelUsergroup->addPermission($this->user->getId(), 'modify', 'module/post_list/install');

            $this->modelUsergroup->addPermission($this->user->getId(), 'access', 'module/post_list/uninstall');
            $this->modelUsergroup->addPermission($this->user->getId(), 'modify', 'module/post_list/uninstall');

            $this->modelUsergroup->addPermission($this->user->getId(), 'access', 'module/post_list/widget');
            $this->modelUsergroup->addPermission($this->user->getId(), 'modify', 'module/post_list/widget');
            $this->modelUsergroup->addPermission($this->user->getId(), 'delete', 'module/post_list/widget');

            $this->redirect(Url::createAdminUrl('extension/module'));
        }
    }

    /**
     * ControllerModulePostListInstall::validate()
     * 
     * @return
     */
    private function validate() {
        if (!$this->user->hasPermission('modify', 'module/post_list/install')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }

}
