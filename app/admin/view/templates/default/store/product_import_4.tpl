<?php if ($error_warning) { ?><div class="warning"><?php echo $error_warning; ?></div><?php } ?>
    <div class="box">
        <h1>4. Informe</h1>
        <div class="buttons">
            <a onclick="location = '<?php echo $Url::createAdminUrl("store/product/import"); ?>'" class="button">Importar Nuevos</a>
            <a onclick="location = '<?php echo $Url::createAdminUrl("store/product"); ?>'" class="button">Volver a Productos</a>
        </div>
        
        <div class="clear"></div>
        
        <p>Productos Nuevos: <b id="new"><?php echo (int)$_GET['new']; ?></b></p>
        <p>Productos Actualizados: <b id="updated"><?php echo (int)$_GET['updated']; ?></b></p>
        <p>Productos Malos: <b id="bad"><?php echo (int)$_GET['bad']; ?></b></p>
        <p>Total de Productos: <b id="total"><?php echo (int)$_GET['total']; ?></b></p>
        
    </div>