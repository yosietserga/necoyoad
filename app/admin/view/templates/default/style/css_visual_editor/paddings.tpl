<hr />
<h3><?php echo $l('M&aacute;rgenes Interiores'); ?></h3>
<div>
    <a onclick="showAdvanced(this)"><?php echo $l('Avanzado'); ?></a>
    <input class="style-panel advanced" type="hidden" id="paddingAdvanced" value="" />
    <div>
        <table>
            <tr>
                <td colspan="2"><b><?php echo $l('B&aacute;sico'); ?></b></td>
            </tr>
            <tr>
                <td><?php echo $l('Todos'); ?>:</td>
                <td>
                    <div id="paddingSlider" style="width:180px;display:block"></div>
                    <input class="style-panel" type="necoNumber" id="padding" name="Style[padding]" value="" />
                </td>
            </tr>
        </table>
    </div>
    <div style="display:none;">
        <table>
            <tr>
                <td colspan="2"><b><?php echo $l('Opciones Avanzadas'); ?></b></td>
            </tr>
            <tr>
                <td><?php echo $l('Superior'); ?>:</td>
                <td>
                    <div id="paddingTopSlider" style="width:180px;display:block"></div>
                    <input class="style-panel" type="necoNumber" id="paddingTop" name="Style[padding-top]" value="" />
                </td>
            </tr>
            <tr>
                <td><?php echo $l('Inferior'); ?>:</td>
                <td>
                    <div id="paddingBottomSlider" style="width:180px;display:block"></div>
                    <input class="style-panel" type="necoNumber" id="paddingBottom" name="Style[padding-bottom]" value="" />
                </td>
            </tr>
            <tr>
                <td><?php echo $l('Derecha'); ?>:</td>
                <td>
                    <div id="paddingRightSlider" style="width:180px;display:block"></div>
                    <input class="style-panel" type="necoNumber" id="paddingRight" name="Style[padding-right]" value="" />
                </td>
            </tr>
            <tr>
                <td><?php echo $l('Izquierda'); ?>:</td>
                <td>
                    <div id="paddingLeftSlider" style="width:180px;display:block"></div>
                    <input class="style-panel" type="necoNumber" id="paddingLeft" name="Style[padding-left]" value="" />
                </td>
            </tr>
        </table>
    </div>
    <table>
        <tr>
            <td><a class="button" onclick="resetPadding()"><?php echo $l('Clear'); ?></a></td>
            <td><a class="button" onclick="setStyle()"><?php echo $l('Apply'); ?></a></td>
        </tr>
    </table>

</div>