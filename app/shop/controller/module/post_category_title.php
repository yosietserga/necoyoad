<?php

require_once(DIR_CONTROLLER . "module/modulecontroller.php");

class ControllerModulePostCategoryTitle extends ControllerModuleModuleController
{
    protected string $moduleName = 'post_category_title';
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