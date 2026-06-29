<hr />
<h3><?php echo $l('Dimensiones y Posiciones'); ?></h3>
<div>
    <table>
        <tr>
            <td><?php echo $l('Ancho'); ?>:</td>
            <td>
                <div id="widthSlider" style="width:180px;display:block"></div>
                <input class="style-panel" type="necoNumber" id="width" name="Style[width]" value="" />
            </td>
        </tr>
        <tr>
            <td><?php echo $l('Alto'); ?>:</td>
            <td>
                <div id="heightSlider" style="width:180px;display:block"></div>
                <input class="style-panel" type="necoNumber" id="height" name="Style[height]" value="" />
            </td>
        </tr>
        <tr>
            <td><?php echo $l('Float'); ?>:</td>
            <td>
                <select class="style-panel" id="float" name="Style[float]">
                    <option value="none"><?php echo $l('Ninguno'); ?></option>
                    <option value="left"><?php echo $l('Izquierda'); ?></option>
                    <option value="right"><?php echo $l('Derecha'); ?></option>
                </select>
            </td>
        </tr>
        <tr>
            <td><?php echo $l('Overflow'); ?>:</td>
            <td>
                <select class="style-panel" id="overflow" name="Style[overflow]">
                    <option value="auto"><?php echo $l('Auto'); ?></option>
                    <option value="scroll"><?php echo $l('Scroll'); ?></option>
                    <option value="hidden"><?php echo $l('Oculto'); ?></option>
                </select>
            </td>
        </tr>
        <tr>
            <td><?php echo $l('Tipo de Posici&oacute;n'); ?>:</td>
            <td>
                <select class="style-panel" id="position" name="Style[position]">
                    <option value="static"><?php echo $l('Static'); ?></option>
                    <option value="relative"><?php echo $l('Relative'); ?></option>
                    <option value="fixed"><?php echo $l('Fixed'); ?></option>
                    <option value="absolute"><?php echo $l('Absolute'); ?></option>
                </select>
            </td>
        </tr>
        <tr>
            <td><?php echo $l('Posici&oacute;n Izquierda'); ?>:</td>
            <td>
                <div id="leftSlider" style="width:180px;display:block"></div>
                <input class="style-panel" type="necoNumber" id="left" name="Style[left]" value="auto" />
            </td>
            <td><a class="style-icons help" title="<?php echo $l('help_'); ?>"></a></td>
        </tr>
        <tr>
            <td><?php echo $l('Posici&oacute;n Superior'); ?>:</td>
            <td>
                <div id="topSlider" style="width:180px;display:block"></div>
                <input class="style-panel" type="necoNumber" id="top" name="Style[top]" value="auto" />
            </td>
            <td><a class="style-icons help" title="<?php echo $l('help_'); ?>"></a></td>
        </tr>
    </table>
</div>
<table>
    <tr>
        <td><a class="button" onclick="resetDimensions()"><?php echo $l('Clear'); ?></a></td>
        <td><a class="button" onclick="setStyle()"><?php echo $l('Apply'); ?></a></td>
    </tr>
</table>