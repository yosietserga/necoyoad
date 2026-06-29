<h2>Direcci&oacute; del Pago</h2>
<input type="hidden" name="payment_firstname" value="<?php echo $payment_firstname; ?>" />
<input type="hidden" name="payment_lastname" value="<?php echo $payment_lastname; ?>" />
<input type="hidden" name="payment_company" value="<?php echo $payment_company; ?>" />
<table class="form">
    <tr>
        <td><?php echo $l('entry_address_1'); ?></td>
        <td><input type="text" name="payment_address_1" value="<?php echo $payment_address_1; ?>" /></td>
    </tr>
    <tr>
        <td><?php echo $l('entry_address_2'); ?></td>
        <td><input type="text" name="payment_address_2" value="<?php echo $payment_address_2; ?>" /></td>
    </tr>
    <tr>
        <td><?php echo $l('entry_city'); ?></td>
        <td><input type="text" name="payment_city" value="<?php echo $payment_city; ?>" /></td>
    </tr>
    <tr>
        <td><?php echo $l('entry_postcode'); ?></td>
        <td><input type="text" name="payment_postcode" value="<?php echo $payment_postcode; ?>" /></td>
    </tr>
    <tr>
        <td><?php echo $l('entry_country'); ?></td>
        <td>
            <select name="payment_country_id" id="payment_country" onchange="$('#payment_zone').load('<?php echo $Url::createAdminUrl("sale/order/zone",array("zone_id"=>$payment_zone_id,"type"=>"payment_zone")); ?>&country_id=' + this.value);">
            <?php foreach ($countries as $country) { ?>
                <option value="<?php echo $country['country_id']; ?>"<?php if ($country['country_id'] == $payment_country_id) { ?> selected="selected"<?php } ?>><?php echo $country['title']; ?></option>
            <?php } ?>
            </select>
            <input type="hidden" name="payment_country" value="<?php echo $payment_country; ?>" />
        </td>
    </tr>
    <tr>
        <td><?php echo $l('entry_zone'); ?></td>
        <td id="payment_zone"></td>
    </tr>
</table>
<script type="text/javascript">
$(function() {
    $('#payment_zone').load('<?php
        echo $Url::createAdminUrl("sale/order/zone", array(
            "country_id"=>$payment_country_id,
            "zone_id"=>$payment_zone_id,
            "type"=>"payment_zone"
        ));
    ?>', 
    function() {
        $('#payment_zone select').on('change', function() {
        $('#payment_zone_name').remove();
        $('#payment_zone select').after('<input id="payment_zone_name" name="payment_zone" value="' + $('#payment_zone select :selected').text() + '" type="hidden" />');
        });
    });
});
</script>