<?php
$cssPath = str_replace("%theme%", $tpl, $cssPath);

$css_assets = array(
    'icons/fontawesome-icons.css' => array(
        'css' => array(
            'media' => 'all',
            'href' => $cssSharedPath . 'icons/fontawesome-icons.css'
        ),
        'routes'=> '*'
    ),
    'animate.css' => array(
        'css' => array(
            'media' => 'all',
            'href' => $cssSharedPath . 'animate.css'
        ),
        'routes'=> '*'
    ),
    'jquery-ui/jquery-ui.min.css' => array(
        'css' => array(
            'media' => 'all',
            'href' => $cssSharedPath . 'jquery-ui/jquery-ui.min.css'
        ),
        'routes'=> array(
            'account/login',
            'account/register',
            'account/address/update',
            'account/address/insert',
            'account/password'
        )
    ),
    'slick.css' => array(
        'css' => array(
            'media' => 'all',
            'href' => $cssPath . 'vendor/slick.css'
        ),
        'routes'=> '*'
    ),
    'neco.form.css' => array(
        'css' => array(
            'media' => 'all',
            'href' => $cssSharedPath . 'neco.form.css'
        ),
        'routes'=> array(
            'account/login',
            'account/register',
            'account/address/update',
            'account/address/insert',
            'account/message',
            'account/message/create',
            'account/password'
        )
    ),
    'fonts.stylesheet.css' => array(
        'css' => array(
            'media' => 'all',
            'href' => $cssPath . 'fonts.stylesheet.css'
        ),
        'routes'=> '*'
    ),
    'fancybox.css' => array(
        'css' => array(
            'media' => 'all',
            'href' => $cssPath . 'vendor/fancybox/jquery.fancybox.css'
        ),
        'routes'=> '*'
    ),
    'non-critical.css' => array(
        'css' => array(
            'media' => 'only x',
            'href' => $cssPath . 'non-critical.css'
        ),
        'routes'=> '*'
    ),
    'print.css' => array(
        'css' => array(
            'media' => 'all',
            'href' => $cssPath . 'print.css'
        ),
        'routes'=> '*'
    ),
    'grids.css' => array(
        'css' => array(
            'media' => 'all',
            'href' => $cssPath . 'grids.css'
        ),
        'routes'=> '*'
    ),
    'fonts.css' => array(
        'css' => array(
            'media' => 'all',
            'href' => $cssPath . 'fonts.css'
        ),
        'routes'=> '*'
    ),
    'color-default.css' => array(
        'css' => array(
            'media' => 'all',
            'href' => $cssPath . 'color-default.css'
        ),
        'routes'=> '*'
    ),
    'theme.css' => array(
        'css' => array(
            'media' => 'all',
            'href' => $cssPath . 'theme.css'
        ),
        'routes'=> '*'
    ),
    'responsive.css' => array(
        'css' => array(
            'media' => 'all',
            'href' => $cssPath . 'responsive.css'
        ),
        'routes'=> '*'
    ),
);