<?php

require_once(DIR_CONTROLLER . "module/modulecontroller.php");

class ControllerModuleContactForm extends ControllerModuleModuleController
{
    protected string $moduleName = 'contact_form';
    protected array $defaults = [];

    public function processform() {
        $this->load->auto('json');
        $this->load->auto('ntsmailer');
        $this->load->helper('widgets');
        
        $w = new NecoWidget($this->registry, $this->Route);
        
        $name = $this->request->hasPost('w') ? $this->request->getPost('w') : $this->request->getQuery('w');

        $widget = $w->getWidget($name);
        $settings = (array) unserialize($widget['settings']);
        
        $toemail = $this->validateEmail($settings['toemail']) ? $settings['toemail'] : $this->config->get('config_email');
        $toname = $this->validateEmail($settings['toname']) ? $settings['toname'] : $this->config->get('config_name');
        
        $this->data['error_name'] = isset($this->error['name']) ? $this->error['name'] : '';
        $this->data['error_email'] = isset($this->error['email']) ? $this->error['email'] : '';
        $this->data['error_enquiry'] = isset($this->error['enquiry']) ? $this->error['enquiry'] : '';
        
        $this->setvar('name');
        $this->setvar('email');
        $this->setvar('enquiry');
        
        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $mailer = new ntsMailer($this->registry);
            
            $mailer->AddAddress($toemail, $toname);
            $mailer->SetFrom($this->data['email'], $this->data['name']);
            $mailer->SetSubject($toname . " - Contacto");
            $mailer->SetBody($this->data['enquiry']);
            $return['result'] = $mailer->Send();
            $return['success'] = ($return['result']) ? 1 : $this->language->get('text_error');
            $return['msg'] = $this->language->get('text_success');
            
            if ($this->request->hasPost('newsletter')) {
                $this->load->model('marketing/contact');
                $this->modelContact->add($this->request->post);
            }
            
        } else {
            $return['error'] = 1;
            $return['msg'] = isset($this->error['name']) ? $this->error['name'] : '';
            $return['msg'] = isset($this->error['email']) ? $this->error['email'] : $return['msg'];
            $return['msg'] = isset($this->error['enquiry']) ? $this->error['enquiry'] : $return['msg'];
        }
        
        $this->response->setOutput(Json::encode($return), $this->config->get('config_compression'));
    }

    private function validateEmail() {
        return !$this->validar->validEmail($this->request->post['email']);
    }

    private function validate() {
        $this->load->auto('validar');
        if (empty($this->request->post['name'])) {
            $this->error['name'] = $this->language->get('error_name');
        }

        if (!$this->validar->validEmail($this->request->post['email'])) {
            $this->error['email'] = $this->language->get('error_email');
        }

        if (empty($this->request->post['enquiry'])) {
            $this->error['enquiry'] = $this->language->get('error_enquiry');
        }

        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }
}