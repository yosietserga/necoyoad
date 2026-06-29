<?php
/**
 * ControllerModulePostDatePublishedUninstall
 * 
 * @package NecoTienda
 * @author Yosiet Serga
 * @copyright Inversiones Necoyoad, C.A.
 * @version 1.0.0
 * @access public
 * @see Controller
 */
class ControllerModulePostDatePublishedUninstall extends Controller {
	private $error = [];
    private $module = 'post_date_published';
	
	/**
	 * ControllerModulePostDatePublishedUninstall::index()
	 * 
	 * @return
	 */
	public function index() {   
		if (!$this->user->hasPermission('modify', 'module/'. $this->module .'/uninstall')) {
			$this->session->set('error',$this->language->get('error_permission')); ; 
			$this->redirect(Url::createAdminUrl('extension/module'));
		} else {
            $this->load->auto('setting/extension');
            if (count($this->modelExtension->isInstalled($this->module)) >= 1) {
                $this->load->auto('setting/setting');
                $this->load->auto('style/widget');
                $this->modelExtension->uninstall('module', $this->module);
                $this->modelSetting->delete($this->module);
                $this->modelWidget->deleteAll($this->module);
            }
            $this->redirect(Url::createAdminUrl('extension/module'));
		}
	}
}
