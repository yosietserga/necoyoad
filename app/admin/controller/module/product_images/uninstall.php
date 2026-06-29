<?php
/**
 * ControllerModuleProductImagesUninstall
 * 
 * @package NecoTienda
 * @author Yosiet Serga
 * @copyright Inversiones Necoyoad, C.A.
 * @version 1.0.0
 * @access public
 * @see Controller
 */
class ControllerModuleProductImagesUninstall extends Controller {
	private $error = [];
    private $module = 'product_images';
	
	/**
	 * ControllerModuleProductImagesUninstall::index()
	 * 
	 * @return
	 */
	public function index() {   
		if (!$this->user->hasPermission('modify', 'module/product_images/uninstall')) {
			$this->session->set('error',$this->language->get('error_permission')); ; 
			$this->redirect(Url::createAdminUrl('extension/module'));
		} else {
            $this->load->auto('setting/extension');
            $this->load->auto('setting/setting');
            $this->load->auto('style/widget');
			$this->modelExtension->uninstall('module', 'product_images');
			$this->modelSetting->delete('product_images');
            $this->modelWidget->deleteAll('product_images');
			$this->redirect(Url::createAdminUrl('extension/module'));	
		}
	}
}
