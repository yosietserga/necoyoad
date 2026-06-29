<?php if ($testmode) { ?>
<div class="warning"><?php echo $text_testmode; ?></div>
<?php } ?>

<?php if (isset($error) && $error) { ?>
<div class="warning"><?php echo $text_error; ?></div>
<?php } ?>

<?php if (!$error) { ?>
<form method="post" action="https://sandbox.gateway.payulatam.com/ppp-web-gateway/">
    <input name="merchantId"    type="hidden"  value="508029"   >
    <input name="accountId"     type="hidden"  value="512321" >
    <input name="description"   type="hidden"  value="Test PAYU"  >
    <input name="referenceCode" type="hidden"  value="TestPayU" >
    <input name="amount"        type="hidden"  value="3"   >
    <input name="tax"           type="hidden"  value="0"  >
    <input name="taxReturnBase" type="hidden"  value="0" >
    <input name="currency"      type="hidden"  value="USD" >
    <input name="signature"     type="hidden"  value="ba9ffa71559580175585e45ce70b6c37"  >
    <input name="test"          type="hidden"  value="1" >
    <input name="buyerEmail"    type="hidden"  value="test@test.com" >
    <input name="responseUrl"    type="hidden"  value="https://www.test.com/response" >
    <input name="confirmationUrl"    type="hidden"  value="https://www.test.com/confirmation" >
    <input name="Submit"        type="submit"  value="Enviar" >
</form>
<?php } ?>