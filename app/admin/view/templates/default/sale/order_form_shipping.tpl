<h2>Direcci&oacute; de Env&iacute;o</h2>
<input type="hidden" name="shipping_firstname" value="<?php echo $shipping_firstname; ?>" />
<input type="hidden" name="shipping_lastname" value="<?php echo $shipping_lastname; ?>" />
<input type="hidden" name="shipping_company" value="<?php echo $shipping_company; ?>" />
<table class="form">
    <tr>
        <td><?php echo $l('entry_address_1'); ?></td>
        <td><input type="text" name="shipping_address_1" value="<?php echo $shipping_address_1; ?>" /></td>
    </tr>
    <tr>
        <td><?php echo $l('entry_address_2'); ?></td>
        <td><input type="text" name="shipping_address_2" value="<?php echo $shipping_address_2; ?>" /></td>
    </tr>
    <tr>
        <td><?php echo $l('entry_city'); ?></td>
        <td><input type="text" name="shipping_city" value="<?php echo $shipping_city; ?>" /></td>
    </tr>
    <tr>
        <td><?php echo $l('entry_postcode'); ?></td>
        <td><input type="text" name="shipping_postcode" value="<?php echo $shipping_postcode; ?>" /></td>
    </tr>
    <tr>
        <td><?php echo $l('entry_country'); ?></td>
        <td>
            <select name="shipping_country_id" id="shipping_country" onchange="$('#shipping_zone').load('<?php echo $Url::createAdminUrl("sale/order/zone",array("zone_id"=>$shipping_zone_id,"type"=>"shipping_zone")); ?>&country_id=' + this.value);">
            <?php foreach ($countries as $country) { ?>
                <option value="<?php echo $country['country_id']; ?>"<?php if ($country['country_id'] == $shipping_country_id) { ?> selected="selected"<?php } ?>><?php echo $country['title']; ?></option>
            <?php } ?>
            </select>
            <input type="hidden" name="shipping_country" value="<?php echo $shipping_country; ?>" />
        </td>
    </tr>
    <tr>
        <td><?php echo $l('entry_zone'); ?></td>
        <td id="shipping_zone"></td>
    </tr>
</table>
<script type="text/javascript">
$(function() {
    $('#shipping_zone').load('<?php
        echo $Url::createAdminUrl("sale/order/zone", array(
            "country_id"=>$shipping_country_id,
            "zone_id"=>$shipping_zone_id,
            "type"=>"shipping_zone"
        ));
    ?>', 
    function() {
        $('#shipping_zone select').on('change', function() {
        $('#shipping_zone_name').remove();
        $('#shipping_zone select').after('<input id="shipping_zone_name" name="shipping_zone" value="' + $('#shipping_zone select :selected').text() + '" type="hidden" />');
        });
    });
});
</script>