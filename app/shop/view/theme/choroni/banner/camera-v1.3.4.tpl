<?php $tpl = is_dir(DIR_TEMPLATE. $this->config->get('config_template') ."/shared") ? $this->config->get('config_template') : "choroni"; ?> 
<?php include(DIR_TEMPLATE. $tpl ."/shared/widget-head.tpl");?> 
    <?php include(DIR_TEMPLATE. $tpl ."/shared/module-heading.tpl");?> 

<?php if (count($banner['items'])) { ?>
<div class="content" id="<?php echo $widgetName; ?>Content">
    <div class="camera_wrap camera_ash_skin" id="<?php echo $widgetName; ?>camera">
    <?php foreach ($banner['items'] as $item) { ?>
        <?php if (!empty($item['image'])) { ?>
            <div data-thumb="<?php echo $Image->resizeAndSave($item['image'],285,115); ?>" data-src="<?php echo HTTP_IMAGE . $item['image']; ?>">
                <?php if (!empty($item['descriptions'][$Config->get('config_language_id')]['description'])) { ?>
                    <div class="camera_caption fadeIn">
                        <h1><a href="<?php echo $item['link']; ?>" title="<?php echo $item['descriptions'][$Config->get('config_language_id')]['title']; ?>"><?php echo $item['descriptions'][$Config->get('config_language_id')]['title']; ?></a></h1>
                        <p><?php echo $item['descriptions'][$Config->get('config_language_id')]['description']; ?></p>
                        <?php if (!empty($item['link'])) { ?>
                            <a class="read-more" href="<?php echo $item['link']; ?>" title="<?php echo $item['descriptions'][$Config->get('config_language_id')]['title']; ?>">Más detalles</a>
                        <?php } ?>
                    </div>
                <?php } ?>
            </div>
        <?php } ?> 
    <?php } ?>
    </div>
</div>

<script id="cameraPlugin">
    /**script:<?php echo $widgetName; ?>Scripts**/
    $(function(){
        ntPlugins = window.ntPlugins || [];

        ntPlugins.push({
            id:'#<?php echo $widgetName; ?>camera',
            config:{
                navigation: false,
                navigationHover: false,
                playPause: false,
                pagination: true
            },
            plugin:'camera'
        });
        window.ntPlugins = ntPlugins;
        
        if (typeof loadNTPlugins !== 'undefined' && typeof loadNTPlugins === 'function') {
            loadNTPlugins();
        }
    });
    /** /script:<?php echo $widgetName; ?>Scripts**/
</script>
<?php } ?>
<?php include(DIR_TEMPLATE. $tpl ."/shared/widget-footer.tpl");?>
