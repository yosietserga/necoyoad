<?php
$cssPath = str_replace("%theme%", $tpl, HTTP_ADMIN_THEME_CSS);
$cssFolder = str_replace("%theme%", $tpl, DIR_ADMIN_THEME_CSS);
$css_assets = array(
    'vendor/fancybox/jquery.fancybox.css' => array(
        'css' => array(
            'media' => 'all',
            'href' => $cssPath . 'vendor/fancybox/jquery.fancybox.css'
        ),
        'routes'=> '*'
    ),
    'vendor/font-awesome.min.css' => array(
        'css' => array(
            'media' => 'all',
            'href' => $cssPath . 'vendor/font-awesome.min.css'
        ),
        'routes'=> '*'
    ),
    'vendor/jquery.chosen/chosen.min.css' => array(
        'css' => array(
            'media' => 'all',
            'href' => $cssPath . 'vendor/jquery.chosen/chosen.min.css'
        ),
        'routes'=> array(
            'store/category/insert',
            'store/category/update',
            'store/manufacturer/insert',
            'store/manufacturer/update',
            'store/product/insert',
            'store/product/update',
            'content/post_category/insert',
            'content/post_category/update',
            'content/post/insert',
            'content/post/update',
            'content/page/insert',
            'content/page/update',
            'style/widget',
        )
    ),
    'stylewidget.css' => array(
        'css' => array(
            'media' => 'all',
            'href' => $cssPath . 'stylewidget.css'
        ),
        'routes'=> array(
            'store/category/update',
            'store/manufacturer/update',
            'store/product/update',
            'content/post_category/update',
            'content/post/update',
            'content/page/update'
        )
    ),
    'vendor/jquery.sidr.dark.css' => array(
        'css' => array(
            'media' => 'all',
            'href' => $cssPath . 'vendor/jquery.sidr.dark.css'
        ),
        'routes'=> '*'
    ),
    'jquery-ui.min.css' => array(
        'css' => array(
            'media' => 'all',
            'href' => $cssPath . 'jquery-ui.min.css'
        ),
        'routes'=> '*'
    ),
    'neco.form.css' => array(
        'css' => array(
            'media' => 'all',
            'href' => $cssSharedPath . 'neco.form.css'
        ),
        'routes'=> '*'
    ),
    'neco.colorpicker.css' => array(
        'css' => array(
            'media' => 'all',
            'href' => $cssSharedPath . 'neco.colorpicker.css'
        ),
        'routes'=> '*'
    ),
    'joyride.css' => array(
        'css' => array(
            'media' => 'all',
            'href' => $cssPath . 'joyride.css'
        ),
        'routes'=>  array(
            'store/category/insert',
            'store/category/update',
            'store/manufacturer/insert',
            'store/manufacturer/update',
            'store/product/insert',
            'store/product/update',
            'content/post_category/insert',
            'content/post_category/update',
            'content/post/insert',
            'content/post/update',
            'content/page/insert',
            'content/page/update',
        )
    ),
    'vendor.css' => array(
        'css' => array(
            'media' => 'all',
            'href' => $cssPath . 'vendor.css'
        ),
        'routes'=> '*'
    ),
    'main.css' => array(
        'css' => array(
            'media' => 'all',
            'href' => $cssPath . 'main.css'
        ),
        'routes'=> '*'
    ),
);
