<div class="last-name-entry form-entry">
    <label><?php echo $l('entry_lastname'); ?></label>
    <input data-label="<?php echo $l('entry_lastname'); ?>" type="lastname" id="lastname" name="lastname" value="<?php echo $lastname??""; ?>" title="Ingrese sus apellidos" required="required" placeholder="Ingrese su apellido(s)" />
    <?php if (isset($error_lastname) && $error_lastname) { ?><span class="error" id="error_lastname"><?php echo $error_lastname; ?></span><?php } ?>
</div>