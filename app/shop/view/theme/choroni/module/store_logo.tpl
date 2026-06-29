<?php $tpl = is_dir(DIR_TEMPLATE. $this->config->get('config_template') ."/shared") ? $this->config->get('config_template') : "choroni"; ?>
<?php $settings['module'] = 'store_logo'; ?>
<?php include(DIR_TEMPLATE. $tpl ."/shared/widget-head.tpl");?> 
	<?php if ($logo) { ?>
	<a title="<?php echo $store ?? ""; ?>" href="<?php echo str_replace('&', '&', HTTP_HOME); ?>"><img src="<?php echo $logo; ?>" title="<?php echo $store ?? ""; ?>" alt="<?php echo $store ?? ""; ?>" /></a>
	<?php } else { ?>
	<a title="<?php echo $store ?? ""; ?>" href="<?php echo str_replace('&', '&', HTTP_HOME); ?>"><?php echo $text_store; ?></a>
	<?php } ?>
    <div class="clear"></div><br />
<?php include(DIR_TEMPLATE. $tpl ."/shared/widget-footer.tpl"); ?>