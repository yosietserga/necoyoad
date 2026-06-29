<?php $tpl = is_dir(DIR_TEMPLATE. $this->config->get('config_template') ."/shared") ? $this->config->get('config_template') : "choroni"; ?> 
<!--shipping-section -->
<?php if (isset($shipping_methods) || (!$isLogged || ($isLogged && !$shipping_country_id))) { ?>
<section id="necoWizardStep_3" data-wizard="step">
    <div class="delivery-form info-form">
    <?php if (!isset($isLogged) || ($isLogged && !$shipping_country_id)) { ?>


        <fieldset>
            <div class="heading widget-heading feature-heading form-heading" id="<?php echo $widgetName; ?>Header">
                <div class="heading-title">
                    <h3>
                        <?php echo $l('legend_shipping_form'); ?>
                    </h3>
                </div>
            </div>

            <?php include(DIR_TEMPLATE. $tpl ."/shared/fields/shipping/country.tpl"); ?>
            <?php include(DIR_TEMPLATE. $tpl ."/shared/fields/shipping/zone.tpl"); ?>
            <?php include(DIR_TEMPLATE. $tpl ."/shared/fields/shipping/city.tpl"); ?>
            <?php include(DIR_TEMPLATE. $tpl ."/shared/fields/shipping/street.tpl"); ?>
            <?php include(DIR_TEMPLATE. $tpl ."/shared/fields/shipping/postcode.tpl"); ?>
            <?php include(DIR_TEMPLATE. $tpl ."/shared/fields/shipping/address.tpl"); ?>

            <input type="hidden" name="payment_country_id" id="payment_country_id" value="<?php echo $payment_country_id??0; ?>" />
            <input type="hidden" name="payment_zone_id" id="payment_zone_id" value="<?php echo $payment_zone_id??0; ?>" />
            <input type="hidden" name="payment_street" id="payment_street" value="<?php echo $payment_street??""; ?>" />
            <input type="hidden" name="payment_city" id="payment_city" value="<?php echo $payment_city??""; ?>" />
            <input type="hidden" name="payment_postcode" id="payment_postcode" value="<?php echo $payment_postcode??""; ?>" />
            <input type="hidden" name="payment_address_1" id="payment_address_1" value="<?php echo $payment_address_1??""; ?>" />
        </fieldset>


    <?php } else { ?>


        <input type="hidden" name="payment_country_id" id="payment_country_id" value="<?php echo $payment_country_id??0; ?>" />
        <input type="hidden" name="payment_zone_id" id="payment_zone_id" value="<?php echo $payment_zone_id??0; ?>" />
        <input type="hidden" name="payment_city" id="payment_city" value="<?php echo $payment_city??""; ?>" />
        <input type="hidden" name="payment_street" id="payment_street" value="<?php echo $payment_street??""; ?>" />
        <input type="hidden" name="payment_postcode" id="payment_postcode" value="<?php echo $payment_postcode??""; ?>" />
        <input type="hidden" name="payment_address_1" id="payment_address_1" value="<?php echo $payment_address_1??""; ?>" />

        <input type="hidden" name="shipping_country_id" id="shipping_country_id" value="<?php echo $shipping_country_id??0; ?>" />
        <input type="hidden" name="shipping_zone_id" id="shipping_zone_id" value="<?php echo $shipping_zone_id??0; ?>" />
        <input type="hidden" name="shipping_city" id="shipping_city" value="<?php echo $shipping_city??""; ?>" />
        <input type="hidden" name="shipping_street" id="shipping_street" value="<?php echo $shipping_street??""; ?>" />
        <input type="hidden" name="shipping_postcode" id="shipping_postcode" value="<?php echo $shipping_postcode??""; ?>" />
        <input type="hidden" name="shipping_address_1" id="shipping_address_1" value="<?php echo $shipping_address_1??""; ?>" />


    <?php } ?>

    <?php if ($shipping_methods) { ?>


        <div class="shipping-methods break">
            <fieldset>
                <div class="heading widget-heading feature-heading form-heading" id="<?php echo $widgetName; ?>Header">
                    <div class="heading-title">
                        <h3>
                            <i class="heading-icon icon icon-envelope">
                                <?php include(DIR_TEMPLATE. $tpl . "/shared/icons/envelope.tpl"); ?>
                            </i>
                            <?php echo $l('text_shipping_methods'); ?>
                        </h3>
                    </div>
                </div>
                <table>
                    <thead>
                    <tr>
                        <th><?php echo $l('table_head_shipping_select'); ?></th>
                        <th><?php echo $l('table_head_shipping_method'); ?></th>
                        <th><?php echo $l('table_head_shipping_price'); ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($shipping_methods as $shipping_method) { ?>
                    <?php foreach ($shipping_method['quote'] as $quote) { ?>
                    <tr>
                        <td data-label="<?php echo $l('table_head_shipping_select'); ?>">
                            <div class="check-action">
                                <input data-check="order" type="radio" name="shipping_method" value="<?php echo $quote['id']; ?>" />
                                <i class="radio-button"></i>
                            </div>
                        </td>
                        <td data-shipping_title>
                            <?php echo $quote['title']; ?>
                        </td>
                        <td data-shipping_price data-label="<?php echo $l('table_head_shipping_price'); ?>">
                            <?php echo $quote['text']; ?>
                        </td>
                    </tr>
                    <?php } ?>
                    <?php } ?>
                    </tbody>
                </table>
            </fieldset>
        </div>


    <?php } ?>
    </div>
</section>
<?php } ?>
<!--/shipping section-->