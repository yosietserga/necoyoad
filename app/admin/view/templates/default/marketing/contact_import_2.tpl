<?php if ($error_warning) { ?><div class="warning"><?php echo $error_warning; ?></div><?php } ?>
    <div class="box">
        <h1>2. Mapa del Archivo</h1>
        <div class="buttons">
            <a id="next" data-next="3" class="button">Importar Contactos</a>
            <a onclick="location = '<?php echo $Url::createAdminUrl("marketing/contact/import",array('menu'=>'mercadeo')); ?>'" class="button">Cancelar</a>
        </div>
        
        <div class="clear"></div>
        <h2 style="padding:10px;background:#900;color:#fff;border:solid 1px #000;" class="standout">Lee las Instrucciones!</h2>  
        <p>Por favor indica cu&aacute;les son los campos correspondientes que est&aacute;n asociados a los diferentes atributos de los contactos, por ejemplo "Nombre" -&gt; "Nombre del Prudcto". Esto permite que puedas subir cualquier archivo CSV con cualquier estrutura.</p>
        <p>Es obligatorio que indiques que campo corresponde al modelo del contacto, de lo contrario no se procesar&aacute;n los datos.</p>
        
        <div class="clear"></div>
                                    
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
        <?php if (isset($header)) { 
            foreach ($header as $column) { ?>
            <div class="row">
                <label><b><?php echo $column; ?></b> pertenece a:</label>
                <select title="Selecciona el archivo CSV que contiene la información de los contactos que se van a importar" name="Header[<?php echo str_replace(" ","_",trim($column)); ?>]" id="<?php echo str_replace(" ","_",trim($column)); ?>" showquick="off">
                    <option value="">Selecciona un campo</option>
                    <?php foreach ($fields as $group=>$fields2) { ?>
                    <optgroup label="<?php echo $group; ?>">
                        <?php foreach ($fields2 as $field=>$label) { ?>
                            <option value="<?php echo $field; ?>"><?php echo $label; ?></option>
                        <?php } ?>
                    </optgroup>
                    <?php } ?>
                </select>
            </div>
            <div class="clear"></div>
            <?php }  ?>
        <?php } else { ?>
            <p>No se detectaron cabeceras en el archivo.</p>
        <?php }  ?>
            
        </form>
    </div>
<script>
$(function(){
    $('#form').ntForm({
        lockButton:false,
        submitButton:false,
        cancelButton:false,
    });
    $('#next').on('click',function(e){
        var  hasEmail = false;
        $("select").each(function(){
            if ($(this).val() == 'email') hasEmail = true;
        });
        if (!hasEmail) {
            alert("Debes seleccionar un campo que corresponda al email del contacto o no podr\u00E1s continuar con el proceso");
        } else {
            $('#gridWrapper').hide();
            $('#gridPreloader').fadeIn();
            $.post('<?php echo $Url::createAdminUrl("marketing/contact/importprocess"); ?>&step=' + $('#next').attr('data-next'),
            $("#form").serialize(),
            function(data)
            {
                data = $.parseJSON(data);
                $('#gridWrapper').load('<?php echo $Url::createAdminUrl("marketing/contact/importwizard"); ?>&step=' + $('#next').attr('data-next') +'&new=' + data.nuevo +'&updated=' + data.updated +'&bad=' + data.bad +'&total=' + data.total,
                function()
                {
                    $('#gridPreloader').hide();
                    $('#gridWrapper').fadeIn();
                });
            });
        }
    });
});
</script>