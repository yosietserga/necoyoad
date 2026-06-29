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
        <div class="clear"></div>

        <ul>
            <?php foreach ($product_types as $type) { ?>
            <li dataproduct--type="<?php echo $type['type']; ?>">
                <img src="<?php echo HTTP_ADMIN_IMAGE . $type['icon']; ?>" />
                <?php echo $type['name']; ?>
            </li>
            <?php } ?>
        </ul>

        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">

        </form>
    </div>
</div>
<script>
$(function() {
    $('[data-product-type]').each(function(){
        $('#form').load(createAdminUrl('store/product/insertWizard', {
            step:'relations',
            type:$(this).data('product-type'),
            product_id:'<?php echo $product_id; ?>'
        }));
    });
});
</script>
<?php echo $footer; ?>