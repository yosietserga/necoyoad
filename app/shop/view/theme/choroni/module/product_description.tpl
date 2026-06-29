<?php $tpl = is_dir(DIR_TEMPLATE. $this->config->get('config_template') ."/shared") ? $this->config->get('config_template') : "choroni"; ?>
<?php include(DIR_TEMPLATE. $tpl ."/shared/widget-head.tpl");?> 
    <div itemprop="description" id="<?php echo $widgetName; ?>_productDescription"><?php echo $description;?></div>
<?php include(DIR_TEMPLATE. $tpl ."/shared/widget-footer.tpl"); ?>