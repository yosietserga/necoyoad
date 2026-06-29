<?php $tpl = is_dir(DIR_TEMPLATE. $this->config->get('config_template') ."/shared") ? $this->config->get('config_template') : "choroni"; ?>
<?php $settings['module'] = 'search'; ?> 
<?php include(DIR_TEMPLATE. $tpl ."/shared/widget-head.tpl");?> 
  <?php include(DIR_TEMPLATE. $tpl ."/shared/module-heading.tpl");?> 

  <div class="content" id="<?php echo $widgetName; ?>Content">
    <input id="<?php echo $widgetName; ?>Keyword" type="text" value="" autocomplete="off" placeholder="Buscar" />
    <a title="Buscar" class="button" onclick="moduleSearch($('#<?php echo $widgetName; ?>Keyword'));">
      <i class="fa fa-search"></i></a>
  </div>
  <div class="clear"></div><br />
<?php include(DIR_TEMPLATE. $tpl ."/shared/widget-footer.tpl"); ?>
<script>
  $(function () {
    $('#<?php echo $widgetName; ?>Keyword').on('keyup', function (e) {
      var code = e.keyCode || e.which;
      if ($(this).val().length > 0 && code == 13) {
        moduleSearch(this, $('#<?php echo $widgetName; ?>Category').val());
      }
    });
  });
</script>