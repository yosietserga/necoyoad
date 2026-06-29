<?php $tpl = is_dir(DIR_TEMPLATE. $this->config->get('config_template') ."/shared") ? $this->config->get('config_template') : "choroni"; ?> 
<?php include(DIR_TEMPLATE. $tpl ."/shared/widget-head.tpl");?> 
    <?php include(DIR_TEMPLATE. $tpl ."/shared/module-heading.tpl");?> 

<?php if (count($banner['items'])) { ?>
<div class="content" id="<?php echo $widgetName; ?>Content">
    <div id="<?php echo $widgetName; ?>jssorPlugin" class="jssorContainer">
    
        <div data-u="loading">
            <div></div>
            <div></div>
        </div>
        
        <div data-u="slides">
        <?php foreach ($banner['items'] as $item) { ?>
            <?php if (!empty($item['image'])) { ?>
                <div>
                    <img data-u="image" src="<?php echo HTTP_IMAGE . $item['image']; ?>" />
                    <div data-u="thumb">
                        <img class="i" src="<?php echo $Image->resizeAndSave($item['image'],80,50); ?>" />
                        <?php if (!empty($item['descriptions'][$Config->get('config_language_id')]['title'])) { ?><div class="t"><?php echo $item['descriptions'][$Config->get('config_language_id')]['title']; ?></div><?php } ?>
                        <?php if (!empty($item['descriptions'][$Config->get('config_language_id')]['description'])) { ?><div class="c"><?php echo $item['descriptions'][$Config->get('config_language_id')]['description']; ?></div><?php } ?>
                    </div>
                </div>
            <?php } ?>
        <?php } ?>
        </div>
    
    
        <!-- Thumbnail Navigator -->
        <div data-u="thumbnavigator" class="jssort11" data-autocenter="2">
            <!-- Thumbnail Item Skin Begin -->
            <div data-u="slides">
                <div data-u="prototype" class="p">
                    <div data-u="thumbnailtemplate" class="tp"></div>
                </div>
            </div>
            <!-- Thumbnail Item Skin End -->
        </div>
        
        
        <!-- Arrow Navigator -->
        <span data-u="arrowleft" class="jssora02l" data-autocenter="2"></span>
        <span data-u="arrowright" class="jssora02r" data-autocenter="2"></span>
    </div>
</div>

<script id="jssorPlugin">
    /**script:<?php echo $widgetName; ?>Scripts**/
    $(function(){
        ntPlugins = window.ntPlugins || [];

        ntPlugins.push({
            id:'#<?php echo $widgetName; ?>jssorPlugin',
            config:{
                $AutoPlay: true,
                $ArrowNavigatorOptions: {
                    $Class: $JssorArrowNavigator$
                },
                $ThumbnailNavigatorOptions: {
                    $Class: $JssorThumbnailNavigator$,
                    $Cols: 4,
                    $SpacingX: 4,
                    $SpacingY: 4,
                    $Orientation: 2,
                    $Align: 0
                }
            },
            plugin:'jssorPlugin'
        });
        window.ntPlugins = ntPlugins;
        
        if (typeof loadNTPlugins !== 'undefined' && typeof loadNTPlugins === 'function') {
            loadNTPlugins();
        }
    });
</script>
<?php } ?>
<?php include(DIR_TEMPLATE. $tpl ."/shared/widget-footer.tpl");?>