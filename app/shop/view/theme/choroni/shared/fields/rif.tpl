<div class="entry-rif form-entry">
    <label><?php echo $l('entry_rif'); ?></label>
    <div class="clear"></div>

    <div class="large-3 medium-4 small-5">
        <select name="riftype" title="Selecciona el tipo de documentaci&oacute;n">
            <option value="V" <?php if (isset($rif_type) && strtolower($rif_type) == 'v') echo 'selected="selected"'; ?>>V</option>
            <option value="J" <?php if (isset($rif_type) && strtolower($rif_type) == 'j') echo 'selected="selected"'; ?>>J</option>
            <option value="E" <?php if (isset($rif_type) && strtolower($rif_type) == 'e') echo 'selected="selected"'; ?>>E</option>
            <option value="G" <?php if (isset($rif_type) && strtolower($rif_type) == 'g') echo 'selected="selected"'; ?>>G</option>
        </select>
    </div>
    <div class="large-9 medium-8 small-7">
        <input type="rif" id="rif" name="rif" value="<?php echo $rif??""; ?>" title="Por favor ingrese su RIF. Si es persona natural y a&uacute;n no posee uno, ingrese su n&uacute;mero de c&eacute;dula con un n&uacute;mero cero al final" required="required" placeholder="Ingrese su rif o cedula de indentidad"/>
    </div>
    <?php if (isset($error_rif) && $error_rif) { ?><span class="error" id="error_rif"><?php echo $error_rif; ?></span><?php } ?>
</div>