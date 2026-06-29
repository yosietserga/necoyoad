<div class="row">
    <a class="button" onclick="saveWidget('<?php echo $name; ?>', '<?php echo $module; ?>');">Save Style</a>


    <textarea 
    showquick="off" 
    name="Widgets[<?php echo $name; ?>][settings][style]" 
    id="<?php echo $name; ?>_style_code" 
    style="display:none;"><?php echo isset($settings['style']) && !empty($settings['style']) ? $settings['style'] : "/** \nWrite your own css style only for this widget \nWe recommend use the widget name as a wrapper for styling, \nfor example #{$name} .someClass { ... } \n**/"; ?></textarea>
    <div id="<?php echo $name; ?>_style_editor" style="width:90%;height:800px;display:block;"></div>

</div>

<script type="text/javascript">
$(function(){
    var widgetModule  = '<?php echo $module; ?>';
    var widgetName    = '<?php echo $name; ?>';
    var styleTextarea = $('#'+ widgetName +'_style_code');
    if (document.getElementById(widgetName +'_style_editor')) {
        var styleEditor = ace.edit(widgetName +'_style_editor');

        styleEditor.setTheme("ace/theme/twilight");
        styleEditor.getSession().setValue(styleTextarea.val());
        styleEditor.getSession().setMode('ace/mode/css');

        styleEditor.getSession().setUseWrapMode(true);
        styleEditor.getSession().on('change', function(){
            styleTextarea.val( styleEditor.getSession().getValue() );
        });
    }
});
</script>