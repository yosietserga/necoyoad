
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