<?php

require_once(realpath(dirname(__FILE__) . '/../widgetcontroller.php'));

class ControllerModuleRichTextWidget extends ControllerWidgetController
{
	protected string $moduleName = 'richtext';

	public function init()
	{
		$this->addFilter("widget:settings", function ($widget) {
			$this->load->model('content/page');
			$this->data['pages'] = $this->modelPage->getAll(['language_id' => $this->config->get('config_language_id')]);
        
			return $widget;
		});
	}

}