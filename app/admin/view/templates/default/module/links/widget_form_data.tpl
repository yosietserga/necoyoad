<div class="row">
    <label for="<?php echo $name; ?>SettingsClass"><?php echo $l('entry_class'); ?></label>
    <input id="<?php echo $name; ?>SettingsClass" name="Widgets[<?php echo $name; ?>][settings][class]" value="<?php echo isset($settings['class']) ? $settings['class'] : ''; ?>" />
</div>

<div class="row">
    <label><?php echo $l('entry_menu'); ?></label>
    <select name="Widgets[<?php echo $name; ?>][settings][menu_id]" showquick="off">
        <option value=""><?php echo $l('text_select'); ?></option>
        <?php foreach ($menus as $result) { ?>
        <option value="<?php echo $result['menu_id']; ?>"<?php if (isset($settings['menu_id']) && (int)$result['menu_id']==(int)$settings['menu_id']) { ?> selected="selected"<?php } ?>><?php echo $result['name']; ?></option>
        <?php } ?>
    </select>
</div>
