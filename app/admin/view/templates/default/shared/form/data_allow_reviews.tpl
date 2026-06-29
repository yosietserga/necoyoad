<div class="clear"></div>

<div class="row">
    <label><?php echo $l('Allow Comments'); ?></label>
    <input name="allow_reviews" value="1" type="checkbox"<?php if (!empty($allow_reviews) || !isset($allow_reviews)) { echo ' checked="checked"'; } ?> />
</div>
