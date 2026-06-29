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
        <?php if ($product_id) { ?><a href="<?php echo $Url::createUrl("store/product",array('product_id'=>$product_id),'NONSSL',HTTP_CATALOG); ?>" target="_blank"><?php echo $l('text_see_product_in_storefront'); ?></a><?php } ?>
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
                <h2><?php echo $l('heading_tour_welcome'); ?></h2>
                <p><?php echo $l('help_tour_welcome'); ?></p>
            </li>
            <li data-class="htabs2" data-button="<?php echo $l('button_next'); ?>" data-options="tipLocation:right">
                <h2><?php echo $l('heading_product_language_tabs'); ?></h2>
                <p><?php echo $l('help_product_language_tabs'); ?></p>
            </li>
            <li data-class="necoName" data-button="<?php echo $l('button_next'); ?>" data-options="tipLocation:right">
                <h2><?php echo $l('heading_product_name'); ?></h2>
                <p><?php echo $l('help_product_name'); ?></p>
            </li>
            <li data-class="necoKeywords" data-button="<?php echo $l('button_next'); ?>" data-options="tipLocation:right">
                <h2><?php echo $l('heading_product_keywords'); ?></h2>
                <p><?php echo $l('help_product_keywords'); ?></p>
            </li>
            <li data-class="necoMetaDescription" data-button="<?php echo $l('button_next'); ?>" data-options="tipLocation:right">
                <h2><?php echo $l('heading_product_meta_description'); ?></h2>
                <p><?php echo $l('help_product_meta_description'); ?></p>
            </li>
            <li data-class="necoDescription" data-button="<?php echo $l('button_next'); ?>" data-options="tipLocation:top">
                <h2><?php echo $l('heading_product_description'); ?></h2>
                <p><?php echo $l('help_product_description'); ?></p>
            </li>
            <li data-class="necoKeyword" data-button="<?php echo $l('button_next'); ?>" data-options="tipLocation:right">
                <h2><?php echo $l('heading_product_keyword'); ?></h2>
                <p><?php echo $l('help_product_keyword'); ?></p>
            </li>
            <li data-button="<?php echo $l('button_next'); ?>">
                <h2><?php echo $l('heading_product_data_tab'); ?></h2>
                <p><?php echo $l('help_product_data_tab'); ?></p>
            </li>
            <li data-class="necoModel" data-button="<?php echo $l('button_next'); ?>" data-options="tipLocation:right">
                <h2><?php echo $l('heading_product_model'); ?></h2>
                <p><?php echo $l('help_product_model'); ?></p>
            </li>
            <li data-class="necoPrice" data-button="<?php echo $l('button_next'); ?>" data-options="tipLocation:right">
                <h2><?php echo $l('heading_product_price'); ?></h2>
                <p><?php echo $l('help_product_price'); ?></p>
            </li>
            <li data-class="necoTaxClass" data-button="<?php echo $l('button_next'); ?>" data-options="tipLocation:right">
                <h2><?php echo $l('heading_product_tax_class'); ?></h2>
                <p><?php echo $l('help_product_tax_class'); ?></p>
            </li>
            <li data-class="necoQuantity" data-button="<?php echo $l('button_next'); ?>" data-options="tipLocation:right">
                <h2><?php echo $l('heading_product_quantity'); ?></h2>
                <p><?php echo $l('help_product_quantity'); ?></p>
            </li>
            <li data-class="necoMinimun" data-button="<?php echo $l('button_next'); ?>" data-options="tipLocation:right">
                <h2><?php echo $l('heading_product_minimun'); ?></h2>
                <p><?php echo $l('help_product_minimun'); ?></p>
            </li>
            <li data-class="necoImage" data-button="<?php echo $l('button_next'); ?>" data-options="tipLocation:right">
                <h2><?php echo $l('heading_product_image'); ?></h2>
                <p><?php echo $l('help_product_image'); ?></p>
            </li>
            <li data-class="necoDateAvailable" data-button="<?php echo $l('button_next'); ?>" data-options="tipLocation:right">
                <h2><?php echo $l('heading_product_date_available'); ?></h2>
                <p><?php echo $l('help_product_date_available'); ?></p>
            </li>
            <li data-class="necoStockStatus" data-button="<?php echo $l('button_next'); ?>" data-options="tipLocation:right">
                <h2><?php echo $l('heading_product_stock_status'); ?></h2>
                <p><?php echo $l('help_product_stock_status'); ?></p>
            </li>
            <li data-class="necoStatus" data-button="<?php echo $l('button_next'); ?>" data-options="tipLocation:right">
                <h2><?php echo $l('heading_product_status'); ?></h2>
                <p><?php echo $l('help_product_status'); ?></p>
            </li>
            <li data-class="necoSubtract" data-button="<?php echo $l('button_next'); ?>" data-options="tipLocation:right">
                <h2><?php echo $l('heading_product_subtract'); ?></h2>
                <p><?php echo $l('help_product_subtract'); ?></p>
            </li>
            <li data-class="necoShipping" data-button="<?php echo $l('button_next'); ?>" data-options="tipLocation:right">
                <h2><?php echo $l('heading_product_shipping'); ?></h2>
                <p><?php echo $l('help_product_shipping'); ?></p>
            </li>
            <li data-class="necoWeight" data-button="<?php echo $l('button_next'); ?>" data-options="tipLocation:right">
                <h2><?php echo $l('heading_product_weight'); ?></h2>
                <p><?php echo $l('help_product_weight'); ?></p>
            </li>
            <li data-class="necoWeightClass" data-button="<?php echo $l('button_next'); ?>" data-options="tipLocation:right">
                <h2><?php echo $l('heading_product_weight_class'); ?></h2>
                <p><?php echo $l('help_product_weight_class'); ?></p>
            </li>
            <li data-button="<?php echo $l('button_next'); ?>">
                <h2><?php echo $l('heading_product_link_tab'); ?></h2>
                <p><?php echo $l('help_product_link_tab'); ?></p>
            </li>
            <li data-class="necoManufacturer" data-button="<?php echo $l('button_next'); ?>" data-options="tipLocation:right">
                <h2><?php echo $l('heading_product_manufacturer'); ?></h2>
                <p><?php echo $l('help_product_manufacturer'); ?></p>
            </li>
            <li data-class="necoCategory" data-button="<?php echo $l('button_next'); ?>" data-options="tipLocation:right">
                <h2><?php echo $l('heading_product_category'); ?></h2>
                <p><?php echo $l('help_product_category'); ?></p>
            </li>
            <li data-class="necoStore" data-button="<?php echo $l('button_next'); ?>" data-options="tipLocation:right">
                <h2><?php echo $l('heading_product_store'); ?></h2>
                <p><?php echo $l('help_product_store'); ?></p>
            </li>
            <li data-class="necoDownload" data-button="<?php echo $l('button_next'); ?>" data-options="tipLocation:right">
                <h2><?php echo $l('heading_product_download'); ?></h2>
                <p><?php echo $l('help_product_download'); ?></p>
            </li>
            <li data-class="necoRelated" data-button="<?php echo $l('button_next'); ?>" data-options="tipLocation:top">
                <h2><?php echo $l('heading_product_related'); ?></h2>
                <p><?php echo $l('help_product_related'); ?></p>
            </li>
            <li data-button="<?php echo $l('button_next'); ?>">
                <h2><?php echo $l('heading_product_other_tabs'); ?></h2>
                <p><?php echo $l('help_product_other_tabs'); ?></p>
            </li>
            <li data-button="<?php echo $l('button_next'); ?>">
                <h2><?php echo $l('heading_product_discount_tab'); ?></h2>
                <p><?php echo $l('help_product_discount_tab'); ?></p>
            </li>
            <li data-button="<?php echo $l('button_next'); ?>">
                <h2><?php echo $l('heading_product_special_tab'); ?></h2>
                <p><?php echo $l('help_product_special_tab'); ?></p>
            </li>
            <li data-button="<?php echo $l('button_next'); ?>">
                <h2><?php echo $l('heading_product_options_tab'); ?></h2>
                <p><?php echo $l('help_product_options_tab'); ?></p>
            </li>
            <li data-button="<?php echo $l('button_next'); ?>">
                <h2><?php echo $l('heading_product_images_tab'); ?></h2>
                <p><?php echo $l('help_product_images_tab'); ?></p>
            </li>
            <li data-button="<?php echo $l('button_close'); ?>">
                <h2><?php echo $l('heading_steps_final'); ?></h2>
                <p><?php echo $l('help_steps_final'); ?></p>
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
                            if (index == 6) {
                                $('.htabs .htab:eq(1)').trigger('click');
                            }
                            if (index == 20) {
                                $('.htabs .htab:eq(2)').trigger('click');
                            }
                            if (index == 27) {
                                $('.htabs .htab:eq(3)').trigger('click');
                            }
                            if (index == 28) {
                                $('.htabs .htab:eq(4)').trigger('click');
                            }
                            if (index == 29) {
                                $('.htabs .htab:eq(5)').trigger('click');
                            }
                            if (index == 30) {
                                $('.htabs .htab:eq(6)').trigger('click');
                            }
                        },
                        modal:false,
                        expose:true,
                    });
                });
            });
        </script>

        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
            <div class="htabs">
                <a tab="#general" class="htab"><?php echo $l('tab_general'); ?></a>
                <a tab="#data" class="htab"><?php echo $l('tab_data'); ?></a>
                <a tab="#links" class="htab"><?php echo $l('tab_links'); ?></a>
                <a tab="#discount" class="htab"><?php echo $l('tab_discount'); ?></a>
                <a tab="#special" class="htab"><?php echo $l('tab_special'); ?></a>
                <a tab="#option" class="htab"><?php echo $l('tab_option'); ?></a>
                <a tab="#images" class="htab"><?php echo $l('tab_image'); ?></a>
                <a tab="#attributes" class="htab"><?php echo $l('Attributes'); ?></a>
                <a tab="#widgets" class="htab"><?php echo $l('Widgets'); ?></a>
            </div>

            <div id="general"><?php require_once(dirname(__FILE__)."/product_form_general.tpl"); ?></div>
            <div id="data"><?php require_once(dirname(__FILE__)."/product_form_data.tpl"); ?></div>
            <div id="links"><?php require_once(dirname(__FILE__)."/product_form_links.tpl"); ?></div>
            <div id="discount"><?php require_once(dirname(__FILE__)."/product_form_discount.tpl"); ?></div>
            <div id="special"><?php require_once(dirname(__FILE__)."/product_form_special.tpl"); ?></div>
            <div id="option"><?php require_once(dirname(__FILE__)."/product_form_option.tpl"); ?></div>
            <div id="images"><?php require_once(dirname(__FILE__)."/product_form_images.tpl"); ?></div>
            <div id="attributes"><?php require_once(dirname(__FILE__)."/product_form_attributes.tpl"); ?></div>
            <div id="widgets"><?php require_once(dirname(__FILE__)."/product_form_widgets.tpl"); ?></div>

        </form>
    </div>
</div>
<script>
$(function() {
});
</script>
<?php echo $footer; ?>