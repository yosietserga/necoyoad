<?php include(DIR_TEMPLATE. $tpl ."/shared/module-heading.tpl");?>

<div id="<?php echo $widgetName; ?>Content" class="links-02">
	<button class="dl-trigger">Open Menu</button>
	<?php echo $links; ?>
</div>

<script id="<?php echo $widgetName; ?>Content">
    $(function(){
        ntPlugins = window.ntPlugins || [];

        ntPlugins.push({
            id:'#<?php echo $widgetName; ?>Content',
            config:{
            },
            plugin:'dlmenu'
        });
        window.ntPlugins = ntPlugins;
        $('#<?php echo $widgetName; ?>').css({
        	display:'flex',
        	position:'relative',
        	zIndex:'9'
        });
    });
</script>