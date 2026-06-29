<?php 
	$tpl = (is_dir(DIR_TEMPLATE. $this->config->get('config_template') ."/shared")) ? $this->config->get('config_template') : "choroni";
	include(DIR_TEMPLATE. $tpl ."/shared/widget-head.tpl");
    include($settings['module'] ."_". $settings['view'] .'.tpl');
    include(DIR_TEMPLATE. $tpl ."/shared/widget-footer.tpl");