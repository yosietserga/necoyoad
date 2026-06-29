<?php

class ControllerCommonLogout extends Controller {

    public function index() {
        $this->user->registerActivity($this->user->getId(), 'user', 'Cierre de sesión', 'logout');
        $this->user->logout();
        $this->session->clear('token');
        $this->redirect(Url::createAdminUrl('common/login'));
    }

}
