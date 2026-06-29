<?php

class ControllerCommonFooter extends Controller {

    protected function index($params = null) {
        $this->language->load('common/footer');
        $this->load->library('user');
        $this->data['config_js_security'] = $this->config->get('config_js_security');

        $config_text_powered_by = $this->config->get('config_text_powered_by');
        if (!empty($config_text_powered_by)) {
            $this->data['text_powered_by'] = html_entity_decode(sprintf($config_text_powered_by, $this->config->get('config_name'), date('Y')));
        } else {
            $this->data['text_powered_by'] = sprintf($this->language->get('text_powered_by'), $this->config->get('config_name'));
        }

        $this->id = 'footer';

        // SCRIPTS
        if ($this->config->get('config_seo_url')) {
            $urlBase = HTTP_HOME . 'buscar/';
        } else {
            $urlBase = HTTP_HOME . 'index.php?r=store/search&q=';
        }
        $scripts[] = array('id' => 'search', 'method' => 'function', 'script' =>
            "function moduleSearch(keyword) {
            var url = '" . $urlBase . "';
            var form = $(keyword).closest('form');
            var category = $('#'+ $(keyword).attr('id').replace('Keyword','Category')).val();
            var store = $('#'+ $(keyword).attr('id').replace('Keyword','Store')).val();
            var zone = $('#'+ $(keyword).attr('id').replace('Keyword','Zone')).val();
            
            url += $(keyword).val()
                .replace(/_/g,'-')
                .replace('+','-')
                .replace(/\s+/g,'-');
            
            if (typeof category != 'undefined') {
                url += '_Cat_'+ category
                    .replace(/_/g,'-')
                    .replace('+','-')
                    .replace(/\s+/g,'-');
            }
            
            if (typeof zone != 'undefined') {
                url += '_Estado_'+ zone
                    .replace(/_/g,'-')
                    .replace('+','-')
                    .replace(/\s+/g,'-');
            }
            
            if (typeof store != 'undefined') {
                url += '_Tienda_'+ store
                    .replace(/_/g,'-')
                    .replace('+','-')
                    .replace(/\s+/g,'-');
            }
            
            window.location = url;
        }

        function moduleSearchFilters(data) {
            var url = data.baseUrl;
            var form = data.form;
            $(form).find('input').each(function(i,item){
                url += '_Filtro_';
                url += $(item).attr('name')
                        .replace(/_/g,'-')
                        .replace(/\//g,'-')
                        .replace('+','-')
                        .replace(/\s+/g,'-');
                url += '+';
                url += $(item).val()
                    .replace(/_/g,'-')
                    .replace(/\//g,'-')
                    .replace('+','-')
                    .replace(/\s+/g,'-');
                console.log(url);
            });
            window.location = url;
        }");
        
        $this->scripts = array_merge($this->scripts, $scripts);
        $r_output = $w_output = $s_output = $f_output = "";
        $script_keys = [];
        foreach ($this->scripts as $k => $script) {
            if (in_array($script['id'], $script_keys))
                continue;
            $script_keys[$k] = $script['id'];
            switch ($script['method']) {
                case 'ready':
                default:
                    $r_output .= $script['script'];
                    break;
                case 'window':
                    $w_output .= $script['script'];
                    break;
                case 'function':
                    $f_output .= $script['script'];
                    break;
            }
        }
        
        $this->loadWidgets('footer');
        
        $this->loadCss();
        $this->loadJs();
        
        if ($this->config->get('config_render_js_in_file')) {
            $done = [];
            foreach ($this->javascripts as $key => $js) {
                if (in_array($js, $done) || !file_exists($js)) continue;
                $done[] = $js;
                $f_output .= file_get_contents($js);
            }
            $this->javascripts = null;
            $javascripts = null;
        }
        
        if (!isset($this->data['scripts'])) $this->data['scripts'] = "";
        $this->data['scripts'] .= ($f_output) ? "<script> \n " . $f_output . " </script>" : "";
        $this->data['scripts'] .= $s_output;
        $this->data['scripts'] .= ($r_output) ? "<script> \n $(function(){" . $r_output . "}); </script>" : "";
        $this->data['scripts'] .= ($w_output) ? "<script> \n (function($){ $(window).load(function(){ " . $w_output . " }); })(jQuery);</script>" : "";

        if (isset($javascripts) && $javascripts)
            $this->javascripts = array_merge($this->javascripts, $javascripts);

        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/common/footer.tpl')) {
            $this->template = $this->config->get('config_template') . '/common/footer.tpl';
        } else {
            $this->template = 'choroni/common/footer.tpl';
        }
        
        $this->data['google_analytics_code'] = $this->config->get('google_analytics_code');
        $this->data['live_client_id'] = $this->config->get('social_live_client_id');
        $this->data['facebook_app_id'] = $this->config->get('social_facebook_app_id');
        $this->data['google_client_id'] = $this->config->get('social_google_client_id');
        $this->data['twitter_oauth_token_secret'] = $this->config->get('social_twitter_oauth_token_secret');

        $this->render();
    }
    
    protected function loadJs() {
        $jspath = defined("CDN") ? CDN_JS : HTTP_THEME_JS;
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/common/header.tpl')) {
            $jspath = str_replace("%theme%", $this->config->get('config_template'), $jspath);
            $jsFolder = str_replace("%theme%", $this->config->get('config_template'), DIR_THEME_JS);
        } else {
            $jspath = str_replace("%theme%", "choroni", $jspath);
            $jsFolder = str_replace("%theme%", "choroni", DIR_THEME_JS);
        }

        if (file_exists($jsFolder . str_replace('/', '', strtolower($this->Route) . '.js'))) {
            if ($this->config->get('config_render_js_in_file')) {
                $javascripts[] = $jsFolder . str_replace('/', '', strtolower($this->Route) . '.js');
            } else {
                $javascripts[] = $jspath . str_replace('/', '', strtolower($this->Route) . '.js');
            }
        }

        $jspath = defined("CDN_JS") ? CDN_JS : HTTP_JS;
        
        // javascript files
        if ($this->user->getId()) {
            $javascripts[] = HTTP_ADMIN . "js/frontend/admin.js";

            if ($this->request->hasQuery('theme_editor') && $this->request->hasQuery('theme_id') && (int) $this->request->getQuery('theme_id') > 0) {
                $javascripts[] = $jspath . "vendor/jquery-ui.min.js";
                //$javascripts[] = $jspath . "necojs/neco.css.js";
                //$javascripts[] = $jspath . "necojs/neco.colorpicker.js";
                //$javascripts[] = HTTP_ADMIN . "js/frontend/theme_editor.js";
            }
        }

        if (is_array($javascripts) && count($javascripts)) {
            $this->javascripts = array_merge($this->javascripts, $javascripts);
        }
    }

    protected function loadCss() {
        $this->data['css'] = "";

        $csspath = defined("CDN") ? CDN_CSS : HTTP_THEME_CSS;
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/common/header.tpl')) {
            $csspath = str_replace("%theme%", $this->config->get('config_template'), $csspath);
            $cssFolder = str_replace("%theme%", $this->config->get('config_template'), DIR_THEME_CSS);
        } else {
            $csspath = str_replace("%theme%", "choroni", $csspath);
            $cssFolder = str_replace("%theme%", "choroni", DIR_THEME_CSS);
        }

        $cssFile = str_replace('/', '', strtolower($this->Route) . '.css');
        if (file_exists($cssFolder . $cssFile)) {
            if ($this->config->get('config_render_css_in_file')) {
                $this->data['css'] .= file_get_contents($cssFolder . $cssFile);
            } else {
                $styles[$cssFile] = array('media' => 'all', 'href' => $csspath . $cssFile);
            }
        }

        if (isset($styles) && is_array($styles) && !empty($styles)) {
            $this->styles = array_merge($this->styles, $styles);
        }

        if ($this->config->get('config_render_css_in_file')) {
            $done = [];
            foreach ($this->styles as $k => $css) {
                if (in_array($css['href'], $done)) continue;
                if (!file_exists($css['href'])) continue;
                $done[] = $css['href'];
                $this->data['css'] .= file_get_contents($css['href']);
            }
            $this->styles = null;
            $styles = null;
        }

        if (count($this->css)) {
            $_css = $this->css;
            $done = [];
            foreach ($this->css as $id => $css) {
                if (in_array($id, $done)) continue;
                $done[] = $id;
                $this->data['css'] .= $css;
                $_css[$id] = null;
            }
            $this->registry->set('css', $_css);
        }
        
        if ($this->data['css']) {
            $this->data['css'] = str_replace("../../../images/", HTTP_IMAGE, $this->data['css']);
            $this->data['css'] = str_replace("../images/", str_replace('%theme%', $this->config->get('config_template'), HTTP_THEME_IMAGE), $this->data['css']);
            $this->data['css'] = str_replace("../fonts/", str_replace('%theme%', $this->config->get('config_template'), HTTP_THEME_FONT), $this->data['css']);
        }
        $this->load->helper('tools');
        $tools = new NecoTool($this->registry);
        $this->data['css'] = $tools->minify($this->data['css']);

        if (isset($styles) && $styles)
            $this->styles = array_merge($this->styles, $styles);
    }
}
