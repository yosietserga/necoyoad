<div class="row">
    <label for="<?php echo $name; ?>SettingsClass"><?php echo $l('entry_class'); ?></label>
    <input id="<?php echo $name; ?>SettingsClass" name="Widgets[<?php echo $name; ?>][settings][class]" value="<?php echo isset($settings['class']) ? $settings['class'] : ''; ?>" />
</div>

<div class="row">
    <label><?php echo $l('entry_code'); ?></label><br />
    <textarea name="Widgets[<?php echo $name; ?>][settings][google_maps_code]" cols="40" rows="7"><?php echo $google_maps_code; ?></textarea>
</div>
