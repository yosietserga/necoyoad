<?php

class ControllerCommonHeader extends Controller {

    /**
     * ControllerCommonHeader::index()
     * 
     * @return
     */
    protected function index() {
        if ($this->request->hasQuery('hl')) {
            $this->session->set('language', $this->request->getQuery('hl'));
            if ($this->session->has('redirect')) {
                $this->redirect($this->session->get('redirect'));
            } else {
                $this->redirect(Url::createAdminUrl('common/home'));
            }
        }
        
        $this->load->language('common/header');
        $this->data['title'] = $this->document->title . " | NecoTienda";
        $this->data['breadcrumbs'] = $this->document->breadcrumbs;
        $this->data['heading_title'] = $this->language->get('heading_title');

        $this->load->library('browser');
        $browser = new Browser;
        if ($browser->getBrowser() == 'Internet Explorer' && $browser->getVersion() <= 8) {
            $this->redirect(Url::createUrl("page/deprecated", null, 'NONSSL', HTTP_CATALOG));
        }

        if (!$this->user->validSession()) {
            $this->data['logged'] = '';
            $this->data['home'] = Url::createAdminUrl('common/login');
        } else {
            $this->data['logged'] = sprintf($this->language->get('text_logged'), $this->user->getUserName());

            if ($this->session->has('success')) {
                $this->data['success'] = $this->session->get('success');
                $this->session->clear('success');
            }

            if ($this->session->has('error')) {
                $this->data['error'] = $this->session->get('error');
                $this->session->clear('error');
            }

            $this->load->auto("store/store");
            $this->data['stores'] = $this->modelStore->getAll();
        }

        $this->loadCss();

        $this->id = 'header';

        $template = ($this->config->get('default_admin_view_header')) ? $this->config->get('default_admin_view_header') : 'common/header.tpl';
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_admin_template') . '/'. $template)) {
            $this->template = $this->config->get('config_admin_template') . '/' . $template;
        } else {
            $this->template = 'default/' . $template;
        }

        $this->render();
    }

    protected function loadCss() {
        $csspath = defined("CDN_CSS") ? CDN_CSS : HTTP_ADMIN_CSS;

        if ($this->config->get('config_admin_render_css_in_file')) {
            foreach ($this->styles as $k => $css) {
                if (in_array($css['href'], $done)) conitnue;
                if (!file_exists($css['href'])) conitnue;
                $this->data['css'] .= file_get_contents($css['href']);
                $done[] = $css['href'];
                unset($this->styles[$k]);
            }
        }

        if (isset($this->data['css'])) {
            $this->data['css'] = str_replace("../../../images/", HTTP_IMAGE, $this->data['css']);
            $this->data['css'] = str_replace("../images/", str_replace('%theme%', $this->config->get('config_admin_template'), HTTP_ADMIN_THEME_IMAGE), $this->data['css']);
            $this->data['css'] = str_replace("../fonts/", str_replace('%theme%', $this->config->get('config_admin_template'), HTTP_ADMIN_THEME_FONT), $this->data['css']);
        }

        if (isset($styles)) 
            $this->styles = array_merge($this->styles, $styles);

        $this->data['styles'] = $this->styles;
    }

}
