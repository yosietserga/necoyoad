<?php echo $header; ?>
<?php $tpl = is_dir(DIR_TEMPLATE. $this->config->get('config_template') ."/shared") ? $this->config->get('config_template') : "choroni"; ?>

<!--contentContainer -->
<div id="contentContainer" class="tpl-account-download" nt-editable>

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

                            <?php if ($downloads) { ?>
                            <div class="filter simple-form">
                                <?php echo $l('text_search_label');?>
                                <input type="text" name="filter_name" id="filter_name" value="" placeholder="Buscar..." />
                                <select name="filter_limit" id="filter_limit">
                                    <option value="5">5 por p&aacute;gina</option>
                                    <option value="10">10 por p&aacute;gina</option>
                                    <option value="20">20 por p&aacute;gina</option>
                                    <option value="50">50 por p&aacute;gina</option>
                                </select>
                                <a href="#" id="filter" class="button"><?php echo $l('text_filter_button');?></a>
                            </div>
                            <form action="<?php echo str_replace('&', '&amp;', $action); ?>" method="post" enctype="multipart/form-data" id="form">
                                <table>
                                    <thead>
                                    <tr>
                                        <th><input title="Seleccionar Todos" type="checkbox" onclick="$('input[name*=\'selected\']').attr('checked', this.checked);" style="width: 5px !important;" /></th>
                                        <th><?php echo $text_order; ?></th>
                                        <th><?php echo $text_name; ?></th>
                                        <th><?php echo $text_size; ?></th>
                                        <th><?php echo $text_remaining; ?></th>
                                        <th><?php echo $text_date_added; ?></th>
                                        <th><?php echo $text_download; ?></th>
                                    </tr>
                                    </thead>
                                    <?php foreach ($downloads as $value) { ?>
                                    <tr id="pid_<?php echo $value['order_id']; ?>">
                                        <td><input type="checkbox" name="selected[]" value="<?php echo $value['order_id']??""; ?>"<?php if ($value['selected']) { ?> checked="checked"<?php } ?> style="width: 5px !important;" /></td>
                                        <td><b>#<?php echo $value['order_id']; ?></b></td>
                                        <td><?php echo $value['name']; ?></td>
                                        <td><?php echo $value['size']; ?></td>
                                        <td><?php echo $value['remaining']; ?></td>
                                        <td><?php echo $value['date_added']; ?></td>
                                        <td>
                                            <a href="<?php echo str_replace('&', '&amp;', $value['href']); ?>" title="<?php echo $text_download; ?>" class="button"><?php echo $text_download; ?></a>
                                        </td>
                                    </tr>
                                    <?php } ?>
                                </table>
                                <?php if ($pagination) { ?><div class="pagination"><?php echo $pagination; ?></div><?php } ?>
                            </form>
                            <?php } else { ?>
                            <div><?php echo $l('text_empty_page');?></div>
                            <?php } ?>





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

        <script type="text/javascript">
            window.deferjQuery(function () {
                function filterProducts() {
                    var url = '';
                    var subjectFilter = $('#filter_subject').val();
                    var sortFilter = $('#filter_sort').val();
                    var statusFilter = $('#filter_status').val();
                    var limitFilter = $('#filter_limit').val();

                    if (subjectFilter){
                        url += '&keyword=' + subjectFilter;
                    }

                    if (sortFilter){
                        url += '&sort=' + sortFilter;
                    }

                    if (statusFilter){
                        url += '&status=' + statusFilter;
                    }

                    if (limitFilter){
                        url += '&limit=' + limitFilter;
                    }
                    window.location.href = '<?php echo $Url::createUrl("account/order"); ?>' + url;
                    return false;
                }
                $('#filter').on('click',function(e){
                    filterProducts();
                    return false;
                });
                $('#filter_customer_product').on('keydown',function(e) {
                    if (e.keyCode == 13) {
                        filterProducts();
                    }
                    return false;
                });
            });
        </script>

        <?php echo $footer; ?>