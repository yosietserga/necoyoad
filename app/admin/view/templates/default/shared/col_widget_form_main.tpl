<?php $name = $column['column_id'] ?>

<div class="grid_<?php echo $column['settings']['grid_large']; ?> widgetColumn widgetBox" id="<?php echo $name; ?>">
    <header>
        <span class="internal_name" style="float: left;margin: 3px 0;font: bold 10px arial;color: #fff;"><?php if (isset($column['settings']['internal_name'])) echo $column['settings']['internal_name']; ?></span>
        <span>
            <small class="colMove ui-sortable-handle">
                <i class="fa fa-arrows fa-lg"></i>
            </small>
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

                                <div id="<?php echo $name; ?>_form_general"><?php include("col_widget_form_general.tpl"); ?></div>
                                <div id="<?php echo $name; ?>_form_style"><?php include("col_widget_form_style.tpl"); ?></div>

                                <input id="<?php echo $name; ?>_position" name="position" type="hidden" onchange="updateColUI('<?php echo $name; ?>')" value="<?php echo $column['settings']['position']; ?>">
                            </form>
                        </div>

                    </em>
                    <em>
                        <a onclick="removeColumn('#<?php echo $name; ?>')">Delete</a>
                    </em>
                </div>
            </small>
        </span>
    </header>

    <ul class="widgetWrapper ui-sortable" id="<?php echo $name; ?>_widgets">
    <?php foreach ($column['widgets'] as $widget) { ?>
        <li class="widgetSet" id="<?php echo $widget['name']; ?>">
            <b class="widgetTitle"><?php echo $l('text_'. $widget['extension']); ?></b><br />
            <a class="advanced" href="#<?php echo $widget['name']; ?>_attributes"><?php echo $l('Advanced'); ?></a><br />
            <div id="<?php echo $widget['name']; ?>_attributes" class="attributes"></div>

            <div style="float:right">
                <a class="moveWidget button" style="padding:2px;cursor:move">Mover</a>
                <a class="deleteWidget button" onclick="deleteWidget(this)" style="padding:2px;">Eliminar</a>
            </div>
        </li>

        <script type="text/javascript">
            $(function(){ 
                loadNtWidgets({ 
                    name: '<?php echo $widget['name']; ?>', 
                    position: '<?php echo $widget['position']; ?>', 
                    extension: '<?php echo $widget['extension']; ?>', 
                    order: '<?php echo (int)$widget['order']; ?>' 
                }); 
            }); 
        </script>
    <?php } ?>
    </ul>
</div>

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