<?php $tpl = is_dir(DIR_TEMPLATE. $this->config->get('config_template') ."/shared") ? $this->config->get('config_template') : "choroni"; ?> 
<?php include(DIR_TEMPLATE. $tpl ."/shared/widget-head.tpl");?> 
    <?php include(DIR_TEMPLATE. $tpl ."/shared/module-heading.tpl");?> 


<?php if (count($banner['items'])) { ?>
<div class="content" id="<?php echo $widgetName; ?>Content">
    <div class="slider-wrapper theme-default" style="position:relative;">
        <div id="<?php echo $widgetName; ?>slider" class="nivoSlider">
            <?php foreach ($banner['items'] as $k => $item) { ?>
            <?php if (empty($item['image'])) { continue; } ?>
                <?php if (!empty($item['link'])) { ?><a href="<?php echo $item['link']; ?>" title="<?php echo $item['descriptions'][$Config->get('config_language_id')]['title']; ?>"> <?php } ?>

                <img src="<?php echo HTTP_IMAGE . $item['image']; ?>" data-thumb="<?php echo $Image->resizeAndSave($item['image'],50,50); ?>" alt="<?php echo $item['descriptions'][$Config->get('config_language_id')]['title']; ?>" title="<?php if (!empty($item['descriptions'][$Config->get('config_language_id')]['description'])) { echo '#'. $widgetName .'_slide_caption_'. $k; } elseif (!empty($item['descriptions'][$Config->get('config_language_id')]['title'])) { echo $item['descriptions'][$Config->get('config_language_id')]['title']; } ?>" />

                <?php if (!empty($item['link'])) { ?></a> <?php } ?>


                <?php if (!empty($item['descriptions'][$Config->get('config_language_id')]['description'])) { ?>
                <div id="<?php echo '#'. $widgetName .'_slide_caption_'. $k; ?>" class="nivo-html-caption">
                    <?php if (!empty($item['descriptions'][$Config->get('config_language_id')]['title'])) { ?><h2 data-apply="parseHTML"><?php echo $item['descriptions'][$Config->get('config_language_id')]['title']; ?></h2><?php } ?>
                    <p data-apply="parsehtml"><?php echo $item['descriptions'][$Config->get('config_language_id')]['description']; ?></p>
                    <?php if (!empty($item['link'])) { ?>
                    <a class="read-more" href="<?php echo $item['link']; ?>" title="<?php echo $item['descriptions'][$Config->get('config_language_id')]['title']; ?>">Más detalles</a>
                    <?php } ?>
                </div>
                <?php } ?>
            <?php } ?>
        </div>

    </div>
</div>
<script>
    /**script:<?php echo $widgetName; ?>Scripts**/
    $(function(){
        ntPlugins = window.ntPlugins || [];

        ntPlugins.push({
            id:"#<?php echo $widgetName; ?>slider",
            plugin:'nivoSlider'
        });
        window.ntPlugins = ntPlugins;
        
        if (typeof loadNTPlugins !== 'undefined' && typeof loadNTPlugins === 'function') {
            loadNTPlugins();
        }
    });
</script>
<?php } ?>
<?php include(DIR_TEMPLATE. $tpl ."/shared/widget-footer.tpl");?>
