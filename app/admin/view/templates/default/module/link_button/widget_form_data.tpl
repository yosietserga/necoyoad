<div class="row">
    <label for="<?php echo $name; ?>SettingsClass"><?php echo $l('entry_class'); ?></label>
    <input id="<?php echo $name; ?>SettingsClass" name="Widgets[<?php echo $name; ?>][settings][class]" value="<?php echo isset($settings['class']) ? $settings['class'] : ''; ?>" />
</div>

<div class="row">
    <label for="<?php echo $name; ?>SettingsText"><?php echo $l('Text'); ?></label>
    <input id="<?php echo $name; ?>SettingsText" name="Widgets[<?php echo $name; ?>][settings][text]" value="<?php echo isset($settings['text']) ? $settings['text'] : ''; ?>" />
</div>

<div class="row">
    <label for="<?php echo $name; ?>SettingsHerf"><?php echo $l('Url'); ?></label>
    <input id="<?php echo $name; ?>SettingsHerf" name="Widgets[<?php echo $name; ?>][settings][href]" value="<?php echo isset($settings['href']) ? $settings['href'] : ''; ?>" />
</div>


<!-- put section for link to app objects (products, categories, posts, etc.) -->
<!-- put section for hover link effects -->
<!-- put section for image rollover -->
<!-- put section for image onclick events -->