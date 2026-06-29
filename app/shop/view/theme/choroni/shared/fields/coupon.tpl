<div class="coupon-entry form-entry">
    <input data-label="<?php echo $l('Apply A Coupon'); ?>" type="text" id="input-coupon" name="coupon" value="" placeholder="<?php echo $l('Apply A Coupon'); ?>"/>
    <div class="action-step" id="apply-coupon-button"><?php echo $l('Apply A Coupon'); ?></div>
</div>
<script>
    (function ($) {
        $('#apply-coupon-button').on('click', function () {
            $(this).text('<?php echo $l('Loading...'); ?>');
            var that = this;
            $.post('<?php echo $Url::createUrl("total/coupon/coupon"); ?>',
            {
                coupon:$('input[name="coupon"]').val()
            }).done(function(resp){
                var json = $.parseJSON(resp);
                $(that).text('<?php echo $l('Apply A Coupon'); ?>');
                if (json['error']) {
                    $('.coupon-entry').append('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> ' + json['error'] + '<button type="button" class="close" data-dismiss="alert">&times;</button></div>');
                }
                if (json['redirect']) {
                    $(that).text('<?php echo $l('Refreshing...'); ?>');
                    location = json['redirect'];
                }
            });
        });
    })(jQuery);
</script>