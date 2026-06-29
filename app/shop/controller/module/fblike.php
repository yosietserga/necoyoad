<?php

require_once(DIR_CONTROLLER . "module/modulecontroller.php");

class ControllerModuleFBLike extends ControllerModuleModuleController
{
    protected string $moduleName = 'fblike';
    protected array $defaults = [];

    public function init()
    {
        $this->addFilter("module:settings", function ($data) {
            $settings = $data['settings'];
            $widget   = $data['widget'];
            $render   = $data['render'];

            $this->data['pageid'] = html_entity_decode($settings['fblike_pageid']);
            $this->data['totalconnection'] = html_entity_decode($settings['fblike_totalconnection']);
            $this->data['width']  = html_entity_decode($settings['fblike_width']);
            $this->data['height'] = html_entity_decode($settings['fblike_height']);
            $this->data['stream'] = html_entity_decode($settings['fblike_stream']);
            $this->data['header'] = html_entity_decode($settings['fblike_header']);

            return [
                'widget'   => $widget,
                'render'   => $render,
                'settings' => $settings,
            ];
        });
    }
}