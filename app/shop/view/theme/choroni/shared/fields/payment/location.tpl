<div class="property form-entry country-entry">
    <label for="payment_country_id"><?php echo $l('entry_country'); ?></label>
    <select name="payment_country_id" id="payment_country_id" title="<?php echo $l('help_country'); ?>" onchange="$('select[name=\'payment_zone_id\']').load('index.php?r=account/register/zone&country_id=' + this.value + '&zone_id=<?php echo $payment_zone_id??0; ?>');">
        <option value="false"><?php echo $l('help_country'); ?></option>
        <?php foreach ($countries as $country) { ?>
        <?php if (isset($payment_country_id) && $country['country_id'] === $payment_country_id) { ?>
        <option value="<?php echo $country['country_id']; ?>" selected="selected"><?php echo $country['name']; ?></option>
        <?php } else { ?>
        <option value="<?php echo $country['country_id']; ?>"><?php echo $country['name']; ?></option>
        <?php } ?>
        <?php } ?>
    </select>
</div>

<div class="property form-entry zone-entry">
    <label for="payment_zone_id"><?php echo $l('entry_zone'); ?></label>
    <select name="payment_zone_id" id="payment_zone_id" title="<?php echo $l('help_zone'); ?>">
        <option value="false">-- Seleccione un pa&iacute;s --</option>
    </select>
</div>