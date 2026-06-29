<?php echo $header; ?>
<div id="login">
    <h1><a href="https://www.necotienda.com/" title="NecoTienda.com"><img src="images/logo.png" alt="NecoTienda" /></a></h1>
    <?php if (!empty($error_warning)) { ?><div class="message warning"><?php echo $error_warning; ?></div><?php } ?>
    <div class="box">
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="formLogin">
            <label><?php echo $l('entry_username'); ?></label>
            <input type="text" name="username" value="<?php echo $username??""; ?>" showquick="off" autofocus="autofocus" />
            
            <div class="clear"></div>
            
            <label><?php echo $l('entry_password'); ?></label>
            <input type="password" name="password" value="<?php echo $password??""; ?>" showquick="off" />
            
            <div class="clear"></div>
            
            <a onclick="submit();" class="button"><?php echo $l('button_login'); ?></a>
            
            <div class="clear"></div>
            <a href="<?php echo $Url::createAdminUrl("common/login/recover"); ?>" title="Haga click aqu&iacute; si olvid&oacute; su contrase&ntilde;a">&iquest;Olvid&oacute; su contrase&ntilde;a?</a>
            <input type="hidden" name="fid" value="<?php echo $fid; ?>" />
            <?php if ($redirect) { ?>
            <input type="hidden" name="redirect" value="<?php echo $redirect; ?>" />
            <?php } ?>
        </form>
    </div>
</div>
<script>
$(function(){
    $('#formLogin input').keydown(function(e) {
        if (e.keyCode === 13) {
            submit();
        }
    });
});

function submit() {
    if(window.$) {
        $('#formLogin .button').before('<div id="loading" style="margin:5px auto;width:210px;"><img src="<?php echo str_replace('%theme%',$this->config->get('config_admin_template'),HTTP_ADMIN_THEME_IMAGE); ?>loader.gif" alt="cargando..." /><div>');

        $.post('<?php echo $Url::createAdminUrl("common/login/login"); ?>',
        {
            username:$('input[name=username]').val(),
            password:$('input[name=password]').crypt({method:'md5'}),
            /*//TODO: create common encrypt function */
            redirect:$('input[name=redirect]').val(),
            fid:'<?php echo $this->session->get('fid'); ?>'
        })
        .done(function(response){
            data = $.parseJSON(response);
            if (typeof data.success !== 'undefined') {
                window.location.href = data.redirect;
            } else {
                if (typeof $.ui !== 'undefined') {
                $('#formLogin').effect('shake');
            }
            }
            $('#loading').remove();
        });
        return false;
    } else {
        document.forms['formLogin'].submit();
    }
}
</script>
<?php echo $footer; ?> 