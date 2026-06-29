<div>
    <h2>Local</h2>
    <div class="row">
        <label><?php echo $l('entry_country'); ?></label>
        <select name="config_country_id" id="country" onchange="$('#zone').load('<?php echo $Url::createAdminUrl("setting/setting/zone"); ?>&country_id=' + this.value + '&zone_id=<?php echo $config_zone_id; ?>');">
        <?php foreach ($countries as $country) { ?>
            <option value="<?php echo $country['country_id']; ?>"<?php if ($country['country_id'] == $config_country_id) { ?> selected="selected"<?php } ?>><?php echo $country['name']; ?></option>
        <?php } ?>
        </select>
    </div>
                                          
    <div class="clear"></div>
    
    <div class="row">
        <label><?php echo $l('entry_zone'); ?></label>
        <select name="config_zone_id" id="zone"></select>
    </div>
                                          
    <div class="clear"></div>
    
    <div class="row">
        <label><?php echo $l('entry_language'); ?></label>
        <select name="config_language">
        <?php foreach ($languages as $language) { ?>
            <option value="<?php echo $language['code']; ?>"<?php if ($language['code'] == $config_language) { ?> selected="selected"<?php } ?>><?php echo $language['name']; ?></option>
        <?php } ?>
        </select>
    </div>
                                          
    <div class="clear"></div>
    
    <div class="row">
        <label><?php echo $l('entry_admin_language'); ?></label>
        <select name="config_admin_language">
        <?php foreach ($languages as $language) { ?>
            <option value="<?php echo $language['code']; ?>"<?php if ($language['code'] == $config_language) { ?> selected="selected"<?php } ?>><?php echo $language['name']; ?></option>
        <?php } ?>
        </select>
    </div>
                                          
    <div class="clear"></div>
    
    <div class="row">
        <label><?php echo $l('entry_currency'); ?></label>
        <select name="config_currency">
        <?php foreach ($currencies as $currency) { ?>
            <option value="<?php echo $currency['code']; ?>"<?php if ($currency['code'] == $config_currency) { ?> selected="selected"<?php } ?>><?php echo $currency['title']; ?></option>
        <?php } ?>
        </select>
    </div>
                                          
    <div class="clear"></div>
    
    <div class="row">
        <label><?php echo $l('entry_decimal_separator'); ?></label>
        <input type="text" title="<?php echo $l('help_decimal_separator'); ?>" name="config_decimal_separator" value="<?php echo $config_decimal_separator; ?>" required="true"<?php if (isset($error_decimal_separator)) echo ' class="neco-input-error'; ?> />
    </div>
                                     
    <div class="clear"></div>
    
    <div class="row">
        <label><?php echo $l('entry_thousands_separator'); ?></label>
        <input type="text" title="<?php echo $l('help_thousands_separator'); ?>" name="config_thousands_separator" value="<?php echo $config_thousands_separator; ?>" required="true"<?php if (isset($error_thousands_separator)) echo ' class="neco-input-error'; ?> />
    </div>
                                     
    <div class="clear"></div>
    
    <div class="row">
        <label><?php echo $l('entry_currency_auto'); ?></label>
        <input type="checkbox" showquick="off" name="config_currency_auto" value="1"<?php if ($config_currency_auto) { ?> checked="checked"<?php } ?> />
    </div>
                                      
    <div class="clear"></div>
</div>