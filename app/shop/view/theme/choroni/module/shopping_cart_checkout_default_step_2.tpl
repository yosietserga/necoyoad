<?php $tpl = is_dir(DIR_TEMPLATE. $this->config->get('config_template') ."/shared") ? $this->config->get('config_template') : "choroni"; ?> 
<?php if (!isset($isLogged) || !$isLogged) { ?>
<!-- address info -->
<section id="necoWizardStep_2" data-wizard="step">
    <div class="recipe-info info-form">
        <fieldset>
            <div class="heading widget-heading feature-heading form-heading" id="<?php echo $widgetName; ?>Header">
                <div class="heading-title">
                    <h3>
                        <?php echo $l('legend_recipe_form'); ?>
                    </h3>
                </div>
            </div>
            <?php if (isset($isLogged) && $isLogged) { ?>
            <a href="index.php?r=account/account" title="<?php echo $l('text_update'); ?>"></a>
            <?php } ?>

            <?php include(DIR_TEMPLATE. $tpl ."/shared/fields/email.tpl"); ?>
            <?php include(DIR_TEMPLATE. $tpl ."/shared/fields/name.tpl"); ?>
            <?php include(DIR_TEMPLATE. $tpl ."/shared/fields/lastname.tpl"); ?>
            <?php include(DIR_TEMPLATE. $tpl ."/shared/fields/company.tpl"); ?>
            <?php include(DIR_TEMPLATE. $tpl ."/shared/fields/rif.tpl"); ?>
            <?php include(DIR_TEMPLATE. $tpl ."/shared/fields/telephone.tpl"); ?>
            <?php include(DIR_TEMPLATE. $tpl ."/shared/fields/referenceby.tpl"); ?>
            <?php include(DIR_TEMPLATE. $tpl ."/shared/fields/payment/location.tpl"); ?>
            <?php include(DIR_TEMPLATE. $tpl ."/shared/fields/payment/city.tpl"); ?>
            <?php include(DIR_TEMPLATE. $tpl ."/shared/fields/payment/street.tpl"); ?>
            <?php include(DIR_TEMPLATE. $tpl ."/shared/fields/payment/postcode.tpl"); ?>
            <?php include(DIR_TEMPLATE. $tpl ."/shared/fields/payment/address.tpl"); ?>

        </fieldset>
    </div>
</section>
<!-- /address-info -->
<?php } ?>