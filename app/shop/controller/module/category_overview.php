<?php

require_once(DIR_CONTROLLER . "module/modulecontroller.php");

class ControllerModuleCategoryOverview extends ControllerModuleModuleController {
    protected string $moduleName = 'category_overview';
    protected array $defaults = [];

    public function init() {

        $this->addFilter("module:settings", function ($data) {
            $settings = $data['settings'];
            $widget   = $data['widget'];
            $render   = $data['render'];

            $query_data = [];
            if ($this->request->hasQuery('category_id') || $this->request->hasPost('category_id')) {
                $query_data['category_id'] = $this->request->hasPost('category_id') ? $this->request->getPost('category_id') : $this->request->getQuery('category_id');
            } else {
                $query_data['category_id'] = (!empty($settings['categories'])) ? $settings['categories'] : null;
            }

            if (!is_callable([$this, 'modelCategory'])) $this->load->model('store/category');
            $results = $this->modelCategory->getAll($query_data);

            if (isset($results[0])) {
                $this->data['overview'] = $results[0]['meta_description'];
            }

            return [
                'widget'   => $widget,
                'render'   => $render,
                'settings' => $settings,
            ];
        });
    }
}