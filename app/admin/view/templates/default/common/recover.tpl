<?php echo $header; ?>
<div id="login">
    <h1><a href="https://www.necotienda.com/" title="NecoTienda.com"><img src="images/logo.png" alt="NecoTienda" /></a></h1>
    <?php if (!empty($error_warning)) { ?><div class="message warning"><?php echo $error_warning; ?></div><?php } ?>
    <div class="box">
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
            <label><?php echo $l('entry_username'); ?></label>
            <input type="text" name="username" value="" autocomplete="off" />
            
            <div class="clear"></div>
            
            <label><?php echo $l('entry_email'); ?></label>
            <input type="email" name="email" value="" autocomplete="off" />
            
            <div class="clear"></div>
            
            <a onclick="$('#form').submit();" class="button"><?php echo $l('button_submit'); ?></a>
            
            <div class="clear"></div>
            <a href="<?php echo $Url::createAdminUrl("common/login"); ?>"><?php echo $l('text_back'); ?></a>
        </form>
    </div>
</div>
<?php echo $footer; ?> 