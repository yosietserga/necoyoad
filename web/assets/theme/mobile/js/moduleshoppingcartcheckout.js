$(function () {

    $('#orderForm').ntForm({
        lockButton: false,
        cancelButton: false,
        submitButton: false
    });

    $('select[name="payment_zone_id"]').load(createUrl("account/register/zone",
        {
            country_id: window.nt.cart.paymentCountryId,
            zone_id: window.nt.cart.paymentZoneId
        }
    ));

    $('select[name="shipping_zone_id"]').load(createUrl("account/register/zone",
        {
            country_id: window.nt.cart.shippingCountryId,
            zone_id: window.nt.cart.shippingZoneId
        }
    ));

    var hasAddress = window.nt.cart.shippingCountryId;

    if (!window.nt.customer.isLogged) {
        $('#email').on('change', function (e) {
            var that = this;

            $.post(createUrl("account/register/checkemail", {
                email: $(that).val()
            })).done(function (response) {
                $('#tempLink').remove();
                var data = $.parseJSON(response);
                if (typeof data.error != 'undefined') {
                    $(that).removeClass('neco-input-success').addClass('neco-input-error');
                    $(that).parent().find('.neco-form-error')
                        .attr({'title': "Este email ya existe!"})
                        .append('<p id="tempLink" class="error">' + data.msg + '</p>');
                } else {
                    showSuccess(this, "No hay errores en este campo");
                    $('#tempLink').remove();
                }
            });
        });
    }

    $('#contentWrapper').ntWizard({
        next: function (data) {
            var stepId = $('.neco-wizard-step-active').attr('id');
            $(data.element).find('.neco-wizard-next').text('Siguiente');
            $(data.element).find('.neco-wizard-prev').show();

            if (stepId == 'necoWizardStep_1') {
                $(this).find('.neco-wizard-next').text(window.nt.cart.txtButtonCheckout);
                return false;
            }

            if (stepId == 'necoWizardStep_2') {
                var error = false;

                $(data.element).find('.neco-wizard-prev').show();

                /* si hay metodos de envios configurados y debe seleccionar uno */
                if (window.nt.cart.shippingMethods && window.nt.customer.isLogged) {
                    var isChecked = $('input[name=shipping_method]:checked').val();

                    if (!isChecked) {
                        error = true;
                        alert(window.nt.cart.txtMustSelectShippingMethod);
                    }
                }

                if (!window.nt.customer.isLogged) {
                    $('#email,#firstname,#lastname,#company,#rif,#telephone,#city,#postcode,#address_1').each(function (e) {
                        var value = !!$(this).val();
                        var required = $(this).attr('required');
                        var type = $(this).attr('type');
                        var top = $(this).offset().top;

                        if (!value) {
                            error = true;
                            $('#tempError').remove();

                            var msg = getMsgErrorContainer(window.nt.cart.txtMustFillMandatory);

                            showError(this, window.nt.cart.txtMustFillCorrectly);
                        }

                        var pattern = new RegExp(/.[\"\\\/\{\}\[\]\+']/i);

                        if (pattern.test($(this).val()) && !error) {
                            error = true;
                            $('#tempError').remove();

                            msg = getMsgErrorContainer('No se permiten ninguno de estos caracteres especiales [\"#$/\'+}{\u003C\u003E] en este formulario');

                            showError(this, 'No se permiten ninguno de estos caracteres especiales [\"#$&/?\'+}{\u003C\u003E] en este campo');

                            top = $(this).offset().top;
                        }

                        if (type == 'email' && $(this).val() == '@') {
                            error = true;
                            showError(this, 'Debes ingresar una direcci\u00F3n de email v\u00E1lida');
                        }

                        if (type == 'email') {
                            pattern = /^[_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)*\.(([0-9]{1,3})|([a-zA-Z]{2,4})|(aero|coop|info|museum|name))$/i;
                            $(this).on('change', function (event) {
                                err = checkPattern(pattern, $(this).val());
                                if (!err) {
                                    showError(this, 'Debes ingresar una direcci\u00F3n de email v\u00E1lida y que exista realmente');
                                    error = true;
                                } else {
                                    showSuccess(this, "No hay errores en este campo");
                                }
                            });
                        }

                        if (type == 'rif') {
                            var pattern = /\b[JGVE]-[0-9]{8}-[0-9]{1}\b/i;
                            $(this).on('change', function (event) {
                                err = checkPattern(pattern, $(this).val());
                                if (!err) {
                                    showError(this, 'Debes ingresar un n\u00FAmero de C\u00E9dula o RIF v\u00E1lido para poder continuar');
                                    error = true;
                                } else {
                                    showSuccess(this, "No hay errores en este campo");
                                }

                                if ($(this).hasClass('neco-input-error') && !error) {
                                    error = true;
                                    $("#tempError").remove();
                                    msg = getMsgErrorContainer('Hay errores en el formulario, por favor revise y corr\u00EDjalos todos para poder continuar');
                                }
                            });
                        }

                        if (!error) {
                            if (!window.nt.customer.isLogged) {
                                addCustomer();
                            }

                            if ((window.nt.customer.isLogged && window.nt.cart.shippingCountryId === 0) || !window.nt.customer.isLogged) {
                                addShippingAddress();
                            }
                        }
                    });
                } else if (window.nt.customer.isLogged && !hasAddress) {
                    $('#shipping_country_id,#shipping_zone_id,#shipping_city,#shipping_street,#shipping_postcode,#shipping_address_1').each(function (e) {
                        var value = !!$(this).val();
                        var required = $(this).attr('required');
                        var type = $(this).attr('type');
                        var top = $(this).offset().top;

                        if (!value) {
                            error = true;
                            $("#tempError").remove();
                            msg = getMsgErrorContainer('Debes rellenar todos los campos obligatorios identificados con asterisco (*)');
                            showError(this, 'Debes rellenar este campo con la informaci\u00F3n correspondiente');
                        }

                        var pattern = new RegExp(/.[\"\\\/\{\}\[\]\+']/i);
                        if (pattern.test($(this).val()) && !error) {
                            error = true;
                            $("#tempError").remove();
                            msg = getMsgErrorContainer('No se permiten ninguno de estos caracteres especiales [\"#$/\'+}{\u003C\u003E] en este formulario');
                            showError(this, 'No se permiten ninguno de estos caracteres especiales [\"#$&/?\'+}{\u003C\u003E] en este campo');
                            top = $(this).offset().top;
                        }

                        if ($(this).hasClass('neco-input-error') && !error) {
                            error = true;
                            $("#tempError").remove();
                            msg = getMsgErrorContainer('Hay errores en el formulario, por favor revise y corr\u00EDjalos todos para poder continuar');
                        }
                    });

                    if (!error) {
                        if (!window.nt.customer.isLogged) {
                            addCustomer();
                        }

                        if ((window.nt.customer.isLogged && window.nt.cart.shippingCountryId === 0) || !window.nt.customer.isLogged) {
                            addShippingAddress();
                        }
                    }
                } else if (window.nt.customer.isLogged && hasAddress && !window.nt.cart.shippingMethods && !error) {
                    processCart();
                }
                return error;
            }

            if (stepId == 'necoWizardStep_3') {
                var error = false;

                /* si hay metodos de envios configurados y debe seleccionar uno */
                if (window.nt.cart.shippingMethods) {
                    var isChecked = $('input[name=shipping_method]:checked').val();

                    if (!isChecked) {
                        error = true;
                        alert('Debes seleccionar un m\u00E9todo de env\u00EDo');
                    }
                }

                if (window.nt.customer.isLogged === 0 || hasAddress === 0) {
                    $('#shipping_country_id,#shipping_zone_id,#shipping_city,#shipping_street,#shipping_postcode,#shipping_address_1').each(function (e) {
                        var value = !!$(this).val();
                        var required = $(this).attr('required');
                        var type = $(this).attr('type');
                        var top = $(this).offset().top;

                        if (!value) {
                            error = true;
                            $("#tempError").remove();
                            msg = getMsgErrorContainer('Debes rellenar todos los campos obligatorios identificados con asterisco (*)');
                            showError(this, 'Debes rellenar este campo con la informaci\u00F3n correspondiente');
                        }

                        var pattern = new RegExp(/.[\"\\\/\{\}\[\]\+']/i);
                        if (pattern.test($(this).val()) && !error) {
                            error = true;
                            $("#tempError").remove();
                            msg = getMsgErrorContainer('No se permiten ninguno de estos caracteres especiales [\"#$/\'+}{\u003C\u003E] en este formulario');
                            showError(this, 'No se permiten ninguno de estos caracteres especiales [\"#$&/?\'+}{\u003C\u003E] en este campo');
                            top = $(this).offset().top;
                        }

                        if ($(this).hasClass('neco-input-error') && !error) {
                            error = true;
                            $("#tempError").remove();
                            msg = getMsgErrorContainer('Hay errores en el formulario, por favor revise y corr\u00EDjalos todos para poder continuar');
                        }
                    });

                    if (!error) {
                        addShippingAddress();
                    }
                } else if (!error) {
                    processCart();
                }
                return error;
            }

            if (!error && stepId == 'necoWizardStep_4') {
                processCart();
                return false;
            }
        },
        prev: function (data) {
            var stepId = $('.neco-wizard-step-active').attr('id');
            if (stepId == 'necoWizardStep_2') {
                $('#contentWrapper').find('.neco-wizard-prev').hide();
                $('#contentWrapper').find('.neco-wizard-next').text(window.nt.cart.txtButtonCheckout);
                $(this).find('.neco-wizard-next').text(window.nt.cart.txtButtonCheckout);
            }
            if (stepId == 'necoWizardStep_3') {
                $('input').each(function () {
                    $(this).removeClass('neco-input-success');
                });
            }
        },
        create: function (e) {
            $(e).find('.neco-wizard-next').text(window.nt.cart.txtButtonCheckout);
        }
    }).find('.neco-wizard-next').text(window.nt.cart.txtButtonCheckout);

    $('#contentWrapper').find('.neco-wizard-prev').hide();

    $('input[name=shipping_method]').on('change', function (e) {
        var tr = $('input[name=shipping_method]:checked').closest('tr');
        var title = tr.find('[data-shipping_title]').text();
        var price = tr.find('[data-shipping_price]').text();
        $('#shipping_method').html(title + ' ' + price);
    });

});

function addCustomer() {
    window.nt = window.nt || {};
    window.nt.events = window.nt.events || {};

    if (typeof window.nt.events.addingCustomer == 'undefined' || !window.nt.events.addingCustomer) {
        window.nt.events.addingCustomer = true;
        if (!$('#email').attr('disabled')) {
            $.post(createUrl("account/register/register"),
                {
                    email: $('#email').val(),
                    firstname: $('#firstname').val(),
                    lastname: $('#lastname').val(),
                    company: $('#company').val(),
                    rif: $('#rif').val(),
                    telephone: $('#telephone').val(),
                    country_id: $('#payment_country_id').val(),
                    zone_id: $('#payment_zone_id').val(),
                    city: $('#payment_city').val(),
                    street: $('#payment_street').val(),
                    postcode: $('#payment_postcode').val(),
                    address_1: $('#payment_address_1').val(),
                    session_address_var: 'shipping_address_id'
                }
            ).done(function () {
                window.nt.events.addingCustomer = false;
                $.post(createUrl("account/account/islogged"))
                    .done(function (data) {
                        if (data) {
                            $('#email').attr('disabled', 'disabled');
                            $('#firstname').attr('disabled', 'disabled');
                            $('#lastname').attr('disabled', 'disabled');
                            $('#company').attr('disabled', 'disabled');
                            $('#rif').attr('disabled', 'disabled');
                            $('#telephone').attr('disabled', 'disabled');

                            $('#shipping_country_id').val($('#payment_country_id').val());
                            $('#shipping_zone_id').load(createUrl("account/register/zone") + '&country_id=' + $('#payment_country_id').val() + '&zone_id=' + $('#payment_zone_id').val());
                            $('#shipping_street').val($('#payment_street').val());
                            $('#shipping_city').val($('#payment_city').val());
                            $('#shipping_postcode').val($('#payment_postcode').val());
                            $('#shipping_address_1').val($('#payment_address_1').val());

                            $('#confirmCompany').text($('#company').val());
                            $('#confirmRif').text($('#rif').val());

                            var confirmPaymentAddress = $('#payment_address_1').val() + ' ' + $('#payment_street').val() + ', ' + $('#payment_city').val() + '.';
                            $('#confirmPaymentAddress').text(confirmPaymentAddress);
                            $('#confirmShippingAddress').text(confirmPaymentAddress);
                        }
                    });
            });
        }
    }
}

function addShippingAddress() {
    window.nt = window.nt || {};
    window.nt.events = window.nt.events || {};

    if (typeof window.nt.events.addingAddress == 'undefined' || !window.nt.events.addingAddress) {
        window.nt.events.addingAddress = true;
        $.post(createUrl("account/register/addAddress"),
            {
                country_id: $('#shipping_country_id').val(),
                zone_id: $('#shipping_zone_id').val(),
                street: $('#shipping_street').val(),
                city: $('#shipping_city').val(),
                postcode: $('#shipping_postcode').val(),
                address_1: $('#shipping_address_1').val(),
                session_address_var: 'shipping_address_id'
            }
        ).done(function (data) {
            window.nt.events.addingAddress = false;
            hasAddress = 1;

            if (window.nt.customer.isLogged && window.nt.cart.shippingCountryId === 0) {
                $('#payment_zone_id').val($('#shipping_zone_id').val());
                $('#payment_street').val($('#shipping_street').val());
                $('#payment_city').val($('#shipping_city').val());
                $('#payment_postcode').val($('#shipping_postcode').val());
                $('#payment_address_1').val($('#shipping_address_1').val());
            }

            var confirmShippingAddress = $('#shipping_address_1').val() + ' ' + $('#payment_street').val() + ', ' + $('#shipping_city').val() + '.';
            $('#confirmShippingAddress').text(confirmShippingAddress);
        });
    }
}

function getMsgErrorContainer(msg) {
    return $(document.createElement('p'))
        .attr('id', 'tempError')
        .addClass('neco-submit-error')
        .text(msg);
}

function showError(input, msg) {
    $(input)
        .removeClass('neco-input-success')
        .addClass('neco-input-error')
        .parent()
        .find('.neco-form-error')
        .attr({'title': msg});
}

function showSuccess(input, msg) {
    $(input)
        .removeClass('neco-input-error')
        .addClass('neco-input-success')
        .parent()
        .find('.neco-form-error')
        .attr({'title': msg});
}

function processCart() {
    $.post(createUrl("checkout/confirm") + '&resp=json',
        $('#orderForm').serialize())
        .done(function (resp) {
            data = $.parseJSON(resp);
            location.href = createUrl("checkout/success") + '&order_id=' + data.order_id;
        });
}

function deleteCart(e, k) {
    $('#totals').html('<img src="' + window.nt.http_image + 'load.gif" alt="Cargando..." />');
    $(e).closest('tr').remove();
    $('#confirmItem' + k).remove();

    $.getJSON(createUrl('checkout/cart/delete'),
        {
            key: k
        }, function (data) {
            if (!data.error) {
                $('#weight').html(data.weight);
                $('#totals').html(data.totals);
                $('#totalsConfirm').html(data.totals);
            } else if (data.error == 'No hay productos en el carrito') {
                /*
                //change the text conditional by a var
                 */
                $('#weight').html(0.00);
                $('#totals').html(0);
                $('#totalsConfirm').html(0);
            } else {
                $('#cart').html(data.error);
                $('#weight').html('0.00kg');
            }
        });
}

function refreshCart(e, k) {
    $('#totals').html('<img src="' + window.nt.http_image + 'load.gif" alt="Cargando..." />');
    if (e.tagName != 'INPUT') {
        e = $(e).prev('input');
    }
    var price = $(e).closest('tr').find('td:nth-child(6)');

    var values = price.text().split(',');
    values[0] = values[0].replace(/\./g, '').replace(/\D+/, '');
    var intValue = Number(values[0].replace(/[^0-9\.]+/g, ''));
    var floatValue = parseInt(Number(values[1].replace(/[^0-9\.]+/g, '')));
    price = parseFloat(intValue + '.' + floatValue);
    var totalValue = Math.round((parseInt($(e).val()) * price) * 100) / 100;
    $(e).closest('tr').find('td:nth-child(7)').text('Bs. ' + totalValue);

    $('#confirmTotal' + k).text('Bs. ' + totalValue);
    $('#confirmQty' + k).text($(e).val());

    $.getJSON(createUrl('checkout/cart/refresh'),
        {
            key: k,
            quantity: $(e).val()
        }, function (data) {
            if (!data.error) {
                $('#weight').html(data.weight);
                $('#totals').html(data.totals);
                $('#totalsConfirm').html(data.totals);
            } else {
                $('#cart').html(data.error);
                $('#weight').html('0.00kg');
            }
        });
}

function checkPattern(pat, value) {
    pattern = new RegExp(pat);
    return pattern.test(value);
}
