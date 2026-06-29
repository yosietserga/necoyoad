<?php $tpl = is_dir(DIR_TEMPLATE. $this->config->get('config_template') ."/shared") ? $this->config->get('config_template') : "choroni"; ?> 
<div class="info-form">
    <form action="<?php echo str_replace('&', '&amp;', $settings['action']); ?>" method="post" enctype="multipart/form-data" id="create">
        <fieldset>
            <?php include(DIR_TEMPLATE. $tpl ."/shared/fragment/form-details-heading.tpl"); ?>
            <?php include(DIR_TEMPLATE. $tpl ."/shared/fields/email.tpl"); ?>
            <?php include(DIR_TEMPLATE. $tpl ."/shared/fields/name.tpl"); ?>
            <?php include(DIR_TEMPLATE. $tpl ."/shared/fields/lastname.tpl"); ?>
            <?php include(DIR_TEMPLATE. $tpl ."/shared/fields/company.tpl"); ?>
            <?php include(DIR_TEMPLATE. $tpl ."/shared/fields/rif.tpl"); ?>
            <?php include(DIR_TEMPLATE. $tpl ."/shared/fields/birthday.tpl"); ?>
            <?php include(DIR_TEMPLATE. $tpl ."/shared/fields/telephone.tpl"); ?>
        </fieldset>

        <fieldset>
            <?php include(DIR_TEMPLATE. $tpl ."/shared/fragment/form-address-heading.tpl"); ?>
            <?php include(DIR_TEMPLATE. $tpl ."/shared/fields/country.tpl"); ?>
            <?php include(DIR_TEMPLATE. $tpl ."/shared/fields/zone.tpl"); ?>
            <?php include(DIR_TEMPLATE. $tpl ."/shared/fields/city.tpl"); ?>
            <?php include(DIR_TEMPLATE. $tpl ."/shared/fields/street.tpl"); ?>
            <?php include(DIR_TEMPLATE. $tpl ."/shared/fields/postcode.tpl"); ?>
            <?php include(DIR_TEMPLATE. $tpl ."/shared/fields/extra-address.tpl"); ?>
        </fieldset>

        <fieldset>
            <?php include(DIR_TEMPLATE. $tpl ."/shared/fragment/form-password-heading.tpl"); ?>
            <?php include(DIR_TEMPLATE. $tpl ."/shared/fields/password.tpl"); ?>
            <?php include(DIR_TEMPLATE. $tpl ."/shared/fields/confirm.tpl"); ?>
        </fieldset>

        <?php 
        if (isset($error) && !empty($error)) {
            foreach ($error as $v) {
                echo "<span class='error'>$v</span>";
            } //end foreach
        } //end if 
        ?>
        <input type="hidden" name="activation_code" value="<?php echo md5(rand(1000000,99999999)); ?>"/>
        <div class="necoform-actions" data-actions="necoform"></div>
        <input type="submit" />
    </form>
</div>

<script>
    (function () {
        window.fetchStyle('<?php echo HTTP_CSS; ?>jquery-ui/jquery-ui.min.css');
        window.deferjQuery(function () {
            window.appendScriptSource('<?php echo HTTP_JS; ?>necojs/neco.form.js');
            window.appendScriptSource('<?php echo HTTP_JS; ?>vendor/jquery-ui.min.js');
        });

        window.deferPlugin('ntForm', function () {
            var emailField = $('#email');
            var companyField = $('#company');
            var namesFields = $('#firstname, #lastname');
            var firstNameField = $(namesFields[0]);
            var lastNameField = $(namesFields[1]);

            var showInputErrorFeedback = function (target, message) {
                var formEntry = target.parent();
                var messageElement = $('<p>').attr({
                    id: 'emailCheckError',
                })
                    .addClass('neco-submit-error')
                    .html(message);

                target.removeClass('neco-input-success')
                    .addClass('neco-input-error');
                formEntry.append(messageElement);
            };

            var showInputSuccessFeedback = function (target) {
                var messageElement = $('#emailCheckError');
                target.removeClass('neco-input-error')
                    .addClass('neco-input-success');
                if (messageElement.length) {
                    messageElement.remove();
                }
            };

            $('#create').ntForm({
                lockButton:false,
                url: "<?php echo str_replace('&', '&amp;', $settings['action']); ?>",
            });

            emailField.on('change',function(e){
                    $.post('<?php echo Url::createUrl("account/register/checkemail");?>', {email: $(this).val()},
                        function(response){
                            $('#tempLink').remove();
                            var data = $.parseJSON(response);
                            if (typeof data.error !== 'undefined') {
                                showInputErrorFeedback(emailField, data.msg);
                            } else {
                                showInputSuccessFeedback(emailField);
                            }
                        });
                }
            );
            namesFields.on('change',function(e){
                var firstNameText = firstNameField.val();
                var lastNameText = lastNameField.val();
                var companyText = companyField.val();

                if (firstNameText.length !== 0 && lastNameText.length !== 0) {
                    if (companyText.length === 0 ) {
                        companyField.val(firstNameText + ' ' + lastNameText).trigger("keypress");
                    }
                }
            });
        });

    })();

</script>