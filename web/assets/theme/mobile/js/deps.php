<?php

$jsPath = str_replace("%theme%", $tpl, $jsPath);
$jsAppPath = str_replace("%theme%", $tpl, $jsAppPath);

$elevate = $jsPath . 'vendor/elevatezoom/jquery.elevateZoom-3.0.8.min.js';
$fancybox = $jsPath . 'vendor/fancybox/jquery.fancybox.pack.js';
$slick = $jsPath . 'vendor/slick.min.js';
$rrssb = $jsPath . 'vendor/rrssb/js/rrssb.min.js';
$neco_wizard = $jsPath . 'vendor/necojs/neco.wizard.min.js';
$neco_form = $jsPath . 'vendor/necojs/neco.form.min.js';
$neco_carousel = $jsPath . 'vendor/necojs/neco.carousel.js';
$jquery_ui = $jsPath . 'vendor/jquery-ui.min.js';
$jquery = $jsPath . 'vendor/jquery.min.js';
$jquery_easin = $jsPath . 'vendor/jquery.easing.1.3.js';
$modernizr = $jsPath . 'vendor/modernizr.min.js';
$theme = $jsPath . 'theme.js';

$jScrollPane = $jsPath . 'vendor/jQuery.jScrollPane/jquery.jscrollpane.min.js';
$mousewheel = $jsPath . 'vendor/jQuery.jScrollPane/jquery.mousewheel.min.js';
$mwheelIntent = $jsPath . 'vendor/jQuery.jScrollPane/mwheelIntent.min.js';

$js_header_assets = array(
    $jquery => '*',
    $modernizr => '*',
);

$js_assets = array(
    $jquery_easin => '*',
    $jsPath . 'vendor/mmenu/jquery.mmenu.oncanvas.js' => '*',
    $theme => '*',
    $elevate => '*',
    $fancybox => '*',
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