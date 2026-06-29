<?php echo $header; ?>
<?php $tpl = is_dir(DIR_TEMPLATE. $this->config->get('config_template') ."/shared") ? $this->config->get('config_template') : "choroni"; ?> 
<!--contentContainer -->
<div id="contentContainer" class="tpl-special" nt-editable>
    <?php include(DIR_TEMPLATE. $tpl ."/shared/widgets-common.tpl");?>
</div>
<!--/contentContainer -->
<?php echo $footer; ?>