<?php

require_once(DIR_CONTROLLER . "module/modulecontroller.php");

class ControllerModuleLanguageSelector extends ControllerModuleModuleController
{
    protected string $moduleName = 'language_selector';

    public function init() {
        $this->addFilter("module:settings", function ($data) {
            $settings = $data['settings'];
            $widget   = $data['widget'];
            $render   = $data['render'];

            //get all languages 
            //get language selected from cookies 
            //or get from browser language 
            $this->load->auto('localisation/language');
            $this->data['languages'] = $this->modelLanguage->getAll();
            $this->data['language_selected'] = $this->request->getCookie('language');

            return [
                'widget'   => $widget,
                'render'   => $render,
                'settings' => $settings,
            ];
        });
    }
}