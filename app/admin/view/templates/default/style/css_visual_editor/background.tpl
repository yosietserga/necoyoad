<hr />
<h3><?php echo $l('Fondos'); ?></h3>
<div>
    <div>
        <table>
            <tr>
                <td><?php echo $l('Color'); ?>:<div id="background-colorpicker"></div></td>
                <td>
                    <input class="style-panel" type="text" id="backgroundColor" name="Style[background-color]" value="" />
                </td>
                <td><a class="style-icons help" title="<?php echo $l('help_color'); ?>"></a></td>
            </tr>
            <tr>
                <td><?php echo $l('Image'); ?>:<br /><a onclick="image_upload()">explorer</a></td>
                <td><input class="style-panel" type="url" id="backgroundImage" name="Style[background-image]" value="" /></td>
                <td><a class="style-icons help" title="<?php echo $l('help_image'); ?>"></a></td>
            </tr>
            <tr>
                <td><?php echo $l('Repetir'); ?>:</td>
                <td>
                    <select class="style-panel" id="backgroundRepeat" name="Style[background-repeat]">
                        <option value=""><?php echo $l('Default'); ?></option>
                        <option value="repeat"><?php echo $l('Repetir XY'); ?></option>
                        <option value="repeat-x"><?php echo $l('Repetir X'); ?></option>
                        <option value="repeat-y"><?php echo $l('Repetir Y'); ?></option>
                        <option value="no-repeat"><?php echo $l('No Repetir'); ?></option>
                    </select>
                </td>
                <td><a class="style-icons help" title="<?php echo $l('help_repeat'); ?>"></a></td>
            </tr>
            <tr>
                <td><?php echo $l('Posici&oacute;n'); ?>:</td>
                <td>
                    <input class="style-panel" type="necoNumber" id="backgroundPositionX" name="Style[background-position-x]" value="" style="width:40px" />
                    <input class="style-panel" type="necoNumber" id="backgroundPositionY" name="Style[background-position-y]" value="" style="width:40px" />
                </td>
                <td><a class="style-icons help" title="<?php echo $l('help_image_position'); ?>"></a></td>
            </tr>
            <tr>
                <td><?php echo $l('Fondo Fijo'); ?>:</td>
                <td>
                    <div class="checkbox">
                        <input class="style-panel" type="checkbox" id="backgroundAttachment" name="Style[background-attachment]" value="1" />
                        <span></span>
                    </div>
                </td>
                <td><a class="style-icons help" title="<?php echo $l('help_image_attachment'); ?>"></a></td>
            </tr>
        </table>
    </div>

        <table>
            <tr>
                <td><a class="button" onclick="resetBackground()"><?php echo $l('Clear'); ?></a></td>
                <td><a class="button" onclick="setStyle()"><?php echo $l('Apply'); ?></a></td>
            </tr>
        </table>
</div>