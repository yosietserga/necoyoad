<hr />
<h3><?php echo $l('M&aacute;rgenes Exteriores'); ?></h3>
<div>
    <a onclick="showAdvanced(this)"><?php echo $l('Avanzado'); ?></a>
    <input class="style-panel advanced" type="hidden" id="marginAdvanced" value="" />
    <div>
        <table>
            <tr>
                <td colspan="2"><b><?php echo $l('B&aacute;sico'); ?></b></td>
            </tr>
            <tr>
                <td><?php echo $l('Todos'); ?>:</td>
                <td>
                    <div id="marginSlider" style="width:180px;display:block"></div>
                    <input class="style-panel" type="necoNumber" id="margin" name="Style[margin]" value="" />
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
                    <div id="marginTopSlider" style="width:180px;display:block"></div>
                    <input class="style-panel" type="necoNumber" id="marginTop" name="Style[margin-top]" value="" />
                </td>
            </tr>
            <tr>
                <td><?php echo $l('Inferior'); ?>:</td>
                <td>
                    <div id="marginBottomSlider" style="width:180px;display:block"></div>
                    <input class="style-panel" type="necoNumber" id="marginBottom" name="Style[margin-bottom]" value="" />
                </td>
            </tr>
            <tr>
                <td><?php echo $l('Derecha'); ?>:</td>
                <td>
                    <div id="marginRightSlider" style="width:180px;display:block"></div>
                    <input class="style-panel" type="necoNumber" id="marginRight" name="Style[margin-right]" value="" />
                </td>
            </tr>
            <tr>
                <td><?php echo $l('Izquierda'); ?>:</td>
                <td>
                    <div id="marginLeftSlider" style="width:180px;display:block"></div>
                    <input class="style-panel" type="necoNumber" id="marginLeft" name="Style[margin-left]" value="" />
                </td>
            </tr>
        </table>
    </div>
    <table>
        <tr>
            <td><a class="button" onclick="resetMargin()"><?php echo $l('Clear'); ?></a></td>
            <td><a class="button" onclick="setStyle()"><?php echo $l('Apply'); ?></a></td>
        </tr>
    </table>
</div>