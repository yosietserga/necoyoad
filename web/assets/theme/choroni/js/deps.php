<?php

/**
 * $cssPath is the http url to the configured template path. For example /web/assets/theme/choroni/js/
 * @var [type]
 */
$jsPath = str_replace("%theme%", $tpl, $jsPath);

/**
 * $jsAppPath is the http url to your JS app 
 * 
 * it can be into the configured template path. For example /web/assets/theme/choroni/js/app/
 * or it can be in another place. For example /web/assets/app/
 * 
 * @var [type]
 */
$jsAppPath = str_replace("%theme%", $tpl, $jsAppPath);

/**
 * $jsSharedPath is the http url to the shared path /web/assets/js/
 * @param $jsSharedPath
 * @var String
 */

$elevate = $jsPath . 'vendor/elevatezoom/jquery.elevateZoom-3.0.8.min.js';
$fancybox = $jsPath . 'vendor/fancybox/jquery.fancybox.pack.js';
$slick = $jsPath . 'vendor/slick.min.js';
$rrssb = $jsPath . 'vendor/rrssb/js/rrssb.min.js';
$neco_wizard = $jsSharedPath . 'necojs/neco.wizard.min.js';
$neco_form = $jsSharedPath . 'necojs/neco.form.min.js';
$neco_carousel = $jsSharedPath . 'necojs/neco.carousel.js';
$jquery_ui = $jsPath . 'vendor/jquery-ui.min.js';
$jquery = $jsPath . 'vendor/jquery.min.js';
$jquery_easin = $jsPath . 'vendor/jquery.easing.1.3.js';
$modernizr = $jsPath . 'vendor/modernizr.min.js';
$theme = $jsPath . 'theme.js';

$jScrollPane = $jsPath . 'vendor/jQuery.jScrollPane/jquery.jscrollpane.min.js';
$mousewheel = $jsPath . 'vendor/jQuery.jScrollPane/jquery.mousewheel.min.js';
$mwheelIntent = $jsPath . 'vendor/jQuery.jScrollPane/mwheelIntent.min.js';

/**
 * $js_assets hold all the scripts files that will be into the generated html 
 * put here your js files that you want to be rendered
 * 
 * This is done to improve the performance, to satisfy Google's insights and to manage
 * better your script structure.
 *
 * The rendered HTML will have all the js files unified and minified, you don't have to do it,
 * the app will do it for you. NO MORE WEBPACKS AND BLOWFISH PLUGINS :)
 * 
 * NOTE: if you need that the file be rendered for one or few controllers, you have
 * to create the index 'routes' with the controller's list
 *
 * NOTE: There exists a magic assets loader using namespace convention, if you are not adding
 * vendor js files and just need to put some custom style for a specific controller, just create
 * a file with the full route name without spaces. For example:
 *
 *      for common/home route, create a js file named commonhome.js (this works for common/home/*)
 *      for store/product route, create a js file named storeproduct.js (this works for store/product/*)
 *
 * If you need for a specific route, you have to create a file with the complete route. For example:
 *
 *      for store/product/quickview, create a js file named storeproductquickview.js
 *
 * And Yes! it works for modules, too! But instead of using a method inside controller, use the view params
 * 
 *      for a module, create a js file named module[module_name].js (this works for module/[module_name]/*)
 *      for a specific view of the module, create a js file named module[module_name][view].js
 *      
 *      example: modulestorelogomycustomview.js 
 *
 * Check the module's views documentation for more help
 *      
 * @var array
 */
$js_assets = array(
    $jquery_easin => '*',
    $jsPath . 'vendor/mmenu/jquery.mmenu.oncanvas.js' => '*',
    $theme => '*',
    $elevate => '*',
    $fancybox => array(
        'store/product',
    ),
    $slick => '*',
    $neco_carousel => array(
        'store/product',
        'content/post',
        'content/page'
    ),
    $rrssb => '*',
    $neco_wizard => array(
        'checkout/cart',
    ),
    $neco_form => array(
        'checkout/cart',
        'account/login',
        'account/register',
        'account/address/update',
        'account/address/insert',
        'account/password',
        'account/message',
        'account/message/create',
        'account/message/sent',
        'checkout/success',
    ),
    $jquery_ui => array(
        'checkout/cart',
        'account/login',
        'account/register',
        'account/address/update',
        'account/address/insert',
        'account/password',
        'checkout/success',
    ),
    $jScrollPane => array(
        'store/search',
    ),
    $mousewheel => array(
        'store/search',
    ),
    $mwheelIntent => array(
        'store/search',
    )
);

if (isset($_GET['admin_tools'])) {
   $js_assets[ $jsSharedPath.'necojs/neco.css.js' ] = '*';
   $js_assets[ $jsSharedPath.'necojs/neco.colorpicker.js' ] = '*';
}


/**
 * $js_header_assets it holds the scripts that will be loaded in the HTML head tag
 * @var array
 */
$js_header_assets = array(
    $jquery => '*',
    $modernizr => '*',
);


/**
 * SOON!
 * $jsx_asset it holds the jsx scripts that will be loaded in the HTML rendered
 * @todo create a php jsx interpreter
 * @var array
 */
//$jsx_asset = array(
    //$jsAppPath.'containers/store/product/form.jsx' => array( 
        //'store/product/insert', 
        //'store/product/update' 
    //),
//);