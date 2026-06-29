<?php

class Module extends Controller
{

    public function __construct($registry) {
        parent::__construct($registry);

        $this->moduleClass = get_called_class();
        $this->moduleRoute = 'module/'. str_replace('controllermodule', '', strtolower($this->moduleClass));
        $this->loadDeps($this->moduleRoute);
    }

    public function __get($key) {
        return parent::__get($key);
    }

    public function __set($key, $value) {
        parent::__set($key, $value);
    }

    protected function loadDeps($route) {
        foreach ($this->js_assets as $i => $routes) {
            if (empty($routes)) continue;
            if ((is_array($routes) && in_array($route, $routes)) || $routes === '*') {
                $this->javascripts = array_merge($this->javascripts, array($i));
                unset($this->js_assets[$i]);
            }
        }

        foreach ($this->js_header_assets as $i => $routes) {
            if (empty($routes)) continue;
            if (is_array($routes) && in_array($route, $routes) || $routes === '*') {
                $this->header_javascripts = array_merge($this->header_javascripts, array($i));
                unset($this->js_header_assets[$i]);
            }
        }

        if ($this->jsx_assets) {
            foreach ($this->jsx_assets as $i => $routes) {
                if (empty($routes)) continue;
                if ((is_array($routes) && in_array($route, $routes)) || $routes === '*') {
                    $this->scripts = array_merge($this->scripts, array(
                        array(
                        'method' => 'jsx',
                        'id' => $i,
                        'script' => file_get_contents($i)
                        )
                    ));
                    unset($this->jsx_assets[$i]);
                }
            }
        }

        foreach ($this->css_assets as $i => $asset) {
            if (empty($asset['css'])) continue;
            if ((is_array($asset['routes']) && in_array($route, $asset['routes'])) || $asset['routes'] === '*') {
                $this->styles = array_merge($this->styles, array($asset['css']));
                break;
            }
        }
    }

    protected function loadWidgetAssets($filename, $subfolder = null, $async = false) {
        if (!$filename) return false;
        $this->_loadAssets($filename, $subfolder);

        if ($async && $this->config->get('config_render_css_in_file') && count($this->styles)>0) {
        	$styles = $this->styles;

        	$css_path = realpath(DIR_CSS);
        	$css_theme_path = realpath(str_replace('%theme%', $this->config->get('config_template'), DIR_THEME_CSS));
        	$css_theme_url = str_replace('%theme%', $this->config->get('config_template'), HTTP_THEME_CSS);
        	$css_theme_url = substr($css_theme_url, 0, strlen($css_theme_url) -1);
        	$css_url = substr(HTTP_CSS, 0, strlen(HTTP_CSS) -1);

        	foreach($styles as $k => $v) {
        		if (!empty($v['href']) && strpos(realpath($v['href']), $css_path) !== false) {
        			$styles[$k]['href'] = str_replace($css_path, $css_url, realpath($v['href']));
        		} elseif (!empty($v['href']) && strpos(realpath($v['href']), $css_theme_path) !== false) {
        			$styles[$k]['href'] = str_replace($css_theme_path, $css_theme_url, realpath($v['href']));
        		}
        	}
        	$this->styles = $styles;
        }

        if ($async && $this->config->get('config_render_js_in_file') && count($this->javascripts)>0) {
        	$javascripts = $this->javascripts;

        	$js_path = realpath(DIR_JS);
        	$js_theme_path = realpath(str_replace('%theme%', $this->config->get('config_template'), DIR_THEME_JS));
        	$js_theme_url = str_replace('%theme%', $this->config->get('config_template'), HTTP_THEME_JS);
        	$js_theme_url = substr($js_theme_url, 0, strlen($js_theme_url) -1);
        	$js_url = substr(HTTP_JS, 0, strlen(HTTP_JS) -1);

        	foreach($javascripts as $k => $v) {
        		if (!empty($v) && strpos(realpath($v), $js_path) !== false) {
        			$javascripts[$k] = str_replace($js_path, $js_url, realpath($v));
        		} elseif (!empty($v) && strpos(realpath($v), $js_theme_path) !== false) {
        			$javascripts[$k] = str_replace($js_theme_path, $js_theme_url, realpath($v));
        		}
        	}
        	$this->javascripts = $javascripts;
        }

    }
}