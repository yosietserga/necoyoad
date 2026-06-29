<div class="entry-sexo form-entry">
    <label><?php echo $l('entry_sexo'); ?></label>
    <select id="sexo" name="sexo" title="Seleccione su sexo" >
        <option value="false">Seleccione su sexo</option>
        <option value="m"<?php if (strtolower($sexo) == 'm') { ?> selected="selected"<?php } ?>>Hombre</option>
        <option value="f"<?php if (strtolower($sexo) == 'f') { ?> selected="selected"<?php } ?>>Mujer</option>
        <option value="x"<?php if (strtolower($sexo) == 'x') { ?> selected="selected"<?php } ?>>No quiero decirlo</option>
    </select>
    <?php if ($error_sexo) { ?><span class="error" id="error_sexo"><?php echo $error_sexo; ?></span><?php } ?>
</div>