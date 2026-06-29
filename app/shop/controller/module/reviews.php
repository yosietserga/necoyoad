<?php

require_once(DIR_CONTROLLER . "module/modulecontroller.php");

class ControllerModuleReviews extends ControllerModuleModuleController
{
    protected string $moduleName = 'reviews';
    protected array $defaults = [];

    public function init()
    {
        $this->addFilter("module:settings", function ($data) {
            $settings = $data['settings'];
            $widget   = $data['widget'];
            $render   = $data['render'];

            $ot = $oid = false;
            if ($this->request->hasQuery('ot')) $ot = $this->request->getQuery('ot');

            if ($this->request->hasQuery('product_id') || $this->request->hasPost('product_id')) {
                if (!$ot) $ot = 'product';
                $oid = $this->request->hasPost('product_id') ? $this->request->getPost('product_id') : $this->request->getQuery('product_id');
            }

            if ($this->request->hasQuery('post_id') || $this->request->hasPost('post_id')) {
                if (!$ot) $ot = 'post';
                $oid = $this->request->hasPost('post_id') ? $this->request->getPost('post_id') : $this->request->getQuery('post_id');
            }

            if ($this->request->hasQuery('page_id') || $this->request->hasPost('page_id')) {
                if (!$ot) $ot = 'page';
                $oid = $this->request->hasPost('page_id') ? $this->request->getPost('page_id') : $this->request->getQuery('page_id');
            }

            if ($this->request->hasQuery('category_id') || $this->request->hasPost('category_id')) {
                if (!$ot) $ot = 'category';
                $oid = $this->request->hasPost('category_id') ? $this->request->getPost('category_id') : $this->request->getQuery('category_id');
            }

            if ($this->request->hasQuery('category_id') || $this->request->hasPost('category_id')) {
                if (!$ot) $ot = 'post_category';
                $oid = $this->request->hasPost('category_id') ? $this->request->getPost('category_id') : $this->request->getQuery('category_id');
            }

            if ($this->request->hasQuery('manufacturer_id') || $this->request->hasPost('manufacturer_id')) {
                if (!$ot) $ot = 'manufacturer';
                $oid = $this->request->hasPost('manufacturer_id') ? $this->request->getPost('manufacturer_id') : $this->request->getQuery('manufacturer_id');
            }

            if ($oid && $ot) {
                $average = ($this->config->get('config_review')) ? $this->modelReview->getAverageRating($oid, $ot) : 0;
                $this->data['text_stars'] = sprintf($this->language->get('text_stars'), $average);
            }

            return [
                'widget'   => $widget,
                'render'   => $render,
                'settings' => $settings,
            ];
        });
    }
}