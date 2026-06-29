<?php echo $header; ?>
<?php $tpl = is_dir(DIR_TEMPLATE. $this->config->get('config_template') ."/shared") ? $this->config->get('config_template') : "choroni"; ?>

<!--contentContainer -->
<div id="contentContainer" class="tpl-account-payment" nt-editable>

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


                            <form id="filterForm" class="filter-form simple-form break">
                                <input type="text" name="filter_list" id="filter_list" value="" placeholder="Buscar por ID de Pago" />
                                <input type="text" name="filter_order" id="filter_order" value="" placeholder="Buscar por ID de Pedido" />
                                <select name="filter_status" id="filter_status">
                                    <option value="">Todos</option>
                                    <?php foreach ($statuses as $status) { ?>
                                    <option value="<?php echo $status['order_payment_status_id']; ?>"><?php echo $status['name']; ?></option>
                                    <?php } ?>
                                </select>
                                <select name="filter_limit" id="filter_limit">
                                    <option value="5">5 por p&aacute;gina</option>
                                    <option value="10">10 por p&aacute;gina</option>
                                    <option value="20">20 por p&aacute;gina</option>
                                    <option value="50">50 por p&aacute;gina</option>
                                </select>
                                <div class="btn btn-filter btn--primary" data-action="filter" role="button" aria-label="Sort">
                                    <a onclick="filter()" id="filter"><?php echo $l('text_filter');?></a>
                                </div>
                            </form>
                            <div class="tabulated-form">
                                <form action="<?php echo str_replace('&', '&amp;', $action); ?>" method="post" enctype="multipart/form-data" id="form">
                                    <?php if ($payments) { ?>
                                    <table>
                                        <thead>
                                        <tr>
                                            <th>ID del Pago</th>
                                            <th>ID del Pedido</th>
                                            <th><?php echo $text_status; ?></th>
                                            <th><?php echo $text_date_added; ?></th>
                                            <th><?php echo $text_total; ?></th>
                                            <th>Acciones</th>
                                        </tr>
                                        </thead>
                                        <?php foreach ($payments as $value) { ?>
                                        <tr id="pid_<?php echo $value['order_payment_id']; ?>">
                                            <td><b>#<?php echo $value['order_payment_id']; ?></b></td>
                                            <td><a href="<?php echo $Url::createUrl("account/invoice",array('order_id'=>$value['order_id'])); ?>"><?php echo $value['order_id']; ?></a></td>
                                            <td><?php echo $value['status']; ?></td>
                                            <td><?php echo $value['date_added']; ?></td>
                                            <td><b><?php echo $value['amount']; ?></b></td>
                                            <td><a href="<?php echo $Url::createUrl("account/payment/receipt",array('payment_id'=>$value['order_payment_id'])); ?>" class="button">Ver Recibo</a></td>
                                        </tr>
                                        <?php } ?>
                                        <a class="action-button register-payment-action"href="<?php echo $Url::createUrl("account/payment/register"); ?>">Registrar Pago</a>
                                    </table>
                                    <?php if ($pagination) { ?><div class="pagination"><?php echo $pagination; ?></div><?php } ?>
                                    <?php } else { ?>
                                    <div class="no-info">No tiene ning&uacute;n pago registrado <a class="suggestion-action" href="<?php echo $Url::createUrl("account/payment/register"); ?>">¿Deasea registrar uno?</a></div>
                                    <?php } ?>
                                </form>
                            </div>




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
                $('#filterForm').ntForm();
                function filterProducts() {
                    var url = '';
                    var subjectFilter = $('#filter_subject').val();
                    var sortFilter = $('#filter_sort').val();
                    var statusFilter = $('#filter_status').val();
                    var limitFilter = $('#filter_limit').val();
                    var listFilter = $('#filter_list').val();

                    if (subjectFilter) {
                        url += '&keyword=' + subjectFilter;
                    }

                    if (listFilter) {
                        url += '&payment_id=' + listFilter;
                    }

                    if (sortFilter) {
                        url += '&sort=' + sortFilter;
                    }

                    if (statusFilter) {
                        url += '&status=' + statusFilter;
                    }

                    if (limitFilter) {
                        url += '&limit=' + limitFilter;
                    }
                    window.location.href = '<?php echo $Url::createUrl("account/order"); ?>' + url;
                    return false;
                }

                $('#filter').on('click', function (e) {
                    filterProducts();
                    return false;
                });
                $('#filter_customer_product').on('keydown', function (e) {
                    if (e.keyCode == 13) {
                        filterProducts();
                    }
                    return false;
                });
            });
        </script>

        <?php echo $footer; ?>