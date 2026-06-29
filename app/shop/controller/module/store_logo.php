<?php

require_once(DIR_CONTROLLER . "module/modulecontroller.php");

class ControllerModuleStoreLogo extends ControllerModuleModuleController
{
    protected string $moduleName = 'store_logo';
    protected array $defaults = [];

    public function init()
    {
        $this->addFilter("module:settings", function ($data) {
            $settings = $data['settings'];
            $widget   = $data['widget'];
            $render   = $data['render'];

            $this->load->library('browser');
            $browser = new Browser;

            if ($browser->isMobile() && $this->config->get('config_mobile_logo') && file_exists(DIR_IMAGE . $this->config->get('config_mobile_logo'))) {
                $this->data['logo'] = HTTP_IMAGE . $this->config->get('config_mobile_logo');
            } elseif ($this->config->get('config_logo') && file_exists(DIR_IMAGE . $this->config->get('config_logo'))) {
                $this->data['logo'] = HTTP_IMAGE . $this->config->get('config_logo');
            } else {
                $this->data['logo'] = '';
            }

            return [
                'widget'   => $widget,
                'render'   => $render,
                'settings' => $settings,
            ];
        });
    }
}