<div class="entry-confirm form-entry">
    <label><?php echo $l('entry_confirm'); ?></label>
    <input data-label="<?php echo $l('entry_confirm'); ?>" type="password" name="confirm" id="confirm" value="" autocomplete="off" title="Vuelva a escribir la contrase&ntilde;a"/>
    <?php if (isset($error_confirm) && $error_confirm) { ?><span class="error" id="error_confirm"><?php echo $error_confirm; ?></span><?php } ?>
</div>