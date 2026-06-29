<?php if ($error_warning) { ?><div class="warning"><?php echo $error_warning; ?></div><?php } ?>
    <div class="box">
        <h1>1. Selecciona las categor&iacute;as</h1>
        <div class="buttons">
            <a id="next" data-next="2" class="button">Siguiente</a>
            <a onclick="location = '<?php echo $Url::createAdminUrl("store/product/import"); ?>'" class="button">Cancelar</a>
        </div>
        
        <div class="clear"></div>
        <h2 style="padding:10px;background:#900;color:#fff;border:solid 1px #000;" class="standout">Lee las Instrucciones!</h2>  
        <p>Para obtener los resultados deseados, siga las siguientes instrucciones paso a paso:</p>
        <ol>
            <li>Seleccione el archivo que contiene la informaci&oacute;n a importar.<br /><span>El archivo debe ser creado con el formato CSV, de lo contrario no funcionar&aacute; correctamente la importaci&oacute;n.</span></li>
            <li>Si desea actualizar algunos o todos los productos, seleccione la opci&oacute;n "Actualizar Productos", de lo contrario ser&aacute;n ignorados los registros existentes.</li>
            <li>Ingrese el caracter que delimita los diferentes campos de los productos, por defecto se utiliza el punto y coma ( ; ) pero depender&aacute; del archivo que vaya a importar.</li>
            <li>Ingrese el caracter que encapsula los valores de los campos de los productos, por defecto se utiliza la doble comilla ( " ) pero depender&aacute; del archivo que vaya a importar.</li>
            <li>Ingrese el caracter que escapa los caracteres especiales en los valores de los campos de los productos, por defecto se utiliza la barra invertida ( \ ) pero depender&aacute; del archivo que vaya a importar.</li>
            <li>Si desea asociar los productos importados a algunas categor&iacute;as, seleccionelas en el listado inferior. Si no selecciona ninguna, los productos ser&aacute;n importados igual pero no ser&aacute;n asociados a ninguna categor&iacute;a.</li>
            <li>Haga click en Siguiente para continuar con la importaci&oacute;n.</li>
        </ol>
        <br />
        <p><b>NOTAS:</b></p>
        <ul style="list-style: circle;">
            <li>Si va a actualizar las opciones o las descripciones del producto, debe seleccionar "Actualizar Producto", de lo contrario el proceso generar&aacute; error.</li>
            <li>Si aun no has creado la categor&iacute;a, no selecciones ninguna y haz click en Siguiente, luego podr&aacute;s asignar los productos importados a las categor&iacute;as correspondientes.</li>
            <li>Los campos del archivo de formato CSV (Separado por Comas) pueden estar separados o delimitados por cualquier caracter, si se ha utilizado un caracter diferente a punto y coma (;), entonces ind&iacute;calo en el formulario.</li>
            <li>El archivo que se va a subir no debe ser mayor a 20MB y solo se aceptan archivos con extensiones .txt o .csv</li>
            <li>Un archivo de 20MB de informaci&oacute;n contiene un aproximado de 100.000,00 productos.</li>
            <li>Para una mejor importaci&oacute;n de los datos, por favor descarga la plantilla CSV <a href="#" title="Plantilla CSV para importar productos">Aqu&iacute;</a></li>
        </ul>
        
        <div class="clear"></div><br /><hr />
                           
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
   
            <div class="row">
                <label>Subir Archivo</label>
                <a class="button" id="preview" onclick="file_upload('file_to_import', 'preview');">Seleccionar Archivo</a>
                <input type="hidden" name="file" value="" id="file_to_import" />
                <br />
                <a onclick="file_upload('file_to_import', 'preview');" style="margin-left: 220px;color:#FFA500;font-size:10px">[ Cambiar ]</a>
                <a onclick="file_delete('file_to_import', 'preview');" style="color:#FFA500;font-size:10px">[ Quitar ]</a>
            </div>
                      
            <div class="clear"></div>
            
            <input type="checkbox" value="1" name="header" checked="checked" style="display:none;" showquick="off" />
            
            <div class="clear"></div>
            
            <div class="row">
                <label>Actualizar Productos</label>
                <input type="checkbox" title="Marca esta casilla si quieres actualizar los productos. Ten en cuenta que la informaci&oacute;n ser&aacute; sobreescrita" value="1" name="update" />
            </div>
                    
            <div class="clear"></div>
            
            <div class="row">
                <label>Caracter Separador</label>
                <input type="text" title="Ingresa el caracter utilizado en el archivo CSV para separar los datos" value=";" name="separator" style="width:20px" />
            </div>
                    
            <div class="clear"></div>
            
            <div class="row">
                <label>Caracter para Encapsular</label>
                <input type="text" title="Ingresa el caracter utilizado en el archivo CSV para encapsular datos complejos" value="" name="enclosure" style="width:20px" />
            </div>
                    
            <div class="clear"></div>
            
            <div class="row">
                <label>Caracter Escape</label>
                <input type="text" title="Ingresa el caracter utilizado en el archivo CSV para separar los datos" value="\" name="escape" style="width:20px" />
            </div>
                    
            <div class="clear"></div>
            
            <div class="row">
                <label>Filtrar categor&iacute;as por su nombre:</label>
                        <input type="text" title="Filtrar listado de categor&iacute;as" value="" name="q" id="q" />
                        <div class="clear"></div>
                        <a onclick="$('#categoriesWrapper :checkbox').attr('checked','checked');">Seleccionar Todos</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a onclick="$('#categoriesWrapper :checkbox').removeAttr('checked');">Ninguno</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a onclick="$('#categoriesWrapper :checkbox').each(function() { if ($(this).attr('checked')) { $(this).removeAttr('checked'); } else { $(this).attr('checked','checked'); } });">Alternar</a>
                        <ul id="categoriesWrapper" class="scrollbox" data-scrollbox="1">
                            <?php foreach ($categories as $category) { ?>
                                <li class="categories">
                                    <input title="<?php echo $l('help_category'); ?>" type="checkbox" name="product_category[]" value="<?php echo $category['category_id']; ?>"<?php if (in_array($category['category_id'], $product_category)) { ?> checked="checked"<?php } ?> showquick="off" />
                                    <b><?php echo $category['name']; ?></b>
                                
                                </li>
                            <?php } ?>
                        </ul>
            </div>
                    
            <div class="clear"></div>
                 
        </form>
    </div>
<script>
$('#form').ntForm({
    lockButton:false,
    submitButton:false,
    cancelButton:false,
});
$('#next').on('click',function(e)
{
    if (validateForm()) {
        $('#gridWrapper').hide();
        $('#gridPreloader').fadeIn();
        $.post('<?php echo $Url::createAdminUrl("store/product/importprocess"); ?>&step=' + $('#next').attr('data-next'),
            $("#form").serialize(),
            function(data)
            {
                $('#gridWrapper').load('<?php echo $Url::createAdminUrl("store/product/importwizard"); ?>&step=' + $('#next').attr('data-next'),
                function()
                {
                    $('#gridPreloader').hide();
                    $('#gridWrapper').fadeIn();
                });
        });
    }
});
function validateForm() {
    var fileCsv = $("#file_to_import").val();
    if (fileCsv.length == 0) {
        return false;
    }
    return true;
}
</script>