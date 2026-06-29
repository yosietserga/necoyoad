<div class="clear"></div>
<div class="row">
    <label><?php echo $l('entry_view'); ?></label>
    <select name="layout">
        <option value=""<?php if (empty($layout)) { echo ' selected="selected"'; } ?>><?php echo $l('text_default'); ?></option>
        <?php foreach ($views as $key => $value) { ?>
        <optgroup label="<?php echo $value['folder']; ?>">
            <?php foreach ($value['files'] as $k => $v) { ?>
            <option value="<?php echo basename($value['folder']) ."/". basename($v); ?>"<?php if ($layout==basename($value['folder']) ."/". basename($v)) { echo ' selected="selected"'; } ?>><?php echo basename($v); ?></option>
            <?php } ?>
        </optgroup>
        <?php } ?>
    </select>
</div>