<?php
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "cconfig.php");

$defaultPath    = dirname(__FILE__) . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR;
$adminPath      = dirname(__FILE__) . DIRECTORY_SEPARATOR;
$systemPath     = $defaultPath . "system" . DIRECTORY_SEPARATOR;
$shopPath       = $adminPath . ".." . DIRECTORY_SEPARATOR . "shop" . DIRECTORY_SEPARATOR;

define('HTTP_HOME', "https://www.necoyoad.com/");
define('HTTP_IMAGE', "https://www.necoyoad.com/assets/images/");

// Admin system
define('DIR_APPLICATION',   $adminPath);
define('DIR_LANGUAGE',      DIR_APPLICATION . "language" . DIRECTORY_SEPARATOR);
define('DIR_TEMPLATE',      DIR_APPLICATION . "view/template" . DIRECTORY_SEPARATOR);
define('DIR_EMAIL_TEMPLATE',$defaultPath . "web/admin/email_templates" . DIRECTORY_SEPARATOR);

// Core System
define('DIR_SYSTEM',    $systemPath);
define('DIR_DATABASE',  DIR_SYSTEM . 'database' . DIRECTORY_SEPARATOR);
define('DIR_CONFIG',    DIR_SYSTEM . 'config' . DIRECTORY_SEPARATOR);
define('DIR_CACHE',     DIR_SYSTEM . 'temp/cache/');
define('DIR_SESSION',   DIR_SYSTEM . 'temp/session/');
define('DIR_LOGS',      DIR_SYSTEM . 'logs' . DIRECTORY_SEPARATOR);

// Catalog System
define('DIR_IMAGE',     $defaultPath . "web/assets/images/");
define('DIR_CSS',     $defaultPath . "web/assets/css/");
define('DIR_JS',     $defaultPath . "web/assets/js/");
define('DIR_UPLOAD',   $defaultPath . "web/upload/");
define('DIR_DOWNLOAD',  $defaultPath . "web/download/");
define('DIR_CATALOG',   $shopPath);

