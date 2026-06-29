
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