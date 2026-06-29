<?php echo $header; ?>
<?php $tpl = is_dir(DIR_TEMPLATE. $this->config->get('config_template') ."/shared") ? $this->config->get('config_template') : "choroni"; ?>
<section id="maincontent" class="row">
    <?php include_once(DIR_TEMPLATE. $tpl ."/shared/featured-widgets.tpl"); ?>
    <?php include_once(DIR_TEMPLATE. $tpl ."/shared/columns-start.tpl"); ?>
    <?php include_once(DIR_TEMPLATE. $tpl ."/shared/messages.tpl"); ?>

    <?php if($widgets) { ?><ul class="widgets"><?php foreach ($widgets as $widget) { ?>{%<?php echo $widget; ?>%}<?php } ?></ul><?php } ?>

    <?php include_once(DIR_TEMPLATE. $tpl ."/shared/columns-end.tpl"); ?>

</section>
<?php echo $footer; ?>