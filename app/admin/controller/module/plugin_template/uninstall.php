<?php
/**
 * ControllerModulePluginTemplateUninstall
 * 
 * @package NecoTienda
 * @author Yosiet Serga
 * @copyright Inversiones Necoyoad, C.A.
 * @version 1.0.0
 * @access public
 * @see Controller
 */
class ControllerModulePluginTemplateUninstall extends Controller {
	private $error = [];
    private $module = 'plugin_template';
	
	/**
	 * ControllerModulePluginTemplateUninstall::index()
	 * 
	 * @return
	 */
	public function index() {   
		if (!$this->user->hasPermission('modify', 'module/'. $this->module .'/uninstall')) {
			$this->session->set('error',$this->language->get('error_permission')); ; 
			$this->redirect(Url::createAdminUrl('extension/module'));
		} else {
            $this->load->auto('setting/extension');


            //remove links and submenus from Admin Panel Menu
            $this->modelExtension->removeMenu('admin', $this->module, 'store');
            $this->modelExtension->removeMenu('admin', $this->module, 'custom');

            //remove links and submenus from Account Panel Menu
            $this->modelExtension->removeMenu('account', $this->module, 'orders');
            $this->modelExtension->removeMenu('account', $this->module, 'account');
            $this->modelExtension->removeMenu('account', $this->module, 'custom');

            //remove links and submenus from Login Form Panel Menu
            $this->modelExtension->removePartialTemplate('login_form', $this->module, 'login_form');
            $this->modelExtension->removePartialTemplate('login_form', $this->module, 'account_box');

            if (count($this->modelExtension->isInstalled($this->module)) >= 1) {


                $forms_field_widgets = array(
                    'text',
                    'checkbox',
                    'radio',
                    'number',
                    'hide' ,
                    'password',
                    'select',
                    'textarea',
                    'button',
                    'datepicker',
                    'datefield',
                    'file'
                );

                foreach ($forms_field_widgets as $v) {
                    $this->modelExtension->uninstall('form', $v);
                }

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
