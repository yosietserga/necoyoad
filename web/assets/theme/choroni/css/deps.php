<?php
/**
 * $cssPath is the http url to the configured template path
 * @var String
 */
$cssPath = str_replace("%theme%", $tpl, $cssPath);

/**
 * $cssSharedPath is the http url to the shared path /web/assets/css/
 * @param $cssSharedPath
 * @var String
 */

/**
 * $css_assets hold all the style sheets files that will be into the generated html 
 * put here your css files that you want to be rendered
 * 
 * This is done to improve the performance, to satisfy Google's insights and to manage
 * better style sheets structure.
 *
 * The rendered HTML will have all the css files unified and minified, you don't have to do it,
 * the app will do it for you. NO MORE WEBPACKS AND BLOWFISH PLUGINS :)
 * 
 * NOTE: if you need that the file be rendered for one or few controllers, you have
 * to create the index 'routes' with the controller's list
 *
 * NOTE: There exists a magic assets loader using namespace convention, if you are not adding
 * vendor css files and just need to put some custom style for a specific controller, just create
 * a file with the full route name without spaces. For example:
 *
 *      for common/home route, create a css file named commonhome.css (this works for common/home/*)
 *      for store/product route, create a css file named storeproduct.css (this works for store/product/*)
 *
 * If you need for a specific route, you have to create a file with the complete route. For example:
 *
 *      for store/product/quickview, create a css file named storeproductquickview.css
 *
 * And Yes! it works for modules, too! But instead of using a method inside controller, use the view params
 * 
 *      for a module, create a css file named module[module_name].css (this works for module/[module_name]/*)
 *      for a specific view of the module, create a css file named module[module_name][view].css
 *      
 *      example: modulestorelogomycustomtheme.css 
 *
 * Check the module's views documentation for more help
 *      
 * @var array
 */
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
        'routes'=> array(
            'module/productimages'
        )
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


if (isset($_GET['admin_tools'])) {
   $css_assets[ 'neco.colorpicker.css' ] = array(
        'css' => array(
            'media' => 'all',
            'href' => $cssSharedPath . 'neco.colorpicker.css'
        ),
        'routes'=> '*'
    );
}

