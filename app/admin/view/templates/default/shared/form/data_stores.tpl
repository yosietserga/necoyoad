<div class="clear"></div>

<?php if (!isset($object_id)) throw new Exception("Must declare variable object_id before include stores form partial"); ?>
<?php if (isset($stores) && is_array($stores) && !empty($stores)) { ?>
<div class="clear"></div>
<div class="row">
    <label><?php echo $l('entry_store'); ?></label><br />
    <div class="clear"></div>
    <input type="text" title="Filtrar listado de tiendas y sucursales" value="" name="q" id="q" placeholder="Filtrar Tiendas" />
    <div class="clear"></div>
    <a onclick="$('#storesWrapper input[type=checkbox]').attr('checked','checked');">Seleccionar Todos</a>&nbsp;&nbsp;&nbsp;&nbsp;
    <a onclick="$('#storesWrapper input[type=checkbox]').removeAttr('checked');">Seleccionar Ninguno</a>
    <div class="clear"></div>
    <ul id="storesWrapper" class="scrollbox" data-scrollbox="1">
        <li class="stores">
            <input id="scrollboxStores0" type="checkbox" name="stores[]" value="0"<?php if (in_array(0, (array)$_stores)) { ?> checked="checked"<?php } ?> showquick="off" />
            <label for="scrollboxStores0"><?php echo $l('text_default'); ?></label>
            <div class="clear"></div>
        </li>
    <?php foreach ($stores as $store) { ?>
        <li class="stores">
            <input id="scrollboxStores<?php echo (int)$store['store_id']; ?>" type="checkbox" name="stores[]" value="<?php echo (int)$store['store_id']; ?>"<?php if (in_array($store['store_id'], (array)$_stores)) { ?> checked="checked"<?php } ?> showquick="off" />
            <label for="scrollboxStores<?php echo (int)$store['store_id']; ?>"><?php echo $store['name']; ?></label>
            <div class="clear"></div>
        </li>
    <?php } ?>
    </ul>
</div> 
<?php } else { ?>
    <input type="hidden" name="stores[]" value="0" />
<?php } ?>