<ul class="neco-wizard-controls" data-wizard="controls">
    <li id="necoWizardControl_1" data-wizard="nav" data-wizard-step="basket">
        <span><?php echo $l('text_basket'); ?></span>
    </li>

    <?php if (!isset($isLogged) || !$isLogged) { ?>
    <li id="necoWizardControl_2" data-wizard="nav" data-wizard-step="billing">
        <span><?php echo $l('text_billing'); ?></span>
    </li>
    <?php } ?>

    <?php if (isset($shipping_methods) || (!$isLogged || ($isLogged && !$shipping_country_id))) { ?>

    <li id="necoWizardControl_3" data-wizard="nav" data-wizard-step="shipping">
        <span><?php echo $l('text_shipping'); ?></span>
    </li>

    <?php }?>

    <li id="necoWizardControl_4" data-wizard="nav" data-wizard-step="confirm">
        <span><?php echo $l('text_confirm'); ?></span>
    </li>

    <li id="necoWizardControl_5" data-wizard="nav" data-wizard-step="complete">
        <span><?php echo $l('text_complete'); ?></span>
    </li>
</ul>