<?php

require_once(realpath(dirname(__FILE__) . '/../widgetcontroller.php'));

class ControllerModuleBannerWidget extends ControllerWidgetController
{
    protected string $moduleName = 'banner';

    public function init() {
        $this->addFilter("widget:settings", function ($widget) {
            $this->load->model('content/' . $this->moduleName);
            $this->data['banners'] = $this->modelBanner->getAll();
            return $widget;
        });
    }
}