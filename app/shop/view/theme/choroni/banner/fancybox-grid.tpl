<?php $tpl = is_dir(DIR_TEMPLATE. $this->config->get('config_template') ."/shared") ? $this->config->get('config_template') : "choroni"; ?> 
<?php $settings['class'] = !empty($settings['class']) ? $settings['class'].' fancybox' : 'fancybox'; ?>
<?php include(DIR_TEMPLATE. $tpl ."/shared/widget-head.tpl");?> 
    <?php include(DIR_TEMPLATE. $tpl ."/shared/module-heading.tpl");?> 

    <?php if (count($banner['items'])) { ?>
        <div class="widget-content ri-grid ri-grid-size-2" id="<?php echo $widgetName; ?>Content">
            <ul class="catalog block-grid ">
            <?php foreach ($banner['items'] as $item) { ?>
                <!--picture-->
                <li class="catalog-item">
                    <?php if (!empty($item['image'])) { ?>
                            <a data-item="fancybox" title="<?php echo $item['descriptions'][$Config->get('config_language_id')]['title']; ?>" rel="<?php echo $widgetName; ?>group" data-fancybox="<?php echo $widgetName; ?>gallery" class="thumb fancybox b-horizontal-picture b-horizontal-item" href="<?php echo $Image->resizeAndSave($item['image'],550,550); ?>">
                                <img src="<?php echo $Image->resizeAndSave($item['image'],275,275); ?>" alt=""/>
                            </a>
                    <?php } ?>
                </li>
                <?php } ?>
            </ul>
        </div>
    <?php } ?>
<?php include(DIR_TEMPLATE. $tpl ."/shared/widget-footer.tpl");?>

<script>
    /**script:<?php echo $widgetName; ?>Scripts**/
    $(function(){
        ntPlugins = window.ntPlugins || [];

        ntPlugins.push({
            id:"<?php echo $widgetName; ?>Content",
            config:{
                rows : 4,
                columns : 10,
                w1024 : { rows : 1, columns : 2 },
                w768 : {rows : 1,columns : 2 },
                w480 : {rows : 1,columns : 2 },
                w320 : {rows : 1,columns : 2 },
                w240 : {rows : 1,columns : 2 },
                /*
                w1024 : { rows : 3, columns : 8 },
                w768 : {rows : 3,columns : 7 },
                w480 : {rows : 3,columns : 5 },
                w320 : {rows : 2,columns : 4 },
                w240 : {rows : 2,columns : 3 },
                */
                step : 'random',
                maxStep : 3,
                preventClick : false,
                animType : 'random',
                animSpeed : 800,
                animEasingOut : 'linear',
                animEasingIn: 'linear',
                interval : 3000,
                slideshow : true,
                onhover : false,
                nochange : [0,1,2,3]
            },
            plugin:'gridrotator'
        });
        window.ntPlugins = ntPlugins;
        
        if (typeof loadNTPlugins !== 'undefined' && typeof loadNTPlugins === 'function') {
            loadNTPlugins();
        }
    });
</script>
