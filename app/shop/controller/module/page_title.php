<?php

require_once(DIR_CONTROLLER . "module/modulecontroller.php");

class ControllerModulePageTitle extends ControllerModuleModuleController
{
    protected string $moduleName = 'page_title';
    protected array $defaults = [];

    public function init()
    {

        $this->addFilter("module:settings", function ($data) {
            $settings = $data['settings'];
            $widget   = $data['widget'];
            $render   = $data['render'];

            $query_data = [];
            if ($this->request->hasQuery('page_id') || $this->request->hasPost('page_id')) {
                $query_data['page_id'] = $this->request->hasPost('page_id') ? $this->request->getPost('page_id') : $this->request->getQuery('page_id');
            } else {
                $query_data['page_id'] = (!empty($settings['pages'])) ? $settings['pages'] : null;
            }

            if (!is_callable([$this, 'modelPage'])) $this->load->model('content/page');
            $results = $this->modelPage->getAll($query_data);

            if (isset($results[0])) {
                $this->data['title'] = $results[0]['title'];
                $this->data['heading_title'] = isset($results[0]['title']) ? $results[0]['title'] : (isset($settings['title']) ? $settings['title'] : $this->language->get('heading_title'));
            }

            return [
                'widget'   => $widget,
                'render'   => $render,
                'settings' => $settings,
            ];
        });
    }
}