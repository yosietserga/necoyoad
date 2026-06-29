<div>
    
    <ul id="vtabs" class="vtabs">
        <?php foreach ($options as $_row => $option) { ?>
        <li><a data-target="#tab_option_<?php echo (int)$_row; ?>" id="option_<?php echo (int)$_row; ?>" onclick="showTab(this)">
        <?php foreach ($languages as $language) { ?><?php if ($language['language_id'] == $language_id) { ?><?php echo $option['language'][$language['language_id']]['name']; ?><?php } ?><?php } ?>
        <span title="Eliminar Opci&oacute;n" onclick="$('.vtabs_page').hide();$('.vtabs_page:first-child').show();$('#option_<?php echo (int)$_row; ?>').remove(); $('#tab_option_<?php echo (int)$_row; ?>').remove();" class="remove">&nbsp;</span>
        </a></li>
        <?php } ?>
        <li><a title="<?php echo $l('button_add_option'); ?>" id="option_add" onclick="addRow();"><?php echo $l('button_add_option'); ?>&nbsp;
        <span class="add">&nbsp;</span>
        </a></li>
    </ul>
    
    <div id="options">
    <?php foreach ($options as $_row => $option) { ?>
        <div id="tab_option_<?php echo (int)$_row; ?>" class="vtabs_page">
        <?php foreach ($languages as $language) { ?>
            <?php if ($language['language_id'] == $language_id) { ?>
            <h2><?php echo $option['language'][$language['language_id']]['name']; ?></h2><br />
            <?php } ?>
            
            <input showquick="off" title="<?php echo $l('help_option'); ?>" name="options[<?php echo (int)$_row; ?>][language][<?php echo $language['language_id']; ?>][name]" value="<?php echo $option['language'][$language['language_id']]['name']; ?>" placeholder="<?php echo $l('entry_option'); ?>"<?php if ($language['language_id'] == $language_id) { ?> onkeyup="$('#option_<?php echo (int)$_row; ?>').text(this.value);$('#tab_option_<?php echo (int)$_row; ?> h2').text(this.value);"<?php } ?> />
            <img src="images/flags/<?php echo $language['image']; ?>" title="<?php echo $language['name']; ?>" />
            <br /><br />
        <?php } ?>
            
            <table id="table_options_<?php echo (int)$_row; ?>" class="list">
                <thead>
                    <tr>
                        <th><?php echo $l('entry_option_value'); ?></th>
                        <th><?php echo $l('entry_quantity'); ?></th>
                        <th><?php echo $l('entry_price'); ?></th>
                        <th><?php echo $l('entry_prefix'); ?></th>
                        <th><?php echo $l('entry_subtract'); ?></th>
                        <th><?php echo $l('entry_sort_order'); ?></th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                <?php if ($option['option_value']) { ?>
                <?php foreach ($option['option_value'] as $option_value_row => $option_value) { ?>
                
                    <tr id="option_<?php echo (int)$_row; ?>_value_<?php echo (int)$option_value_row; ?>">
                    
                        <td>
                        <?php foreach ($languages as $language) { ?>
                            <input showquick="off" title="<?php echo $l('help_option_value'); ?>" type="text" name="options[<?php echo (int)$_row; ?>][option_value][<?php echo (int)$option_value_row; ?>][language][<?php echo $language['language_id']; ?>][name]" value="<?php echo $option_value['language'][$language['language_id']]['name']; ?>" />
                            <img src="images/flags/<?php echo $language['image']; ?>" title="<?php echo $language['name']; ?>" /><br />
                        <?php } ?>
                        </td>
                        
                        <td>
                            <input showquick="off" title="<?php echo $l('help_quantity'); ?>" type="necoNumber" name="options[<?php echo (int)$_row; ?>][option_value][<?php echo (int)$option_value_row; ?>][quantity]" value="<?php echo $option_value['quantity']; ?>" size="2" style="width:40px" />
                        </td>
                        
                        <td>
                            <input showquick="off" title="<?php echo $l('help_price'); ?>" type="text" name="options[<?php echo (int)$_row; ?>][option_value][<?php echo (int)$option_value_row; ?>][price]" value="<?php echo $option_value['price']; ?>" style="width:60px" />
                        </td>
                        
                        <td>
                            <select showquick="off" title="<?php echo $l('help_prefix'); ?>" name="options[<?php echo (int)$_row; ?>][option_value][<?php echo (int)$option_value_row; ?>][prefix]">
                                <option value="+"<?php if ($option_value['prefix'] != '-') { ?> selected="selected"<?php } ?>><?php echo $l('text_plus'); ?></option>
                                <option value="-"<?php if ($option_value['prefix'] == '-') { ?> selected="selected"<?php } ?>><?php echo $l('text_minus'); ?></option>
                            </select>
                        </td>
                        
                        <td>
                            <select showquick="off" title="<?php echo $l('help_subtract'); ?>" name="options[<?php echo (int)$_row; ?>][option_value][<?php echo (int)$option_value_row; ?>][subtract]">
                                <option value="1"<?php if ($option_value['subtract']) { ?> selected="selected"<?php } ?>><?php echo $l('text_yes'); ?></option>
                                <option value="0"<?php if (!$option_value['subtract']) { ?> selected="selected"<?php } ?>><?php echo $l('text_no'); ?></option>
                            </select>
                        </td>
                        
                        <td>
                            <input showquick="off" title="<?php echo $l('help_sort_order'); ?>" type="necoNumber" name="options[<?php echo (int)$_row; ?>][option_value][<?php echo (int)$option_value_row; ?>][sort_order]" value="<?php echo $option_value['sort_order']; ?>" size="2" style="width:40px" />
                        </td>
                        
                        <td>
                            <a onclick="$('#option_<?php echo (int)$_row; ?>_value_<?php echo (int)$option_value_row; ?>').remove();" class="button"><?php echo $l('button_remove'); ?></a>
                        </td>
                        
                    </tr>
                    
                <?php } ?>
                <?php } ?>
                </tbody>
                
                <tfoot>
                    <tr>
                        <td colspan="6"></td>
                        <td><a onclick="addValue(<?php echo (int)$_row; ?>);" class="button"><?php echo $l('button_add_option_value'); ?></a></td>
                    </tr>
                </tfoot>
                
            </table>
                
        </div>
        <?php } ?>
    </div>
</div>
<script type="text/javascript">
function addRow() {
    var _row = $('.vtabs_page:last-child').index() + 1 * 1;
    
    var html  = '<div id="tab_option_'+ _row +'" class="vtabs_page">';
    
    html += '<h2>Opci\u00F3n '+ _row +'</h2>';
    html += '<br />';
    
    <?php foreach ($languages as $language) { ?>
	html += '<input title="<?php echo $l('help_option'); ?>" name="options['+ _row +'][language][<?php echo $language['language_id']; ?>][name]" value="" placeholder="<?php echo $l('entry_option'); ?>"<?php if ($language['language_id'] == $language_id) { ?> onkeyup="$(\'#option_'+ _row +'\').text(this.value);$(\'#tab_option_'+ _row +' h2\').text(this.value);"<?php } ?> />';
	html += '<img src="images/flags/<?php echo $language['image']; ?>" title="<?php echo $language['name']; ?>" />';
    html += '<br />';
    html += '<br />';
    <?php } ?>
    
	html += '<table id="table_options_'+ _row +'" class="list">';
    
	html += '<thead>';
	html += '<tr>';
	html += '<th><?php echo $l('entry_option_value'); ?></th>';
	html += '<th><?php echo $l('entry_quantity'); ?></th>';
	html += '<th><?php echo $l('entry_price'); ?></th>';
	html += '<th><?php echo $l('entry_prefix'); ?></th>';
	html += '<th><?php echo $l('entry_subtract'); ?></th>';
	html += '<th><?php echo $l('entry_sort_order'); ?></th>';
	html += '<th>&nbsp;</th>';
	html += '</tr>';
	html += '</thead>';
    
	html += '<tbody></tbody>';
    
	html += '<tfoot>';
	html += '<tr>';
	html += '<td colspan="6"></td>';
	html += '<td><a onclick="addValue('+ _row +');" class="button"><?php echo $l('button_add_option_value'); ?></a></td>';
	html += '</tr>';
	html += '</tfoot>';
    
	html += '</table>';
    
	html += '</div>';     
            
	$('#options').append(html);
	
    li = '<li>';
    li += '<a data-target="#tab_option_'+ _row +'" id="option_'+ _row +'" onclick="showTab(this)">';
    li += '<?php echo $l('tab_option'); ?> '+ _row;
    li += '<span title="Eliminar Opci&oacute;n" onclick="$(\'.vtabs_page\').hide();$(\'.vtabs_page:first-child\').show();$(\'#option_'+ _row +'\').remove(); $(\'#tab_option_'+ _row +'\').remove();" class="remove">&nbsp;</span>';
    li += '</a>';
    li += '</li>';
    
    $('#option_add').before(li);
    $('.vtabs_page').hide();
    $('#tab_option_'+ _row).show();
}

function addValue(_row) {
    
    if (typeof _row == 'undefined') {
        var _row = $('.vtabs_page:last-child').index() + 1 * 1;
    }
    
    var _value_row = $('#table_options_'+ _row +' tbody tr:last-child').index() + 1 * 1;
    
    if (!_value_row) {
        _value_row = 0;
    }
    
	var html = '<tr id="option_'+ _row +'_value_'+ _value_row +'">'; 
    
    html += '<td>';
    <?php foreach ($languages as $language) { ?>
    html += '<input title="<?php echo $l('help_option_value'); ?>" type="text" name="options['+ _row +'][option_value]['+ _value_row +'][language][<?php echo $language['language_id']; ?>][name]" value="" placeholder="<?php echo $l('entry_option_value'); ?>" />';
    html += '<img src="images/flags/<?php echo $language['image']; ?>" title="<?php echo $language['name']; ?>" /><br />';
    html += '<div class="clear"></div>';
    <?php } ?>
    html += '</td>';
                    
    html += '<td>';
    html += '<input title="<?php echo $l('help_quantity'); ?>" type="necoNumber" name="options['+ _row +'][option_value]['+ _value_row +'][quantity]" value="" size="2" style="width:40px" />';
    html += '</td>';
    
    html += '<td>';
    html += '<input title="<?php echo $l('help_price'); ?>" type="text" name="options['+ _row +'][option_value]['+ _value_row +'][price]" value="" style="width:60px" />';
    html += '</td>';
    
    html += '<td>';
    html += '<select title="<?php echo $l('help_prefix'); ?>" name="options['+ _row +'][option_value]['+ _value_row +'][prefix]">';
    html += '<option value="+"><?php echo $l('text_plus'); ?></option>';
    html += '<option value="-"><?php echo $l('text_minus'); ?></option>';
    html += '</select>';
    html += '</td>';
    
    html += '<td>';
    html += '<select title="<?php echo $l('help_subtract'); ?>" name="options['+ _row +'][option_value]['+ _value_row +'][subtract]">';
    html += '<option value="1"><?php echo $l('text_yes'); ?></option>';
    html += '<option value="0"><?php echo $l('text_no'); ?></option>';
    html += '</select>';
    html += '</td>';
    
    html += '<td>';
    html += '<input title="<?php echo $l('help_sort_order'); ?>" type="necoNumber" name="options['+ _row +'][option_value]['+ _value_row +'][sort_order]" value="" size="2" style="width:40px" />';
    html += '</td>';
    
    html += '<td>';
    html += '<a onclick="$(\'#option_'+ _row +'_value_'+ _value_row +'\').remove();" class="button"><?php echo $l('button_remove'); ?></a>';
    html += '</td>';

	html += '</tr>';
	
	$('#table_options_'+ _row +' tbody').append(html);
}
</script>