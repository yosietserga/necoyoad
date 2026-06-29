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
                <label><?php echo $l('entry_title'); ?></label>
                <input type="text" name="title" id="title" value="<?php echo $title ?? ""; ?>" placeholder="IVA" required="required" style="width:40%" />
            </div>
            
            <div class="clear"></div>
            
            <div class="row">
                <label><?php echo $l('entry_description'); ?></label>
                <input type="text" name="description" id="description" value="<?php echo $description ?? ""; ?>" placeholder="Impuesto al Valor Agregado" required="required" style="width:40%" />
            </div>
            
            <div class="clear"></div><br />
            
            <table id="tax_rate" class="list">
                <thead>
                    <tr>
                        <td><?php echo $l('entry_geo_zone'); ?></td>
                        <td><?php echo $l('entry_description'); ?></td>
                        <td><?php echo $l('entry_rate'); ?></td>
                        <td><?php echo $l('entry_priority'); ?></td>
                        <td></td>
                    </tr>
                </thead>
                <tbody>
                <?php if (isset($tax_rates) && is_array($tax_rates) && !empty($tax_rates)) { ?>
                <?php foreach ($tax_rates as $row => $tax_rate) { ?>
                    <tr id="_row<?php echo $row; ?>">
                        <td>
                            <select name="tax_rate[<?php echo $row; ?>][geo_zone_id]" id="geo_zone_id<?php echo $row; ?>">
                            <?php foreach ($geo_zones as $geo_zone) { ?>
                            <option value="<?php echo $geo_zone['geo_zone_id']; ?>"<?php if ($geo_zone['geo_zone_id'] == $tax_rate['geo_zone_id']) { ?> selected="selected"<?php } ?>><?php echo $geo_zone['name']; ?></option>
                            <?php } ?>
                          </select>
                        </td>
                        <td><input type="text" name="tax_rate[<?php echo $row; ?>][description]" value="<?php echo $tax_rate['description']; ?>" required="required" /></td>
                        <td><input type="text" name="tax_rate[<?php echo $row; ?>][rate]" value="<?php echo $tax_rate['rate']; ?>" required="required" /></td>
                        <td><input type="text" name="tax_rate[<?php echo $row; ?>][priority]" value="<?php echo $tax_rate['priority']; ?>" size="1" required="required" /></td>
                        <td><a onclick="$('#_row<?php echo $row; ?>').remove();" class="button"><?php echo $l('button_remove'); ?></a></td>
                  </tr>
                <?php } //end foreach ?>
                <?php } //end if ?>
                </tbody>
                <tfoot>
                  <tr>
                    <td colspan="4"></td>
                    <td><a title="" onclick="addRate();" class="button"><?php echo $l('button_add_rate'); ?></a></td>
                  </tr>
                </tfoot>
              </table>
        </form>
    </div>
</div>
<script type="text/javascript">
function addRate() {
    _row = ($('#tax_rate tbody tr:last-child').index() + 1);
	html = '<tr id="_row' + _row + '">';
	html += '<td class="left"><select name="tax_rate[' + _row + '][geo_zone_id]">';
    <?php foreach ($geo_zones as $geo_zone) { ?>
    html += '<option value="<?php echo $geo_zone["geo_zone_id"]; ?>"><?php echo $geo_zone["name"]; ?></option>';
    <?php } ?>
	html += '</select></td>';
	html += '<td><input type="text" name="tax_rate[' + _row + '][description]" value=""></td>';
	html += '<td><input type="text" name="tax_rate[' + _row + '][rate]" value=""></td>';
	html += '<td><input type="text" name="tax_rate[' + _row + '][priority]" value="" size="1"></td>';
	html += '<td><a onclick="$(\'#_row' + _row + '\').remove();" class="button"><?php echo $l('button_remove'); ?></a></td>';
	html += '</tr>';
	
	$('#tax_rate tbody').append(html);
}
</script>
<?php echo $footer; ?>