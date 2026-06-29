<?php

/**
 * ControllerModuleModuleController
 * 
 * @package  NecoTienda
 * @author Yosiet Serga
 * @copyright Inversiones Necoyoad, C.A.
 * @version 1.1.0
 * @access public
 * @see Controller
 */
class ControllerModuleModuleController extends Module {
    protected string $moduleName = '';
    protected array $defaults = [];

    /**
     * Module::on
     * 
     * Add a function listeners for a event
     * 
     * @param string $ev event name
     * @param callable $fn 
     * 
     * @uses self::$moduleName
     * @uses \Events::on
     * 
     * @see url {@link https://www.php.net/manual/en/language.types.callable.php}
     * @see \Events::on
     * 
     * @return void
     */
    public function on(string $ev, callable $fn)
    {
        Events::on($this->moduleName . $ev, $fn);
    }

    /**
     * Module::off
     * 
     * Remove all functions listeners for a event
     * 
     * @param string $ev event name
     * 
     * @uses self::$moduleName
     * 
     * @see {@link https://www.php.net/manual/en/language.types.callable.php}
     * @see \Events::off
     * 
     * @return void
     */
    public function off(string $ev)
    {
        Events::off($this->moduleName . $ev);
    }

    /**
     * Module::trigger
     * 
     * Execute all functions listeners for a event
     * 
     * @param string $ev event name
     * @param mixed $args 
     * 
     * @uses self::$moduleName
     * @see Events::emit
     * 
     * @return void
     */
    public function trigger(string $ev, ...$args)
    {
        Events::emit($this->moduleName . $ev, $args); //for this model moduleName
        Events::emit($ev, $args); //for all models
    }

    /**
     * Module::addFilter
     * 
     * Add a filter function for a hook
     * 
     * @param string $name filter name
     * @param callable $fn 
     * 
     * @uses self::$moduleName
     * @see url {@link https://www.php.net/manual/en/language.types.callable.php}
     * 
     * @return void
     */
    public function addFilter(string $name, callable $fn)
    {
        global $hooks;
        $hooks->addFilter("module:{$this->moduleName}:{$name}", $fn);
    }

    /**
     * Module::applyFilters
     * 
     * Add a filter function for a hook
     * 
     * @param string $name filter name
     * @param array $data to filter
     * 
     * @uses self::$moduleName
     * @uses global $hooks 
     * @see \Hooks
     * 
     * @return mixed $data filtered
     */
    public function applyFilters(string $name, $data)
    {
        global $hooks;
        $data = $hooks->applyFilters($name, $data); //for all models
        $data = $hooks->applyFilters("module:{$this->moduleName}:{$name}", $data); //for this model moduleName
        return $data;
    }

    /**
     * Module::addHook
     * 
     * Add a hook listener
     * 
     * @param string $name hook name
     * @param callable $fn function listener
     * 
     * @uses self::$moduleName
     * @uses global $hooks 
     * @see \Hooks
     * @see {@link https://www.php.net/manual/en/language.types.callable.php}
     * 
     * @return void
     */
    public function addHook(string $name, callable $fn)
    {
        global $hooks;
        $hooks->addAction("module:{$this->moduleName}:{$name}", $fn);
    }

    /**
     * Module::runHook
     *
     * Execute all hooks listeners for this event
     * If return any boolean true value, will change or break the sequence stack 
     * 
     * @param string $name hook name 
     * @param mixed $args
     * 
     * @uses self::$moduleName
     * @uses global $hooks 
     * @see \Hooks
     * 
     * @return void|boolean|mixed
     */
    public function runHook(string $name, ...$args)
    {
        global $hooks;
        $hooks->run($name, $args); //for all models
        return $hooks->run("module:{$this->moduleName}:{$name}", $args); //for this model moduleName
    }

    public function init() {
        if (empty(trim($this->moduleName))) {
            throw new Exception("Can't load this module, does not have explicit module name for controller {$this->ClassName} and route {$this->Route}");
        }
    }

    protected function index($widget = null, $render = false) {
        $this->init();
        //do actions for this controller method 
        $hasToReturn = $this->runHook("index", $this);
        if ($hasToReturn) {
            return $hasToReturn;
        }


        $this->trigger('moduleLoad', [
            'widget'   => $widget,
            'render'   => $render,
        ]);
        
        $this->language->load('module/' . $this->moduleName);

        if (isset($widget)) {
            $settings = (array)unserialize($widget['settings']);
            $this->data['widget_hook'] = $this->data['widgetName'] = $widget['name'];
        }

        if (!isset($settings['module'])) $settings['module'] = $this->moduleName;

        if (!empty($this->defaults)) {
            foreach ($this->defaults as $k => $v) {
                $settings[$k] = isset($settings[$k]) ? $settings[$k] : $v;
            }
        }

        $settings['title'] = isset($settings['title']) ? $settings['title'] : (isset($default['title']) ? $default['title'] : '');
        $settings['view']  = isset($settings['view']) ? $settings['view'] : (isset($default['view']) ? $default['view'] : 'default');

        $this->data['heading_title'] = $settings['title'];

        if (isset($settings['style'])) {
            $settings['style'] = preg_replace('!/\*.*?\*/!s', '', $settings['style']);
            $settings['style'] = str_replace("\n", "", $settings['style']);
            $settings['style'] = str_replace("\r", "", $settings['style']);
            $settings['style'] = preg_replace('/\s{2,}/', "", $settings['style']);
            $settings['style'] = preg_replace('/\n\s*\n/', "\n", $settings['style']);
            $settings['style'] = trim($settings['style']);

            if (!empty($settings['style'])) {
                $this->css = array_merge($this->css, array(
                    $widget['name'] => "\n/**" . $widget['name'] . "**/\n" . $settings['style'] . "\n/** /" . $widget['name'] . "**/\n"
                ));
            }
        }
        $route = 'module/'. str_replace('controllermodule', '', strtolower(str_replace(["_"], "", $settings["route"]))) .'/'. $settings['view'];
        $this->loadDeps($route);
        
        $filename = str_replace('controller', '', strtolower(str_replace(["_","/"], "", $settings["route"]))) . $settings['view'];
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/module/' . $this->moduleName . '.tpl')) {
            $this->template = $this->config->get('config_template') . '/module/' . $this->moduleName . '.tpl';
        } else {
            $this->template = 'choroni/module/' . $this->moduleName . '.tpl';
        }

        //apply filters to settings
        $_settings = $this->applyFilters("module:settings", [
            'widget'   => $widget,
            'render'   => $render,
            'settings' => $settings,
        ]);

        if (isset($_settings['settings']) && $_settings['settings']) $settings = $_settings['settings'];
        
        $this->data['settings'] = $settings;
        $this->id = $this->moduleName;

        if (!$this->request->hasQuery('cve')) {
            if ($render) {
                $this->javascripts = [];
                $this->scripts = [];
                $this->styles = [];

                $html = $this->render(true);

                $this->loadWidgetAssets($filename, null, true);

                $return = [
                    'id'        => $widget['name'],
                    'settings'  => $settings,
                    'javascripts' => $this->javascripts,
                    'scripts'   => $this->scripts,
                    'styles'    => $this->styles,
                    'css'       => $this->css,
                    'html'      => $html
                ];

                $this->trigger('moduleAsyncResponse', $return);
                
                $this->load->auto('json');
                $this->response->setOutput(Json::encode($return), $this->config->get('config_compression'));
            } else {
                $this->trigger('moduleRender', [
                    'id'        => $widget['name'],
                    'settings'  => $settings,
                    'javascripts' => $this->javascripts,
                    'scripts'   => $this->scripts,
                    'styles'    => $this->styles,
                    'css'       => $this->css,
                ]);
                $this->loadWidgetAssets($filename);
                $this->render();
            }
        } else {
            $this->template = 'choroni/module/theme_editor_placeholder.tpl';

            $this->loadWidgetAssets($filename, null, true);

            $html = $this->render(true);

            $return = array(
                'id'        => $widget['name'],
                'settings'  => $settings,
                'javascripts' => $this->javascripts,
                'scripts'   => $this->scripts,
                'styles'    => $this->styles,
                'css'       => $this->css,
                'html'      => $html
            );

            $this->trigger('moduleEditorResponse', $return);
                
            $this->load->auto('json');
            $this->response->setOutput(Json::encode($return), $this->config->get('config_compression'));
        }
    }

    public function async() {
        $this->init();
        //do actions for this controller method 
        $hasToReturn = $this->runHook("async", $this);
        if ($hasToReturn) {
            return $hasToReturn;
        }

        $this->load->helper('widgets');

        $w = new NecoWidget($this->registry, $this->request->getQuery('route'));

        $name = $this->request->hasPost('w') ? $this->request->getPost('w') : $this->request->getQuery('w');
        //TODO: put in admin params settings to use cache or not
        $widget = $w->getWidget($name, false);
        if ($widget) {
            $this->index($widget, true);
        }
    }
}
