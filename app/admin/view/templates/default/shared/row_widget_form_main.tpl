<?php $name = $row['row_id']; ?>
<li class="row widgetRow widgetBox" id="<?php echo $name; ?>">
    <header>
        <span class="internal_name" style="float: left;margin: 3px 0;font: bold 10px arial;color: #900;text-shadow: 0 0 5px #f89090fa;"><?php if (isset($row['settings']['internal_name'])) echo $row['settings']['internal_name']; ?></span>
        <span>
            <small class="widgetRowMove">[mover]</small>
            <small><?php echo $name; ?></small>
            <small class="submenu">
                <i class="fa fa-bars"></i>
                <div>
                    <em>
                        <a class="advanced" href="#<?php echo $name; ?>_config">Settings</a>

                        <div style="display:none;" id="<?php echo $name; ?>_config">
                            <form id="<?php echo $name; ?>_config_form">
                                <p style="text-align:center;font-size:10px;"><?php echo $name; ?></p>

                                <div id="<?php echo $name; ?>_htabs" class="htabs">
                                    <a tab="#<?php echo $name; ?>_form_general" class="htab"><?php echo $l('General'); ?></a>
                                    <a tab="#<?php echo $name; ?>_form_style" class="htab"><?php echo $l('Style'); ?></a>
                                </div>

                                <div id="<?php echo $name; ?>_form_general"><?php include("row_widget_form_general.tpl"); ?></div>
                                <div id="<?php echo $name; ?>_form_style"><?php include("row_widget_form_style.tpl"); ?></div>

                                <input id="<?php echo $name; ?>_position" name="position" type="hidden" onchange="updateRowUI('<?php echo $name; ?>')" value="<?php echo $row['settings']['position']; ?>">
                            </form>
                        </div>

                    </em>

                    <em>
                        <a onclick="removeRow('#<?php echo $name; ?>')">Delete</a>
                    </em>
                </div>
            </small>
        </span>
    </header>

    <div class="widgetColsWrapper ui-sortable">

    <?php 
    foreach ($row['columns'] as $key => $column) {
        if (memoizeRows($column['column_id'])) continue;
        include('col_widget_form_main.tpl');
        unset($row['colums'][$key]);
    }
    ?>

    </div>

    <div class="grid_3">
        <button class="button" onclick="addColumn(this)">Add Column</button>
    </div>
</li>

<script>
    $(function(){
        $('#<?php echo $name; ?>_config_form .chosen').chosen();

        $('#<?php echo $name; ?>_config_form .htabs .htab').on('click', function () {
            $(this).closest('.htabs').find('.htab').each(function () {
                $($(this).attr('tab')).hide();
                $(this).removeClass('selected');
            });
            $(this).addClass('selected');
            $($(this).attr('tab')).show();
        });
        $('#<?php echo $name; ?>_config_form .htab').eq(0).trigger('click');
    });
</script>