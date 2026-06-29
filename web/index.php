<?php
if (file_exists(dirname(__FILE__). '/../install.php')) {
    unlink(dirname(__FILE__). '/../install.php');
}

error_reporting(0);
define('PACKAGE', 'standalone');
define('VERSION', '1.0.2');
$matches = [];
$config_path = dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR;
if (!file_exists($config_path . 'cconfig.php')) {
    $protocol = strpos(strtolower($_SERVER['SERVER_PROTOCOL']), 'https') === FALSE ? 'https://' : 'https://';
    $httpDefaultPath = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] : substr($_SERVER['PHP_SELF'], 0, strrpos($_SERVER['PHP_SELF'], "/") + 1);
    $httpPath = str_replace('/index.php', "", $httpDefaultPath);
    $httpPath = str_replace('/web/', "", $httpPath);
    header('Location: ' . $protocol . $httpPath . '/install/index.php');
    exit;
} else {
    require_once($config_path . 'cconfig.php');
    $db = new mysqli(DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
    if (isset($_GET['_route_'])) {
        $parts = explode("/", $_SERVER['REQUEST_URI']);
        foreach ($parts as $part) {
            if (empty($part))
                continue;
            $results = $db->query("SELECT * FROM " . DB_PREFIX . "store WHERE folder = '" . $db->real_escape_string($part) . "'");
            $store = $results->fetch_assoc();
            if ($store['folder']) {
                $matches[1] = $store['folder'];
            }
        }
    } elseif (isset($_GET['store_id'])) {
        $results = $db->query("SELECT * FROM " . DB_PREFIX . "store WHERE store_id = '" . $db->real_escape_string((int)$_GET['store_id']) . "'");
        $store = $results->fetch_assoc();
        if ($store['store_id']) {
            $matches[1] = $store['folder'];
        }
    }
}

if (!isset($matches[1]))
    preg_match('/([^.]+)\.necoyoad\.com/', $_SERVER['SERVER_NAME'], $matches);
if (isset($matches[1]) && $matches[1] != 'www') {
    if (file_exists($config_path . "app/" . strtolower($matches[1]) . "/config.php")) {
        require_once($config_path . "app/" . strtolower($matches[1]) . "/config.php");
    } else {
        require_once($config_path . 'app/shop/config.php');
    }
} else {
    require_once($config_path . 'app/shop/config.php');
}
// Startup
require_once(DIR_SYSTEM . 'startup.php');

// App Libs and Configs Preload
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'app/shop/map.php');

// Front Controller 
$controller = new Front($registry);

// Maintenance Mode
$controller->addPreAction(new Action('common/maintenance/check'));

// SEO URL's
if ($config->get('config_seo_url')) $controller->addPreAction(new Action('common/seo_url'));

// Workflows, Hooks and Events 
//TODO: add logic for workflows, hooks and events
//if ($config->get('config_run_workflows')) $controller->addPreAction(new Action('automation/workflows'));
//if ($config->get('config_run_events')) $controller->addPreAction(new Action('automation/events'));

// Router
if (isset($request->get['r'])) {
    if (!isset($controller->ClassName))
        $controller->ClassName = $request->get['r'];
    $action = new Action($request->get['r']);
} else {
    $action = new Action('common/home');
}

// Dispatch
$controller->dispatch($action, new Action('error/not_found'));

// Output
$response->output();