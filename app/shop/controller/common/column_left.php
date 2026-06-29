<?php

class ControllerCommonColumnLeft extends Controller {
    protected function index($params=null) {
        $this->loadWidgets('column_left');
        

        $this->id = 'column_left';

        if ($this->data['rows']['column_left']) {
            if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/common/column_left.tpl')) {
                $this->template = $this->config->get('config_template') . '/common/column_left.tpl';
            } else {
                $this->template = 'choroni/common/column_left.tpl';
            }

            $this->render();
        }
    }
}
