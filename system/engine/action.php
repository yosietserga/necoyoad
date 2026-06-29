<?php

final class Action {

    protected $file;
    protected $class;
    protected $method;
    protected $args = [];
    public $route;

    public function __construct($route, $args = array()) {
        $this->route = $route;
        $path = '';
        $parts = explode('/', str_replace('../', '', $route));
        //TODO: rewrite the url library and the seo url rwrite controllers 
        $default_path = DIR_APPLICATION;
        if ($parts[0] == "modules") {
            //r=modules/mymodule/shop/home
            array_shift($parts);

            //r=mymodule/shop/home
            //$default_path = realpath(DIR_MODULE . $parts[0] . '/app') . '/';
            $default_path = DIR_MODULE . $parts[0] . '/app' . '/';
            array_shift($parts);
            
            //r=shop/home
            $default_path .= $parts[0] . '/';
            array_shift($parts);

            //r=home
        }

        foreach ($parts as $part) {
            $path .= $part;
            if (is_dir($default_path . 'controller/' . $path)) {
                $path .= '/';
                array_shift($parts);
                continue;
            }

            //TODO: put a debugger to notify when the convention name is wrong
            if (is_file($default_path . 'controller/' . str_replace('../', '', $path) . '.php')) {
                $this->file = $default_path . 'controller/' . str_replace('../', '', $path) . '.php';
                $this->class = 'Controller' . preg_replace('/[^a-zA-Z0-9]/', '', $path);
                array_shift($parts);
                break;
            }

            if ($args) {
                $this->args = $args;
            }
        }

        $method = array_shift($parts);

        if ($method) {
            $this->method = $method;
        } else {
            $this->method = 'index';
        }
    }

    public function getFile() {
        return $this->file;
    }

    public function getClass() {
        return $this->class;
    }

    public function getMethod() {
        return $this->method;
    }

    public function getArgs() {
        return $this->args;
    }

}
