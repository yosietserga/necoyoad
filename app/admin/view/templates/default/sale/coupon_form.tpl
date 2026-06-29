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

            <div id="languages" class="htabs">
            <?php foreach ($languages as $language) { ?>
                <a tab="#language<?php echo $language['language_id']; ?>" class="htab"><img src="images/flags/<?php echo $language['image']; ?>" title="<?php echo $language['name']; ?>" /> <?php echo $language['name']; ?></a>
            <?php } ?>
            </div> 

            <?php foreach ($languages as $language) { ?>
            <div id="language<?php echo $language['language_id']; ?>">

                <div class="row">
                    <label><?php echo $l('entry_name'); ?></label>
                    <input id="coupon_description<?php echo $language['language_id']; ?>_name" name="coupon_description[<?php echo $language['language_id']; ?>][title]" value="<?php echo isset($coupon_description[$language['language_id']]) ? $coupon_description[$language['language_id']]['title'] : ''; ?>" required="true" title="<?php echo $l('help_name'); ?>" />
                </div>

                <div class="clear"></div>

                <div class="row">
                    <label><?php echo $l('entry_description'); ?></label>
                    <textarea title="<?php echo $l('help_description'); ?>" name="coupon_description[<?php echo $language['language_id']; ?>][description]" id="description<?php echo $language['language_id']; ?>"><?php echo isset($coupon_description[$language['language_id']]) ? $coupon_description[$language['language_id']]['description'] : ''; ?></textarea>
                </div>

            </div>
            <?php } ?>

            <div class="clear"></div>

            <div class="row">
                <label><?php echo $l('entry_code'); ?></label>
                <input id="code" name="code" value="<?php echo isset($code) ? $code : ''; ?>" required="true" title="<?php echo $l('help_code'); ?>" />
            </div>

            <div class="row">
                <label><?php echo $l('entry_type'); ?></label>
                <select title="<?php echo $l('help_type'); ?>" name="type">
                  <option value="P"<?php if ($type == 'P') { ?> selected="selected"<?php } ?>><?php echo $l('text_percent'); ?></option>
                  <option value="F"<?php if ($type == 'F') { ?> selected="selected"<?php } ?>><?php echo $l('text_amount'); ?></option>
                </select>
            </div>

            <div class="row">
                <label><?php echo $l('entry_discount'); ?></label>
                <input id="discount" name="discount" value="<?php echo isset($discount) ? $discount : ''; ?>" required="true" title="<?php echo $l('help_discount'); ?>" />
            </div>

            <div class="row">
                <label><?php echo $l('entry_total'); ?></label>
                <input type="necoNumber" id="total" name="total" value="<?php echo isset($total) ? $total : ''; ?>" required="true" title="<?php echo $l('help_total'); ?>" />
            </div>

            <div class="row">
                <label><?php echo $l('entry_logged'); ?></label>
                <input type="checkbox" id="logged" name="logged" value="1" title="<?php echo $l('help_logged'); ?>" showquick="off"<?php if ($logged) { ?> checked="checked"<?php } ?> />
            </div>

            <div class="row">
                <label><?php echo $l('entry_shipping'); ?></label>
                <input type="checkbox" id="shipping" name="shipping" value="1" title="<?php echo $l('help_shipping'); ?>" showquick="off"<?php if ($shipping) { ?> checked="checked"<?php } ?> />
            </div>

            <div class="clear"></div>

            <div class="row">
                <label><?php echo $l('entry_date_start'); ?></label>
                <input type="necoDate" title="<?php echo $l('help_date_start'); ?>" name="date_start" value="<?php echo $date_start; ?>" size="12" />
            </div>

            <div class="clear"></div>

            <div class="row">
                <label><?php echo $l('entry_date_end'); ?></label>
                <input type="necoDate" title="<?php echo $l('help_date_end'); ?>" name="date_end" value="<?php echo $date_end; ?>" size="12" />
            </div>

            <div class="clear"></div>

            <div class="row">
                <label><?php echo $l('entry_uses_total'); ?></label>
                <input type="necoNumber" title="<?php echo $l('help_uses_total'); ?>" name="uses_total" value="<?php echo $uses_total; ?>" />
            </div>

            <div class="clear"></div>

            <div class="row">
                <label><?php echo $l('entry_uses_customer'); ?></label>
                <input type="necoNumber" title="<?php echo $l('help_uses_customer'); ?>" name="uses_customer" value="<?php echo $uses_customer; ?>" />
            </div>

            <div class="clear"></div>

            <div class="row">
                <label><?php echo $l('entry_status'); ?></label>
                <select title="<?php echo $l('help_status'); ?>" name="status">
                    <option value="1"<?php if ($status) { ?> selected="selected"<?php } ?>><?php echo $l('text_enabled'); ?></option>
                    <option value="0"<?php if (!$status) { ?> selected="selected"<?php } ?>><?php echo $l('text_disabled'); ?></option>
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

            <div id="addsPanel"><b>Agregar / Eliminar Productos</b></div>
            <div id="addsWrapper"><div id="gridPreloader"></div></div>
        </form>
    </div>
</div>
<?php echo $footer; ?>