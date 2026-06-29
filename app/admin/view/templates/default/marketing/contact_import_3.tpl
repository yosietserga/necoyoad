<?php if ($error_warning) { ?><div class="warning"><?php echo $error_warning; ?></div><?php } ?>
    <div class="box">
        <h1>4. Informe</h1>
        <div class="buttons">
            <a onclick="location = '<?php echo $Url::createAdminUrl("marketing/contact/import",array('menu'=>'mercadeo')); ?>'" class="button">Importar Nuevos</a>
            <a onclick="location = '<?php echo $Url::createAdminUrl("marketing/contact",array('menu'=>'mercadeo')); ?>'" class="button">Volver a Contactos</a>
        </div>
        
        <div class="clear"></div>
        
        <p>Contactos Nuevos: <b id="new"><?php echo (int)$_GET['new']; ?></b></p>
        <p>Contactos Actualizados: <b id="updated"><?php echo (int)$_GET['updated']; ?></b></p>
        <p>Contactos Malos: <b id="bad"><?php echo (int)$_GET['bad']; ?></b></p>
        <p>Total de Contactos: <b id="total"><?php echo (int)$_GET['total']; ?></b></p>
        
    </div>