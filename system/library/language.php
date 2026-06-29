<?php

final class Language {

    private $directory;
    private $data = [];

    public function __construct($directory) {
        $this->directory = $directory;
    }

    public function get($key) {
        return (isset($this->data[$key]) ? $this->data[$key] : $key);
    }

    public function load($filename, $path=null) {
        if ($path && is_dir($path)) {
            $folder = $path . '/language';
        } else {
            $folder = DIR_LANGUAGE;
        }
        $_ = [];
        
        $default = DIR_LANGUAGE . 'spanish/' . $filename . '.php';
        $file = $folder .'/'. $this->directory .'/'. $filename . '.php';

        if (file_exists($file)) {
            include_once($file);
        } elseif (file_exists($default)) {
            include_once($default);
        }

        if (empty($_)) {
            //if (defined('NTS_DEBUG_MODE') && NTS_DEBUG_MODE === true) echo('<div class="msg warning">Warning: Could not load language ' . $filename . '!</div>');
        } else {
            $this->data = array_merge($this->data, $_);
        }

        return $this->data;
    }

}
