<h2>Productos del Pedido</h2>
<table id="products" class="list">
    <thead>
        <tr>
            <th><?php echo $l('column_product'); ?></th>
            <th><?php echo $l('column_quantity'); ?></th>
            <th><?php echo $l('column_price'); ?></th>
            <th><?php echo $l('column_total'); ?></th>
        </tr>
    </thead>
    
    <tbody>
    <?php foreach ($order_products as $key => $order_product) { ?>
        <tr id="product_<?php echo $key; ?>" class="product_row">
            <td>
                <span title="Eliminar Producto" class="remove" onclick="$('#product_<?php echo $key; ?>').remove();">&nbsp;</span>&nbsp;&nbsp;
                <a title="Ver Producto" href="<?php echo $order_product['href']; ?>"><?php echo $order_product['name']; ?> (<?php echo $order_product['model']; ?>)</a>
                <input type="hidden" name="product[<?php echo $key; ?>][product_id]" value="<?php echo $order_product['product_id']; ?>" />
                <?php foreach ($order_product['option'] as $option) { ?>
                <br />&nbsp;<small> - <?php echo $option['name']; ?> <?php echo $option['value']; ?></small>
                <?php } ?>
            </td>
            <td><input type="necoNumber" name="product[<?php echo $key; ?>][quantity]" value="<?php echo $order_product['quantity']; ?>" size="4" showquick="off" /></td>
            <td><input type="text" name="product[<?php echo $key; ?>][price]" value="<?php echo $order_product['price']; ?>" showquick="off" /></td>
            <td><input type="text" name="product[<?php echo $key; ?>][total]" value="<?php echo $order_product['total']; ?>" showquick="off" /></td>
        </tr>
    <?php } ?>
    </tbody>
          
    <tfoot id="totals">
        <?php foreach ($totals as $key => $totals) { ?>
        <tr>
            <td colspan="3" style="text-align:right;"><?php echo $totals['title']; ?></td>
            <td><input type="text" name="totals[<?php echo $totals['order_total_id']; ?>]" value="<?php echo $totals['text']; ?>" showquick="off" /></td>
        </tr>
        <?php } ?>
    </tfoot>
</table>

<?php if ($downloads) { ?>
<table class="list">
    <thead>
        <tr>
            <th><?php echo $l('column_download'); ?></th>
            <th><?php echo $l('column_filename'); ?></th>
            <th><?php echo $l('column_remaining'); ?></th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($downloads as $download) { ?>
        <tr>
            <td><?php echo $download['name']; ?></td>
            <td><?php echo $download['filename']; ?></td>
            <td><?php echo $download['remaining']; ?></td>
        </tr>
    <?php } ?>
    </tbody>
</table>
<?php } ?>

<table>
    <tr>
        <td><?php echo $l('entry_add_product'); ?><br />
        <table>
            <tr>
                <td style="padding: 0;" colspan="3">
                    <select id="category" style="margin-bottom: 5px;" onchange="getAll();">
                    <?php foreach ($categories as $category) { ?>
                        <option value="<?php echo $category['category_id']; ?>"><?php echo $category['title']; ?></option>
			        <?php } ?>
			        </select>
                </td>
            </tr>
			<tr>
                <td style="padding: 0;">
                    <select multiple="multiple" id="product" size="10" style="width: 350px;"></select>
                </td>
			    <td style="vertical-align: middle;"><span class="add" onclick="add();">&nbsp;</span></td>
            </tr>
        </table>
    </tr>
</table>
<script type="text/javascript">
function add() {
    var orderProductRow = $('.product_row:last-child').index(),
    html = '';

    $('#product :selected').each(function() {
        
	    html += '<td>';
	    html += '<input type="hidden" name="product[' + orderProductRow + '][product_id]" value="' + $(this).val() + '" />';
	    html += '<span onclick="$(\'#product_' + orderProductRow + '\').remove();" class="remove">&nbsp;</span>&nbsp;';
	    html += '<a href="<?php echo $Url::createAdminUrl("store/product/update"); ?>&product_id=' + $(this).val() + '">' + $(this).html() + '</a>';
	    html += '</td>';
	    html += '<td><input type="necoNumber" name="product[' + orderProductRow + '][quantity]" value="1" size="4" /></td>';
	    html += '<td><input type="text" name="product[' + orderProductRow + '][price]" value="' + $(this).attr('data-price') + '" /></td>';
	    html += '<td><input type="text" name="product[' + orderProductRow + '][total]" value="' + $(this).attr('data-price') + '" /></td>';
        
        var tr = $(document.createElement('tr')).attr({
            'id':'product_'+ orderProductRow
        })
        .html(html)
        .appendTo('#products tbody');
        
		orderProductRow++;
	});

}

function getAll() {
	$('#product option').remove();
	$.getJSON('<?php echo $Url::createAdminUrl("sale/order/category"); ?>&category_id=' + $('#category').val(), function(data) {
        for (i = 0; i < data.length; i++) {
            $('#product').append('<option data-model="' + data[i]['model'] + '" data-price="' + data[i]['price'] + '" value="' + data[i]['product_id'] + '">' + data[i]['title'] + ' (model: ' + data[i]['model'] + ')</option>');
        }
	});
}
$(function() {
    getAll();
});
</script>