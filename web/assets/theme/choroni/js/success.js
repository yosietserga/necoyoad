$(function(){
    var body =  $('body');
    var methods = $('*[data-action="payment"]');
    
    var loadingIcon = ['<i data-loader="payment" class="spinner-loader icon">',
                            '<?php include(DIR_TEMPLATE. $this->config->get("config_template") . "/shared/icons/loader.tpl"); ?>',
                      '</i>'].join('');

    var overlayClosedStateStyle = {
        opacity: 0,
    };
    var overlayOpenStateStyle = {
        opacity: 1,
        position: "fixed",
        overflow: 'hidden'
    };


    var overlay, appendMessage, clearForm, onCloseOverlay, createMessage, createOverlay;

    appendMessage = function (target, messageElement) {
        target.find('[data-loader="payment"]').remove();
        target.append(messageElement);
    };
    clearForm = function () {
        form.find('input')
            .val('')
            .removeAttr('checked')
            .removeClass('neco-input-success')
            .removeClass('neco-input-error');
        form.find('select')
            .removeClass('neco-input-success')
            .removeClass('neco-input-error');
        form.find('textarea')
            .val('')
            .removeClass('neco-input-success')
            .removeClass('neco-input-error');
    };
    createMessage = function (type, message) {
        var message = $('<div>').attr({
            'id': 'temp',
            'class': 'message overlayed ' +  type
        }).html(message);
        return message;
    };
    createOverlay = function () {
        var overlay = $('<div>');
        overlay.attr({
            'class':'overlay-view',
            'id': 'temp'
        }).css(overlayOpenStateStyle).html(loadingIcon);
        overlay.click(onCloseOverlay(overlay));
        return overlay;
    };
    onCloseOverlay = function (target) {
        var $target = $(target);
        $target.css(overlayClosedStateStyle);
        $target.on("transitionend webkitTransitionEnd oTransitionEnd MSTransitionEnd", function (e) {
            body.css({
                overflow: 'auto',
                marginRight: '0rem'
            });
            $target.remove();
        });
    };
    var initPaymentMethod = function (form, guide) {
        var overlay = createOverlay();
        var config = {
            ajax:form.data('ajax'),
            url:form.attr('action'),
            beforeSend: function() {
                body.css({
                    overflow: 'hidden',
                    marginRight: '1.063rem'
                });
                guide.append(overlay);
            },
            success:function(data) {
                if (typeof data.error !== 'undefined' && typeof data.msg !== 'undefined') {
                    appendMessage(overlay, createMessage('error', data.msg));
                }
                else if (typeof data.warning !== 'undefined') {
                    appendMessage(overlay, createMessage('warning', data.msg));
                }
                else if (typeof data.success !== 'undefined') {
                    clearForm();
                    appendMessage(overlay, createMessage('success', data.msg));
                }
                if (typeof data.redirect !== 'undefined') {
                    window.location.href = data.redirect;
                }
            }
        };
        form.ntForm( config );
    };
    console.log(methods);
    methods.each(function (i, method) {
        var guide = $(method).find('[data-guide="payment"]');
        var form = $(method).find('[data-form="payment"]');
        initPaymentMethod(form, guide);
    });
});
  