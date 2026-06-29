<?php $tpl = is_dir(DIR_TEMPLATE. $this->config->get('config_template') ."/shared") ? $this->config->get('config_template') : "choroni"; ?>
<?php include(DIR_TEMPLATE. $tpl ."/shared/widget-head.tpl");?> 
    <?php include("lightbox_". $settings['view'] .'.tpl'); ?>
<?php include(DIR_TEMPLATE. $tpl ."/shared/widget-footer.tpl");?>
<script>
    $(function() {
        var style = $('<style>').text(
            '#<?php echo $widgetName; ?>,' +
            '#<?php echo $widgetName; ?>:before ' +
            '{' +
                'background:<?php echo $necoTool->hex2rgba($settings["background"], $settings["opacity"]); ?>; !important' +
            '}'
        ).appendTo('head');
    });
</script>