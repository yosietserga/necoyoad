<?php

class ControllerToolErrorLog extends Controller {

    private $error = [];

    public function index() {
        $this->load->language('tool/error_log');

        $this->document->title = $this->language->get('heading_title');

        $this->data['heading_title'] = $this->language->get('heading_title');

        $this->data['button_clear'] = $this->language->get('button_clear');

        $this->data['tab_general'] = $this->language->get('tab_general');

        if ($this->session->has('success')) {
            $this->data['success'] = $this->session->get('success');

            $this->session->clear('success');
        } else {
            $this->data['success'] = '';
        }

        $this->document->breadcrumbs = [];

        $this->document->breadcrumbs[] = array(
            'href' => Url::createAdminUrl('common/home'),
            'text' => $this->language->get('text_home'),
            'separator' => false
        );

        $this->document->breadcrumbs[] = array(
            'href' => Url::createAdminUrl('tool/error_log'),
            'text' => $this->language->get('heading_title'),
            'separator' => ' :: '
        );

        $this->data['clear'] = Url::createAdminUrl('tool/error_log/clear');

        $file = DIR_LOGS . $this->config->get('config_error_filename');

        if (file_exists($file)) {
            $this->data['log'] = file_get_contents($file, FILE_USE_INCLUDE_PATH, NULL);
        } else {
            $this->data['log'] = '';
        }

        $template = ($this->config->get('default_admin_view_tool_error_log')) ? $this->config->get('default_admin_view_tool_error_log') : 'tool/error_log.tpl';
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_admin_template') . '/'. $template)) {
            $this->template = $this->config->get('config_admin_template') . '/' . $template;
        } else {
            $this->template = 'default/' . $template;
        }


        $this->children[] = 'common/header';
        $this->children[] = 'common/nav';
        $this->children[] = 'common/footer';
        
        $this->response->setOutput($this->render(true), $this->config->get('config_compression'));
    }

    public function clear() {
        $this->load->language('tool/error_log');

        $file = DIR_LOGS . $this->config->get('config_error_filename');

        $handle = fopen($file, 'w+');

        fclose($handle);

        $this->session->set('success', $this->language->get('text_success'));

        $this->redirect(Url::createAdminUrl('tool/error_log'));
    }

}
