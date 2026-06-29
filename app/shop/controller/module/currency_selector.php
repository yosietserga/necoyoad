<?php

require_once(DIR_CONTROLLER . "module/modulecontroller.php");

class ControllerModuleCurrencySelector extends ControllerModuleModuleController
{
    protected string $moduleName = 'currency_selector';
    
    public function init() {
        $this->addFilter("module:settings", function ($data) {
            $settings = $data['settings'];
            $widget   = $data['widget'];
            $render   = $data['render'];

            //get all currencies 
            //get currency selected from cookies 
            $this->load->auto('localisation/currency');
            $this->data['currencies'] = $this->modelCurrency->getAll();
            $this->data['currency_selected'] = $this->request->getCookie('currency');
            
            return [
                'widget'   => $widget,
                'render'   => $render,
                'settings' => $settings,
            ];
        });
    }
}