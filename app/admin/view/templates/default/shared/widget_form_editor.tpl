<div class="grid_3">
    <h2><?php echo $l('Widget Views'); ?></h2>
    <div id="<?php echo $name; ?>_view_files">
        <?php foreach ($view_files as $k => $v) { ?>
            <a href="#" onclick="loadWidgetFile('<?php echo $Url::createAdminUrl("style/editor/file", array( "f"=>urlencode($v) )); ?>', '<?php echo $name; ?>');return false;"><?php echo basename($v); ?></a><br />
        <?php } ?>
        <hr />
        <input id="<?php echo $name; ?>_new_view_file" type="text" name="new_view_file" value="" />
        <span class="button" onclick="if ($('#<?php echo $name; ?>_new_view_file').val().length>0) addWidgetFile(
                $('#<?php echo $name; ?>_new_view_file').val() +'.tpl',
                '<?php echo urlencode($module_view_folder); ?>',
                '<?php echo $module_view_file_prefix; ?>',
                '<?php echo $name; ?>',
                '<?php echo $name; ?>_view_files'
            );"><?php echo $l('Add View File'); ?></span>
    </div>

    <hr /><div class="clear"></div><br />
    <h2><?php echo $l('Widget CSS Files'); ?></h2>
    <div id="<?php echo $name; ?>_css_files">
        <?php foreach ($css_files as $k => $v) { ?>
        <a href="#" onclick="loadWidgetFile('<?php echo $Url::createAdminUrl("style/editor/file", array( "f"=>urlencode($v) )); ?>', '<?php echo $name; ?>');return false;"><?php echo basename($v); ?></a><br />
        <?php } ?>
        <hr />
        <input id="<?php echo $name; ?>_new_css_file" type="text" name="new_css_file" value="" />
        <span class="button" onclick="if ($('#<?php echo $name; ?>_new_css_file').val().length>0) addWidgetFile(
                $('#<?php echo $name; ?>_new_css_file').val() +'.css',
                '<?php echo urlencode($module_css_folder); ?>',
                '<?php echo $module_css_file_prefix; ?>',
                '<?php echo $name; ?>',
                '<?php echo $name; ?>_css_files'
            )"><?php echo $l('Add CSS File'); ?></span>
    </div>

    <hr /><div class="clear"></div><br />
    <h2><?php echo $l('Widget JS Files'); ?></h2>
    <div id="<?php echo $name; ?>_js_files">
        <?php foreach ($js_files as $k => $v) { ?>
        <a href="#" onclick="loadWidgetFile('<?php echo $Url::createAdminUrl("style/editor/file", array( "f"=>urlencode($v) )); ?>', '<?php echo $name; ?>');return false;"><?php echo basename($v); ?></a><br />
        <?php } ?>
        <hr />
        <input id="<?php echo $name; ?>_new_js_file" type="text" name="new_js_file" value="" />
        <span class="button" onclick="if ($('#<?php echo $name; ?>_new_js_file').val().length>0) addWidgetFile(
                $('#<?php echo $name; ?>_new_js_file').val() +'.js',
                '<?php echo urlencode($module_js_folder); ?>',
                '<?php echo $module_js_file_prefix; ?>',
                '<?php echo $name; ?>',
                '<?php echo $name; ?>_js_files'
            )"><?php echo $l('Add JS File'); ?></span>
    </div>
</div>


<div class="grid_8">

    <h1 class="fileEditorName" id="<?php echo $name; ?>_editor_filename"></h1>
    <span class="button" id="<?php echo $name; ?>_editor_save"><?php echo $l('Save'); ?></span>

    <div class="clear"></div><br />

    <textarea name="code" id="<?php echo $name; ?>_editor_code" style="display:none;"><?php echo $code; ?></textarea>
    <div id="<?php echo $name; ?>_editor" style="display:none;border: solid 1px #900;width:800px;height:1800px;display:block;float:left"></div>

</div>