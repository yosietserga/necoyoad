<?php echo $header; ?>
<?php $tpl = is_dir(DIR_TEMPLATE. $this->config->get('config_template') ."/shared") ? $this->config->get('config_template') : "choroni"; ?>

<!--contentContainer -->
<div id="contentContainer" class="tpl-account-message" nt-editable>

    <?php include(DIR_TEMPLATE. $tpl ."/shared/widgets-featured.tpl");?>

    <!--mainContentContainer -->
    <div id="mainContentContainer" nt-editable>
        <div class="row">

            <!-- left-column -->
            <div class="large-3 medium-3 small-12">
                <div id="columnLeft" nt-editable>
                    <?php echo $account_column_left; ?>
                    <?php if ($column_left) { echo $column_left; } ?>
                </div>
            </div>
            <!--/left-column -->

            <!--center-column -->
            <?php if ($column_left && $column_right) { ?>
            <div class="large-6 medium-6 small-12">
            <?php } else { ?>
            <div class="large-9 medium-9 small-12">
            <?php } ?>
                <div id="columnCenter" nt-editable>

                    <form id="filterForm">

                        <div class="form-entry">
                            <label for="filter_subject"><?php echo $l('text_search');?>:</label>
                            <input type="text" name="filter_subject" id="filter_subject" value="" placeholder="Buscar..." />
                        </div>
                        <div class="form-entry">
                            <label for="filter_status"><?php echo $l('text_status');?>:</label>
                            <select name="filter_status" id="filter_status">
                                <option value=""><?php echo $l('select_option_all');?></option>
                                <option value="1"><?php echo $l('Read'); ?></option>
                                <option value="2"><?php echo $l('Not Read'); ?></option>
                                <option value="-1"><?php echo $l('Spam'); ?></option>
                            </select>
                        </div>
                        <div class="form-entry">
                            <label for="filter_limit"><?php echo $l('text_display');?>:</label>
                            <select name="filter_limit" id="filter_limit">
                                <option value="5"><?php echo $l('5 per page');?></option>
                                <option value="10"><?php echo $l('10 per page');?></option>
                                <option value="20"><?php echo $l('20 per page');?></option>
                                <option value="50"><?php echo $l('50 per page');?></option>
                            </select>
                        </div>
                    </form>

                    <form action="<?php echo str_replace('&', '&amp;', $action); ?>" method="post" enctype="multipart/form-data" id="form">
                        <?php if ($messages) { ?>
                        <table class="account-sale">
                            <thead>
                            <tr>
                                <th><input title="Seleccionar Todos" type="checkbox" onclick="$('input[name*=\'selected\']').attr('checked', this.checked);" style="width: 5px !important;" /></th>
                                <th><?php echo $l('table_head_subject');?></th>
                                <th><?php echo $l('table_head_message');?></th>
                                <th><?php echo $l('table_head_sent');?></th>
                                <th><?php echo $l('table_head_actions');?></th>
                            </tr>
                            </thead>
                            <?php foreach ($messages as $value) { ?>
                            <tr id="pid_<?php echo $value['message_id']; ?>">
                                <td>
                                    <input type="checkbox" name="selected[]" value="<?php echo $value['message_id']; ?>"<?php if ($value['selected']) { ?> checked="checked"<?php } ?> /></td>
                                <td>
                                    <a href="<?php echo $Url::createUrl("account/message/read",array("message_id"=>$value['message_id'])); ?>" title="Leer Mensaje"><?php echo $value['subject']; ?></a>
                                </td>
                                <td><?php echo substr($value['message'],0,150) . "..."; ?></td>
                                <td><?php echo $value['date_added']; ?></td>
                                <td>
                                    <a href="#" onclick="if (confirm('Seguro que desea eliminarlo?')) { $.getJSON('<?php echo $Url::createUrl("account/message/delete",array("id"=>$value['message_id'])); ?>',function(){ $('#pid_<?php echo $value['message_id']; ?>').remove(); }); } return false;" title="Finalizar">Eliminar</a>
                                </td>
                            </tr>
                            <?php } ?>
                        </table>
                        <?php if ($pagination) { ?><div class="pagination"><?php echo $pagination; ?></div><?php } ?>
                        <?php } else { ?>
                        <div>
                            <div class="no-info"><?php echo $l('text_empty_data');?>&nbsp;<a href="<?php echo $Url::createUrl("account/message/create"); ?>" title="Nuevo Mensaje"><?php echo $l('text_help');?></a></div>
                        </div>
                        <?php } ?>
                    </form>





                    <?php $position = 'main'; ?>
                    <?php foreach($rows[$position] as $j => $row) { ?>
                    <?php if (!$row['key']) continue; ?>
                    <?php $row_id = $row['key']; ?>
                    <?php $row_settings = unserialize($row['value']); ?>
                    <div class="row" id="<?php echo $position; ?>_<?php echo $row_id; ?>" nt-editable>
                        <?php foreach($row['columns'] as $k => $column) { ?>
                        <?php if (!$column['key']) continue; ?>
                        <?php $column_id = $column['key']; ?>
                        <?php $column_settings = unserialize($column['value']); ?>
                        <div class="large-<?php echo $column_settings['grid_large']; ?> medium-<?php echo $column_settings['grid_medium']; ?> small-<?php echo $column_settings['grid_small']; ?>" id="<?php echo $position; ?>_<?php echo $column_id; ?>" nt-editable>
                            <ul class="widgets">
                                <?php foreach($column['widgets'] as $l => $widget) { ?> {%<?php echo $widget['name']; ?>%} <?php } ?>
                            </ul>
                        </div>
                        <?php } ?>
                    </div>
                    <?php } ?>

                </div>
            </div>
            <!--/center-column -->

            <!-- right-column -->
            <?php if ($column_right) { ?>
            <?php include(DIR_TEMPLATE. $tpl ."/shared/widgets-column-right.tpl");?>
            <?php } ?>
            <!--/right-column -->

        </div>
    </div>
    <!--/mainContentContainer -->

    <!--featuredFooterContainer -->
    <?php include(DIR_TEMPLATE. $tpl ."/shared/widgets-featured-footer.tpl");?>
    <!--/featuredFooterContainer -->

</div>
<!--/contentContainer -->

<?php echo $footer; ?>