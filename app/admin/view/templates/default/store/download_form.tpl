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
        
        <ol id="stepsForm" class="joyRideTipContent" style="display:none">
            <li data-button="<?php echo $l('button_next'); ?>">
                <h2><?php echo $l('heading_joyride_begin'); ?></h2>
                <p><?php echo $l('help_joyride_begin'); ?></p>
            </li>
            <li data-button="<?php echo $l('button_next'); ?>">
                <h2><?php echo $l('heading_joyride_01'); ?></h2>
                <p><?php echo $l('help_joyride_01'); ?></p>
            </li>
            <li data-class="htabs" data-button="<?php echo $l('button_next'); ?>" data-options="tipLocation:right">
                <h2><?php echo $l('heading_joyride_02'); ?></h2>
                <p><?php echo $l('help_joyride_02'); ?></p>
            </li>
            <li data-class="necoName" data-button="<?php echo $l('button_next'); ?>" data-options="tipLocation:right">
                <h2><?php echo $l('heading_joyride_03'); ?></h2>
                <p><?php echo $l('help_joyride_03'); ?></p>
            </li>
            <li data-class="necoImage" data-button="<?php echo $l('button_next'); ?>" data-options="tipLocation:right">
                <h2><?php echo $l('heading_joyride_04'); ?></h2>
                <p><?php echo $l('help_joyride_04'); ?></p>
            </li>
            <li data-class="necoQuantity" data-button="<?php echo $l('button_next'); ?>" data-options="tipLocation:right">
                <h2><?php echo $l('heading_joyride_05'); ?></h2>
                <p><?php echo $l('help_joyride_05'); ?></p>
            </li>
            <li data-class="necoStore" data-button="<?php echo $l('button_next'); ?>" data-options="tipLocation:right">
                <h2><?php echo $l('heading_joyride_06'); ?></h2>
                <p><?php echo $l('help_joyride_06'); ?></p>
            </li>
            <li data-button="<?php echo $l('button_close'); ?>">
                <h2><?php echo $l('heading_joyride_final'); ?></h2>
                <p><?php echo $l('help_joyride_final'); ?></p>
            </li>
        </ol>
        <script>
            $(function(){
                $('#necoBoy').on('click', function(e){
                    $('#stepsForm').joyride({
                        autoStart : true,
                        modal:false,
                        expose:true
                    });
                });
            });
        </script>
        
        <div class="clear"></div>
        
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
            <div id="languages" class="htabs">
                <?php foreach ($languages as $language) { ?>
                    <a tab="#language<?php echo $language['language_id']; ?>" class="htab"><img src="images/flags/<?php echo $language['image']; ?>" title="<?php echo $language['name']; ?>" /> <?php echo $language['name']; ?></a>
                <?php } ?>
                <?php foreach ($languages as $language) { ?>
                    <div id="language<?php echo $language['language_id']; ?>">
                    
                        <div class="row">
                            <label><?php echo $l('entry_name'); ?></label>
                            <input id="descriptions<?php echo $language['language_id']; ?>_name" name="descriptions[<?php echo $language['language_id']; ?>][title]" value="<?php echo isset($descriptions[$language['language_id']]) ? $descriptions[$language['language_id']]['title'] : ''; ?>" required="true" style="width:40%" class="necoName" />
                        </div>
                        
                    </div>
            <?php } ?>
            </div>
            
            <div class="clear"></div>
            
            <div class="row">
                <label><?php echo $l('entry_filename'); ?></label>
                <?php if (!empty($filename)) { ?>
                <input type="text" id="preview" value="<?php echo str_replace("data/","",$filename); ?>" disabled="disabled" style="width:40%" />
                <input type="hidden" name="download" id="download" value="<?php echo $filename; ?>" />
                <div class="clear"></div>
                <?php } else { ?>
                <a class="button necoImage" id="preview" onclick="file_upload('download', 'preview');">Seleccionar Archivo</a>
                <input type="hidden" name="download" id="download" value="" />
                <?php } ?>
                <br />
                <a onclick="file_upload('download', 'preview');" style="margin-left: 220px;color:#FFA500;font-size:10px">[ Cambiar ]</a>
                <a onclick="file_delete('download', 'preview');" style="color:#FFA500;font-size:10px">[ Quitar ]</a>
            </div>
            
            <div class="clear"></div>
            
            <div class="row">
                <label><?php echo $l('entry_remaining'); ?></label>
                <input type="necoNumber" name="remaining" id="remaining" value="<?php echo $remaining; ?>" title="<?php echo $l('help_remaining'); ?>" size="6" class="necoQuantity" />
            </div>
            
            <div class="clear"></div>
            
            <?php if ($show_update) { ?>
            <div class="row">
                <label><?php echo $l('entry_update'); ?></label>
                <input title="<?php echo $l('help_update'); ?>" type="checkbox" name="update" value="1"<?php if ($update) { ?> checked="checked"<?php } ?> />
            </div>
            
            <div class="clear"></div>
            <?php } ?>
                   
            <?php if ($stores) { ?>
            <div class="clear"></div>
            <div class="row">
                <label><?php echo $l('entry_store'); ?></label><br />
                <input type="text" title="Filtrar listado de tiendas y sucursales" value="" name="q" id="q" placeholder="Filtrar Tiendas" />
                <div class="clear"></div>
                <a onclick="$('#storesWrapper input[type=checkbox]').attr('checked','checked');">Seleccionar Todos</a>&nbsp;&nbsp;&nbsp;&nbsp;
                <a onclick="$('#storesWrapper input[type=checkbox]').removeAttr('checked');">Seleccionar Ninguno</a>
                <div class="clear"></div>
                <ul id="storesWrapper" class="scrollbox necoStore">
                    <li class="stores">
                        <input id="scrollboxStores0" type="checkbox" name="stores[]" value="0"<?php if (in_array(0, $_stores)) { ?> checked="checked"<?php } ?> showquick="off" />
                        <label for="scrollboxStores0"><?php echo $l('text_default'); ?></label>
                        <div class="clear"></div>
                    </li>
                <?php foreach ($stores as $store) { ?>
                    <li class="stores">
                        <input id="scrollboxStores<?php echo (int)$store['store_id']; ?>" type="checkbox" name="stores[]" value="<?php echo (int)$store['store_id']; ?>"<?php if (in_array($store['store_id'], $_stores)) { ?> checked="checked"<?php } ?> showquick="off" />
                        <label for="scrollboxStores<?php echo (int)$store['store_id']; ?>"><?php echo $store['name']; ?></label>
                        <div class="clear"></div>
                    </li>
                <?php } ?>
                </ul>
            </div> 
            <?php } else { ?>
                <input type="hidden" name="stores[]" value="0" />
            <?php } ?>
            
            <div class="clear"></div><br />
        </form>
    </div>
</div>
<?php echo $footer; ?>