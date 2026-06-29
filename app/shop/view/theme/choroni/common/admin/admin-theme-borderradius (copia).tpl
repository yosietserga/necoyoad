    <div id="adminTopNav">
            <ul>
                <?php if ($is_admin && $_GET['theme_editor']) { ?>
                <li>
                    <a onclick="slidePanel__('style')"><img src="<?php echo HTTP_IMAGE; ?>icons/color/palette.png" /></a>
                </li>
                <?php } ?>
                <li class="dd">
                    <span><?php echo $l('text_create'); ?> &darr;</span>
                    <ul class="menu_body">
                        <li><a href="<?php echo $create_product; ?>" title="<?php echo $l('text_create_product'); ?>"><?php echo $l('text_create_product'); ?></a></li>
                        <li><a href="<?php echo $create_page; ?>" title="<?php echo $l('text_create_page'); ?>"><?php echo $l('text_create_page'); ?></a></li>
                        <li><a href="<?php echo $create_post; ?>" title="<?php echo $l('text_create_post'); ?>"><?php echo $l('text_create_post'); ?></a></li>
                        <li><a href="<?php echo $create_manufacturer; ?>" title="<?php echo $l('text_create_manufacturer'); ?>"><?php echo $l('text_create_manufacturer'); ?></a></li>
                        <li><a href="<?php echo $create_product_category; ?>" title="<?php echo $l('text_create_category'); ?>"><?php echo $l('text_create_category'); ?></a></li>
                        <li><a href="<?php echo $create_post_category; ?>" title="<?php echo $l('text_create_post_category'); ?>"><?php echo $l('text_create_post_category'); ?></a></li>
                    </ul>
                </li>
            </ul>
    </div>
    
    <form id="cssDataWrapper"></form>
    
    <?php if ($is_admin && $_GET['theme_editor']) { ?>
    <div class="panel-lateral" id="style">
        <div class="panel-lateral-tabs">
            <span>Editor CSS</span>
            <span>Configurar</span>
            <span>Widgets</span>
        </div>
        
        
        <div class="panel-lateral-tab">
        <form id="formStyle">
            <input type="hidden" id="selector" name="selector" value="" />
            <input type="hidden" id="mainselector" name="mainselector" value="" />

            <a class="style-icons nuevo" href="'<?php echo $new_theme; ?>'"></a>
            <a class="style-icons save" onclick="saveStyle('<?php echo $save_theme; ?>')"></a>
            <a class="style-icons clean" onclick="cleanStyle()"></a>
            <a class="style-icons copy" onclick="copyStyle()"></a>
            <a class="style-icons paste" onclick="pasteStyle()"></a>
            <a class="style-icons print" onclick="printStyle()"></a>
            <a class="style-icons close" onclick="slidePanel('style')"><?php echo $l('text_close'); ?></a>
            
            <div class="clear"></div>
            
            <select id="selectors" onchange="setElementToStyle($(this).val())">
                <option value="null"><?php echo $l('text_general'); ?></option>
                
                <optgroup label="<?php echo $l('text_fonts'); ?>">
                    <option value="h1"><?php echo $l('text_heading_h1'); ?></option>
                    <!-- <option value="h1:hover"><?php echo $l('text_heading_h1_hover'); ?></option> -->
                    <option value="subtitle"><?php echo $l('text_subheading_h2_h6'); ?></option>
                    <option value="p"><?php echo $l('text_paragraphs'); ?></option>
                    <option value="b"><?php echo $l('text_strong'); ?></option>
                    <option value="a"><?php echo $l('text_links'); ?></option>
                    <!-- <option value="a:hover"><?php echo $l('text_links_hover'); ?></option> -->
                </optgroup>
                
                <optgroup label="<?php echo $l('text_forms'); ?>">
                    <option value="input"><?php echo $l('text_inputs'); ?></option>
                    <!-- <option value="input:hover"><?php echo $l('text_inputs_hover'); ?></option> -->
                    <!-- <option value="input:focus"><?php echo $l('text_inputs_focus'); ?></option> -->
                    <option value="select"><?php echo $l('text_selects'); ?></option>
                    <!-- <option value="select:hover"><?php echo $l('text_selects_hover'); ?></option> -->
                    <option value="textarea"><?php echo $l('text_textareas'); ?></option>
                    <!-- <option value="textarea:hover"><?php echo $l('text_textareas_hover'); ?></option> -->
                    <!-- <option value="textarea:focus"><?php echo $l('text_textareas_focus'); ?></option> -->
                    <option value="label"><?php echo $l('text_label'); ?></option>
                </optgroup>
                
                <optgroup label="<?php echo $l('text_tables'); ?>">
                    <option value="th"><?php echo $l('text_table_header'); ?></option>
                    <option value="tr"><?php echo $l('text_table_rows'); ?></option>
                    <option value="td"><?php echo $l('text_table_columns'); ?></option>
                    <option value="tr:first-child"><?php echo $l('text_table_first_row'); ?></option>
                    <option value="td:first-child"><?php echo $l('text_table_first_column'); ?></option>
                </optgroup>
                
                <optgroup label="<?php echo $l('text_others'); ?>">
                    <option value="li"><?php echo $l('text_list_items'); ?></option>
                    <option value="li:hover"><?php echo $l('text_list_items_hover'); ?></option>
                    <option value="span"><?php echo $l('text_span'); ?></option>
                    <option value=".header"><?php echo $l('text_headers'); ?></option>
                    <option value=".content"><?php echo $l('text_contents'); ?></option>
                </optgroup>
                
            </select>
            
            <div class="clear"></div>
            
            <h3 style="padding:10px;background: #84A150;border: solid 1px ;#376300; color:#fff;"><?php echo $l('text_selector'); ?>: <span id="el" style="font-weight: bold;"></span></h3>
            <div class="clear"></div>
            
            <div class="panelWrapper">
                <h3><?php echo $l('text_backgrounds'); ?></h3>
                <div>
                    <table>
                        <tr>
                            <td><?php echo $l('text_color'); ?>:<div id="background-colorpicker"></div></td>
                            <td>
                                <input class="style-panel" type="text" id="backgroundColor" name="Style[background-color]" value="" />
                            </td>
                            <td><a class="style-icons help" title="<?php echo $l('help_color'); ?>"></a></td>
                        </tr>
                        <tr>
                            <td><?php echo $l('text_image'); ?>:<br /><a onclick="image_upload()"><?php echo $l('text_selector'); ?>Ver Servidor</a></td>
                            <td><input class="style-panel" type="url" id="backgroundImage" name="Style[background-image]" value="" /></td>
                            <td><a class="style-icons help" title="<?php echo $l('help_image'); ?>"></a></td>
                        </tr>
                        <tr>
                            <td><?php echo $l('text_image_repeat'); ?>:</td>
                            <td>
                                <select class="style-panel" id="backgroundRepeat" name="Style[background-repeat]">
                                    <option value=""><?php echo $l('text_default'); ?></option>
                                    <option value="repeat"><?php echo $l('text_repeat'); ?></option>
                                    <option value="repeat-x"><?php echo $l('text_repeat_from_left_to_right'); ?></option>
                                    <option value="repeat-y"><?php echo $l('text_repeat_from_top_to_bottom'); ?></option>
                                    <option value="no-repeat"><?php echo $l('text_no_repeat'); ?></option>
                                </select>
                            </td>
                            <td><a class="style-icons help" title="<?php echo $l('help_repeat'); ?>"></a></td>
                        </tr>
                        <tr>
                            <td><?php echo $l('text_image_position'); ?>:</td>
                            <td>
                                <input class="style-panel" type="necoNumber" id="backgroundPositionX" name="Style[background-position-x]" value="" style="width:40px" />
                                <input class="style-panel" type="necoNumber" id="backgroundPositionY" name="Style[background-position-y]" value="" style="width:40px" />
                            </td>
                            <td><a class="style-icons help" title="<?php echo $l('help_image_position'); ?>"></a></td>
                        </tr>
                        <tr>
                            <td><?php echo $l('text_image_attachment'); ?>:</td>
                            <td>
                                <input class="style-panel" type="checkbox" id="backgroundAttachment" name="Style[background-attachment]" value="1" />
                            </td>
                            <td><a class="style-icons help" title="<?php echo $l('help_image_attachment'); ?>"></a></td>
                        </tr>
                        <tr>
                            <td><a class="button" onclick="resetBackground()"><?php echo $l('text_clean'); ?></a></td>
                            <td><a class="button" onclick="setStyle()"><?php echo $l('text_apply'); ?></a></td>
                        </tr>
                    </table>
                </div>
                
                <h3><?php echo $l('text_fonts'); ?></h3>
                <div>
                    <table>
                        <tr>
                            <td><?php echo $l('text_color'); ?>:<div id="font-colorpicker"></div></td>
                            <td>
                                <input class="style-panel" type="text" id="fontColor" name="Style[color]" value="" />
                                
                            </td>
                            <td><a class="style-icons help" title="<?php echo $l('help_color'); ?>"></a></td>
                        </tr>
                        <tr>
                            <td><?php echo $l('text_font_type'); ?>:</td>
                            <td>
                                <select class="style-panel" id="fontFamily" name="Style[font-familiy]">
                                    <option value=""><?php echo $l('text_none'); ?></option>
                                    <option value="Verdana, Geneva, sans-serif">Verdana</option>
                                    <option value="Georgia, 'Times New Roman', Times, serif">Georgia</option>
                                    <option value="'Courier New', Courier, monospace">Courier New</option>
                                    <option value="Arial, Helvetica, sans-serif">Arial</option>
                                    <option value="Tahoma, Geneva, sans-serif">Tahoma</option>
                                    <option value="'Trebuchet MS', Arial, Helvetica, sans-serif">Trebuchet MS</option>
                                    <option value="'Palatino Linotype', 'Book Antiqua', Palatino, serif">Palatino Linotype</option>
                                    <option value="'Times New Roman', Times, serif">Times New Roman</option>
                                    <option value="'Lucida Sans Unicode', 'Lucida Grande', sans-serif">Lucida Sans Unicode</option>
                                    <option value="'MS Serif', 'New York', serif">MS Serif</option>
                                    <option value="'Lucida Console', Monaco, monospace">Lucida Console</option>
                                    <option value="'Comic Sans MS', cursive">Comic Sans MS</option>
                                </select>
                            </td>
                            <td><a class="style-icons help" title="<?php echo $l('help_font_family'); ?>"></a></td>
                        </tr>
                        <tr>
                            <td><?php echo $l('text_font_size'); ?>:</td>
                            <td>
                                <select class="style-panel" id="fontSize" name="Style[font-size]">
                                    <option value=""><?php echo $l('text_none'); ?></option>
                                    <option value="8px">8</option>
                                    <option value="9px">9</option>
                                    <option value="10px">10</option>
                                    <option value="11px">11</option>
                                    <option value="12px">12</option>
                                    <option value="14px">14</option>
                                    <option value="18px">18</option>
                                    <option value="22px">22</option>
                                    <option value="24px">24</option>
                                    <option value="28px">28</option>
                                    <option value="32px">32</option>
                                    <option value="36px">36</option>
                                    <option value="40px">40</option>
                                    <option value="44px">44</option>
                                    <option value="48px">48</option>
                                    <option value="54px">54</option>
                                    <option value="60px">60</option>
                                    <option value="72px">72</option>
                                </select>
                            </td>
                            <td><a class="style-icons help" title="<?php echo $l('help_font_size'); ?>"></a></td>
                        </tr>
                        <tr>
                            <td><?php echo $l('text_letter_spacing'); ?>:</td>
                            <td>
                                <div id="letterSpacingSlider" class="slider"></div>
                                <input class="style-panel" type="necoNumber" id="letterSpacing" name="Style[letter-spacing]" value="" />
                            </td>
                            <td><a class="style-icons help" title="<?php echo $l('help_letter_spacing'); ?>"></a></td>
                        </tr>
                        <tr>
                            <td><?php echo $l('text_word_spacing'); ?>:</td>
                            <td>
                                <div id="wordSpacingSlider" class="slider"></div>
                                <input class="style-panel" type="necoNumber" id="wordSpacing" name="Style[word-spacing]" value="" />
                            </td>
                            <td><a class="style-icons help" title="<?php echo $l('help_word_spacing'); ?>"></a></td>
                        </tr>
                        <tr>
                            <td><?php echo $l('text_line_height'); ?>:</td>
                            <td>
                                <div id="lineHeightSlider" class="slider"></div>
                                <input class="style-panel" type="necoNumber" id="lineHeight" name="Style[line-height]" value="" />
                            </td>
                            <td><a class="style-icons help" title="<?php echo $l('help_line_height'); ?>"></a></td>
                        </tr>
                        <tr>
                            <td colspan="2">
                            
                            <a id="bold" class="bold style-icons" onclick="setWeight(this,'bold','boldOn')"></a>
                            <input class="style-panel" type="hidden" id="fontWeight" name="Style[font-weight]" value="" />
                                
                            <a id="italic" class="italic style-icons" onclick="setItalic(this,'italic','italicOn')"></a>
                            <input class="style-panel" type="hidden" id="fontStyle" name="Style[font-style]" value="" />
                                
                            <a id="underline" class="underline style-icons" onclick="setDecoration(this,'underline','underlineOn')"></a>
                            <a id="lineThrough" class="line-through style-icons" onclick="setDecoration(this,'line-through','line-throughOn')"></a>
                            <input class="style-panel" type="hidden" id="textDecoration" name="Style[text-decoration]" value="" />
                                
                            <a id="upper" class="uppercase style-icons" onclick="setTransform(this,'uppercase','uppercaseOn')"></a>
                            <a id="lower" class="lowercase style-icons" onclick="setTransform(this,'lowercase','lowercaseOn')"></a>
                            <input class="style-panel" type="hidden" id="textTransform" name="Style[text-transform]" value="" />
                            
                            <a id="alignLeft" class="align-left style-icons" onclick="setAlign(this,'left','align-leftOn')"></a>
                            <a id="alignCenter" class="align-center style-icons" onclick="setAlign(this,'center','align-centerOn')"></a>
                            <a id="alignRight" class="align-right style-icons" onclick="setAlign(this,'right','align-rightOn')"></a>
                            <a id="alignJustify" class="align-justify style-icons" onclick="setAlign(this,'justify','align-justifyOn')"></a>
                            <input class="style-panel" type="hidden" id="textAlign" name="Style[text-align]" value="" />
                            </td>
                            <td><a class="style-icons help" title="<?php echo $l('help_font_format'); ?>"></a></td>
                        </tr>
                        <tr>
                            <td><a class="button" onclick="resetFont()"><?php echo $l('text_clean'); ?></a></td>
                            <td><a class="button" onclick="setStyle()"><?php echo $l('text_apply'); ?></a></td>
                        </tr>
                    </table>
                </div>
                
                <h3><?php echo $l('text_borders'); ?></h3>
                <div>
                    <a onclick="showAdvanced(this)"><?php echo $l('text_advanced'); ?></a>
                    <input class="style-panel advanced" type="hidden" id="borderAdvanced" value="" />
                    <div>
                        <table>
                            <tr>
                                <td colspan="2"><b><?php echo $l('text_basic'); ?></b></td>
                            </tr>
                            <tr>
                                <td><?php echo $l('text_color'); ?>:<div id="border-colorpicker"></div></td>
                                <td><input class="style-panel" type="text" id="borderColor" name="Style[border-color]" value="" /></td>
                                <td><a class="style-icons help" title="<?php echo $l('help_'); ?>"></a></td>
                            </tr>
                            <tr>
                                <td><?php echo $l('text_border_line'); ?>:</td>
                                <td>
                                    <select class="style-panel" id="borderStyle" name="Style[border-style]">
                                        <option value=""><?php echo $l('text_none'); ?></option>
                                        <option value="solid"><?php echo $l('text_border_solid'); ?></option>
                                        <option value="dashed"><?php echo $l('text_border_dashed'); ?></option>
                                        <option value="dotted"><?php echo $l('text_border_dotted'); ?></option>
                                        <option value="double"><?php echo $l('text_border_double'); ?></option>
                                        <option value="groove"><?php echo $l('text_border_groove'); ?></option>
                                        <option value="ridge"><?php echo $l('text_border_ridge'); ?></option>
                                    </select>
                                </td>
                                <td><a class="style-icons help" title="<?php echo $l('help_border_line'); ?>"></a></td>
                            </tr>
                            <tr>
                                <td><?php echo $l('text_border_weight'); ?>:</td>
                                <td>
                                    <div id="borderWidthSlider" class="slider"></div>
                                    <input class="style-panel" type="necoNumber" id="borderWidth" name="Style[border-width]" value="" />
                                </td>
                                <td><a class="style-icons help" title="<?php echo $l('help_'); ?>"></a></td>
                            </tr>
                        </table>
                    </div>
                    <div style="display: none;">
                        <table>
                            <tr>
                                <td colspan="2"><b><?php echo $l('text_advanced'); ?></b></td>
                            </tr>
                            <tr><td colspan="2"><hr /><b><?php echo $l('text_border_top'); ?></b></td></tr>
                            <tr>
                                <td><?php echo $l('text_color'); ?>:<div id="border_top_colorpicker"></div></td>
                                <td><input class="style-panel" type="text" id="borderTopColor" name="Style[border-top-color]" value="" /></td>
                                <td><a class="style-icons help" title="<?php echo $l('help_border_color'); ?>"></a></td>
                            </tr>
                            <tr>
                                <td><?php echo $l('text_border_line'); ?>:</td>
                                <td>
                                    <select class="style-panel" id="borderTopStyle" name="Style[border-top-style]">
                                        <option value=""><?php echo $l('text_none'); ?></option>
                                        <option value="solid"><?php echo $l('text_border_solid'); ?></option>
                                        <option value="dashed"><?php echo $l('text_border_dashed'); ?></option>
                                        <option value="dotted"><?php echo $l('text_border_dotted'); ?></option>
                                        <option value="double"><?php echo $l('text_border_double'); ?></option>
                                        <option value="groove"><?php echo $l('text_border_groove'); ?></option>
                                        <option value="ridge"><?php echo $l('text_border_ridge'); ?></option>
                                    </select>
                                </td>
                                <td><a class="style-icons help" title="<?php echo $l('help_border_line'); ?>"></a></td>
                            </tr>
                            <tr>
                                <td><?php echo $l('text_border_weight'); ?>:</td>
                                <td>
                                    <div id="borderTopWidthSlider" class="slider"></div>
                                    <input class="style-panel" type="necoNumber" id="borderTopWidth" name="Style[border-top-width]" value="" />
                                </td>
                                <td><a class="style-icons help" title="<?php echo $l('help_border_weight'); ?>"></a></td>
                            </tr>
                            <tr><td colspan="2"><hr /><b><?php echo $l('text_border_right'); ?></b></td></tr>
                            <tr>
                                <td><?php echo $l('text_color'); ?>:<div id="border-right-colorpicker"></div></td>
                                <td><input class="style-panel" type="text" id="borderRightColor" name="Style[border-right-color]" value="" /></td>
                                <td><a class="style-icons help" title="<?php echo $l('help_border_color'); ?>"></a></td>
                            </tr>
                            <tr>
                                <td><?php echo $l('text_border_line'); ?>:</td>
                                <td>
                                    <select class="style-panel" id="borderRightStyle" name="Style[border-right-style]">
                                        <option value=""><?php echo $l('text_none'); ?></option>
                                        <option value="solid"><?php echo $l('text_border_solid'); ?></option>
                                        <option value="dashed"><?php echo $l('text_border_dashed'); ?></option>
                                        <option value="dotted"><?php echo $l('text_border_dotted'); ?></option>
                                        <option value="double"><?php echo $l('text_border_double'); ?></option>
                                        <option value="groove"><?php echo $l('text_border_groove'); ?></option>
                                        <option value="ridge"><?php echo $l('text_border_ridge'); ?></option>
                                    </select>
                                </td>
                                <td><a class="style-icons help" title="<?php echo $l('help_border_line'); ?>"></a></td>
                            </tr>
                            <tr>
                                <td><?php echo $l('text_border_weight'); ?>:</td>
                                <td>
                                    <div id="borderRightWidthSlider" class="slider"></div>
                                    <input class="style-panel" type="necoNumber" id="borderRightWidth" name="Style[border-right-width]" value="" />
                                </td>
                                <td><a class="style-icons help" title="<?php echo $l('help_border_weight'); ?>"></a></td>
                            </tr>
                            <tr><td colspan="2"><hr /><b><?php echo $l('text_border_bottom'); ?></b></td></tr>
                            <tr>
                                <td><?php echo $l('text_color'); ?>:<div id="border-bottom-colorpicker"></div></td>
                                <td><input class="style-panel" type="text" id="borderBottomColor" name="Style[border-bottom-color]" value="" /></td>
                                <td><a class="style-icons help" title="<?php echo $l('help_border_color'); ?>"></a></td>
                            </tr>
                            <tr>
                                <td><?php echo $l('text_border_line'); ?>:</td>
                                <td>
                                    <select class="style-panel" id="borderBottomStyle" name="Style[border-bottom-style]">
                                        <option value=""><?php echo $l('text_none'); ?></option>
                                        <option value="solid"><?php echo $l('text_border_solid'); ?></option>
                                        <option value="dashed"><?php echo $l('text_border_dashed'); ?></option>
                                        <option value="dotted"><?php echo $l('text_border_dotted'); ?></option>
                                        <option value="double"><?php echo $l('text_border_double'); ?></option>
                                        <option value="groove"><?php echo $l('text_border_groove'); ?></option>
                                        <option value="ridge"><?php echo $l('text_border_ridge'); ?></option>
                                    </select>
                                </td>
                                <td><a class="style-icons help" title="<?php echo $l('help_border_line'); ?>"></a></td>
                            </tr>
                            <tr>
                                <td><?php echo $l('text_border_weight'); ?>:</td>
                                <td>
                                    <div id="borderBottomWidthSlider" class="slider"></div>
                                    <input class="style-panel" type="necoNumber" id="borderBottomWidth" name="Style[border-bottom-width]" value="" />
                                </td>
                                <td><a class="style-icons help" title="<?php echo $l('help_border_weight'); ?>"></a></td>
                            </tr>
                            <tr><td colspan="2"><hr /><b><?php echo $l('text_border_left'); ?></b></td></tr>
                            <tr>
                                <td><?php echo $l('text_color'); ?>:<div id="border-left-colorpicker"></div></td>
                                <td><input class="style-panel" type="text" id="borderLeftColor" name="Style[border-left-color]" value="" /></td>
                                <td><a class="style-icons help" title="<?php echo $l('help_border_color'); ?>"></a></td>
                            </tr>
                            <tr>
                                <td><?php echo $l('text_border_line'); ?>:</td>
                                <td>
                                    <select class="style-panel" id="borderLeftStyle" name="Style[border-left-style]">
                                        <option value=""><?php echo $l('text_none'); ?></option>
                                        <option value="solid"><?php echo $l('text_border_solid'); ?></option>
                                        <option value="dashed"><?php echo $l('text_border_dashed'); ?></option>
                                        <option value="dotted"><?php echo $l('text_border_dotted'); ?></option>
                                        <option value="double"><?php echo $l('text_border_double'); ?></option>
                                        <option value="groove"><?php echo $l('text_border_groove'); ?></option>
                                        <option value="ridge"><?php echo $l('text_border_ridge'); ?></option>
                                    </select>
                                </td>
                                <td><a class="style-icons help" title="<?php echo $l('help_border_line'); ?>"></a></td>
                            </tr>
                            <tr>
                                <td><?php echo $l('text_border_weight'); ?>:</td>
                                <td>
                                    <div id="borderLeftWidthSlider" class="slider"></div>
                                    <input class="style-panel" type="necoNumber" id="borderLeftWidth" name="Style[border-left-width]" value="" />
                                </td>
                                <td><a class="style-icons help" title="<?php echo $l('help_border_weight'); ?>"></a></td>
                            </tr>
                        </table>   
                    </div>
                </div>
                
                <h3><?php echo $l('text_border_radius'); ?></h3>
                <div>
                    <a onclick="showAdvanced(this)"><?php echo $l('text_advanced'); ?></a>
                    <input class="style-panel advanced" type="hidden" id="borderRadiusAdvanced" value="" />
                    <div>
                        <table>
                            <tr>
                                <td colspan="2"><b><?php echo $l('text_basic'); ?></b></td>
                            </tr>
                            <tr>
                                <td><?php echo $l('text_radius'); ?>:</td>
                                <td>
                                    <div id="borderRadiusSlider" style="width:180px;display:block"></div>
                                    <input class="style-panel" type="necoNumber" id="borderRadius" name="Style[border-radius]" value="" />
                                </td>
                                <td><a class="style-icons help" title="<?php echo $l('help_'); ?>"></a></td>
                            </tr>
                        </table>
                    </div>
                    <div style="display:none;">
                        <table>
                            <tr>
                                <td colspan="2"><b><?php echo $l('text_advanced'); ?></b></td>
                            </tr>
                            <tr>
                                <td><?php echo $l('text_top_left'); ?>:</td>
                                <td>
                                    <div id="borderRadiusTopLeftSlider" class="slider"></div>
                                    <input class="style-panel" type="necoNumber" id="borderRadiusTopLeft" name="Style[border-radius-topleft]" value="" />
                                </td>
                                <td><a class="style-icons help" title="<?php echo $l('help_'); ?>"></a></td>
                            </tr>
                            <tr>
                                <td><?php echo $l('text_top_right'); ?>:</td>
                                <td>
                                    <div id="borderRadiusTopRightSlider" class="slider"></div>
                                    <input class="style-panel" type="necoNumber" id="borderRadiusTopRight" name="Style[border-radius-topright]" value="" />
                                </td>
                                <td><a class="style-icons help" title="<?php echo $l('help_'); ?>"></a></td>
                            </tr>
                            <tr>
                                <td><?php echo $l('text_bottom_right'); ?>:</td>
                                <td>
                                    <div id="borderRadiusBottomRightSlider" class="slider"></div>
                                    <input class="style-panel" type="necoNumber" id="borderRadiusBottomRight" name="Style[border-radius-bottomright]" value="" />
                                </td>
                                <td><a class="style-icons help" title="<?php echo $l('help_'); ?>"></a></td>
                            </tr>
                            <tr>
                                <td><?php echo $l('text_bottom_left'); ?>:</td>
                                <td>
                                    <div id="borderRadiusBottomLeftSlider" class="slider"></div>
                                    <input class="style-panel" type="necoNumber" id="borderRadiusBottomLeft" name="Style[border-radius-bottomleft]" value="" />
                                </td>
                                <td><a class="style-icons help" title="<?php echo $l('help_'); ?>"></a></td>
                            </tr>
                        </table>   
                    </div>
                </div>
                
                <h3><?php echo $l('text_shadows'); ?></h3>
                <div>
                    <table>
                        <tr>
                            <td><?php echo $l('text_color'); ?>:<div id="box-colorpicker"></div></td>
                            <td><input class="style-panel" type="text" id="boxColor" name="Style[box-shadow-color]" value="" /></td>
                            <td><a class="style-icons help" title="<?php echo $l('help_'); ?>"></a></td>
                        </tr>
                        <tr>
                            <td><?php echo $l('text_shadow_inset'); ?>:</td>
                            <td><input class="style-panel" type="checkbox" id="boxShadowInset" name="Style[box-shadow-inset]" value="1" /></td>
                            <td><a class="style-icons help" title="<?php echo $l('help_'); ?>"></a></td>
                        </tr>
                        <tr>
                            <td><?php echo $l('text_shadow_x'); ?>:</td>
                            <td>
                                <div id="boxShadowXSlider" class="slider"></div>
                                <input class="style-panel" type="necoNumber" id="boxShadowX" name="Style[box-shadow-x]" value="" />
                            </td>
                            <td><a class="style-icons help" title="<?php echo $l('help_'); ?>"></a></td>
                        </tr>
                        <tr>
                            <td><?php echo $l('text_shadow_y'); ?>:</td>
                            <td>
                                <div id="boxShadowYSlider" class="slider"></div>
                                <input class="style-panel" type="necoNumber" id="boxShadowY" name="Style[box-shadow-y]" value="" />
                            </td>
                            <td><a class="style-icons help" title="<?php echo $l('help_'); ?>"></a></td>
                        </tr>
                        <tr>
                            <td><?php echo $l('text_blur'); ?>:</td>
                            <td>
                                <div id="boxShadowBlurSlider" class="slider"></div>
                                <input class="style-panel" type="necoNumber" id="boxShadowBlur" name="Style[box-shadow-blur]" value="" />
                            </td>
                            <td><a class="style-icons help" title="<?php echo $l('help_'); ?>"></a></td>
                        </tr>
                        <tr>
                            <td><?php echo $l('text_spread'); ?>:</td>
                            <td>
                                <div id="boxShadowSpreadSlider" class="slider"></div>
                                <input class="style-panel" type="necoNumber" id="boxShadowSpread" name="Style[box-shadow-spread]" value="" />
                            </td>
                            <td><a class="style-icons help" title="<?php echo $l('help_'); ?>"></a></td>
                        </tr>
                    </table>
                </div>
                
                <h3><?php echo $l('text_margins'); ?></h3>
                <div>
                    <a onclick="showAdvanced(this)"><?php echo $l('text_advanced'); ?></a>
                    <input class="style-panel advanced" type="hidden" id="marginAdvanced" value="" />
                    <div>
                        <table>
                            <tr>
                                <td colspan="2"><b><?php echo $l('text_basic'); ?></b></td>
                            </tr>
                            <tr>
                                <td><?php echo $l('text_all_margins'); ?>:</td>
                                <td>
                                    <div id="marginSlider" style="width:180px;display:block"></div>
                                    <input class="style-panel" type="necoNumber" id="margin" name="Style[margin]" value="" />
                                    <td><a class="style-icons help" title="<?php echo $l('help_'); ?>"></a></td>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div style="display:none;">
                        <table>
                            <tr>
                                <td colspan="2"><b><?php echo $l('text_advanced'); ?></b></td>
                            </tr>
                            <tr>
                                <td><?php echo $l('text_margin_top'); ?>:</td>
                                <td>
                                    <div id="marginTopSlider" style="width:180px;display:block"></div>
                                    <input class="style-panel" type="necoNumber" id="marginTop" name="Style[margin-top]" value="" />
                                </td>
                                <td><a class="style-icons help" title="<?php echo $l('help_'); ?>"></a></td>
                            </tr>
                            <tr>
                                <td><?php echo $l('text_margin_bottom'); ?>:</td>
                                <td>
                                    <div id="marginBottomSlider" style="width:180px;display:block"></div>
                                    <input class="style-panel" type="necoNumber" id="marginBottom" name="Style[margin-bottom]" value="" />
                                </td>
                                <td><a class="style-icons help" title="<?php echo $l('help_'); ?>"></a></td>
                            </tr>
                            <tr>
                                <td><?php echo $l('text_margin_right'); ?>:</td>
                                <td>
                                    <div id="marginRightSlider" style="width:180px;display:block"></div>
                                    <input class="style-panel" type="necoNumber" id="marginRight" name="Style[margin-right]" value="" />
                                </td>
                                <td><a class="style-icons help" title="<?php echo $l('help_'); ?>"></a></td>
                            </tr>
                            <tr>
                                <td><?php echo $l('text_margin_left'); ?>:</td>
                                <td>
                                    <div id="marginLeftSlider" style="width:180px;display:block"></div>
                                    <input class="style-panel" type="necoNumber" id="marginLeft" name="Style[margin-left]" value="" />
                                </td>
                                <td><a class="style-icons help" title="<?php echo $l('help_'); ?>"></a></td>
                            </tr>
                        </table>
                    </div>
                </div>
                
                <h3><?php echo $l('text_paddings'); ?></h3>
                <div>
                    <a onclick="showAdvanced(this)"><?php echo $l('text_advanced'); ?></a>
                    <input class="style-panel advanced" type="hidden" id="paddingAdvanced" value="" />
                    <div>
                        <table>
                            <tr>
                                <td colspan="2"><b><?php echo $l('text_basic'); ?></b></td>
                            </tr>
                            <tr>
                                <td><?php echo $l('text_all_margins'); ?>:</td>
                                <td>
                                    <div id="paddingSlider" style="width:180px;display:block"></div>
                                    <input class="style-panel" type="necoNumber" id="padding" name="Style[padding]" value="" />
                                </td>
                                <td><a class="style-icons help" title="<?php echo $l('help_'); ?>"></a></td>
                            </tr>
                        </table>
                    </div>
                    <div style="display:none;">
                        <table>
                            <tr>
                                <td colspan="2"><b><?php echo $l('text_advanced'); ?></b></td>
                            </tr>
                            <tr>
                                <td><?php echo $l('text_margin_top'); ?>:</td>
                                <td>
                                    <div id="paddingTopSlider" style="width:180px;display:block"></div>
                                    <input class="style-panel" type="necoNumber" id="paddingTop" name="Style[padding-top]" value="" />
                                </td>
                                <td><a class="style-icons help" title="<?php echo $l('help_'); ?>"></a></td>
                            </tr>
                            <tr>
                                <td><?php echo $l('text_margin_bottom'); ?>:</td>
                                <td>
                                    <div id="paddingBottomSlider" style="width:180px;display:block"></div>
                                    <input class="style-panel" type="necoNumber" id="paddingBottom" name="Style[padding-bottom]" value="" />
                                </td>
                                <td><a class="style-icons help" title="<?php echo $l('help_'); ?>"></a></td>
                            </tr>
                            <tr>
                                <td><?php echo $l('text_margin_right'); ?>:</td>
                                <td>
                                    <div id="paddingRightSlider" style="width:180px;display:block"></div>
                                    <input class="style-panel" type="necoNumber" id="paddingRight" name="Style[padding-right]" value="" />
                                </td>
                                <td><a class="style-icons help" title="<?php echo $l('help_'); ?>"></a></td>
                            </tr>
                            <tr>
                                <td><?php echo $l('text_margin_left'); ?>:</td>
                                <td>
                                    <div id="paddingLeftSlider" style="width:180px;display:block"></div>
                                    <input class="style-panel" type="necoNumber" id="paddingLeft" name="Style[padding-left]" value="" />
                                </td>
                                <td><a class="style-icons help" title="<?php echo $l('help_'); ?>"></a></td>
                            </tr>
                        </table>
                    </div>
                </div>
                
                <h3><?php echo $l('text_dimensions_and_positions'); ?></h3>
                <div>
                    <table>
                        <tr>
                            <td><?php echo $l('text_width'); ?>:</td>
                            <td>
                                <div id="widthSlider" style="width:180px;display:block"></div>
                                <input class="style-panel" type="necoNumber" id="width" name="Style[width]" value="" />
                            </td>
                                <td><a class="style-icons help" title="<?php echo $l('help_'); ?>"></a></td>
                        </tr>
                        <tr>
                            <td><?php echo $l('text_height'); ?>:</td>
                            <td>
                                <div id="heightSlider" style="width:180px;display:block"></div>
                                <input class="style-panel" type="necoNumber" id="height" name="Style[height]" value="" />
                            </td>
                            <td><a class="style-icons help" title="<?php echo $l('help_'); ?>"></a></td>
                        </tr>
                        <tr>
                            <td><?php echo $l('text_float'); ?>:</td>
                            <td>
                                <select class="style-panel" id="float" name="Style[float]">
                                    <option value="none"><?php echo $l('text_none'); ?></option>
                                    <option value="left"><?php echo $l('text_left'); ?></option>
                                    <option value="right"><?php echo $l('text_right'); ?></option>
                                </select>
                            </td>
                            <td><a class="style-icons help" title="<?php echo $l('help_'); ?>"></a></td>
                        </tr>
                        <tr>
                            <td><?php echo $l('text_overflow'); ?>:</td>
                            <td>
                                <select class="style-panel" id="overflow" name="Style[overflow]">
                                    <option value="auto"><?php echo $l('text_auto'); ?></option>
                                    <option value="scroll"><?php echo $l('text_scroll'); ?></option>
                                    <option value="hidden"><?php echo $l('text_hidden'); ?></option>
                                </select>
                            </td>
                            <td><a class="style-icons help" title="<?php echo $l('help_'); ?>"></a></td>
                        </tr>
                        <tr>
                            <td><?php echo $l('text_position'); ?>:</td>
                            <td>
                                <select class="style-panel" id="position" name="Style[position]">
                                    <option value="static"><?php echo $l('text_static'); ?></option>
                                    <option value="relative"><?php echo $l('text_relative'); ?></option>
                                    <option value="fixed"><?php echo $l('text_fixed'); ?></option>
                                    <option value="absolute"><?php echo $l('text_absolute'); ?></option>
                                </select>
                            </td>
                            <td><a class="style-icons help" title="<?php echo $l('help_'); ?>"></a></td>
                        </tr>
                        <tr>
                            <td><?php echo $l('text_horizontal_position'); ?>:</td>
                            <td>
                                <div id="leftSlider" style="width:180px;display:block"></div>
                                <input class="style-panel" type="necoNumber" id="left" name="Style[left]" value="auto" />
                            </td>
                            <td><a class="style-icons help" title="<?php echo $l('help_'); ?>"></a></td>
                        </tr>
                        <tr>
                            <td><?php echo $l('text_vertical_position'); ?>:</td>
                            <td>
                                <div id="topSlider" style="width:180px;display:block"></div>
                                <input class="style-panel" type="necoNumber" id="top" name="Style[top]" value="auto" />
                            </td>
                            <td><a class="style-icons help" title="<?php echo $l('help_'); ?>"></a></td>
                        </tr>
                    </table>
                </div>
                
            </div>
        </form>
        </div>
    </div>
<script>    
function image_upload() {
    var height = $(window).height() * 0.8;
    var width = $(window).width() * 0.8;
                
    $('#dialog').remove();
    $('#mainbody').append('<div id="dialog" style="padding: 3px 0px 0px 0px;z-index:10000;"><iframe src="<?php echo $Url::createAdminUrl('common/filemanager',array(),'NONSSL',HTTP_ADMIN); ?>&field=backgroundImage" style="padding:0; margin: 0; display: block; width: 100%; height: 100%;z-index:10000" frameborder="no" scrolling="auto"></iframe></div>');

    $('#dialog').dialog({
        title: '<?php echo $l('text_image_manager'); ?>',
        close: function (event, ui) {
            if ($('#backgroundImage').val()) {
                $('#backgroundImage').val('<?php echo HTTP_IMAGE; ?>'+ $('#backgroundImage').val());
                setStyle();
            }
        },	
        bgiframe: false,
        width: width,
        height: height,
        resizable: false,
        modal: false
    });
}
</script>
    <?php } ?>