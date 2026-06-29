<form id="cssDataWrapper"></form>
<form id="formStyle">
    <input type="hidden" id="selector" name="selector" value="" />

    <a class="style-icons save" onclick="saveStyle('<?php echo $save_theme; ?>')">Save</a>
    <a class="style-icons clean" onclick="cleanStyle()">Clean</a>
    <a class="style-icons copy" onclick="copyStyle()">Copy</a>
    <a class="style-icons paste" onclick="pasteStyle()">Paste</a>

    <div class="clear"></div>

    <h3 style="padding: 5px;background: #ff1c1c;border: solid 1px #222;#376300;color:#fff;">
        <?php echo $l('Selector'); ?>: 
        <input style="background: transparent !important; border:none !important;" type="text" id="mainselector" name="mainselector" value="" onchange="renderPanels(this.value);" />
    </h3>
    <div class="clear"></div>

    <div class="panelWrapper">
        <?php require_once(dirname(__FILE__).'/css_visual_editor/background.tpl'); ?>
        <?php require_once(dirname(__FILE__).'/css_visual_editor/fonts.tpl'); ?>
        <?php require_once(dirname(__FILE__).'/css_visual_editor/borders.tpl'); ?>
        <?php require_once(dirname(__FILE__).'/css_visual_editor/borderradius.tpl'); ?>
        <?php require_once(dirname(__FILE__).'/css_visual_editor/shadows.tpl'); ?>
        <?php require_once(dirname(__FILE__).'/css_visual_editor/margins.tpl'); ?>
        <?php require_once(dirname(__FILE__).'/css_visual_editor/paddings.tpl'); ?>
        <?php require_once(dirname(__FILE__).'/css_visual_editor/dimensions.tpl'); ?>
    </div>
</form>