<div class="row">
    <label for="<?php echo $name; ?>SettingsClass"><?php echo $l('entry_class'); ?></label>
    <input id="<?php echo $name; ?>SettingsClass" name="Widgets[<?php echo $name; ?>][settings][class]" value="<?php echo isset($settings['class']) ? $settings['class'] : ''; ?>" />
</div>

<div class="row">
    <label for="widget_attributess<?php echo $name; ?>"><?php echo $l('entry_attributes'); ?></label>
    <select name="Widgets[<?php echo $name; ?>][settings][product_attribute_group_id]" id="widget_attributess<?php echo $name; ?>" showquick="off">
        <option value=""><?php echo $l('text_select'); ?></option>
        <?php foreach ($attributess as $result) { ?>
        <option value="<?php echo $result['product_attribute_group_id']; ?>"<?php if ($result['product_attribute_group_id']==$settings['product_attribute_group_id']) { ?> selected="selected"<?php } ?>><?php echo $result['name']; ?></option>
        <?php } ?>
    </select>
</div>
