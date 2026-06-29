<?php

require_once(DIR_CONTROLLER . "module/modulecontroller.php");

class ControllerModuleInviteFriends extends ControllerModuleModuleController {
    protected string$moduleName = 'invitefriends';
    protected array $defaults = [];

    public function init() {
        $this->defaults['live_client_id'] = $this->config->get('social_live_client_id');
        $this->defaults['google_client_id'] = $this->config->get('social_google_client_id');
    }
}