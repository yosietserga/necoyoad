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
        <h1><?php echo $l('heading_title'); ?></h1>
        <div class="buttons">
            <a id="necoBoy" style="margin: 0px 10px;" title="NecoBoy ay&uacute;dame!"><img src="<?php echo HTTP_IMAGE; ?>necoBoy.png" alt="NecoBoy" /></a>
            <a onclick="saveAndExit();$('#form').submit();" class="button"><?php echo $l('button_save_and_exit'); ?></a>
            <a onclick="saveAndKeep();$('#form').submit();" class="button"><?php echo $l('button_save_and_keep'); ?></a>
            <a onclick="saveAndNew();$('#form').submit();" class="button"><?php echo $l('button_save_and_new'); ?></a>
            <a onclick="location = '<?php echo $cancel; ?>';" class="button"><?php echo $l('button_cancel'); ?></a>
        </div>
        
        <div class="clear"></div>
                                
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
            <div class="row">
                <label>Nombre del Grupo de Atributos:</label>
                <input class="category" id="name" name="name" value="<?php echo $name ?? ""; ?>" required="true" style="width:40%" />
            </div>
            
            <div class="clear"></div>
                        
            <div class="row">
                <label>Seleccione las categor&iacute;as asociadas:</label>
                <input type="text" title="Filtrar listado de categor&iacute;as" placeholder="Filtrar Categorias" value="" name="q" id="q" />
                <div class="clear"></div>
                <ul id="categoriesWrapper" class="scrollbox">
                    <?php if (isset($categories) && is_array($categories)) { ?>
                    <?php foreach ($categories as $category) { ?>
                    <li class="categories">
                        <input title="<?php echo $l('help_category'); ?>" type="checkbox" name="categories[]" value="<?php echo $category['category_id']; ?>"<?php if (in_array($category['category_id'], $object_category)) { ?> checked="checked"<?php } ?> showquick="off" />
                        <b><?php echo $category['title']; ?></b>
                    </li>
                    <?php } //end foreach ?>
                    <?php } //end if ?>
                </ul>
            </div>
                    
            <div class="clear"></div><br />
            
            <div>
                <table id="special" class="list">
                    <thead>
                        <tr>
                            <th>Tipo</th>
                            <th>Etiqueta del Atributo</th>
                            <th>Nombre del Atributo</th>
                            <th>Valor Predeterminado</th>
                            <th>Requerido</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="rows">
                    <?php if (isset($model_info['attributes']) && is_array($model_info['attributes'])) { ?>
                    <?php foreach ($model_info['attributes'] as $row => $property) { ?>
                        <tr>
                            <td>
                                <select name="Properties[<?php echo $row; ?>][type]" showquick="off">
                                    <option value="text"<?php if ($property['type'] == 'text') { echo ' selected="selected"'; } ?>>Texto</option>
                                    <option value="checkbox"<?php if ($property['type'] == 'checkbox') { echo ' selected="selected"'; } ?>>Checkbox</option>
                                    <option value="radio"<?php if ($property['type'] == 'radio') { echo ' selected="selected"'; } ?>>Radio</option>
                                    <option value="email"<?php if ($property['type'] == 'email') { echo ' selected="selected"'; } ?>>Email</option>
                                    <option value="necoNumber"<?php if ($property['type'] == 'necoNumber') { echo ' selected="selected"'; } ?>>N&uacute;mero</option>
                                    <option value="necoDate"<?php if ($property['type'] == 'necoDate') { echo ' selected="selected"'; } ?>>Fecha (dd/mm/yyyy)</option>
                                    <option value="password"<?php if ($property['type'] == 'password') { echo ' selected="selected"'; } ?>>Contrase&ntilde;a</option>
                                </select>
                            </td>
                            <td><input type="text" name="Properties[<?php echo $row; ?>][label]" id="label_<?php echo $row; ?>" value="<?php echo $property['label']; ?>" placeholder="Etiqueta del Atributo" showquick="off" /></td>
                            <td><input type="text" name="Properties[<?php echo $row; ?>][name]" id="name_<?php echo $row; ?>" value="<?php echo $property['name']; ?>" placeholder="Nombre del Atributo" showquick="off" /></td>
                            <td><input type="text" name="Properties[<?php echo $row; ?>][default]" id="default_<?php echo $row; ?>" value="<?php echo $property['default']; ?>" placeholder="Valor Predeterminado" showquick="off" /></td>
                            <td><input type="checkbox" name="Properties[<?php echo $row; ?>][required]" id="required_<?php echo $row; ?>" value="1" title="Campo Obligatorio"<?php if ($property['required']) { echo ' checked="checked"'; } ?> showquick="off" /></td>
                            <td><a onclick="$(this).closest('tr').remove();" class="button"><?php echo $l('button_remove'); ?></a>
                        <script>
                        $(function(){
                            $('#label_<?php echo $row; ?>').on('change',function(event) {
                                $.getJSON('<?php echo $Url::createAdminUrl("common/home/slug") ."&language_id=". $Config->get('config_language_id'); ?>&slug='+ $(this).val(),
                                function(response) {
                                    $('#name_<?php echo $row; ?>').val(response.slug);
                                });
                            });
                        });
                        </script></td>
                        </tr>
                        <?php } //end foreach ?>
                        <?php } //end if ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="5"></td>
                            <td><a onclick="addSpecial();" class="button">Agregar Atributo</a></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <script type="text/javascript">
            function addSpecial() {
                var row = ($('#rows tr:last-child').index() + 1);
                var html = "";
            	html += '<tr>';
                html += '<td><select name="Properties['+ row +'][type]">';
                html += '<option value="text">Texto</option>';
                html += '<option value="checkbox">Checkbox</option>';
                html += '<option value="radio">Radio</option>';
                html += '<option value="email">Email</option>';
                html += '<option value="number">N&uacute;mero</option>';
                html += '<option value="date">Fecha (dd/mm/yyyy)</option>';
                html += '<option value="password">Contrase&ntilde;a</option>';
                html += '</select></td>';
            	html += '<td><input type="text" name="Properties['+ row +'][label]" id="label_'+ row +'" value="" placeholder="Etiqueta del Atributo"></td>';
            	html += '<td><input type="text" name="Properties['+ row +'][name]" id="name_'+ row +'" value="" placeholder="Nombre del Atributo"></td>';
            	html += '<td><input type="text" name="Properties['+ row +'][default]" id="default_'+ row +'" value="" placeholder="Valor Predeterminado"></td>';
            	html += '<td><input type="checkbox" name="Properties['+ row +'][required]" id="required_'+ row +'" value="1" title="Campo Obligatorio"></td>';
            	html += '<td><a onclick="$(this).closest(\'tr\').remove();" class="button"><?php echo $l('button_remove'); ?></a></td>';
            	html += '</tr>';
            	$('#rows').append(html);
                $('#label_'+ row).on('change',function(event) {
                    $.getJSON('<?php echo $Url::createAdminUrl("common/home/slug") ."&language_id=". $Config->get('config_language_id'); ?>&slug='+ $(this).val(),
                    function(response) {
                        $('#name_'+ row).val(response.slug);
                    });
                });
            }
            </script>             
        </form>
    </div>
</div>
<?php echo $footer; ?>