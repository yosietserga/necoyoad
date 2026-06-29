<div class="row">
    <label for="<?php echo $name; ?>SettingsClass"><?php echo $l('entry_class'); ?></label>
    <input id="<?php echo $name; ?>SettingsClass" name="Widgets[<?php echo $name; ?>][settings][class]" value="<?php echo isset($settings['class']) ? $settings['class'] : ''; ?>" />
</div>

<div class="row">
    <label for="<?php echo $name; ?>SettingsText"><?php echo $l('Text'); ?></label>
    <textarea id="<?php echo $name; ?>SettingsText" name="Widgets[<?php echo $name; ?>][settings][text]"><?php echo isset($settings['text']) ? $settings['text'] : ''; ?></textarea>
</div>