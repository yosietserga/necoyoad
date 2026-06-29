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
        <h1>Configurar Correo Saliente</h1>
        <div class="buttons">
            <a onclick="saveAndExit();$('#form').submit();" class="button"><?php echo $l('button_save_and_exit'); ?></a>
            <a onclick="saveAndKeep();$('#form').submit();" class="button"><?php echo $l('button_save_and_keep'); ?></a>
            <a onclick="saveAndNew();$('#form').submit();" class="button"><?php echo $l('button_save_and_new'); ?></a>
            <a onclick="location = '<?php echo $cancel; ?>';" class="button"><?php echo $l('button_cancel'); ?></a>
        </div>
        
        <div class="clear"></div>
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
            <div>
                <table class="form">
                    <tr>
                        <td><?php echo $l('Username'); ?></td>
                        <td><input type="text" name="username" value="<?php echo $server['username']; ?>" /></td>
                    </tr>
                    <tr>
                        <td><?php echo $l('Password'); ?></td>
                        <td><input type="password" name="password" value="<?php echo $server['password']; ?>" /></td>
                    </tr>
                    <tr>
                        <td><?php echo $l('Host'); ?></td>
                        <td><input type="text" name="server" value="<?php echo $server['server']; ?>" /></td>
                    </tr>
                    <tr>
                        <td><?php echo $l('Port'); ?></td>
                        <td><input type="necoNumber" name="port" value="<?php echo $server['port']; ?>" /></td>
                    </tr>
                    <tr>
                        <td><?php echo $l('Security'); ?></td>
                        <td><select name="security" title="Ingrese el tama&ntilde;o m&aacute;ximo del email en Bytes, esto es utilizado para prevenir que se env&iacute;en emails con contenidos mayores a los permitidos">
                            <option value=""<?php if ($server['security'] == '') { ?> selected="selected"<?php } ?>>No SSL/TLS</option>
                            <option value="ssl"<?php if ($server['security'] == 'ssl') { ?> selected="selected"<?php } ?>>SSL</option>
                            <option value="tls"<?php if ($server['security'] == 'tls') { ?> selected="selected"<?php } ?>>TLS</option>
                          </select></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td><a class="button" data-test-connection="1"><?php echo $l('Test Connection'); ?></a></td>
                    </tr>
                </table>
            </div>
        </form>
    </div>
</div>
<script>
$(function(){
    $('a[data-test-connection]').on('click', function(){
        var that = this;
        if ($(this).attr('data-test-connection') == 1) {
            $(this).text('Loading').attr('data-test-connection',0);
            $.getJSON('<?php echo $Url::createAdminUrl('marketing/mailserver/testConnection'); ?>',{
               server:$('input[name=server]').val(),
               username:$('input[name=username]').val(),
               password:$('input[name=password]').val(),
               port:$('input[name=port]').val(),
               security:$('select[name=security]').val()
            }).done(function(data){
                if (data.error) {
                    $('#msg').html('<div class="message error"><?php echo $l('Cannot connect to the mail server, please check the data and try again'); ?></div>');
                } else {
                    $('#msg').html('<div class="message success"><?php echo $l('Connection Successful'); ?></div>');
                }
                $(that).text('Test Connection').attr('data-test-connection', 1);
            });
        }
    });
});
</script>
<?php echo $footer; ?>