<div>
    <h2>Correo Entrante</h2>
    <table class="form">
        <tr>
            <td><?php echo $l('entry_smtp_username'); ?></td>
            <td><input class="necoEmail01" title="Ingrese el nombre de usuario de la cuenta de email" type="text" name="config_smtp_username" value="<?php echo $config_smtp_username; ?>" /></td>
          </tr>
          <tr>
            <td><?php echo $l('entry_smtp_password'); ?></td>
            <td><input class="necoEmail02" title="Ingrese la contrase&ntilde;a del usuario" type="password" name="config_smtp_password" value="<?php echo $config_smtp_password; ?>"></td>
          </tr>
          <tr>
            <td><?php echo $l('entry_pop3_protocol'); ?></td>
            <td>
                <select class="necoEmail03" name="config_pop3_protocol" title="Seleccione el protocolo de env&iacute;o para los emails. Le recomendamos que si no conoce los conceptos, acuda a nuestra documentaci&oacute;n  en la pesta&ntilde;a de Ayuda para aprender sobre este tema">
                    <option value="pop3"<?php if ($config_pop3_protocol == 'pop3') { ?> selected="selected"<?php } ?>>POP3</option>
                    <option value="imap"<?php if ($config_pop3_protocol == 'imap') { ?> selected="selected"<?php } ?>>IMAP4</option>
                </select>
            </td>
          </tr>
          <tr>
            <td><?php echo $l('entry_pop3_host'); ?></td>
            <td><input class="necoEmail04" title="Ingrese los par&aacute;metros del servidor de email" type="text" name="config_pop3_host" value="<?php echo $config_pop3_host; ?>"></td>
            <?php if ($error_pop3_host) { ?><span class="error"><?php echo $error_pop3_host; ?></span><?php } ?>
          </tr>
          <tr>
            <td><?php echo $l('entry_pop3_port'); ?></td>
            <td><input class="necoEmail05" title="Ingrese el n&uacute;mero de puerto del servidor de email" type="necoNumber" name="config_pop3_port" value="<?php echo $config_pop3_port; ?>"></td>
            <?php if ($error_pop3_port) { ?>
              <span class="error"><?php echo $error_pop3_port; ?></span>
              <?php } ?>
          </tr>
          <tr>
            <td><?php echo $l('entry_pop3_ssl'); ?></td>
            <td><select class="necoEmail06" name="config_pop3_ssl" title="Ingrese el tama&ntilde;o m&aacute;ximo del email en Bytes, esto es utilizado para prevenir que se env&iacute;en emails con contenidos mayores a los permitidos">
                <option value=""<?php if ($config_pop3_ssl == '') { ?> selected="selected"<?php } ?>>No SSL/TLS</option>
                <option value="ssl"<?php if ($config_pop3_ssl == 'ssl') { ?> selected="selected"<?php } ?>>SSL</option>
                <option value="tls"<?php if ($config_pop3_ssl == 'tls') { ?> selected="selected"<?php } ?>>TLS</option>
              </select></td>
          </tr>
          <tr>
            <td>
                <?php echo $l('entry_process_bounce'); ?>
            </td>
            <td>
                <input class="necoEmail07" title="Seleccione en cuales categor&iacute;as desea que aparezca el producto. Hay casos en los que el mismo producto encaja en diferentes categor&iacute;as, por ejemplo un televisor de tercera generaci&oacute;n puede ser utilizado como monitor de un computador o como un simple televisor, por lo que estar&iacute;a en las categor&iacute;as monitores y televisores" type="checkbox" showquick="off" name="config_bounce_process" id="process_bounce"<?php if ($config_bounce_process) { ?> checked="checked"<?php } ?> onclick="show_bounce_stores()">
            </td>
        </tr>
    </table>
    
    <h2>Correo Saliente</h2>
    <table class="form">
        <tr>
            <td><?php echo $l('entry_mail_protocol'); ?></td>
            <td><select class="necoEmail08" name="config_smtp_method" title="Seleccione el protocolo de env&iacute;o para los emails. Le recomendamos que si no conoce los conceptos, acuda a nuestra documentaci&oacute;n  en la pesta&ntilde;a de Ayuda para aprender sobre este tema">
                <option value="mail"<?php if ($config_smtp_method == 'mail') { ?> selected="selected"<?php } ?>><?php echo $l('text_mail'); ?></option>
                <option value="smtp"<?php if ($config_smtp_method == 'smtp') { ?> selected="selected"<?php } ?>><?php echo $l('text_smtp'); ?></option>   
                <option value="sendmail"<?php if ($config_smtp_method == 'sendmail') { ?> selected="selected"<?php } ?>><?php echo $l('text_sendmail'); ?></option>
              </select></td>
          </tr>
          <tr>
            <td><?php echo $l('entry_smtp_host'); ?></td>
            <td><input class="necoEmail09" title="Ingrese la direcci&oacute;n del servidor de email" type="text" name="config_smtp_host" value="<?php echo $config_smtp_host; ?>"></td>
            <?php if ($error_smtp_host) { ?>
              <span class="error"><?php echo $error_smtp_host; ?></span>
              <?php } ?>
          </tr>
          <tr>
            <td><?php echo $l('entry_smtp_from_name'); ?></td>
            <td><input class="necoEmail10" title="Ingrese el tama&ntilde;o m&aacute;ximo del email en Bytes, esto es utilizado para prevenir que se env&iacute;en emails con contenidos mayores a los permitidos" type="text" name="config_smtp_from_name" value="<?php echo $config_smtp_from_name; ?>"></td>
          </tr>
          <tr>
            <td><?php echo $l('entry_smtp_from_email'); ?></td>
            <td><input class="necoEmail11" title="Ingrese el tama&ntilde;o m&aacute;ximo del email en Bytes, esto es utilizado para prevenir que se env&iacute;en emails con contenidos mayores a los permitidos" type="email" name="config_smtp_from_email" value="<?php echo $config_smtp_from_email; ?>"></td>
            <?php if ($error_smtp_from_email) { ?>
              <span class="error"><?php echo $error_smtp_from_email; ?></span>
              <?php } ?>
          </tr>
          <tr>
            <td><?php echo $l('entry_smtp_port'); ?></td>
            <td><input class="necoEmail12" title="Ingrese el n&uacute;mero de puerto del servidor de email" type="necoNumber" name="config_smtp_port" value="<?php echo $config_smtp_port; ?>"></td>
            <?php if ($error_smtp_port) { ?>
              <span class="error"><?php echo $error_smtp_port; ?></span>
              <?php } ?>
          </tr>
          <tr>
            <td><?php echo $l('entry_smtp_ssl'); ?></td>
            <td><select class="necoEmail13" name="config_smtp_ssl" title="Ingrese el tama&ntilde;o m&aacute;ximo del email en Bytes, esto es utilizado para prevenir que se env&iacute;en emails con contenidos mayores a los permitidos">
                <option value=""<?php if ($config_smtp_ssl == '') { ?> selected="selected"<?php } ?>>No SSL/TLS</option>
                <option value="ssl"<?php if ($config_smtp_ssl == 'ssl') { ?> selected="selected"<?php } ?>>SSL</option>
                <option value="tls"<?php if ($config_smtp_ssl == 'tls') { ?> selected="selected"<?php } ?>>TLS</option>
              </select></td>
          </tr>
          <tr>
            <td><?php echo $l('entry_smtp_auth'); ?></td>
            <td><input class="necoEmail14" title="Ingrese el tama&ntilde;o m&aacute;ximo del email en Bytes, esto es utilizado para prevenir que se env&iacute;en emails con contenidos mayores a los permitidos" type="checkbox" showquick="off" name="config_smtp_auth" value="1"<?php if (isset($config_smtp_auth)) { ?> checked="checked"<?php } ?>></td>
          </tr>
          <tr>
            <td><?php echo $l('entry_smtp_charset'); ?></td>
            <td><input class="necoEmail15" title="Ingrese el tama&ntilde;o m&aacute;ximo del email en Bytes, esto es utilizado para prevenir que se env&iacute;en emails con contenidos mayores a los permitidos" type="text" name="config_smtp_charset" value="<?php echo $config_smtp_charset; ?>"></td>
          </tr>
          <tr>
            <td><?php echo $l('entry_mail_parameter'); ?></td>
            <td><input class="necoEmail16" title="Ingrese los par&aacute;metros del servidor de email" type="text" name="config_mail_parameter" value="<?php echo $config_mail_parameter; ?>"></td>
          </tr>
        </table>
</div>