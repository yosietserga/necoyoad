<?php

class ControllerAccountLogin extends Controller {

    private $error = [];

    public function index() {
        $this->session->clear('object_type');
        $this->session->clear('object_id');
        $this->session->clear('landing_page');

        $Url = new Url($this->registry);
        if ($this->customer->isLogged()) {
            $this->redirect(Url::createUrl("account/account"));
        }

        $this->activarUser();
        $this->language->load('account/login');
        $this->document->title = $this->language->get('heading_title');

        if ($this->request->server['REQUEST_METHOD'] == 'POST' && !empty($this->request->post['email']) && !empty($this->request->post['password']) && $this->validate()) {
            if (isset($this->request->post['redirect'])) {
                $this->redirect($this->request->post['redirect']);
            } elseif ($this->session->has('redirect')) {
                $this->redirect($this->session->get('redirect'));
            } else {
                $this->redirect(Url::createUrl("account/account"));
            }
        }

        $this->document->breadcrumbs = [];
        $this->document->breadcrumbs[] = array(
            'href' => $Url::createUrl("common/home"),
            'text' => $this->language->get('text_home'),
            'separator' => false
        );
        $this->document->breadcrumbs[] = array(
            'href' => $Url::createUrl("account/account"),
            'text' => $this->language->get('text_account'),
            'separator' => $this->language->get('text_separator')
        );
        $this->document->breadcrumbs[] = array(
            'href' => $Url::createUrl("account/login"),
            'text' => $this->language->get('text_login'),
            'separator' => $this->language->get('text_separator')
        );

        $this->data['breadcrumbs'] = $this->document->breadcrumbs;

        $this->data['error'] = isset($this->error['message']) ? $this->error['message'] : '';
        $this->data['action'] = $Url::createUrl("account/login");
        $this->data['register'] = $Url::createUrl("account/register");

        if (isset($this->request->post['redirect'])) {
            $this->data['redirect'] = $this->request->post['redirect'];
        } elseif ($this->session->has('redirect') && strpos($this->session->get('redirect'), 'login') == -1) {
            $this->data['redirect'] = $this->session->get('redirect');
        } else {
            $this->data['redirect'] = '';
        }

        if ($this->request->hasQuery('error')) {
            $this->data['error'] = $this->language->get('error_login');
        }

        if ($this->session->has('success')) {
            $this->data['success'] = $this->session->get('success');
            $this->session->clear('success');
        } else {
            $this->data['success'] = '';
        }

        if ($this->session->has('account')) {
            $this->data['account'] = $this->session->get('account');
        } else {
            $this->data['account'] = 'register';
        }

        $this->load->model('localisation/country');
        $this->data['countries'] = $this->modelCountry->getCountries();
        $this->data['page_legal_terms_id'] = ($this->config->get('config_account_id')) ? $this->config->get('config_account_id') : 0;
        $this->data['page_privacy_terms_id'] = ($this->config->get('config_account_id')) ? $this->config->get('config_account_id') : 0;

        $this->session->set('state', md5(rand()));
        $this->data['live_client_id'] = $this->config->get('social_live_client_id');
        $this->data['google_client_id'] = $this->config->get('social_google_client_id');
        $this->data['facebook_app_id'] = $this->config->get('social_facebook_app_id');
        $this->data['twitter_oauth_token_secret'] = $this->config->get('social_twitter_oauth_token_secret');

        $this->data['forgotten'] = $Url::createUrl("account/forgotten");



        $this->session->set('landing_page','account/login');
        $this->loadWidgets('featuredContent');
        $this->loadWidgets('main');
        $this->loadWidgets('featuredFooter');

            $this->addChild('common/column_left');
            $this->addChild('common/column_right');
            $this->addChild('common/header');
            $this->addChild('common/footer');


        $template = ($this->config->get('default_view_account_login')) ? $this->config->get('default_view_account_login') : 'account/login.tpl';
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/' . $template)) {
            $this->template = $this->config->get('config_template') . '/' . $template;
        } else {
            $this->template = 'choroni/' . $template;
        }

        $this->response->setOutput($this->render(true), $this->config->get('config_compression'));
    }

    private function activarUser() {
        $arrValor = [];
        $codigo = $_SERVER['QUERY_STRING'];
        $arrCodigo = explode('&amp;', $codigo);
        foreach ($arrCodigo as $key => $value) {
            $arrValor[] = explode('=', $value);
        }
        foreach ($arrValor as $key => $value) {
            foreach ($value as $key2 => $value2) {
                if ($key2 == '1')
                    $arrFinal[] = $value2;
            }
        }
        if (!empty($arrFinal[3])) {
            $email = $arrFinal[1];
            $password = $arrFinal[2];
            $codigo = $arrFinal[3];
            if ($this->customer->activateUser($codigo)) {
                echo "<center><div style='background:#fff88d top center;display:block;width:1000px;height:15px;font:bold 11px verdana;color:#e47202;'>Su cuenta ha sido activada, Ya puede acceder y disfrutar de nuestros servicios.</div></center>";
            }
        }
    }

    private function validate() {
        $this->language->load('account/login');
        if (!$this->customer->login($this->request->post['email'], $this->request->post['password'])) {
            $this->error['message'] = $this->language->get('error_login');
        }

        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }

    public function header() {
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . "GMT");
        header("Cache-Control: no-cache, must-revalidate");
        header("Pragma: no-cache");
        header("Content-type: application/json");

        $this->language->load('account/login');
        if (!$this->request->hasPost("email") && !$this->request->hasPost("password")) {
            $json['error'] = 1;
            $json['message'] = $this->language->get('error_login');
        }

        if (!$this->request->hasPost("token") && $this->request->getPost("token") != $this->session->get('token')) {
            $json['error'] = 1;
            $json['message'] = $this->language->get('error_login');
        }

        if (!$this->customer->login($this->request->getPost("email"), $this->request->getPost("password"), false)) {
            $json['error'] = 1;
            $json['message'] = $this->language->get('error_login');
        }

        if (!$json['error']) {
            $json['success'] = 1;
        }

        $this->load->auto('json');
        $this->response->setOutput(Json::encode($json), $this->config->get('config_compression'));
    }
}
