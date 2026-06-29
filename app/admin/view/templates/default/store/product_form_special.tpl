<div>
    <table id="special" class="list">
        <thead>
            <tr>
                <th><?php echo $l('entry_customer_group'); ?></th>
                <th><?php echo $l('entry_priority'); ?></th>
                <th><?php echo $l('entry_price'); ?></th>
                <th><?php echo $l('entry_date_start'); ?></th>
                <th><?php echo $l('entry_date_end'); ?></th>
                <th></th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($specials as $special_row => $special) { ?>
            <tr id="special_row<?php echo $special_row; ?>" class="special_row">
                <td>
                    <select name="specials[<?php echo $special_row; ?>][customer_group_id]">
                    <?php foreach ($customerGroups as $customer_group) { ?>
                        <?php if ($customer_group['customer_group_id'] == $special['customer_group_id']) { ?>
                        <option value="<?php echo $customer_group['customer_group_id']; ?>" selected="selected"><?php echo $customer_group['name']; ?></option>
                        <?php } else { ?>
                        <option value="<?php echo $customer_group['customer_group_id']; ?>"><?php echo $customer_group['name']; ?></option>
                        <?php } ?>
                    <?php } ?>
                    </select>
                </td>
                <td><input type="text" name="specials[<?php echo $special_row; ?>][priority]" value="<?php echo $special['priority']; ?>" size="2" showquick="off" /></td>
                <td><input type="text" name="specials[<?php echo $special_row; ?>][price]" value="<?php echo $special['price']; ?>" showquick="off" /></td>
                <td><input type="text" name="specials[<?php echo $special_row; ?>][date_start]" value="<?php list($y, $m, $d) = explode('-', $special['date_start']); echo $d.'/'.$m.'/'.$y; ?>" class="date" showquick="off" /></td>
                <td><input type="text" name="specials[<?php echo $special_row; ?>][date_end]" value="<?php list($y, $m, $d) = explode('-', $special['date_end']); echo ((int)$special['date_end']) ? $d.'/'.$m.'/'.$y : ''; ?>" class="date" showquick="off" /></td>
                <td><a onclick="$('#special_row<?php echo $special_row; ?>').remove();" class="button"><?php echo $l('button_remove'); ?></a></td>
            </tr>
        <?php } ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="5"></td>
                <td><a onclick="addSpecial();" class="button"><?php echo $l('button_add_special'); ?></a></td>
            </tr>
        </tfoot>
    </table>
</div>
<script type="text/javascript">
$(function(){
    $('.date').datepicker({dateFormat: 'dd-mm-yy'});
});
function addSpecial() {
    _row = $('.special_row:last-child').index() + 1 * 1;
	html = '<tr id="special_row' + _row + '" class="special_row">'; 
    html += '<td class="left"><select name="specials[' + _row + '][customer_group_id]">';
    <?php foreach ($customerGroups as $customer_group) { ?>
    html += '<option value="<?php echo $customer_group['customer_group_id']; ?>"><?php echo $customer_group['name']; ?></option>';
    <?php } ?>
    html += '</select></td>';		
    html += '<td class="left"><input type="necoNumber" name="specials[' + _row + '][priority]" value="" size="2" showquick="off" /></td>';
	html += '<td class="left"><input type="text" name="specials[' + _row + '][price]" value="" showquick="off" /></td>';
    html += '<td class="left"><input type="text" name="specials[' + _row + '][date_start]" value="" class="date" showquick="off" /></td>';
	html += '<td class="left"><input type="text" name="specials[' + _row + '][date_end]" value="" class="date" showquick="off" /></td>';
	html += '<td class="left"><a onclick="$(\'#special_row' + _row + '\').remove();" class="button"><span><?php echo $l('button_remove'); ?></span></a></td>';
	html += '</tr>';
	$('#special tbody').append(html);
 
	$('.date').datepicker({dateFormat: 'yy-mm-dd'});
}
</script>             