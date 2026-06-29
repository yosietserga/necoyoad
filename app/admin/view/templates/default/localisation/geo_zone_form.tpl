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
                <input type="text" name="name" id="name" value="<?php echo $name; ?>" placeholder="Venezuela" required="required" style="width:40%" />
            </div>
            
            <div class="clear"></div>
            
            <div class="row">
                <label><?php echo $l('entry_description'); ?></label>
                <input type="text" name="description" id="description" value="<?php echo $description; ?>" placeholder="Alguna Descripci&oacute;n" required="required" style="width:40%" />
            </div>
            
            <div class="clear"></div><br />
            
            <table id="zone_to_geo_zone" class="list">
                <thead>
                    <tr>
                        <th><?php echo $l('entry_country'); ?></th>
                        <th><?php echo $l('entry_zone'); ?></th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($zone_to_geo_zones as $row => $zone_to_geo_zone) { ?>
                    <tr id="_row<?php echo $row; ?>">
                        <td>
                            <select name="zone_to_geo_zone[<?php echo $row; ?>][country_id]" id="country<?php echo $row; ?>" onchange="$('#zone<?php echo $row; ?>').load('<?php echo $Url::createAdminUrl("localisation/geo_zone/zone"); ?>&country_id=' + this.value + '&zone_id=0');">
                            <?php foreach ($countries as $country) { ?>
                            <option value="<?php echo $country['country_id']; ?>"<?php if ($country['country_id'] == $zone_to_geo_zone['country_id']) { ?> selected="selected"<?php } ?>><?php echo $country['name']; ?></option>
                            <?php } ?>
                            </select>
                        </td>
                        <td><select name="zone_to_geo_zone[<?php echo $row; ?>][zone_id]" id="zone<?php echo $row; ?>"></select></td>
                        <td><a onclick="$('#_row<?php echo $row; ?>').remove();" class="button"><?php echo $l('button_remove'); ?></a></td>
                    </tr>
                <?php } ?>
                </tbody>
                <tfoot>
                  <tr>
                    <td colspan="2"></td>
                    <td><a onclick="add();" class="button"><?php echo $l('button_add_geo_zone'); ?></a></td>
                  </tr>
                </tfoot>
            </table>
      
            <div class="clear"></div><br />
            
        </form>
    </div>
</div>
<script type="text/javascript">
$(function() {
    <?php foreach ($zone_to_geo_zones as $row => $zone_to_geo_zone) { ?>
    $('#zone<?php echo $row; ?>').load('<?php echo $Url::createAdminUrl("localisation/geo_zone/zone") 
    ."&country_id=". $zone_to_geo_zone['country_id'] 
    ."&zone_id=". $zone_to_geo_zone['zone_id']; ?>');
    <?php } ?>
});
function add() {
    _row = ($('#zone_to_geo_zone tbody tr:last-child').index() + 1);
	html  = '<tr id="_row'+ _row +'">';
	html += '<td><select name="zone_to_geo_zone[' + _row + '][country_id]" id="country'+ _row +'" onchange="$(\'#zone'+ _row +'\').load(\'<?php echo $Url::createAdminUrl("localisation/geo_zone/zone"); ?>&country_id=\' + this.value + \'&zone_id=0\');">';
	<?php foreach ($countries as $country) { ?>
	html += '<option value="<?php echo $country['country_id']; ?>"><?php echo addslashes($country['name']); ?></option>';
	<?php } ?>   
	html += '</select></td>';
	html += '<td><select name="zone_to_geo_zone['+ _row +'][zone_id]" id="zone'+ _row +'"></select></td>';
	html += '<td><a title="" onclick="$(\'#zone_to_geo_zone_row'+ _row +'\').remove();" class="button"><?php echo $l('button_remove'); ?></a></td>';
	html += '</tr>';
	
	$('#zone_to_geo_zone tbody').append(html);
		
	$('#zone' + _row).load('<?php echo $Url::createAdminUrl("localisation/geo_zone/zone"); ?>&country_id=' + $('#country' + _row).attr('value') + '&zone_id=0');
}
</script>
<?php echo $footer; ?>