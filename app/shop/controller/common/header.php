<?php

class ControllerCommonHeader extends Controller {

    protected function index($params = null) {
        $this->load->library('browser');
        $Url = new Url($this->registry);
        $browser = new Browser;
        if ($browser->getBrowser() == 'Internet Explorer' && $browser->getVersion() <= 9) {
            $this->redirect($Url::createUrl("page/deprecated"));
        }

        if ($this->request->hasQuery('hl') || $this->request->hasQuery('cc')) {

            if ($this->request->hasQuery('_route_')) {
                $this->session->set('redirect', HTTP_HOME . $this->request->getQuery('_route_'));
            } elseif ($this->request->hasQuery('r')) {
                $data = $this->request->get;
                unset($data['_route_']);
                $route = $data['r'];
                unset($data['r']);
                unset($data['cc']);
                unset($data['hl']);
                $url = '';

                if ($data) {
                    $url = '&' . urldecode(http_build_query($data));
                }

                $this->session->set('redirect', $Url::createUrl($route, $url));
            } else {
                $this->session->set('redirect', HTTP_HOME);
            }
        }

        if ($this->request->hasQuery('hl')) {
            $this->session->set('language', $this->request->getQuery('hl'));
            if (!$this->request->hasQuery('cc')) {
                if ($this->session->has('redirect')) {
                    $this->redirect($this->session->get('redirect'));
                } else {
                    $this->redirect(HTTP_HOME);
                }
            }
        }

        if ($this->request->hasQuery('cc')) {
            $this->currency->set($this->request->getQuery('cc'));
            $this->session->clear('shipping_methods');
            $this->session->clear('shipping_method');
            if ($this->session->has('redirect')) {
                $this->redirect($this->session->get('redirect'));
            } else {
                $this->redirect(HTTP_HOME);
            }
        }

        if (!$this->session->has('token')) {
            $this->session->set('token', md5(rand().time()));
        }

        $this->data['token'] = $this->session->get('token');

        if (isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) {
            $this->data['base'] = HTTPS_HOME;
        } else {
            $this->data['base'] = HTTP_HOME;
        }

        if ($this->config->get('config_icon') && file_exists(DIR_IMAGE . $this->config->get('config_icon'))) {
            $this->data['icon'] = HTTP_IMAGE . $this->config->get('config_icon');
        } else {
            $this->data['icon'] = '';
        }

        if ($this->config->get('config_logo') && file_exists(DIR_IMAGE . $this->config->get('config_logo'))) {
            $this->data['logo'] = HTTP_IMAGE . $this->config->get('config_logo');
        } else {
            $this->data['logo'] = '';
        }

        $this->data['title'] = $this->document->title;
        $this->data['keywords'] = $this->document->keywords;
        $this->data['description'] = $this->document->description;
        $this->data['template'] = $this->config->get('config_template');
        $this->data['charset'] = $this->language->get('charset');
        $this->data['lang'] = $this->language->get('code');
        $this->data['direction'] = $this->language->get('direction');
        $this->data['links'] = $this->document->links;
        $this->data['breadcrumbs'] = $this->document->breadcrumbs;

        if (isset($params['product']) && !empty($params['product'])) {
            $this->data['opengraph']['og:type'] = 'product';
            $this->data['opengraph']['og:title'] = $params['product']['name'];
            $this->data['opengraph']['og:description'] = $params['product']['overview'];
            $this->data['opengraph']['og:url'] = $Url::createUrl('store/product',array('product_id'=>$params['product']['product_id']));
            $this->data['opengraph']['og:image'] = $params['product']['images'][0]['preview'];
            $this->data['opengraph']['product:plural_title'] = $params['product']['name'];
            $this->data['opengraph']['product:price:amount'] = $params['product']['original_price'];
            $this->data['opengraph']['product:price:currency'] = $this->config->get('config_currency');
            $this->data['headAttributes'] = ' prefix="og: https://ogp.me/ns# fb: https://ogp.me/ns/fb# product: https://ogp.me/ns/product#"';
        }

        if (isset($params['category']) && !empty($params['category'])) {
            $this->data['opengraph']['og:type'] = 'product.group';
            $this->data['opengraph']['og:title'] = $params['category']['name'];
            $this->data['opengraph']['og:description'] = $params['category']['overview'];
            $this->data['opengraph']['og:url'] = $Url::createUrl('store/category',array('path'=>$params['category']['category_id']));
            $this->data['opengraph']['og:image'] = $params['category']['thumb'];
            $this->data['headAttributes'] = ' prefix="og: https://ogp.me/ns# fb: https://ogp.me/ns/fb# product: https://ogp.me/ns/product#"';
        }

        $this->load->library('user');
        if ($this->user->getId()) {
            $this->data['is_admin'] = true;
            $this->language->load('common/admin');
            if ($this->request->hasQuery('theme_editor')) {
                $this->data['theme_editor'] = true;
                $this->data['url_widgets_load']= Url::createUrl('module/{%widgetModule%}/async');
                $this->data['url_widgets_save']= Url::createAdminUrl('module/{%widgetModule%}/widget', array(), 'NONSSL', HTTP_ADMIN);
                $this->data['url_widgets_savecol']= Url::createAdminUrl('style/widget/savecol', array(), 'NONSSL', HTTP_ADMIN);
                $this->data['url_widgets_saverow']= Url::createAdminUrl('style/widget/saverow', array(), 'NONSSL', HTTP_ADMIN);

                $this->data['url_widgets_sortable']= Url::createAdminUrl('style/widget/sortable', array(), 'NONSSL', HTTP_ADMIN);
                $this->data['url_widgets_sortrow']= Url::createAdminUrl('style/widget/sortrow', array(), 'NONSSL', HTTP_ADMIN);
                $this->data['url_widgets_sortcol']= Url::createAdminUrl('style/widget/sortcol', array(), 'NONSSL', HTTP_ADMIN);

                $this->data['url_widgets_delete']= Url::createAdminUrl('style/widget/delete', array(), 'NONSSL', HTTP_ADMIN);
                $this->data['url_widgets_deletecolumn']= Url::createAdminUrl('style/widget/deletecolumn', array(), 'NONSSL', HTTP_ADMIN);
                $this->data['url_widgets_deleterow']= Url::createAdminUrl('style/widget/deleterow', array(), 'NONSSL', HTTP_ADMIN);

                $this->load->model('setting/extension');
                $extensions = $this->modelExtension->getInstalled('module');
                $this->data['extensions'] = [];
                $modules = glob(DIR_ADMIN_APPLICATION . "controller/module/*");
                if ($modules) {
                    foreach ($modules as $module) {
                        if (!file_exists($module . '/widget.php'))
                            continue;
                        $extension = basename($module, '/widget.php');
                        $m = basename($module);
                        $this->load->language('module/' . $m);

                        if (in_array($extension, $extensions)) {
                            $this->data['modules'][] = array(
                                'widget' => $extension,
                                'name' => $this->language->get('heading_title'),
                                'description' => $this->language->get('description')
                            );
                        }
                    }
                }

            }

        }

        $this->loadWidgets('header', 'shop', true);
        
        $this->loadCss();
        $this->loadJs();

        $this->data['store'] = $this->config->get('config_name');
        $this->data['isLogged'] = $this->customer->isLogged();

        if ($this->customer->isLogged()) {
            $this->data['greetings'] = 'Bienvenido(a), ' . ucwords($this->customer->getFirstName() . ' ' . $this->customer->getLastName());
        }

        if (isset($this->request->get['q'])) {
            $this->data['q'] = $this->request->get['q'];
        } else {
            $this->data['q'] = '';
        }

        if (isset($this->request->get['category_id'])) {
            $this->data['category_id'] = $this->request->get['category_id'];
        } elseif (isset($this->request->get['path'])) {
            $path = explode('_', $this->request->get['path']);
            $this->data['category_id'] = end($path);
        } else {
            $this->data['category_id'] = 0;
        }

        if (isset($this->request->get['product_id'])) {
            $this->data['product_id'] = $this->request->get['product_id'];
        } else {
            $this->data['product_id'] = 0;
        }

        if (isset($this->request->get['manufacturer_id'])) {
            $this->data['manufacturer_id'] = $this->request->get['manufacturer_id'];
        } else {
            $this->data['manufacturer_id'] = 0;
        }

        /*
          // Auto suggest through email and while is online
          $this->track->autoSuggest(array(
          'category_id'       =>$this->data['category_id'],
          'product_id'        =>$this->data['product_id'],
          'manufacturer_id'   =>$this->data['manufacturer_id'],
          'q'                 =>$this->data['q']
          ));
         */

        if (!isset($this->request->get['r'])) {
            $this->session->set('redirect', HTTP_HOME);
        } else {
            $data = $this->request->get;
            unset($data['_route_']);
            $route = $data['r'];
            unset($data['r']);
            $url = '';

            if ($data) {
                $url = '&' . urldecode(http_build_query($data));
            }

            $this->session->set('redirect', Url::createUrl($route, $url));
        }
        $this->data['current_url'] = $this->session->get('redirect');

        $this->data['language_code'] = $this->session->get('language');
        $this->data['languages'] = [];
        $results = $this->modelLanguage->getLanguages();

        foreach ($results as $result) {
            if ($result['status']) {
                $this->data['languages'][] = array(
                    'name' => $result['name'],
                    'code' => $result['code'],
                    'image' => HTTP_IMAGE . "flags/" . $result['image']
                );
            }
        }

        $this->data['currency_code'] = $this->currency->getCode();
        $this->data['currencies'] = [];
        $results = $this->modelCurrency->getCurrencies();

        if (is_array($results)) {
            foreach ($results as $result) {
                if ($result['status']) {
                    $this->data['currencies'][] = array(
                        'title' => $result['title'],
                        'code' => $result['code']
                    );
                }
            }
        }

        $this->session->set('state', md5(rand()));

        $this->id = 'header';
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/common/header.tpl')) {
            $this->template = $this->config->get('config_template') . '/common/header.tpl';
        } else {
            $this->template = 'choroni/common/header.tpl';
        }

        $this->render();
    }

    public function getLanguages() {
        $this->data['languages'] = [];
        $this->load->auto('localisation/language');
        $results = $this->modelLanguage->getLanguages();

        foreach ($results as $result) {
            if ($result['status']) {
                $this->data['languages'][] = array(
                    'name' => $result['name'],
                    'code' => $result['code'],
                    'image' => HTTP_IMAGE . "flags/" . $result['image']
                );
            }
        }

        $this->data['redirect'] = $this->session->get('redirect');

        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/localisation/languages.tpl')) {
            $this->template = $this->config->get('config_template') . '/localisation/languages.tpl';
        } else {
            $this->template = 'choroni/localisation/languages.tpl';
        }

        $this->response->setOutput($this->render(true), $this->config->get('config_compression'));
    }

    public function getCurrencies() {
        $this->load->auto('localisation/currency');
        $this->data['currencies'] = [];
        $results = $this->modelCurrency->getCurrencies();

        foreach ($results as $result) {
            if ($result['status']) {
                $this->data['currencies'][] = array(
                    'title' => $result['title'],
                    'code' => $result['code']
                );
            }
        }

        $this->data['redirect'] = $this->session->get('redirect');

        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/localisation/currencies.tpl')) {
            $this->template = $this->config->get('config_template') . '/localisation/currencies.tpl';
        } else {
            $this->template = 'choroni/localisation/currencies.tpl';
        }

        $this->response->setOutput($this->render(true), $this->config->get('config_compression'));
    }

    protected function loadJs() {
        $f_output = '';
        if ($this->config->get('config_render_js_in_file')) {
            $done = [];
            foreach ($this->header_javascripts as $key => $js) {
                if (in_array($js, $done)) continue;
                if (!file_exists($js)) continue;
                $done[] = $js;
                $f_output .= file_get_contents($js);
            }
            $this->header_javascripts = null;
            $this->data['scripts'] .= ($f_output) ? "<script> \n " . $f_output . " </script>" : "";
        } else {
            $this->data['header_javascripts'] = $this->header_javascripts;
        }

    }

    protected function loadCss() {
        $this->data['css'] = "";

        $csspath = defined("CDN_CSS") ? CDN_CSS : HTTP_THEME_CSS;
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/common/header.tpl')) {
            $csspath = str_replace("%theme%", $this->config->get('config_template'), $csspath);
            $cssFolder = str_replace("%theme%", $this->config->get('config_template'), DIR_THEME_CSS);
        } else {
            $csspath = str_replace("%theme%", "choroni", $csspath);
            $cssFolder = str_replace("%theme%", "choroni", DIR_THEME_CSS);
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
        
        $this->load->auto('style/theme');
        $cssmainpath = defined("CDN_CSS") ? CDN_CSS : HTTP_CSS;
        $theme = $this->modelTheme->getById($this->config->get('theme_default_id'));
        if (isset($theme['theme_id']) && !empty($theme['theme_id'])) {
            if (file_exists(DIR_CSS . "custom-" . $theme['theme_id'] . "-" . $this->config->get('config_template') . ".css")) {
                $this->data['css'] .= file_get_contents($cssmainpath . "custom-" . $theme['theme_id'] . "-" . $this->config->get('config_template') . ".css");
            } elseif (file_exists($cssFolder . "custom-" . $theme['theme_id'] . "-" . $this->config->get('config_template') . ".css")) {
                $this->data['css'] .= file_get_contents($csspath . "custom-" . $theme['theme_id'] . "-" . $this->config->get('config_template') . ".css");
            }

        }

        $this->load->library('user');
        if ($this->user->getId()) {
            $this->data['is_admin'] = true;
            $styles[] = array('media' => 'all', 'href' => HTTP_ADMIN . 'css/frontend/admin.css');
        }
        if (is_array($styles) && count($styles))
            $this->styles = array_merge($this->styles, $styles);
    }
}