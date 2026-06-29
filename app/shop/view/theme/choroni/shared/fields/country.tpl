<div class="entry-country form-entry">
    <label for="country_id"><?php echo $l('entry_country'); ?></label>
    <select name="country_id" id="country_id" title="Selecciona el pa&iaacute;s de la facturaci&oacute;n" onchange="$('select[name=\'zone_id\']').load('index.php?r=account/register/zone&country_id=' + this.value + '&zone_id=<?php echo $zone_id??0; ?>');">
        <option value="false"> <?php echo $l('text_option_select'); ?></option>
        <?php foreach ($countries as $country) { ?>
        <option value="<?php echo $country['country_id']; ?>"<?php if (isset($country_id) && $country['country_id'] == $country_id) { ?> selected="selected"<?php } ?>><?php echo $country['name']; ?></option>
        <?php } ?>
    </select>
</div>
