<hr />
<h3><?php echo $l('Fuentes'); ?></h3>
<div>
    <table>
        <tr>
            <td><?php echo $l('Color'); ?>:<div id="font-colorpicker"></div></td>
            <td>
                <input class="style-panel" type="text" id="fontColor" name="Style[color]" value="" />

            </td>
            <td><a class="style-icons help" title="<?php echo $l('help_color'); ?>"></a></td>
        </tr>
        <tr>
            <td><?php echo $l('Estilo'); ?>:</td>
            <td>
                <select class="style-panel" id="fontFamily" name="Style[font-familiy]">
                    <option value=""><?php echo $l('Ninguno'); ?></option>
                    <option value="Verdana, Geneva, sans-serif">Verdana</option>
                    <option value="Georgia, 'Times New Roman', Times, serif">Georgia</option>
                    <option value="'Courier New', Courier, monospace">Courier New</option>
                    <option value="Arial, Helvetica, sans-serif">Arial</option>
                    <option value="Tahoma, Geneva, sans-serif">Tahoma</option>
                    <option value="'Trebuchet MS', Arial, Helvetica, sans-serif">Trebuchet MS</option>
                    <option value="'Palatino Linotype', 'Book Antiqua', Palatino, serif">Palatino Linotype</option>
                    <option value="'Times New Roman', Times, serif">Times New Roman</option>
                    <option value="'Lucida Sans Unicode', 'Lucida Grande', sans-serif">Lucida Sans Unicode</option>
                    <option value="'MS Serif', 'New York', serif">MS Serif</option>
                    <option value="'Lucida Console', Monaco, monospace">Lucida Console</option>
                    <option value="'Comic Sans MS', cursive">Comic Sans MS</option>
                </select>
            </td>
        </tr>
        <tr>
            <td><?php echo $l('Tama&ntilde;o'); ?>:</td>
            <td>
                <select class="style-panel" id="fontSize" name="Style[font-size]">
                    <option value=""><?php echo $l('Ninguno'); ?></option>
                    <option value="8px">8</option>
                    <option value="9px">9</option>
                    <option value="10px">10</option>
                    <option value="11px">11</option>
                    <option value="12px">12</option>
                    <option value="14px">14</option>
                    <option value="18px">18</option>
                    <option value="22px">22</option>
                    <option value="24px">24</option>
                    <option value="28px">28</option>
                    <option value="32px">32</option>
                    <option value="36px">36</option>
                    <option value="40px">40</option>
                    <option value="44px">44</option>
                    <option value="48px">48</option>
                    <option value="54px">54</option>
                    <option value="60px">60</option>
                    <option value="72px">72</option>
                </select>
            </td>
        </tr>
        <tr>
            <td><?php echo $l('Espacio Entre Letras'); ?>:</td>
            <td>
                <div id="letterSpacingSlider" class="slider"></div>
                <input class="style-panel" type="necoNumber" id="letterSpacing" name="Style[letter-spacing]" value="" />
            </td>
        </tr>
        <tr>
            <td><?php echo $l('Espacio Entre Palabras'); ?>:</td>
            <td>
                <div id="wordSpacingSlider" class="slider"></div>
                <input class="style-panel" type="necoNumber" id="wordSpacing" name="Style[word-spacing]" value="" />
            </td>
        </tr>
        <tr>
            <td><?php echo $l('Alto de la L&iacute;nea'); ?>:</td>
            <td>
                <div id="lineHeightSlider" class="slider"></div>
                <input class="style-panel" type="necoNumber" id="lineHeight" name="Style[line-height]" value="" />
            </td>
        </tr>
        <tr>
            <td></td>
            <td>

            <a id="bold" class="bold style-icons" onclick="setWeight(this,'bold','boldOn')"></a>
            <input class="style-panel" type="hidden" id="fontWeight" name="Style[font-weight]" value="" />

            <a id="italic" class="italic style-icons" onclick="setItalic(this,'italic','italicOn')"></a>
            <input class="style-panel" type="hidden" id="fontStyle" name="Style[font-style]" value="" />

            <a id="underline" class="underline style-icons" onclick="setDecoration(this,'underline','underlineOn')"></a>
            <a id="lineThrough" class="line-through style-icons" onclick="setDecoration(this,'line-through','line-throughOn')"></a>
            <input class="style-panel" type="hidden" id="textDecoration" name="Style[text-decoration]" value="" />

            <a id="upper" class="uppercase style-icons" onclick="setTransform(this,'uppercase','uppercaseOn')"></a>
            <a id="lower" class="lowercase style-icons" onclick="setTransform(this,'lowercase','lowercaseOn')"></a>
            <input class="style-panel" type="hidden" id="textTransform" name="Style[text-transform]" value="" />

            <a id="alignLeft" class="align-left style-icons" onclick="setAlign(this,'left','align-leftOn')"></a>
            <a id="alignCenter" class="align-center style-icons" onclick="setAlign(this,'center','align-centerOn')"></a>
            <a id="alignRight" class="align-right style-icons" onclick="setAlign(this,'right','align-rightOn')"></a>
            <a id="alignJustify" class="align-justify style-icons" onclick="setAlign(this,'justify','align-justifyOn')"></a>
            <input class="style-panel" type="hidden" id="textAlign" name="Style[text-align]" value="" />
            </td>
        </tr>
    </table>
</div>
<table>
    <tr>
        <td><a class="button" onclick="resetFont()"><?php echo $l('Clear'); ?></a></td>
        <td><a class="button" onclick="setStyle()"><?php echo $l('Apply'); ?></a></td>
    </tr>
</table>


