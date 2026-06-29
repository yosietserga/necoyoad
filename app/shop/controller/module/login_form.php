<?php

require_once(DIR_CONTROLLER . "module/modulecontroller.php");

class ControllerModuleLoginForm extends ControllerModuleModuleController
{
    protected string $moduleName = 'login_form';
    protected array $defaults = [];

    public function init()
    {
        $this->addFilter("module:settings", function ($data) {
            $settings = $data['settings'];
            $widget   = $data['widget'];
            $render   = $data['render'];

            $this->data['tokenLogin'] = $this->getToken();

            return [
                'widget'   => $widget,
                'render'   => $render,
                'settings' => $settings,
            ];
        });
    }

    public function login() {
        $json = [];
        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $json['success'] = 1;
        } else {
            $json['error'] = 1;
            $json['msg'] = $this->language->get('error_email');
            $this->session->clear('tokenLogin');
            $json['tokenLogin'] = $this->getToken(true);
        }

        $this->load->library('json');
        $this->response->setOutput(Json::encode($json), $this->config->get('config_compression'));
    }

    protected function getToken($reset=false) {
        if ($reset) {
            $this->session->clear('tokenLogin');
        }

        if (!$this->session->has('tokenLogin')) {
            $token = md5(rand() . time());
            $this->session->set('tokenLogin', $token);
        } else {
            $token = $this->session->get('tokenLogin');
        }

        return $token;
    }

    protected function validate() {
        $this->load->library('validar');
        $validate = new Validar;
        if (empty($this->request->post['email']) || !$validate->validEmail($this->request->post['email'])) {
            return false;
        }
        if (empty($this->request->post['password'])) {
            return false;
        }
        if ($this->request->post['token'] != $this->session->get('tokenLogin')) {
            return false;
        }
        if (!$this->customer->login($this->request->post['email'], $this->request->post['password'], false)) {
            return false;
        }
        return true;
    }
}
