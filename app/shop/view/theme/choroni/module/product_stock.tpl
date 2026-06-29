<?php $tpl = is_dir(DIR_TEMPLATE. $this->config->get('config_template') ."/shared") ? $this->config->get('config_template') : "choroni"; ?>
<?php include(DIR_TEMPLATE. $tpl ."/shared/widget-head.tpl");?> 
    <div itemprop="availability" href="https://schema.org/InStock" id="<?php echo $widgetName; ?>_productAvailability">
        <?php echo $heading_title; ?>
        <span><?php echo $stock; ?></span>
    </div>
<?php include(DIR_TEMPLATE. $tpl ."/shared/widget-footer.tpl"); ?>