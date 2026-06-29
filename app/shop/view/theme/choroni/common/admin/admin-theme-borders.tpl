
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