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