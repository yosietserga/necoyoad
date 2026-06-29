/**
 * NecoForm
 * Author: Yosiet Serga
 * Contributors: Jesús Bejarano
 * Version: 1.0.1
 *
 * Dual licensed under the MIT and GPL licenses
 *
 */
(function ($) {
    'use strict';
    $.fn.ntForm = function (method) {
        var defaults = {
            url: '',
            enctype: 'multipart/form-data',
            ajax: false,
            type: 'post',
            dataType: 'json',
            classname: 'neco-form',
            lockButton: false,
            submitButton: true,
            cancelButton: true,
            loading: {
                title: 'Cargando...',
                image: '../loader.gif',
                classname: 'neco-form-loading'
            },
            error: {
                classname: 'neco-form-error',
                text: 'Lo sentimos pero no se pudo procesar el formulario'
            },
            options: {},
            create: function () {
            },
            beforeSend: function () {
            },
            complete: function () {
            },
            success: function () {
            },
            submit: function () {
            }
        };

        var settings = {};
        var data = {};
        var methods = {
            init: function (options) {
                return this.each(function () {
                    settings = $.extend({}, defaults, options);
                    data.element = $(this);
                    helpers._create();
                    data.container = data.element.find('.' + settings.classname);
                });

            }
        };
        var helpers = {
            _create: function () {
                var formCounter = 0;
                var actions = $("[data-action='necoform']");
                var formActions = (actions.length) ? actions : data.element;
                var template = Object.freeze({
                    acceptButton: "<div class='action-button action-accept' style='margin-right: 0.875rem; display:inline-block;'><a>" + window.I18n.Common.accept + "</a></div>",
                    cancelButton: "<div class='action-button action-cancel' style='margin-right: 0.875rem; display:inline-block;'><a>" + window.I18n.Common.cancel + "</a></div>",
                    unlockButton: "<div id='slide-to-unlock'></div><div id='neco-unlock-slider-wrapper'><div id='neco-unlock-slider'><div class='ui-slider-handle'></div></div></div>"
                });
                var patterns = Object.freeze({
                    alphaNumeric: /^\D+$/i,
                    email: /^[_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)*\.(([0-9]{1,3})|([a-zA-Z]{2,4})|(aero|coop|info|museum|name))$/i,
                    date: /^(0[1-9]|[12][0-9]|3[01])+[\-\/]+(0[1-9]|1[012])+[\-\/]+(19|20)[0-9]{2}/i,
                    rif: /\b[JGVE]-[0-9]{8}-[0-9]{1}\b/i,
                    numeric: /^\d+$/i,
                    phone: /\(?(\d{4})\)? ?(\d{3})+\.(\d{2})+\.(\d{2})/i,
                    password: /^.*(?=.{6,})(?=.*\d)(?=.*[a-zA-Z]).*$/i,
                    notAllowed: /.["\\\/\{\}\[\]\+']/i
                });
                var submitButton = $(template.acceptButton);
                var cancelButton = $(template.cancelButton);

                var showFeedbackOnSubmit = function (context, msg) {
                    var warning;
                    context = $(context);
                    $("#tempError").remove();
                    warning = $('<p>')
                        .attr("id", "tempError")
                        .addClass("neco-submit-error")
                        .text(msg);
                    if (context.hasClass('neco-input-success')) {
                        context.removeClass('neco-input-success');
                    }
                    context.addClass('neco-input-error');
                    context.after(warning);
                    context.focus();
                };

                var validateInput = function (pattern, element) {
                    pattern = new RegExp(pattern);
                    if (element.attr('required') === 'required') {
                        return pattern.test(element.val());
                    }
                    return true;
                };

                if (data.element.length === 0) {
                    data.element = $('body');
                    $(data.element).find('form').each(function () {
                        $(this).attr('id', 'neco-form-' + formCounter).addClass('neco-form');
                        formCounter = formCounter + 1 * 1;

                        $(data.element).find('input').each(function () {
                            $(this).ntInput($(this).attr('type'));
                        });

                        $(data.element).find('select').each(function () {
                            $(this).ntSelect();
                        });

                        $(data.element).find('textarea').each(function () {
                            $(this).ntTextArea();
                        });
                    });
                }
                if (data.element.length > 0 && $(data.element).get(0).tagName !== 'FORM') {
                    if ($(data.element).find('form')) {
                        $(data.element).find('form').each(function () {
                            $(this).attr('id', 'neco-form-' + formCounter).addClass('neco-form');
                            formCounter = formCounter + 1 * 1;

                            $(data.element).find('input').each(function () {
                                $(this).ntInput($(this).attr('type'));
                            });

                            $(data.element).find('select').each(function () {
                                $(this).ntSelect();
                            });

                            $(data.element).find('textarea').each(function () {
                                $(this).ntTextArea();
                            });
                        });
                    }
                }
                if ($(data.element).get(0).tagName === 'FORM') {
                    $(data.element).addClass('neco-form').attr({
                        action: settings.url,
                        method: settings.type,
                        enctype: settings.enctype,
                        name: 'neco-form-' + formCounter
                    });
                    $(data.element).find('input').each(function () {
                        $(this).ntInput();
                    });
                }

                $(data.element).find('label').each(function () {
                    $(this).addClass('neco-label');
                });

                setTimeout(function () {
                    if (settings.submitButton) {
                      submitButton.appendTo(formActions);
                    }
                    if (settings.cancelButton) {
                      cancelButton.appendTo(formActions);
                    }
                    $(cancelButton).on('click', function (e) {
                        $(data.element).find('input').each(function () {
                            $(this).val('').removeClass('neco-input-error').removeClass('neco-input-success');
                            $("#tempError").remove();
                        });
                    });
                }, 3000);


                if (settings.lockButton) {
                    var unlockButton = $(document.createElement('div')).attr({
                       id:'neco-unlock-form'
                    }).html('<div id="slide-to-unlock"></div><div id="neco-unlock-slider-wrapper"><div id="neco-unlock-slider"><div class="ui-slider-handle"></div></div></div>').appendTo(data.element);

                    $("#neco-unlock-slider").slider({
                		animate:true,
                		slide: function(e,ui) {
                			$("#slide-to-unlock").css("opacity", 1-(parseInt($(ui.handle).css("left"))/120));
                		},
                        stop:function(e,ui) {
                            var left = Math.round($(ui.handle).position().left);
                			if(left > 200) {
                				 $(unlockButton).fadeOut(function(data){
         				             $(unlockButton).remove();
                        			 $(submitButton).fadeIn();
                        			 $(cancelButton).fadeIn();
                				 });
                			} else {
                			     $(ui.handle).animate({left: 0}, 200 );
    				            $("#slide-to-unlock").animate({opacity: 1}, 200 );
                			}
                        }
              		});
                } else {
                    if (settings.submitButton) $(submitButton).fadeIn();
                    if (settings.cancelButton) $(cancelButton).fadeIn();
                }

                $(submitButton).on('click', function () {
                    var msg;
                    var error = false;
                    var top, input;
                    $(data.element).find('input').each(function (i, element) {

                        var $self = $(this);
                        top = $self.offset().top;
                        var value = !!$self.val();
                        var required = $self.attr('required');
                        var type = $self.attr('type');

                        if (type === 'email' && !validateInput(patterns.email, $self)) {
                            error = true;
                            showFeedbackOnSubmit($self, window.I18n.Form.Warnings.email);
                        }

                        if (type === 'fullname' && !validateInput(patterns.alphaNumeric, $self)) {
                            error = true;
                            showFeedbackOnSubmit($self, window.I18n.Form.Warnings.fullname);
                        }

                        if (type === 'firstname' && !validateInput(patterns.alphaNumeric, $self)) {
                            error = true;
                            showFeedbackOnSubmit($self, window.I18n.Form.Warnings.firstname);
                        }

                        if (type === 'lastname' && !validateInput(patterns.alphaNumeric, $self)) {
                            error = true;
                            showFeedbackOnSubmit($self, window.I18n.Form.Warnings.lastname);
                        }

                        if (type === 'necoDate' && !validateInput(patterns.date, $self)) {
                            error = true;
                            showFeedbackOnSubmit($self, window.I18n.Form.Warnings.date);
                        }

                        /*if (type === 'numeric' && !validateInput(patterns.numeric, $self)) {
                         error = true;
                         showFeedbackOnSubmit($self, warnings.numeric);
                         }*/

                        if (!value && required && !error) {
                            error = true;
                            showFeedbackOnSubmit($self, window.I18n.Form.Warnings.empty);
                        }

                        if (patterns.notAllowed.test($(this).val()) && !error && type !== 'password' && type !== 'hidden' && type !== 'date' && type !== 'necoDate') {
                            error = true;
                            showFeedbackOnSubmit($self, window.I18n.Form.Warnings.notAllowed);
                        }

                        if ($(this).hasClass('neco-input-error') && !error) {
                            error = true;
                            $("#tempError").remove();
                            msg = $('<p>').attr('id', 'tempError')
                                .addClass('neco-submit-error')
                                .text(window.I18n.Form.Warnings.noErrors);
                        }
                        if (error) {
                            return false;
                        }
                    });

                    if (error) {
                        $(this).before(msg);
                        $('html, body').animate({scrollTop: top}, 'slow');
                    } else {
                        error = false;
                        if (settings.ajax) {
                            $.ajax({
                                type: settings.type,
                                dataType: settings.dataType,
                                data: $(data.element).serialize(),
                                url: settings.url,
                                beforeSend: helpers._beforeSend(),
                                complete: helpers._complete(),
                                success: function (data) {
                                    helpers._success(data);
                                }
                            });
                        } else {
                            $(data.element).submit();
                        }
                    }
                });

                if (typeof settings.create === 'function') {
                    settings.create();
                }
            },
            _beforeSend: function () {
                if (typeof settings.beforeSend === "function") {
                    settings.beforeSend();
                }
            },
            _complete: function () {
                if (typeof settings.complete === "function") {
                    settings.complete();
                }
            },
            _success: function (data) {
                if (typeof settings.success === "function") {
                    settings.success(data);
                }
            },
            _submit: function () {
                if (typeof settings.mouseleave === "function") {
                    settings.mouseleave(this, data.li);
                }
            }
        };

        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || !method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error('Method "' + method + '" does not exist in ntForm plugin!');
        }
    };
})(jQuery);

/**
 * NecoInput
 * Author: Yosiet Serga
 * Version: 1.0.1
 *
 * Dual licensed under the MIT and GPL licenses
 *
 */
(function ($) {
    'use strict';
    $.fn.ntInput = function (method) {
        var PLUGIN = "neco-form";
        var defaults = {
            error: false,
            message: false,
            pattern: '',
            format: '',
            thousands: '.',
            decimals: ',',
            showQuick: true,
            focus: function () {},
            blur: function () {},
            keydown: function () {},
            change: function () {},
            options: {},
            loading: {
                title: 'Comprobando...',
                image: '../loader.gif',
                classname: 'neco-input-loading'
            }
        };
        var patterns = Object.freeze({
            alphaNumeric: /^\D+$/i,
            email: /^[^@]+@[^@\\.]+[\\.].+/i,
            date: /^(0[1-9]|[12][0-9]|3[01])+[\-\/]+(0[1-9]|1[012])+[\-\/]+(19|20)[0-9]{2}/i,
            rif: /\b[JGVE]-[0-9]{8}-[0-9]{1}\b/i,
            numeric: /^\d+$/i,
            phone: /\(?(\d{4})\)? ?(\d{3})+\.(\d{2})+\.(\d{2})/i,
            password: /^.*(?=.{6,})(?=.*\d)(?=.*[a-zA-Z]).*$/i,
            place: /^[^!<>;?=+@#"°{}_$%]+$/,
            plain: /^[^<>{}]+$/
        });

        /*Classes Events DataType*/

        var settings = {};
        var data = {};
        var methods = {
            init: function (options) {
                return this.each(function () {
                    settings = $.extend({}, defaults, options);
                    data.element = $(this);
                    helpers._create();
                    helpers._focus();
                    helpers._blur();
                    helpers._keydown();
                    helpers._change();
                });
            },
            date: function () {
            },
            text: function () {
            },
        };

        var helpers = {
            _create: function () {
                data.type = $(data.element).attr('type');
                if (data.type === 'hidden') {
                    return;
                }
                $(data.element).addClass('neco-input-' + data.type);
                $('*', data.element).change(helpers._change);
                $('*', data.element).keydown(helpers._keydown);

                if (data.type === 'rif') {
                    helpers.maskInput("a-99999999-9", " ", data.element);

                    data.element.on('change', function (e) {
                        e.stopPropagation();
                        helpers.validateAndFeedInput(window.I18n.Form.Warnings.rif, patterns.rif, data);
                    });

                    $(data.element).on('change', function (e) {
                        $(this).val(this.value.charAt(0).toUpperCase() + this.value.slice(1));
                    });
                }

                if (data.type === 'text') {
                    helpers.actionOnEvent(
                        data.element,
                        'keypress blur',
                        helpers.validateAndFeedTextInput(window.I18n.Form.Warnings.plain,
                            patterns.plain, data));
                    helpers.actionOnEvent(
                        data.element,
                        'focusout',
                        helpers.validateTextInputEmptiness(data));
                }

                if (data.type === 'fullname') {
                    helpers.actionOnEvent(
                        data.element,
                        'keypress blur',
                        helpers.validateAndFeedTextInput(window.I18n.Form.Warnings.alphaNumeric,
                            patterns.alphaNumeric, data));
                    helpers.actionOnEvent(
                        data.element,
                        'focusout',
                        helpers.validateTextInputEmptiness(data));
                }

                if (data.type === 'firstname') {
                    helpers.actionOnEvent(
                        data.element,
                        'keypress blur',
                        helpers.validateAndFeedTextInput(window.I18n.Form.Warnings.alphaNumeric,
                            patterns.alphaNumeric, data));
                    helpers.actionOnEvent(
                        data.element,
                        'keypress blur',
                        helpers.validateTextInputEmptiness(data));
                }

                if (data.type === 'lastname') {
                    helpers.actionOnEvent(
                        data.element,
                        'keypress blur',
                        helpers.validateAndFeedTextInput(window.I18n.Form.Warnings.alphaNumeric,
                            patterns.alphaNumeric, data));
                    helpers.actionOnEvent(
                        data.element,
                        'focusout',
                        helpers.validateTextInputEmptiness(data));
                }

                if (data.type === 'necoDate') {
                    data.element.attr("type", "necoDate");
                    $(data.element).datepicker({
                        changeMonth: true,
                        changeYear: true,
                        dateFormat: 'dd/mm/yy'
                    });
                    helpers.actionOnEvent( data.element, 'change',
                        helpers.validateAndFeedTextInput(window.I18n.Form.Warnings.date, patterns.date, data));
                }

                if (data.type === 'email') {
                    helpers.actionOnEvent(
                        data.element,
                        'keypress blur',
                        helpers.validateAndFeedTextInput(window.I18n.Form.Warnings.email, patterns.email, data));

                    helpers.actionOnEvent(
                        data.element,
                        'focusout',
                        helpers.validateTextInputEmptiness(data));
                }

                if (data.type === 'money') {
                    $(data.element).autoNumeric({aSep: settings.thousands, aDec: settings.decimals});
                    if ($(data.element).val().length === 0) {
                        $(data.element).val('0' + settings.decimals + '00');
                    }
                    data.element.on('change', function (event) {
                        data.error = isNaN($(data.element).val());
                        if (!data.error) {
                            $(data.element).parent().find('.neco-form-error').text(window.I18n.Form.Warnings.float);
                            helpers.showError();
                        } else {
                            helpers.showSuccess();
                        }
                    });
                }

                if (data.type === 'phone') {
                    helpers.actionOnEvent(
                        data.element,
                        'keypress blur',
                        helpers.validateAndFeedTextInput(window.I18n.Form.Warnings.phone, patterns.phone, data));

                    helpers.actionOnEvent(
                        data.element,
                        'focusout',
                        helpers.validateTextInputEmptiness(data));
                }

                if (data.type === 'password') {
                    helpers.actionOnEvent(
                        data.element,
                        'keypress blur',
                        helpers.validateAndFeedTextInput(window.I18n.Form.Warnings.password, patterns.password, data));


                    if ($(data.element).data('confirm') === 1) {
                        var confirmPwd = $(document.createElement('div')).addClass('property').html('<label for="confirm">Confirmar Contrase\u00F1a:</label><input type="password" name="confirm" id="confirm" value="" autocomplete="off" required="true" title="Vuelva a escribir la contrase&ntilde;a" /><span class="neco-tooltip">Por favor repita la contrase\u00F1a</span></a><a class="neco-form-tip"><span class="neco-tooltip">Debe repetir la contrase\u00F1a para confirmar que la haya escrito bien</span></a><a class="neco-form-error" title="No hay errores en el campo"><span class="neco-tooltip"></span></a>');

                        $(data.element).parent('div').after(confirmPwd);

                        $(confirmPwd).on('change', function () {
                            var confirmInput = $(confirmPwd).find('input');
                            if ($(data.element).val() !== confirmInput.val()) {
                                $(confirmPwd).find('.neco-form-error').text(window.I18n.Form.Warnings.confirm);
                                helpers.showErrorFeedback(confirmInput);
                            } else {
                                helpers.showSuccessFeedback(confirmInput);
                            }
                        });
                        $(confirmPwd).find('.neco-form-error').on('mouseover', function () {
                            if ($(this).attr('title').length === 0) {
                                return false;
                            }
                            $(this).find('span').text($(this).attr('title'));
                            $(this).attr('title', '');
                        });
                    }
                }

                if (settings.showQuick) {
                    helpers.quickError();
                }

                if (typeof settings.create === 'function') {
                    settings.create();
                }
            },
            quickError: function () {
                var $element = data.element;
                var quick = $('<span>')
                    .addClass('neco-form-error')
                    .attr('data-label', $element.attr("data-label"));
                data.element.after(quick);
            },
            actionOnEvent: function (input, eventType, callback) {
                var timeoutID;
                input.on(eventType, function (e) {
                    if (timeoutID) {
                        window.clearTimeout(timeoutID);
                    }
                    timeoutID = window.setTimeout(function () {
                        callback();
                    }, 300);
                });
            },
            maskInput: function (mask, placeholder, element) {
                if ($.fn.mask !== undefined) {
                    $(element).mask(mask, {placeholder: placeholder});
                }
            },
            validateAndFeedInput: function (message, data) {
                data.error = helpers.checkPattern();
                var $element = $(data.element);
                var errorFeedback = $element.parent().find('.neco-form-error');
                if (!data.error) {
                    errorFeedback
                        .addClass("active-feedback")
                        .text(message);
                    helpers.showError();
                } else {
                    errorFeedback.removeClass("active-feedback");
                    helpers.showSuccess();
                }
            },
            validateAndFeedTextInput: function (message, pattern, data) {
                var $element;
                $element = $element || data.element;

                return function () {
                    var errorFeedback = $element.parent().find('.neco-form-error');
                    data.error = helpers.matchInput(pattern, data.element);
                    if (!data.error && $element.val() !== '') {
                        errorFeedback
                            .addClass("active-feedback")
                            .text(message);
                        helpers.showError();
                    } else if ($element.val() === '') {
                        errorFeedback.removeClass("active-feedback");
                        helpers.validateTextInputEmptiness(data)();
                    }
                    else {
                        errorFeedback.removeClass("active-feedback");
                        helpers.showSuccess();
                    }
                };
            },
            validateTextInputEmptiness: function (data) {
                var $element = data.element;
                var isRequired = isRequired || $element.attr('required');
                return function () {
                    if ($element.val() === '' && isRequired) {
                        $element.attr('placeholder', window.I18n.Form.Warnings.empty);
                        helpers.showError();
                    }
                };
            },
            isDateInputSupported: function(){
                var elem = document.createElement('input');
                elem.setAttribute('type','date');
                elem.value = 'foo';
                return (elem.type === 'date' && elem.value != 'foo');
            },
            showError: function () {
                if ($(data.element).hasClass('neco-input-success')) {
                    $(data.element).removeClass('neco-input-success');
                }
                $(data.element).addClass('neco-input-error');
            },
            showSuccess: function () {
                if ($(data.element).hasClass('neco-input-error')) {
                    $(data.element).removeClass('neco-input-error');
                }
                $(data.element).addClass('neco-input-success');
            },
            showErrorFeedback: function (element) {
                if ($(element).hasClass('neco-input-success')) {
                    $(element).removeClass('neco-input-success');
                }
                $(element).addClass('neco-input-error');
            },
            showSuccessFeedback: function (element) {
                if ($(element).hasClass('neco-input-error')) {
                    $(element).removeClass('neco-input-error');
                }
                $(element).addClass('neco-input-success');
            },
            matchInput: function (pattern, element) {
                pattern = new RegExp(pattern);
                return pattern.test(element.val());
            },
            checkPattern: function () {
                var pattern = new RegExp(settings.pattern);
                return pattern.test(data.element.val());
            },
            _focus: function () {
                /*$(data.element).on('focus',function(event){
                 if (typeof settings.focus === "function") {
                 settings.focus(this);
                 }
                 });*/
            },
            _blur: function () {
                /*
                 $(data.element).on('blur',function(event){
                 if (typeof settings.blur === "function") {
                 settings.blur(this);
                 }
                 });*/
            },
            _keydown: function () {
                /*
                 $(data.element).on('change',function(event){
                 if (typeof settings.keydown === "function") {
                 settings.keydown(this);
                 }
                 });*/
            },
            _change: function () {
                /*
                 $(data.element).on('change',function(event){
                 if (settings.required) {
                 helpers.checkNoEmpty();
                 }
                 if (typeof settings.change === "function") {
                 settings.change(this);
                 }
                 });*/
            },
            checkNoEmpty: function () {
                if (!data.element.val()) {
                    $(data.element).parent().find('.neco-form-error').attr({'title': "No puedes dejar este campo en blanco"});
                    helpers.showError();
                }
            }
        };

        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || !method) {
            return methods.init.apply(this, arguments);
        } else {
            console.log(methods);
            $.error('Method "' + method + '" does not exist in ntInput plugin!');
        }
    };
})(jQuery);

/**
 * NecoTextArea
 * Author: Yosiet Serga
 * Version: 1.0.1
 *
 * Dual licensed under the MIT and GPL licenses
 *
 */
(function ($) {
    'use strict';
    $.fn.ntTextArea = function (method) {
        var defaults = {
            error: false,
            message: false,
            pattern: '',
            format: '',
            showQuick: true,
            focus: function () {
            },
            blur: function () {
            },
            keydown: function () {
            },
            change: function () {
            },
            options: {},
            loading: {
                title: 'Comprobando...',
                image: '../loader.gif',
                classname: 'neco-input-loading'
            }
        };

        var settings = {};
        var data = {};
        var methods = {
            init: function (options) {
                return this.each(function () {
                    settings = $.extend({}, defaults, options);
                    data.element = $(this);
                    helpers._create();
                    helpers._focus();
                    helpers._blur();
                    helpers._keydown();
                    helpers._change();
                });
            }
        };

        var helpers = {
            _create: function () {
                $('*', data.element).change(helpers._change);
                $('*', data.element).keydown(helpers._keydown);

                if (typeof settings.create === 'function') {
                    settings.create();
                }
                helpers.isRequired();
            },
            isRequired: function () {
                var required = $(data.element).attr('required');
                if (required) {
                    var el = $('<span class="neco-input-required">*</span>');
                    data.element.closest('.form-entry').find('label').append(el);
                }
                settings.required = required;
            },
            quickHelp: function () {
                if (!settings.help && $(data.element).attr('title')) {
                    settings.help = $(data.element).attr('title');
                } else if (!settings.help) {
                    settings.help = "No se pudo cargar el mensaje";
                }
                var quick = $(document.createElement('a')).attr({'title': settings.help}).text('').addClass('neco-form-help');
                $(data.element).after(quick);
                var msg = $(document.createElement('span')).addClass('neco-tooltip');
                $(quick).append(msg);
                $(quick).on('mouseover', function (e) {
                    var message = $(this).attr('title');
                    $(this).removeAttr('title');
                    $(msg).text(message);
                });
            },
            quickTip: function () {
                if (!settings.tip && $(data.element).attr('quicktip')) {
                    settings.tip = $(data.element).attr('quicktip');
                } else if (!settings.tip) {
                    settings.tip = "No se pudo cargar el mensaje";
                }
                var quick = $(document.createElement('a')).attr({'title': settings.tip}).text('').addClass('neco-form-tip');
                $(data.element).after(quick);
                var msg = $(document.createElement('span')).addClass('neco-tooltip');
                $(quick).append(msg);
                $(quick).on('mouseover', function (e) {
                    var message = $(this).attr('title');
                    $(this).removeAttr('title');
                    $(msg).text(message);
                });
            },
            quickError: function () {
                if (!settings.error) {
                    settings.error = "No hay errores en el campo";
                }
                var quick = $(document.createElement('a')).attr({'title': settings.error}).text('').addClass('neco-form-error');
                $(data.element).after(quick);
                var msg = $(document.createElement('span')).addClass('neco-tooltip');
                $(quick).append(msg);
                $(quick).on('mouseover', function (e) {
                    var message = $(this).attr('title');
                    $(this).removeAttr('title');
                    $(msg).text(message);
                });
            },
            showError: function () {
                if ($(data.element).hasClass('neco-input-success')) {
                    $(data.element).removeClass('neco-input-success');
                }
                $(data.element).addClass('neco-input-error');
            },
            showSuccess: function () {
                if ($(data.element).hasClass('neco-input-error')) {
                    $(data.element).removeClass('neco-input-error');
                }
                $(data.element).addClass('neco-input-success');
            },
            checkPattern: function () {
                var pattern = new RegExp(settings.pattern);
                return pattern.test(data.element.val());
            },
            _focus: function () {
                $(data.element).on('focus', function (event) {
                    if (typeof settings.focus === "function") {
                        settings.focus(this);
                    }
                });
            },
            _blur: function () {
                $(data.element).on('blur', function (event) {
                    if (typeof settings.blur === "function") {
                        settings.blur(this);
                    }
                });
            },
            _keydown: function () {
                $(data.element).on('change', function (event) {
                    if (typeof settings.keydown === "function") {
                        settings.keydown(this);
                    }
                });
            },
            _change: function () {
                $(data.element).on('change', function (event) {
                    if (settings.required) {
                        helpers.checkNoEmpty();
                    }
                    if (typeof settings.change === "function") {
                        settings.change(this);
                    }
                });
            },
            checkNoEmpty: function () {
                if (!data.element.val()) {
                    $(data.element).parent().find('.neco-form-error').attr({'title': "No puedes dejar este campo en blanco"});
                    helpers.showError();
                }
            }
        };

        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || !method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error('Method "' + method + '" does not exist in ntTextArea plugin!');
        }
    };
})(jQuery);

/**
 * NecoSelect
 * Author: Yosiet Serga
 * Version: 1.0.1
 *
 * Dual licensed under the MIT and GPL licenses
 *
 */

(function ($) {
    'use strict';
    $.fn.ntSelect = function (method) {
        /*
         var defaults = {
         error:      false,
         message:    false,
         showQuick:   true,
         focus:      function(){},
         blur:       function(){},
         change:     function(){},
         options:    {},
         loading:    {
         title:'Comprobando...',
         image:'../loader.gif',
         classname:'neco-select-loading'
         }
         };

         var settings = {};
         var data = {};
         var methods = {
         init : function(options) {
         return this.each(function() {
         settings = $.extend({}, defaults, options);
         data.element = $(this);
         helpers._create();
         helpers._focus();
         helpers._blur();
         helpers._change();
         });
         }
         };

         var helpers = {
         _create: function() {
         $('*', data.element).change(helpers._change);

         if (typeof $.chosen !== 'undefined') {
         if (typeof settings.chosen === 'undefined') {
         settings.chosen = {};
         }

         }
         $(data.element).chosen(settings.chosen);
         if ($(data.element).attr('showquick') === 'off') {
         settings.showQuick=false; 
         }
         if (settings.showQuick) {
         helpers.quickError();
         helpers.quickTip();
         helpers.quickHelp(); 
         }

         if (typeof settings.create === 'function') {
         settings.create();
         }
         },
         isRequired:function() {
         var required = $(data.element).attr('required');
         var el;
         if (required) {
         el = $('<span class="neco-input-required">*</span>');
         $(data.element).after(el);
         }
         settings.required = required;
         },
         quickHelp:function(){
         if (!settings.help && $(data.element).attr('title')) {
         settings.help = $(data.element).attr('title');
         } else if (!settings.help) {
         settings.help = "No se pudo cargar el mensaje";
         }
         var quick = $(document.createElement('a')).attr({'title':settings.help}).text('').addClass('neco-form-help');
         $(data.element).after(quick);
         var msg = $(document.createElement('span')).addClass('neco-tooltip');
         $(quick).append(msg);
         $(quick).on('mouseover',function(e){
         message = $(this).attr('title');
         $(this).removeAttr('title');
         $(msg).text(message);
         });
         },
         quickTip:function(){
         if (!settings.tip && $(data.element).attr('quicktip')) {
         settings.tip = $(data.element).attr('quicktip');
         } else if (!settings.tip) {
         settings.tip = "No se pudo cargar el mensaje";
         }
         var quick = $(document.createElement('a')).attr({'title':settings.tip}).text('').addClass('neco-form-tip');
         $(data.element).after(quick);
         var msg = $(document.createElement('span')).addClass('neco-tooltip');
         $(quick).append(msg);
         $(quick).on('mouseover',function(e){
         message = $(this).attr('title');
         $(this).removeAttr('title');
         $(msg).text(message);
         });
         },
         quickError:function(){
         if (!settings.error) {
         settings.error = "No hay errores en el campo";
         }
         var quick = $(document.createElement('a')).attr({'title':settings.error}).text('').addClass('neco-form-error');
         $(data.element).after(quick);
         var msg = $(document.createElement('span')).addClass('neco-tooltip');
         $(quick).append(msg);
         $(quick).on('mouseover',function(e){
         message = $(this).attr('title');
         $(this).removeAttr('title');
         $(msg).text(message);
         });
         },
         setQuickHelp:function(el) {

         },
         showError:function(){
         if ($(data.element).hasClass('neco-input-success')) {
         $(data.element).removeClass('neco-input-success');
         }
         $(data.element).addClass('neco-input-error');
         },
         showSuccess:function(){
         if ($(data.element).hasClass('neco-input-error')) {
         $(data.element).removeClass('neco-input-error');
         }
         $(data.element).addClass('neco-input-success');
         },
         _focus: function() {
         $(data.element).on('focus',function(event){
         if (typeof settings.focus === "function") {
         settings.focus(this);
         }
         });
         },
         _blur: function() {
         $(data.element).on('blur',function(event){
         if (typeof settings.blur === "function") {
         settings.blur(this);
         }
         });
         },
         _change: function() {
         $(data.element).on('change',function(event){
         if (settings.required) {
         helpers.checkNoEmpty();
         }
         if (typeof settings.change === "function") {
         settings.change(this);
         }
         });
         },
         checkNoEmpty: function() {
         if (!data.element.val()) {
         $(data.element).parent().find('.neco-form-error').attr({'title':"No puedes dejar este campo en blanco"});
         helpers.showError();
         }
         }
         };

         if (methods[method]) {
         return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
         } else if (typeof method === 'object' || !method) {
         return methods.init.apply(this, arguments);
         } else {
         $.error( 'Method "' +  method + '" does not exist in ntSelect plugin!');
         }
         */
    };
})(jQuery);

/**
 * autoNumeric.js
 * @author: Bob Knothe
 * @author: Sokolov Yura aka funny_falcon
 * @version: 1.7.5
 *
 * Created by Robert J. Knothe on 2010-10-25. Please report any bug at https://www.decorplanit.com/plugin/
 * Created by Sokolov Yura on 2010-11-07. https://github.com/funny_falcon
 *
 * Copyright (c) 2011 Robert J. Knothe  https://www.decorplanit.com/plugin/
 * Copyright (c) 2011 Sokolov Yura aka funny_falcon
 *
 * The MIT License (https://www.opensource.org/licenses/mit-license.php)
 *
 * Permission is hereby granted, free of charge, to any person
 * obtaining a copy of this software and associated documentation
 * files (the "Software"), to deal in the Software without
 * restriction, including without limitation the rights to use,
 * copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the
 * Software is furnished to do so, subject to the following
 * conditions:
 *
 * The above copyright notice and this permission notice shall be
 * included in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
 * OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
 * NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
 * HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
 * WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR
 * OTHER DEALINGS IN THE SOFTWARE.
 */
(function ($) {
    /**
     * Cross browser routin for getting selected range/cursor position
     */
    'use strict';
    function getElementSelection(that) {
        var position = {};
        var select;
        if (that.selectionStart === undefined) {
            that.focus();
            select = document.selection.createRange();
            position.length = select.text.length;
            select.moveStart('character', -that.value.length);
            position.end = select.text.length;
            position.start = position.end - position.length;
        } else {
            position.start = that.selectionStart;
            position.end = that.selectionEnd;
            position.length = position.end - position.start;
        }
        return position;
    }

    /**
     * Cross browser routin for setting selected range/cursor position
     */
    function setElementSelection(that, start, end) {
        if (that.selectionStart === undefined) {
            that.focus();
            var r = that.createTextRange();
            r.collapse(true);
            r.moveEnd('character', end);
            r.moveStart('character', start);
            r.select();
        } else {
            that.selectionStart = start;
            that.selectionEnd = end;
        }
    }

    /**
     * run callbacks in parameters if any
     * any parameter could be a callback:
     * - a function, which invoked with jQuery element, parameters and this parameter name and returns parameter value
     * - a name of function, attached to $.autoNumeric, which called as previous
     * - a css selector recognized by jQuery - value of input is taken as a parameter value
     */
    function runCallbacks($this, io) {
        /**
         * loops through the io object (option array) to find the following
         * k = option name example k=aNum
         * val = option value example val=0123456789
         */
        $.each(io, function (k, val) {
            if (typeof (val) === 'function') {
                io[k] = val($this, io, k);
            } else if (typeof (val) === 'string') {
                var kind = val.substr(0, 4);
                if (kind === 'fun:') {
                    var fun = $.autoNumeric[val.substr(4)];
                    if (typeof (fun) === 'function') {
                        /**
                         * calls the attached function from meta="{aSign:'fun:functionName'}"
                         * example: $autoNumeric.functionName($this, io, optionName);
                         */
                        io[k] = $.autoNumeric[val.substr(4)]($this, io, k);
                    } else {
                        io[k] = null;
                    }
                } else if (kind === 'css:') {
                    /**
                     * retrieves the value by css selector meta="{mDec:'css:#decimal'}"
                     * example: would assign the value to io[k] = $('#decimal').val();
                     */
                    io[k] = $(val.substr(4)).val();
                }
            }
        });
    }

    function convertKeyToNumber(io, key) {
        if (typeof (io[key]) === 'string') {
            io[key] *= 1;
        }
    }

    /**
     * Preparing user defined options for further usage
     * merge them with defaults appropriatly
     */
    function autoCode($this, options) {
        var io = $.extend({}, $.fn.autoNumeric.defaults, options);
        if ($.metadata) {
            /** consider declared metadata on input */
            io = $.extend(io, $this.metadata());
        }
        runCallbacks($this, io);
        var vmax = io.vMax.toString().split('.');
        var vmin = (!io.vMin && io.vMin !== 0) ? [] : io.vMin.toString().split('.');
        convertKeyToNumber(io, 'vMax');
        convertKeyToNumber(io, 'vMin');
        convertKeyToNumber(io, 'mDec');
        io.aNeg = io.vMin < 0 ? '-' : '';
        /** set mDec, if not defained by user */
        if (typeof (io.mDec) !== 'number') {
            io.mDec = Math.max((vmax[1] ? vmax[1] : '').length, (vmin[1] ? vmin[1] : '').length);
        }
        /** set alternative decimal separator key */
        if (io.altDec === null && io.mDec > 0) {
            if (io.aDec === '.' && io.aSep !== ',') {
                io.altDec = ',';
            } else if (io.aDec === ',' && io.aSep !== '.') {
                io.altDec = '.';
            }
        }
        /** cache regexps for autoStrip */
        var aNegReg = io.aNeg ? '([-\\' + io.aNeg + ']?)' : '(-?)';
        io._aNegReg = aNegReg;
        io._skipFirst = new RegExp(aNegReg + '[^-' + (io.aNeg ? '\\' + io.aNeg : '') + '\\' + io.aDec + '\\d]' + '.*?(\\d|\\' + io.aDec + '\\d)');
        io._skipLast = new RegExp('(\\d\\' + io.aDec + '?)[^\\' + io.aDec + '\\d]\\D*$');
        var allowed = (io.aNeg ? io.aNeg : '-') + io.aNum + '\\' + io.aDec;
        if (io.altDec && io.altDec !== io.aSep) {
            allowed += io.altDec;
        }
        io._allowed = new RegExp('[^' + allowed + ']', 'gi');
        io._numReg = new RegExp(aNegReg + '(?:\\' + io.aDec + '?(\\d+\\' + io.aDec + '\\d+)|(\\d*(?:\\' + io.aDec + '\\d*)?))');
        return io;
    }

    /**
     * strip all unwanted characters and leave only a number
     */
    function autoStrip(s, io, strip_zero) {
        if (io.aSign) { /** remove currency sign */
            while (s.indexOf(io.aSign) > -1) {
                s = s.replace(io.aSign, '');
            }
        }
        s = s.replace(io._skipFirst, '$1$2');
        /** first replace anything before digits */
        s = s.replace(io._skipLast, '$1');
        /** then replace anything after digits */
        s = s.replace(io._allowed, '');
        /** then remove any uninterested characters */
        if (io.altDec) {
            s = s.replace(io.altDec, io.aDec);
        }
        /** get only number string */
        var m = s.match(io._numReg);
        s = m ? [m[1], m[2], m[3]].join('') : '';
        /** strip zero if need */
        if (strip_zero) {
            var strip_reg = '^' + io._aNegReg + '0*(\\d' + (strip_zero === 'leading' ? ')' : '|$)');
            strip_reg = new RegExp(strip_reg);
            s = s.replace(strip_reg, '$1$2');
        }
        return s;
    }

    /**
     * truncate decimal part of a number
     */
    function truncateDecimal(s, aDec, mDec) {
        if (aDec && mDec) {
            var parts = s.split(aDec);
            /** truncate decimal part to satisfying length
             * cause we would round it anyway */
            if (parts[1] && parts[1].length > mDec) {
                if (mDec > 0) {
                    parts[1] = parts[1].substring(0, mDec);
                    s = parts.join(aDec);
                } else {
                    s = parts[0];
                }
            }
        }
        return s;
    }

    /**
     * prepare number string to be converted to real number
     */
    function fixNumber(s, aDec, aNeg) {
        if (aDec && aDec !== '.') {
            s = s.replace(aDec, '.');
        }
        if (aNeg && aNeg !== '-') {
            s = s.replace(aNeg, '-');
        }
        if (!s.match(/\d/)) {
            s += '0';
        }
        return s;
    }

    /**
     * prepare real number to be converted to our format
     */
    function presentNumber(s, aDec, aNeg) {
        if (aNeg && aNeg !== '-') {
            s = s.replace('-', aNeg);
        }
        if (aDec && aDec !== '.') {
            s = s.replace('.', aDec);
        }
        return s;
    }

    /**
     * checking that number satisfy format conditions
     * and lays between io.vMin and io.vMax
     */
    function autoCheck(s, io) {
        s = autoStrip(s, io);
        s = truncateDecimal(s, io.aDec, io.mDec);
        s = fixNumber(s, io.aDec, io.aNeg);
        var value = +s;
        return value >= io.vMin && value <= io.vMax;
    }

    /**
     * private function to check for empty value
     */
    function checkEmpty(iv, io, signOnEmpty) {
        if (iv === '' || iv === io.aNeg) {
            if (io.wEmpty === 'zero') {
                return iv + '0';
            } else if (io.wEmpty === 'sign' || signOnEmpty) {
                return iv + io.aSign;
            } else {
                return iv;
            }
        }
        return null;
    }

    /**
     * private function that formats our number
     */
    function autoGroup(iv, io) {
        iv = autoStrip(iv, io);
        var empty = checkEmpty(iv, io, true);
        if (empty !== null) {
            return empty;
        }
        var digitalGroup = '';
        if (io.dGroup === 2) {
            digitalGroup = /(\d)((\d)(\d{2}?)+)$/;
        } else if (io.dGroup === 4) {
            digitalGroup = /(\d)((\d{4}?)+)$/;
        } else {
            digitalGroup = /(\d)((\d{3}?)+)$/;
        }
        /** splits the string at the decimal string */
        var ivSplit = iv.split(io.aDec);
        if (io.altDec && ivSplit.length === 1) {
            ivSplit = iv.split(io.altDec);
        }
        /** assigns the whole number to the a varibale (s) */
        var s = ivSplit[0];
        if (io.aSep) {
            while (digitalGroup.test(s)) {
                /**  re-inserts the thousand sepparator via a regualer expression */
                s = s.replace(digitalGroup, '$1' + io.aSep + '$2');
            }
        }
        if (io.mDec !== 0 && ivSplit.length > 1) {
            if (ivSplit[1].length > io.mDec) {
                ivSplit[1] = ivSplit[1].substring(0, io.mDec);
            }
            /** joins the whole number with the deciaml value */
            iv = s + io.aDec + ivSplit[1];
        } else {
            /** if whole numers only */
            iv = s;
        }
        if (io.aSign) {
            var has_aNeg = iv.indexOf(io.aNeg) !== -1;
            iv = iv.replace(io.aNeg, '');
            iv = io.pSign === 'p' ? io.aSign + iv : iv + io.aSign;
            if (has_aNeg) {
                iv = io.aNeg + iv;
            }
        }
        return iv;
    }

    /**
     * round number after setting by pasting or $().autoNumericSet()
     * private function for round the number
     * please note this handled as text - Javascript math function can return inaccurate values
     * also this offers multiple rounding metods that are not easily accomplished in javascript
     */
    function autoRound(iv, mDec, mRound, aPad) {
        /** value to string */
        iv = (iv === '') ? '0' : iv.toString();
        var ivRounded = '';
        var i = 0;
        var nSign = '';
        var rDec = (typeof (aPad) === 'boolean' || aPad === null) ? (aPad ? mDec : 0) : +aPad;
        var truncateZeros = function (ivRounded) {
            /** truncate not needed zeros */
            var regex = rDec === 0 ? (/(\.[1-9]*)0*$/) : rDec === 1 ? (/(\.\d[1-9]*)0*$/) : new RegExp('(\\.\\d{' + rDec + '}[1-9]*)0*$');
            ivRounded = ivRounded.replace(regex, '$1');
            /** If there are no decimal places, we don't need a decimal point at the end */
            if (rDec === 0) {
                ivRounded = ivRounded.replace(/\.$/, '');
            }
            return ivRounded;
        };
        if (iv.charAt(0) === '-') {
            /** Checks if the iv (input Value)is a negative value */
            nSign = '-';
            /** removes the negative sign will be added back later if required */
            iv = iv.replace('-', '');
        }
        /** prepend a zero if first character is not a digit (then it is likely to be a dot)*/
        if (!iv.match(/^\d/)) {
            iv = '0' + iv;
        }
        /** determines if the value is zero - if zero no negative sign */
        if (nSign === '-' && +iv === 0) {
            nSign = '';
        }
        /** trims leading zero's if needed */
        if ((+iv) > 0) {
            iv = iv.replace(/^0*(\d)/, '$1');
        }
        /** decimal postion as an integer */
        var dPos = iv.lastIndexOf('.');
        /** virtual decimal position */
        var vdPos = dPos === -1 ? iv.length - 1 : dPos;
        /** checks decimal places to determine if rounding is required */
        var cDec = (iv.length - 1) - vdPos;
        /** check if no rounding is required */
        if (cDec <= mDec) {
            ivRounded = iv;
            /** check if we need to pad with zeros */
            if (cDec < rDec) {
                if (dPos === -1) {
                    ivRounded += '.';
                }
                while (cDec < rDec) {
                    var zeros = '000000'.substring(0, rDec - cDec);
                    ivRounded += zeros;
                    cDec += zeros.length;
                }
            } else if (cDec > rDec) {
                ivRounded = truncateZeros(ivRounded);
            } else if (cDec === 0 && rDec === 0) {
                ivRounded = ivRounded.replace(/\.$/, '');
            }
            return nSign + ivRounded;
        }
        /** rounded length of the string after rounding  */
        var rLength = dPos + mDec;
        /** test round */
        var tRound = +iv.charAt(rLength + 1);
        var ivArray = iv.substring(0, rLength + 1).split('');
        var odd = (iv.charAt(rLength) === '.') ? (iv.charAt(rLength - 1) % 2) : (iv.charAt(rLength) % 2);
        if ((tRound > 4 && mRound === 'S') || (tRound > 4 && mRound === 'A' && nSign === '') || (tRound > 5 && mRound === 'A' && nSign === '-') || (tRound > 5 && mRound === 's') || (tRound > 5 && mRound === 'a' && nSign === '') || (tRound > 4 && mRound === 'a' && nSign === '-') || (tRound > 5 && mRound === 'B') || (tRound === 5 && mRound === 'B' && odd === 1) || (tRound > 0 && mRound === 'C' && nSign === '') || (tRound > 0 && mRound === 'F' && nSign === '-') || (tRound > 0 && mRound === 'U')) {
            /** Round up the last digit if required, and continue until no more 9's are found */
            for (i = (ivArray.length - 1); i >= 0; i -= 1) {
                if (ivArray[i] !== '.') {
                    ivArray[i] = +ivArray[i] + 1;
                    if (ivArray[i] < 10) {
                        break;
                    } else if (i > 0) {
                        ivArray[i] = '0';
                    }
                }
            }
        }
        /** Reconstruct the string, converting any 10's to 0's */
        ivArray = ivArray.slice(0, rLength + 1);
        ivRounded = truncateZeros(ivArray.join(''));
        /** return rounded value */
        return nSign + ivRounded;
    }

    /**
     * Holder object for field properties
     */
    function autoNumericHolder(that, options) {
        this.options = options;
        this.that = that;
        this.$that = $(that);
        this.formatted = false;
        this.io = autoCode(this.$that, this.options);
        this.value = that.value;
    }

    autoNumericHolder.prototype = {
        init: function (e) {
            this.value = this.that.value;
            this.io = autoCode(this.$that, this.options);
            this.ctrlKey = e.ctrlKey;
            this.cmdKey = e.metaKey;
            this.shiftKey = e.shiftKey;
            this.selection = getElementSelection(this.that);
            /** keypress event overwrites meaningfull value of e.keyCode */
            if (e.type === 'keydown' || e.type === 'keyup') {
                this.kdCode = e.keyCode;
            }
            this.which = e.which;
            this.processed = false;
            this.formatted = false;
        },
        setSelection: function (start, end, setReal) {
            start = Math.max(start, 0);
            end = Math.min(end, this.that.value.length);
            this.selection = {
                start: start,
                end: end,
                length: end - start
            };
            if (setReal === undefined || setReal) {
                setElementSelection(this.that, start, end);
            }
        },
        setPosition: function (pos, setReal) {
            this.setSelection(pos, pos, setReal);
        },
        getBeforeAfter: function () {
            var value = this.value;
            var left = value.substring(0, this.selection.start);
            var right = value.substring(this.selection.end, value.length);
            return [left, right];
        },
        getBeforeAfterStriped: function () {
            var parts = this.getBeforeAfter();
            parts[0] = autoStrip(parts[0], this.io);
            parts[1] = autoStrip(parts[1], this.io);
            return parts;
        },
        /**
         * strip parts from excess characters and leading zeroes
         */
        normalizeParts: function (left, right) {
            var io = this.io;
            right = autoStrip(right, io);
            /** if right is not empty and first character is not aDec, */
            /** we could strip all zeros, otherwise only leading */
            var strip = right.match(/^\d/) ? true : 'leading';
            left = autoStrip(left, io, strip);
            /** strip leading zeros from right part if left part has no digits */
            if ((left === '' || left === io.aNeg)) {
                if (right > '') {
                    right = right.replace(/^0*(\d)/, '$1');
                }
            }
            var new_value = left + right;
            /** insert zero if has leading dot */
            if (io.aDec) {
                var m = new_value.match(new RegExp('^' + io._aNegReg + '\\' + io.aDec));
                if (m) {
                    left = left.replace(m[1], m[1] + '0');
                    new_value = left + right;
                }
            }
            /** insert zero if number is empty and io.wEmpty == 'zero' */
            if (io.wEmpty === 'zero' && (new_value === io.aNeg || new_value === '')) {
                left += '0';
            }
            return [left, right];
        },
        /**
         * set part of number to value keeping position of cursor
         */
        setValueParts: function (left, right) {
            var io = this.io;
            var parts = this.normalizeParts(left, right);
            var new_value = parts.join('');
            var position = parts[0].length;
            if (autoCheck(new_value, io)) {
                new_value = truncateDecimal(new_value, io.aDec, io.mDec);
                if (position > new_value.length) {
                    position = new_value.length;
                }
                this.value = new_value;
                this.setPosition(position, false);
                return true;
            }
            return false;
        },
        /**
         * helper function for expandSelectionOnSign
         * returns sign position of a formatted value
         */
        signPosition: function () {
            var io = this.io, aSign = io.aSign, that = this.that;
            if (aSign) {
                var aSignLen = aSign.length;
                if (io.pSign === 'p') {
                    var hasNeg = io.aNeg && that.value && that.value.charAt(0) === io.aNeg;
                    return hasNeg ? [1, aSignLen + 1] : [0, aSignLen];
                } else {
                    var valueLen = that.value.length;
                    return [valueLen - aSignLen, valueLen];
                }
            } else {
                return [1000, -1];
            }
        },
        /**
         * expands selection to cover whole sign
         * prevents partial deletion/copying/overwritting of a sign
         */
        expandSelectionOnSign: function (setReal) {
            var sign_position = this.signPosition();
            var selection = this.selection;
            if (selection.start < sign_position[1] && selection.end > sign_position[0]) { /** if selection catches something except sign and catches only space from sign */
                if ((selection.start < sign_position[0] || selection.end > sign_position[1]) && this.value.substring(Math.max(selection.start, sign_position[0]), Math.min(selection.end, sign_position[1])).match(/^\s*$/)) { /** then select without empty space */
                    if (selection.start < sign_position[0]) {
                        this.setSelection(selection.start, sign_position[0], setReal);
                    } else {
                        this.setSelection(sign_position[1], selection.end, setReal);
                    }
                } else {
                    /** else select with whole sign */
                    this.setSelection(Math.min(selection.start, sign_position[0]), Math.max(selection.end, sign_position[1]), setReal);
                }
            }
        },
        /**
         * try to strip pasted value to digits
         */
        checkPaste: function () {
            if (this.valuePartsBeforePaste !== undefined) {
                var parts = this.getBeforeAfter();
                var oldParts = this.valuePartsBeforePaste;
                delete this.valuePartsBeforePaste;
                /* try to strip pasted value first */
                parts[0] = parts[0].substr(0, oldParts[0].length) + autoStrip(parts[0].substr(oldParts[0].length), this.io);
                if (!this.setValueParts(parts[0], parts[1])) {
                    this.value = oldParts.join('');
                    this.setPosition(oldParts[0].length, false);
                }
            }
        },
        /**
         * process pasting, cursor moving and skipping of not interesting keys
         * if returns true, futher processing is not performed
         */
        skipAllways: function (e) {
            var kdCode = this.kdCode, which = this.which, ctrlKey = this.ctrlKey, cmdKey = this.cmdKey;
            /** catch the ctrl up on ctrl-v */
            if (kdCode === 17 && e.type === 'keyup' && this.valuePartsBeforePaste !== undefined) {
                this.checkPaste();
                return false;
            }
            /** codes are taken from https://www.cambiaresearch.com/c4/702b8cd1-e5b0-42e6-83ac-25f0306e3e25/Javascript-Char-Codes-Key-Codes.aspx
             * skip Fx keys, windows keys, other special keys */
            if ((kdCode >= 112 && kdCode <= 123) || (kdCode >= 91 && kdCode <= 93) || (kdCode >= 9 && kdCode <= 31) || (kdCode < 8 && (which === 0 || which === kdCode)) || kdCode === 144 || kdCode === 145 || kdCode === 45) {
                return true;
            }
            /** if select all (a=65)*/
            if ((ctrlKey || cmdKey) && kdCode === 65) {
                return true;
            }
            /** if copy (c=67) paste (v=86) or cut (x=88) */
            if ((ctrlKey || cmdKey) && (kdCode === 67 || kdCode === 86 || kdCode === 88)) { /** replace or cut whole sign */
                if (e.type === 'keydown') {
                    this.expandSelectionOnSign();
                }
                /** try to prevent wrong paste */
                if (kdCode === 86) {
                    if (e.type === 'keydown' || e.type === 'keypress') {
                        if (this.valuePartsBeforePaste === undefined) {
                            this.valuePartsBeforePaste = this.getBeforeAfter();
                        }
                    } else {
                        this.checkPaste();
                    }
                }
                return e.type === 'keydown' || e.type === 'keypress' || kdCode === 67;
            }
            if (ctrlKey || cmdKey) {
                return true;
            }
            if (kdCode === 37 || kdCode === 39) {
                /** jump over thousand separator */
                var aSep = this.io.aSep, start = this.selection.start, value = this.that.value;
                if (e.type === 'keydown' && aSep && !this.shiftKey) {
                    if (kdCode === 37 && value.charAt(start - 2) === aSep) {
                        this.setPosition(start - 1);
                    } else if (kdCode === 39 && value.charAt(start) === aSep) {
                        this.setPosition(start + 1);
                    }
                }
                return true;
            }
            if (kdCode >= 34 && kdCode <= 40) {
                return true;
            }
            return false;
        },
        /**
         * process deletion of characters alert
         * returns true if processing performed
         */
        processAllways: function () {
            var parts;
            /** process backspace or delete */
            if (this.kdCode === 8 || this.kdCode === 46) {
                if (!this.selection.length) {
                    parts = this.getBeforeAfterStriped();
                    if (this.kdCode === 8) {
                        parts[0] = parts[0].substring(0, parts[0].length - 1);
                    } else {
                        parts[1] = parts[1].substring(1, parts[1].length);
                    }
                    this.setValueParts(parts[0], parts[1]);
                } else {
                    this.expandSelectionOnSign(false);
                    parts = this.getBeforeAfterStriped();
                    this.setValueParts(parts[0], parts[1]);
                }
                return true;
            }
            return false;
        },
        /**
         * process insertion of characters
         * returns true if processing performed
         */
        processKeypress: function () {
            var io = this.io;
            var cCode = String.fromCharCode(this.which);
            var parts = this.getBeforeAfterStriped();
            var left = parts[0], right = parts[1];
            /** start rules when the decimal charactor key is pressed */
            /** always use numeric pad dot to insert decimal separator */
            if (cCode === io.aDec || (io.altDec && cCode === io.altDec) || ((cCode === '.' || cCode === ',') && this.kdCode === 110)) { /** do not allow decimal character if no decimal part allowed */
                if (!io.mDec || !io.aDec) {
                    return true;
                }
                /** do not allow decimal character before aNeg character */
                if (io.aNeg && right.indexOf(io.aNeg) > -1) {
                    return true;
                }
                /** do not allow decimal character if other decimal character present */
                if (left.indexOf(io.aDec) > -1) {
                    return true;
                }
                if (right.indexOf(io.aDec) > 0) {
                    return true;
                }
                if (right.indexOf(io.aDec) === 0) {
                    right = right.substr(1);
                }
                this.setValueParts(left + io.aDec, right);
                return true;
            }
            /** start rule on negative sign */
            if (cCode === '-' || cCode === '+') { /** prevent minus if not allowed */
                if (!io.aNeg) {
                    return true;
                }
                /** carret is always after minus */
                if (left === '' && right.indexOf(io.aNeg) > -1) {
                    left = io.aNeg;
                    right = right.substring(1, right.length);
                }
                /** change sign of number, remove part if should */
                if (left.charAt(0) === io.aNeg) {
                    left = left.substring(1, left.length);
                } else {
                    left = (cCode === '-') ? io.aNeg + left : left;
                }
                this.setValueParts(left, right);
                return true;
            }
            /** digits */
            if (cCode >= '0' && cCode <= '9') { /** if try to insert digit before minus */
                if (io.aNeg && left === '' && right.indexOf(io.aNeg) > -1) {
                    left = io.aNeg;
                    right = right.substring(1, right.length);
                }
                this.setValueParts(left + cCode, right);
                return true;
            }
            /** prevent any other character */
            return true;
        },
        /**
         * formatting of just processed value with keeping of cursor position
         */
        formatQuick: function () {
            var io = this.io;
            var parts = this.getBeforeAfterStriped();
            var value = autoGroup(this.value, this.io);
            var position = value.length;
            if (value) {
                /** prepare regexp which searches for cursor position from unformatted left part */
                var left_ar = parts[0].split('');
                var i;
                for (i = 0; i < left_ar.length; i += 1) { /** thanks Peter Kovari */
                    if (!left_ar[i].match('\\d')) {
                        left_ar[i] = '\\' + left_ar[i];
                    }
                }
                var leftReg = new RegExp('^.*?' + left_ar.join('.*?'));
                /** search cursor position in formatted value */
                var newLeft = value.match(leftReg);
                if (newLeft) {
                    position = newLeft[0].length;
                    /** if we are just before sign which is in prefix position */
                    if (((position === 0 && value.charAt(0) !== io.aNeg) || (position === 1 && value.charAt(0) === io.aNeg)) && io.aSign && io.pSign === 'p') {
                        /** place carret after prefix sign */
                        position = this.io.aSign.length + (value.charAt(0) === '-' ? 1 : 0);
                    }
                } else if (io.aSign && io.pSign === 's') {
                    /** if we could not find a place for cursor and have a sign as a suffix */
                    /** place carret before suffix currency sign */
                    position -= io.aSign.length;
                }
            }
            this.that.value = value;
            this.setPosition(position);
            this.formatted = true;
        }
    };
    function getData($that) {
        var data = $that.data('autoNumeric');
        if (!data) {
            data = {};
            $that.data('autoNumeric', data);
        }
        return data;
    }

    function getHolder($that, options) {
        var data = getData($that);
        var holder = data.holder;
        if (holder === undefined && options) {
            holder = new autoNumericHolder($that.get(0), options);
            data.holder = holder;
        }
        return holder;
    }

    function getOptions($that) {
        var data = $that.data('autoNumeric');
        if (data && data.holder) {
            return data.holder.options;
        }
        return {};
    }

    function onInit(options) {
        options = options || {};
        var iv = $(this), holder = getHolder(iv, options);
        if (holder.io.aForm && (this.value || holder.io.wEmpty !== 'empty')) {
            iv.autoNumericSet(iv.autoNumericGet(options), options);
        }
    }

    function onKeyDown(e) {
        var iv = $(e.target), holder = getHolder(iv);
        holder.init(e);
        if (holder.skipAllways(e)) {
            holder.processed = true;
            return true;
        }
        if (holder.processAllways()) {
            holder.processed = true;
            holder.formatQuick();
            e.preventDefault();
            return false;
        } else {
            holder.formatted = false;
        }
        return true;
    }

    function onKeyPress(e) {
        var iv = $(e.target), holder = getHolder(iv);
        var processed = holder.processed;
        holder.init(e);
        if (holder.skipAllways(e)) {
            return true;
        }
        if (processed) {
            e.preventDefault();
            return false;
        }
        if (holder.processAllways() || holder.processKeypress()) {
            holder.formatQuick();
            e.preventDefault();
            return false;
        } else {
            holder.formatted = false;
        }
    }

    function onKeyUp(e) {
        var iv = $(e.target), holder = getHolder(iv);
        holder.init(e);
        var skip = holder.skipAllways(e);
        holder.kdCode = 0;
        delete holder.valuePartsBeforePaste;
        if (skip) {
            return true;
        }
        if (this.value === '') {
            return true;
        }
        if (!holder.formatted) {
            holder.formatQuick();
        }
    }

    function onFocusIn(e) {
        var iv = $(e.target), holder = getHolder(iv);
        holder.inVal = iv.val();
        var onempty = checkEmpty(holder.inVal, holder.io, true);
        if (onempty !== null) {
            iv.val(onempty);
        }
    }

    /** start change - thanks to Javier P. corrected the inline onChange event  added focusout version 1.55*/
    function onFocusOut(e) {
        var iv = $(e.target), holder = getHolder(iv);
        var io = holder.io, value = iv.val(), origValue = value;
        if (value !== '') {
            value = autoStrip(value, io);
            if (checkEmpty(value, io) === null && autoCheck(value, io)) {
                value = fixNumber(value, io.aDec, io.aNeg);
                value = autoRound(value, io.mDec, io.mRound, io.aPad);
                value = presentNumber(value, io.aDec, io.aNeg);
            } else {
                value = '';
            }
        }
        var groupedValue = checkEmpty(value, io, false);
        if (groupedValue === null) {
            groupedValue = autoGroup(value, io);
        }
        if (groupedValue !== origValue) {
            iv.val(groupedValue);
        }
        if (groupedValue !== holder.inVal) {
            iv.change();
            delete holder.inVal;
        }
    }

    $.fn.autoNumeric = function (options) {
        return this.each(function () {
            onInit.call(this, options);
        }).unbind('.autoNumeric').bind({
            'keydown.autoNumeric': onKeyDown,
            'keypress.autoNumeric': onKeyPress,
            'keyup.autoNumeric': onKeyUp,
            'focusin.autoNumeric': onFocusIn,
            'focusout.autoNumeric': onFocusOut
        });
    };
    /** thanks to Anthony & Evan C */
    function autoGet(obj) {
        if (typeof (obj) === 'string') {
            obj = obj.replace(/\[/g, "\\[").replace(/\]/g, "\\]");
            obj = '#' + obj.replace(/(:|\.)/g, '\\$1');
        }
        return $(obj);
    }

    $.autoNumeric = {};
    /**
     * public function that stripes the format and converts decimal seperator to a period
     * as of 1.7.2 `options` argument is deprecated, options are taken from initializer
     */
    $.autoNumeric.Strip = function (ii) {
        var $that = autoGet(ii);
        var options = getOptions($that);
        if (arguments[1] && typeof (arguments[1]) === 'object') {
            options = $.extend({}, options, arguments[1]);
        }
        var io = autoCode($that, options);
        var iv = autoGet(ii).val();
        iv = autoStrip(iv, io);
        iv = fixNumber(iv, io.aDec, io.aNeg);
        if (+iv === 0) {
            iv = '0';
        }
        return iv;
    };
    /**
     * public function that recieves a numeric string and formats to the target input field
     * as of 1.7.2 `options` argument is deprecated, options are taken from initializer
     */
    $.autoNumeric.Format = function (ii, iv) {
        var $that = autoGet(ii);
        var options = getOptions($that);
        if (arguments[2] && typeof (arguments[2]) === 'object') {
            options = $.extend({}, options, arguments[2]);
        }
        iv.toString();
        var io = autoCode($that, options);
        iv = autoRound(iv, io.mDec, io.mRound, io.aPad);
        iv = presentNumber(iv, io.aDec, io.aNeg);
        if (!autoCheck(iv, io)) {
            iv = autoRound('', io.mDec, io.mRound, io.aPad);
        }
        return autoGroup(iv, io);
    };
    /**
     * get a number (as a number) from a field.
     * as of 1.7.2 argument is deprecated, options are taken from initializer
     * $('input#my').autoNumericGet()
     */
    $.fn.autoNumericGet = function () {
        if (arguments[0]) {
            return $.autoNumeric.Strip(this, arguments[0]);
        }
        return $.autoNumeric.Strip(this);
    };
    /**
     * set a number to a field, formatting it appropriatly
     * as of 1.7.2 second argument is deprecated, options are taken from initializer
     * $('input#my').autoNumericSet(2.423)
     */
    $.fn.autoNumericSet = function (iv) {
        if (arguments[1]) {
            return this.val($.autoNumeric.Format(this, iv, arguments[1]));
        }
        return this.val($.fn.autoNumeric.Format(this, iv));
    };
    /**
     * plugin defaults
     */
    $.autoNumeric.defaults = {
        /**  allowed  numeric values
         * please do not modify
         */
        aNum: '0123456789',
        /** allowed thousand separator characters
         * comma = ','
         * period "full stop" = '.'
         * apostrophe is escaped = '\''
         * space = ' '
         * none = ''
         * NOTE: do not use numeric characters
         */
        aSep: ',',
        /** digital grouping for the thousand separator used in Format
         * dGroup: '2', results in 99,99,99,999 common in India
         * dGroup: '3', results in 999,999,999 default
         * dGroup: '4', results in 9999,9999,9999 used in some Asian countries
         */
        dGroup: '3',
        /** allowed decimal separator characters
         * period "full stop" = '.'
         * comma = ','
         */
        aDec: '.',
        /** allow to declare alternative decimal separator which is automatically replaced by aDec
         * developed for countries the use a comma ',' as the decimal character
         * and have keyboards\numeric pads that have a period 'full stop' as the decimal characters (Spain is an example)
         */
        altDec: null,
        /** allowed currency symbol
         * Must be in quotes aSign: '$',
         */
        aSign: '',
        /** placement of currency sign
         * for prefix  pSign: 'p',
         * for suffix pSign: 's',
         */
        pSign: 'p',
        /** maximum possible value
         * value must be enclosed in quotes and use the period for the decimal point
         * value must be larger than vMin
         */
        vMax: '999999999.99',
        /** minimum possible value
         * value must be enclosed in quotes and use the period for the decimal point
         * value must be smaller than vMax
         */
        vMin: '0.00',
        /** max number of decimal places = used to overide deciaml places set by the vMin & vMax values
         * value must be enclosed in quotes example mDec: '3',
         * This can also set the value via a call back function mDec: 'css:#
         */
        mDec: null,
        /** method used for rounding
         * mRound: 'S', Round-Half-Up Symmetric (default)
         * mRound: 'A', Round-Half-Up Asymmetric
         * mRound: 's', Round-Half-Down Symmetric (lower case s)
         * mRound: 'a', Round-Half-Down Asymmetric (lower case a)
         * mRound: 'B', Round-Half-Even "Bankers Rounding"
         * mRound: 'U', Round Up "Round-Away-From-Zero"
         * mRound: 'D', Round Down "Round-Toward-Zero" - same as trancate
         * mRound: 'C', Round to Ceiling "Toward Positive Infinity"
         * mRound: 'F', Round to Floor "Toward Negative Infinity"
         */
        mRound: 'S',
        /** controls decimal padding
         * aPad: true - always Pad decimals with zeros
         * aPad: false - does not pad with zeros.
         * aPad: `some number` - pad decimals with zero to number different from mDec
         * thanks to Jonas Johansson for the suggestion
         */
        aPad: true,
        /** Displayed on empty string
         * wEmpty: 'empty', - input can be blank
         * wEmpty: 'zero', - displays zero
         * wEmpty: 'sign', - displays the currency sign
         */
        wEmpty: 'empty',
        /** atomatically format value "###########.##" in form
         * Please note this is a little buggy due to how each browser handles refresh
         * use with caution
         */
        aForm: false
    };
    /** deprecated way to access defaults and helper functions */
    $.fn.autoNumeric.defaults = $.autoNumeric.defaults;
    $.fn.autoNumeric.Strip = $.autoNumeric.Strip;
    $.fn.autoNumeric.Format = $.autoNumeric.Format;
})(jQuery);

/*Chosen, a Select Box Enhancer for jQuery and Prototype
 by Patrick Filler for Harvest, https://getharvest.com
 //
 Version 0.9.14
 Full source at https://github.com/harvesthq/chosen
 Copyright (c) 2011 Harvest https://getharvest.com

 MIT License, https://github.com/harvesthq/chosen/blob/master/LICENSE.md
 This file is generated by `cake build`, do not edit it by hand.
 */
;
(function () {
    'use strict';
    var e;
    e = function () {
        function e() {
            this.options_index = 0, this.parsed = []
        }

        return e.prototype.add_node = function (e) {
            return e.nodeName.toUpperCase() === "OPTGROUP" ? this.add_group(e) : this.add_option(e)
        }, e.prototype.add_group = function (e) {
            var t, n, r, i, s, o;
            t = this.parsed.length, this.parsed.push({
                array_index: t,
                group: !0,
                label: e.label,
                children: 0,
                disabled: e.disabled
            }), s = e.childNodes, o = [];
            for (r = 0, i = s.length; r < i; r++)n = s[r], o.push(this.add_option(n, t, e.disabled));
            return o
        }, e.prototype.add_option = function (e, t, n) {
            if (e.nodeName.toUpperCase() === "OPTION")return e.text !== "" ? (t != null && (this.parsed[t].children += 1), this.parsed.push({
                array_index: this.parsed.length,
                options_index: this.options_index,
                value: e.value,
                text: e.text,
                html: e.innerHTML,
                selected: e.selected,
                disabled: n === !0 ? n : e.disabled,
                group_array_index: t,
                classes: e.className,
                style: e.style.cssText
            })) : this.parsed.push({
                array_index: this.parsed.length,
                options_index: this.options_index,
                empty: !0
            }), this.options_index += 1
        }, e
    }(), e.select_to_array = function (t) {
        var n, r, i, s, o;
        r = new e, o = t.childNodes;
        for (i = 0, s = o.length; i < s; i++)n = o[i], r.add_node(n);
        return r.parsed
    }, this.SelectParser = e
}).call(this), function () {
    var e, t;
    t = this, e = function () {
        function e(t, n) {
            this.form_field = t, this.options = n != null ? n : {};
            if (!e.browser_is_supported())return;
            this.is_multiple = this.form_field.multiple, this.set_default_text(), this.set_default_values(), this.setup(), this.set_up_html(), this.register_observers(), this.finish_setup()
        }

        return e.prototype.set_default_values = function () {
            var e = this;
            return this.click_test_action = function (t) {
                return e.test_active_click(t)
            }, this.activate_action = function (t) {
                return e.activate_field(t)
            }, this.active_field = !1, this.mouse_on_container = !1, this.results_showing = !1, this.result_highlighted = null, this.result_single_selected = null, this.allow_single_deselect = this.options.allow_single_deselect != null && this.form_field.options[0] != null && this.form_field.options[0].text === "" ? this.options.allow_single_deselect : !1, this.disable_search_threshold = this.options.disable_search_threshold || 0, this.disable_search = this.options.disable_search || !1, this.enable_split_word_search = this.options.enable_split_word_search != null ? this.options.enable_split_word_search : !0, this.search_contains = this.options.search_contains || !1, this.choices = 0, this.single_backstroke_delete = this.options.single_backstroke_delete || !1, this.max_selected_options = this.options.max_selected_options || Infinity, this.inherit_select_classes = this.options.inherit_select_classes || !1
        }, e.prototype.set_default_text = function () {
            return this.form_field.getAttribute("data-placeholder") ? this.default_text = this.form_field.getAttribute("data-placeholder") : this.is_multiple ? this.default_text = this.options.placeholder_text_multiple || this.options.placeholder_text || e.default_multiple_text : this.default_text = this.options.placeholder_text_single || this.options.placeholder_text || e.default_single_text, this.results_none_found = this.form_field.getAttribute("data-no_results_text") || this.options.no_results_text || e.default_no_result_text
        }, e.prototype.mouse_enter = function () {
            return this.mouse_on_container = !0
        }, e.prototype.mouse_leave = function () {
            return this.mouse_on_container = !1
        }, e.prototype.input_focus = function (e) {
            var t = this;
            if (this.is_multiple) {
                if (!this.active_field)return setTimeout(function () {
                    return t.container_mousedown()
                }, 50)
            } else if (!this.active_field)return this.activate_field()
        }, e.prototype.input_blur = function (e) {
            var t = this;
            if (!this.mouse_on_container)return this.active_field = !1, setTimeout(function () {
                return t.blur_test()
            }, 100)
        }, e.prototype.result_add_option = function (e) {
            var t, n;
            return e.disabled ? "" : (e.dom_id = this.container_id + "_o_" + e.array_index, t = e.selected && this.is_multiple ? [] : ["active-result"], e.selected && t.push("result-selected"), e.group_array_index != null && t.push("group-option"), e.classes !== "" && t.push(e.classes), n = e.style.cssText !== "" ? ' style="' + e.style + '"' : "", '<li id="' + e.dom_id + '" class="' + t.join(" ") + '"' + n + ">" + e.html + "</li>")
        }, e.prototype.results_update_field = function () {
            return this.set_default_text(), this.is_multiple || this.results_reset_cleanup(), this.result_clear_highlight(), this.result_single_selected = null, this.results_build()
        }, e.prototype.results_toggle = function () {
            return this.results_showing ? this.results_hide() : this.results_show()
        }, e.prototype.results_search = function (e) {
            return this.results_showing ? this.winnow_results() : this.results_show()
        }, e.prototype.choices_click = function (e) {
            e.preventDefault();
            if (!this.results_showing)return this.results_show()
        }, e.prototype.keyup_checker = function (e) {
            var t, n;
            t = (n = e.which) != null ? n : e.keyCode, this.search_field_scale();
            switch (t) {
                case 8:
                    if (this.is_multiple && this.backstroke_length < 1 && this.choices > 0)return this.keydown_backstroke();
                    if (!this.pending_backstroke)return this.result_clear_highlight(), this.results_search();
                    break;
                case 13:
                    e.preventDefault();
                    if (this.results_showing)return this.result_select(e);
                    break;
                case 27:
                    return this.results_showing && this.results_hide(), !0;
                case 9:
                case 38:
                case 40:
                case 16:
                case 91:
                case 17:
                    break;
                default:
                    return this.results_search()
            }
        }, e.prototype.generate_field_id = function () {
            var e;
            return e = this.generate_random_id(), this.form_field.id = e, e
        }, e.prototype.generate_random_char = function () {
            var e, t, n;
            return e = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ", n = Math.floor(Math.random() * e.length), t = e.substring(n, n + 1)
        }, e.prototype.container_width = function () {
            var e;
            return this.options.width != null ? this.options.width : (e = window.getComputedStyle != null ? parseFloat(window.getComputedStyle(this.form_field).getPropertyValue("width")) : typeof jQuery != "undefined" && jQuery !== null && this.form_field_jq != null ? this.form_field_jq.outerWidth() : this.form_field.getWidth(), e + "px")
        }, e.browser_is_supported = function () {
            var e;
            return window.navigator.appName === "Microsoft Internet Explorer" ? null !== (e = document.documentMode) && e >= 8 : !0
        }, e.default_multiple_text = "Select Some Options", e.default_single_text = "Select an Option", e.default_no_result_text = "No results match", e
    }(), t.AbstractChosen = e
}.call(this), function () {
    var e, t, n, r = {}.hasOwnProperty, i = function (e, t) {
        function i() {
            this.constructor = e
        }

        for (var n in t)r.call(t, n) && (e[n] = t[n]);
        return i.prototype = t.prototype, e.prototype = new i, e.__super__ = t.prototype, e
    };
    n = this, e = jQuery, e.fn.extend({
        chosen: function (n) {
            return AbstractChosen.browser_is_supported() ? this.each(function (r) {
                var i;
                i = e(this);
                if (!i.hasClass("chzn-done"))return i.data("chosen", new t(this, n))
            }) : this
        }
    }), t = function (t) {
        function r() {
            return r.__super__.constructor.apply(this, arguments)
        }

        return i(r, t), r.prototype.setup = function () {
            return this.form_field_jq = e(this.form_field), this.current_selectedIndex = this.form_field.selectedIndex, this.is_rtl = this.form_field_jq.hasClass("chzn-rtl")
        }, r.prototype.finish_setup = function () {
            return this.form_field_jq.addClass("chzn-done")
        }, r.prototype.set_up_html = function () {
            var t, n;
            return this.container_id = this.form_field.id.length ? this.form_field.id.replace(/[^\w]/g, "_") : this.generate_field_id(), this.container_id += "_chzn", t = ["chzn-container"], t.push("chzn-container-" + (this.is_multiple ? "multi" : "single")), this.inherit_select_classes && this.form_field.className && t.push(this.form_field.className), this.is_rtl && t.push("chzn-rtl"), n = {
                id: this.container_id,
                "class": t.join(" "),
                style: "width: " + this.container_width() + ";",
                title: this.form_field.title
            }, this.container = e("<div />", n), this.is_multiple ? this.container.html('<ul class="chzn-choices"><li class="search-field"><input type="text" value="' + this.default_text + '" class="default" autocomplete="off" style="width:25px;" /></li></ul><div class="chzn-drop"><ul class="chzn-results"></ul></div>') : this.container.html('<a href="javascript:void(0)" class="chzn-single chzn-default" tabindex="-1"><span>' + this.default_text + '</span><div><b></b></div></a><div class="chzn-drop"><div class="chzn-search"><input type="text" autocomplete="off" /></div><ul class="chzn-results"></ul></div>'), this.form_field_jq.hide().after(this.container), this.dropdown = this.container.find("div.chzn-drop").first(), this.search_field = this.container.find("input").first(), this.search_results = this.container.find("ul.chzn-results").first(), this.search_field_scale(), this.search_no_results = this.container.find("li.no-results").first(), this.is_multiple ? (this.search_choices = this.container.find("ul.chzn-choices").first(), this.search_container = this.container.find("li.search-field").first()) : (this.search_container = this.container.find("div.chzn-search").first(), this.selected_item = this.container.find(".chzn-single").first()), this.results_build(), this.set_tab_index(), this.set_label_behavior(), this.form_field_jq.trigger("liszt:ready", {chosen: this})
        }, r.prototype.register_observers = function () {
            var e = this;
            return this.container.mousedown(function (t) {
                e.container_mousedown(t)
            }), this.container.mouseup(function (t) {
                e.container_mouseup(t)
            }), this.container.mouseenter(function (t) {
                e.mouse_enter(t)
            }), this.container.mouseleave(function (t) {
                e.mouse_leave(t)
            }), this.search_results.mouseup(function (t) {
                e.search_results_mouseup(t)
            }), this.search_results.mouseover(function (t) {
                e.search_results_mouseover(t)
            }), this.search_results.mouseout(function (t) {
                e.search_results_mouseout(t)
            }), this.search_results.bind("mousewheel DOMMouseScroll", function (t) {
                e.search_results_mousewheel(t)
            }), this.form_field_jq.bind("liszt:updated", function (t) {
                e.results_update_field(t)
            }), this.form_field_jq.bind("liszt:activate", function (t) {
                e.activate_field(t)
            }), this.form_field_jq.bind("liszt:open", function (t) {
                e.container_mousedown(t)
            }), this.search_field.blur(function (t) {
                e.input_blur(t)
            }), this.search_field.keyup(function (t) {
                e.keyup_checker(t)
            }), this.search_field.keydown(function (t) {
                e.keydown_checker(t)
            }), this.search_field.focus(function (t) {
                e.input_focus(t)
            }), this.is_multiple ? this.search_choices.click(function (t) {
                e.choices_click(t)
            }) : this.container.click(function (e) {
                e.preventDefault()
            })
        }, r.prototype.search_field_disabled = function () {
            this.is_disabled = this.form_field_jq[0].disabled;
            if (this.is_disabled)return this.container.addClass("chzn-disabled"), this.search_field[0].disabled = !0, this.is_multiple || this.selected_item.unbind("focus", this.activate_action), this.close_field();
            this.container.removeClass("chzn-disabled"), this.search_field[0].disabled = !1;
            if (!this.is_multiple)return this.selected_item.bind("focus", this.activate_action)
        }, r.prototype.container_mousedown = function (t) {
            if (!this.is_disabled) {
                t && t.type === "mousedown" && !this.results_showing && t.preventDefault();
                if (t == null || !e(t.target).hasClass("search-choice-close"))return this.active_field ? !this.is_multiple && t && (e(t.target)[0] === this.selected_item[0] || e(t.target).parents("a.chzn-single").length) && (t.preventDefault(), this.results_toggle()) : (this.is_multiple && this.search_field.val(""), e(document).click(this.click_test_action), this.results_show()), this.activate_field()
            }
        }, r.prototype.container_mouseup = function (e) {
            if (e.target.nodeName === "ABBR" && !this.is_disabled)return this.results_reset(e)
        }, r.prototype.search_results_mousewheel = function (e) {
            var t, n, r;
            t = -((n = e.originalEvent) != null ? n.wheelDelta : void 0) || ((r = e.originialEvent) != null ? r.detail : void 0);
            if (t != null)return e.preventDefault(), e.type === "DOMMouseScroll" && (t *= 40), this.search_results.scrollTop(t + this.search_results.scrollTop())
        }, r.prototype.blur_test = function (e) {
            if (!this.active_field && this.container.hasClass("chzn-container-active"))return this.close_field()
        }, r.prototype.close_field = function () {
            return e(document).unbind("click", this.click_test_action), this.active_field = !1, this.results_hide(), this.container.removeClass("chzn-container-active"), this.winnow_results_clear(), this.clear_backstroke(), this.show_search_field_default(), this.search_field_scale()
        }, r.prototype.activate_field = function () {
            return this.container.addClass("chzn-container-active"), this.active_field = !0, this.search_field.val(this.search_field.val()), this.search_field.focus()
        }, r.prototype.test_active_click = function (t) {
            return e(t.target).parents("#" + this.container_id).length ? this.active_field = !0 : this.close_field()
        }, r.prototype.results_build = function () {
            var e, t, r, i, s;
            this.parsing = !0, this.results_data = n.SelectParser.select_to_array(this.form_field), this.is_multiple && this.choices > 0 ? (this.search_choices.find("li.search-choice").remove(), this.choices = 0) : this.is_multiple || (this.selected_item.addClass("chzn-default").find("span").text(this.default_text), this.disable_search || this.form_field.options.length <= this.disable_search_threshold ? this.container.addClass("chzn-container-single-nosearch") : this.container.removeClass("chzn-container-single-nosearch")), e = "", s = this.results_data;
            for (r = 0, i = s.length; r < i; r++)t = s[r], t.group ? e += this.result_add_group(t) : t.empty || (e += this.result_add_option(t), t.selected && this.is_multiple ? this.choice_build(t) : t.selected && !this.is_multiple && (this.selected_item.removeClass("chzn-default").find("span").text(t.text), this.allow_single_deselect && this.single_deselect_control_build()));
            return this.search_field_disabled(), this.show_search_field_default(), this.search_field_scale(), this.search_results.html(e), this.parsing = !1
        }, r.prototype.result_add_group = function (t) {
            return t.disabled ? "" : (t.dom_id = this.container_id + "_g_" + t.array_index, '<li id="' + t.dom_id + '" class="group-result">' + e("<div />").text(t.label).html() + "</li>")
        }, r.prototype.result_do_highlight = function (e) {
            var t, n, r, i, s;
            if (e.length) {
                this.result_clear_highlight(), this.result_highlight = e, this.result_highlight.addClass("highlighted"), r = parseInt(this.search_results.css("maxHeight"), 10), s = this.search_results.scrollTop(), i = r + s, n = this.result_highlight.position().top + this.search_results.scrollTop(), t = n + this.result_highlight.outerHeight();
                if (t >= i)return this.search_results.scrollTop(t - r > 0 ? t - r : 0);
                if (n < s)return this.search_results.scrollTop(n)
            }
        }, r.prototype.result_clear_highlight = function () {
            return this.result_highlight && this.result_highlight.removeClass("highlighted"), this.result_highlight = null
        }, r.prototype.results_show = function () {
            if (this.result_single_selected != null)this.result_do_highlight(this.result_single_selected); else if (this.is_multiple && this.max_selected_options <= this.choices)return this.form_field_jq.trigger("liszt:maxselected", {chosen: this}), !1;
            return this.container.addClass("chzn-with-drop"), this.form_field_jq.trigger("liszt:showing_dropdown", {chosen: this}), this.results_showing = !0, this.search_field.focus(), this.search_field.val(this.search_field.val()), this.winnow_results()
        }, r.prototype.results_hide = function () {
            return this.result_clear_highlight(), this.container.removeClass("chzn-with-drop"), this.form_field_jq.trigger("liszt:hiding_dropdown", {chosen: this}), this.results_showing = !1
        }, r.prototype.set_tab_index = function (e) {
            var t;
            if (this.form_field_jq.attr("tabindex"))return t = this.form_field_jq.attr("tabindex"), this.form_field_jq.attr("tabindex", -1), this.search_field.attr("tabindex", t)
        }, r.prototype.set_label_behavior = function () {
            var t = this;
            this.form_field_label = this.form_field_jq.parents("label"), !this.form_field_label.length && this.form_field.id.length && (this.form_field_label = e("label[for=" + this.form_field.id + "]"));
            if (this.form_field_label.length > 0)return this.form_field_label.click(function (e) {
                return t.is_multiple ? t.container_mousedown(e) : t.activate_field()
            })
        }, r.prototype.show_search_field_default = function () {
            return this.is_multiple && this.choices < 1 && !this.active_field ? (this.search_field.val(this.default_text), this.search_field.addClass("default")) : (this.search_field.val(""), this.search_field.removeClass("default"))
        }, r.prototype.search_results_mouseup = function (t) {
            var n;
            n = e(t.target).hasClass("active-result") ? e(t.target) : e(t.target).parents(".active-result").first();
            if (n.length)return this.result_highlight = n, this.result_select(t), this.search_field.focus()
        }, r.prototype.search_results_mouseover = function (t) {
            var n;
            n = e(t.target).hasClass("active-result") ? e(t.target) : e(t.target).parents(".active-result").first();
            if (n)return this.result_do_highlight(n)
        }, r.prototype.search_results_mouseout = function (t) {
            if (e(t.target).hasClass("active-result"))return this.result_clear_highlight()
        }, r.prototype.choice_build = function (t) {
            var n, r, i, s = this;
            return this.is_multiple && this.max_selected_options <= this.choices ? (this.form_field_jq.trigger("liszt:maxselected", {chosen: this}), !1) : (n = this.container_id + "_c_" + t.array_index, this.choices += 1, t.disabled ? r = '<li class="search-choice search-choice-disabled" id="' + n + '"><span>' + t.html + "</span></li>" : r = '<li class="search-choice" id="' + n + '"><span>' + t.html + '</span><a href="javascript:void(0)" class="search-choice-close" rel="' + t.array_index + '"></a></li>', this.search_container.before(r), i = e("#" + n).find("a").first(), i.click(function (e) {
                return s.choice_destroy_link_click(e)
            }))
        }, r.prototype.choice_destroy_link_click = function (t) {
            t.preventDefault(), t.stopPropagation();
            if (!this.is_disabled)return this.choice_destroy(e(t.target))
        }, r.prototype.choice_destroy = function (e) {
            if (this.result_deselect(e.attr("rel")))return this.choices -= 1, this.show_search_field_default(), this.is_multiple && this.choices > 0 && this.search_field.val().length < 1 && this.results_hide(), e.parents("li").first().remove(), this.search_field_scale()
        }, r.prototype.results_reset = function () {
            this.form_field.options[0].selected = !0, this.selected_item.find("span").text(this.default_text), this.is_multiple || this.selected_item.addClass("chzn-default"), this.show_search_field_default(), this.results_reset_cleanup(), this.form_field_jq.trigger("change");
            if (this.active_field)return this.results_hide()
        }, r.prototype.results_reset_cleanup = function () {
            return this.current_selectedIndex = this.form_field.selectedIndex, this.selected_item.find("abbr").remove()
        }, r.prototype.result_select = function (e) {
            var t, n, r, i;
            if (this.result_highlight)return t = this.result_highlight, n = t.attr("id"), this.result_clear_highlight(), this.is_multiple ? this.result_deactivate(t) : (this.search_results.find(".result-selected").removeClass("result-selected"), this.result_single_selected = t, this.selected_item.removeClass("chzn-default")), t.addClass("result-selected"), i = n.substr(n.lastIndexOf("_") + 1), r = this.results_data[i], r.selected = !0, this.form_field.options[r.options_index].selected = !0, this.is_multiple ? this.choice_build(r) : (this.selected_item.find("span").first().text(r.text), this.allow_single_deselect && this.single_deselect_control_build()), (!e.metaKey && !e.ctrlKey || !this.is_multiple) && this.results_hide(), this.search_field.val(""), (this.is_multiple || this.form_field.selectedIndex !== this.current_selectedIndex) && this.form_field_jq.trigger("change", {selected: this.form_field.options[r.options_index].value}), this.current_selectedIndex = this.form_field.selectedIndex, this.search_field_scale()
        }, r.prototype.result_activate = function (e) {
            return e.addClass("active-result")
        }, r.prototype.result_deactivate = function (e) {
            return e.removeClass("active-result")
        }, r.prototype.result_deselect = function (t) {
            var n, r;
            return r = this.results_data[t], this.form_field.options[r.options_index].disabled ? !1 : (r.selected = !1, this.form_field.options[r.options_index].selected = !1, n = e("#" + this.container_id + "_o_" + t), n.removeClass("result-selected").addClass("active-result").show(), this.result_clear_highlight(), this.winnow_results(), this.form_field_jq.trigger("change", {deselected: this.form_field.options[r.options_index].value}), this.search_field_scale(), !0)
        }, r.prototype.single_deselect_control_build = function () {
            if (this.allow_single_deselect && this.selected_item.find("abbr").length < 1)return this.selected_item.find("span").first().after('<abbr class="search-choice-close"></abbr>')
        }, r.prototype.winnow_results = function () {
            var t, n, r, i, s, o, u, a, f, l, c, h, p, d, v, m, g, y;
            this.no_results_clear(), f = 0, l = this.search_field.val() === this.default_text ? "" : e("<div/>").text(e.trim(this.search_field.val())).html(), o = this.search_contains ? "" : "^", s = new RegExp(o + l.replace(/[-[\]{}()*+?.,\\^$|#\s]/g, "\\$&"), "i"), p = new RegExp(l.replace(/[-[\]{}()*+?.,\\^$|#\s]/g, "\\$&"), "i"), y = this.results_data;
            for (d = 0, m = y.length; d < m; d++) {
                n = y[d];
                if (!n.disabled && !n.empty)if (n.group)e("#" + n.dom_id).css("display", "none"); else if (!this.is_multiple || !n.selected) {
                    t = !1, a = n.dom_id, u = e("#" + a);
                    if (s.test(n.html))t = !0, f += 1; else if (this.enable_split_word_search && (n.html.indexOf(" ") >= 0 || n.html.indexOf("[") === 0)) {
                        i = n.html.replace(/\[|\]/g, "").split(" ");
                        if (i.length)for (v = 0, g = i.length; v < g; v++)r = i[v], s.test(r) && (t = !0, f += 1)
                    }
                    t ? (l.length ? (c = n.html.search(p), h = n.html.substr(0, c + l.length) + "</em>" + n.html.substr(c + l.length), h = h.substr(0, c) + "<em>" + h.substr(c)) : h = n.html, u.html(h), this.result_activate(u), n.group_array_index != null && e("#" + this.results_data[n.group_array_index].dom_id).css("display", "list-item")) : (this.result_highlight && a === this.result_highlight.attr("id") && this.result_clear_highlight(), this.result_deactivate(u))
                }
            }
            return f < 1 && l.length ? this.no_results(l) : this.winnow_results_set_highlight()
        }, r.prototype.winnow_results_clear = function () {
            var t, n, r, i, s;
            this.search_field.val(""), n = this.search_results.find("li"), s = [];
            for (r = 0, i = n.length; r < i; r++)t = n[r], t = e(t), t.hasClass("group-result") ? s.push(t.css("display", "auto")) : !this.is_multiple || !t.hasClass("result-selected") ? s.push(this.result_activate(t)) : s.push(void 0);
            return s
        }, r.prototype.winnow_results_set_highlight = function () {
            var e, t;
            if (!this.result_highlight) {
                t = this.is_multiple ? [] : this.search_results.find(".result-selected.active-result"), e = t.length ? t.first() : this.search_results.find(".active-result").first();
                if (e != null)return this.result_do_highlight(e)
            }
        }, r.prototype.no_results = function (t) {
            var n;
            return n = e('<li class="no-results">' + this.results_none_found + ' "<span></span>"</li>'), n.find("span").first().html(t), this.search_results.append(n)
        }, r.prototype.no_results_clear = function () {
            return this.search_results.find(".no-results").remove()
        }, r.prototype.keydown_arrow = function () {
            var t, n;
            this.result_highlight ? this.results_showing && (n = this.result_highlight.nextAll("li.active-result").first(), n && this.result_do_highlight(n)) : (t = this.search_results.find("li.active-result").first(), t && this.result_do_highlight(e(t)));
            if (!this.results_showing)return this.results_show()
        }, r.prototype.keyup_arrow = function () {
            var e;
            if (!this.results_showing && !this.is_multiple)return this.results_show();
            if (this.result_highlight)return e = this.result_highlight.prevAll("li.active-result"), e.length ? this.result_do_highlight(e.first()) : (this.choices > 0 && this.results_hide(), this.result_clear_highlight())
        }, r.prototype.keydown_backstroke = function () {
            var e;
            if (this.pending_backstroke)return this.choice_destroy(this.pending_backstroke.find("a").first()), this.clear_backstroke();
            e = this.search_container.siblings("li.search-choice").last();
            if (e.length && !e.hasClass("search-choice-disabled"))return this.pending_backstroke = e, this.single_backstroke_delete ? this.keydown_backstroke() : this.pending_backstroke.addClass("search-choice-focus")
        }, r.prototype.clear_backstroke = function () {
            return this.pending_backstroke && this.pending_backstroke.removeClass("search-choice-focus"), this.pending_backstroke = null
        }, r.prototype.keydown_checker = function (e) {
            var t, n;
            t = (n = e.which) != null ? n : e.keyCode, this.search_field_scale(), t !== 8 && this.pending_backstroke && this.clear_backstroke();
            switch (t) {
                case 8:
                    this.backstroke_length = this.search_field.val().length;
                    break;
                case 9:
                    this.results_showing && !this.is_multiple && this.result_select(e), this.mouse_on_container = !1;
                    break;
                case 13:
                    e.preventDefault();
                    break;
                case 38:
                    e.preventDefault(), this.keyup_arrow();
                    break;
                case 40:
                    this.keydown_arrow()
            }
        }, r.prototype.search_field_scale = function () {
            var t, n, r, i, s, o, u, a;
            if (this.is_multiple) {
                n = 0, o = 0, i = "position:absolute; left: -1000px; top: -1000px; display:none;", s = ["font-size", "font-style", "font-weight", "font-family", "line-height", "text-transform", "letter-spacing"];
                for (u = 0, a = s.length; u < a; u++)r = s[u], i += r + ":" + this.search_field.css(r) + ";";
                return t = e("<div />", {style: i}), t.text(this.search_field.val()), e("body").append(t), o = t.width() + 25, t.remove(), this.f_width || (this.f_width = this.container.outerWidth()), o > this.f_width - 10 && (o = this.f_width - 10), this.search_field.css({width: o + "px"})
            }
        }, r.prototype.generate_random_id = function () {
            var t;
            t = "sel" + this.generate_random_char() + this.generate_random_char() + this.generate_random_char();
            while (e("#" + t).length > 0)t += this.generate_random_char();
            return t
        }, r
    }(AbstractChosen), n.Chosen = t
}.call(this);