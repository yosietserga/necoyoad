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
            <a onclick="location = '<?php echo $cancel; ?>';" class="button"><?php echo $l('button_cancel'); ?></a>
        </div>

        <div class="clear"></div>

        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">

            <div class="row">
                <label><?php echo $l('entry_name'); ?></label>
                <input type="text" title="<?php echo $l('help_name'); ?>" name="name" value="<?php echo $name; ?>" required="true" style="width:40%" />
            </div>

            <div class="clear"></div>

            <div class="row">
                <label>Cant. de Pedidos Mensuales</label>
                <input type="necoNumber" title="<?php echo $l('help_qty_orders'); ?>" name="Params[qty_orders]" value="<?php echo ($params['qty_orders']) ? $params['qty_orders'] : 0; ?>" />
            </div>

            <div class="clear"></div>

            <div class="row">
                <label>Cant. de Compras Mensuales</label>
                <input type="necoNumber" title="<?php echo $l('help_qty_invoices'); ?>" name="Params[qty_invoices]" value="<?php echo ($params['qty_invoices']) ? $params['qty_invoices'] : 0; ?>" />
            </div>

            <div class="clear"></div>

            <div class="row">
                <label>Cant. de Comentarios Mensuales</label>
                <input type="necoNumber" title="<?php echo $l('help_qty_reviews'); ?>" name="Params[qty_reviews]" value="<?php echo ($params['qty_reviews']) ? $params['qty_reviews'] : 0; ?>" />
            </div>

            <div class="clear"></div>

            <div class="row">
                <label>Cant. de Referencias Mensuales</label>
                <input type="necoNumber" title="<?php echo $l('help_qty_references'); ?>" name="Params[qty_references]" value="<?php echo ($params['qty_references']) ? $params['qty_references'] : 0; ?>" />
            </div>

        </form>
    </div>
</div>
<?php echo $footer; ?>