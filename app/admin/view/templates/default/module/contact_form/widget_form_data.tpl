<div class="row">
    <label for="<?php echo $name; ?>SettingsClass"><?php echo $l('entry_class'); ?></label>
    <input id="<?php echo $name; ?>SettingsClass" name="Widgets[<?php echo $name; ?>][settings][class]" value="<?php echo isset($settings['class']) ? $settings['class'] : ''; ?>" />
</div>

<div class="row">
    <label for="<?php echo $name; ?>SettingsName"><?php echo $l('To Name'); ?></label>
    <input type="text" id="<?php echo $name; ?>SettingsName" name="Widgets[<?php echo $name; ?>][settings][toname]" value="<?php echo isset($settings['toname']) ? $settings['toname'] : $Config->get('config_name'); ?>" />
</div>

<div class="row">
    <label for="<?php echo $name; ?>SettingsEmail"><?php echo $l('To Email'); ?></label>
    <input type="email" id="<?php echo $name; ?>SettingsEmail" name="Widgets[<?php echo $name; ?>][settings][toemail]" value="<?php echo isset($settings['toemail']) ? $settings['toemail'] : $Config->get('config_email'); ?>" />
</div>

<div class="row">
    <label for="<?php echo $name; ?>SettingsPlaceholderName"><?php echo $l('Placeholder Name'); ?></label>
    <input type="email" id="<?php echo $name; ?>SettingsPlaceholderName" name="Widgets[<?php echo $name; ?>][settings][placeholder_name]" value="<?php echo isset($settings['placeholder_name']) ? $settings['placeholder_name'] : 'Name'; ?>" />
</div>

<div class="row">
    <label for="<?php echo $name; ?>SettingsPlaceholderEmail"><?php echo $l('Placeholder Email'); ?></label>
    <input type="email" id="<?php echo $name; ?>SettingsPlaceholderEmail" name="Widgets[<?php echo $name; ?>][settings][placeholder_email]" value="<?php echo isset($settings['placeholder_email']) ? $settings['placeholder_email'] : 'Email'; ?>" />
</div>

<div class="row">
    <label for="<?php echo $name; ?>SettingsPlaceholderMessage"><?php echo $l('Placeholder Message'); ?></label>
    <input type="email" id="<?php echo $name; ?>SettingsPlaceholderMessage" name="Widgets[<?php echo $name; ?>][settings][placeholder_enquiry]" value="<?php echo isset($settings['placeholder_enquiry']) ? $settings['placeholder_enquiry'] : 'Message'; ?>" />
</div>
