<?php

require_once(DIR_CONTROLLER . "module/modulecontroller.php");

class ControllerModulePostDescription extends ControllerModuleModuleController
{
    protected string $moduleName = 'post_description';
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
            $results = $this->modelPost->getAll($data);

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