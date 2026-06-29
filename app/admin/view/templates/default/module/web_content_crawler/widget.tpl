<form id="<?php echo $name; ?>_form" class="container">

    <p style="text-align:center;font-size:10px;">{%<?php echo $name; ?>%}</p>

    <div id="<?php echo $name; ?>_htabs" class="htabs">
        <a tab="#<?php echo $name; ?>_form_general" class="htab"><?php echo $l('General'); ?></a>
        <a tab="#<?php echo $name; ?>_form_data" class="htab"><?php echo $l('Data'); ?></a>
        <a tab="#<?php echo $name; ?>_form_effects" class="htab"><?php echo $l('Transitions'); ?></a>
        <a tab="#<?php echo $name; ?>_form_editor" class="htab"><?php echo $l('Editor'); ?></a>
    </div>

    <div id="<?php echo $name; ?>_form_general"><?php require_once("widget_form_general.tpl"); ?></div>
    <div id="<?php echo $name; ?>_form_data"><?php require_once("widget_form_data.tpl"); ?></div>
    <div id="<?php echo $name; ?>_form_effects"><?php require_once("widget_form_effects.tpl"); ?></div>
    <div id="<?php echo $name; ?>_form_editor"><?php require_once("widget_form_editor.tpl"); ?></div>

    <input type="hidden" name="Widgets[<?php echo $name; ?>][settings][landing_page]" value="<?php echo $settings['landing_page']; ?>" />
    <input type="hidden" name="Widgets[<?php echo $name; ?>][settings][route]" value="module/web_content_crawler" />

    <?php if (isset($settings['object_type']) && !empty($settings['object_type'])) { ?>
    <input type="hidden" name="Widgets[<?php echo $name; ?>][settings][object_type]" value="<?php echo $settings['object_type']; ?>" />
    <?php } ?>

    <?php if (isset($settings['object_id']) && !empty($settings['object_id'])) { ?>
    <input type="hidden" name="Widgets[<?php echo $name; ?>][settings][object_id]" value="<?php echo $settings['object_id']; ?>" />
    <?php } ?>

    <?php if (isset($settings['row_id']) && !empty($settings['row_id'])) { ?>
    <input type="hidden" name="Widgets[<?php echo $name; ?>][settings][row_id]" value="<?php echo str_replace('row_id=','',$settings['row_id']); ?>" />
    <?php } ?>

    <?php if (isset($settings['col_id']) && !empty($settings['col_id'])) { ?>
    <input type="hidden" name="Widgets[<?php echo $name; ?>][settings][col_id]" value="<?php echo str_replace('col_id=','',$settings['col_id']); ?>" />
    <?php } ?>

</form>
<script>
$(function(){
    $('.htabs .htab').on('click', function () {
        $(this).closest('.htabs').find('.htab').each(function () {
            $($(this).attr('tab')).hide();
            $(this).removeClass('selected');
        });
        $(this).addClass('selected');
        $($(this).attr('tab')).show();
    });
    $('.htabs .htab:first-child').trigger('click');

});
</script>