<?php

require_once(DIR_CONTROLLER . "module/modulecontroller.php");

class ControllerModuleRichText extends ControllerModuleModuleController
{
    protected string $moduleName = 'richtext';
    protected array $defaults = [];

    public function init()
    {
        $this->addFilter("module:settings", function ($data) {
            $settings = $data['settings'];
            $widget   = $data['widget'];
            $render   = $data['render'];
            $this->data['description'] = '';
            if (isset($settings['post_id']) && (int)$settings['post_id'] && $settings['content_type'] === 'post_id') {
                $this->load->model('content/page');
                $page = $this->modelPage->getById($settings['post_id']);
                $this->data['description'] = html_entity_decode($page['description']);

                $pageController = $this->load->controller('content/page');
                $this->data['description'] .= html_entity_decode($pageController->embed($settings['post_id']));
            } elseif ($settings['content_type'] == 'html_content') {
                if (!empty($settings['descriptions'][$this->config->get('config_language_id')]['description'])) {
                    $this->data['description'] = html_entity_decode($settings['descriptions'][$this->config->get('config_language_id')]['description']);
                }
            }

            return [
                'widget'   => $widget,
                'render'   => $render,
                'settings' => $settings,
            ];
        });
    }
}