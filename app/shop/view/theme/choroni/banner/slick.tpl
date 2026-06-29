<?php $tpl = is_dir(DIR_TEMPLATE. $this->config->get('config_template') ."/shared") ? $this->config->get('config_template') : "choroni"; ?> 
<?php $settings['class'] = !empty($settings['class']) ? $settings['class'].' slick' : 'slick'; ?>
<?php include(DIR_TEMPLATE. $tpl ."/shared/widget-head.tpl");?> 
    <?php include(DIR_TEMPLATE. $tpl ."/shared/module-heading.tpl");?> 

     <div class="widget-content" id="<?php echo $widgetName; ?>Content" data-banner="slick">
        <?php foreach ($banner['items'] as $item) { ?>
            <div>
                <?php if (empty($item['image'])) { continue; } ?>
                <?php if (!empty($item['link'])) { ?>
                        <a href="<?php echo $item['link']; ?>" title="<?php echo $item['descriptions'][$Config->get('config_language_id')]['title']; ?>">
                <?php } ?>
                <img src="<?php echo HTTP_IMAGE . $item['image']; ?>" data-thumb="<?php echo $item['thumb']; ?>" alt="<?php echo $item['descriptions'][$Config->get('config_language_id')]['title']; ?>" title="<?php echo $item['descriptions'][$Config->get('config_language_id')]['title']; ?>" />
                <?php if (!empty($item['link'])) { ?></a><?php } ?>
            </div>
        <?php } ?>
     </div>
<?php include(DIR_TEMPLATE. $tpl ."/shared/widget-footer.tpl");?>

<script>
    /**script:<?php echo $widgetName; ?>Scripts**/
    $(function(){
        ntPlugins = window.ntPlugins || [];

        ntPlugins.push({
            id:"<?php echo $widgetName; ?>Content",
            config:{
                slidesToShow: 1,
                slidesToScroll: 1,
                infinite: true,
                dots: false,
                fade: false,
                arrows: true,
                slide: 'div',
                cssEase: 'linear',
                useCSS: false
            },
            plugin:'slick'
        });
        window.ntPlugins = ntPlugins;
        
        if (typeof loadNTPlugins !== 'undefined' && typeof loadNTPlugins === 'function') {
            loadNTPlugins();
        }
    });
</script>
