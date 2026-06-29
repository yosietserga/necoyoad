<?php $tpl = is_dir(DIR_TEMPLATE. $this->config->get('config_template') ."/shared") ? $this->config->get('config_template') : "choroni"; ?>
<?php include(DIR_TEMPLATE. $tpl ."/shared/widget-head.tpl");?> 

  <?php include(DIR_TEMPLATE. $tpl ."/shared/module-heading.tpl");?> 

  <div class="widget-content googlemap-widget-content" id="<?php echo $widgetName; ?>Content">
    <?php echo $settings['google_maps_code']; ?>
  </div>
<?php include(DIR_TEMPLATE. $tpl ."/shared/widget-footer.tpl");?>