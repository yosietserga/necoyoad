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
    <div class="grid_12" id="menuMsg"></div>
    
    <div class="grid_12">
        <div class="box">
                        
            <h1><?php echo $l('heading_title'); ?></h1>
            <div class="buttons">
                <a onclick="$('#menuItems').submit();" class="button"><?php echo $l('button_save'); ?></a>
                <a onclick="location = '<?php echo $cancel; ?>';" class="button"><?php echo $l('button_cancel'); ?></a>
            </div>
        </div>
    </div>

    <div class="clear"></div>
        
    <div class="grid_4">
        <div class="box">
            <h2>Datos del Men&uacute;</h2>
            <form action="<?php echo $action; ?>" class="neco-form" id="formMenu">
                <div class="row">
                    <label class="neco-label">Nombre:</label>
                    <input id="_name" name="_name" value="<?php echo $name; ?>" style="width:140px" />
                </div>
                
                <div class="clear"></div>
                   
                <div class="row">
                    <label class="neco-label">Predeterminado:</label>
                    <input type="checkbox" id="_default" name="_default" value="1" onclick="$('#default').attr('checked', this.checked);"<?php if ($default) { echo ' checked="checked"'; }?> />
                </div>
                
                <?php if ($stores) { ?>
                <div class="clear"></div>
                <div class="row">
                    <label><?php echo $l('entry_store'); ?></label><br />
                    <input type="text" title="Filtrar listado de tiendas y sucursales" value="" name="q" id="q" placeholder="Filtrar Tiendas" />
                    <div class="clear"></div>
                    <a onclick="$('#storesWrapper input[type=checkbox]').each(function(){ $(this).attr('checked','checked').trigger('change'); })">Seleccionar Todos</a>&nbsp;&nbsp;&nbsp;&nbsp;
                    <a onclick="$('#storesWrapper input[type=checkbox]').each(function(){ $(this).removeAttr('checked').trigger('change'); })">Seleccionar Ninguno</a>
                    <div class="clear"></div>
                    <ul id="storesWrapper" class="scrollbox" data-scrollbox="1">
                        <li class="stores" onclick="$('#store0').attr('checked', !$('#_store0').attr('checked'));">
                            <input 
                                type="checkbox" 
                                id="_store0" 
                                value="0"
                                <?php if (in_array(0, $_stores)) { ?> checked="checked"<?php } ?> 
                                onchange="$('#store0').attr('checked', this.checked);"
                                showquick="off" 
                            />
                            <label for="_store0"><?php echo $l('text_default'); ?></label>
                            <div class="clear"></div>
                        </li>
                    <?php foreach ($stores as $store) { ?>
                        <li class="stores" onclick="$('#store<?php echo (int)$store['store_id']; ?>').attr('checked', !$('#_store<?php echo (int)$store['store_id']; ?>').attr('checked'));">
                            <input 
                                type="checkbox" 
                                id="_store<?php echo (int)$store['store_id']; ?>" 
                                value="<?php echo (int)$store['store_id']; ?>"
                                <?php if (in_array($store['store_id'], $_stores)) { ?> checked="checked"<?php } ?> 
                                onchange="$('#store<?php echo (int)$store['store_id']; ?>').attr('checked', this.checked);"
                                showquick="off"
                            />
                            <label for="_store<?php echo (int)$store['store_id']; ?>"><?php echo $store['name']; ?></label>
                            <div class="clear"></div>
                        </li>
                    <?php } ?>
                    </ul>
                </div> 
                <?php } else { ?>
                    <input type="hidden" value="0" />
                <?php } ?>
                
            </form>
        </div>
            
        <div class="clear"></div>
            
        <div class="box neco-form">
            <h2>Enlaces (URL)</h2>
            <div class="row">
                <label class="neco-label">Enlace</label>
                <input id="external_link" name="external_link" value="" style="width:40%" />
            </div>
            <div class="row">
                <label class="neco-label">Etiqueta</label>
                <input id="external_tag" name="external_tag" value="" style="width:40%" />
            </div>
            <div class="clear"></div>
            <a class="button" onclick="addLink('<?php echo $_GET['token']; ?>')">Agregar al men&uacute;</a>
        </div>
            
        <div class="clear"></div>
            
        <?php if ($pages) { ?>
        <div class="box">
            <h2>P&aacute;ginas</h2>
            <input name="qp" id="qp" value="Buscar..." onfocus="if (this.value=='Buscar...') {this.value=''}" onchange="ntSearch(this.value,'pagesWrapper')" />
            <div class="row">
                <ul id="pagesWrapper" class="scrollbox" data-scrollbox="1" style="width:96%;">
                    <?php foreach($pages as $page) { ?>
                    <li>
                        <input id="scrollboxPages<?php echo $page['post_id']; ?>" type="checkbox" name="pages[]" value="<?php echo $page['post_id']; ?>" />
                        <label for="scrollboxPages<?php echo $page['post_id']; ?>"><?php echo $page['title']; ?></label>
                    </li>
                    <?php } ?>
                        
                </ul>
            </div>
            <div class="clear"></div>
            <a class="button" onclick="addPage('<?php echo $_GET['token']; ?>')">Agregar al men&uacute;</a>
        </div>
            
        <div class="clear"></div>
        <?php } ?>
            
        <?php if ($categories) { ?>
        <div class="box">
            <h2>Categor&iacute;as de Productos</h2>
            <input name="qc" id="qc" value="Buscar..." onfocus="if (this.value=='Buscar...') {this.value=''}" onchange="ntSearch(this.value,'categoriesWrapper')" />
            <div class="row">
                <ul id="categoriesWrapper" class="scrollbox" data-scrollbox="1" style="width:96%;"><?php echo $categories; ?></ul>
            </div>
            <div class="clear"></div>
            <a class="button" onclick="addCategory('<?php echo $_GET['token']; ?>')">Agregar al men&uacute;</a>
        </div>
            
        <div class="clear"></div>
        <?php } ?>
        
        <?php if ($post_categories) { ?>
        <div class="box">
            <h2>Categor&iacute;as de Art&iacute;culos</h2>
            <input name="qpc" id="qpc" value="Buscar..." onfocus="if (this.value=='Buscar...') {this.value=''}" onchange="ntSearch(this.value,'post_categoriesWrapper')" />
            <div class="row">
                <ul id="post_categoriesWrapper" class="scrollbox" data-scrollbox="1" style="width:96%;"><?php echo $post_categories; ?></ul>
            </div>
            <div class="clear"></div>
            <a class="button" onclick="addPostCategory('<?php echo $_GET['token']; ?>')">Agregar al men&uacute;</a>
        </div>
            
        <div class="clear"></div>
        <?php } ?>
            
    </div>

    <div class="grid_8">
        <div class="box">
            <h2>Enlaces del Men&uacute;</h2>
            <form action="<?php echo str_replace('&','&amp;',$action); ?>" class="neco-form" id="menuItems" method="post">
            <input type="hidden" id="name" name="name" value="<?php echo $name; ?>" style="width:140px" />
            <input type="checkbox" id="default" name="default" value="1" style="display: none;"<?php if ($default) echo ' checked="checked"'; ?> />
            <?php if ($stores) { ?>
            <input type="checkbox" name="stores[]" value="0"<?php if (in_array(0, $_stores)) { ?> checked="checked"<?php } ?> id="store0" style="display: none;" />
            <?php foreach ($stores as $store) { ?>
            <input type="checkbox" name="stores[]" value="<?php echo $store['store_id']; ?>"<?php if (in_array($store['store_id'], $_stores)) { ?> checked="checked"<?php } ?> id="store<?php echo $store['store_id']; ?>" style="display: none;" />
            <?php } ?>
            <?php } else { ?>
            <input type="hidden" name="stores[]" value="0" />
            <?php } ?>
                <ol class="items">
                    <?php if (isset($links)) echo $links; ?>
                </ol>
            </form>
        </div>
    </div>
</div>
<script type="text/javascript">
    <?php
    $cssrules = "assets/theme/". ($Config->get('config_template') ? $Config->get('config_template') : 'choroni') ."/css/theme.css";
    $contentsCss = '';
    if (file_exists(DIR_ROOT . $cssrules)) {
        $contentsCss = HTTP_CATALOG . $cssrules;
    }
    ?>
    
    const cssEditorConfig = {
        filebrowserBrowseUrl: '<?php echo $Url::createAdminUrl("common/filemanager"); ?>',
        filebrowserImageBrowseUrl: '<?php echo $Url::createAdminUrl("common/filemanager"); ?>',
        filebrowserFlashBrowseUrl: '<?php echo $Url::createAdminUrl("common/filemanager"); ?>',
        filebrowserUploadUrl: '<?php echo $Url::createAdminUrl("common/filemanager"); ?>',
        filebrowserImageUploadUrl: '<?php echo $Url::createAdminUrl("common/filemanager"); ?>',
        filebrowserFlashUploadUrl: '<?php echo $Url::createAdminUrl("common/filemanager"); ?>',
        height:100,
        allowedContent: true,
        contentsCss:'<?php echo $contentsCss; ?>'
    };
</script>
<?php echo $footer; ?>