<?php echo $header; ?>
<?php $tpl = is_dir(DIR_TEMPLATE. $this->config->get('config_template') ."/shared") ? $this->config->get('config_template') : "choroni"; ?>

<!--contentContainer -->
<div id="contentContainer" class="tpl-account-payment-receipt" nt-editable>

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


                            <div class="print-action">
                                <a onclick="window.print();" class="button">Imprimir</a>
                            </div>

                            <div class="order-info row">
                                <div class="large-8 columns">
                                    <img src="<?php echo HTTP_IMAGE . $Config->get('config_logo'); ?>" alt="<?php echo $Config->get('config_name'); ?>" />
                                    <?php echo $Config->get('config_owner'); ?>
                                    <?php echo $Config->get('config_rif'); ?>
                                    <?php echo $Config->get('config_address'); ?>
                                </div>
                                <div class="large-8 columns">
                                    Control N&deg; <?php echo $order_payment_id; ?><br />
                                    Pedido N&deg; <?php echo $order_id; ?><br />
                                    Fecha de Emisi&oacute;n <?php echo date('d-m-Y h:i A',strtotime($date_added)); ?>
                                </div>
                            </div>

                            <div class="payment-message">
                                <p>Hemos recibido un pago realizado por <?php echo $payment_firstname ." ". $payment_lastname; ?> por la cantidad de <?php echo $amount; ?> por concepto del pago/abono del pedido <?php echo $order_id; ?> con un total de <?php echo $total; ?>.</p>
                                <p>El pago fue realizado a tr&aacute;ves del m&eacute;todo <span><?php echo ucfirst($payment_method); ?></span></p>
                            </div>
                            <div class="order-data">
                                <h2>Datos del Pedido</h2>
                                <table>
                                    <tr>
                                        <td>Pedido ID</td>
                                        <td><b><?php echo $order_id; ?></b></td>
                                    </tr>
                                    <tr>
                                        <td>Total</td>
                                        <td><b><?php echo $total; ?></b></td>
                                    </tr>
                                </table>
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
                $('#passwordConfirmForm').ntForm();
            });
        </script>

        <?php echo $footer; ?>