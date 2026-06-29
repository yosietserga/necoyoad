<?php

class ControllerCommonFooter extends Controller {

    protected function index() {
        $this->load->language('common/footer');
        
        $this->data['text_footer'] = sprintf($this->language->get('text_footer'), VERSION);

        $this->id = 'footer';

        $r_output = $w_output = $s_output = $f_output = $jsx_output = "";
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
                case 'jsx':
                    $jsx_output .= $script['script'];
                    break;
                case 'window':
                    $w_output .= $script['script'];
                    break;
                case 'function':
                    $f_output .= $script['script'];
                    break;
            }
        }

        if ($this->config->get('config_admin_render_js_in_file')) {
            foreach ($this->javascripts as $key => $js) {
                if  (!file_exists($js)) continue;
                $f_output .= file_get_contents($js);
                unset($this->javascripts[$key]);
            }
        }
        
        $f_output = str_replace('{%token%}', $this->request->getQuery('token'), $f_output);
        $f_output = str_replace('{%http_home%}', HTTP_HOME, $f_output);

        $this->data['scripts'] = ($r_output) ? "<script>$(function() { " . $r_output . " });</script>" : "";
        $this->data['scripts'] .= ($w_output) ? "<script>window.onload = function() { " . $w_output . "  };</script>" : "";
        $this->data['scripts'] .= ($f_output) ? "<script>" . $f_output . "</script>" : "";
        $this->data['scripts'] .= ($jsx_output) ? "<script type=\"text/jsx\">" . $jsx_output . "</script>" : "";

        $this->data['javascripts'] = $this->javascripts;

        $template = ($this->config->get('default_admin_view_footer')) ? $this->config->get('default_admin_view_footer') : 'common/footer.tpl';
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_admin_template') . '/'. $template)) {
            $this->template = $this->config->get('config_admin_template') . '/' . $template;
        } else {
            $this->template = 'default/' . $template;
        }

        $this->render();
    }
}