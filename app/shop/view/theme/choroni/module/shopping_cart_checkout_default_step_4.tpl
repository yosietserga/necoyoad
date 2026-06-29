<!--payment section -->
<section id="necoWizardStep_4" data-wizard="step" class="break">
    <div class="payment-section row">


        <div class="recipe-data large-6 medium-6 small-12 data">
            <h3 class="payment-heading data-heading"><?php echo $l('text_order_confirm'); ?></h3>
            <ul class="confirmOrder">
                <li>
                    <span><?php echo $l('text_company'); ?>:</span>
                    <span id="confirmCompany"><?php echo $company??""; ?></span>
                </li>
                <li>
                    <span><?php echo $l('text_rif'); ?>:</span>
                    <span id="confirmRif"><?php echo $riff??""; ?></span>
                </li>
                <li>
                    <span><?php echo $l('text_address'); ?>:</span>
                    <span id="confirmPaymentAddress"><?php echo $payment_address??""; ?></span>
                </li>
            </ul>
        </div>


        <div class="shipping-data large-6 medium-6 small-12 data">
            <h3 class="shipping-heading data-heading"><?php echo $l('text_shipping_address_and_method'); ?></h3>
            <ul class="confirmOrder">
                <?php if (isset($shipping_methods) && $shipping_methods) { ?>
                <li>
                    <span><?php echo $l('text_payment_shipping_method'); ?>:</span>
                    <span id="shipping_method"></span>
                </li>
                <?php } ?>
                <li>
                    <span><?php echo $l('text_payment_address'); ?>:</span>
                    <span id="confirmShippingAddress"><?php echo $shipping_address??""; ?></span>
                </li>
            </ul>
        </div>


        <div class="cart-summary large-12 medium-12 small-12">
            <table class="cart-recipe">
                <thead>
                <tr>
                    <th><?php echo $l('text_summary_description'); ?></th>
                    <th><?php echo $l('text_summary_model');?></th>
                    <th><?php echo $l('text_summary_cant');?></th>
                    <?php if ($display_price && $Config->get('config_store_mode')=='store') { ?>
                    <th><?php echo $l('text_summary_price');?></th>
                    <th><?php echo $l('text_summary_total');?></th>
                    <?php } ?>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($products as $product) { ?>
                <tr id="confirmItem<?php echo $product['product_id']; ?>">
                    <td>
                        <a title="<?php echo $product['name']; ?>" href="<?php echo str_replace('&', '&amp;', $product['href']); ?>">
                            <?php echo $product['name']; ?>
                        </a>
                        <div>
                            <?php foreach ($product['option'] as $option) { ?>- <small><?php echo $option['name']; ?> <?php echo $option['value']; ?></small>
                            <?php } ?>
                        </div>
                    </td>
                    <td data-label="<?php echo $l('text_summary_model'); ?>">
                        <?php echo $product['model']; ?>
                    </td>
                    <td data-label="<?php echo $l('text_summary_cant'); ?>" id="confirmQty<?php echo $product['product_id']; ?>">
                        <?php echo $product['quantity']; ?>
                    </td>
                    <?php if ($display_price && $Config->get('config_store_mode') === 'store') { ?>
                    <td data-label="<?php echo $l('text_summary_price'); ?>">
                        <?php echo $product['price']; ?>
                    </td>
                    <td data-label="<?php echo $l('text_summary_total'); ?>" id="confirmTotal<?php echo $product['product_id']; ?>">
                        <?php echo $product['total']; ?>
                    </td>
                    <?php } ?>
                </tr>
                <?php } ?>
                </tbody>
            </table>
            <?php if ($display_price && $Config->get('config_store_mode') === 'store') { ?>
            <table id="totalsConfirm" class="cart-totals">
                <?php foreach ($totals as $total) { ?>
                <tr>
                    <td><?php echo $total['title']; ?></td>
                    <td><?php echo $total['text']; ?></td>
                </tr>
                <?php } ?>
            </table>
            <?php } ?>
        </div>

        <div class="confirmation-comment column">
            <textarea name="comment" placeholder="Ingresa tus comentarios sobre el pedido aqu&iacute;"></textarea>
        </div>

    </div>
</section>
<!-- payment-section-->