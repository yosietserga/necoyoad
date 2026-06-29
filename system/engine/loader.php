<?php

final class Loader {

    protected $registry;

    public function __construct($registry) {
        $this->registry = $registry;
    }

    public function __get($key) {
        return $this->registry->get($key);
    }

    public function __set($key, $value) {
        $this->registry->set($key, $value);
    }

    public function auto($route) {
        if (file_exists(DIR_SYSTEM . 'library' . DIRECTORY_SEPARATOR . $route . ".php")) {
            include_once(DIR_SYSTEM . 'library' . DIRECTORY_SEPARATOR . $route . ".php");
        } elseif (file_exists(DIR_SYSTEM . 'helper' . DIRECTORY_SEPARATOR . $route . ".php")) {
            include_once(DIR_SYSTEM . 'helper' . DIRECTORY_SEPARATOR . $route . ".php");
        } elseif (file_exists(DIR_APPLICATION . 'model' . DIRECTORY_SEPARATOR . $route . ".php")) {
            $this->model($route);
        } elseif (file_exists(DIR_APPLICATION . 'language' . DIRECTORY_SEPARATOR . $route . ".php")) {
            $this->language($route);
        }
    }

    public function library($library) {
        $file = DIR_SYSTEM . 'library/' . $library . '.php';

        if (file_exists($file)) {
            if (class_exists("Events") && is_callable([Events::class, 'emit'])) {
                Events::emit(strtolower(__CLASS__) . ":library:load", $library);
            }
            include_once($file);
        } else {
            if (class_exists("Events") && is_callable([Events::class, 'emit'])) {
                Events::emit(strtolower(__CLASS__) . ":library:fail", $library);
            }
            exit('<div class="msg error">Error: Could not load library ' . $library . '!</div>');
        }
    }
    
    public function controller(string $route) {
        $file = DIR_APPLICATION . 'controller/' . $route . '.php';
        $class = 'Controller' . preg_replace('/[^a-zA-Z0-9]/', '', $route);

        if (file_exists($file)) {
            include_once($file);
            $instance = new $class($this->registry);
            if (class_exists("Events") && is_callable([Events::class, 'emit'])) {
                Events::emit(strtolower(__CLASS__) . ":controller:load", $class, $route);
            }
            return $instance;
        } else {
            if (class_exists("Events") && is_callable([Events::class, 'emit'])) {
                Events::emit(strtolower(__CLASS__) . ":controller:fail", $class, $route);
            }
            exit('<div class="msg error">Error: Could not found controller in route ' . $route . '!</div>');
        }
    }

    public function model($model, $return = false) {
        $file = DIR_APPLICATION . 'model/' . $model . '.php';
        $class = 'Model' . preg_replace('/[^a-zA-Z0-9]/', '', $model);

        if (file_exists($file)) {
            include_once($file);
            $m = array_reverse(explode("/", $model));
            $model_instance = new $class($this->registry);
            $model_name = 'model' . ucfirst($m[0]);

            $this->registry->set($model_name, $model_instance);
            $this->registry->set('model_' . str_replace('/', '_', $model), $model_instance); //legacy

            if (class_exists("Events") && is_callable([Events::class, 'emit'])) {
                Events::emit(strtolower(__CLASS__) . ":model:load", $model_name, $model);
            }
            if ($return) {
                return $this->registry->get($model_name);
            }
        } else {
            if (class_exists("Events") && is_callable([Events::class, 'emit'])) {
                Events::emit(strtolower(__CLASS__) . ":model:fail", $model_name, $model);
            }
            exit('<div class="msg error">Error: Could not load model ' . $model . '!</div>');
        }
    }

    public function database($driver, $hostname, $username, $password, $database, $return = null) {
        $file = DIR_SYSTEM . 'library/db.php';
        $class = 'DB';

        if (file_exists($file)) {
            include_once($file);
            $db_instance = new $class($driver, $hostname, $username, $password, $database);

            if (class_exists("Events") && is_callable([Events::class, 'emit'])) {
                Events::emit(strtolower(__CLASS__) . ":database:load", $db_instance, $driver, $hostname, $username, $password, $database);
            }
            if ($return) {
                return $db_instance;
            } else {
                //TODO: set non-blocking name into registry
                $this->registry->set(str_replace('/', '_', $driver), $db_instance);
            }
        } else {
            if (class_exists("Events") && is_callable([Events::class, 'emit'])) {
                Events::emit(strtolower(__CLASS__) . ":database:fail", $driver, $hostname, $username, $password, $database);
            }
            exit('<div class="msg error">Error: Could not load database ' . $driver . '!</div>');
        }
    }

    public function helper($helper) {
        $file = DIR_SYSTEM . 'helper/' . $helper . '.php';

        if (file_exists($file)) {

            if (class_exists("Events") && is_callable([Events::class, 'emit'])) {
                Events::emit(strtolower(__CLASS__) . ":helper:load", $helper);
            }
            include_once($file);
        } else {
            if (class_exists("Events") && is_callable([Events::class, 'emit'])) {
                Events::emit(strtolower(__CLASS__) . ":helper:fail", $helper);
            }
            exit('<div class="msg error">Error: Could not load helper ' . $helper . '!</div>');
        }
    }

    public function config($config) {
        $this->config->load($config);
    }

    public function language($language) {
        return $this->language->load($language);
    }

    public function moduleModel(string $model, string $path, bool $return = null) {
        $file = $path . '/model/' . $model . '.php';
        $class = 'Model' . preg_replace('/[^a-zA-Z0-9]/', '', $model);

        if (file_exists($file)) {
            include_once($file);
            $m = array_reverse(explode("/", $model));
            $model_name = 'model' . ucfirst($m[0]);
            $model_instance = new $class($this->registry);
            $this->registry->set($model_name, $model_instance);
            if (class_exists("Events") && is_callable([Events::class, 'emit'])) {
                Events::emit(strtolower(__CLASS__) . ":modulemodel:load", $model_name, $model, $path);
            }
            if ($return) {
                return $model_instance;
            }
        } else {
            if (class_exists("Events") && is_callable([Events::class, 'emit'])) {
                Events::emit(strtolower(__CLASS__) . ":modulemodel:fail", $model_name, $model, $path);
            }
            exit('Error: Could not load model ' . $model . '!');
            exit('<div class="msg error">Error: Could not load model ' . $file . '!</div>');
        }
    }

    public function moduleLibrary($library, $path) {
        $file = $path . '/vendor/' . $library . '.php';

        if (file_exists($file)) {
            if (class_exists("Events") && is_callable([Events::class, 'emit'])) {
                Events::emit(strtolower(__CLASS__) . ":modulelibrary:load", $library, $path);
            }
            include_once($file);
        } else {
            if (class_exists("Events") && is_callable([Events::class, 'emit'])) {
                Events::emit(strtolower(__CLASS__) . ":modulelibrary:fail", $library, $path);
            }
            exit('<div class="msg error">Error: Could not load library ' . $file . '!</div>');
        }
    }

    public function moduleLanguage($language, $path) {
        return $this->language->load($language, $path);
    }
}
