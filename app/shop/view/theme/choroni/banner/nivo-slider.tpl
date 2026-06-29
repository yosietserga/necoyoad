<?php $tpl = is_dir(DIR_TEMPLATE. $this->config->get('config_template') ."/shared") ? $this->config->get('config_template') : "choroni"; ?> 
<li id="<?php echo $widgetName; ?>" class="banner nivo<?php echo ($settings['class']) ? " ".$settings['class'] : ''; ?> nt-editable" data-banner="nivoSlider">
<?php if ($heading_title) { ?>
    <div class="header" id="<?php echo $widgetName; ?>Header">
        <h3><?php echo $heading_title; ?></h3>
    </div><?php } ?>
<?php if (count($banner['items'])) { ?>
    <div class="content" id="<?php echo $widgetName; ?>Content">
        <div class="slider-wrapper theme-default">
            <div id="slider" class="nivoSlider">
                <?php foreach ($banner['items'] as $item) { ?>
                    <?php if (empty($item['image'])) continue; ?>
                    <?php if (!empty($item['link'])) { ?><a href="<?php echo $item['link']; ?>" title="<?php echo $item['descriptions'][$Config->get('config_language_id')]['title']; ?>"><?php } ?>
                    <img src="<?php echo HTTP_IMAGE . $item['image']; ?>" data-thumb="<?php echo $Image->resizeAndSave($item['image'],50,50); ?>" alt="<?php echo $item['descriptions'][$Config->get('config_language_id')]['title']; ?>" title="<?php echo $item['descriptions'][$Config->get('config_language_id')]['title']; ?>" />
                    <?php if (!empty($item['link'])) { ?></a><?php } ?>
                <?php } ?>
            </div>
        </div>
    </div>
    <script>
    /**script:<?php echo $widgetName; ?>Scripts**/
    $(function(){
        ntPlugins = window.ntPlugins || [];

        ntPlugins.push({
            id:"<?php echo $widgetname; ?> .nivoSlider",
            config:{
                effect:'random',
                slices:12,
                animSpeed:300,
                pauseTime:6000,
                startSlide:0,
                directionNav:false,
                directionNavHide:true,
                controlNav: false,
                controlNavThumbs:true,
                controlNavThumbsFromRel:false,
                controlNavThumbsSearch: '.jpg',
                controlNavThumbsReplace: '_thumb.jpg',
                keyboardNav:true,
                pauseOnHover:true,
                manualAdvance:false,
                captionOpacity: 0.8,
                beforeChange: function(){},
                afterChange: function(){},
                slideshowEnd: function(){}
            },
            plugin:'nivoSlider'
        });
        window.ntPlugins = ntPlugins;
        
        if (typeof loadNTPlugins !== 'undefined' && typeof loadNTPlugins === 'function') {
            loadNTPlugins();
        }
    });
    </script>
<?php } ?>
</li>
