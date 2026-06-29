<div class="row">
    <label for="<?php echo $name; ?>SettingsClass"><?php echo $l('entry_class'); ?></label>
    <input id="<?php echo $name; ?>SettingsClass" name="Widgets[<?php echo $name; ?>][settings][class]" value="<?php echo isset($settings['class']) ? $settings['class'] : ''; ?>" />
</div>

<div class="row">
    <label><?php echo $l('entry_pageid'); ?></label>
    <input title="<?php echo $l('help_pageid'); ?>" type="text" name="Widgets[<?php echo $name; ?>][settings][fblike_pageid]" value="<?php echo isset($settings['fblike_pageid']) ? $settings['fblike_pageid'] : ''; ?>" />
</div>

<div class="row">
    <label><?php echo $l('entry_totalconnection'); ?></label>
    <input title="<?php echo $l('help_totalconnection'); ?>" type="necoNumber" name="Widgets[<?php echo $name; ?>][settings][fblike_totalconnection]" value="<?php echo isset($settings['fblike_totalconnection']) ? $settings['fblike_totalconnection'] : 8; ?>" style="width: 40px;" />
</div>

<div class="row">
    <label><?php echo $l('entry_width'); ?></label>
    <input title="<?php echo $l('help_width'); ?>" type="necoNumber" name="Widgets[<?php echo $name; ?>][settings][fblike_width]" value="<?php echo isset($settings['fblike_width']) ? $settings['fblike_width'] : 200; ?>" style="width: 40px;" />
</div>

<div class="row">
    <label><?php echo $l('entry_height'); ?></label>
    <input title="<?php echo $l('help_height'); ?>" type="necoNumber" name="Widgets[<?php echo $name; ?>][settings][fblike_height]" value="<?php echo isset($settings['fblike_height']) ? $settings['fblike_height'] : 250; ?>" style="width: 40px;" />
</div>

<div class="row">
    <label><?php echo $l('entry_stream'); ?></label>
    <select name="Widgets[<?php echo $name; ?>][settings][fblike_stream]">
        <option value="1"<?php if (isset($settings['fblike_stream'])) { ?> selected="selected"<?php } ?>><?php echo $l('text_true'); ?></option>
        <option value="0"<?php if (!isset($settings['fblike_stream'])) { ?> selected="selected"<?php } ?>><?php echo $l('text_false'); ?></option>
    </select>
</div>

<div class="row">
    <label><?php echo $l('entry_header'); ?></label>
    <select name="Widgets[<?php echo $name; ?>][settings][fblike_header]">
        <option value="1"<?php if (isset($settings['fblike_header'])) { ?> selected="selected"<?php } ?>><?php echo $l('text_true'); ?></option>
        <option value="0"<?php if (!isset($settings['fblike_header'])) { ?> selected="selected"<?php } ?>><?php echo $l('text_false'); ?></option>
    </select>
</div>
