<?php $tpl = is_dir(DIR_TEMPLATE. $this->config->get('config_template') ."/shared") ? $this->config->get('config_template') : "choroni"; ?>
<?php include(DIR_TEMPLATE. $tpl ."/shared/widget-head.tpl");?> 
    <?php include("product_list_". $settings['view'] .'.tpl'); ?>
<?php include(DIR_TEMPLATE. $tpl ."/shared/widget-footer.tpl"); ?>