<div class="heading large-heading-dropdown" id="<?php echo $widgetName; ?>Header">
    <div  onclick="$('#payuWebCheckoutGuide').slideToggle();" class="heading-title">
        <h3> 
            <?php echo $l('text_title'); ?>
        </h3>
    </div>
</div>

<div class="simple-form guide" id="payuWebCheckoutGuide" style="display: none;" data-guide="payment">
    <?php if (!empty($instructions)) { echo $instructions; } ?>
    
    <form action="<?php echo str_replace('&', '&amp;', $action); ?>" method="post" id="payuCheckoutForm" data-form="payment">
        <input name="merchantId" type="hidden" value="<?php echo $merchantId; ?>" />
        <input name="accountId" type="hidden" value="<?php echo $accountId; ?>" />
        <input name="ApiKey" type="hidden" value="<?php echo $ApiKey; ?>" />
        <input name="description" type="hidden" value="<?php echo $description; ?>" />
        <input name="referenceCode" type="hidden" value="<?php echo $referenceCode; ?>" />
        <input name="amount" type="hidden" value="<?php echo $amount; ?>" />
        <input name="tax" type="hidden" value="<?php echo $tax; ?>" />
        <input name="taxReturnBase" type="hidden" value="<?php echo $taxReturnBase; ?>" />
        <input name="currency" type="hidden" value="<?php echo $currency; ?>" />
        <input name="signature" type="hidden" value="<?php echo $signature; ?>" />
        <input name="test" type="hidden" value="<?php echo $test; ?>" />
        <input name="buyerEmail" type="hidden" value="<?php echo $buyerEmail; ?>" />
        <input name="responseUrl" type="hidden" value="<?php echo $responseUrl; ?>" />
        <input name="confirmationUrl" type="hidden" value="<?php echo $confirmationUrl; ?>" />
        <input name="shippingAddress" type="hidden" value="<?php echo $shippingAddress; ?>" >
        <input name="shippingCity" type="hidden" value="<?php echo $shippingCity; ?>" >
        <input name="shippingCountry" type="hidden" value="<?php echo $shippingCountry; ?>" >

    </form>
</div>

