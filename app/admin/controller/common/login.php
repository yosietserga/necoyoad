<?php

class ControllerCommonLogin extends Controller {

    private $error = [];

    public function index() {
        $this->load->language('common/login');

        $this->document->title = $this->language->get('heading_title');

        if ($this->user->isLogged() && isset($this->request->get['token']) && ($this->request->get['token'] == $this->session->get('ukey'))) {
            $this->redirect(Url::createAdminUrl('common/home'));
        }

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $redirect = ($this->request->hasPost('redirect') && $this->request->getPost('redirect') !== 'common/login') ? $this->request->getPost('redirect') : 'common/home';
            $this->redirect(Url::createUrl($redirect, array('token' => $this->session->get('ukey'))));
        }

        if (!$this->session->has('ukey') || !isset($this->request->get['token']) || ($this->request->get['token'] != $this->session->get('ukey'))) {
            $this->error['warning'] = $this->language->get('error_token');
        }

        $this->data['error_warning'] = isset($this->error['warning']) ? $this->error['warning'] : '';

        $this->data['action'] = Url::createAdminUrl('common/login');

        $this->setvar('username');
        $this->setvar('password');

        if ($this->request->hasQuery('r')) {
            $route = $this->request->getQuery('r');
            unset($this->request->get['r']);
            if ($this->request->hasQuery('token')) {
                unset($this->request->get['token']);
            }
            $url = '';
            if ($this->request->get) {
                $url = '&' . http_build_query($this->request->get);
            }
            $this->data['redirect'] = $route;
        } else {
            $this->data['redirect'] = '';
        }

        $this->session->set('fid', md5(rand()));

        $template = ($this->config->get('default_admin_view_login')) ? $this->config->get('default_admin_view_login') : 'common/login.tpl';
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_admin_template') . '/' . $template)) {
            $this->template = $this->config->get('config_admin_template') . '/' . $template;
        } else {
            $this->template = 'default/' . $template;
        }

        $this->children = array(
            'common/header',
            'common/footer'
        );

        $this->response->setOutput($this->render(true), $this->config->get('config_compression'));
    }

    public function login() {
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . "GMT");
        header("Cache-Control: no-cache, must-revalidate");
        header("Pragma: no-cache");
        header("Content-type: application/json");

        $this->load->language('common/login');
        $json = [];

        //TODO: improve CSRF security policies and protocols 
        if ($this->user->isLogged() || ($this->request->server['REQUEST_METHOD'] != 'POST') || ($this->session->get('fid') != $this->request->post['fid'])) {
            //$json['error'] = 1;
        }

        if (!$this->user->login($this->request->post['username'], $this->request->post['password'], false)) {
            $json['error'] = 1;
            $userInfo = $this->db->query("SELECT user_id FROM ". DB_PREFIX ."user WHERE username = '". $this->request->post['username'] ."'");
            $this->user->registerActivity($userInfo->row['user_id'], 'user', 'Intento de inicio de sesión fallido', 'login');
        } elseif (!isset($json['error'])) {
            $json['redirect'] = ($this->request->hasPost('redirect') && $this->request->getPost('redirect') !== 'common/login') ?
                    Url::createUrl($this->request->getPost('redirect'), array('token' => $this->session->get('ukey'))) :
                    Url::createUrl('common/home', array('token' => $this->session->get('ukey')));
            $this->user->registerActivity($this->user->getId(), 'user', 'Inicio de sesión', 'login');
            $json['success'] = 1;
        }

        $this->load->auto('json');
        $this->response->setOutput(Json::encode($json), $this->config->get('config_compression'));
    }

    public function recover() {
        $this->load->language('common/login');
        $this->data['error_warning'] = '';

        $this->document->title = $this->language->get('heading_recover_title');

        if ($this->user->isLogged() && isset($this->request->get['token']) && ($this->request->get['token'] == $this->session->get('ukey'))) {
            $this->redirect(Url::createAdminUrl('common/home'));
        }

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateRecover()) {
            $result = $this->db->query("SELECT * FROM " . DB_PREFIX . "user WHERE username = '" . $this->request->getPost('username') . "' AND email = '" . $this->request->getPost('email') . "'");
            if ($result->num_rows) {
                $password = substr(md5(rand()), 0, 7);
                $this->db->query("UPDATE " . DB_PREFIX . "user SET 
                `password` = '" . md5($password) . "' 
                WHERE username = '" . $this->request->getPost('username') . "' 
                AND email = '" . $this->request->getPost('email') . "'");

                $this->user->registerActivity($result->row['user_id'], 'user', 'Solicitud de generación de contraseña nueva', 'update');
            
                $this->load->auto('email/mailer');
                $mailer = new Mailer;

                $message = "<h1>Hola " . $this->request->getPost('username') . ",</h1>\n\n";
                $message .= "<p>Tu nueva contrase&ntilde;a es:</p>\n";
                $message .= "<h1>" . $password . "</h1>\n";

                if ($this->config->get('config_smtp_method') == 'smtp') {
                    $mailer->IsSMTP();
                    $mailer->Host = $this->config->get('config_smtp_host');
                    $mailer->Username = $this->config->get('config_smtp_username');
                    $mailer->Password = base64_decode($this->config->get('config_smtp_password'));
                    $mailer->Port = $this->config->get('config_smtp_port');
                    $mailer->Timeout = $this->config->get('config_smtp_timeout');
                    $mailer->SMTPSecure = $this->config->get('config_smtp_ssl');
                    $mailer->SMTPAuth = ($this->config->get('config_smtp_auth')) ? true : false;
                } elseif ($this->config->get('config_smtp_method') == 'sendmail') {
                    $mailer->IsSendmail();
                } else {
                    $mailer->IsMail();
                }
                $mailer->IsHTML();
                $mailer->AddAddress($this->request->getPost('email'), $this->request->getPost('username'));
                $mailer->SetFrom('soporte@necotienda.com', 'NecoTienda');
                $mailer->Subject = 'Recuperacion de Contrasena';
                $mailer->Body = $message;
                $mailer->Send();

                $this->redirect(Url::createUrl('common/login'));
            } else {
                $this->user->registerActivity($result->row['user_id'], 'user', 'Intento fallido de solicitud de generación de contraseña nueva', 'update');
                $this->data['error_warning'] = $this->language->get('error_user_unknown');
            }
        }

        if (isset($this->error['warning']))
            $this->data['error_warning'] = $this->error['warning'];

        $this->data['action'] = Url::createAdminUrl('common/login/recover');

        $this->setvar('username');
        $this->setvar('email');

        $scripts[] = array('id' => 'login', 'method' => 'ready', 'script' =>
            "$('#form input').keydown(function(e) {
        		if (e.keyCode == 13) {
                    $('#form').submit();
        		}
        	});");
        $this->scripts = array_merge($scripts, $this->scripts);

        $template = ($this->config->get('default_admin_view_login_recover')) ? $this->config->get('default_admin_view_login_recover') : 'common/recover.tpl';
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_admin_template') . '/' . $template)) {
            $this->template = $this->config->get('config_admin_template') . '/' . $template;
        } else {
            $this->template = 'default/' . $template;
        }

        $this->children = array(
            'common/header',
            'common/footer'
        );

        $this->response->setOutput($this->render(true), $this->config->get('config_compression'));
    }

    private function validate() {
        if (!$this->user->login($this->request->post['username'], $this->request->post['password']) && ($this->session->get('fid') != $this->request->post['fid'])) {
            $this->error['warning'] = $this->language->get('error_login');
        }

        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }

    private function validateRecover() {
        if (empty($this->request->post['username'])) {
            $this->error['warning'] = $this->language->get('error_recover');
        }
        if (empty($this->request->post['email'])) {
            $this->error['warning'] = $this->language->get('error_recover');
        }

        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }

}
