<h2>General</h2>
<table class="form">

            <?php if ($stores) { ?>
            <div class="clear"></div>
            <div class="row">
                <label><?php echo $l('entry_store'); ?></label><br />
                <input type="text" title="Filtrar listado de tiendas y sucursales" value="" name="q" id="q" placeholder="Filtrar Tiendas" />
                <div class="clear"></div>
                <a onclick="$('input[name@=stores]').attr('checked','checked');">Seleccionar Todos</a>&nbsp;&nbsp;&nbsp;&nbsp;
                <a onclick="$('input[name@=stores]').removeAttr('checked');">Seleccionar Ninguno</a>
                <div class="clear"></div><br />
                        
                <ul id="storesWrapper" class="scrollbox" data-scrollbox="1">
                <?php foreach ($stores as $store) { ?>
                    <li class="stores">
                        <input type="checkbox" name="stores[]" value="<?php echo $store['store_id']; ?>"<?php if (in_array($store['store_id'], $_stores)) { ?> checked="checked"<?php } ?> showquick="off" />
                        <label for="scrollboxStores<?php echo (int)$store['store_id']; ?>"><?php echo $store['name']; ?></label>
                    </li>
                <?php } ?>
                </ul>
            </div>
            <?php } ?>    
                   
	<tr>
		<td><label><?php echo $l('entry_rif'); ?></label></td>
		<td><input title="<?php echo $l('help_rif'); ?>" type="text" name="rif" id="rif" value="<?php echo $rif; ?>" /></td>
	</tr>
	<tr>
		<td><label><?php echo $l('entry_company'); ?></label></td>
		<td><input title="<?php echo $l('help_company'); ?>" type="text" name="company" id="company" value="<?php echo $company; ?>" /></td>
	</tr>
	<tr>
		<td><label><?php echo $l('entry_firstname'); ?></label></td>
		<td><input title="<?php echo $l('help_firstname'); ?>" type="text" name="firstname" id="firstname" value="<?php echo $firstname; ?>" /></td>
	</tr>
	<tr>
		<td><label><?php echo $l('entry_lastname'); ?></label></td>
		<td><input title="<?php echo $l('help_lastname'); ?>" name="lastname" value="<?php echo $lastname; ?>" /></td>
	</tr>
	<tr>
		<td><label><?php echo $l('entry_email'); ?></label></td>
		<td><input title="<?php echo $l('help_email'); ?>" type="email" name="email" value="<?php echo $email; ?>" /></td>
	</tr>
	<tr>
		<td><label><?php echo $l('entry_sexo'); ?></label></td>
		<td>
            <select name="sexo" title="<?php echo $l('help_sexo'); ?>">
                <option value=""><?php echo $l('text_sexo'); ?></option>
				<option value="m"<?php if ($sexo=='m') { ?> selected="selected"<?php } ?>><?php echo $l('text_man'); ?></option>
				<option value="f"<?php if ($sexo=='f') { ?> selected="selected"<?php } ?>><?php echo $l('text_woman'); ?></option>		
			</select>
        </td>
	</tr>
	<tr>
		<td><label><?php echo $l('entry_telephone'); ?></label></td>
		<td><input title="<?php echo $l('help_telephone'); ?>" name="telephone" value="<?php echo $telephone; ?>" /></td>
	</tr>
	<tr>
		<td><label><?php echo $l('entry_customer_group'); ?></label></td>
		<td>
			<select name="customer_group_id">
			<?php foreach ($customer_groups as $customer_group) { ?>
                <option value="<?php echo $customer_group['customer_group_id']; ?>"<?php if ($customer_group[ 'customer_group_id']==$customer_group_id) { ?> selected="selected"<?php } ?>><?php echo $customer_group[ 'name']; ?></option>
			<?php } ?>
			</select>
		</td>
	</tr>
</table>