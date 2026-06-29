<?php

$registry = new Registry();
//TODO: import and init automation libraries 
//load Events Class
//Load Workflows Class 
//set global Events and Workflows
//$ev = new Event($registry);
//$workflow = new Workflow($registry);

$loader = new Loader($registry);
$config = new Config();
$db = new DB(DB_DRIVER, DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
$request = new Request();
$response = new Response();
$controller = new Front($registry);

$hooks->run('system_load', [
    "db"=> $db,
    "loader"=>$loader,
    "registry"=> $registry,
]);
Events::emit("system_load", [
    "db" => $db,
    "loader" => $loader,
    "registry" => $registry,
]);


// llave para utilizar en los formularios y evitar ataques csrf
if (!$session->has('fkey')) {
    $i = 0;
    $super_rand = "";
    while ($i <= 10) {
        $super_rand .= md5(mt_rand(1000000, 9999999));
        $i++;
    }
    $session->set('fkey', md5($_SERVER['REMOTE_ADDR']) . "." . $super_rand . "_" . strtotime(date('d-m-Y')));
    $registry->set('fkey', $session->get('fkey'));
} else {
    $registry->set('fkey', $session->get('fkey'));
}
$hooks->run('csrf_load', $session);


// Settings
if (!$session->has('ntConfig_' . (int) STORE_ID)) {
    $query = $db->query("SELECT * FROM " . DB_PREFIX . "setting WHERE store_id = '" . (int) STORE_ID . "'");
    foreach ($query->rows as $setting) {
        $config->set($setting['key'], $setting['value']);
    }
} else {
    $config = unserialize($session->get('ntConfig_' . (int) STORE_ID));
}
$config->set('config_store_id', STORE_ID);

$hooks->run('config_load', $config);
Events::emit("config_load", $config);

$response->addHeader('Content-Type: text/html; charset=utf-8');

// Language Detection
$languages = [];

$query = $db->query("SELECT * FROM " . DB_PREFIX . "language");

foreach ($query->rows as $result) {
    $languages[$result['code']] = array(
        'language_id' => $result['language_id'],
        'name' => $result['name'],
        'code' => $result['code'],
        'locale' => $result['locale'],
        'directory' => $result['directory'],
        'filename' => $result['filename']
    );
}

$detect = '';

if (isset($request->server['HTTP_ACCEPT_LANGUAGE']) && ($request->server['HTTP_ACCEPT_LANGUAGE'])) {
    $browser_languages = explode(',', $request->server['HTTP_ACCEPT_LANGUAGE']);

    foreach ($browser_languages as $browser_language) {
        foreach ($languages as $key => $value) {
            $locale = explode(',', $value['locale']);

            if (in_array($browser_language, $locale)) {
                $detect = $key;
            }
        }
    }
}

if (isset($_GET['language']) && array_key_exists($_GET['language'], $languages)) {
    $code = $_GET['language'];
} elseif (isset($_GET['hl']) && array_key_exists($_GET['hl'], $languages)) {
    $code = $_GET['hl'];
} elseif ($session->has('language') && array_key_exists($session->get('language'), $languages)) {
    $code = $session->get('language');
} elseif ($request->hasCookie('language') && array_key_exists($request->getCookie('language'), $languages)) {
    $code = $request->getCookie('language');
} elseif ($detect) {
    $code = $detect;
} else {
    $code = $config->get('config_language');
}

if (!$session->has('language') || $session->get('language') != $code) {
    $session->set('language', $code);
}

if (!$request->hasCookie('language') || $request->getCookie('language') != $code) {
    $request->setCookie('language', $code);
}
$config->set('config_language_id', $languages[$code]['language_id']);
$config->set('config_language', $languages[$code]['code']);

// Language     
$language = new Language($languages[$code]['directory']);
$language->load($languages[$code]['filename']);
$hooks->run('language_load', $code);

// Log 
global $log;
$log = new Log('log.txt');
$registry->set('log', $log);
//listen for errors
Events::on("php:error", function ($error_msg) {
    global $log;
    $log->trace($error_msg);
});

$loader->auto('url');
$loader->auto('user');
$loader->auto('customer');
$loader->auto('currency');
$loader->auto('tax');
$loader->auto('weight');
$loader->auto('length');
$loader->auto('cart');
$loader->auto('validar');
$loader->auto('encoder');
$loader->auto('browser');
$loader->auto('tracker');

$registry->set('config', $config);
$registry->set('load', $loader);
$registry->set('db', $db);
$registry->set('log', $log);
$registry->set('request', $request);
$registry->set('response', $response);
$registry->set('session', $session);
$registry->set('cache', new Cache());
$registry->set('document', new Document());
$registry->set('language', $language);
$registry->set('user', new User($registry));

$hooks->run('app_load', $registry);
Events::emit("app_load", $registry);

$customer = new Customer($registry);

// App Libs
$registry->set('customer', $customer);
$registry->set('currency', new Currency($registry));
$registry->set('tax', new Tax($registry));
$registry->set('weight', new Weight($registry));
$registry->set('length', new Length($registry));
$registry->set('cart', new Cart($registry));
$registry->set('browser', new Browser);
$registry->set('tracker', new Tracker($registry));
$registry->set('javascripts', array());
$registry->set('styles', array());
$registry->set('scripts', array());


if ($request->hasQuery('refby')) {
    $customer->setRefByCustomer($request->getQuery('refby'));
}

if ($request->hasQuery('ref')) {
    $customer->setRefCustomer($request->getQuery('ref'));
};

// for background color when it resizes images
if (!defined('IMAGE_BG_COLOR_R')) {
    $config->get('config_image_bg_color_r') ?
        define('IMAGE_BG_COLOR_R', $config->get('config_image_bg_color_r')) : define('IMAGE_BG_COLOR_R', 255);
}
if (!defined('IMAGE_BG_COLOR_G')) {
    $config->get('config_image_bg_color_g') ?
        define('IMAGE_BG_COLOR_G', $config->get('config_image_bg_color_g')) : define('IMAGE_BG_COLOR_G', 255);
}
if (!defined('IMAGE_BG_COLOR_B')) {
    $config->get('config_image_bg_color_b') ?
        define('IMAGE_BG_COLOR_B', $config->get('config_image_bg_color_b')) : define('IMAGE_BG_COLOR_B', 255);
}

$loader->library('browser');
$browser = new Browser;
if ($browser->isMobile()) {
    if ($config->get('config_redirect_when_mobile') && str_replace('/web','',HTTP_HOME) !== $config->get('config_mobile_url')) {
        if (!headers_sent()) {
            header('Location: ' . str_replace(array('&amp;', "\n", "\r"), array('&', '', ''), $config->get('config_mobile_url')));
            exit;
        } else {
            echo "<script> window.location = '".str_replace('&amp;', '&', $config->get('config_mobile_url'))."'; </script>";
        }
    } else {
        $config->set('config_template', $config->get('config_mobile_template'));
    }
} elseif ($browser->isTablet()) {
    if ($config->get('config_redirect_when_tablet') && str_replace('/web','',HTTP_HOME) !== $config->get('config_tablet_url')) {
        if (!headers_sent()) {
            header('Location: ' . str_replace(array('&amp;', "\n", "\r"), array('&', '', ''), $config->get('config_tablet_url')));
            exit;
        } else {
            echo "<script> window.location = '".str_replace('&amp;', '&', $config->get('config_tablet_url'))."'; </script>";
        }
    } else {
        $config->set('config_template', $config->get('config_tablet_template'));
    }
} elseif ($browser->isFacebook()) {
    if ($config->get('config_redirect_when_facebbok') && str_replace('/web','',HTTP_HOME) !== $config->get('config_facebook_url')) {
        if (!headers_sent()) {
            header('Location: ' . str_replace(array('&amp;', "\n", "\r"), array('&', '', ''), $config->get('config_facebook_url')));
            exit;
        } else {
            echo "<script> window.location = '".str_replace('&amp;', '&', $config->get('config_facebook_url'))."'; </script>";
        }
    } else {
        $config->set('config_template', $config->get('config_facebook_theme'));
    }
}

// Default
$language->load('common/header');

$loader->auto('account/customer');
$loader->auto('store/product');
$loader->auto('store/category');
//TODO: redise�ar clase de URLs
$loader->auto('localisation/language');
$loader->auto('localisation/currency');

$registry->set('validar', new Validar());

$route = $request->hasQuery('_r_') ? strtolower($request->getQuery('_r_')??"") : strtolower($request->getQuery('r')??"");
// Currency code
if (!empty($_GET['cc'])) {
    $config->set('config_currency', $_GET['cc']);
}

// Template Preview
if (!empty($_GET['template']) && file_exists(DIR_TEMPLATE . $_GET['template'] . '/common/header.tpl')) {
    $config->set('config_template', $_GET['template']);
}

$tpl = $config->get('config_template') ? $config->get('config_template') : 'choroni';

$header_javascripts = $javascripts = $styles = $scripts = $css = [];

$jsSharedPath = ($config->get('config_render_js_in_file')) ? DIR_JS : HTTP_JS;
$jsPath = ($config->get('config_render_js_in_file')) ? DIR_THEME_JS : HTTP_THEME_JS;
$jsAppPath = ($config->get('config_render_js_in_file')) ? DIR_THEME_JS : HTTP_THEME_JS;
if (file_exists(str_replace("%theme%", $tpl, DIR_THEME_JS) . 'deps.php')) {
    require_once(str_replace("%theme%", $tpl, DIR_THEME_JS) . 'deps.php');

    foreach ($js_assets as $i => $routes) {
        if (empty($routes)) continue;
        if (is_array($routes) && in_array($route, $routes) || $routes === '*') {
            array_push($javascripts, $i);
            unset($js_assets[$i]);
        }
    }

    foreach ($js_header_assets as $i => $routes) {
        if (empty($routes)) continue;
        if (is_array($routes) && in_array($route, $routes) || $routes === '*') {
            array_push($header_javascripts, $i);
            unset($js_header_assets[$i]);
        }
    }

    if (isset($jsx_assets)) {
        foreach ($jsx_assets as $i => $routes) {
            if (empty($routes)) continue;
            if ((is_array($routes) && in_array($route, $routes)) || $routes === '*') {
                array_push($scripts, array(
                    'method'=>'jsx',
                    'id'=>$i,
                    'script'=>file_get_contents($i)
                ));
                unset($jsx_assets[$i]);
            }
        }
    }

    if (isset($js_assets)) $registry->set('js_assets', $js_assets);
    if (isset($js_header_assets)) $registry->set('js_header_assets', $js_header_assets);
    if (isset($jsx_assets)) $registry->set('jsx_assets', $jsx_assets);
}

$cssSharedPath = ($config->get('config_render_css_in_file')) ? DIR_CSS : HTTP_CSS;
$cssPath = ($config->get('config_render_css_in_file')) ? DIR_THEME_CSS : HTTP_THEME_CSS;
if (file_exists(str_replace("%theme%", $tpl, DIR_THEME_CSS) . 'deps.php')) {
    require_once(str_replace("%theme%", $tpl, DIR_THEME_CSS) . 'deps.php');
    foreach ($css_assets as $i => $asset) {
        if (empty($asset['css'])) continue;
        if ((is_array($asset['routes']) && in_array($route, $asset['routes'])) || $asset['routes'] === '*') {
            array_push($styles, $asset['css']);
            unset($css_assets[$i]);
        }
    }
    $registry->set('css_assets', $css_assets);
}
$registry->set('header_javascripts', $header_javascripts);
$registry->set('javascripts', $javascripts);
$registry->set('styles', $styles);
$registry->set('css', $css);
$registry->set('scripts', $scripts);

$session->set('ntConfig_' . STORE_ID, serialize($config));

$registry->set('config', $config);
$registry->set('hooks', $hooks);

$hooks->run('load', $registry);
Events::emit("load", $registry);
