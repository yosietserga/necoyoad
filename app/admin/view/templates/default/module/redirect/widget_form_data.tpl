<div class="row">
    <label for="<?php echo $name; ?>SettingsClass"><?php echo $l('entry_class'); ?></label>
    <input id="<?php echo $name; ?>SettingsClass" name="Widgets[<?php echo $name; ?>][settings][class]" value="<?php echo isset($settings['class']) ? $settings['class'] : ''; ?>" />
</div>

<div class="row">
    <label for="<?php echo $name; ?>SettingsClass"><?php echo $l('Redirect To'); ?></label>
    <input id="<?php echo $name; ?>SettingsClass" name="Widgets[<?php echo $name; ?>][settings][redirect_to]" value="<?php echo isset($settings['redirect_to']) ? $settings['redirect_to'] : ''; ?>" />
</div>

<div class="row">
    <label for="<?php echo $name; ?>SettingsClass"><?php echo $l('Delay in seconds'); ?></label>
    <input id="<?php echo $name; ?>SettingsClass" name="Widgets[<?php echo $name; ?>][settings][delay]" value="<?php echo isset($settings['delay']) ? $settings['delay'] : ''; ?>" />
</div>

<div class="row">
    <label for="<?php echo $name; ?>SettingsClass"><?php echo $l('Message'); ?></label>
    <input id="<?php echo $name; ?>SettingsClass" name="Widgets[<?php echo $name; ?>][settings][message]" value="<?php echo isset($settings['message']) ? $settings['message'] : ''; ?>" />
</div>