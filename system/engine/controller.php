<?php

abstract class Controller {

    protected $registry;
    protected $id;
    protected $template;
    protected $namespace = '';
    protected $templatePath = null;
    protected $children = [];
    protected $childrenParams = [];
    protected $data = [];
    protected $widget = [];
    protected $output;
    protected $cacheId = null;

    /**
     * Each new instance it calls to Controller::init() 
     * Inside Controller::init() has to define all events, filters and hooks functions 
     * @return void
     */
    public function __construct($registry) {
        $this->registry = $registry;
        $this->init();
    }

    public function init() {}

    public function __get($key) {
        return $this->registry->get($key);
    }

    public function __set($key, $value) {
        $this->registry->set($key, $value);
    }

    public function get($key) {
        return isset($this->data[$key]) ? $this->data[$key] : null;
    }

    public function set($key, $value) {
        $v = $this->applyFilters("data:update", ["key"=>$key, "value"=>$value, "data"=>$this->data]);
        if (isset($v["value"])) $value = $v["value"];
        $this->data[$key] = $value;
        //trigger events
        $this->trigger("data:update", $this->data);
    }

    /**
     * Add a function listeners for a event
     * @param string $ev event name
     * @param callable $fn 
     * @see https://www.php.net/manual/en/language.types.callable.php
     * @see Events::on
     * @return void
     */
    public function on(string $ev, callable $fn)
    {
        Events::on($ev, $fn);
    }

    /**
     * Remove all functions listeners for a event
     * @param string $ev event name
     * @see Events::off
     * @return void
     */
    public function off(string $ev)
    {
        Events::off($ev);
    }

    /**
     * Execute all functions listeners for a event
     * @param string $ev event name
     * @param mixed $args 
     * @see Events::emit
     * @return void
     */
    public function trigger(string $ev, ...$args)
    {
        Events::emit($ev, $args);
    }

    /**
     * Add a filter function for a hook
     * @param string $name filter name
     * @param callable $fn 
     * @see https://www.php.net/manual/en/language.types.callable.php
     * @return void
     */
    public function addFilter(string $name, callable $fn)
    {
        global $hooks;
        $hooks->addFilter($this->namespace . $name, $fn);
    }

    /**
     * Add a filter function for a hook
     * @param string $name filter name
     * @param callable $fn 
     * @see https://www.php.net/manual/en/language.types.callable.php
     * @return void
     */
    public function applyFilters(string $name, $value)
    {
        global $hooks;
        return $hooks->applyFilters($name, $value);
    }

    public function addHook(string $name, callable $fn)
    {
        global $hooks;
        $hooks->addAction($name, $fn);
    }

    public function runHook(string $name, ...$args)
    {
        global $hooks;
        return $hooks->run($name, $args);
    }

    /**
     * Controller::setvar()
     * Asigna un valor a una variable 
     * @param string $varname
     * @param array $model
     * @return mixed $varname
     * */
    public function setvar($varname, $model = null, $default = null) {
        $config = $this->registry->get('config');
        if (isset($this->request->post[$varname])) {
            $this->set($varname, $this->request->post[$varname]);
        } elseif (isset($model) && isset($model[$varname])) {
            $this->set($varname, $model[$varname]);
        } elseif (isset($this->request->get[$varname])) {
            $this->data[$varname] = $this->request->get[$varname];
        } elseif (isset($config) && $config->get($varname)) {
            $this->set($varname, $config->get($varname));
        } elseif (isset($default)) {
            $this->set($varname, $default);
        } else {
            $this->set($varname, '');
        }

        return $this->data[$varname];
    }

    public function getvar($varname) {
        return isset($this->data[$varname]) ? $this->data[$varname] : false;
    }

    protected function forward($route, $args = array()) {
        //trigger events
        $this->trigger("forward", $route, $args);

        return new Action($route, $args);
    }

    protected function redirect($url) {
        //do actions for this model 
        $hasToReturn = $this->runHook("redirect", $url);
        if ($hasToReturn) {
            return $hasToReturn;
        }

        //trigger events
        $this->trigger("redirect", $url);
        
        if (!headers_sent()) {
            header('Location: ' . str_replace(array('&amp;', "\n", "\r"), array('&', '', ''), $url));
            exit;
        } else {
            echo "<script> window.location = '".str_replace('&amp;', '&', $url)."'; </script>";
        }
    }

    protected function addChild($child, $params = null) {
        if (!isset($child) || empty($child)) return false;

        array_push($this->children, $child);

        //trigger events
        $this->trigger("addChild", $child, $params);

        if (isset($params) || !empty($params)) {
            $this->childrenParams[$child] = $params;
        }
    }

    protected function getChild($child) {
        return isset($this->children[$child]) ? $this->children[$child] : false;
    }

    protected function getChildParams($child) {
        return isset($this->childrenParams[$child]) ? $this->childrenParams[$child] : false;
    }

    protected function getChildren() {
        return $this->children;
    }

    protected function render($return = false) {

        //do actions for this model 
        $hasToReturn = $this->runHook("render", $this, $return);
        if ($hasToReturn) {
            return $hasToReturn;
        }

        $cache = $this->registry->get('cache');
        $user = $this->registry->get('user');
        $browser = $this->registry->get('browser');
        $customer = $this->registry->get('customer');

        $device = '';
        if ($browser && is_callable([$browser,'isMobile'])) {
            if($browser->isMobile()) {
                $device = '.mobile';
            } elseif ($browser->isTablet()) {
                $device = '.tablet';
            } elseif ($browser->isFacebook()) {
                $device = '.facebook';
            } else {
                $device = '.pc';
            }
        }
        $this->cacheId .= $device;

        $customerLogged = 0;
        if ($customer && is_callable([$customer,'isLogged'])) {
            $customerLogged = $customer->isLogged();
        }
        $this->cacheId .= $customerLogged;

        if (isset($this->cacheId) && !empty($this->cacheId) && isset($user) && !$user->islogged()) {
            $cached = $cache->get($this->cacheId, substr($this->cacheId, 0, strpos($this->cacheId, '.')));
        }

        if (!isset($cached)) {
            if (defined('APP_PATH')) {
                if (str_replace('controller','',strtolower($this->ClassName)) != str_replace('/','',strtolower($this->Route))) $this->loadAssets($this->ClassName, APP_PATH);
                $this->loadAssets($this->Route, APP_PATH);
            } else {
                if (str_replace('controller','',strtolower($this->ClassName)) != str_replace('/','',strtolower($this->Route))) $this->loadAssets($this->ClassName);
                $this->loadAssets($this->Route);
            }
            
            foreach ($this->getChildren() as $key => $child) {
                $action = new Action($child);
                $file   = $action->getFile();
                $class  = $action->getClass();
                $method = $action->getMethod();
                $args   = $action->getArgs();

                if (defined('NTS_DEBUG_MODE') && NTS_DEBUG_MODE) {
                    $this->log->trace("Loading child {$class} for {$this->ClassName}");
                }

                if (!empty($file) && is_file($file)) {
                    require_once($file);
                    $controller = new $class($this->registry);
                    $params = isset($this->widget[$key]) ? $this->widget[$key] : $this->getChildParams($child);
                    
                    if (defined('APP_PATH')) {
                        if ($this->Method != 'index') $this->loadAssets($class.$this->Method, APP_PATH);
                        $this->loadAssets($class, APP_PATH);
                    } else {
                        if ($this->Method != 'index') $this->loadAssets($class.$this->Method);
                        $this->loadAssets($class);
                    }

                    $this->trigger('beforeLoad', $action, $controller, $params);

                    $controller->index($params);
                    $output = $controller->output;

                    $this->trigger('afterLoad', $action, $controller, $params);
                    
                    if (!is_numeric($key)) {
                        $this->data[$key . "_hook"] = $key;
                        $this->data[$key . "_code"] = $output;
                    } else {
                        $this->data[$controller->id] = $output;
                    }

                    if (defined('NTS_DEBUG_MODE') && NTS_DEBUG_MODE) {
                        $this->log->trace("Loaded child {$class} for {$this->ClassName}");
                    }
                } else {
                    if (!is_numeric($key)) echo 'Error: Could not load controller ' . $child . '!';
                    else exit('Error: Could not load controller ' . $child . '!');
                }
            }

            $tpl = $this->template;
            $this->trigger('beforeRender', $tpl);
            $r = $this->fetch($tpl);
            $this->trigger('afterRender', $r);

            if ($return) {
                if (isset($this->cacheId) && !empty($this->cacheId)) {
                    $cache->set($this->cacheId, $r, substr($this->cacheId, 0, strpos($this->cacheId, '.')));
                }
                return $r;
            } else {
                $this->output = $r;
            }
        } else {
            if ($return) {
                return $cached;
            } else {
                $this->output = $cached;
            }
        }
    }

    protected function fetch($filename)
    {
        //do actions for this model 
        $hasToReturn = $this->runHook("fetch", $filename, $this);
        if ($hasToReturn) {
            return $hasToReturn;
        }

        if ($this->templatePath && is_dir($this->templatePath)) {
            $file = $this->templatePath . $filename;
        } else {
            $file = DIR_TEMPLATE . $filename;
        }

        if (!empty($file) && is_file($file)) {
            $this->data['Config'] = $this->registry->get('config');
            $this->data['Language'] = $this->registry->get('language');
            $this->data['l'] = function(string $str):string { return $this->language->get($str); };
            $this->data['Request'] = $this->registry->get('request');
            $this->data['Url'] = new Url($this->registry);
            $this->data['Image'] = new NTImage;

            $User = $this->registry->get('user');
            if ($User->getId()) $this->data['is_admin'] = true;
            
            $class_name = get_class($this);
            if ($class_name == 'ControllerCommonFooter') $this->data['javascripts'] = $this->registry->get('javascripts');
            if ($class_name == 'ControllerCommonHeader') {
                $this->__loadCss();
                $this->data['header_javascripts'] = $this->registry->get('header_javascripts');
            }

            extract($this->data);
            ob_start();
            require($file);
            $content = ob_get_contents();
            ob_end_clean();

            foreach ($this->children as $key => $child) {
                if (!is_numeric($key)) {
                    //trigger events
                    $this->trigger("renderWidget", $key, $child);
                    $old_content = $content;
                    $content = str_replace('{%' . $this->data[$key . "_hook"] . '%}', $this->data[$key . "_code"], $content);
                    if ($old_content != $content) {
                        unset($this->children[$key]);
                    }
                }
            }

            foreach ($this->children as $key => $child) {
                if (!is_numeric($key)) {
                    //trigger events
                    $this->trigger("renderWidget", $key, $child);
                    $content = str_replace('{%' . $this->data[$key . "_hook"] . '%}', $this->data[$key . "_code"], $content);
                }
            }

            $content = preg_replace('!/\*.*?\*/!s', '', $content); //remove multiline comments
            $content = str_replace("/\n{2,}/", "", $content);
            $content = str_replace("/\r{2,}/", "", $content);
            if ($this->data['Config']->get('config_minified_html') && defined('STORE_ID')) {
                $content = str_replace("\n", "", $content);
                $content = str_replace("\r", "", $content);
                $content = preg_replace('/\s{2,}/', "", $content);
                $content = preg_replace('/\n\s*\n/', "\n", $content);
            }

            //apply filters
            $content = $this->applyFilters("render", $content);

            return $content;
        } elseif (empty($file)) {
            if (defined('NTS_DEBUG_MODE') && NTS_DEBUG_MODE) {
                echo("Error: Could not load empty template pointer in {$this->ClassName}!");
            }
        } else {            
            if (defined('NTS_DEBUG_MODE') && NTS_DEBUG_MODE) {
                exit("Error: Could not found template {$file} in {$this->ClassName}!");
            }
        }
    }

    protected function __loadCss() {
        $Config = $this->config;
        $styles = $this->styles;
        if (!isset($this->data['css'])) $this->data['css'] = "";

        $cssFolder = '';
        $csspath = defined("CDN_CSS") ? CDN_CSS : (defined("HTTP_THEME_CSS") ? HTTP_THEME_CSS : "");
        if (file_exists(DIR_TEMPLATE . $Config->get('config_template') . '/common/header.tpl')) {
            $csspath = str_replace("%theme%", $Config->get('config_template'), $csspath);
            if (defined("DIR_THEME_CSS")) $cssFolder = str_replace("%theme%", $Config->get('config_template'), DIR_THEME_CSS);
        } else {
            $csspath = str_replace("%theme%", "choroni", $csspath);
            if (defined("DIR_THEME_CSS")) $cssFolder = str_replace("%theme%", "choroni", DIR_THEME_CSS);
        }

        $cssFile = str_replace('/', '', strtolower($this->Route) . '.css');
        if (file_exists($cssFolder . $cssFile)) {
            if ($Config->get('config_render_css_in_file')) {
                $this->data['css'] .= file_get_contents($cssFolder . $cssFile);
            } else {
                $styles[$cssFile] = array('media' => 'all', 'href' => $csspath . $cssFile);
            }
        }

        if ($Config->get('config_render_css_in_file')) {
            $done = [];
            foreach ($styles as $k => $css) {
                if (in_array($css['href'], $done)) continue;
                if (!file_exists($css['href'])) continue;
                $done[] = $css['href'];
                $this->data['css'] .= file_get_contents($css['href']);
                $styles[$k] = null;
            }
        }

        //apply filters to css
        $this->data['css'] = $this->applyFilters("loadcss", $this->data['css']);

        if ($this->data['css']) {
            $this->data['css'] = str_replace("../../../images/", HTTP_IMAGE, $this->data['css']);
            $this->data['css'] = str_replace("../images/", str_replace('%theme%', $Config->get('config_template'), HTTP_THEME_IMAGE), $this->data['css']);
            $this->data['css'] = str_replace("../fonts/", str_replace('%theme%', $Config->get('config_template'), HTTP_THEME_FONT), $this->data['css']);
            $this->data['css'] = str_replace("../", '', $this->data['css']);
        }

        //apply filters to styles
        $styles = $this->applyFilters("loadstyles", $styles);

        if ($styles)
            $this->data['styles'] = $styles;
    }

    protected function loadWidgets($position, $landing_page = 'all', $app = 'shop', $full_tree = true) {

        $load = $this->registry->get('load');
        $request = $this->registry->get('request');
        $session = $this->registry->get('session');
        $browser = $this->registry->get('browser');
        $user = $this->registry->get('user');
        $customer = $this->registry->get('customer');
        $rows = [];

        //do actions for this model 
        $hasToReturn = $this->runHook("loadWidgets", [
            "position" => $position,
            "landing_page" => $session->get('landing_page') ? $session->get('landing_page') : $landing_page,
            "app" => $app,
            "full_tree" => $full_tree,
        ]);
        if ($hasToReturn) {
            return $hasToReturn;
        }

        if (!$browser) {
            $load->library('browser');
            $browser = $this->registry->get('browser');
        }

        $isMobile = $browser->isMobile();
        $isTablet = $browser->isTablet();
        $isFacebook = $browser->isFacebook();
        if (is_callable(array($customer, 'isLogged'), true)) $isCustomerLogged = (bool)$customer->isLogged();

        if ($user->getId() && $request->getQuery('force_mobile')) { $isMobile = true; }
        if ($user->getId() && $request->getQuery('force_tablet')) { $isTablet = true; }
        if ($user->getId() && $request->getQuery('force_facebook')) { $isFacebook = true; }
        if ($user->getId() && $request->getQuery('force_customer_session')) { $isCustomerLogged = true; }

        $load->helper('widgets');
        $widgets = new NecoWidget($this->registry, $this->Route);

        if ($full_tree) {

            $params = array(
                'store_id'=>STORE_ID,
                'landing_page'=>$session->get('landing_page'),
                'position'=>$position,
                'show_in_mobile'=>$isMobile,
                'show_in_tablet'=>$isTablet,
                'show_in_facebook'=>$isFacebook,
                'customer_session_mode'=>$isCustomerLogged,
                'conditional_logic_when_route_contains'=>$this->Route,
                'show_in_desktop'=>(!$isMobile && !$isTablet && !$isFacebook),
                'full_tree'=>$full_tree
            );

            //trigger events
            $this->trigger("beforeLoadWidget", $params);
            
            if (!strpos($position, 'only:')) {
                if (!isset($this->user)) $this->load->library('user');
                $rows = $widgets->getRows($params, !$this->user->getId());
                foreach ($rows as $row) {
                    if (!is_array($this->children) ) {
                        $this->children = [];
                    }
                    if (!is_array($this->widget) ) {
                        $this->widget = [];
                    }
                    if (isset($row['children']) && is_array($row['children']) && !empty($row['children'])) {
                        $this->children = array_merge($this->children, $row['children']);
                    }
                    if (isset($row['widget']) && is_array($row['widget']) && !empty($row['widget'])) {
                        $this->widget = array_merge($this->widget, $row['widget']);
                    }

                    $row_settings = unserialize($row['value']);
                    if ($row_settings['style']) {
                        $row_settings['style'] = preg_replace('!/\*.*?\*/!s', '', $row_settings['style']);
                        $row_settings['style'] = str_replace("\n", "", $row_settings['style']);
                        $row_settings['style'] = str_replace("\r", "", $row_settings['style']);
                        $row_settings['style'] = preg_replace('/\s{2,}/', "", $row_settings['style']);
                        $row_settings['style'] = preg_replace('/\n\s*\n/', "\n", $row_settings['style']);
                        $row_settings['style'] = trim($row_settings['style']);
                
                        if (!empty($row_settings['style'])) {
                            $this->css = array_merge($this->css, array(
                                $row['key']=>"\n/**{$row['key']}**/\n". $row_settings['style'] ."\n/** /{$row['key']}**/\n"
                            ));
                        }
                        //apply filters to styles
                        $this->css = $this->applyFilters("rowcss", $this->css);
                    }

                    if (isset($row['columns']) && !empty($row['columns'])) {
                        foreach ($row['columns'] as $column) {
                            $column_settings = unserialize($column['value']);
                            if ($column_settings['style']) {
                                $column_settings['style'] = preg_replace('!/\*.*?\*/!s', '', $column_settings['style']);
                                $column_settings['style'] = str_replace("\n", "", $column_settings['style']);
                                $column_settings['style'] = str_replace("\r", "", $column_settings['style']);
                                $column_settings['style'] = preg_replace('/\s{2,}/', "", $column_settings['style']);
                                $column_settings['style'] = preg_replace('/\n\s*\n/', "\n", $column_settings['style']);
                                $column_settings['style'] = trim($column_settings['style']);
                         
                                if (!empty($column_settings['style'])) {
                                    $this->css = array_merge($this->css, array(
                                        $column['key']=>"\n/**{$column['key']}**/\n". $column_settings['style'] ."\n/** /{$column['key']}**/\n"
                                    ));
                                }
                            }
                        }
                        //apply filters to styles
                        $this->css = $this->applyFilters("columncss", $this->css);
                    }
                }
            }
            
            if ($session->has('object_type') || $session->has('object_id')) {
                $params['position'] = $position = str_replace('only:', '', $position);
                if ($session->has('object_type')) $widgets->object_type = $session->get('object_type');
                if ($session->has('object_id')) $widgets->object_id = $session->get('object_id');

                $params['object_type'] = $session->get('object_type');
                $params['object_id'] = $session->get('object_id');
                
                if (!isset($this->user)) $this->load->library('user');
                $_rows = $widgets->getRows($params, !$this->user->getId());

                foreach ($_rows as $row) {
                    if (isset($row['children']) && is_array($row['children']) && is_array($this->children) && !empty($row['children'])) {
                        $this->children = array_merge($this->children, $row['children']);
                    }
                    if (isset($row['widget']) && is_array($row['widget']) && is_array($this->widget) && !empty($row['widget'])) {
                        $this->widget = array_merge($this->widget, $row['widget']);
                    }

                    $row_settings = unserialize($row['value']);
                    if (isset($row_settings['style'])) {
                        $row_settings['style'] = preg_replace('!/\*.*?\*/!s', '', $row_settings['style']);
                        $row_settings['style'] = str_replace("\n", "", $row_settings['style']);
                        $row_settings['style'] = str_replace("\r", "", $row_settings['style']);
                        $row_settings['style'] = preg_replace('/\s{2,}/', "", $row_settings['style']);
                        $row_settings['style'] = preg_replace('/\n\s*\n/', "\n", $row_settings['style']);
                        $row_settings['style'] = trim($row_settings['style']);
                
                        if (!empty($row_settings['style'])) {
                            $this->css = array_merge($this->css, array(
                                $row['key']=>"\n/**{$row['key']}**/\n". $row_settings['style'] ."\n/** /{$row['key']}**/\n"
                            ));
                        }
                        //apply filters to styles
                        $this->css = $this->applyFilters("rowcss", $this->css);
                    }

                    if (isset($row['columns']) && !empty($row['columns'])) {
                        foreach ($row['columns'] as $column) {
                            $column_settings = unserialize($column['value']);
                            if (isset($column_settings['style'])) {
                                $column_settings['style'] = preg_replace('!/\*.*?\*/!s', '', $column_settings['style']);
                                $column_settings['style'] = str_replace("\n", "", $column_settings['style']);
                                $column_settings['style'] = str_replace("\r", "", $column_settings['style']);
                                $column_settings['style'] = preg_replace('/\s{2,}/', "", $column_settings['style']);
                                $column_settings['style'] = preg_replace('/\n\s*\n/', "\n", $column_settings['style']);
                                $column_settings['style'] = trim($column_settings['style']);
                         
                                if (!empty($column_settings['style'])) {
                                    $this->css = array_merge($this->css, array(
                                        $column['key']=>"\n/**{$column['key']}**/\n". $column_settings['style'] ."\n/** /{$column['key']}**/\n"
                                    ));
                                }
                            }
                        }
                        //apply filters to styles
                        $this->css = $this->applyFilters("columncss", $this->css);
                    }
                }
            }
            if (isset($_rows) && is_array($_rows)) $rows = array_merge($rows, $_rows);
            $this->data['rows'][$position] = $rows;
        } else {
            foreach ($widgets->getWidgets($position, $app) as $k => $widget) {
                $settings = (array)unserialize($widget['settings']);

                //check if the customer has to be logged or not
                if ($settings['customer_session_mode'] == 'logon' && is_callable(array($customer, 'isLogged'), true) && !$customer->isLogged()) {
                    //customer needs to log in first
                    continue;
                    //TODO: log this 
                }

                if ($settings['customer_session_mode'] == 'logoff' && is_callable(array($customer, 'isLogged'), true) && $customer->isLogged()) {
                    //customer needs to log out first
                    continue;
                    //TODO: log this 
                }

                if (isset($settings['route'])) {
                    if (($browser->isMobile() && $settings['showonmobile']) || (!$browser->isMobile() && $settings['showondesktop'])) {
                        if ($settings['autoload']) {

                            $row_id = str_replace('row_id=','',$settings['row_id']);
                            $col_id = str_replace('col_id=','',$settings['col_id']);

                            $rows[$row_id]['columns'][$col_id]['column'] = $settings['column'];
                            $rows[$row_id]['columns'][$col_id]['widgets'][$k] = $widget['name'];

                        }

                        //trigger events
                        $this->trigger("loadWidget", $widget, $settings);
                        
                        $this->children[$widget['name']] = $settings['route'];
                        $this->widget[$widget['name']] = $widget;
                    }
                }
            }

            if ($session->has('object_type') || $session->has('object_id')) {
                //loading widgets just for this object_type
                if ($session->has('object_type')) $widgets->object_type = $session->get('object_type');

                //loading widgets just for this object id
                if ($session->has('object_id')) $widgets->object_id = $session->get('object_id');

                foreach ($widgets->getWidgets($position, $app) as $widget) {
                    $settings = (array)unserialize($widget['settings']);

                    //check if the customer has to be logged or not
                    if ($settings['customer_session_mode'] == 'logon' && is_callable(array($customer, 'isLogged'), true) && !$customer->isLogged()) {
                        //customer needs to log in first
                        continue;
                        //TODO: log this 
                    }

                    if ($settings['customer_session_mode'] == 'logoff' && is_callable(array($customer, 'isLogged'), true) && $customer->isLogged()) {
                        //customer needs to log out first
                        continue;
                        //TODO: log this 
                    }

                    if (isset($settings['route'])) {
                        if (($browser->isMobile() && $settings['showonmobile']) || (!$browser->isMobile() && $settings['showondesktop'])) {
                            if ($settings['autoload']) {

                                $row_id = str_replace('row_id=','',$settings['row_id']);
                                $col_id = str_replace('col_id=','',$settings['col_id']);

                                $rows[$row_id]['columns'][$col_id]['column'] = $settings['column'];
                                $rows[$row_id]['columns'][$col_id]['widgets'][$k] = $widget['name'];

                            }

                            //trigger events
                            $this->trigger("loadWidget", $widget, $settings);
            
                            $this->children[$widget['name']] = $settings['route'];
                            $this->widget[$widget['name']] = $widget;
                        }
                    }
                }
            }
            $this->data['rows'][$position] = $rows;
        }
    }

    protected function loadAssets($classname, $subfolder = null) {
        if (!$classname) return false;
        $filename = str_replace('/', '', str_replace('controller', '', strtolower($classname)));
        $this->_loadAssets($filename, $subfolder);
    }

    protected function _loadAssets($filename, $subfolder = null) {
        //TODO: optimize this function, the file scheme validation is executed everytime, put this into some memoization or cache
        if (!$filename) return false;

        if (!$this->assetLoaded) {
            $this->registry->set('assetLoaded', array());
        }
        $assetLoaded = $this->registry->get('assetLoaded');

        //do actions for this model 
        $hasToReturn = $this->runHook("loadAssets", [
            "filename" => $filename,
            "subfolder" => $subfolder,
        ]);
        if ($hasToReturn) {
            return $hasToReturn;
        }

        if (in_array($filename, $assetLoaded)) return false;
        array_push($assetLoaded, $filename);
        $this->registry->set('assetLoaded', $assetLoaded);

        $config = $this->registry->get('config');

        if (!isset($subfolder)) {
            $render_css_in_file = $config->get('config_render_css_in_file');
            $render_js_in_file = $config->get('config_render_js_in_file');
            $template = $config->get('config_template');
            if (!file_exists(DIR_TEMPLATE . $template . '/common/header.tpl')) $template = 'choroni';
            if (!file_exists(DIR_TEMPLATE . $template . '/common/header.tpl')) return false;
            

            $csspath = defined("CDN_CSS") ? CDN_CSS : HTTP_THEME_CSS;
            $jspath = defined("CDN_JS") ? CDN_JS : HTTP_THEME_JS;

            if (file_exists(DIR_TEMPLATE . $template . '/common/header.tpl')) {
                $csspath = str_replace("%theme%", $template, $csspath);
                $cssFolder = str_replace("%theme%", $template, DIR_THEME_CSS);

                $jspath = str_replace("%theme%", $template, $jspath);
                $jsFolder = str_replace("%theme%", $template, DIR_THEME_JS);
            } else {
                $csspath = str_replace("%theme%", "default", $csspath);
                $cssFolder = str_replace("%theme%", "default", DIR_THEME_CSS);

                $jspath = str_replace("%theme%", "default", $jspath);
                $jsFolder = str_replace("%theme%", "default", DIR_THEME_JS);
            }
        } else {
            $render_css_in_file = $config->get('config_'. strtolower($subfolder) .'_render_css_in_file');
            $render_js_in_file = $config->get('config_'. strtolower($subfolder) .'_render_js_in_file');
            $template = $config->get('config_'. strtolower($subfolder) .'_template');
            if (!file_exists(DIR_TEMPLATE . $template . '/common/header.tpl')) $template = 'default';
            if (!file_exists(DIR_TEMPLATE . $template . '/common/header.tpl')) return false;
            $csspath = defined("CDN_". strtoupper($subfolder) ."_CSS") ? constant("CDN_". strtoupper($subfolder) ."_CSS") : constant('HTTP_'. strtoupper($subfolder) .'_THEME_CSS');
            $jspath = defined("CDN_". strtoupper($subfolder) ."_JS") ? constant("CDN_". strtoupper($subfolder) ."_JS") : constant('HTTP_'. strtoupper($subfolder) .'_THEME_JS');

            if (file_exists(DIR_TEMPLATE . $template . '/common/header.tpl')) {
                $csspath = str_replace("%theme%", $template, $csspath);
                $cssFolder = str_replace("%theme%", $template, constant('DIR_'. strtoupper($subfolder) .'_THEME_CSS'));

                $jspath = str_replace("%theme%", $template, $jspath);
                $jsFolder = str_replace("%theme%", $template, constant('DIR_'. strtoupper($subfolder) .'_THEME_JS'));
            }
        }

        //apply filters
        $csspath = $this->applyFilters("csspath", $csspath, $template);
        $cssFolder = $this->applyFilters("cssfolder", $cssFolder, $template, $subfolder);
        $jsFolder = $this->applyFilters("jsfolder", $jsFolder, $template, $subfolder);
        $jspath = $this->applyFilters("jspath", $jspath, $template);

        if (file_exists($cssFolder . $filename .'.css')) {
            if ($render_css_in_file) {
                $_css = file_get_contents($cssFolder . $filename .'.css');
                $_css = $this->applyFilters("processcss", $cssFolder, $filename, $template);
                $this->data['css'] .= $_css;
            } else {
                $styles[$filename .'.css'] = array('media' => 'all', 'href' => $csspath . $filename .'.css');
            }
        }
        
        if (file_exists($jsFolder . $filename .'.js')) {
            if ($render_js_in_file) {
                $javascripts[$filename .'.js'] = $jsFolder . $filename .'.js';
            } else {
                $javascripts[$filename .'.js'] = $jspath . $filename .'.js';
            }
        }

        //apply filters to styles file list
        if (isset($styles)) $styles = $this->applyFilters("loadstyles", $styles);

        //apply filters to javascripts file list
        if (isset($javascripts)) $javascripts = $this->applyFilters("loadjavascripts", $javascripts);

        if (isset($styles)) $this->styles = array_merge($this->styles, $styles);
        if (isset($javascripts)) $this->javascripts = array_merge($this->javascripts, $javascripts);
    }
}