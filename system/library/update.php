<?php

/*
 * jQuery File Upload Plugin PHP Class 5.9.2
 * https://github.com/blueimp/jQuery-File-Upload
 *
 * Copyright 2010, Sebastian Tschan
 * https://blueimp.net
 *
 * Licensed under the MIT license:
 * https://www.opensource.org/licenses/MIT
 */

require_once('xhttp/xhttp.php');
require_once('pclzip.php');
require_once('language.php');

/**
 * Update
 * 
 * @package NecoTienda Standalone
 * @author Yosiet Serga
 * @copyright 2012
 * @version $Id$
 * @access public
 */
class Update {

    /**
     * @param $registry wrapper for registry object
     * */
    protected $registry;

    /**
     * @param $db wrapper for db object
     * */
    protected $db;

    /**
     * @param $load wrapper for load object
     * */
    protected $load;

    /**
     * @param $handler wrapper for xhttp object
     * */
    protected $handler;

    /**
     * @param $language wrapper for language object
     * */
    protected $language;

    /**
     * @param $updateAvailable bool check for update available
     * */
    protected $updateAvailable;

    /**
     * @param $update_info array que se retorna
     * */
    public $update_info = "";

    /**
     * Update::__construct()
     * 
     * @param mixed $registry
     * @return
     */
    function __construct($registry) {
        $this->registry = $registry;
        $this->db = $registry->get('db');
        $this->load = $registry->get('load');

        if (!defined('UPDATE_STATUS_PACKAGE')) {
            define('UPDATE_STATUS_PACKAGE','stable');
        }

        $this->update_info = "https://www.necotienda.org/api/index.php?r=update/info"
                . "&p=" . urlencode(PACKAGE)
                . "&v=" . urlencode(VERSION)
                . "&c=" . urlencode(C_CODE)
                . "&s=" . urlencode(UPDATE_STATUS_PACKAGE)
                . "&i=" . urlencode($_SERVER['SERVER_ADDR'])
                . "&d=" . urlencode(HTTP_HOME);

        if (in_array('curl', get_loaded_extensions())) {
            $this->handler = new xhttp;
        } else {
            $this->handler = new UpdateClass;
        }
    }

    /**
     * Update::run()
     * 
     * @return
     */
    public function run() {
        $info = $this->getInfo();
        $this->checkForUpdates();

        if ($this->updateAvailable) {

            /**
             * 8. verificar que exista el archivo update.php
             * 9. importar update.php y ejecutar el proceso
             * */
            $requirements = $this->checkRequirements($info['requirements']);
            if ($requirements) {
                return $requirements;
            }

//TODO: mostrar mensaje de error al fallar la validación de licencia y la causa
            if (!is_dir(DIR_ROOT . "updates")) {
                mkdir(DIR_ROOT . "updates", '0755');
            }
            $file_saved = DIR_ROOT . "updates/update". time() .".zip";

            $response = $this->handler->fetch($info['url_update']);
            if (isset($response['body']) && $response['successful']) {
                $file_update = $response['body'];
            } else {
                $file_update = $response;
            }

            $f = fopen($file_saved, 'wb');
            fwrite($f, $file_update);
            fclose($f);
            if (file_exists($file_saved) && sha1_file($file_saved) === $info['checksum']) {
                $zip = new PclZip();
                $zip->setZipName($file_saved);
                if ($zip->extract(PCLZIP_OPT_PATH,DIR_ROOT,PCLZIP_OPT_REPLACE_NEWER) > 0) {
                    unlink($file_saved);
                    if (file_exists(DIR_ROOT.'update.php')) {
                        include_once(DIR_ROOT.'update.php');
                        if (function_exists('upgradeNecoTienda')) {
                            upgradeNecoTienda($this->registry, VERSION);
                        }
                        unlink(DIR_ROOT.'update.php');
                    }
                } else {
                    echo $zip->errorInfo(true);
                    return $zip->errorInfo(true);
                }
            } else {
                return false;
            }
        } else {
            //TODO: Ya esta actualizada
        }
    }

    /**
     * Update::info()
     * 
     * array(
     *  'description'   =>$description,     // html text
     *  'changelog'     =>$changelog,       // text
     *  'version'       =>$version,         // string version
     *  'files_to_install'=>array(),        // array filenames to install
     *  'files_to_update'=>array(),         // array filenames to update
     *  'files_to_remove'=>array(),         // array filenames to delete
     *  'checksum'=>$checksum,              // string file checksum
     *  'url_update'=>$url_update,          // string file url for update
     * );
     * 
     * @return array update info
     */
    public function getInfo() {
        $file_info = $this->handler->fetch($this->update_info);
        if (isset($file_info['body'])) {
            return unserialize($file_info['body']);
        } else {
            return is_string($file_info) ? unserialize($file_info) : $file_info;
        }
    }

    public function checkRequirements($requirements) {
        $return = [];

        if (version_compare(phpversion(), $requirements['php_version'], '<')) {
            $return['requirements_error'] = true;
            $return['php_version'] = 'Se necesita la versi&oacute;n de PHP mayor o igual a ' . $requirements['php_version'] . ' y actualmente posee la versi&oacute;n ' . phpversion();
        }

        if (!ini_get('safe_mode')) {
            preg_match('/[0-9]\.[0-9]+\.[0-9]+/', shell_exec('mysql -V'), $version);

            if (empty($version[0])) {
                $version[0] = $this->db->getVersion();
            }

            if (version_compare($version[0], $requirements['mysql_version'], '<')) {
                $return['requirements_error'] = true;
                $return['php_version'] = 'Se necesita la versi&oacute;n de MySQL mayor o igual a ' . $requirements['mysql_version'] . ' y actualmente posee la versi&oacute;n ' . $version[0];
            }
        } else {
            $return['requirements_error'] = true;
            $return['safe_mode'] = 'Debe desactivar el status Safe Mode de PHP';
        }

        foreach ($requirements['php_extensions'] as $extension) {
            if (!extension_loaded($extension)) {
                $return['requirements_error'] = true;
                $return[$extension] = 'Debe instalar y activar en PHP la extensi&oacute;n ' . $extension;
            }
        }

        foreach ($requirements['php_vars'] as $var => $value) {
            if (ini_get($var) != $value) {
                $return['requirements_error'] = true;
                $return[$extension] = 'Debe configurar la variable de php '. $var .' con el valor ' . $value;
            }
        }

        foreach ($requirements['file_permissions'] as $path => $permission) {
            if (!file_exists(DIR_ROOT . $path)) {
                $return['requirements_error'] = true;
                $return[$extension] = 'La Carpeta o Archivo ' . $path . ' no existe';
            }
            $fileperm = substr(sprintf('%o', fileperms(DIR_ROOT . $path)), -4);
            if ($fileperm != $permission) {
                $return['requirements_error'] = true;
                $return[$extension] = 'La Carpeta o Archivo ' . $path . ' no posee los permisos requeridos (' . $permission . ')';
            }
        }
        return $return;
    }

    public function checkForUpdates() {
        $info = $this->getInfo();
        $this->updateAvailable = true;

        $error = "Hay una nueva versi&oacute;n disponible, Para instalarla haz click <a href=\"" . Url::createAdminUrl("tool/update") . "\" title=\"Actualizar\">aqu&iacute;</a>";

        if (isset($info['package']) && PACKAGE != $info['package']) {
            $error = 'No hay actualizaciones disponibles para este paquete de NecoTienda.';
            $this->updateAvailable = false;
        } else {
            if (isset($info['version']) && version_compare(VERSION, $info['version'], '>=')) {
                $this->updateAvailable = false;
                $error = 'La versi&oacute;n de NecoTienda que est&aacute; usando actualmente, es la &uacute;ltima versi&oacute;n disponible.';
            }
        }

        if (!isset($info['license']) || !$info['license']) {
            $this->updateAvailable = false;
            $error = '<b>LICENCIA INV&Aacute;LIDA</b><br />Debe comprar una licencia comercial de NecoTienda para disfrutar de las actualizaciones autom&aacute;ticas. Para saber m&aacute;s ingresa a <a href="https://www.necotienda.org/pricing">https://www.necotienda.org/pricing</a>';
        }
        
        return $error;
    }

}

class UpdateClass {

    /**
     * UpdateClass::file_exists()
     * 
     * @param mixed $url
     * @return
     */
    public function file_exists($url) {
        if (fopen($url, 'r')) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * UpdateClass::fetch()
     * 
     * @param mixed $url
     * @param mixed $requestData
     * @return
     */
    public function fetch($url, $requestData = array()) {
        return file_get_contents($url);
    }

}
