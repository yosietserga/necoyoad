<?php include(DIR_TEMPLATE. $tpl ."/shared/module-heading.tpl");?>

<div id="<?php echo $widgetName; ?>Content" class="links-overheader">
	<?php echo $links; ?>
	<i data-icon class="fa fa-bars">&nbsp;</i>
</div>

<script>
	$('#<?php echo $widgetName; ?>Content [data-icon]').on('click', ()=>{
		var menu = $('#<?php echo $widgetName; ?>Content');
		if (menu.hasClass('responsive')) {
			menu.removeClass('responsive');
			$('#<?php echo $widgetName; ?>Content [data-icon]').removeClass('fa-window-close').addClass('fa-bars');
		} else {
			menu.addClass('responsive');
			$('#<?php echo $widgetName; ?>Content [data-icon]').removeClass('fa-bars').addClass('fa-window-close');
		}
	});
</script>