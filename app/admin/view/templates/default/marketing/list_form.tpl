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
            <a onclick="saveAndExit();" class="button"><?php echo $l('button_save_and_exit'); ?></a>
            <a onclick="saveAndKeep();" class="button"><?php echo $l('button_save_and_keep'); ?></a>
            <a onclick="saveAndNew();" class="button"><?php echo $l('button_save_and_new'); ?></a>
            <a onclick="location = '<?php echo $cancel; ?>';" class="button"><?php echo $l('button_cancel'); ?></a>
        </div>
        
        <div class="clear"></div>
                                
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
            <div class="row">
                <label><?php echo $l('entry_name'); ?></label>
                <input id="name" name="name" value="<?php echo isset($name) ? $name : ''; ?>" required="true" style="width:40%" />
                <?php if ($error_name) {echo $error_name;} ?>
            </div>
                        
            <div class="clear"></div>
                        
            <div class="row">
                <label><?php echo $l('entry_description'); ?></label>
                <textarea id="description" name="description" style="width:40%"><?php echo isset($description) ? $description : ''; ?></textarea>
            </div>
            
            <div class="clear"></div>
            
            <div class="row">
                <label><?php echo $l('entry_contact'); ?></label>
                <?php if ($contacts) { ?>
                <input type="text" title="Filtrar listado de categor&iacute;as" value="" name="q" id="q" />
                <div class="clear"></div>
                <label>&nbsp;</label>
                <div class="clear"></div>
                <a onclick="$('#contactsWrapper input[type=checkbox]').attr('checked','checked');">Seleccionar Todos</a>&nbsp;&nbsp;&nbsp;&nbsp;
                <a onclick="$('#contactsWrapper input[type=checkbox]').removeAttr('checked');">Seleccionar Ninguno</a>
                <div class="clear"></div>
                <ul id="contactsWrapper" class="scrollbox" data-scrollbox="1">
                <?php foreach ($contacts as $contact) { ?>
                    <li>
                        <input title="<?php echo $l('help_contact'); ?>" type="checkbox" name="contact_list[]" value="<?php echo $contact['contact_id']; ?>"<?php if (in_array($contact['contact_id'], $contacts_list)) { ?> checked="checked"<?php } ?> showquick="off" />
                        <b><?php echo $contact['name']; ?>&nbsp;&nbsp;(&nbsp;<?php echo $contact['mail']; ?>&nbsp;)</b>
                    </li>
                <?php } ?>
                </ul>
                <?php } else { ?>
                No hay contactos registrados
                <?php } ?>
            </div>
                   
            <div class="clear"></div><br />
            
        </form>
    </div>
</div>
<?php echo $footer; ?>