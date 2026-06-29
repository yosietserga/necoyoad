<div class="row">
    <label for="<?php echo $name; ?>SettingsClass"><?php echo $l('entry_class'); ?></label>
    <input id="<?php echo $name; ?>SettingsClass" name="Widgets[<?php echo $name; ?>][settings][class]" value="<?php echo isset($settings['class']) ? $settings['class'] : ''; ?>" />
</div>

<div class="row">
    <label><?php echo $l('Position'); ?></label>
    <select name="Widgets[<?php echo $name; ?>][settings][position]" id="widget_pages<?php echo $name; ?>" showquick="off">
        <option value="left"<?php if ($settings['position']==='left') echo ' selected="1"'; ?>><?php echo $l('Left'); ?></option>
        <option value="center"<?php if ($settings['position']==='center') echo ' selected="1"'; ?>><?php echo $l('Center'); ?></option>
        <option value="right"<?php if ($settings['position']==='right') echo ' selected="1"'; ?>><?php echo $l('Right'); ?></option>
    </select>
</div>
