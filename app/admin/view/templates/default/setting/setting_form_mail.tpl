<div>
    <h2>Correo Entrante</h2>
    <table class="form">
        <tr>
            <td><?php echo $l('entry_smtp_username'); ?></td>
            <td><input title="Ingrese el nombre de usuario de la cuenta de email" type="text" name="config_smtp_username" value="<?php echo $config_smtp_username; ?>" /></td>
          </tr>
          <tr>
            <td><?php echo $l('entry_smtp_password'); ?></td>
            <td><input title="Ingrese la contrase&ntilde;a del usuario" type="password" name="config_smtp_password" value="<?php echo $config_smtp_password; ?>"></td>
          </tr>
          <tr>
            <td><?php echo $l('entry_pop3_protocol'); ?></td>
            <td>
                <select name="config_pop3_protocol" title="Seleccione el protocolo de env&iacute;o para los emails. Le recomendamos que si no conoce los conceptos, acuda a nuestra documentaci&oacute;n  en la pesta&ntilde;a de Ayuda para aprender sobre este tema">
                    <option value="pop3"<?php if ($config_pop3_protocol == 'pop3') { ?> selected="selected"<?php } ?>>POP3</option>
                    <option value="imap"<?php if ($config_pop3_protocol == 'imap') { ?> selected="selected"<?php } ?>>IMAP4</option>
                </select>
            </td>
          </tr>
          <tr>
            <td><?php echo $l('entry_pop3_host'); ?></td>
            <td><input title="Ingrese los par&aacute;metros del servidor de email" type="text" name="config_pop3_host" value="<?php echo $config_pop3_host; ?>"></td>
            <?php if ($error_pop3_host) { ?><span class="error"><?php echo $error_pop3_host; ?></span><?php } ?>
          </tr>
          <tr>
            <td><?php echo $l('entry_pop3_port'); ?></td>
            <td><input title="Ingrese el n&uacute;mero de puerto del servidor de email" type="necoNumber" name="config_pop3_port" value="<?php echo $config_pop3_port; ?>"></td>
            <?php if ($error_pop3_port) { ?>
              <span class="error"><?php echo $error_pop3_port; ?></span>
              <?php } ?>
          </tr>
          <tr>
            <td><?php echo $l('entry_pop3_ssl'); ?></td>
            <td><select name="config_pop3_ssl" title="Ingrese el tama&ntilde;o m&aacute;ximo del email en Bytes, esto es utilizado para prevenir que se env&iacute;en emails con contenidos mayores a los permitidos">
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
                <input title="Seleccione en cuales categor&iacute;as desea que aparezca el producto. Hay casos en los que el mismo producto encaja en diferentes categor&iacute;as, por ejemplo un televisor de tercera generaci&oacute;n puede ser utilizado como monitor de un computador o como un simple televisor, por lo que estar&iacute;a en las categor&iacute;as monitores y televisores" type="checkbox" showquick="off" name="config_bounce_process" id="process_bounce"<?php if ($config_bounce_process) { ?> checked="checked"<?php } ?> onclick="show_bounce_settings()">
            </td>
        </tr>
    </table>
    
    <h2>Correo Saliente</h2>
    <table class="form">
        <tr>
            <td><?php echo $l('entry_mail_protocol'); ?></td>
            <td><select name="config_smtp_method" title="Seleccione el protocolo de env&iacute;o para los emails. Le recomendamos que si no conoce los conceptos, acuda a nuestra documentaci&oacute;n  en la pesta&ntilde;a de Ayuda para aprender sobre este tema">
                <option value="mail"<?php if ($config_smtp_method == 'mail') { ?> selected="selected"<?php } ?>><?php echo $l('text_mail'); ?></option>
                <option value="smtp"<?php if ($config_smtp_method == 'smtp') { ?> selected="selected"<?php } ?>><?php echo $l('text_smtp'); ?></option>   
                <option value="sendmail"<?php if ($config_smtp_method == 'sendmail') { ?> selected="selected"<?php } ?>><?php echo $l('text_sendmail'); ?></option>
              </select></td>
          </tr>
          <tr>
            <td><?php echo $l('entry_smtp_host'); ?></td>
            <td><input title="Ingrese la direcci&oacute;n del servidor de email" type="text" name="config_smtp_host" value="<?php echo $config_smtp_host; ?>"></td>
            <?php if ($error_smtp_host) { ?>
              <span class="error"><?php echo $error_smtp_host; ?></span>
              <?php } ?>
          </tr>
          <tr>
            <td><?php echo $l('entry_smtp_from_name'); ?></td>
            <td><input title="Ingrese el tama&ntilde;o m&aacute;ximo del email en Bytes, esto es utilizado para prevenir que se env&iacute;en emails con contenidos mayores a los permitidos" type="text" name="config_smtp_from_name" value="<?php echo $config_smtp_from_name; ?>"></td>
          </tr>
          <tr>
            <td><?php echo $l('entry_smtp_from_email'); ?></td>
            <td><input title="Ingrese el tama&ntilde;o m&aacute;ximo del email en Bytes, esto es utilizado para prevenir que se env&iacute;en emails con contenidos mayores a los permitidos" type="email" name="config_smtp_from_email" value="<?php echo $config_smtp_from_email; ?>"></td>
            <?php if ($error_smtp_from_email) { ?>
              <span class="error"><?php echo $error_smtp_from_email; ?></span>
              <?php } ?>
          </tr>
          <tr>
            <td><?php echo $l('entry_smtp_port'); ?></td>
            <td><input title="Ingrese el n&uacute;mero de puerto del servidor de email" type="necoNumber" name="config_smtp_port" value="<?php echo $config_smtp_port; ?>"></td>
            <?php if ($error_smtp_port) { ?>
              <span class="error"><?php echo $error_smtp_port; ?></span>
              <?php } ?>
          </tr>
          <tr>
            <td><?php echo $l('entry_smtp_ssl'); ?></td>
            <td><select name="config_smtp_ssl" title="Ingrese el tama&ntilde;o m&aacute;ximo del email en Bytes, esto es utilizado para prevenir que se env&iacute;en emails con contenidos mayores a los permitidos">
                <option value=""<?php if ($config_smtp_ssl == '') { ?> selected="selected"<?php } ?>>No SSL/TLS</option>
                <option value="ssl"<?php if ($config_smtp_ssl == 'ssl') { ?> selected="selected"<?php } ?>>SSL</option>
                <option value="tls"<?php if ($config_smtp_ssl == 'tls') { ?> selected="selected"<?php } ?>>TLS</option>
              </select></td>
          </tr>
          <tr>
            <td><?php echo $l('entry_smtp_auth'); ?></td>
            <td><input title="Ingrese el tama&ntilde;o m&aacute;ximo del email en Bytes, esto es utilizado para prevenir que se env&iacute;en emails con contenidos mayores a los permitidos" type="checkbox" showquick="off" name="config_smtp_auth" value="1"<?php if (isset($config_smtp_auth)) { ?> checked="checked"<?php } ?>></td>
          </tr>
          <tr>
            <td><?php echo $l('entry_smtp_charset'); ?></td>
            <td><input title="Ingrese el tama&ntilde;o m&aacute;ximo del email en Bytes, esto es utilizado para prevenir que se env&iacute;en emails con contenidos mayores a los permitidos" type="text" name="config_smtp_charset" value="<?php echo $config_smtp_charset; ?>"></td>
          </tr>
          <tr>
            <td><?php echo $l('entry_smtp_timeout'); ?></td>
            <td><input title="Ingrese el tiempo de parada del servidor de email" type="necoNumber" name="config_smtp_timeout" value="<?php echo $config_smtp_timeout; ?>"></td>
            <?php if ($error_smtp_timeout) { ?>
              <span class="error"><?php echo $error_smtp_timeout; ?></span>
              <?php } ?>
          </tr>
          <tr>
            <td><?php echo $l('entry_email_max_size'); ?></td>
            <td><input title="Ingrese el tama&ntilde;o m&aacute;ximo del email en Bytes, esto es utilizado para prevenir que se env&iacute;en emails con contenidos mayores a los permitidos" type="necoNumber" name="config_smtp_maxsize" value="<?php echo $config_smtp_maxsize; ?>"></td>
          </tr>
          <tr>
            <td><?php echo $l('entry_mail_parameter'); ?></td>
            <td><input title="Ingrese los par&aacute;metros del servidor de email" type="text" name="config_mail_parameter" value="<?php echo $config_mail_parameter; ?>"></td>
          </tr>     
          <tr>
            <td><?php echo $l('entry_alert_mail'); ?><br><span class="help">Enviar un email al dueño de la tienda cuando se crea un nuevo pedido.</span></td>
            <td>
              <input title="Seleccione si desea recibir un email cada vez que se crea un pedido" type="checkbox" showquick="off" name="config_alert_mail" value="1"<?php if ($config_alert_mail) { ?> checked="checked"<?php } ?>></td>
          </tr>
          <tr>
            <td><?php echo $l('entry_alert_emails'); ?></td>
            <td><textarea title="Ingrese el texto que ser&aacute; enviado por email por cada pedido creado" name="config_alert_emails" cols="40" rows="5"><?php echo $config_alert_emails; ?></textarea></td>
          </tr>
        </table>
        <h2>Correos Rebotados</h2>
        <table id="bounce_process" class="form">
        <tr> 
            <td>
                <?php echo $l('entry_bounce_server'); ?>
            </td>
            <td>
                <input type="text" name="config_bounce_server" id="bounce_server" value="<?php echo $config_bounce_server; ?>">
            </td>
        </tr>
         <tr>
            <td>
                <?php echo $l('entry_bounce_username'); ?>
            </td>
            <td>
                <input type="text" name="config_bounce_username" id="bounce_username" value="<?php echo $config_bounce_username; ?>">
            </td>
        </tr>
        <tr>
            <td>
                <?php echo $l('entry_bounce_password'); ?>
            </td>
            <td>
                <input type="password" name="config_bounce_password" id="bounce_password" value="<?php echo $config_bounce_password; ?>">
            </td>
        </tr>
        <tr>
            <td>
                <?php echo $l('entry_imap_account'); ?>
            </td>
            <td>
            <?php if ($config_bounce_protocol == 'imap') { ?>
                <select name="config_bounce_protocol">
                    <option value="pop3"><?php echo $l('text_pop'); ?></option>
                    <option value="imap" selected="selected"><?php echo $l('text_imap'); ?></option>
                </select>
             <?php } else { ?>
             	<select name="config_bounce_protocol">
                    <option value="pop3" selected="selected"><?php echo $l('text_pop'); ?></option>
                    <option value="imap"><?php echo $l('text_imap'); ?></option>
                </select>
             <?php } ?>
            </td>
        </tr>
        
        <tr>
            <td>
                <?php echo $l('entry_extra_mail_settings'); ?>
            </td>
            <td>
                  <input type="checkbox" showquick="off" name="config_bounce_extraoption" id="bounce_extraoption" value="<?php echo $config_bounce_extraoption; ?>">
            </td>
        </tr>
        <tr>
          <td></td>
          <td>
          <table id="bounce_extra_sttings">
                	<tr>
                        <td>
                            <?php echo $l('entry_extramail_nossl'); ?>
                        </td>
                        <td>
                            <input type="radio" showquick="off" name="extra_mail_nossl" value="ssl" /><?php echo $l('text_true'); ?>
                            <input type="radio" showquick="off" name="extra_mail_nossl" value="nossl" /><?php echo $l('text_false'); ?>
                            <input type="radio" showquick="off" name="extra_mail_nossl" value="" /><?php echo $l('text_nosure'); ?>
                        </td>
                      </tr>
                      <tr>
                        <td>
                            <?php echo $l('entry_extramail_notls'); ?>
                        </td>
                        <td>
                            <input type="radio" showquick="off" name="extra_mail_notls" value="tls" /><?php echo $l('text_true'); ?>
                            <input type="radio" showquick="off" name="extra_mail_notls" value="notls" /><?php echo $l('text_false'); ?>
                            <input type="radio" showquick="off" name="extra_mail_notls" value="" /><?php echo $l('text_nosure'); ?>
                        </td>
                      </tr>
                      <tr>
                    	<td>
                          <?php echo $l('entry_extramail_novalidate'); ?>
                        </td>
                        <td>
                            <input type="radio" showquick="off" name="extra_mail_novalidate" value="" /><?php echo $l('text_true'); ?>
                            <input type="radio" showquick="off" name="extra_mail_novalidate" value="novalidate-cert" /><?php echo $l('text_false'); ?>
                            <input type="radio" showquick="off" name="extra_mail_novalidate" value="" /><?php echo $l('text_nosure'); ?>
                    	</td>
                      </tr>
                      <tr>
                        <td>
                            <?php echo $l('entry_extramail_others'); ?>
                        </td>
                        <td>
                            <input type="checkbox" showquick="off" name="extramail_others" id="extramail_others" value="" onclick="other_setting();"/>
                            <input type="text" name="extra_mail_others" id="other" value="" disabled="disabled" />
                        </td>
                      </tr>
                </table>
          </td>
        </tr>
        
         <tr>
            <td>
                <?php echo $l('entry_agree_delete'); ?>
            </td>
            <td>
                <input type="checkbox" showquick="off" name="config_bounce_agree_delete" value="1" id="agree_delete"><label for="agree_delete"><?php echo $l('entry_agree_delete'); ?></label>
            </td>
        </tr>
        
        <tr>
             <td></td>
            <td><a name="cmdTestBounce" id="cmdTestBounce" onclick="bounce();" class="button"><span>Probar Conexi&oacute;n</span></a></td>
        </tr>
    </table>
</div>