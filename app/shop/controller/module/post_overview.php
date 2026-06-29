<?php

require_once(DIR_CONTROLLER . "module/modulecontroller.php");

class ControllerModulePostOverview extends ControllerModuleModuleController
{
    protected string $moduleName = 'post_overview';
    protected array $defaults = [];

    public function init()
    {
        $this->addFilter("module:settings", function ($data) {
            $settings = $data['settings'];
            $widget   = $data['widget'];
            $render   = $data['render'];

            $query_data = [];
            if ($this->request->hasQuery('post_id') || $this->request->hasPost('post_id')) {
                $query_data['post_id'] = $this->request->hasPost('post_id') ? $this->request->getPost('post_id') : $this->request->getQuery('post_id');
            } else {
                $query_data['post_id'] = (!empty($settings['posts'])) ? $settings['posts'] : null;
            }

            if (!is_callable([$this, 'modelPost'])) $this->load->model('content/post');
            $results = $this->modelPost->getAll($query_data);

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