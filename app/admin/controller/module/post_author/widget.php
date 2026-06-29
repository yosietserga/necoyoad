<?php
/**
 * ControllerModulePostAuthorWidget
 * 
 * @package NecoTienda
 * @author Yosiet Serga
 * @copyright Inversiones Necoyoad, C.A.
 * @version 1.0.0
 * @access public
 * @see Controller
 */
class ControllerModulePostAuthorWidget extends Controller {
	private $error = [];
    private $module = 'post_author';
	
	/**
	 * ControllerModulePostAuthorWidget::index()
	 * 
	 * @return
	 */
	public function index() {
        $wc = dirname(__FILE__) .'/../widget_common.php';
        if (!$this->request->hasQuery('name') || !file_exists($wc)) return false;

        include($wc);
	}
}
