<?php $tpl = is_dir(DIR_TEMPLATE. $this->config->get('config_template') ."/shared") ? $this->config->get('config_template') : "choroni"; ?> 
<?php $settings['class'] = !empty($settings['class']) ? $settings['class'].' slick' : 'slick'; ?>
<?php include(DIR_TEMPLATE. $tpl ."/shared/widget-head.tpl");?> 
    <?php include(DIR_TEMPLATE. $tpl ."/shared/module-heading.tpl");?> 

    <div class="layer-slider" id="<?php echo $widgetName; ?>Content">
        <?php foreach ($banner['items'] as $item) { ?>
            <div data-slide="1" class="layer-slide">
                    <img src="<?php echo $Url::createUrl('common/home/getimage', array('image'=>$item['image'])); ?>" data-src="<?php echo $Url::createUrl('common/home/getimage', array('image'=>$item['image'])); ?>" />

                    <!--widgets-->
                    <?php if (!empty($item['widgets'])) { ?>
                    <ul><?php foreach ($item['widgets'] as $w) { echo '{%'. $w['name'] .'%}'; } ?></ul>
                    <?php } ?>
                    <!--/widgets-->

            </div>
        <?php } ?>
    </div>

    <a class="arrow-left" href="javascript:void(0);"></a>
    <a class="arrow-right" href="javascript:void(0);"></a>
    <div class="dots-wrapper"></div>

<?php include(DIR_TEMPLATE. $tpl ."/shared/widget-footer.tpl");?>

<script>
    /**script:<?php echo $widgetName; ?>Scripts**/
    $(function(){
        ntPlugins = window.ntPlugins || [];

        ntPlugins.push({
            id:'#<?php echo $widgetName; ?>Content',
            config:{
                parentSelector: '#<?php echo $widgetName; ?>',
                targetSelector: '#<?php echo $widgetName; ?>Content',
                dotsSelector: '.dots-wrapper',
                arrowLeftSelector: '.arrow-left',
                arrowRightSelector: '.arrow-right'
            },
            plugin:'slider_plugin'
        });
        window.ntPlugins = ntPlugins;
        
        if (typeof loadNTPlugins !== 'undefined' && typeof loadNTPlugins === 'function') {
            loadNTPlugins();
        }
    });
</script>