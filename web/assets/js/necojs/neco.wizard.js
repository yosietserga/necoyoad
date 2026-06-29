/**
 * NecoWizard
 * Author: Yosiet Serga
 * Version: 1.0.0
 *
 * Dual licensed under the MIT and GPL licenses
 *
 */
(function($) {
    $.fn.ntWizard = function(method) {
        var defaults = {
            navControl: '*[data-wizard="nav"]',
            handle: '[data-wizard="step"]',
            nextText: 'Siguiente',
            prevText: 'Atr\u00E1s',
            ignoreText: 'Omitir',
            showIgnore: false,
            begin: 0,
            error:      {
                classname:'neco-form-error',
                text:'Lo sentimos pero no se pudo procesar el formulario'
            },
            options:    {},
            create:     function(){},
            start: function(){},
            stop:   function(){},
            next:   function(){},
            prev:   function(){},
            ignore: function(){}
        };

        var settings = {};
        var data = {};

        var methods = {
            init : function(options) {
                return this.each(function() {
                    settings = $.extend({}, defaults, options);
                    data.element = $(this);
                    helpers._create();
                });
            }
        };


        var helpers = {
            _create: function() {

                if ($(data.element).length > 0) {
                    $(data.element).addClass('neco-wizard');

                    data.navCount = data.stepCount = 0;
                    var w = $('.neco-wizard-controls').innerWidth();
                    var t = $('.neco-wizard-controls').find(settings.navControl).last().index() * 1 + 1;

                    $('.neco-wizard-controls').find(settings.navControl).each(function(e) {
                        $(this).addClass('neco-wizard-control').attr({
                            'id':'necoWizardControl_' + ($(this).index() + 1)
                        })
                            .css('width', (w/t) +'px')
                            .prepend('<b>'+ ($(this).index() + 1) +'. </b>');
                        if ($(this).index() == 0 && !settings.begin) {
                            $(this).show().addClass('neco-wizard-control-active');
                        } else if ($(this).index() == settings.begin) {
                            $(this).show().addClass('neco-wizard-control-active');
                        }
                        data.navCount++;
                    });

                    $(window).on('resize',function(){
                        var w = $(data.element).find('.neco-wizard-controls').innerWidth() - 0.3;
                        var t = $(data.element).find('.neco-wizard-controls').find(settings.navControl).last().index() * 1 + 1;
                        $(data.element).find('.neco-wizard-control').css('width', (w/t) +'px');
                    })

                    $('.neco-wizard-steps > ' + settings.handle).each(function(e) {
                        $(this).addClass('neco-wizard-step').attr({
                            'id':'necoWizardStep_' + ($(this).index() + 1)
                        }).hide();
                        if ($(this).index() == 0 && !settings.begin) {
                            $(this).show().addClass('neco-wizard-step-active');
                        } else if ($(this).index() == settings.begin) {
                            $(this).show().addClass('neco-wizard-step-active');
                        }
                        data.stepCount++;
                    });

                    /* if (navCount == stepCount) { */

                    var createStepper = function createStepper (direction) {
                        'use strict';
                        var markup,
                            stepper;

                        markup = [
                            "<div class='action-button neco-wizard-" + direction + "'>",
                            "<a>",
                            settings[direction + "Text"],
                            "</a>",
                            "</div>" ];

                        stepper = document.createElement('div');
                        stepper.innerHTML = markup.join("");
                        stepper.addEventListener('click', function (e) {
                            e.preventDefault();
                            e.stopPropagation();
                            helpers['_'.concat(direction)](e);
                        });
                    };

                    var nextBtn = $('<div>').attr({
                        'class':'action-step neco-wizard-next'
                    })
                        .text(settings.nextText)
                        .on('click',function(e){
                            e.preventDefault();
                            helpers._next(e);
                        });

                    var prevBtn = $('<div>').attr({
                        'class':'action-step neco-wizard-prev'
                    })
                        .text(settings.prevText)
                        .on('click',function(e){
                            e.preventDefault();
                            helpers._prev(e);
                        });

                    var ignoreBtn = $(document.createElement('a')).attr({
                        'class':'action-step neco-wizard-ignore'
                    })
                        .text(settings.ignoreText)
                        .on('click',function(e){
                            helpers._ignore(e);
                        });

                    data.element.append(prevBtn);
                    data.element.append(nextBtn);
                    if (settings.ignoreShow) {data.element.append(ignoreBtn);}
                    /*} else {
                        //TODO: error, no cargar wizard
                    }*/

                }
                if (typeof settings.create == 'function') {
                    settings.create(data.element);
                }
            },
            _start: function() {
                if (typeof settings.start == "function") {
                    settings.start();
                }
            },
            _stop: function() {
                if (typeof settings.stop == "function") {
                    settings.stop();
                }
            },

            _next: function(e) {
                e.preventDefault();
                e.stopPropagation();
                helpers._start();
                var necowizardControls = $('[data-wizard="controls"]');
                var necoWizardSteps = $('.neco-wizard-steps');
                var currentStep = $('.neco-wizard-step-active');
                var nextStep = currentStep.next();
                var currentControl = $('.neco-wizard-control-active');
                var nextControl = currentControl.next();
                var nextControlName = $(nextControl).attr("data-wizard-step");


                if (typeof settings.next == "function") {
                    error = settings.next(data);
                    if (typeof error != 'undefined' && error) {
                        return false;
                    } else {
                        currentStep.animate({
                            opacity:0,
                            marginLeft:'-' + necoWizardSteps.width(),
                            marginTop:'-' +  necoWizardSteps.height()
                        }).css({
                            display:'none'
                        }).removeClass('neco-wizard-step-active');

                        $(nextStep).css({
                            marginLeft:$('.neco-wizard-steps').width() + 'px',
                            marginTop:$('.neco-wizard-steps').height() + 'px',
                            display:'block'
                        }).animate({
                            opacity:1,
                            marginLeft:'0px',
                            marginTop:'0px',
                        }).addClass('neco-wizard-step-active');

                        necowizardControls.attr("data-current-step", nextControlName);
                        currentControl.removeClass('neco-wizard-control-active').addClass('neco-wizard-control-done');
                        nextControl.removeClass('neco-wizard-control-done').addClass('neco-wizard-control-active');
                    }
                } else {
                    currentStep.animate({
                        opacity:0,
                        marginLeft:'-' + $('.neco-wizard-steps').width(),
                        marginTop:'-' + $('.neco-wizard-steps').height()
                    }).css({
                        display:'none'
                    }).removeClass('neco-wizard-step-active');

                    $(nextStep).css({
                        marginLeft: necoWizardSteps.width() + 'px',
                        marginTop: necoWizardSteps.height() + 'px',
                        display:'block'
                    }).animate({
                        opacity:1,
                        marginLeft:'0px',
                        marginTop:'0px',
                    }).addClass('neco-wizard-step-active');

                    necowizardControls.attr("data-current-step", nextControlName);
                    currentControl.removeClass('neco-wizard-control-active').addClass('neco-wizard-control-done');
                    nextControl.removeClass('neco-wizard-control-done').addClass('neco-wizard-control-active');
                }

                helpers._stop();
            },
            _prev: function(e) {
                e.preventDefault();
                e.stopPropagation();
                helpers._start();
                var necowizardControls = $('[data-wizard="controls"]');
                var necoWizardSteps = $('.neco-wizard-steps');
                var currentStep = $('.neco-wizard-step-active');
                var previousStep = currentStep.prev();
                var currentControl = $('.neco-wizard-control-active');
                var previousControl = currentControl.prev();
                var nextControlName = previousControl.attr("data-wizard-step");

                if (typeof settings.prev == "function") {
                    res = settings.prev();
                    if (typeof res != 'undefined' && res.error) {
                        return false;

                    } else {
                        currentStep.animate({
                            opacity:0,
                            marginLeft:'-' +  necoWizardSteps.width(),
                            marginTop:'-' +  necoWizardSteps.height()
                        }).css({
                            display:'none'
                        }).removeClass('neco-wizard-step-active');

                        $(previousStep).css({
                            marginLeft: necoWizardSteps.width() + 'px',
                            marginTop:  necoWizardSteps.height() + 'px',
                            display:'block'
                        }).animate({
                            opacity:1,
                            marginLeft:'0px',
                            marginTop:'0px',
                        }).addClass('neco-wizard-step-active');

                        necowizardControls.attr("data-current-step", nextControlName);
                        currentControl.removeClass('neco-wizard-control-active').removeClass('neco-wizard-control-done');
                        previousControl.removeClass('neco-wizard-control-done').addClass('neco-wizard-control-active');
                    }
                } else {
                    currentStep.animate({
                        opacity:0,
                        marginLeft:'-' + necoWizardSteps.width(),
                        marginTop:'-' + necoWizardSteps.height()
                    }).css({
                        display:'none'
                    }).removeClass('neco-wizard-step-active');

                    $(previousStep).css({
                        marginLeft: necoWizardSteps.width() + 'px',
                        marginTop: necoWizardSteps.height() + 'px',
                        display:'block'
                    }).animate({
                        opacity:1,
                        marginLeft:'0px',
                        marginTop:'0px',
                    }).addClass('neco-wizard-step-active');

                    necowizardControls.attr("data-current-step", nextControlName);
                    currentControl.removeClass('neco-wizard-control-active').addClass('neco-wizard-control-done');
                    previousControl.removeClass('neco-wizard-control-done').addClass('neco-wizard-control-active');
                }
                helpers._stop();
            },
            _ignore: function() {
                if (typeof settings.ignore == "function") {
                    settings.ignore();
                }
            }
        };

        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || !method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error( 'Method "' +  method + '" does not exist in ntWizard plugin!');
        }
    }
})(jQuery);