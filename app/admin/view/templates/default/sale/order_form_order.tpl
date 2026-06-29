<h2>Datos del Pedido</h2>
<table class="form">

	<?php if ($order_id) { ?>
	<tr>
		<td><?php echo $l('entry_order_id'); ?></td>
		<td>#<?php echo $order_id; ?></td>
	</tr>
	<tr>
		<td><?php echo $l('entry_invoice_id'); ?></td>
		<td id="invoice">
        <?php if ($invoice_id) { ?>
            <?php echo $invoice_id; ?>
        <?php } else { ?>
            <button id="generate_button" class="button" title="Genera Factura. Para poder ver e imprimir la factura, haga clic en el bot&oacute;n Pedido"><?php echo $l('button_generate'); ?></button>
        <?php } ?>
        </td>
	</tr>
	<tr>
		<td><?php echo $l('entry_name'); ?></td>
		<td>
			<?php if ($customer) { ?><a href="<?php echo $customer; ?>"><?php } ?>
				<?php echo $firstname ." ". $lastname; ?>
			<?php if ($customer) { ?></a><?php } ?>
		</td>
	</tr>
	<tr>
		<td><?php echo $l('entry_customer'); ?></td>
		<td><?php if ($customer) { ?><a href="<?php echo $customer; ?>"><?php } ?><?php echo $company; ?><?php if ($customer) { ?></a><?php } ?></td>
	</tr>
	<?php } else { ?>
	<tr>
		<td><?php echo $l('entry_customer'); ?></td>
		<td>
			<a class="lightbox customerName" data-fancybox-type="iframe" href="<?php echo $Url::createAdminUrl("sale/order/searchcustomer"); ?>&amp;field=image&amp;preview=preview">
			<?php echo $l('Search Customer'); ?>
			</a>

			<div class="clear"></div>

			<input type="hidden" name="firstname" value="<?php echo $firstname; ?>" />
			<input type="hidden" name="lastname" value="<?php echo $lastname; ?>" />
			<input type="hidden" name="customer_id" value="<?php echo $customer_id; ?>" />
			<input type="hidden" name="company" value="<?php echo $company; ?>" />
		</td>
	</tr>
	<?php } ?>

    <?php if ($customer_group) { ?>
	<tr>
		<td><?php echo $l('entry_customer_group'); ?></td>
		<td><?php echo $customer_group; ?></td>
	</tr>
    <?php } ?>

	<tr>
		<td><?php echo $l('entry_email'); ?></td>
		<td><input type="email" name="email" value="<?php echo $email; ?>" /></td>
	</tr>
	<tr>
		<td><?php echo $l('entry_telephone'); ?></td>
		<td><input type="text" name="telephone" value="<?php echo $telephone; ?>" /></td>
	</tr>
	<tr>
		<td><?php echo $l('entry_ip'); ?></td>
		<td><?php echo $ip; ?></td>
	</tr>
	<tr>
		<td><?php echo $l('entry_store_name'); ?></td>
		<td><?php echo $store_name; ?></td>
	</tr>
	<tr>
		<td><?php echo $l('entry_store_url'); ?></td>
		<td><a onclick="window.open('<?php echo $store_url; ?>');" style="font-style: italic;"><?php echo $store_url; ?></a></td>
	</tr>

	<?php if ($order_id) { ?>
	<tr>
		<td><?php echo $l('entry_date_added'); ?></td>
		<td><?php echo $date_added; ?></td>
	</tr>
	<?php } ?>

	<tr>
		<td><?php echo $l('entry_shipping_method'); ?></td>
		<td><input type="text" name="shipping_method" value="<?php echo $shipping_method; ?>" /></td>
	</tr>
	<tr>
		<td><?php echo $l('entry_payment_method'); ?></td>
		<td><input type="text" name="payment_method" value="<?php echo $payment_method; ?>" /></td>
	</tr>
	<tr>
		<td><?php echo $l('entry_total'); ?></td>
		<td><?php echo $total; ?></td>
	</tr>
	<tr>
		<td><?php echo $l('entry_order_status'); ?></td>
		<td id="order_status"><?php echo $order_status; ?></td>
	</tr>
    <?php if ($comment) { ?>
    <tr>
        <td><?php echo $l('entry_comment'); ?></td>
        <td><?php echo $comment; ?></td>
    </tr>
    <?php } ?>
</table>
<script type="text/javascript">
$('#generate_button').on('click', function(e) {
    $('#generate_button').attr('disabled', 'disabled');
	$.getJSON('<?php echo $Url::createAdminUrl("sale/order/generate", array('order_id'=>$order_id)); ?>', 
        function(data) {
			if (data) {
				$('#generate_button').fadeOut('slow',function() {
				    $('#generate_button').remove();
				});
   	            $('#invoice').html('#'+data.invoice_id);
			} else {
                $('#generate_button').attr('disabled', '');
			}
        });
});

function setCustomerOrder(data) {
	$('input[name=firstname]').val(data.firstname);
	$('input[name=lastname]').val(data.lastname);
	$('input[name=email]').val(data.email);
	$('input[name=company]').val(data.company);
	$('input[name=telephone]').val(data.telephone);
	$('input[name=customer_id]').val(data.customer_id);

	$('.customerName').html(data.firstname +' '+ data.lastname);
}
</script>