<div class="heading large-heading-dropdown" id="<?php echo $widgetName; ?>Header">
    <div  onclick="$('#paypalGuide').slideToggle();" class="heading-title">
        <h3> 
            <?php echo $l('text_title'); ?>
        </h3>
    </div>
</div>

<div class="simple-form guide" id="paypalGuide" style="display: none;" data-guide="payment">
    <?php if (!empty($instructions)) { echo $instructions; } ?>
    
    <form action="<?php echo str_replace('&', '&amp;', $action); ?>" method="post" id="paypalCheckoutForm" data-form="payment" data-async>
        <input type="hidden" name="cmd" value="_xclick">
        <input type="hidden" name="business" value="<?php echo $business; ?>">
        <input type="hidden" name="item_name" value="<?php echo $item_name; ?>">
        <input type="hidden" name="currency_code" value="<?php echo $currency_code; ?>">
        <input type="hidden" name="amount" value="<?php echo $amount; ?>">
        <input type="hidden" name="first_name" value="<?php echo $first_name; ?>">
        <input type="hidden" name="last_name" value="<?php echo $last_name; ?>">
        <input type="hidden" name="address1" value="<?php echo $address1; ?>">
        <input type="hidden" name="address2" value="<?php echo $address2; ?>">
        <input type="hidden" name="city" value="<?php echo $city; ?>">
        <input type="hidden" name="zip" value="<?php echo $zip; ?>">
        <input type="hidden" name="country" value="<?php echo $country; ?>">
        <input type="hidden" name="address_override" value="0">
        <input type="hidden" name="notify_url" value="<?php echo $notify_url; ?>">
        <input type="hidden" name="email" value="<?php echo $email; ?>">
        <input type="hidden" name="invoice" value="<?php echo $invoice; ?>">
        <input type="hidden" name="lc" value="<?php echo $lc; ?>">
        <input type="hidden" name="return" value="<?php echo $return; ?>">
        <input type="hidden" name="rm" value="2">
        <input type="hidden" name="no_note" value="1">
        <input type="hidden" name="cancel_return" value="<?php echo $cancel_return; ?>">
        <input type="hidden" name="paymentaction" value="<?php echo $paymentaction; ?>">
        <input type="hidden" name="custom" value="<?php echo $custom; ?>">
    </form>
</div>

