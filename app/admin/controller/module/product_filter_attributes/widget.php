<?php

require_once(realpath(dirname(__FILE__) . '/../widgetcontroller.php'));

class ControllerModuleProductFilterAttributesWidget extends ControllerWidgetController
{
    protected string $moduleName = 'product_filter_attributes';
    
    public function init() {
        $this->addFilter("widget:settings", function ($widget) {
            $this->load->model('store/attribute');
            $this->data['attributess'] = $this->modelAttribute->getAll();

            return $widget;
        });
    }
}