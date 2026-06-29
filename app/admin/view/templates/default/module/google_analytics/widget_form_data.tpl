<div class="row">
    <label><?php echo $l('entry_code'); ?></label>
    <input type="text" name="Widgets[<?php echo $name; ?>][settings][google_analytics_code]" value="<?php echo isset($settings['google_analytics_code']) ? $settings['google_analytics_code'] : ''; ?>" />
</div>
                    
<div class="row">
    <label for="<?php echo $name; ?>SettingsClass"><?php echo $l('entry_class'); ?></label>
    <input id="<?php echo $name; ?>SettingsClass" name="Widgets[<?php echo $name; ?>][settings][class]" value="<?php echo isset($settings['class']) ? $settings['class'] : ''; ?>" />
</div>