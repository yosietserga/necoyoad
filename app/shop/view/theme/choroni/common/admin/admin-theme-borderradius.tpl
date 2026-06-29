
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