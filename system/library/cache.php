<?php

final class Cache
{

    private $expire;

    public function __construct()
    {
        if (!is_dir(DIR_CACHE)) {
            mkdir(DIR_CACHE, 0755);
        }

        $this->expire = 60*3600;

        $files = glob(realpath(DIR_CACHE) . "/" . '*.cache');

        if ($files) {
            foreach ($files as $file) {
                $time = substr(strrchr(str_replace('.cache', '', $file), '.'), 1);

                if ($time < time()) {
                    if (file_exists($file)) {
                        unlink($file);
                    }
                }
            }
        }
    }

    public function get($key, $prefix = "")
    {
        if (!empty($prefix)) {
            $prefix = $this->sanitizeCacheId($prefix).'.';
        }
        $files = glob(realpath(DIR_CACHE) . "/" . $prefix . md5($key) . '*.cache');
        if ($files) {
            $cache = file_get_contents($files[0]);
            return unserialize($cache);
        }
    }

    public function set($key, $value, $prefix = "")
    {
        $this->delete($prefix, $key);
        if (!empty($prefix)) {
            $prefix = $this->sanitizeCacheId($prefix).'.';
        }
        $file = realpath(DIR_CACHE) . "/" . $prefix . md5($key) .'.'. (time() + $this->expire) . '.cache';
 
        $handle = fopen($file, 'w');

        fwrite($handle, serialize($value));

        fclose($handle);
    }

    public function delete($prefix = "", $key = "")
    {
        if (!empty($prefix)) {
            $prefix = $this->sanitizeCacheId($prefix) . '.';
        }
        $files = glob(realpath(DIR_CACHE) . "/" . $prefix . md5($key) . '*.cache');
        foreach ($files as $file) {
            if (file_exists($file)) {
                unlink($file);
            }
        }

        //$this->deleteFiles(DIR_IMAGE . 'cache', true);
    }

    public function deleteFiles($directory, $recursive = false)
    {
        if (is_dir($directory)) {
            $handle = opendir($directory);
        }
        if (!$handle) {
            return false;
        }
        
        while (false !== ($file = readdir($handle))) {
            if ($file !== '.' && $file !== '..') {
                if (!is_dir($directory .'/'. $file)) {
                    unlink($directory .'/'. $file);
                } elseif ($recursive && is_dir($directory .'/'. $file)) {
                    $this->deleteFiles($directory .'/'. $file, $recursive);
                    rmdir($directory .'/'. $file);
                }
            }
        }

        closedir($handle);
    }

    protected function sanitizeCacheId($cachedId)
    {
        $device = '';
        if (file_exists('browser.php')) {
            require_once('browser.php');
            
            $browser = new Browser();

            if ($browser->isMobile()) {
                $device = '.mobile';
            } elseif ($browser->isTablet()) {
                $device = '.tablet';
            } elseif ($browser->isFacebook()) {
                $device = '.facebook';
            } else {
                $device = '.pc';
            }
        }

        $cachedId .= $device;
        if ($cachedId !== mb_convert_encoding(mb_convert_encoding($cachedId, 'UTF-32', 'UTF-8'), 'UTF-8', 'UTF-32')) {
            $cachedId = mb_convert_encoding($cachedId, 'UTF-8', mb_detect_encoding($cachedId));
        }
        $cachedId = htmlentities($cachedId, ENT_NOQUOTES, 'UTF-8');
        $cachedId = preg_replace('`&([a-z]{1,2})(acute|uml|circ|grave|ring|cedil|slash|tilde|caron|lig);`i', '\1', $cachedId);
        $cachedId = html_entity_decode($cachedId, ENT_NOQUOTES, 'UTF-8');
        $cachedId = preg_replace(array('`[^a-z0-9]`i','`[-]+`'), '-', $cachedId);
        $cachedId = strtolower(trim($cachedId, '-'));

        return $cachedId;
    }
}
