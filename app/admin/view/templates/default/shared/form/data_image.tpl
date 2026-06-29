<div class="clear"></div>
<div class="row">
    <label><?php echo $l('Image'); ?></label>
    <a class="filemanager" data-fancybox-type="iframe" href="<?php echo $Url::createAdminUrl("common/filemanager"); ?>&amp;field=image&amp;preview=preview">
    <img src="<?php echo isset($preview) ? $preview : ''; ?>" id="preview" class="image necoImage" width="100" />
    </a>
    <input type="hidden" name="image" value="<?php echo isset($image) ? $image : ''; ?>" id="image" onchange="$('#preview').attr('src',  window.nt.http_image + this.value);" />
    <br />
    <a class="filemanager" data-fancybox-type="iframe" href="<?php echo $Url::createAdminUrl("common/filemanager"); ?>&amp;field=image&amp;preview=preview" style="margin-left: 220px;color:#FFA500;font-size:10px">[ Cambiar ]</a>
    <a onclick="image_delete('image', 'preview');" style="color:#FFA500;font-size:10px">[ Quitar ]</a>
</div>