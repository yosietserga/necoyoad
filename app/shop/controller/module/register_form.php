<?php

require_once(DIR_CONTROLLER . "module/modulecontroller.php");

class ControllerModuleRegisterForm extends ControllerModuleModuleController
{
    protected string $moduleName = 'register_form';
    protected array $defaults = [];

    public function init()
    {
        $this->addFilter("module:settings", function ($data) {
            $settings = $data['settings'];
            $widget   = $data['widget'];
            $render   = $data['render'];
            $Url = new Url($this->registry);
            $this->load->model('localisation/country');
            $this->data['countries'] = $this->modelCountry->getCountries();
            $settings['action'] = isset($settings['action']) ? $settings['action'] : $Url::createUrl('account/register');
            
            if ($this->session->has("error")) {
                $this->data["error"] = $this->session->get("error");
                $this->session->clear("error");
            }

            return [
                'widget'   => $widget,
                'render'   => $render,
                'settings' => $settings,
            ];
        });
    }
}