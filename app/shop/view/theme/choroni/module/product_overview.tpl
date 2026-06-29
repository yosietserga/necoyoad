<?php $tpl = is_dir(DIR_TEMPLATE. $this->config->get('config_template') ."/shared") ? $this->config->get('config_template') : "choroni"; ?>
<?php include(DIR_TEMPLATE. $tpl ."/shared/widget-head.tpl");?> 
    <?php if ($heading_title) echo '<h2>'. $heading_title .'</h2>'; ?>
    <div itemprop="description" id="<?php echo $widgetName; ?>_productOverview">
        <p><?php echo $overview; ?></p>
    </div>
<?php include(DIR_TEMPLATE. $tpl ."/shared/widget-footer.tpl"); ?>