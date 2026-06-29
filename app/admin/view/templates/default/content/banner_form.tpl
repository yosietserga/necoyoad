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
            <a onclick="saveAndExit();$('#form').submit();" class="button"><?php echo $l('button_save_and_exit'); ?></a>
            <a href="../../../controller/content/banner.php"></a>
            <a onclick="saveAndKeep();$('#form').submit();" class="button"><?php echo $l('button_save_and_keep'); ?></a>
            <a onclick="saveAndNew();$('#form').submit();" class="button"><?php echo $l('button_save_and_new'); ?></a>
            <a onclick="location = '<?php echo $cancel; ?>';" class="button"><?php echo $l('button_cancel'); ?></a>
        </div>
        
        <div class="clear"></div>

            <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">

                <div class="row">
                    <label><?php echo $l('entry_name'); ?></label>
                    <input type="text" name="name" value="<?php echo $name; ?>" required="true" style="width:40%" />
                    <?php if ($error_name) { ?><span class="error"><?php echo $error_name; ?></span><?php } ?>
                </div>

                <div class="clear"></div>

                <div class="row">
                    <label><?php echo $l('entry_date_start'); ?></label>
                    <input type="necoDate" name="publish_date_start" value="<?php echo !empty($publish_date_start) ? date('d-m-Y',strtotime($publish_date_start)) : date('d/m/Y'); ?>" style="width:40%" />
                </div>

                <div class="clear"></div>

                <div class="row">
                    <label><?php echo $l('entry_date_end'); ?></label>
                    <input type="necoDate" name="publish_date_end" value="<?php echo isset($publish_date_end) ? $publish_date_end : ''; ?>" style="width:40%" />
                </div>

                <div class="clear"></div><br />

                <div class="row">
                    <label><?php echo $l('entry_engine'); ?></label>
                    <select name="jquery_plugin" style="width:40%">
                        <option value="0"><?php echo $l('text_none'); ?></option>
                        <?php foreach ($sliders as $slider) { ?>
                        <option value="<?php echo $slider; ?>"<?php if ($jquery_plugin == $slider) {?> selected="selected"<?php } ?>><?php echo $slider; ?></option>
                        <?php } ?>
                   </select>
                </div>

                <div class="clear"></div>

                <?php if ($stores) { ?>
                <div class="clear"></div>
                <div class="row">
                    <label><?php echo $l('entry_store'); ?></label><br />
                    <input type="text" title="Filtrar listado de tiendas y sucursales" value="" name="q" id="q" placeholder="Filtrar Tiendas" />
                    <div class="clear"></div>
                    <a onclick="$('#storesWrapper input[type=checkbox]').attr('checked','checked');">Seleccionar Todos</a>&nbsp;&nbsp;&nbsp;&nbsp;
                    <a onclick="$('#storesWrapper input[type=checkbox]').removeAttr('checked');">Seleccionar Ninguno</a>
                    <div class="clear"></div>
                    <ul id="storesWrapper" class="scrollbox" data-scrollbox="1">
                        <li class="stores">
                            <input id="scrollboxStores0" type="checkbox" name="stores[]" value="0"<?php if (in_array(0, $banner_stores)) { ?> checked="checked"<?php } ?> showquick="off" />
                            <label for="scrollboxStore0"><?php echo $l('text_default'); ?></label>
                            <div class="clear"></div>
                        </li>
                    <?php foreach ($stores as $store) { ?>
                        <li class="stores">
                            <input id="scrollboxStores<?php echo (int)$store['store_id']; ?>" type="checkbox" name="stores[]" value="<?php echo (int)$store['store_id']; ?>"<?php if (in_array($store['store_id'], $banner_stores)) { ?> checked="checked"<?php } ?> showquick="off" />
                            <label for="scrollboxStores<?php echo (int)$store['store_id']; ?>"><?php echo $store['name']; ?></label>
                            <div class="clear"></div>
                        </li>
                    <?php } ?>
                    </ul>
                </div> 
                <?php } else { ?>
                    <input type="hidden" name="stores[]" value="0" />
                <?php } ?>






















                <div>
                    <div class="clear"></div><hr />

                    <?php if (!$banner_id) { ?>
                    <div class="warning"><?php echo $l('You must save the banner first to set the slides'); ?></div>
                    <?php } else { ?>
                    <ul id="vtabs" class="vtabs grid_2">

                        <?php foreach ($banner_items as $key => $banner_item) { ?>
                        <li id="slide_<?php echo $key; ?>" class="vtab" data-banner_id="<?php echo $banner_id; ?>" data-banner_item_id="<?php echo $banner_item['banner_item_id']; ?>" onclick="loadSlideSettings('<?php echo $banner_id; ?>', '<?php echo $banner_item['banner_item_id']; ?>', this)">
                            <a data-target="#slide_row<?php echo $key; ?>">
                                <?php echo empty($banner_item['properties']['slidename']) ? 'Slide '. $key : $banner_item['properties']['slidename']; ?>        
                                <span title="Eliminar" onclick="removeSlide('slide_<?php echo $key; ?>')" class="remove">&nbsp;</span>
                            </a>
                        </li>
                        <?php } ?>

                        <li onclick="addRow(this);">
                            <a class="button"><?php echo $l('Add Slide'); ?></a>
                        </li>
                    </ul>

                    <div data-slider-settings>
                        
                        <div class="grid_3">

                            <div class="htabs2">
                                <a tab="#slide_form_background" class="htab2"><?php echo $l('Bg'); ?></a>
                                <a tab="#slide_form_contents" class="htab2"><?php echo $l('Contents'); ?></a>
                                <a tab="#slide_form_preview" class="htab2"><?php echo $l('Preview'); ?></a>
                            </div>

                            <div id="slide_form_background">

                                <div class="row">
                                    <input id="SlideNameInput" name="properties[slidename]" value="" onchange="updateSlide($('[name=banner_id]').val(), $('[name=banner_item_id]').val());" />
                                </div>

                                <div class="row">
                                    <input type="hidden" name="image" value="" id="image" onchange="$('[data-background]').attr({ src:'<?php echo HTTP_IMAGE; ?>'+ this.value });updateSlide($('[name=banner_id]').val(), $('[name=banner_item_id]').val())" showquick="off" />
                                    <input type="hidden" name="banner_id" value="" id="banner_id" showquick="off" />
                                    <input type="hidden" name="banner_item_id" value="" id="banner_item_id" showquick="off" />
                                    
                                    <a class="filemanager" data-fancybox-type="iframe" href="<?php echo $Url::createAdminUrl("common/filemanager"); ?>&amp;field=image&amp;preview=preview">
                                        <img src="<?php echo $NTImage::resizeAndSave('no_image.jpg', 100, 100); ?>" id="preview" class="image" width="100" />
                                    </a>

                                    <br />

                                    <a onclick="image_delete('image', 'preview');" style="color:#FFA500;font-size:10px">[ Quitar ]</a>
                                </div>

                                <div class="clear"></div>
                                <h2><?php echo $l('Transition In'); ?></h2>
                                <hr />

                                <div class="row">
                                    <label for="SettingsTransitionDelayIn"><?php echo $l('Transition Delay In (Seconds)'); ?></label>
                                    <input name="properties[transition_delay_in]" value="" onchange="updateSlide($('[name=banner_id]').val(), $('[name=banner_item_id]').val());" />
                                </div>

                                <div class="row">
                                    <label for="SettingsTransitionDurationIn"><?php echo $l('Transition Duration In (Seconds)'); ?></label>
                                    <input name="properties[transition_duration_in]" value="" onchange="updateSlide($('[name=banner_id]').val(), $('[name=banner_item_id]').val());" />
                                </div>

                                <div class="row">
                                    <label><?php echo $l('Transition Effect In'); ?></label>
                                    <div class="clear"></div>
                                    <select name="properties[transition_effect_in]" onchange="updateSlide($('[name=banner_id]').val(), $('[name=banner_item_id]').val());">
                                        <?php if (isset($transition_effects) && is_array($transition_effects)) { ?>
                                        <?php foreach ($transition_effects as $k=>$v) { ?>
                                        <option value="<?php echo $v; ?>"<?php if ($v === $settings['transition_effect_in']) { ?> selected="selected"<?php } ?>><?php echo $k; ?></option>
                                        <?php } //end foreach ?>
                                        <?php } //end if ?>
                                    </select>
                                </div>

                                <div class="clear"></div>
                                <h2><?php echo $l('Transition Out'); ?></h2>
                                <hr />

                                <div class="row">
                                    <label for="SettingsTransitionDelayOut"><?php echo $l('Transition Delay Out (Seconds)'); ?></label>
                                    <input name="properties[transition_delay_out]" value="" onchange="updateSlide($('[name=banner_id]').val(), $('[name=banner_item_id]').val());" />
                                </div>

                                <div class="row">
                                    <label for="SettingsTransitionDurationOut"><?php echo $l('Transition Duration Out (Seconds)'); ?></label>
                                    <input name="properties[transition_duration_out]" value="" onchange="updateSlide($('[name=banner_id]').val(), $('[name=banner_item_id]').val());" />
                                </div>

                                <div class="row">
                                    <label><?php echo $l('Transition Effect Out'); ?></label>
                                    <div class="clear"></div>
                                    <select name="properties[transition_effect_out]" onchange="updateSlide($('[name=banner_id]').val(), $('[name=banner_item_id]').val());">
                                        <?php if (isset($transition_effects) && is_array($transition_effects)) { ?>
                                        <?php foreach ($transition_effects as $k=>$v) { ?>
                                        <option value="<?php echo $v; ?>"<?php if ($v === $settings['transition_effect_out']) { ?> selected="selected"<?php } ?>><?php echo $k; ?></option>
                                        <?php } ?>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>

                            <div id="slide_form_contents">

                                <input type="text" id="qWidgets" placeholder="<?php echo $l('Search Widget'); ?>" showquick="off" />
                   
                                <div class="clear"></div><hr />

                                <ul style="height:300px;overflow-y:scroll;">
                                    <?php foreach ($modules as $module) { ?>
                                    <li data-title="<?php echo $module['name']; ?>" data-widget="<?php echo $module['widget']; ?>" draggable="true">
                                        <b><?php echo $module['name']; ?></b><br />
                                        <?php echo $module['description']; ?>
                                    </li>
                                    <?php } ?>
                                </ul>

                            </div>
                            <div id="slide_form_preview">[ Preview ]</div>

                        </div>
                        
                        <div class="grid_7">
                            <div id="slide_background" data-widgets-wrapper>
                                <img data-background />
                            </div>
                        </div>
                            
                    </div>

                    <div class="clear"></div>
                <?php } ?>


















                    <a onclick="addItem();" class="button"><?php echo $l('button_add_item'); ?></a>

                    <div class="clear"></div>
                    <hr />
                    <div class="clear"></div>

                    <ul id="items" class="list">
                    <?php foreach ($banner_items as $key => $banner_item) { ?>

                        <li class="slideRow" id="row<?php echo $key; ?>">
                            <input type="hidden" name="items[<?php echo $key; ?>][banner_item_id]" value="<?php echo $banner_item['banner_item_id']; ?>" />

                            <div class="row move">
                                <img src="<?php echo str_replace('%theme%',$Config->get('config_admin_template'),HTTP_ADMIN_THEME_IMAGE) .'move.png'; ?>"" alt="Ordenar" title="Ordenar" style="text-align:center" />
                                <input type="hidden" name="items[<?php echo $key; ?>][sort_order]" class="sortOrder" value="<?php echo $key; ?>" />
                            </div>

                            <div class="row">
                                <input type="hidden" id="image<?php echo $key; ?>" name="items[<?php echo $key; ?>][image]" value="<?php echo $banner_item['image']; ?>" />

                                <a class="filemanager" data-fancybox-type="iframe" href="<?php echo $Url::createAdminUrl("common/filemanager"); ?>&amp;field=image<?php echo $key; ?>&amp;preview=preview<?php echo $key; ?>">

                                    <img src="<?php echo ($banner_item['image'] && file_exists(DIR_IMAGE . $banner_item['image'])) ? $NTImage::resizeAndSave($banner_item['image'], 180, 180) : $NTImage::resizeAndSave('no_image.jpg', 180, 180); ?>" id="preview<?php echo $key; ?>" class="image" width="180" />
                                </a>
                                <br />
                                <a onclick="image_delete('image<?php echo $key; ?>', 'preview<?php echo $key; ?>');" style="color:#FFA500;font-size:10px">[ Quitar ]</a>

                                <div class="clear"></div>
                            </div>

                            <div class="row">
                                <input type="text" name="items[<?php echo $key; ?>][link]" value="<?php echo $banner_item['link']; ?>" placeholder="<?php echo $l('entry_link'); ?>" showquick="off" />
                                <div class="clear"></div>
                            </div>

                            <div class="row">
                                <div class="htabs">
                                <?php foreach ($languages as $language) { ?>
                                    <a onclick="showTab(this,'language_<?php echo $key; ?>_<?php echo $language['code']; ?>')" class="htab"><img src="images/flags/<?php echo $language['image']; ?>" title="<?php echo $language['name']; ?>" /></a>
                                <?php } ?>
                                </div>

                                <div class="clear"></div>

                                <?php foreach ($languages as $language) { ?>
                                <div id="language_<?php echo $key; ?>_<?php echo $language['code']; ?>" class="tab">
                                     <input type="text" name="items[<?php echo $key; ?>][descriptions][<?php echo $language['language_id']; ?>][title]" value="<?php echo $banner_item['descriptions'][$language['language_id']]['title'] ?? ""; ?>" required="true" placeholder="<?php echo $l('entry_title') ." ". $language['name']; ?>" showquick="off" />

                                    <div class="clear"></div><br />

                                     <textarea name="items[<?php echo $key; ?>][descriptions][<?php echo $language['language_id']; ?>][description]" cols="90" placeholder="<?php echo $l('entry_description') ." ". $language['name']; ?>" showquick="off"><?php echo $banner_item['descriptions'][$language['language_id']]['description'] ?? ""; ?></textarea>

                                </div>
                                <?php } ?>
                            </div>

                            <div class="row">
                                <a onclick="$('#row<?php echo $key; ?>').remove();" class="button"><?php echo $l('button_remove'); ?></a>
                            </div>
                        </li>
                        <?php } ?>
                    </ul>

                    <div class="clear"></div>
                    <hr />
                    <div class="clear"></div>

                    <a onclick="addItem();" class="button"><?php echo $l('button_add_item'); ?></a>
                </div>
            </form>
    </div>
    
    <div id="products" style="display: none;">
        <table>
            <tbody>
            <?php if (isset($products) && is_array($products)) { ?>
            <?php foreach($products as $product) { ?>
                <tr id="product_<?php echo $product['product_id']; ?>" onclick="setLink('<?php echo $product['href']; ?>')">
                    <td><img src="<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>" /></td>
                    <td><?php echo $product['name']; ?></td>
                </tr>
            <?php } ?>
            <?php } ?>
            </tbody>
        </table>
    </div>
    
    <div id="categories" style="display: none;">
        <table>
            <tbody>
            <?php if (isset($categories) && is_array($categories)) { ?>
            <?php foreach($categories as $category) { ?>
                <tr id="category_<?php echo $category['category_id']; ?>" onclick="setLink('<?php echo $category['href']; ?>')">
                    <td><?php echo $category['name']; ?></td>
                </tr>
            <?php } ?>
            <?php } ?>
            </tbody>
        </table>
    </div>
</div>
<script type="text/javascript">
$(function(){
    $('.tab:first-child').show();
    $('#items').sortable({
        opacity: 0.6, 
        cursor: 'move',
        handle: '.move',
        update: function() {
            $('#items li').each(function(){
                $(this).find('.sortOrder').val($(this).index());
            });
        }
    });
    $('.move').css('cursor','move');
});

function addItem() {
    _row = ($('#items li:last-child').index() + 1);

    html = '<li class="slideRow" id="row'+ _row +'">'
    + '<div class="row move">'
    + '<input type="hidden" name="items['+ _row +'][sort_order]" class="sortOrder" value="'+ _row +'" /><img src="<?php echo str_replace('%theme%',$Config->get('config_admin_template'),HTTP_ADMIN_THEME_IMAGE) .'move.png'; ?>"" alt="Ordenar" title="Ordenar" style="text-align:center" />'
    + '</div>'

    + '<div class="row">'
        + '<input type="hidden" name="items['+ _row +'][image]" value="" id="image'+ _row +'" />'

        + '<a class="filemanager" data-fancybox-type="iframe" href="<?php echo $Url::createAdminUrl("common/filemanager"); ?>&amp;field=image'+ _row +'&amp;preview=preview'+ _row +'">'
            + '<img src="<?php echo HTTP_IMAGE; ?>cache/no_image-180x180.jpg" id="preview'+ _row +'" class="image" width="180" />'
        + '</a>'

        + '<div class="clear"></div>'
        
        + '<a onclick="image_delete(\'image'+ _row +'\', \'preview'+ _row +'\');" style="color:#FFA500;font-size:10px">[ Quitar ]</a>'
        + '<div class="clear"></div>'
    + '</div>'

    + '<div class="row">'
        + '<input type="text" name="items['+ _row +'][link]" value="" placeholder="<?php echo $l('entry_link'); ?>" />'
        + '<div class="clear"></div>'
    + '</div>'

    + '<div class="row">'
        + '<div class="htabs">';

            <?php foreach ($languages as $language) { ?>
            html += '<a onclick="showTab(this,\'language_'+ _row +'_<?php echo $language['code']; ?>\')" class="htab"><img src="images/flags/<?php echo $language['image']; ?>" title="<?php echo $language['name']; ?>" /></a>';
            <?php } ?>
            
        html += '</div>';
            
        <?php foreach ($languages as $language) { ?>
        html += 
        '<div id="language_'+ _row +'_<?php echo $language['code']; ?>">'
            + '<input type="text" name="items['+ _row +'][descriptions][<?php echo $language['language_id']; ?>][title]" value="" required="true" placeholder="<?php echo $l('entry_title') ." ". $language['name']; ?>" />'
            + '<div class="clear"></div><br />'
            + '<textarea name="items['+ _row +'][descriptions][<?php echo $language['language_id']; ?>][description]" style="width:90%" placeholder="<?php echo $l('entry_description') ." ". $language['name']; ?>"></textarea>'
        + '</div>';
        <?php } ?>
        
        html += '</div>'
    + '</div>'

    + '<div class="row">'
        + '<a onclick="$(\'#row'+ _row +'\').remove();" class="button"><?php echo $l('button_remove'); ?></a>'
    + '</div>';
        
	$('#items').append(html);
}

function showTab(e,id) {
    $(e).closest('rows').find('.tab').each(function(){
        $(this).hide();
    });
    $('#'+ id).show();
}
</script>
<?php echo $footer; ?>