<hr />
<h3><?php echo $l('Bordes'); ?></h3>
<div>
    <a onclick="showAdvanced(this)"><?php echo $l('Avanzado'); ?></a>
    <input class="style-panel advanced" type="hidden" id="borderAdvanced" value="" />
    <div>
        <table>
            <tr>
                <td colspan="2"><b><?php echo $l('B&aacute;sico'); ?></b></td>
            </tr>
            <tr>
                <td><?php echo $l('Color'); ?>:<div id="border-colorpicker"></div></td>
                <td><input class="style-panel" type="text" id="borderColor" name="Style[border-color]" value="" /></td>
            </tr>
            <tr>
                <td><?php echo $l('Estilo'); ?>:</td>
                <td>
                    <select class="style-panel" id="borderStyle" name="Style[border-style]">
                        <option value=""><?php echo $l('Ninguno'); ?></option>
                        <option value="solid"><?php echo $l('S&oacute;lido'); ?></option>
                        <option value="dashed"><?php echo $l('L&iacute;nea Cortada'); ?></option>
                        <option value="dotted"><?php echo $l('Puntos'); ?></option>
                        <option value="double"><?php echo $l('Doble L&iacute;nea'); ?></option>
                        <option value="groove"><?php echo $l('Groove'); ?></option>
                        <option value="ridge"><?php echo $l('Ridge'); ?></option>
                    </select>
                </td>
            </tr>
            <tr>
                <td><?php echo $l('Ancho'); ?>:</td>
                <td>
                    <div id="borderWidthSlider" class="slider"></div>
                    <input class="style-panel" type="necoNumber" id="borderWidth" name="Style[border-width]" value="" />
                </td>
            </tr>
        </table>
    </div>
    <div style="display: none;">
        <table>
            <tr>
                <td colspan="2"><b><?php echo $l('Opciones Avanzadas'); ?></b></td>
            </tr>
            <tr><td colspan="2"><hr /><b><?php echo $l('Superior'); ?></b></td></tr>
            <tr>
                <td><?php echo $l('Color'); ?>:<div id="border_top_colorpicker"></div></td>
                <td><input class="style-panel" type="text" id="borderTopColor" name="Style[border-top-color]" value="" /></td>
            </tr>
            <tr>
                <td><?php echo $l('Estilo'); ?>:</td>
                <td>
                    <select class="style-panel" id="borderTopStyle" name="Style[border-top-style]">
                        <option value=""><?php echo $l('Ninguno'); ?></option>
                        <option value="solid"><?php echo $l('S&oacute;lido'); ?></option>
                        <option value="dashed"><?php echo $l('L&iacute;nea Cortada'); ?></option>
                        <option value="dotted"><?php echo $l('Puntos'); ?></option>
                        <option value="double"><?php echo $l('Doble L&iacute;nea'); ?></option>
                        <option value="groove"><?php echo $l('Groove'); ?></option>
                        <option value="ridge"><?php echo $l('Ridge'); ?></option>
                    </select>
                </td>
            </tr>
            <tr>
                <td><?php echo $l('Ancho'); ?>:</td>
                <td>
                    <div id="borderTopWidthSlider" class="slider"></div>
                    <input class="style-panel" type="necoNumber" id="borderTopWidth" name="Style[border-top-width]" value="" />
                </td>
            </tr>
            <tr><td colspan="2"><hr /><b><?php echo $l('Derecha'); ?></b></td></tr>
            <tr>
                <td><?php echo $l('Color'); ?>:<div id="border-right-colorpicker"></div></td>
                <td><input class="style-panel" type="text" id="borderRightColor" name="Style[border-right-color]" value="" /></td>
            </tr>
            <tr>
                <td><?php echo $l('Estilo'); ?>:</td>
                <td>
                    <select class="style-panel" id="borderRightStyle" name="Style[border-right-style]">
                        <option value=""><?php echo $l('Ninguno'); ?></option>
                        <option value="solid"><?php echo $l('S&oacute;lido'); ?></option>
                        <option value="dashed"><?php echo $l('L&iacute;nea Cortada'); ?></option>
                        <option value="dotted"><?php echo $l('Puntos'); ?></option>
                        <option value="double"><?php echo $l('Doble L&iacute;nea'); ?></option>
                        <option value="groove"><?php echo $l('Groove'); ?></option>
                        <option value="ridge"><?php echo $l('Ridge'); ?></option>
                    </select>
                </td>
            </tr>
            <tr>
                <td><?php echo $l('Ancho'); ?>:</td>
                <td>
                    <div id="borderRightWidthSlider" class="slider"></div>
                    <input class="style-panel" type="necoNumber" id="borderRightWidth" name="Style[border-right-width]" value="" />
                </td>
            </tr>
            <tr><td colspan="2"><hr /><b><?php echo $l('Inferior'); ?></b></td></tr>
            <tr>
                <td><?php echo $l('Color'); ?>:<div id="border-bottom-colorpicker"></div></td>
                <td><input class="style-panel" type="text" id="borderBottomColor" name="Style[border-bottom-color]" value="" /></td>
            </tr>
            <tr>
                <td><?php echo $l('Estilo'); ?>:</td>
                <td>
                    <select class="style-panel" id="borderBottomStyle" name="Style[border-bottom-style]">
                        <option value=""><?php echo $l('Ninguno'); ?></option>
                        <option value="solid"><?php echo $l('S&oacute;lido'); ?></option>
                        <option value="dashed"><?php echo $l('L&iacute;nea Cortada'); ?></option>
                        <option value="dotted"><?php echo $l('Puntos'); ?></option>
                        <option value="double"><?php echo $l('Doble L&iacute;nea'); ?></option>
                        <option value="groove"><?php echo $l('Groove'); ?></option>
                        <option value="ridge"><?php echo $l('Ridge'); ?></option>
                    </select>
                </td>
            </tr>
            <tr>
                <td><?php echo $l('Ancho'); ?>:</td>
                <td>
                    <div id="borderBottomWidthSlider" class="slider"></div>
                    <input class="style-panel" type="necoNumber" id="borderBottomWidth" name="Style[border-bottom-width]" value="" />
                </td>
            </tr>
            <tr><td colspan="2"><hr /><b><?php echo $l('Izquierda'); ?></b></td></tr>
            <tr>
                <td><?php echo $l('Color'); ?>:<div id="border-left-colorpicker"></div></td>
                <td><input class="style-panel" type="text" id="borderLeftColor" name="Style[border-left-color]" value="" /></td>
            </tr>
            <tr>
                <td><?php echo $l('Estilo'); ?>:</td>
                <td>
                    <select class="style-panel" id="borderLeftStyle" name="Style[border-left-style]">
                        <option value=""><?php echo $l('Ninguno'); ?></option>
                        <option value="solid"><?php echo $l('S&oacute;lido'); ?></option>
                        <option value="dashed"><?php echo $l('L&iacute;nea Cortada'); ?></option>
                        <option value="dotted"><?php echo $l('Puntos'); ?></option>
                        <option value="double"><?php echo $l('Doble L&iacute;nea'); ?></option>
                        <option value="groove"><?php echo $l('Groove'); ?></option>
                        <option value="ridge"><?php echo $l('Ridge'); ?></option>
                    </select>
                </td>
            </tr>
            <tr>
                <td><?php echo $l('Ancho'); ?>:</td>
                <td>
                    <div id="borderLeftWidthSlider" class="slider"></div>
                    <input class="style-panel" type="necoNumber" id="borderLeftWidth" name="Style[border-left-width]" value="" />
                </td>
            </tr>
        </table>   
    </div>
    <table>
        <tr>
            <td><a class="button" onclick="resetBorder()"><?php echo $l('Clear'); ?></a></td>
            <td><a class="button" onclick="setStyle()"><?php echo $l('Apply'); ?></a></td>
        </tr>
    </table>

</div>