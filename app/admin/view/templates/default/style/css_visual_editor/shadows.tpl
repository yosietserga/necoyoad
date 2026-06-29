<hr />
<h3><?php echo $l('Sombras'); ?></h3>
<div>
    <table>
        <tr>
            <td><?php echo $l('Color'); ?>:<div id="box-colorpicker"></div></td>
            <td><input class="style-panel" type="text" id="boxColor" name="Style[box-shadow-color]" value="" /></td>
            <td><a class="style-icons help" title="<?php echo $l('help_'); ?>"></a></td>
        </tr>
        <tr>
            <td><?php echo $l('Sombra Interna'); ?>:</td>
            <td><div class="checkbox"><input class="style-panel" type="checkbox" id="boxShadowInset" name="Style[box-shadow-inset]" value="1" /><span></span></div></td>
            <td><a class="style-icons help" title="<?php echo $l('help_'); ?>"></a></td>
        </tr>
        <tr>
            <td><?php echo $l('Sombra X'); ?>:</td>
            <td>
                <div id="boxShadowXSlider" class="slider"></div>
                <input class="style-panel" type="necoNumber" id="boxShadowX" name="Style[box-shadow-x]" value="" />
            </td>
            <td><a class="style-icons help" title="<?php echo $l('help_'); ?>"></a></td>
        </tr>
        <tr>
            <td><?php echo $l('Sombra Y'); ?>:</td>
            <td>
                <div id="boxShadowYSlider" class="slider"></div>
                <input class="style-panel" type="necoNumber" id="boxShadowY" name="Style[box-shadow-y]" value="" />
            </td>
            <td><a class="style-icons help" title="<?php echo $l('help_'); ?>"></a></td>
        </tr>
        <tr>
            <td><?php echo $l('Desenfoque'); ?>:</td>
            <td>
                <div id="boxShadowBlurSlider" class="slider"></div>
                <input class="style-panel" type="necoNumber" id="boxShadowBlur" name="Style[box-shadow-blur]" value="" />
            </td>
            <td><a class="style-icons help" title="<?php echo $l('help_'); ?>"></a></td>
        </tr>
        <tr>
            <td><?php echo $l('Tama&ntilde;o'); ?>:</td>
            <td>
                <div id="boxShadowSpreadSlider" class="slider"></div>
                <input class="style-panel" type="necoNumber" id="boxShadowSpread" name="Style[box-shadow-spread]" value="" />
            </td>
            <td><a class="style-icons help" title="<?php echo $l('help_'); ?>"></a></td>
        </tr>
    </table>
</div>
<table>
    <tr>
        <td><a class="button" onclick="resetBoxShadow()"><?php echo $l('Clear'); ?></a></td>
        <td><a class="button" onclick="setStyle()"><?php echo $l('Apply'); ?></a></td>
    </tr>
</table>