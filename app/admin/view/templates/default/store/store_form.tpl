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
            <a id="necoBoy" style="margin: 0px 10px;" title="NecoBoy ay&uacute;dame!"><img src="<?php echo HTTP_IMAGE; ?>necoBoy.png" alt="NecoBoy" /></a>
            <a onclick="saveAndExit();$('#form').submit();" class="button"><?php echo $l('button_save_and_exit'); ?></a>
            <a onclick="saveAndKeep();$('#form').submit();" class="button"><?php echo $l('button_save_and_keep'); ?></a>
            <a onclick="saveAndNew();$('#form').submit();" class="button"><?php echo $l('button_save_and_new'); ?></a>
            <a onclick="location = '<?php echo $cancel; ?>';" class="button"><?php echo $l('button_cancel'); ?></a>
        </div>
        
        <div class="clear"></div>

        <ol id="stepsNewProduct" class="joyRideTipContent" style="display:none">
            <li data-button="<?php echo $l('button_next'); ?>">
                <h2><?php echo $l('heading_joyride_begin'); ?></h2>
                <p><?php echo $l('help_joyride_begin'); ?></p>
            </li>
            <?php
            $a = array(
                1=>'necoApp',
                'necoFolder',
                'htabs',
                'necoName',
                'necoRif',
                'necoUrl',
                'necoCompany',
                'necoAddress',
                'necoEmail',
                'necoSender',
                'necoBounce',
                'necoTelePhone',
                'noClass',
                'necoTemplate',
                'necoContent',
                'necoTitle',
                'necoMetaDescription',
                'necoDescription',
                'noClass',
                'necoCountry',
                'necoState',
                'necoShopLanguage',
                'necoAdminLanguage',
                'necoCurrency',
                'necoDecimals',
                'necoThousands',
                'necoAutoCurrency',
                'noClass',
                'necoAdminItems',
                'necoShopItems',
                'necoNewProduct',
                'necoTax',
                'necoCustomerGroup',
                'necoShowPrice',
                'necoNewCustomer',
                'necoAccountTerms',
                'necoShoppingTerms',
                'necoShowStock',
                'necoCheckStock',
                'necoOrderStatus',
                'necoStockStatus',
                'necoAllowComments',
                'necoAproveComments',
                'necoAllowDownloads',
                'necoDownloadStatus',
                'necoCartWeight',
                'necoCartWeightCost',
                'noClass',
                'necoLogo',
                'necoIcon',
                'necoImage01',
                'necoImage02',
                'necoImage03',
                'necoImage04',
                'necoImage05',
                'necoImage06',
                'necoImage07',
                'necoImage08',
                'noClass',
                'noClass',
                'necoEmail01',
                'necoEmail02',
                'necoEmail03',
                'necoEmail04',
                'necoEmail05',
                'necoEmail06',
                'necoEmail07',
                'necoEmail08',
                'necoEmail09',
                'necoEmail10',
                'necoEmail11',
                'necoEmail12',
                'necoEmail13',
                'necoEmail14',
                'necoEmail15',
                'necoEmail16',
                'noClass',
                'necoServer01',
                'necoServer02',
                'necoServer03',
                'necoServer04',
                'necoServer05',
                'necoServer06',
                'necoServer07',
                'necoServer08',
                'necoServer09',
                'necoServer10',
                'necoServer11'
            );
            foreach($a as $k=>$v) {
            ?>
            <li<?php if ($v!=='noClass') { ?> data-class="<?php echo $v; ?>"<?php } ?> data-button="<?php echo $l('button_next'); ?>" data-options="tipLocation:right">
                <h2><?php echo $l('heading_joyride_'. $k); ?></h2>
                <p><?php echo $l('help_joyride_'. $k); ?></p>
            </li>
            <?php
            }
            ?>
            <li data-button="<?php echo $l('button_close'); ?>">
                <h2><?php echo $l('heading_joyride_final'); ?></h2>
                <p><?php echo $l('help_joyride_final'); ?></p>
            </li>
        </ol>
        <script>
            $(function(){
                $('#necoBoy').on('click', function(e){
                    $('#stepsNewProduct').joyride({
                        autoStart : true,
                        postStepCallback : function (index, tip) {
                            console.log(index);
                            console.log(tip);
                            if (index == 12) {
                                $('.htabs .htab:eq(1)').trigger('click');
                            }
                            if (index == 18) {
                                $('.htabs .htab:eq(2)').trigger('click');
                            }
                            if (index == 27) {
                                $('.htabs .htab:eq(3)').trigger('click');
                            }
                            if (index == 47) {
                                $('.htabs .htab:eq(4)').trigger('click');
                            }
                            if (index == 58) {
                                $('.htabs .htab:eq(5)').trigger('click');
                            }
                            if (index == 75) {
                                $('.htabs .htab:eq(6)').trigger('click');
                            }
                        },
                        modal:false,
                        expose:true
                    });
                });
            });
        </script>

        <div class="clear"></div>

        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
            <?php if ($_GET['r']=='store/store/insert') { ?>
            <div class="row">
                <label><?php echo $l('entry_own_file'); ?></label>
                <input type="checkbox" name="create_app" value="1" class="necoApp" />
            </div>               

            <div class="clear"></div>

            <div class="row">
                <label><?php echo $l('entry_folder'); ?></label>
                <input type="text" name="config_folder" value="" placeholder="newshop" class="necoFolder" />
            </div>

            <div class="clear"></div><br />
            <?php } ?>

            <div class="htabs product_tabs">
                <a data-target="#general" class="htab"><?php echo $l('tab_general'); ?></a>
                <a data-target="#store" class="htab"><?php echo $l('tab_store'); ?></a>
                <a data-target="#local" class="htab"><?php echo $l('tab_local'); ?></a>
                <a data-target="#option" class="htab"><?php echo $l('tab_option'); ?></a>
                <a data-target="#image" class="htab"><?php echo $l('tab_image'); ?></a>
                <a data-target="#mail" class="htab"><?php echo $l('tab_mail'); ?></a>
                <a data-target="#server" class="htab"><?php echo $l('tab_server'); ?></a>
                <?php if ($showContent) { ?><a data-target="#store_content" class="htab"><?php echo $l('tab_content'); ?></a><?php } ?>
            </div>

            <div class="product_tab" id="general"><?php require_once(dirname(__FILE__)."/store_form_general.tpl"); ?></div>
            <div class="product_tab" id="store"><?php require_once(dirname(__FILE__)."/store_form_store.tpl"); ?></div>
            <div class="product_tab" id="local"><?php require_once(dirname(__FILE__)."/store_form_local.tpl"); ?></div>
            <div class="product_tab" id="option"><?php require_once(dirname(__FILE__)."/store_form_option.tpl"); ?></div>
            <div class="product_tab" id="image"><?php require_once(dirname(__FILE__)."/store_form_images.tpl"); ?></div>
            <div class="product_tab" id="mail"><?php require_once(dirname(__FILE__)."/store_form_mail.tpl"); ?></div>
            <div class="product_tab" id="server"><?php require_once(dirname(__FILE__)."/store_form_server.tpl"); ?></div>
            <?php if ($showContent) { ?><div class="product_tab" id="store_content"><?php require_once(dirname(__FILE__)."/store_form_content.tpl"); ?></div><?php } ?>
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
function other_store(){
	if (jQuery('#extramail_others:checked').val() != null) {
		jQuery('#other').attr('disabled',false); 
	} else {
		jQuery('#other').attr('disabled',true); 
		jQuery('#other').val() = '';
	}
}
function show_bounce_stores() {
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
				'href'				: '<?php echo $Url::createAdminUrl("email/bounce/test"); ?>&config_bounce_server=' + encodeURIComponent(jQuery("input[name='config_bounce_server']").val()) 
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
$('#template').load('<?php echo $Url::createAdminUrl("store/store/template"); ?>&template=' + encodeURIComponent($('select[name=\'config_template\']').attr('value')));

$('#zone').load('<?php echo $Url::createAdminUrl("store/store/zone"); ?>&country_id=<?php echo $config_country_id; ?>&zone_id=<?php echo $config_zone_id; ?>');
</script>
<?php echo $footer; ?>