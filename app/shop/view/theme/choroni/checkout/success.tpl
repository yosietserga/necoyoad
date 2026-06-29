<?php echo $header; ?>
<?php $tpl = is_dir(DIR_TEMPLATE. $this->config->get('config_template') ."/shared") ? $this->config->get('config_template') : "choroni"; ?>

    <section id="maincontent" class="row">
        <?php include(DIR_TEMPLATE. $tpl ."/shared/breadcrumbs.tpl"); ?>
        <?php include(DIR_TEMPLATE. $tpl ."/shared/featured-widgets.tpl"); ?>
        <?php include(DIR_TEMPLATE. $tpl ."/shared/columns-start.tpl"); ?>


        <header class="page-heading">
            <h1><?php echo $heading_title; ?></h1>
        </header>
        <div class="text-message"><?php echo $text_message; ?></div>
    
        <div class="payment-data tabulated-data break">
            <h3><?php echo $l('text_payment_title'); ?> #<?php echo $order_id; ?></h3>
            <table>
                <tr>
                    <th><?php echo $l('table_head_payment_description'); ?></th>
                    <th><?php echo $l('table_head_payment_payment'); ?></th>
                    <th><?php echo $l('table_head_payment_total'); ?></th>
                </tr>

                <tr>
                    <td><?php echo $l('table_detail_payment_order_total'); ?></td>
                    <td>&nbsp;</td>
                    <td><?php echo $totals[count($totals)-1]['text']; ?></td>
                </tr>

                <?php foreach ($payments as $value) { ?>
                <?php if ($value['amount'] <= 0) continue; ?>
                <tr>
                    <td>
                        <a href="<?php echo $Url::createUrl("account/payment/receipt",array('payment_id'=>$value['order_payment_id'])); ?>" target="_blank">Pago #<?php echo $value['order_payment_id']; ?></a>
                    </td>
                    <td>
                        <?php echo $Currency->format($value['amount']); ?>
                    </td>
                    <td></td>
                </tr>
                <?php $total_payments = $total_payments + $value['amount']; ?>
                <?php } ?>

                <tr>
                    <td>
                        <b><?php echo $l('table_detail_payment_approved_total'); ?></b>
                    </td>
                    <td>
                        <?php echo $Currency->format($total_payments); ?>
                    </td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td>
                        <?php echo $l('table_detail_payment_returned_total'); ?>
                    </td>
                    <td>&nbsp;</td>
                    <td>
                        <span><?php
                            if (($totals[count($totals)-1]['value'] - $total_payments) < 0) {
                                echo $Currency->format($total_payments - $totals[count($totals)-1]['value']);
                            } else {
                                echo $Currency->format(0);
                            }
                        ?></span>
                    </td>
                </tr>
                <tr>
                    <td><?php echo $l('table_detail_payment_topay_total'); ?></td>
                    <td></td>
                    <td>
                        <h2>
                        <?php
                            if (($totals[count($totals)-1]['value'] - $total_payments) > 0) {
                                echo $Currency->format($totals[count($totals)-1]['value'] - $total_payments);
                            } else {
                                    echo $Currency->format(0);
                            }
                            ?>
                        </h2>
                    </td>
                </tr>
            </table>
        </div>
        <div class="payment-form">
            <h2><?php echo $l('form_payment_title'); ?></h2>
            <ul id="paymentMethods" class="nt-editable">
                <?php foreach ($payment_methods as $payment_method) { ?>
                    <li data-action="payment">{%<?php echo $payment_method['id']; ?>%}</li>
                <?php } ?>
            </ul>
        </div>
        
    <!-- widgets -->
    <div class="large-12 medium-12 small-12 columns">
        <?php if($widgets) { ?><ul class="widgets"><?php foreach ($widgets as $widget) { ?>{%<?php echo $widget; ?>%}<?php } ?></ul><?php } ?>
    </div>
    <!-- widgets -->

    <?php include(DIR_TEMPLATE. $tpl ."/shared/columns-end.tpl"); ?>
</section>

<?php include(DIR_TEMPLATE. $tpl ."/shared/scripts/payment-methods.tpl"); ?>

<?php echo $footer; ?>