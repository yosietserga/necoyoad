<?php $tpl = is_dir(DIR_TEMPLATE. $this->config->get('config_template') ."/shared") ? $this->config->get('config_template') : "choroni"; ?> 
<?php include(DIR_TEMPLATE. $tpl ."/shared/widget-head.tpl");?> 
    <?php include(DIR_TEMPLATE. $tpl ."/shared/module-heading.tpl");?> 

    <?php if (count($banner['items'])) { ?>
        <div class="widget-content" id="<?php echo $widgetName; ?>Content">
            <ul id="<?php echo $widgetName; ?>grid">
            <?php foreach ($banner['items'] as $item) { ?>
                <!--picture-->
                <li class="catalog-item">
                    <?php if (empty($item['image'])) continue; ?>
                    <?php if (!empty($item['link'])) { ?> <a href="<?php echo $item['link']; ?>"><?php } ?>
                        <img src="<?php echo $Image->resizeAndSave($item['image'],80,80); ?>" />
                    <?php if (!empty($item['link'])) { ?></a><?php } ?>
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
            id:"<?php echo $widgetName; ?>grid",
            config:{
        		rows : 4,
        		columns : 10,
        		w1024 : { rows : 3, columns : 8 },
        		w768 : {rows : 3,columns : 7 },
        		w480 : {rows : 3,columns : 5 },
        		w320 : {rows : 2,columns : 4 },
        		w240 : {rows : 2,columns : 3 },
        		step : 'random',
        		maxStep : 3,
        		<?php if (!empty($item['link'])) { ?>
                preventClick : false,
                <?php } else { ?>
                preventClick : true,
                <?php } ?>
        		animType : 'random',
        		animSpeed : 800,
        		animEasingOut : 'linear',
        		animEasingIn: 'linear',
        		interval : 3000,
        		slideshow : true,
        		onhover : true,
				nochange : []
            },
            plugin:'gridrotator'
        });
        window.ntPlugins = ntPlugins;
        
        if (typeof loadNTPlugins !== 'undefined' && typeof loadNTPlugins === 'function') {
            loadNTPlugins();
        }
    });
</script>
