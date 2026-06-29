<?php $tpl = is_dir(DIR_TEMPLATE. $this->config->get('config_template') ."/shared") ? $this->config->get('config_template') : "choroni"; ?> 
<?php include(DIR_TEMPLATE. $tpl ."/shared/widget-head.tpl");?> 
    <?php include(DIR_TEMPLATE. $tpl ."/shared/module-heading.tpl");?> 

    <?php if (count($banner['items'])) { ?>
        <div class="widget-content" id="<?php echo $widgetName; ?>Content">
        
			<div id="<?php echo $widgetName; ?>slider" class="da-slider">
            <?php foreach ($banner['items'] as $item) { ?>
            
				<div class="da-slide">
					<?php if (!empty($item['descriptions'][$Config->get('config_language_id')]['title'])) { ?><h2><?php echo $item['descriptions'][$Config->get('config_language_id')]['title']; ?></h2><?php } ?>
					<?php if (!empty($item['descriptions'][$Config->get('config_language_id')]['description'])) { ?><p><?php echo $item['descriptions'][$Config->get('config_language_id')]['description']; ?></p><?php } ?>
					<?php if (!empty($item['link'])) { ?><a href="<?php echo $item['link']; ?>" class="da-link"><?php echo $l('Read More'); ?></a><?php } ?>
                    
					<?php if (!empty($item['image'])) { ?><div class="da-img"><img src="<?php echo $Image->resizeAndSave($item['image'],275,275); ?>" alt="<?php echo $item['descriptions'][$Config->get('config_language_id')]['title']; ?>"/></div><?php } ?>
				</div>
                
                <?php } ?>
            </div>
        </div>
    <?php } ?>
<?php include(DIR_TEMPLATE. $tpl ."/shared/widget-footer.tpl");?>

<script>
    /**script:<?php echo $widgetName; ?>Scripts**/
    $(function(){
        ntPlugins = window.ntPlugins || [];

        ntPlugins.push({
            id:"<?php echo $widgetname; ?>slider",
            config:{
    			autoplay	: true
            },
            plugin:'cslider'
        });
        window.ntPlugins = ntPlugins;
        
        if (typeof loadNTPlugins !== 'undefined' && typeof loadNTPlugins === 'function') {
            loadNTPlugins();
        }
    });
</script>
