<?php $tpl = is_dir(DIR_TEMPLATE. $this->config->get('config_template') ."/shared") ? $this->config->get('config_template') : "choroni"; ?>
<?php include(DIR_TEMPLATE. $tpl ."/shared/widget-head.tpl");?> 
    <?php include(DIR_TEMPLATE. $tpl ."/shared/module-heading.tpl");?>
    <i class="fa fa-phone"></i>
    <a href="tel:<?php echo $telephone;?>"><?php echo $telephone;?></a>
<?php include(DIR_TEMPLATE. $tpl ."/shared/widget-footer.tpl"); ?>