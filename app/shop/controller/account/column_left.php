<?php

class ControllerAccountColumnLeft extends Controller {

    protected function index() {
        $this->loadWidgets('column_left');
        

        $Url = new Url($this->registry);

        $this->language->load('account/account');
        
        //load model class to get modules links and submenus for account panel
        $this->load->auto('setting/extension');
        $this->data['modelExtension'] = $this->modelExtension;

        // style files
        $csspath = defined("CDN_CSS") ? CDN_CSS : HTTP_CSS;
        $styles[] = array('media' => 'all', 'href' => $csspath . 'jquery-ui/jquery-ui.min.css');
        $this->styles = array_merge($styles, $this->styles);

        // javascript files
        $jspath = defined("CDN_JS") ? CDN_JS : HTTP_JS;
        $javascripts[] = $jspath . "vendor/jquery-ui.min.js";
        $this->javascripts = array_merge($this->javascripts, $javascripts);

        // SCRIPTS
        $scripts[] = array('id' => 'messageScripts', 'method' => 'ready', 'script' =>
            "var icons = {
            header: 'ui-icon-circle-arrow-e',
            activeHeader: 'ui-icon-circle-arrow-s'
            };
            $( '#accordion' ).accordion({
            icons: icons
            });
            $( '#toggle' ).button().click(function() {
            if ( $( '#accordion' ).accordion( 'option', 'icons' ) ) {
            $( '#accordion' ).accordion( 'option', 'icons', null );
            } else {
            $( '#accordion' ).accordion( 'option', 'icons', icons );
            }
       });");

        $this->scripts = array_merge($this->scripts, $scripts);

        $this->id = 'account_column_left';

        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/account/column_left.tpl')) {
            $this->template = $this->config->get('config_template') . '/account/column_left.tpl';
        } else {
            $this->template = 'choroni/account/column_left.tpl';
        }

        $this->render();
    }

}
