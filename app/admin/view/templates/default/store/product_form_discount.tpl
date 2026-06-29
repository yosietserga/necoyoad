<div>
    <table id="discount" class="list">
        <thead>
            <tr>
                <th><?php echo $l('entry_customer_group'); ?></th>
                <th><?php echo $l('entry_quantity'); ?></th>
                <th><?php echo $l('entry_priority'); ?></th>
                <th><?php echo $l('entry_price'); ?></th>
                <th><?php echo $l('entry_date_start'); ?></th>
                <th><?php echo $l('entry_date_end'); ?></th>
                <th></th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($discounts as $discount_row => $discount) { ?>
            <tr id="discount_row<?php echo $discount_row; ?>" class="discount_row">
                <td>
                    <select name="discounts[<?php echo $discount_row; ?>][customer_group_id]">
                    <?php foreach ($customerGroups as $customer_group) { ?>
                        <?php if ($customer_group['customer_group_id'] == $discount['customer_group_id']) { ?>
                        <option value="<?php echo $customer_group['customer_group_id']; ?>" selected="selected"><?php echo $customer_group['name']; ?></option>
                        <?php } else { ?>
                        <option value="<?php echo $customer_group['customer_group_id']; ?>"><?php echo $customer_group['name']; ?></option>
                        <?php } ?>
                    <?php } ?>
                    </select>
                </td>
                <td><input type="text" name="discounts[<?php echo $discount_row; ?>][quantity]" value="<?php echo $discount['quantity']; ?>" size="2" showquick="off" /></td>
                <td><input type="text" name="discounts[<?php echo $discount_row; ?>][priority]" value="<?php echo $discount['priority']; ?>" size="2" showquick="off" /></td>
                <td><input type="text" name="discounts[<?php echo $discount_row; ?>][price]" value="<?php echo $discount['price']; ?>" showquick="off" /></td>
                <td><input type="text" name="discounts[<?php echo $discount_row; ?>][date_start]" value="<?php list($y, $m, $d) = explode('-', $discount['date_start']); echo $d.'/'.$m.'/'.$y; ?>" class="date" showquick="off" /></td>
                <td><input type="text" name="discounts[<?php echo $discount_row; ?>][date_end]" value="<?php list($y, $m, $d) = explode('-', $discount['date_end']); echo ((int)$discount['date_end']) ? $d.'/'.$m.'/'.$y : ''; ?>" class="date" showquick="off" /></td>
                <td><a onclick="$('#discount_row<?php echo $discount_row; ?>').remove();" class="button"><?php echo $l('button_remove'); ?></a></td>
            </tr>
            <?php } ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="6"></td>
                <td><a onclick="addDiscount();" class="button"><?php echo $l('button_add_discount'); ?></a></td>
            </tr>
        </tfoot>
    </table>
                
</div>
<script type="text/javascript">
$(function(){
    $('.date').datepicker({dateFormat: 'dd-mm-yy'});
});
function addDiscount() {
    _row = ($('.discount_row:last-child').index() + 1);
	html = '<tr id="discount_row' + _row + '" class="discount_row">'; 
    html += '<td class="left"><select name="discounts[' + _row + '][customer_group_id]" style="margin-top: 3px;">';
    <?php foreach ($customerGroups as $customer_group) { ?>
    html += '<option value="<?php echo $customer_group['customer_group_id']; ?>"><?php echo $customer_group['name']; ?></option>';
    <?php } ?>
    html += '</select></td>';
    html += '<td class="left"><input type="text" name="discounts[' + _row + '][quantity]" value="" size="2" showquick="off" /></td>';
    html += '<td class="left"><input type="text" name="discounts[' + _row + '][priority]" value="" size="2" showquick="off" /></td>';
	html += '<td class="left"><input type="text" name="discounts[' + _row + '][price]" value="" showquick="off" /></td>';
    html += '<td class="left"><input type="text" name="discounts[' + _row + '][date_start]" value="" class="date" showquick="off" /></td>';
	html += '<td class="left"><input type="text" name="discounts[' + _row + '][date_end]" value="" class="date" showquick="off" /></td>';
	html += '<td class="left"><a onclick="$(\'#discount_row' + _row + '\').remove();" class="button"><span><?php echo $l('button_remove'); ?></span></a></td>';
	html += '</tr>';
	
	$('#discount tbody').append(html);
		
	$('.date').datepicker({dateFormat: 'dd-mm-yy'});
}
</script>