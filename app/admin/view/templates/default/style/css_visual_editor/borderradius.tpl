
<h3><?php echo $l('Redondeo'); ?></h3>
<div>
    <a onclick="showAdvanced(this)"><?php echo $l('Avanzado'); ?></a>
    <input class="style-panel advanced" type="hidden" id="borderRadiusAdvanced" value="" />
    <div>
        <table>
            <tr>
                <td colspan="2"><b><?php echo $l('B&aacute;sico'); ?></b></td>
            </tr>
            <tr>
                <td><?php echo $l('Radio'); ?>:</td>
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
                <td colspan="2"><b><?php echo $l('Opciones Avanzadas'); ?></b></td>
            </tr>
            <tr>
                <td><?php echo $l('Top Left'); ?>:</td>
                <td>
                    <div id="borderRadiusTopLeftSlider" class="slider"></div>
                    <input class="style-panel" type="necoNumber" id="borderRadiusTopLeft" name="Style[border-radius-topleft]" value="" />
                </td>
                <td><a class="style-icons help" title="<?php echo $l('help_'); ?>"></a></td>
            </tr>
            <tr>
                <td><?php echo $l('Top Right'); ?>:</td>
                <td>
                    <div id="borderRadiusTopRightSlider" class="slider"></div>
                    <input class="style-panel" type="necoNumber" id="borderRadiusTopRight" name="Style[border-radius-topright]" value="" />
                </td>
                <td><a class="style-icons help" title="<?php echo $l('help_'); ?>"></a></td>
            </tr>
            <tr>
                <td><?php echo $l('Bottom Right'); ?>:</td>
                <td>
                    <div id="borderRadiusBottomRightSlider" class="slider"></div>
                    <input class="style-panel" type="necoNumber" id="borderRadiusBottomRight" name="Style[border-radius-bottomright]" value="" />
                </td>
                <td><a class="style-icons help" title="<?php echo $l('help_'); ?>"></a></td>
            </tr>
            <tr>
                <td><?php echo $l('Bottom Left'); ?>:</td>
                <td>
                    <div id="borderRadiusBottomLeftSlider" class="slider"></div>
                    <input class="style-panel" type="necoNumber" id="borderRadiusBottomLeft" name="Style[border-radius-bottomleft]" value="" />
                </td>
                <td><a class="style-icons help" title="<?php echo $l('help_'); ?>"></a></td>
            </tr>
        </table>   
    </div>
    <table>
        <tr>
            <td><a class="button" onclick="resetBorderRadius()"><?php echo $l('Clear'); ?></a></td>
            <td><a class="button" onclick="setStyle()"><?php echo $l('Apply'); ?></a></td>
        </tr>
    </table>

</div>