
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