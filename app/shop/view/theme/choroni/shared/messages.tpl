<div class="large-12 medium-12 small-12 columns">
    <?php if (isset($success) && $success) { ?><span class="message success"><?php echo $success; ?><i class="icon icon-message icon-success"><?php include(DIR_TEMPLATE. $this->config->get('config_template') . "/shared/icons/check.tpl"); ?></i></span><?php } ?>
    <?php if (isset($error) && $error) { ?><span class="message warning"><?php echo $error; ?><i class="icon icon-message icon-warning"><?php include(DIR_TEMPLATE. $this->config->get('config_template') . "/shared/icons/bell.tpl"); ?></i></span><?php } ?>
</div>
<?php if ($heading){?>
    <h1><?php echo $heading; ?></h1>
<?php }?>
<?php if ($text){?>
    <h1><?php echo $text; ?></h1>
<?php }?>