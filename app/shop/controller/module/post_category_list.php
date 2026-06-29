<?php

require_once(DIR_CONTROLLER . "module/modulecontroller.php");

class ControllerModulePostCategoryList extends ControllerModuleModuleController
{
    protected string $moduleName = 'post_category_list';
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
                $query_data['category_id'] = (!empty($settings['category_id'])) ? $settings['category_id'] : null;
            }

            $this->data['categories'] = $this->getCategories($query_data);

            return [
                'widget'   => $widget,
                'render'   => $render,
                'settings' => $settings,
            ];
        });
    }
    
    protected function getCategories($data) {
        $data['parent_id'] = $data['parent_id'] ?? 0;

        static $ready = [];
        if (in_array($data['parent_id'], $ready)) return false;
        $ready[] = $data['parent_id'];

        $this->load->auto('content/category');
        $results = $this->modelCategory->getAll($data);
        if (count($results)==0) return false;

        $c_data = $data;
        unset($c_data['parent_id']);
        foreach ($results as $k=>$result) {
            $c_data['parent_id'] = $result['category_id'];
            $results[$k]['children'] = $this->getCategories($c_data);
        }
        return $results;
    }
}
