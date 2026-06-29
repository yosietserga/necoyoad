<div>
    <h2>Servidor</h2>
    <table class="form">
        <tr>
            <td><?php echo $l('entry_ssl'); ?><br><span class="help">Para usar SSL comprueba con tu host si hay instalado un certificado SSL y a continuaci&oacute;n añade la direcci&oacute;n del SSL en tu archivo de configuraci&oacute;n.</span></td>
            <td>
              <input class="necoServer01" title="Seleccione si desea utilizar una direcci&oacute;n segura para su tienda. Le recomendamos que si no conoce los cpnceptos, acuda a nuestra documentaci&oacute;n en la pesta&ntilde;a de Ayuda para aprender sobre este tema" type="checkbox" showquick="off" name="config_ssl" value="1"<?php if ($config_ssl) { ?> checked="checked"<?php } ?>></td>
          </tr>
          <tr>
            <td><?php echo $l('entry_maintenance'); ?></td>
            <td>
              <input class="necoServer02" title="Seleccione si desea colocar en estado de mantenimiento. En estado de mantenimiento, los clientes no tienen ning&uacute;n acceso a la tienda" type="checkbox" showquick="off" name="config_maintenance" value="1"<?php if ($config_maintenance) { ?> checked="checked"<?php } ?>></td>
          </tr>
          <tr>
            <td><?php echo $l('entry_server_security'); ?></td>
            <td>
              <input class="necoServer03" title="Seleccione si desea activar la seguridad de javascript en la tienda. Al activarlo, puede que fallen algunas funciones de la tienda" type="checkbox" showquick="off" name="config_server_security" value="1"<?php if ($config_server_security) { ?> checked="checked"<?php } ?>>
              </td>
          </tr>
          <tr>
            <td><?php echo $l('entry_password_security'); ?></td>
            <td>
              <input class="necoServer04" title="Seleccione si desea activar la validaci&oacute;n de la contrase&ntilde;a para que sea eficiente" type="checkbox" showquick="off" name="config_password_security" value="1"<?php if ($config_password_security) { ?> checked="checked"<?php } ?>>
              </td>
          </tr>
          <tr>
            <td><?php echo $l('entry_seo_url'); ?><a > [ ! ]</a><br><span class="help">Para usar el m&oacute;dulo SEO URL\ de apache  debe estar instalado el mod-rewrite y necesitas renombrar el htaccess.txt a .htaccess.</span></td>
            <td>
              <input class="necoServer05" title="Seleccione si desea utilizar el renombramiento de las URLs. Esto es utilizado para hacer m&aacute;s amigables las direcciones URL de la tienda y lograr una mayor indexaci&oacute;n de los contenidos de la tienda en los buscadores de Internet (p. ej. la URL https://www.mitienda.com/index.php?r=product/product&product_id=28 se ver&iacute;a como https://www.mitienda.com/ipod_classic). Esto es una t&eacute;nica SEO" type="checkbox" showquick="off" name="config_seo_url" value="1"<?php if ($config_seo_url) { ?> checked="checked"<?php } ?>>
              </td>
          </tr>
          <tr>
            <td><?php echo $l('entry_compression'); ?><br><span class="help">Comprime todos los ficheros con GZIP para mejores rendimiento de la tienda. El nivel de compresi&oacute;n deber&iacute;a estar entre 0 - 9</span></td>
            <td><input class="necoServer06" title="Ingrese el nivel de compresi&oacute;n de los archivos de sistema de la tienda. Esto es utilizado para mejorar el rendimiento de la tienda" type="necoNumber" name="config_compression" value="<?php echo $config_compression; ?>" size="3"></td>
          </tr>
          <tr>
            <td><?php echo $l('entry_error_display'); ?></td>
            <td>
              <input class="necoServer07" title="Seleccione si desea mostrar los errores del sistema en la tienda. Le recomendamos que elija la opci&oacute;n No" type="checkbox" showquick="off" name="config_error_display" value="1"<?php if ($config_error_display) { ?> checked="checked"<?php } ?>>
              </td>
          </tr>
          <tr>
            <td><?php echo $l('entry_error_log'); ?></td>
            <td>
              <input class="necoServer08" type="checkbox" showquick="off" name="config_error_log" title="Seleccione si desea registrar todos los errores ocurridos en la tienda. Le recomendamos que elija la opci&oacute;n Si" value="1"<?php if ($config_error_log) { ?> checked="checked"<?php } ?> />
              </td>
          </tr>
          <tr>
            <td><span class="required">*</span> <?php echo $l('entry_error_filename'); ?></td>
            <td><input class="necoServer09" title="Ingrese el nombre del archivo donde se registrar&aacute;n todos los errores (p. ej. log_error.txt)" type="text" name="config_error_filename" value="<?php echo $config_error_filename; ?>">
              <?php if ($error_error_filename) { ?>
              <span class="error"><?php echo $error_error_filename; ?></span>
              <?php } ?></td>
          </tr>
          <tr>
            <td><?php echo $l('entry_dir_export'); ?></td>
            <td><input class="necoServer10" title="Ingrese el nombre del archivo donde se registrar&aacute;n todos los errores (p. ej. log_error.txt)" type="text" name="config_dir_export" value="<?php echo $config_dir_export; ?>"></td>
          </tr>
          <tr>
          <td><?php echo $l('entry_token_ignore'); ?></td>
          <td><div class="scrollbox necoServer11">
              <?php $class = 'odd'; ?>
              <?php foreach ($tokens as $ignore_token) { ?>
              <?php $class = ($class == 'even' ? 'odd' : 'even'); ?>
              <div class="<?php echo $class; ?>">
                <?php if (in_array($ignore_token, $config_token_ignore)) { ?>
                <input title="Seleccione las p&aacute;ginas y los m&oacute;dulos que no desea que se muestren en la tienda" type="checkbox" showquick="off" name="config_token_ignore[]" value="<?php echo $ignore_token; ?>" checked="checked">
                <?php echo $ignore_token; ?>
                <?php } else { ?>
                <input title="Seleccione las p&aacute;ginas y los m&oacute;dulos que no desea que se muestren en la tienda" type="checkbox" showquick="off" name="config_token_ignore[]" value="<?php echo $ignore_token; ?>">
                <?php echo $ignore_token; ?>
                <?php } ?>
              </div>
              <?php } ?>
            </div></td>
          </tr>
        </table>
      </div>