<?php
define('CATALOG', 'm');
define('STORE_ID', '9');
define('ADMIN', 'admin');

require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "cconfig.php");

$publictPath = dirname(__FILE__) . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "web" . DIRECTORY_SEPARATOR;
$privatePath = dirname(__FILE__) . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "shop" . DIRECTORY_SEPARATOR;
$mainPath    = dirname(__FILE__) . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR;

$httpPath= isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'] : substr($_SERVER['PHP_SELF'],0,strrpos($_SERVER['PHP_SELF'],"/")+1);
$httpPath = str_replace('/index.php',"",$httpPath);

$domain = str_replace(CATALOG.".", "", $_SERVER['HTTP_HOST']);
$domain = str_replace("www.", "", $domain);
preg_match('/([^.]+)\.'. addslashes($domain).'/', $_SERVER['SERVER_NAME'], $matches);
if(isset($matches[1]) && $matches[1]==CATALOG) {
    $httpAppPath = $httpPath."/".CATALOG."/";
} else {
    $httpPath .= "/".CATALOG."/";
    $httpAppPath = $httpPath;
}

$httpPath = str_replace(CATALOG .'/'. CATALOG, '/'.CATALOG, $httpPath);
$httpPath = str_replace('//','/',$httpPath);

if (isset($_SERVER['HTTPS']) 
	&& ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1) || isset($_SERVER['HTTP_X_FORWARDED_PROTO']) 
	&& $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') 
{
	$protocol = 'https://';
} else {
	$protocol = 'https://';
}

 // HTTP addresses
define('HTTP_HOME',     $protocol . str_replace('//', '/', $httpPath . "/"));
define('HTTP_ADMIN',    str_replace("/". CATALOG ."/", "/", HTTP_HOME) . ADMIN ."/");
define('HTTP_IMAGE',    str_replace("/".CATALOG."/", "/", HTTP_HOME) . "assets/images/");
define('HTTP_CSS',      str_replace("/".CATALOG."/", "/", HTTP_HOME) . "assets/css/");
define('HTTP_JS',       str_replace("/".CATALOG."/", "/", HTTP_HOME) . "assets/js/");
define('HTTP_UPLOAD',   str_replace("/".CATALOG."/", "/", HTTP_HOME) . "assets/upload/");
define('HTTP_DOWNLOAD', str_replace("/".CATALOG."/", "/", HTTP_HOME) . "assets/upload/");
define('HTTP_THEME_CSS',str_replace("/".CATALOG."/", "/", HTTP_HOME) . "assets/theme/%theme%/css/");
define('HTTP_THEME_JS', str_replace("/".CATALOG."/", "/", HTTP_HOME) . "assets/theme/%theme%/js/");
define('HTTP_THEME_IMAGE', str_replace("/".CATALOG."/", "/", HTTP_HOME) . "assets/theme/%theme%/images/");
define('HTTP_THEME_FONT', str_replace("/".CATALOG."/", "/", HTTP_HOME) . "assets/theme/%theme%/fonts/");

 // HTTPS addresses
define('HTTPS_HOME',     "https://". $httpPath ."/");
define('HTTPS_IMAGE',    str_replace("/".CATALOG."/", "/", HTTPS_HOME) . "assets/images/");
define('HTTPS_CSS',      str_replace("/".CATALOG."/", "/", HTTPS_HOME) . "assets/css/");
define('HTTPS_JS',       str_replace("/".CATALOG."/", "/", HTTPS_HOME) . "assets/js/");
define('HTTPS_UPLOAD',   str_replace("/".CATALOG."/", "/", HTTPS_HOME) . "assets/upload/");
define('HTTPS_DOWNLOAD', str_replace("/".CATALOG."/", "/", HTTPS_HOME) . "assets/upload/");
define('HTTPS_THEME_CSS',str_replace("/".CATALOG."/", "/", HTTPS_HOME) . "assets/theme/%theme%/css/");
define('HTTPS_THEME_JS', str_replace("/".CATALOG."/", "/", HTTPS_HOME) . "assets/theme/%theme%/js/");
define('HTTPS_THEME_IMAGE', str_replace("/".CATALOG."/", "/", HTTPS_HOME) . "assets/theme/%theme%/images/");
define('HTTPS_THEME_FONT', str_replace("/".CATALOG."/", "/", HTTPS_HOME) . "assets/theme/%theme%/fonts/");

define('DIR_APPLICATION',$privatePath);
define('DIR_MODEL',     $privatePath . "model" . DIRECTORY_SEPARATOR);
define('DIR_CONTROLLER',$privatePath . "controller" . DIRECTORY_SEPARATOR);
define('DIR_LANGUAGE',  $privatePath . "language" . DIRECTORY_SEPARATOR);
define('DIR_TEMPLATE',  $privatePath . "view/theme/");

define('DIR_ADMIN_APPLICATION', DIR_APPLICATION ."../admin/");

// Shared Files
define('DIR_IMAGE',     $publictPath . "assets/images/");
define('DIR_CSS',       $publictPath ."assets/css/");
define('DIR_JS',        $publictPath ."assets/js/");
define('DIR_UPLOAD',    $mainPath ."assets/upload/");
define('DIR_DOWNLOAD',  $mainPath ."assets/download/");
define('DIR_THEME_CSS', $publictPath ."assets/theme/%theme%/css/");
define('DIR_THEME_JS',  $publictPath ."assets/theme/%theme%/js/");
define('DIR_THEME_IMAGE', $publictPath ."assets/theme/%theme%/images/");

// System files
define('DIR_SYSTEM',    $mainPath . 'system/');
define('DIR_DATABASE',  DIR_SYSTEM . 'database/');
define('DIR_CONFIG',    DIR_SYSTEM . 'config/');
define('DIR_CACHE',     DIR_SYSTEM . 'temp/cache/');
define('DIR_SESSION',   DIR_SYSTEM . 'temp/session/');
define('DIR_LOGS', 		DIR_SYSTEM . 'temp/logs/'. CATALOG . DIRECTORY_SEPARATOR);


define('NTS_DEBUG_MODE', true);