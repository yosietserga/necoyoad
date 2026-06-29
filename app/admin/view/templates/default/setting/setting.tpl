<?php echo $header; ?>
<?php echo $navigation; ?>
<div class="container">
    
    <?php if (isset($breadcrumbs) && is_array($breadcrumbs)) { ?>
    <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
    </ul>
    <?php } ?>
    
    <?php if (isset($success) && $success) { ?><div class="grid_12"><div class="message success"><?php echo $success; ?></div></div><?php } ?>
    <?php if ((isset($msg) && $msg) || (isset($error_warning) && $error_warning)) { ?><div class="grid_12"><div class="message warning"><?php echo $msg ?? $error_warning; ?></div></div><?php } ?>
    <?php if (isset($error) && $error) { ?><div class="grid_12"><div class="message error"><?php echo $error; ?></div></div><?php } ?>
    <div class="grid_12" id="msg"></div>
    
    <div class="box">
        <h1><?php echo $l('heading_title'); ?></h1>
        <div class="buttons">
            <a onclick="saveAndExit();$('#form').submit();" class="button"><?php echo $l('button_save'); ?></a>
            <a onclick="location = '<?php echo $cancel; ?>';" class="button"><?php echo $l('button_cancel'); ?></a>
        </div>

        <div class="clear"></div>

        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
            <div class="htabs">
                <a tab="#general" class="htab"><?php echo $l('tab_general'); ?></a>
                <a tab="#store" class="htab"><?php echo $l('tab_store'); ?></a>
                <a tab="#local" class="htab"><?php echo $l('tab_local'); ?></a>
                <a tab="#option" class="htab"><?php echo $l('tab_option'); ?></a>
                <a tab="#image" class="htab"><?php echo $l('tab_image'); ?></a>
                <a tab="#mail" class="htab"><?php echo $l('tab_mail'); ?></a>
                <a tab="#server" class="htab"><?php echo $l('tab_server'); ?></a>
            </div>
            
            <p class="message warning">No olvides configurar los cronjobs de tu servidor: 0 * * * * php -f <?php echo str_replace('app/admin/../../', '', DIR_SYSTEM); ?>cron/cron.php > /dev/null 2>&1</p>

            <div id="general"><?php require_once(dirname(__FILE__)."/setting_form_general.tpl"); ?></div>
            <div id="store"><?php require_once(dirname(__FILE__)."/setting_form_store.tpl"); ?></div>
            <div id="local"><?php require_once(dirname(__FILE__)."/setting_form_local.tpl"); ?></div>
            <div id="option"><?php require_once(dirname(__FILE__)."/setting_form_option.tpl"); ?></div>
            <div id="image"><?php require_once(dirname(__FILE__)."/setting_form_images.tpl"); ?></div>
            <div id="mail"><?php require_once(dirname(__FILE__)."/setting_form_mail.tpl"); ?></div>
            <div id="server"><?php require_once(dirname(__FILE__)."/setting_form_server.tpl"); ?></div>
        </form>
    </div>
</div>

<script type="text/javascript">
$(document).ready(function() {
	jQuery('input[type=text],input[type=url],textarea,select,td:first-child').css({'width':'40%'});
	jQuery('input[type=number]').css({'width':'50px'});
	if (jQuery('#process_bounce:checked').val() != null) {
		jQuery("#bounce_process").show();
	} else {
		jQuery("#bounce_process").hide();
	}
	if (jQuery('#bounce_extraoption:checked').val() != null) {
		jQuery("#bounce_extra_sttings").show();
	} else {
		jQuery("#bounce_extra_sttings").hide();
	}
});
function other_setting(){
	if (jQuery('#extramail_others:checked').val() != null) {
		jQuery('#other').attr('disabled',false); 
	} else {
		jQuery('#other').attr('disabled',true); 
		jQuery('#other').val() = '';
	}
}
function show_bounce_settings() {
	var check = jQuery('#process_bounce:checked').val();
	if (jQuery('#process_bounce:checked').val() != null) {
		jQuery("#bounce_process").fadeIn();
	} else {
		jQuery("#bounce_process").fadeOut();
	}
}
jQuery("#bounce_extraoption").bind('click',function() {
	var check = jQuery('#bounce_extraoption:checked').val();
	if (jQuery('#bounce_extraoption:checked').val() != null) {
		jQuery("#bounce_extra_sttings").fadeIn();
	} else {
		jQuery("#bounce_extra_sttings").fadeOut();
	}
});
function bounce() {
	jQuery("#cmdTestBounce").fancybox({
				'width'				: '50%',
				'height'			: '50%',
				'autoScale'			: false,
				'type'				: 'ajax',
				'showCloseButton'   : true,
				'hideOnOverlayClick': false,
				'href'				: '<?php echo $Url::createAdminUrl("marketing/bounce/test"); ?>&config_bounce_server=' + encodeURIComponent(jQuery("input[name='config_bounce_server']").val()) 
				+ '&config_bounce_username=' + encodeURIComponent(jQuery("input[name='config_bounce_username']").val()) 
				+ '&config_bounce_password=' + encodeURIComponent(jQuery("input[name='config_bounce_password']").val()) 
				+ '&config_bounce_protocol=' + encodeURIComponent(jQuery("select[name='config_bounce_protocol']").val()) 
				+ '&extra_mail_nossl=' + a("input[name='extra_mail_nossl']") 
				+ '&extra_mail_notls=' + a("input[name='extra_mail_notls']") 
				+ '&extra_mail_novalidate=' + a("input[name='extra_mail_novalidate']")
				+ '&extra_mail_others=' + encodeURIComponent(jQuery("input[name='extra_mail_others']").val())
				+ '&config_bounce_agree_delete=' + encodeURIComponent(jQuery("input[name='config_bounce_agree_delete']").val())
			});
}
function a(e) {
	if (jQuery(e+":checked").val() != null) {
		return encodeURIComponent(jQuery(e+":checked").val());
	} else {
		return '';
	}
}
</script>

<script>
$(function() {
    $('#template').load('<?php echo $Url::createAdminUrl("setting/setting/template"); ?>&template=' + encodeURIComponent($('select[name=\'config_template\']').val()));

    $('#mobile_template').load('<?php echo $Url::createAdminUrl("setting/setting/template"); ?>&template=' + encodeURIComponent($('select[name=\'config_mobile_template\']').val()));

    $('#tablet_template').load('<?php echo $Url::createAdminUrl("setting/setting/template"); ?>&template=' + encodeURIComponent($('select[name=\'config_tablet_template\']').val()));

    $('#facebook_template').load('<?php echo $Url::createAdminUrl("setting/setting/template"); ?>&template=' + encodeURIComponent($('select[name=\'config_facebook_template\']').val()));

    $('#zone').load('<?php echo $Url::createAdminUrl("setting/setting/zone"); ?>&country_id=<?php echo $config_country_id; ?>&zone_id=<?php echo $config_zone_id; ?>');
});
</script>
<?php echo $footer; ?>