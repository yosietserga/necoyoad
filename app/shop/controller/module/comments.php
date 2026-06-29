<?php

require_once(DIR_CONTROLLER . "module/modulecontroller.php");

class ControllerModuleComments extends ControllerModuleModuleController
{
    protected string $moduleName = 'comments';
    protected array $defaults = [];

    public function init() {
        $this->addFilter("module:settings", function ($data) {
            $settings = $data['settings'];
            $widget   = $data['widget'];
            $render   = $data['render'];

            $ot = $oid = false;
            if ($this->request->hasQuery('ot')) $ot = $this->request->getQuery('ot');

            if (!$ot && ($this->request->hasQuery('product_id') || $this->request->hasPost('product_id'))) {
                $ot = 'product';
                $oid = $this->request->hasPost('product_id') ? $this->request->getPost('product_id') : $this->request->getQuery('product_id');
            }
            if (!$ot && ($this->request->hasQuery('post_id') || $this->request->hasPost('post_id'))) {
                $ot = 'post';
                $oid = $this->request->hasPost('post_id') ? $this->request->getPost('post_id') : $this->request->getQuery('post_id');
            }

            if (!$ot && ($this->request->hasQuery('page_id') || $this->request->hasPost('page_id'))) {
                $ot = 'page';
                $oid = $this->request->hasPost('page_id') ? $this->request->getPost('page_id') : $this->request->getQuery('page_id');
            }

            if (!$ot && ($this->request->hasQuery('category_id') || $this->request->hasPost('category_id'))) {
                $ot = 'category';
                $oid = $this->request->hasPost('category_id') ? $this->request->getPost('category_id') : $this->request->getQuery('category_id');
            }

            if (!$ot && ($this->request->hasQuery('category_id') || $this->request->hasPost('category_id'))) {
                $ot = 'post_category';
                $oid = $this->request->hasPost('category_id') ? $this->request->getPost('category_id') : $this->request->getQuery('category_id');
            }

            if (!$ot && ($this->request->hasQuery('manufacturer_id') || $this->request->hasPost('manufacturer_id'))) {
                $ot = 'manufacturer';
                $oid = $this->request->hasPost('manufacturer_id') ? $this->request->getPost('manufacturer_id') : $this->request->getQuery('manufacturer_id');
            }

            if ($oid && $ot) {
                $this->data['oid'] = $oid;
                $this->data['ot'] = $ot;
            }

            return [
                'widget'   => $widget,
                'render'   => $render,
                'settings' => $settings,
            ];
        });
    }
}