<div class="row">
    <label for="<?php echo $name; ?>SettingsClassnames"><?php echo $l('Classnames'); ?></label>
    <input id="<?php echo $name; ?>SettingsClassnames" type="text" onchange="updateColUI('<?php echo $name; ?>')" name="classnames" value="<?php if (isset($column['settings']['classnames'])) echo $column['settings']['classnames']; ?>" />
</div>

<div class="row">
    <a class="button" onclick="updateColUI('<?php echo $name; ?>');">Save Style</a>

    <textarea showquick="off" name="style" id="<?php echo $name; ?>_style_code" style="display:none;"><?php echo $column['settings']['style'] ? $column['settings']['style'] : "/** \nWrite your own css style only for this widget \nWe recommend use the widget name as a wrapper for styling, \nfor example #{$position}_{$name} .someClass { ... } \n**/"; ?></textarea>
    <div id="<?php echo $name; ?>_style_editor" style="width:90%;height:800px;display:block;"></div>

</div>

<script type="text/javascript">
$(function(){
    var col_id    = '<?php echo $name; ?>';
    var styleTextarea = $('#'+ col_id +'_style_code');
    var styleEditor   = ace.edit(col_id +"_style_editor");

    styleEditor.setTheme("ace/theme/twilight");
    styleEditor.getSession().setValue(styleTextarea.val());
    styleEditor.getSession().setMode('ace/mode/css');

    styleEditor.getSession().setUseWrapMode(true);
    styleEditor.getSession().on('change', function(){
        styleTextarea.val( styleEditor.getSession().getValue() );
    });
});
</script>