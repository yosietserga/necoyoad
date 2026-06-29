<?php

/**
 * ControllerWidgetController
 * 
 * @package  NecoTienda
 * @author Yosiet Serga
 * @copyright Inversiones Necoyoad, C.A.
 * @version 1.1.0
 * @access public
 * @see Controller
 */
class ControllerWidgetController extends Controller {
    protected string $moduleName = '';
	protected array $error = [];
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

    public function init()
    {
        if (empty(trim($this->moduleName))) {
            throw new Exception("Can't load this module, does not have explicit module name for controller {$this->ClassName} and route {$this->Route}");
        }
    }

    public function index($widget = null, $render = false)
    {
        $this->init();
        //do actions for this controller method 
        $hasToReturn = $this->runHook("index", $this);
        if ($hasToReturn) {
            return $hasToReturn;
        }

        if (!$this->request->hasQuery('name')) return false;

        $this->trigger('widgetLoad', [
            'widget'   => $widget,
            'render'   => $render,
        ]);

        //for animate.css
        $effects = [
            'animate.css' => [
                'Attention Seekers' => [
                    'bounce' => 'bounce',
                    'flash' => 'flash',
                    'pulse' => 'pulse',
                    'rubberBand' => 'rubberBand',
                    'shake' => 'shake',
                    'swing' => 'swing',
                    'tada' => 'tada',
                    'wobble' => 'wobble',
                    'jello' => 'jello'
                ],

                'Bouncing Entrances' => [
                    'bounceIn' => 'bounceIn',
                    'bounceInDown' => 'bounceInDown',
                    'bounceInLeft' => 'bounceInLeft',
                    'bounceInRight' => 'bounceInRight',
                    'bounceInUp' => 'bounceInUp'
                ],

                'Bouncing Exits' => [
                    'bounceOut' => 'bounceOut',
                    'bounceOutDown' => 'bounceOutDown',
                    'bounceOutLeft' => 'bounceOutLeft',
                    'bounceOutRight' => 'bounceOutRight',
                    'bounceOutUp' => 'bounceOutUp'
                ],

                'Fading Entrances' => [
                    'fadeIn' => 'fadeIn',
                    'fadeInDown' => 'fadeInDown',
                    'fadeInDownBig' => 'fadeInDownBig',
                    'fadeInLeft' => 'fadeInLeft',
                    'fadeInLeftBig' => 'fadeInLeftBig',
                    'fadeInRight' => 'fadeInRight',
                    'fadeInRightBig' => 'fadeInRightBig',
                    'fadeInUp' => 'fadeInUp',
                    'fadeInUpBig' => 'fadeInUpBig'
                ],

                'Fading Exits' => [
                    'fadeOut' => 'fadeOut',
                    'fadeOutDown' => 'fadeOutDown',
                    'fadeOutDownBig' => 'fadeOutDownBig',
                    'fadeOutLeft' => 'fadeOutLeft',
                    'fadeOutLeftBig' => 'fadeOutLeftBig',
                    'fadeOutRight' => 'fadeOutRight',
                    'fadeOutRightBig' => 'fadeOutRightBig',
                    'fadeOutUp' => 'fadeOutUp',
                    'fadeOutUpBig' => 'fadeOutUpBig'
                ],

                'Flippers' => [
                    'flip' => 'flip',
                    'flipInX' => 'flipInX',
                    'flipInY' => 'flipInY',
                    'flipOutX' => 'flipOutX',
                    'flipOutY' => 'flipOutY'
                ],

                'Lightspeed' => [
                    'lightSpeedIn' => 'lightSpeedIn',
                    'lightSpeedOut' => 'lightSpeedOut'
                ],

                'Rotating Entrances' => [
                    'rotateIn' => 'rotateIn',
                    'rotateInDownLeft' => 'rotateInDownLeft',
                    'rotateInDownRight' => 'rotateInDownRight',
                    'rotateInUpLeft' => 'rotateInUpLeft',
                    'rotateInUpRight' => 'rotateInUpRight'
                ],

                'Rotating Exits' => [
                    'rotateOut' => 'rotateOut',
                    'rotateOutDownLeft' => 'rotateOutDownLeft',
                    'rotateOutDownRight' => 'rotateOutDownRight',
                    'rotateOutUpLeft' => 'rotateOutUpLeft',
                    'rotateOutUpRight' => 'rotateOutUpRight'
                ],

                'Sliding Entrances' => [
                    'slideInUp' => 'slideInUp',
                    'slideInDown' => 'slideInDown',
                    'slideInLeft' => 'slideInLeft',
                    'slideInRight' => 'slideInRight'
                ],

                'Sliding Exits' => [
                    'slideOutUp' => 'slideOutUp',
                    'slideOutDown' => 'slideOutDown',
                    'slideOutLeft' => 'slideOutLeft',
                    'slideOutRight' => 'slideOutRight'
                ],

                'Zoom Entrances' => [
                    'zoomIn' => 'zoomIn',
                    'zoomInDown' => 'zoomInDown',
                    'zoomInLeft' => 'zoomInLeft',
                    'zoomInRight' => 'zoomInRight',
                    'zoomInUp' => 'zoomInUp'
                ],

                'Zoom Exits' => [
                    'zoomOut' => 'zoomOut',
                    'zoomOutDown' => 'zoomOutDown',
                    'zoomOutLeft' => 'zoomOutLeft',
                    'zoomOutRight' => 'zoomOutRight',
                    'zoomOutUp' => 'zoomOutUp'
                ],

                'Specials' => [
                    'hinge' => 'hinge',
                    'jackInTheBox' => 'jackInTheBox',
                    'rollIn' => 'rollIn',
                    'rollOut' => 'rollOut'
                ],
            ]
        ];

        $this->data['transition_effects'] = $effects;

        $this->load->helper('widgets');

        $widget = new NecoWidget($this->registry);

        if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
            $widget_name = $this->request->getQuery('name');
            $widget_position = $this->request->getQuery('position');
            $data = $this->request->post['Widgets'][$widget_name];
            $data['name'] = $widget_name;
            $data['position'] = $widget_position;

            $settings = new stdClass;
            foreach ($data['settings'] as $key => $value) {
                $settings->$key = $value;
            }
            $data['settings'] = $settings;

            $this->cache->delete('widgets-rows');
            $this->cache->delete('widgets-cols');
            $this->cache->delete('widgets-widgets');
            $this->cache->delete('widgets-widget-' . $widget_name);

            if ($widget->save($data)) {
                $json['success'] = 1;
            } else {
                $json['error'] = 1;
                $json['msg'] = $this->language->get('error_saving_widget');
            }
        } else {
            $data['name']           = ($this->request->hasQuery('name')) ? $this->request->getQuery('name') : null;
            $data['row_id']         = ($this->request->hasQuery('row_id')) ? $this->request->getQuery('row_id') : null;
            $data['col_id']         = ($this->request->hasQuery('col_id')) ? $this->request->getQuery('col_id') : null;
            $data['landing_page']   = 'all';
            $data['position']       = ($this->request->hasQuery('position')) ? $this->request->getQuery('position') : null;
            $data['extension']      = ($this->request->hasQuery('extension')) ? $this->request->getQuery('extension') : null;
            $data['app']            = ($this->request->hasQuery('app')) ? $this->request->getQuery('app') : 'shop';
            $data['order']          = ($this->request->hasQuery('order')) ? $this->request->getQuery('order') : 0;
            $data['store_id']       = ($this->request->hasQuery('store_id')) ? $this->request->getQuery('store_id') : 0;

            if ($this->request->hasQuery('store_id')) {
                $this->load->auto('store/store');
                $cfg = $this->modelStore->getSettings('config', 'config_template', $this->request->getQuery('store_id'));
                $t = $cfg[0]['value'];
            } else {
                $t = $this->config->get('config_template');
            }

            $views = glob(DIR_CATALOG . 'view/theme/' . $t . '/module/' . $this->moduleName . '_*.tpl', GLOB_NOSORT);
            $this->data['views'] = $this->data['view_files'] = [];
            foreach ($views as $view) {
                $this->data['views'][] = str_replace(array($this->moduleName . '_', '.tpl'), '', basename($view));
                $this->data['view_files'][] = realpath($view);
            }

            $this->load->model('localisation/language');
            $this->data['languages'] = $this->modelLanguage->getAll();

            $m = str_replace(array('-', '_', ' ', '.'), '', 'module' . $this->moduleName);
            $files = glob(DIR_THEME_ASSETS . $this->config->get('config_template') . "/css/$m*.css");
            $this->data['css_files'] = [];
            foreach ($files as $key => $file) {
                $this->data['css_files'][] = realpath($file);
            }

            $files = glob(DIR_THEME_ASSETS . $this->config->get('config_template') . "/js/$m*.js");
            $this->data['js_files'] = [];
            foreach ($files as $key => $file) {
                $this->data['js_files'][] = realpath($file);
            }

            $this->data['module_view_file_prefix'] = $this->moduleName . '_';
            $this->data['module_view_folder'] = DIR_CATALOG . 'view' . DIRECTORY_SEPARATOR . 'theme' . DIRECTORY_SEPARATOR . $t . DIRECTORY_SEPARATOR . 'module' . DIRECTORY_SEPARATOR;
            $this->data['module_css_file_prefix'] = $m;
            $this->data['module_css_folder'] = DIR_THEME_ASSETS . $t . DIRECTORY_SEPARATOR . "css" . DIRECTORY_SEPARATOR;
            $this->data['module_js_file_prefix'] = $m;
            $this->data['module_js_folder'] = DIR_THEME_ASSETS . $t . DIRECTORY_SEPARATOR . "js" . DIRECTORY_SEPARATOR;

            if ($this->request->hasQuery('w')) {
                $this->load->model('style/widget');
                $widget_info = $this->modelWidget->getByName($data['name']);
                $this->setvar('widget_id', $widget_info);
                $this->setvar('code', $widget_info);
                $this->setvar('name', $widget_info);
                $this->setvar('position', $widget_info);
                $this->setvar('extension', $widget_info);
                $this->setvar('status', $widget_info);
                $this->setvar('app', $widget_info);
                $this->setvar('order', $widget_info);
                $this->setvar('store_id', $widget_info);

                $landing_pages = [];
                foreach ($widget_info['landing_pages'] as $lp) {
                    $landing_pages[] = $lp['landing_page'];
                }
                $this->data['landing_pages'] = $landing_pages;

                $this->data['settings'] = (array)unserialize($widget_info['settings']);
            } else {
                $settings               = new stdClass;
                $settings->route        = 'module/' . $this->moduleName;
                $settings->autoload     = 1;
                $settings->showonmobile = 1;
                $settings->showondesktop= 1;
                $settings->view         = 'default';
                $settings->customer_session_mode = 'any';
                //TODO: add rules flexibility to create custom rules conditions to show and hide
                $settings->conditional_logic_action = 'show';

                $settings->landing_page = 'landing_page=all';

                $lp = $this->request->hasPost('landing_page') ? $this->request->getPost('landing_page') : $this->request->getQuery('landing_page');
                $ot = $this->request->hasPost('ot') ? $this->request->getPost('ot') : $this->request->getQuery('ot');
                $oid = $this->request->hasPost('oid') ? $this->request->getPost('oid') : $this->request->getQuery('oid');
                $row_id = $this->request->hasPost('row_id') ? $this->request->getPost('row_id') : $this->request->getQuery('row_id');
                $col_id = $this->request->hasPost('col_id') ? $this->request->getPost('col_id') : $this->request->getQuery('col_id');
                $offsetY = $this->request->hasPost('offsetY') ? $this->request->getPost('offsetY') : $this->request->getQuery('offsetY');
                $offsetX = $this->request->hasPost('offsetX') ? $this->request->getPost('offsetX') : $this->request->getQuery('offsetX');

                if ($lp) $settings->landing_page = 'landing_page=' . $lp;
                if ($ot) $settings->object_type = 'object_type=' . $ot;
                if ($oid) $settings->object_id = 'object_id=' . $oid;
                if ($row_id) $settings->row_id = 'row_id=' . $row_id;
                if ($col_id) $settings->col_id = 'col_id=' . $col_id;
                if ($offsetY) $settings->offsetY = 'offsetY=' . $offsetY;
                if ($offsetX) $settings->offsetX = 'offsetX=' . $offsetX;

                $this->data['settings'] = (array)$settings;

                $data['settings']   = $settings;
                $widget->save($data);
                $this->setvar('name');
            }


            //apply filters to settings
            $widget = $this->applyFilters("widget:settings", $this->data);

            $this->data['module'] = $this->moduleName;

            $json = $this->data;

            $template = ($this->config->get('default_admin_view_module_' . $this->moduleName . '_widget')) ? $this->config->get('default_admin_view_module_' . $this->moduleName . '_widget') : 'module/' . $this->moduleName . '/widget.tpl';
            if (file_exists(DIR_TEMPLATE . $this->config->get('config_admin_template') . '/' . $template)) {
                $this->template = $this->config->get('config_admin_template') . '/' . $template;
            } else {
                $this->template = 'default/' . $template;
            }

            $json['html'] = $this->render(true);
        }

        $this->load->library('json');
        $this->response->setOutput(Json::encode($json), $this->config->get('config_compression'));
    }

    public function async()
    {
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