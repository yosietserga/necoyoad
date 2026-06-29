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
    
    <div class="grid_12">
        <div class="box">
            <div class="header">
                <h1><?php echo $l('heading_title'); ?></h1>
                <div class="buttons">
                    <a onclick="$('.header').after('<img src=\'<?php echo str_replace('%theme%',$this->config->get('config_admin_template'),HTTP_ADMIN_THEME_IMAGE); ?>loader.gif\' alt=\'Actualizando...\' /><div class=\'message warning\'>Esto puede tardar unos minutos, no cancele ni cierre el navegador.</div>');$('#form').submit();" class="button">Actualizar</a>
                </div>
            </div>      
            <div class="clear"></div><br />

            <h3><?php echo $l('Current Version'); ?></h3>
            <?php echo VERSION; ?>
            <div class="clear"></div><br />

            <?php if (!empty($update_info)) { ?>

            <h3><?php echo $l('Lastest Version Available'); ?></h3>
            <?php echo $update_info['version']; ?>
            <div class="clear"></div><br />

            <h3><?php echo $l('Description'); ?></h3>
            <?php echo $update_info['description']; ?>
            <div class="clear"></div><br />

            <h3>Cambios Realizados (Changelog)</h3>
            <?php echo $update_info['changelog']; ?>
            <div class="clear"></div><br />

            <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">

                <input type="hidden" name="version" value="<?php echo $update_info['version']; ?>" />

            </form>
            <?php } else { ?>
            <h3><?php echo $l('Can\'t get update information!'); ?></h3>
            <p><?php echo $l('Please contact to support team for more information'); ?></p>
            <div class="clear"></div><br />

            <?php } ?>
        </div>
    </div>
</div>
<?php echo $footer; ?>