<?php
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "cconfig.php");
define('ADMIN_PATH','admin');
define('APP_PATH','admin');
define('DB_VERSION', '1.0.2');
define('ADMIN_VERSION', '1.0.2');
define('SHOP_VERSION', '1.0.2');
define('SYSTEM_VERSION', '1.0.2');

if (!defined("STORE_ID")) define('STORE_ID', 0);

$defaultPath    = dirname(__FILE__) . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR;
$adminPath      = dirname(__FILE__) . DIRECTORY_SEPARATOR;
$systemPath     = $defaultPath . "system" . DIRECTORY_SEPARATOR;
$shopPath       = $adminPath . ".." . DIRECTORY_SEPARATOR . "shop" . DIRECTORY_SEPARATOR;

$protocol  = strpos(strtolower($_SERVER['SERVER_PROTOCOL']),'https') === FALSE ? 'https://' : 'https://';
$httpAdminPath = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] : substr($_SERVER['PHP_SELF'],0,strrpos($_SERVER['PHP_SELF'],"/")+1);
$httppath = $httpAdminPath = str_replace('/index.php',"",$httpAdminPath);
$httppath = substr($httppath,0,strrpos($httppath,"/",1));
//$httppath = str_replace("/web","",$httppath);


define('HTTP_HOME',   "https://" . $httpAdminPath . "/");
define('HTTP_CATALOG',  "https://" . $httppath . "/");
define('HTTP_IMAGE',    "https://" . $httppath."/assets/images/");
define('HTTP_UPLOAD',  "https://" . $httppath."/upload/");
define('HTTP_EMAIL_TPL_IMAGE', HTTP_HOME . "email_templates/");
define('HTTP_CSS', "//" . $httppath."/assets/css/");
define('HTTP_TPL_IMAGE', HTTP_HOME . "view/image/");
define('HTTP_JS', "//" . $httppath."/assets/js/");
define('HTTP_TPL', HTTP_HOME . "view/template/");

define('HTTP_ADMIN_FONT', "https://" . $httppath."/". ADMIN_PATH ."/fonts/");
define('HTTP_ADMIN_CSS', "https://" . $httppath."/". ADMIN_PATH ."/css/");
define('HTTP_ADMIN_JS', "https://" . $httppath."/". ADMIN_PATH ."/js/");
define('HTTP_ADMIN_IMAGE', "//" . $httppath."/". ADMIN_PATH ."/images/");
define('HTTP_ADMIN_THEME_CSS', HTTP_HOME . "templates/%theme%/css/");
define('HTTP_ADMIN_THEME_JS', HTTP_HOME . "templates/%theme%/js/");
define('HTTP_ADMIN_THEME_IMAGE', HTTP_HOME . "templates/%theme%/images/");
define('HTTP_ADMIN_THEME_FONT', HTTP_HOME . "templates/%theme%/fonts/");

// DIR
define('CATALOG', 'shop');
define('JS', '/view/theme/%theme%/javascript/');
define('CSS', '/view/theme/%theme%/css/');

// Admin system
define('DIR_ROOT',          $defaultPath);
define('DIR_APPLICATION',   $adminPath);
define('DIR_CONTROLLER',   DIR_APPLICATION .'controller'. DIRECTORY_SEPARATOR);
define('DIR_MODEL',   DIR_APPLICATION .'model'. DIRECTORY_SEPARATOR);
define('DIR_LANGUAGE',      DIR_APPLICATION . "language" . DIRECTORY_SEPARATOR);
define('DIR_TEMPLATE',      DIR_APPLICATION . "view/templates" . DIRECTORY_SEPARATOR);
define('DIR_ADMIN_CSS',     $defaultPath . "web/". ADMIN_PATH ."/css" . DIRECTORY_SEPARATOR);
define('DIR_ADMIN_JS',      $defaultPath . "web/". ADMIN_PATH ."/js" . DIRECTORY_SEPARATOR);
define('DIR_ADMIN_IMAGE',   $defaultPath . "web/". ADMIN_PATH ."/images" . DIRECTORY_SEPARATOR);
define('DIR_EMAIL_TEMPLATE',$defaultPath . "web/". ADMIN_PATH ."/email_templates" . DIRECTORY_SEPARATOR);
define('DIR_ADMIN_THEME_CSS', $defaultPath . "web/". ADMIN_PATH . "/templates/%theme%/css/");
define('DIR_ADMIN_THEME_JS', $defaultPath . "web/". ADMIN_PATH . "/templates/%theme%/js/");
define('DIR_ADMIN_THEME_IMAGE', $defaultPath . "web/". ADMIN_PATH . "/templates/%theme%/images/");
define('DIR_ADMIN_THEME_FONT', $defaultPath . "web/". ADMIN_PATH . "/templates/%theme%/fonts/");

// Modules
define('DIR_MODULE',    $defaultPath . "app/modules/");

// Core System
define('DIR_BACKUP',    $defaultPath . "backups/");
define('DIR_SYSTEM',    $systemPath);
define('DIR_DATABASE',  DIR_SYSTEM . 'database' . DIRECTORY_SEPARATOR);
define('DIR_CONFIG',    DIR_SYSTEM . 'config' . DIRECTORY_SEPARATOR);
define('DIR_CACHE',     DIR_SYSTEM . 'temp/cache/');
define('DIR_SESSION',   DIR_SYSTEM . 'temp/session/');
define('DIR_LOGS',		DIR_SYSTEM . 'logs'. DIRECTORY_SEPARATOR . APP_PATH . DIRECTORY_SEPARATOR);

// Catalog System
define('DIR_IMAGE',     $defaultPath . "web/assets/images/");
define('DIR_CSS',       $defaultPath . "web/assets/css/");
define('DIR_JS',        $defaultPath . "web/assets/js/");
define('DIR_UPLOAD',    $defaultPath . "web/upload/");
define('DIR_DOWNLOAD',  $defaultPath . "web/download/");
define('DIR_THEME_ASSETS',       $defaultPath . "web/assets/theme/");
define('DIR_CATALOG',   $shopPath);

//DEBUG MODE
define('NTS_DEBUG_MODE', false);