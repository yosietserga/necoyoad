<?php echo $header; ?>
<?php $tpl = is_dir(DIR_TEMPLATE. $this->config->get('config_template') ."/shared") ? $this->config->get('config_template') : "choroni"; ?> 

<div class="container">
    <section id="maincontent" class="row">
        <?php include(DIR_TEMPLATE. $tpl ."/shared/page-start.tpl");?>

        <span class="error-content"><?php echo $text_error; ?></span>

        <!-- widgets -->
        <div class="large-12 medium-12 small-12 columns">
            <?php if($widgets) { ?><ul class="widgets"><?php foreach ($widgets as $widget) { ?>{%<?php echo $widget; ?>%}<?php } ?></ul><?php } ?>
        </div>
        <!-- widgets -->

       <?php include(DIR_TEMPLATE. $tpl ."/shared/columns-end.tpl"); ?>
    </section>
</div>
<?php echo $footer; ?>