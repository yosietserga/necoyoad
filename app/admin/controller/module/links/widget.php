<?php

require_once(realpath(dirname(__FILE__) . '/../widgetcontroller.php'));

class ControllerModuleLinksWidget extends ControllerWidgetController
{
    protected string $moduleName = 'links';

    public function init()
    {
        $this->addFilter("widget:settings", function ($widget) {

            $this->load->model('content/menu');
            $this->data['menus'] = $this->modelMenu->getAll(array(
                'status' => 1
            ));
            return $widget;
        });
    }
}
