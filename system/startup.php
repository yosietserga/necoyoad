<?php

// Workflows
require_once(DIR_SYSTEM . 'library/automation/hooks.php');
require_once(DIR_SYSTEM . 'library/automation/events.php');

global $hooks;
$hooks = new Hooks("mainstream");

// Error Handler
if (defined('NTS_DEBUG_MODE') && NTS_DEBUG_MODE === true) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    function error_handler($errno, $errstr, $errfile, $errline) {

        switch ($errno) {
            case E_NOTICE:
            case E_USER_NOTICE:
                $error = 'Notice';
                break;
            case E_WARNING:
            case E_USER_WARNING:
                $error = 'Warning';
                break;
            case E_ERROR:
            case E_USER_ERROR:
                $error = 'Fatal Error';
                break;
            default:
                $error = 'Unknown';
                break;
        }

        $msg = 'PHP ' . $error . ':  ' . $errstr . ' in ' . $errfile . ' on line ' . $errline;
        echo $msg . "<hr />";
        Events::emit("php:error", $msg);
        Events::emit("error", $msg);
        return true;
    }

    set_error_handler('error_handler');
} else {
    ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);
}


//session handler class
require_once(DIR_SYSTEM . 'library/session.php');
$session = new Session();

// Check Version
if (version_compare(phpversion(), '5.1.0', '<') == true) {
    exit('PHP5.1+ Required');
}

// Register Globals
if (ini_get('register_globals')) {
    ini_set('session.use_cookies', 'On');
    ini_set('session.use_trans_sid', 'Off');

    session_set_cookie_params(0, '/');
    session_start();

    $globals = array($_REQUEST, $_SESSION, $_SERVER, $_FILES);

    foreach ($globals as $global) {
        foreach (array_keys($global) as $key) {
            unset($$key);
        }
    }
}

// Magic Quotes Fix
if (ini_get('magic_quotes_gpc')) {

    function clean($data) {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $data[clean($key)] = clean($value);
            }
        } else {
            $data = stripslashes($data);
        }

        return $data;
    }

    $_GET = clean($_GET);
    $_POST = clean($_POST);
    $_REQUEST = clean($_REQUEST);
    $_COOKIE = clean($_COOKIE);
}

if (!ini_get('date.timezone')) {
    date_default_timezone_set('UTC');
}


// Windows IIS Compatibility  
if (!isset($_SERVER['DOCUMENT_ROOT'])) {
    if (isset($_SERVER['SCRIPT_FILENAME'])) {
        $_SERVER['DOCUMENT_ROOT'] = str_replace('\\', '/', substr($_SERVER['SCRIPT_FILENAME'], 0, 0 - strlen($_SERVER['PHP_SELF'])));
    }
}

if (!isset($_SERVER['DOCUMENT_ROOT'])) {
    if (isset($_SERVER['PATH_TRANSLATED'])) {
        $_SERVER['DOCUMENT_ROOT'] = str_replace('\\', '/', substr(str_replace('\\\\', '\\', $_SERVER['PATH_TRANSLATED']), 0, 0 - strlen($_SERVER['PHP_SELF'])));
    }
}

if (!isset($_SERVER['REQUEST_URI'])) {
    $_SERVER['REQUEST_URI'] = substr($_SERVER['PHP_SELF'], 1);

    if (isset($_SERVER['QUERY_STRING'])) {
        $_SERVER['REQUEST_URI'] .= '?' . $_SERVER['QUERY_STRING'];
    }
}

// Engine
require_once(DIR_SYSTEM . 'engine/action.php');
require_once(DIR_SYSTEM . 'engine/controller.php');
require_once(DIR_SYSTEM . 'engine/front.php');
require_once(DIR_SYSTEM . 'engine/loader.php');
require_once(DIR_SYSTEM . 'engine/model.php');
require_once(DIR_SYSTEM . 'engine/registry.php');

Events::emit("engine_load", true);

// Main Classes
require_once(DIR_SYSTEM . 'classes/module.php');

// Common
require_once(DIR_SYSTEM . 'library/cache.php');
require_once(DIR_SYSTEM . 'library/config.php');
require_once(DIR_SYSTEM . 'library/db.php');
require_once(DIR_SYSTEM . 'library/document.php');
require_once(DIR_SYSTEM . 'library/language.php');
require_once(DIR_SYSTEM . 'library/log.php');
require_once(DIR_SYSTEM . 'library/request.php');
require_once(DIR_SYSTEM . 'library/response.php');
require_once(DIR_SYSTEM . 'library/url.php');
require_once(DIR_SYSTEM . 'library/image.php');

$hooks->run('init');
$hooks->addFilter("processcss", function ($original, $filtered) {
    return $filtered;
});


/*
Events::on("dispatch", function($action){
    //do something with this
});

$hooks->addAction("fetch", function ($template, $controller) {
    //do something with this

    //if want to break the spected workflow, return something distinct to false
    return true; //this will break the sequence stack 
});

$hooks->addFilter("query", function ($original, $filtered) {
    //do something with this
    return $filtered;
});
*/