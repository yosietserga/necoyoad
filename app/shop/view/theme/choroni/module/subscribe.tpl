<?php $tpl = is_dir(DIR_TEMPLATE. $this->config->get('config_template') ."/shared") ? $this->config->get('config_template') : "choroni"; ?>
<?php include(DIR_TEMPLATE. $tpl ."/shared/widget-head.tpl");?> 

    <?php include(DIR_TEMPLATE. $tpl ."/shared/module-heading.tpl");?> 

    <div class="widget-content" id="<?php echo $widgetName; ?>Content" >
        <div class="form-container">
            <div class="slogan">
                <span><?php echo $l('text_slogan'); ?></span>
            </div>
            <div class="action-input">
                <form name="subscribe" id="<?php echo $widgetName; ?>_subscribe_form" class="subscribe-form">
                    <div class="row collapse">
                        <div class="input-newsletter large-10 medium-10 small-10 columns">
                            <input type="email" name="subscribe_email" id="<?php echo $widgetName; ?>_subscribe_email" value="" placeholder="Ingresa tu email" />
                        </div>
                        <div class="large-2 medium-2 small-2 columns">
                            <a id="<?php echo $widgetName; ?>_submit_subscribe">
                                <i class="icon icon-envelope">
                                    <?php include(DIR_TEMPLATE. $tpl . "/shared/icons/envelope.tpl"); ?>
                                </i>
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php include(DIR_TEMPLATE. $tpl ."/shared/widget-footer.tpl"); ?>
<script id="subscribe">
    function initSubscribe () {
        $('#<?php echo $widgetName; ?>_submit_subscribe').on('click',function(event){
            $.post('<?php echo $Url::createUrl("module/subscribe/subscribe"); ?>',
            $('#<?php echo $widgetName; ?>_subscribe_form').serialize(),
            function(response){
                $('#temp').remove();
                $('#<?php echo $widgetName; ?>_subscribe_email').removeClass('neco-input-error');

                try {
                   data = $.parseJSON(response);
                } catch(error) {
                   data = response;
                }

                if (typeof data.success != 'undefined') {
                    $('#<?php echo $widgetName; ?>_subscribe_form input').val('');
                    $('#<?php echo $widgetName; ?>_subscribe_email').after('<div class="message success" id="temp">'+ data.msg +'</div>');
                }

                if (typeof data.error != 'undefined') {
                    $('#<?php echo $widgetName; ?>_subscribe_email').addClass('neco-input-error').after('<div class="message warning" id="temp">'+ data.msg +'</div>');
                }
            });
        });
    }
    window.deferjQuery(initSubscribe);

</script>