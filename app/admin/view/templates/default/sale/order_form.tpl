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
            <a onclick="saveAndExit();$('#form').submit();" class="button"><?php echo $l('button_save_and_exit'); ?></a>
            <a onclick="saveAndKeep();$('#form').submit();" class="button"><?php echo $l('button_save_and_keep'); ?></a>
            <a onclick="saveAndNew();$('#form').submit();" class="button"><?php echo $l('button_save_and_new'); ?></a>
            <a onclick="window.open('<?php echo $invoice; ?>');" class="button"><?php echo $l('button_invoice'); ?></a>
            <a onclick="location = '<?php echo $cancel; ?>';" class="button"><?php echo $l('button_cancel'); ?></a>
        </div>

        <div class="clear"></div>

        <ul id="vtabs" class="vtabs">
            <li><a data-target="#tab_order" onclick="showTab(this)"><?php echo $l('tab_order'); ?></a></li>
            <li><a data-target="#tab_product" onclick="showTab(this)"><?php echo $l('tab_product'); ?></a></li>
            <li><a data-target="#tab_shipping" onclick="showTab(this)"><?php echo $l('tab_shipping'); ?></a></li>
            <li><a data-target="#tab_payment" onclick="showTab(this)"><?php echo $l('tab_payment'); ?></a></li>
            <li><a data-target="#tab_history" onclick="showTab(this)"><?php echo $l('tab_history'); ?></a></li>
        </ul>

        <form action="<?php echo str_replace('&', '&amp;', $action); ?>" method="post" enctype="multipart/form-data" id="form">

            <div class="vtabs_page" id="tab_order"><?php require_once(dirname(__FILE__)."/order_form_order.tpl"); ?></div>
            <div class="vtabs_page" id="tab_product"><?php require_once(dirname(__FILE__)."/order_form_product.tpl"); ?></div>
            <div class="vtabs_page" id="tab_shipping"><?php require_once(dirname(__FILE__)."/order_form_shipping.tpl"); ?></div>
            <div class="vtabs_page" id="tab_payment"><?php require_once(dirname(__FILE__)."/order_form_payment.tpl"); ?></div>
            <div class="vtabs_page" id="tab_history"><?php require_once(dirname(__FILE__)."/order_form_history.tpl"); ?></div>

        </form>
    </div>
</div>

<script>
$(function() {
    var height = $(window).height() * 0.8;
    var width = $(window).width() * 0.8;

    $(".lightbox").fancybox({
        maxWidth	: width,
        maxHeight	: height,
        fitToView	: false,
        width	: '90%',
        height	: '90%',
        autoSize	: false,
        closeClick	: false,
        openEffect	: 'none',
        closeEffect	: 'none'
    });
});
</script>
<?php echo $footer; ?>