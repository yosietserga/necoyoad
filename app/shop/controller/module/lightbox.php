<?php

require_once(DIR_CONTROLLER . "module/modulecontroller.php");

class ControllerModuleLightbox extends ControllerModuleModuleController
{
    protected string $moduleName = 'invitefriends';
    protected array $defaults = [];

    public function init() {
        $this->addFilter("module:settings", function ($data) {
            $settings = $data['settings'];
            $widget   = $data['widget'];
            $render   = $data['render'];
            
            if (
                isset($settings['page_id']) && $settings['page_id']
                && (!isset($settings['show_once']) || !$settings['show_once']) 
                && (isset($settings['show_once']) && $settings['show_once'] && !$this->request->getCookie($widget['name']))
             ) {
                $this->load->model('content/page');
                $this->data['page'] = $this->modelPage->getById($settings['page_id']);

                $this->request->setCookie($widget['name'], true);
                $this->session->set($widget['name'], true);

                //TODO: pass widget params through direct vars
                $this->session->clear('object_type');
                $this->session->clear('object_id');
                $this->session->clear('landing_page');

                $this->session->set('object_type', 'page');
                $this->session->set('object_id', $settings['page_id']);
                $this->session->set('landing_page', 'content/page');
                $this->loadWidgets('only:featuredContent');
                $this->loadWidgets('only:main');
                $this->loadWidgets('only:featuredFooter');
            }

            return [
                'widget'   => $widget,
                'render'   => $render,
                'settings' => $settings,
            ];
        });
    }
}