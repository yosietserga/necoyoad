<?php echo $header; ?>
<?php echo $navigation; ?>
<div class="container">
    
    <?php if (isset($breadcrumbs) && is_array($breadcrumbs)) { ?>
    <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
    </ul>
    <?php } ?>
    
    <?php if (isset($success) && $success) { ?><div class="grid_12"><div class="message success"><?php echo $success; ?></div></div><?php } ?>
    <?php if ((isset($msg) && $msg) || (isset($error_warning) && $error_warning)) { ?><div class="grid_12"><div class="message warning"><?php echo $msg ?? $error_warning; ?></div></div><?php } ?>
    <?php if (isset($error) && $error) { ?><div class="grid_12"><div class="message error"><?php echo $error; ?></div></div><?php } ?>
    <div class="grid_12" id="msg"></div>
    
    <div class="box">
        <h1>Crear Campa&ntilde;a</h1>
        <div class="buttons">
            <a onclick="saveAndExit();$('#form').submit();" class="button">Guardar y Enviar</a>
            <a onclick="saveAndNew();$('#form').submit();" class="button">Enviar Prueba</a>
            <a onclick="location = '<?php echo $cancel; ?>';" class="button"><?php echo $l('button_cancel'); ?></a>
        </div>
        
        <div class="clear"></div>
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
        
            <div class="row">
                <label>Nombre de la Campa&ntilde;a</label>
                <input type="text" name="name" id="name" value="<?php echo ($name) ? $name : ''; ?>" required="required" />
            </div>
            
            <div class="row">
                <label>Asunto</label>
                <input type="text" name="subject" id="subject" value="<?php echo ($subject) ? $subject : ''; ?>" required="required" />
            </div>
            
            <div class="row">
                <label>Nombre del Remitente</label>
                <input type="text" name="from_name" id="from_name" value="<?php echo ($from_name) ? $from_name : ''; ?>" required="required" />
            </div>
            
            <div class="row">
                <label>Email del Remitente</label>
                <input type="email" name="from_email" id="from_email" value="<?php echo ($from_email) ? $from_email : ''; ?>" required="required" />
            </div>
            
            <div class="row">
                <label>Email de R&eacute;plica</label>
                <input type="email" name="replyto_email" id="replyto_email" value="<?php echo ($replyto_email) ? $replyto_email : ''; ?>" required="required" />
            </div>
            
            <div class="row">
                <label>Servidor de Email</label>
                <select name="mail_server_id">
                    <option<?php if (!$mail_server_id) echo ' selected="selected"'; ?>>Servidor Local</option>
                    <?php foreach($mail_servers as $id => $server) { ?>
                    <option value="<?php echo $id; ?>"<?php if ($mail_server_id === $id) echo ' selected="selected"'; ?>>
                        <?php echo $server['server'] .' &lt;'. $server['username'] .'&gt;'; ?>
                    </option>
                    <?php } ?>
                </select>
            </div>
            
            <div class="row">
                <label>Fecha de Env&iacute;o ( hh:mm A dd/mm/yy )</label>
                <?php  $i = 0; ?>
                <select name="start_hour">
                    <?php while ($i < 13) { ?>
                    <option value="<?php echo ($i < 10) ? "0".$i : $i; ?>"<?php if (((int)$start_hour + 1) == $i) { ?> selected="selected"<?php } ?>><?php echo ($i < 10) ? "0".$i : $i; ?></option>
                    <?php $i++; 
                     } ?>
                </select>
                <span style="float: left;">&nbsp;:&nbsp;</span>
                <select name="start_minute">
                    <?php foreach ($minutes as $min) { ?>
                    <option value="<?php echo $min; ?>"<?php if ($start_minute==$min) { echo " selected=\"selected\""; } ?>><?php echo $min; ?></option>
                    <?php } ?>
                </select>
                <select name="start_meridium" style="margin-left:5px">
                    <option value="AM"<?php if ($start_meridium=='AM') { echo " selected=\"selected\""; } ?>>AM</option>
                    <option value="PM"<?php if ($start_meridium=='PM') { echo " selected=\"selected\""; } ?>>PM</option>
                </select>
                <select name="start_day" style="margin-left:10px">
                    <?php for ($i = 1; $i <= 31; $i++) { ?>
                    <option value="<?php echo ($i < 10) ? '0'.$i : $i; ?>"<?php if ((int)$start_day == $i) { ?> selected="selected"<?php } ?>><?php echo ($i < 10) ? '0'.$i : $i; ?></option>
                    <?php } ?>
                </select>
                <span style="float: left;">&nbsp;/&nbsp;</span>
                <select name="start_month">
                    <?php for ($i = 1; $i <= 12; $i++) { ?>
                    <option value="<?php echo ($i < 10) ? '0'.$i : $i; ?>"<?php if ((int)$start_month == $i) { ?> selected="selected"<?php } ?>><?php echo ($i < 10) ? '0'.$i : $i; ?></option>
                    <?php } ?>
                </select>
                <span style="float: left;">&nbsp;/&nbsp;</span>
                <select name="start_year">
                    <?php for ($i = date("Y"); $i <= (date("Y") + 5); $i++) { ?>
                    <option value="<?php echo $i; ?>"<?php if ((int)$start_year == $i) { ?> selected="selected"<?php } ?>><?php echo $i; ?></option>
                    <?php } ?>
                </select>
            </div>
            
            <div class="row">
                <label>Repetir esta campa&ntilde;a</label>
                <select name="repeat" onchange="if (this.value=='weekly') {$('#repeat_wday').show()} else {$('#repeat_wday').hide()} if (this.value.length>0) {$('#end').show()} else {$('#end').hide()} ">
                    <option value="">Una sola vez</option>
                    <option value="daily">Todos los d&iacute;as</option>
                    <option value="weekly">Semanal</option>
                    <option value="monthly">Mensual</option>
                    <option value="yearly">Anual</option>
                </select>
                <span style="float: left;">&nbsp;&nbsp;</span>
                <select name="repeat_wday" id="repeat_wday" style="display: none;">
                    <option value="sunday">Domingo</option>
                    <option value="monday">Lunes</option>
                    <option value="tuesday">Martes</option>
                    <option value="wednesday">Mi&eacute;rcoles</option>
                    <option value="thusday">Jueves</option>
                    <option value="friday">Viernes</option>
                    <option value="saturday">S&aacute;bado</option>
                </select>
            </div>
            
            <div class="row" id="end" style="display: none;">
                <label>Repetir Hasta ( hh:mm A dd/mm/yy )</label>
                <?php  $i = 0; ?>
                <select name="end_hour">
                    <?php while ($i < 13) { ?>
                    <option value="<?php echo ($i < 10) ? "0".$i : $i; ?>"<?php if (((int)$end_hour + 1) == $i) { ?> selected="selected"<?php } ?>><?php echo ($i < 10) ? "0".$i : $i; ?></option>
                    <?php $i++; 
                     } ?>
                </select>
                <span style="float: left;">&nbsp;:&nbsp;</span>
                <select name="end_minute">
                    <?php foreach ($minutes as $min) { ?>
                    <option value="<?php echo $min; ?>"<?php if ($end_minute==$min) { echo " selected=\"selected\""; } ?>><?php echo $min; ?></option>
                    <?php } ?>
                </select>
                <select name="end_meridium" style="margin-left:5px">
                    <option value="AM"<?php if ($end_meridium=='AM') { echo " selected=\"selected\""; } ?>>AM</option>
                    <option value="PM"<?php if ($end_meridium=='PM') { echo " selected=\"selected\""; } ?>>PM</option>
                </select>
                <select name="end_day" style="margin-left:10px">
                    <?php for ($i = 1; $i <= 31; $i++) { ?>
                    <option value="<?php echo ($i < 10) ? '0'.$i : $i; ?>"<?php if ((int)$end_day == $i) { ?> selected="selected"<?php } ?>><?php echo ($i < 10) ? '0'.$i : $i; ?></option>
                    <?php } ?>
                </select>
                <span style="float: left;">&nbsp;/&nbsp;</span>
                <select name="end_month">
                    <?php for ($i = 1; $i <= 12; $i++) { ?>
                    <option value="<?php echo ($i < 10) ? '0'.$i : $i; ?>"<?php if ((int)$end_month == $i) { ?> selected="selected"<?php } ?>><?php echo ($i < 10) ? '0'.$i : $i; ?></option>
                    <?php } ?>
                </select>
                <span style="float: left;">&nbsp;/&nbsp;</span>
                <select name="end_year">
                    <?php for ($i = date("Y"); $i <= (date("Y") + 5); $i++) { ?>
                    <option value="<?php echo $i; ?>"<?php if ((int)$end_year == $i) { ?> selected="selected"<?php } ?>><?php echo $i; ?></option>
                    <?php } ?>
                </select>
            </div>
            
            <div class="row">
                <label>Rastrear Email</label>
                <input type="checkbox" name="trace_email" id="trace_email" value="1" />
            </div>
            
            <div class="row">
                <label>Rastrear Clicks</label>
                <input type="checkbox" name="trace_click" id="trace_click" value="1" />
            </div>
            
            <div class="row">
                <label>Incrustar Im&aacute;genes</label>
                <input type="checkbox" name="embed_image" id="embed_image" value="1" />
            </div>
            
            <div class="row">
                <label>Listas de Contactos</label>
                <?php if ($lists) { ?>
                <input type="text" title="Filtrar listado de categor&iacute;as" value="Ingresa el nombre de la lista" name="q" id="q" onfocus="this.value=''" />
                <div class="clear"></div>
                <label>&nbsp;</label>
                <ul id="listsWrapper" class="scrollbox" data-scrollbox="1">
                <?php foreach ($lists as $list) { ?>
                    <li>
                        <input type="checkbox" name="contact_list[]" value="<?php echo $list['contact_list_id']; ?>"<?php if (in_array($list['contact_list_id'], $contacts_list)) { ?> checked="checked"<?php } ?> showquick="off" />
                        <b><?php echo $list['name']; ?>&nbsp;&nbsp;(&nbsp;<?php echo $list['total_contacts']; ?>&nbsp;)</b>
                    </li>
                <?php } ?>
                </ul>
                <?php } else { ?>
                No hay listas de contactos registradas <a href="<?php echo $Url::createAdminUrl('marketing/list/insert'); ?>" title="<?php echo $l('Add Contact List'); ?>"><?php echo $l('Add Contact List'); ?></a>
                <?php } ?>
            </div>
            
            <div class="row">
                <label>Plantilla de Email</label>
                <?php if ($newsletters) { ?>
                <select name="newsletter_id">
                    <option value="">Seleccione</option>
                    <?php foreach ($newsletters as $newsletter) { ?>
                    <option value="<?php echo $newsletter['newsletter_id']; ?>"<?php if (in_array($newsletter['newsletter_id'], $templates)) { ?> selected="selected"<?php } ?>><?php echo $newsletter['name']; ?></option>
                    <?php } ?>
                </select>
                <a href="#" id="email_preview" title="Previsualizar Plantilla" style="margin-left: 10px;font-size: 10px;">[ Previsualizar ]</a>
                <?php } else { ?>
                No hay plantillas de email registradas <a href="<?php echo $Url::createAdminUrl('marketing/newsletter/insert'); ?>" title="<?php echo $l('Add Email Template'); ?>"><?php echo $l('Add Email Template'); ?></a>
                <?php } ?>
            </div>
            
            <div class="clear"></div><br />
        </form>
    </div>
</div>

<?php echo $footer; ?>