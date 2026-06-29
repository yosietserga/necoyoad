<?php

require_once(DIR_CONTROLLER . "module/modulecontroller.php");

class ControllerModulePostCategoryDescription extends ControllerModuleModuleController
{
    protected string $moduleName = 'post_category_description';
    protected array $defaults = [];

    public function init()
    {
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

            if (!is_callable([$this, 'modelCategory'])) $this->load->model('content/category');
            $results = $this->modelCategory->getAll($query_data);

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