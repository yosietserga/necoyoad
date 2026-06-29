<?php

require_once(DIR_CONTROLLER . "module/modulecontroller.php");

class ControllerModuleFacebookComments extends ControllerModuleModuleController
{
    protected string $moduleName = 'facebook_comments';
    protected array $defaults = [];

    public function init()
    {
        $this->addFilter("module:settings", function ($data) {
            $settings = $data['settings'];
            $widget   = $data['widget'];
            $render   = $data['render'];

            $Url = new Url($this->registry);
            $this->data['url'] = false;

            if ($this->request->hasQuery('product_id') || $this->request->hasPost('product_id')) {
                $oid = $this->request->hasPost('product_id') ? $this->request->getPost('product_id') : $this->request->getQuery('product_id');
                $this->data['url'] = $Url::createUrl('store/product', array('product_id' => $oid));
            }

            if ($this->request->hasQuery('post_id') || $this->request->hasPost('post_id')) {
                $oid = $this->request->hasPost('post_id') ? $this->request->getPost('post_id') : $this->request->getQuery('post_id');
                $this->data['url'] = $Url::createUrl('content/post', array('post_id' => $oid));
            }

            if ($this->request->hasQuery('page_id') || $this->request->hasPost('page_id')) {
                $oid = $this->request->hasPost('page_id') ? $this->request->getPost('page_id') : $this->request->getQuery('page_id');
                $this->data['url'] = $Url::createUrl('content/page', array('page_id' => $oid));
            }

            if ($this->request->hasQuery('category_id') || $this->request->hasPost('category_id')) {
                $oid = $this->request->hasPost('category_id') ? $this->request->getPost('category_id') : $this->request->getQuery('category_id');
                $this->data['url'] = $Url::createUrl('store/category', array('category_id' => $oid));
            }

            if ($this->request->hasQuery('post_category_id') || $this->request->hasPost('post_category_id')) {
                $oid = $this->request->hasPost('post_category_id') ? $this->request->getPost('post_category_id') : $this->request->getQuery('post_category_id');
                $this->data['url'] = $Url::createUrl('content/post_category', array('post_category_id' => $oid));
            }

            if ($this->request->hasQuery('manufacturer_id') || $this->request->hasPost('manufacturer_id')) {
                $oid = $this->request->hasPost('manufacturer_id') ? $this->request->getPost('manufacturer_id') : $this->request->getQuery('manufacturer_id');
                $this->data['url'] = $Url::createUrl('store/manufacturer', array('manufacturer_id' => $oid));
            }

            return [
                'widget'   => $widget,
                'render'   => $render,
                'settings' => $settings,
            ];
        });
    }
}