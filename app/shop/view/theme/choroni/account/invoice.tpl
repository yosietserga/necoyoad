<?php echo $header; ?>
<?php $tpl = is_dir(DIR_TEMPLATE. $this->config->get('config_template') ."/shared") ? $this->config->get('config_template') : "choroni"; ?>

<!--contentContainer -->
<div id="contentContainer" class="tpl-account-invoice" nt-editable>

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


                            <div class="group group--btn">
                                <div class="payment-address">
                                    <span><?php echo $text_payment_address; ?></span>
                                    <p><?php echo $payment_address; ?></p>

                                    <span><?php echo $text_payment_method; ?></span>
                                    <p><?php echo $payment_method; ?></p>


                                    <?php if ($shipping_address) { ?>
                                    <span><?php echo $text_shipping_address; ?></span>
                                    <p><?php echo $shipping_address; ?></p>
                                    <?php } ?>

                                    <?php if ($shipping_method) { ?>
                                    <span><?php echo $text_shipping_method; ?></span>
                                    <p><?php echo $shipping_method; ?></p>
                                    <?php } ?>
                                </div>

                                <div class="invoice">
                                    <?php if ($invoice_id) { ?><p><?php echo $text_invoice_id; ?>: <?php echo $invoice_id; ?></p><?php } ?>
                                    <p><?php echo $text_order_id; ?>: #<?php echo $order_id; ?></p>
                                    <p><?php echo $column_date_added; ?>: <?php echo $historys[(count($historys)-1)]['date_added']; ?></p>
                                    <p><?php echo $column_status; ?>:<?php echo $historys[(count($historys)-1)]['status']; ?></p>
                                </div>

                                <div class="order-table">
                                    <h3><?php echo $l('text_recipe_details');?></h3>
                                    <table>
                                        <tr class="order_table_header">
                                            <th><?php echo $text_product; ?></th>
                                            <th><?php echo $text_model; ?></th>
                                            <th><?php echo $text_quantity; ?></th>
                                            <th><?php echo $text_price; ?></th>
                                            <th><?php echo $text_total; ?></th>
                                        </tr>
                                        <?php foreach ($products as $product) { ?>
                                        <tr>
                                            <td>&nbsp;<a title="<?php echo $product['name']; ?>" href="<?php echo str_replace('&', '&amp;', $product['href']); ?>"><?php echo $product['name']; ?></a>
                                                <?php foreach ($product['option'] as $option) { ?>
                                                &nbsp;<small> - <?php echo $option['name']; ?> <?php echo $option['value']; ?></small>
                                                <?php } ?></td>
                                            <td><?php echo $product['model']; ?></td>
                                            <td><?php echo $product['quantity']; ?></td>
                                            <td><?php echo $product['price']; ?></td>
                                            <td><?php echo $product['total']; ?></td>
                                        </tr>
                                        <?php } ?>
                                    </table>
                                </div>

                                <table id="orderTotals" class="order-totals">
                                    <?php foreach ($totals as $total) { ?>
                                    <tr>
                                        <td><?php echo $total['title']; ?></td>
                                        <td><?php echo $total['text']; ?></td>
                                    </tr>
                                    <?php } ?>
                                </table>
                                <?php if ($comment) { ?>
                                <div class="oder-comment">
                                    <span><?php echo $text_comment; ?></span>
                                    <p><?php echo $comment; ?></p>
                                </div>
                                <?php } ?>

                                <?php if ($historys) { ?>
                                <div class="order-history row">
                                    <h3 class="large-12 medium-12 small-12 columns"><?php echo $text_order_history; ?></h3>
                                    <?php foreach ($historys as $history) { ?>
                                    <div class="large-2 medium-2 small-12 columns">
                                        <?php echo $history['status']; ?><br />
                                        <?php echo $history['date_added']; ?>
                                    </div>
                                    <div class="large-10 medium-10 small-12 columns">
                                        <?php echo $history['comment']; ?>
                                    </div>
                                    <?php } ?>
                                </div>
                                <?php } ?>
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