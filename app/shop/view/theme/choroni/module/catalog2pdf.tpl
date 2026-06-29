<?php $tpl = is_dir(DIR_TEMPLATE. $this->config->get('config_template') ."/shared") ? $this->config->get('config_template') : "choroni"; ?> 
<?php include(DIR_TEMPLATE. $tpl ."/shared/widget-head.tpl"); ?> 
    <?php include(DIR_TEMPLATE. $tpl ."/shared/module-heading.tpl");?>
    <div class="widget-content catalogtopdf-widget-content" id="<?php echo $widgetName; ?>Content">
        <a href="<?php echo str_replace("&", "&amp;", $href); ?>" title="<?php echo $l('text_download_pdf'); ?>" class="button"><?php echo $l('text_download_pdf'); ?></a>
    </div>
<?php include(DIR_TEMPLATE. $tpl ."/shared/widget-footer.tpl");?>