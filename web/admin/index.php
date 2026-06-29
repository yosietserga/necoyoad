<?php

define('PACKAGE', 'standalone');
define('VERSION', '2.0.1');

$config_path = dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR;
if (!file_exists($config_path . 'cconfig.php')) {
    header('Location: install/index.php');
    exit;
} else {
    require_once($config_path . 'app/admin/config.php');
    if (file_exists($config_path . 'app/install')) {
        rename($config_path . 'app/install', $config_path . 'app/delete_' . md5(mt_rand()));
    }
    if (file_exists($config_path . 'web/install')) {
        rename($config_path . 'web/install', $config_path . 'web/delete_' . md5(mt_rand()));
    }
}

require_once(DIR_SYSTEM . 'startup.php');

$registry = new Registry();
$loader = new Loader($registry);
$config = new Config();
$db = new DB(DB_DRIVER, DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
$request = new Request();
$response = new Response();
$controller = new Front($registry);
$session = new Session();

//TODO: Generar archivo de configuraci�n txt y si no hay cambios recientes en la tabla, cargar este archivo para ahorrar tiempo y memoria
$query = $db->query("SELECT * FROM " . DB_PREFIX . "setting WHERE store_id = 0");
foreach ($query->rows as $setting) {
    $config->set($setting['key'], $setting['value']);
}
$response->addHeader('Content-Type: text/html; charset=utf-8');

// Language
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
$config->set('config_language_id', $languages[$config->get('config_admin_language')]['language_id']);
$language = new Language($languages[$config->get('config_admin_language')]['directory']);
$language->load($languages[$config->get('config_admin_language')]['filename']);

// Template Preview
if (!$config->get('config_admin_template'))
    $config->set('config_admin_template', 'default');

if (!empty($_GET['template']) && file_exists(DIR_TEMPLATE . $_GET['template'] . '/common/header.tpl')) {
    $config->set('config_admin_template', $_GET['template']);
}

// Application Map
require_once(DIR_APPLICATION . 'map.php');

// Login
$controller->addPreAction(new Action('common/home/login'));

// Permission
$controller->addPreAction(new Action('common/home/permission'));

// Router
if (isset($request->get['r'])) {
    $action = new Action($request->get['r']);
} else {
    $action = new Action('common/home');
}

// Dispatch
$controller->dispatch($action, new Action('error/not_found'));

// Output
$response->output();
