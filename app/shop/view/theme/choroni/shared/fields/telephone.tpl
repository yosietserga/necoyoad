<div class="entry-telephone form-entry">
    <label><?php echo $l('entry_telephone'); ?></label>
    <input data-label="<?php echo $l('entry_telephone'); ?>" type="numeric" id="telephone" name="telephone" value="<?php echo $telephone??""; ?>" title="Ingrese su n&uacute;mero de tel&eacute;fono" placeholder="Ingrese su teléfono. E.j: 04127777777" />
    <?php if (isset($error_telephone) && $error_telephone) { ?><span class="error" ide"error_telephone"><?php echo $error_telephone; ?></span><?php } ?>
</div>