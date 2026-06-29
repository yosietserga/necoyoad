<?php

require_once(DIR_CONTROLLER . "module/modulecontroller.php");

class ControllerModulePageDescription extends ControllerModuleModuleController {
    protected string $moduleName = 'page_description';
    protected array $defaults = [];

    public function init() {
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
            $results = $this->modelPage->getAll($data);

            if (isset($results[0])) {
                $this->data['description'] = html_entity_decode($results[0]['description'], ENT_QUOTES, 'UTF-8');
            }

            return [
                'widget'   => $widget,
                'render'   => $render,
                'settings' => $settings,
            ];
        });
    }
}