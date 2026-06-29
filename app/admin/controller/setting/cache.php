<?php

/**
 * ControllerSettingCache
 * 
 * @package NecoTienda Standalone
 * @author Yosiet Serga
 * @copyright 2013
 * @version $Id$
 * @access public
 */
class ControllerSettingCache extends Controller {

    private $error = [];

    /**
     * ControllerSettingCache::index()
     * 
     * @return
     */
    public function index() {
        echo 'build cache manager';
    }

    /**
     * ControllerSettingCache::index()
     *
     * @return
     */
    public function deletefilecache() {
        $this->load->library('url');
        $res = $this->rrmdir(DIR_CACHE);
        $this->load->model('store/store');
        $stores = $this->modelStore->getAll();
        $this->session->clear('ntConfig_0');
        foreach ($stores as $store) {
            $this->session->clear('ntConfig_' . (int) $store['store_id']);
        }

        $this->session->clear('language');
        $this->session->clear('fkey');

        if ($res) {
            $this->session->set('success', 'Se han eliminado todos los archivos del cache con &eacute;xito');
        } else {
            $this->session->set('error', 'Error: No se pudieron eliminar los archivos del cache');
        }

        if ($_SERVER['HTTP_REFERER']) {
            $this->redirect($_SERVER['HTTP_REFERER']);
        } elseif ($this->session->has('redirect')) {
            $this->redirect($this->session->get('redirect'));
            $this->session->clear('redirect');
        } else {
            $this->redirect(Url::createAdminUrl('common/home'));
        }
    }

    /**
     * ControllerSettingCache::rrmdir()
     * 
     * @param mixed $dir
     * @return
     */
    protected function rrmdir($dir) {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (filetype($dir . "/" . $object) == "dir")
                        $this->rrmdir($dir . "/" . $object);
                    else
                        unlink($dir . "/" . $object);
                }
            }
            reset($objects);
            //rmdir($dir);
            return true;
        }
        return false;
    }

}
