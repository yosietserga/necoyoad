<div class="row">
    <label for="<?php echo $name; ?>SettingsClass"><?php echo $l('entry_class'); ?></label>
    <input id="<?php echo $name; ?>SettingsClass" name="Widgets[<?php echo $name; ?>][settings][class]" value="<?php echo isset($settings['class']) ? $settings['class'] : ''; ?>" />
</div>

<div class="row">
    <label for="<?php echo $name; ?>SettingsWidth"><?php echo $l('Width'); ?></label>
    <input id="<?php echo $name; ?>SettingsWidth" name="Widgets[<?php echo $name; ?>][settings][width]" value="<?php echo isset($settings['width']) ? $settings['width'] : ''; ?>" />
</div>

<div class="row">
    <label for="<?php echo $name; ?>SettingsHeight"><?php echo $l('Height'); ?></label>
    <input id="<?php echo $name; ?>SettingsHeight" name="Widgets[<?php echo $name; ?>][settings][height]" value="<?php echo isset($settings['height']) ? $settings['height'] : ''; ?>" />
</div>

<?php

        if (!empty($settings['image']) && file_exists(DIR_IMAGE . $settings['image'])) {
            $preview = $Image::resizeAndSave($settings['image'], 100, 100);
        } else {
            $preview = $Image::resizeAndSave('no_image.jpg', 100, 100);
        }

?>
 <div class="row">
    <label for="<?php echo $name; ?>SettingsImage"><?php echo $l('Image'); ?></label>

    <a style="text-decoration: none" class="filemanager" data-fancybox-type="iframe" href="<?php echo $Url::createAdminUrl("common/filemanager"); ?>&amp;field=image_<?php echo $name; ?>&amp;preview=preview_<?php echo $name; ?>">
        <img src="<?php echo isset($preview) ? $preview : ''; ?>" id="preview_<?php echo $name; ?>" class="image necoImage" width="100" />
    </a>

    <input type="hidden" name="Widgets[<?php echo $name; ?>][settings][image]" value="<?php echo isset($settings['image']) ? $settings['image'] : ''; ?>" id="image_<?php echo $name; ?>" onchange="$('#preview_<?php echo $name; ?>').attr('src', '<?php echo $Url::createUrl('common/home/getimage', array('width'=>100, 'height'=>100), 'NONSSL', HTTP_CATALOG); ?>&image='+ this.value);$('#<?php echo $name; ?> a.advanced').trigger('click');" />

    <br />

    <a class="filemanager" data-fancybox-type="iframe" href="<?php echo $Url::createAdminUrl("common/filemanager"); ?>&amp;field=image_<?php echo $name; ?>&amp;preview=preview_<?php echo $name; ?>" style="margin-left: 220px;color:#FFA500;font-size:10px">[ Cambiar ]</a>
</div>


<!-- put section for image css filters -->
<!-- 
	put section for hover image effects 
		- zoom
		- image rollover 
		- css mask
		- css filters 
-->
<!-- put section for image rollover -->
<!-- 
	put section for image onclick events 
		- go to external link 
		- go to internal link (app object: product, category, post, etc.)
		- play video
		- play sound 
-->