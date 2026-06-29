<?php $tpl = is_dir(DIR_TEMPLATE. $this->config->get('config_template') ."/shared") ? $this->config->get('config_template') : "choroni"; ?>
<?php 
	include(DIR_TEMPLATE. $tpl ."/shared/widget-head.tpl");
    echo '<div class="placeholder">Open widget settings first for this module <b>'. $settings['module'] .'</b><p><b>'. $widgetName .'</b></p></div>';
    include(DIR_TEMPLATE. $tpl ."/shared/widget-footer.tpl");