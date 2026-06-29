<?php

require_once(realpath(dirname(__FILE__) . '/../widgetcontroller.php'));

class ControllerModuleRedirectWidget extends ControllerWidgetController
{
	//TODO: preload generated urls from the app, database, alias, etc.
	//TODO: make a proxy for external urls
	//TODO: Intl the message for each language configured
	protected string $moduleName = 'redirect';
}