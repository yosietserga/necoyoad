<div class="row">
    <label for="<?php echo $name; ?>SettingsClass"><?php echo $l('entry_class'); ?></label>
    <input id="<?php echo $name; ?>SettingsClass" name="Widgets[<?php echo $name; ?>][settings][class]" value="<?php echo isset($settings['class']) ? $settings['class'] : ''; ?>" />
</div>

<div class="row">
    <label for="<?php echo $name; ?>SettingsWidth"><?php echo $l('Image Width'); ?></label>
    <input id="<?php echo $name; ?>SettingsWidth" name="Widgets[<?php echo $name; ?>][settings][width]" value="<?php echo isset($settings['width']) ? $settings['width'] : ''; ?>" />
</div>

<div class="row">
    <label for="<?php echo $name; ?>SettingsHeight"><?php echo $l('Image Height'); ?></label>
    <input id="<?php echo $name; ?>SettingsHeight" name="Widgets[<?php echo $name; ?>][settings][height]" value="<?php echo isset($settings['height']) ? $settings['height'] : ''; ?>" />
</div>
