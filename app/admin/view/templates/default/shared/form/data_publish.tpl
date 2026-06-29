<div class="clear"></div>

<div class="row">
    <label><?php echo $l('Publicado'); ?></label>
    <input name="publish" value="1" type="checkbox"<?php if (!empty($publish) || !isset($publish)) { echo ' checked="checked"'; } ?> />
</div>