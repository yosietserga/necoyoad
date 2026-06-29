<?php

require_once(DIR_CONTROLLER . "module/modulecontroller.php");

class ControllerModuleProductFilterAttributes extends ControllerModuleModuleController
{
    protected string $moduleName = 'product_filter_attributes';
    protected array $defaults = [];

    public function init()
    {
        $this->addFilter("module:settings", function ($data) {
            $settings = $data['settings'];
            $widget   = $data['widget'];
            $render   = $data['render'];

            if ((int) $settings['product_attribute_group_id']) {
                $this->load->model('store/attribute');
                $this->data['attributes_groups'] = $this->modelAttribute->getAll(['product_attribute_group_id'=>$settings['product_attribute_group_id']])[0];
            }

            return [
                'widget'   => $widget,
                'render'   => $render,
                'settings' => $settings,
            ];
        });
    }
}