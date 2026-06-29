<?php $tpl = is_dir(DIR_TEMPLATE. $this->config->get('config_template') ."/shared") ? $this->config->get('config_template') : "choroni"; ?> <!--  request-section -->
<section id="necoWizardStep_5" class="wizard-step processing-step" data-wizard="step">
    <div class="loader">
    loading...
    </div>
    <div class="text-block" style="text-align:center">
        <p>
            <?php echo $l('help_processing'); ?>
        </p>
    </div>

</section>
<!--  request-section -->