<?php

class ControllerCommonMaintenance extends Controller {

    public function index() {
        $this->session->clear('object_type');
        $this->session->clear('object_id');
        $this->session->clear('landing_page');

        $Url = new Url($this->registry);
        $this->load->language('common/maintenance');
        $this->language->load('common/footer');
        $this->document->title = $this->language->get('heading_title') . " - " . $this->config->get('config_title');

        $this->data['google_analytics_code'] = $this->config->get('google_analytics_code');

        $this->session->set('landing_page','common/maintenance');
        $this->loadWidgets('featuredContent');
        $this->loadWidgets('main');
        $this->loadWidgets('featuredFooter');

            $this->addChild('common/column_left');
            $this->addChild('common/column_right');
            $this->addChild('common/header');
            $this->addChild('common/footer');

        $template = ($this->config->get('default_view_maintenance')) ? $this->config->get('default_view_maintenance') : 'common/maintenance.tpl';
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/' . $template)) {
            $this->template = $this->config->get('config_template') . '/' . $template;
        } else {
            $this->template = 'choroni/' . $template;
        }

        $this->response->setOutput($this->render(true));
    }

    public function check() {
        if ($this->config->get('config_maintenance')) {
            // Show site if logged in as admin
            require_once(DIR_SYSTEM . 'library/user.php');
            $this->registry->set('user', new User($this->registry));
            if (!$this->user->isLogged()) {
                return $this->forward('common/maintenance');
            }
        }
    }
}
