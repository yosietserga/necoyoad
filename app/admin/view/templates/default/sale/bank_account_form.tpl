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
                <label><?php echo $l('entry_number'); ?></label>
                <input id="number" name="number" value="<?php echo isset($number) ? $number : ''; ?>" required="true" style="width:40%" />
            </div>
                        
            <div class="clear"></div>
                  
            <div class="row">
                <label><?php echo $l('entry_accountholder'); ?></label>
                <input id="accountholder" name="accountholder" value="<?php echo isset($accountholder) ? $accountholder : ''; ?>" required="true" style="width:40%" />
            </div>
                        
            <div class="clear"></div>
                  
            <div class="row">
                <label><?php echo $l('entry_bank'); ?></label>
    			<select name="bank_id">
    			<?php foreach ($banks as $bank) { ?>
                    <option value="<?php echo $bank['bank_id']; ?>"<?php if ($bank['bank_id']==$bank_id) { ?> selected="selected"<?php } ?>><?php echo $bank['name']; ?></option>
    			<?php } ?>
    			</select>
            </div>
                        
            <div class="clear"></div>
                  
            <div class="row">
                <label><?php echo $l('entry_email'); ?></label>
                <input type="email" id="email" name="email" value="<?php echo isset($email) ? $email : ''; ?>" required="true" style="width:40%" />
            </div>
                        
            <div class="clear"></div>
                  
            <div class="row">
                <label><?php echo $l('entry_rif'); ?></label>
                <input id="rif" name="rif" value="<?php echo isset($rif) ? $rif : ''; ?>" required="true" style="width:40%" />
            </div>
                        
            <div class="clear"></div>
                  
            <div class="row">
                <label><?php echo $l('entry_type'); ?></label>
    			<select name="type">
    			     <option value="Cuenta de Ahorro"<?php if ("Cuenta de Ahorro"==$type) { ?> selected="selected"<?php } ?>>Cuenta de Ahorro</option>
    			     <option value="Cuenta Corriente"<?php if ("Cuenta Corriente"==$type) { ?> selected="selected"<?php } ?>>Cuenta Corriente</option>
    			     <option value="Cuenta Completa"<?php if ("Cuenta Completa"==$type) { ?> selected="selected"<?php } ?>>Cuenta Completa</option>
    			     <option value="Cuenta Absoluta"<?php if ("Cuenta Absoluta"==$type) { ?> selected="selected"<?php } ?>>Cuenta Absoluta</option>
    			     <option value="Cuenta Privada"<?php if ("Cuenta Privada"==$type) { ?> selected="selected"<?php } ?>>Cuenta Privada</option>
    			</select>
            </div>
                      
            <?php if ($stores) { ?>
            <div class="clear"></div>
            <div class="row">
                <label><?php echo $l('entry_store'); ?></label><br />
                <input type="text" title="Filtrar listado de tiendas y sucursales" value="" name="q" id="q" placeholder="Filtrar Tiendas" />
                <div class="clear"></div>
                <a onclick="$('#storesWrapper input[type=checkbox]').attr('checked','checked');">Seleccionar Todos</a>&nbsp;&nbsp;&nbsp;&nbsp;
                <a onclick="$('#storesWrapper input[type=checkbox]').removeAttr('checked');">Seleccionar Ninguno</a>
                <div class="clear"></div>
                <ul id="storesWrapper" class="scrollbox" data-scrollbox="1">
                    <li class="stores">
                        <input id="scrollboxStores0" type="checkbox" name="stores[]" value="0"<?php if (in_array(0, $_stores)) { ?> checked="checked"<?php } ?> showquick="off" />
                        <label for="scrollboxStores0"><?php echo $l('text_default'); ?></label>
                        <div class="clear"></div>
                    </li>
                <?php foreach ($stores as $store) { ?>
                    <li class="stores">
                        <input id="scrollboxStores<?php echo (int)$store['store_id']; ?>" type="checkbox" name="stores[]" value="<?php echo (int)$store['store_id']; ?>"<?php if (in_array($store['store_id'], $_stores)) { ?> checked="checked"<?php } ?> showquick="off" />
                        <label for="scrollboxStores<?php echo (int)$store['store_id']; ?>"><?php echo $store['name']; ?></label>
                        <div class="clear"></div>
                    </li>
                <?php } ?>
                </ul>
            </div> 
            <?php } else { ?>
                <input type="hidden" name="stores[]" value="0" />
            <?php } ?>
                
            <div class="clear"></div><br />
        </form>
    </div>
</div>
<?php echo $footer; ?>