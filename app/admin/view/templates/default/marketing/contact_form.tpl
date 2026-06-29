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
        <h1><?php echo $heading_title; ?></h1>
        <div class="buttons">
            <a onclick="saveAndExit();$('#form').submit();" class="button"><?php echo $l('button_save_and_exit'); ?></a>
            <a onclick="saveAndKeep();$('#form').submit();" class="button"><?php echo $l('button_save_and_keep'); ?></a>
            <a onclick="saveAndNew();$('#form').submit();" class="button"><?php echo $l('button_save_and_new'); ?></a>
            <a onclick="location = '<?php echo $cancel; ?>';" class="button"><?php echo $l('button_cancel'); ?></a>
        </div>
        
        <div class="clear"></div>
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
        
            <input type="hidden" name="customer_id" id="customer_id" value="<?php echo ($customer_id) ? $customer_id : ''; ?>" />
            
            <div class="row">
                <label>Email</label>
                <!-- <select id="_email" showquick="off"></select> -->
                <input type="email" name="email" id="email" value="<?php echo ($email) ? $email : ''; ?>" />
            </div>
            
            <div class="clear"></div>
            
            <div class="row">
                <label>Nombre Completo</label>
                <input type="text" name="name" id="name" value="<?php echo ($name) ? $name : ''; ?>" required="required" />
            </div>
            
            <div class="clear"></div>
            
            <div class="row">
                <label>Agregar a Lista de Contactos:</label>
                <?php if ($lists) { ?>
                <input type="text" title="Filtrar listas de contactos" value="" name="q" id="q" placeholder="Filtrar Listas de Contactos" />
                <div class="clear"></div>
                <label>&nbsp;</label>
                <ul id="contactsWrapper" class="scrollbox" data-scrollbox="1">
                <?php foreach ($lists as $list) { ?>
                    <li>
                        <input id="contact_id_<?php echo $list['contact_list_id']; ?>" title="<?php echo $l('help_contact'); ?>" type="checkbox" name="contact_list[]" value="<?php echo $list['contact_list_id']; ?>"<?php if (in_array($list['contact_list_id'], $contact_lists)) { ?> checked="checked"<?php } ?> showquick="off" />
                        <label for="contact_id_<?php echo $list['contact_list_id']; ?>"><?php echo $list['name']; ?></label>
                    </li>
                <?php } ?>
                </ul>
                <?php } else { ?>
                No hay listas de contactos registradas
                <?php } ?>
            </div>
        </form>
    </div>
</div>

<script>
(function($){ $(window).load(function(){ 
    $('.ui-combobox-input').val('<?php echo $email; ?>').on('change',function(e){
        $('#email').val(this.value);
    });
});})(jQuery);
</script>
<?php echo $footer; ?>