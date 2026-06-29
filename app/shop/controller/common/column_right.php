<?php

class ControllerCommonColumnRight extends Controller {
    protected function index($params=null) {
        $this->loadWidgets('column_right');
        

        $this->id = 'column_right';

        if ($this->data['rows']['column_right']) {
            if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/common/column_right.tpl')) {
                $this->template = $this->config->get('config_template') . '/common/column_right.tpl';
            } else {
                $this->template = 'choroni/common/column_right.tpl';
            }
            $this->render();
        }
    }
}
