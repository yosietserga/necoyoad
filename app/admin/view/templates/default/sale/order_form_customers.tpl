
<div class="row">
	<input type="text" title="Filtrar listado de tiendas y sucursales" value="" name="q" id="q" placeholder="Filtrar Tiendas" />

	<div class="clear"></div>

	<ul id="customersWrapper" class="scrollbox" data-scrollbox="1">
		<?php foreach ($customers as $customer) { ?>
		<li class="selectCustomer" id="<?php echo $customer['customer_id']; ?>">
			<?php echo $customer['firstname'] .' '. $customer['lastname'] .' ('. $customer['company'] .')'; ?>
			<input type="hidden" name="firstname" value="<?php echo $customer['firstname']; ?>" />
			<input type="hidden" name="lastname" value="<?php echo $customer['lastname']; ?>" />
			<input type="hidden" name="customer_id" value="<?php echo $customer['customer_id']; ?>" />
			<input type="hidden" name="telephone" value="<?php echo $customer['telephone']; ?>" />
			<input type="hidden" name="company" value="<?php echo $customer['company']; ?>" />
			<input type="hidden" name="email" value="<?php echo $customer['email']; ?>" />
			<div class="clear"></div>
		</li>
		<?php } ?>
	</ul>
</div>
<script type="text/javascript" src="<?php echo HTTP_JS; ?>vendor/jquery.min.js"></script>
<script>
$(function(){
	$('.selectCustomer').on('click', function(){
		var data = {};
		data.customer_id = $(this).find('input[name=customer_id]').val();
		data.firstname = $(this).find('input[name=firstname]').val();
		data.lastname = $(this).find('input[name=lastname]').val();
		data.email = $(this).find('input[name=email]').val();
		data.company = $(this).find('input[name=company]').val();
		data.telephone = $(this).find('input[name=telephone]').val();
		parent.setCustomerOrder(data);
		parent.$.fancybox.close();
	});
});
</script>