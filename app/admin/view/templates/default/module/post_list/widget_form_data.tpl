<div class="row">
    <label for="<?php echo $name; ?>SettingsClass"><?php echo $l('entry_class'); ?></label>
    <input id="<?php echo $name; ?>SettingsClass" name="Widgets[<?php echo $name; ?>][settings][class]" value="<?php echo isset($settings['class']) ? $settings['class'] : ''; ?>" />
</div>


<div class="row">
    <label for="<?php echo $name; ?>Settingsimage_popup_width"><?php echo $l('Image Popup Width'); ?></label>
    <input id="<?php echo $name; ?>Settingsimage_popup_width" name="Widgets[<?php echo $name; ?>][settings][image_popup_width]" value="<?php echo isset($settings['image_popup_width']) ? $settings['image_popup_width'] : ''; ?>" />
</div>

<div class="row">
    <label for="<?php echo $name; ?>Settingsimage_popup_height"><?php echo $l('Image Popup Height'); ?></label>
    <input id="<?php echo $name; ?>Settingsimage_popup_height" name="Widgets[<?php echo $name; ?>][settings][image_popup_height]" value="<?php echo isset($settings['image_popup_height']) ? $settings['image_popup_height'] : ''; ?>" />
</div>

<div class="row">
    <label for="<?php echo $name; ?>Settingsimage_thumb_width"><?php echo $l('Image Thumb Width'); ?></label>
    <input id="<?php echo $name; ?>Settingsimage_thumb_width" name="Widgets[<?php echo $name; ?>][settings][image_thumb_width]" value="<?php echo isset($settings['image_thumb_width']) ? $settings['image_thumb_width'] : ''; ?>" />
</div>

<div class="row">
    <label for="<?php echo $name; ?>Settingsimage_thumb_height"><?php echo $l('Image Thumb Height'); ?></label>
    <input id="<?php echo $name; ?>Settingsimage_thumb_height" name="Widgets[<?php echo $name; ?>][settings][image_thumb_height]" value="<?php echo isset($settings['image_thumb_height']) ? $settings['image_thumb_height'] : ''; ?>" />
</div>

<div class="row">
    <label><?php echo $l('entry_module'); ?></label>
    <select name="Widgets[<?php echo $name; ?>][settings][module]">
        <option value="random"<?php if ($settings['module'] == 'random') echo ' selected="selected"'; ?>><?php echo $l('Random Posts'); ?></option>
        <option value="latest"<?php if ($settings['module'] == 'latest') echo ' selected="selected"'; ?>><?php echo $l('Latest Posts'); ?></option>
        <option value="featured"<?php if ($settings['module'] == 'featured') echo ' selected="selected"'; ?>><?php echo $l('Featured Posts'); ?></option>
        <option value="popular"<?php if ($settings['module'] == 'popular') echo ' selected="selected"'; ?>><?php echo $l('Popular Posts'); ?></option>
        <option value="recommended"<?php if ($settings['module'] == 'recommended') echo ' selected="selected"'; ?>><?php echo $l('Top Visits Posts'); ?></option>
    </select>
</div>

<div class="row">
    <label for="<?php echo $name; ?>SettingsPostType"><?php echo $l('Post Type'); ?></label>
    <input id="<?php echo $name; ?>SettingsPostType" name="Widgets[<?php echo $name; ?>][settings][post_type]" value="<?php echo isset($settings['post_type']) ? $settings['post_type'] : 'post'; ?>" />
</div>

<div class="row">
    <label for="<?php echo $name; ?>SettingsShowPagination"><?php echo $l('Show Pagination'); ?></label>
    <div class="checkbox">
        <input id="<?php echo $name; ?>SettingsShowPagination" type="checkbox" name="Widgets[<?php echo $name; ?>][settings][show_pagination]" value="1" />
        <span></span>
    </div>
</div>

<div class="row">
    <label for="<?php echo $name; ?>SettingsEndlessScroll"><?php echo $l('Endless Scroll'); ?></label>
    <div class="checkbox">
        <input id="<?php echo $name; ?>SettingsEndlessScroll" type="checkbox" name="Widgets[<?php echo $name; ?>][settings][endless_scroll]" value="1" />
        <span></span>
    </div>
</div>

<div class="row">
    <label for="<?php echo $name; ?>Settingsshow_featured_image"><?php echo $l('Show Featured Image'); ?></label>
    <div class="checkbox">
        <input id="<?php echo $name; ?>Settingsshow_featured_image" type="checkbox" name="Widgets[<?php echo $name; ?>][settings][show_featured_image]" value="1" />
        <span></span>
    </div>
</div>

<div class="row">
    <label for="<?php echo $name; ?>SettingsLimit"><?php echo $l('entry_limit'); ?></label>
    <input id="<?php echo $name; ?>SettingsLimit" name="Widgets[<?php echo $name; ?>][settings][limit]" value="<?php echo isset($settings['limit']) ? (int)$settings['limit'] : 4; ?>" />
</div>

<?php if ($categories) { ?>
<div class="row">
    <label><?php echo $l('Select Categories'); ?></label>
    <ul class="scrollbox" data-scrollbox="1">
        <?php echo $categories; ?>
    </ul>
</div>
<?php } ?>
