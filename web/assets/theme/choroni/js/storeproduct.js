function loginForm(httpHome,token) {
    if (!$('#overlayTemp')) {
        overlayHelper();
    }
    
    if (!$.ui) {
        $(document.createElement('script')).attr({
            'src' : httpHome + 'assets/js/vendor/jquery-ui.min.js',
            'type': 'text/javascript',
            'async': 1
        }).appendTo('head');
        
        $(document.createElement('link')).attr({
            'href' : httpHome + 'assets/css/jquery-ui/jquery-ui.min.css',
            'rel': 'stylesheet',
            'media': 'all'
        }).appendTo('head');
    }
    
    if (!$.fn.ntForm) {
        $(document.createElement('script')).attr({
            'src' : httpHome + 'assets/js/necojs/neco.form.js',
            'type': 'text/javascript',
            'async': 1
        }).appendTo('head');
        
        $(document.createElement('link')).attr({
            'href' : httpHome + 'assets/css/neco.form.css',
            'rel': 'stylesheet',
            'media': 'all'
        }).appendTo('head');
    }
    
    if (!$.fn.crypt) {
        $(document.createElement('script')).attr({
            'src' : httpHome + 'assets/js/vendor/jquery.crypt.js',
            'type': 'text/javascript',
            'async': 1
        }).appendTo('head');
    }
    
    /* login form */
    inputEmail = $(document.createElement('input')).attr({
        'type':'email',
        'name':'email',
        'required':'required',
        'showquick':'off',
        'id':'productLoginEmail',
        'placeholder':'Ingrese su email'
    });
    
    inputPwd = $(document.createElement('input')).attr({
        'type':'password',
        'name':'password',
        'required':'required',
        'showquick':'off',
        'id':'productLoginPassword',
        'placeholder':'password'
    });
    
    submit = $(document.createElement('a')).attr({
        'class':'button',
        'title':'Login',
    })
    .html('Login')
    .on('click',function(e){
        $('.message').remove();
        if (inputPwd.val().length && inputEmail.val().length) {
            $(this).hide();
            $.post(httpHome + 'index.php?r=account/login/header',
            {
                email:inputEmail.val(),
                password:inputPwd.crypt({method:'md5'}),
                token:token
            },
            function(response) {
                var data = $.parseJSON(response);
                if (data.error==1) {
                    window.location.href = httpHome + 'index.php?r=account/login&error=true'
                } else {
                    $(this).show();
                    window.location.reload();
                }
            });
        } else {
            $(document.createElement('div')).attr({
                'class':'message warning'
            })
            .html('Debes ingresar tu Email y la contrase&ntilde;a de tu cuenta. Si aun no te has registrado, por favor rellena el formulario de la derecha.')
            .after(this);
        }
    });
    
    recovery = $(document.createElement('a')).attr({
        'title':'Recuperar Contrase&ntilde;a',
        'href':httpHome + 'index.php?r=account/forgotten',
    })
    .html('Recuperar Contrase&ntilde;a');
    
    title = $(document.createElement('div')).attr({
        'class':'header'
    })
    .html('<hgroup><h1>Iniciar Sesi&oacute;n</h1></hgroup>');
    
    container = $(document.createElement('div')).attr({
        'class':'grid_3'
    }).appendTo('#overlayTemp span.content');
    
    container2 = container.clone();
    
    form = $(document.createElement('form')).attr({
        'action':httpHome + 'index.php?r=account/login'
    })
    .append(title)
    .append(inputEmail)
    .append(inputPwd)
    .append(submit)
    .append(recovery)
    .appendTo(container)
    .ntForm({
        'submitButton':false,
        'cancelButton':false,
        'lockButton':false
    });
    
    /* register form */
    inputEmail2 = inputEmail.clone();
    inputEmail2.attr('id','pREmail');
    
    inputFirstname = $(document.createElement('input')).attr({
        'type':'text',
        'name':'firstname',
        'id':'pRFirstname',
        'required':'required',
        'showquick':'off',
        'placeholder':'Ingrese sus nombres'
    });
    
    inputLastname = $(document.createElement('input')).attr({
        'type':'text',
        'name':'lastname',
        'id':'pRLastname',
        'required':'required',
        'showquick':'off',
        'placeholder':'Ingrese sus apellidos'
    });
    
    title = $(document.createElement('div')).attr({
        'class':'header'
    })
    .html('<h1>Crear Cuenta</h1>');
    
    form = $(document.createElement('form')).attr({
        'action':httpHome + 'index.php?r=account/register',
        'method':'post'
    })
    .append(title)
    .append(inputFirstname)
    .append(inputLastname)
    .append(inputEmail2)
    .appendTo(container2)
    .ntForm({
        lockButton:false,
        url:httpHome + 'index.php?r=account/register'
    });
    
    container2.appendTo('#overlayTemp span.content');
    
    $(document.createElement('div')).attr({
        'class':'grid_3'
    })
    .html('<p>Su seguridad es muy importante para nosotros y por eso es necesario que para cualquier pregunta, contacto o compra que desee realizar, debe estar previamente registrado y haber iniciado sesi&oacute;n.</p>')
    .appendTo('#overlayTemp span.content');
    
    resizeLightbox(840);
    $(window).on('resize',function(e){
        resizeLightbox(840);
    });
}

function contactForm(data,httpHome,token) {
    if (!$('#overlayTemp')) {
        overlayHelper();
    }
    
    if (!$.ui) {
        $(document.createElement('script')).attr({
            'src' : httpHome + 'assets/js/vendor/jquery-ui.min.js',
            'type': 'text/javascript',
            'async': 1
        }).appendTo('head');
        
        $(document.createElement('link')).attr({
            'href' : httpHome + 'assets/css/jquery-ui/jquery-ui.min.css',
            'rel': 'stylesheet',
            'media': 'all'
        }).appendTo('head');
    }
    
    if (!$.fn.ntForm) {
        $(document.createElement('script')).attr({
            'src' : httpHome + 'assets/js/necojs/neco.form.js',
            'type': 'text/javascript',
            'async': 1
        }).appendTo('head');
        
        $(document.createElement('link')).attr({
            'href' : httpHome + 'assets/css/neco.form.css',
            'rel': 'stylesheet',
            'media': 'all'
        }).appendTo('head');
    }
    
    inputMsg = $(document.createElement('textarea')).attr({
        'name':'message',
        'required':'required',
        'id':'message',
        'placeholder':'Ingresa tu mensaje'
    }).css({
        width:'390px'
    });
    
    inputTo = $(document.createElement('input')).attr({
        'name':'to',
        'type':'hidden',
        'id':'to'
    })
    .val(data.seller_id);
    
    inputSubject = $(document.createElement('input')).attr({
        'name':'subject',
        'type':'hidden',
        'id':'subject'
    })
    .val('Contacto Nuevo de '+ data.buyer_name);
    
    submit = $(document.createElement('a')).attr({
        'class':'button',
        'title':'Contactar'
    })
    .html('Enviar Mensaje')
    .after('<div class="clear"></div>')
    .on('click',function(e){
        var that = $(this);
        $('.message').remove();
        if (inputMsg.val().length) {
            that.hide();
            $(document.createElement('div'))
                .attr({
                    'class':'message alert',
                    'id':'temp'
                })
                .html('<img src="assets/images/loader.gif" alt="Enviando mensaje..." />')
                .appendTo('#messageFormTemp');
            $.post(httpHome + 'index.php?r=account/message/send',
            {
                subject:inputSubject.val(),
                message:inputMsg.val(),
                to:inputTo.val(),
                token:token
            },
            function(response) {
                var data = $.parseJSON(response);
                $('.message').remove();
                if (data.error==1) {
                    that.show();
                } else {
                    that.show();
                    $('#message').val('');
                    $(document.createElement('div'))
                        .attr({
                            'class':'message success',
                            'id':'temp'
                        })
                        .html('Mensaje Enviado')
                        .appendTo('#messageFormTemp');
                }
            });
        } else {
            $(document.createElement('div'))
                .attr({
                    'class':'message warning',
                    'id':'temp'
                })
                .html('Debes ingresar el mensaje a enviar')
                .appendTo('#messageFormTemp');
        }
    });
    
    title = $(document.createElement('div')).attr({
        'class':'header'
    })
    .html('<hgroup><h1>Contactar Al Anunciante</h1></hgroup>')
    .after('<div class="clear"></div>');
    
    container = $(document.createElement('div')).attr({
        'class':'grid_6'
    }).appendTo('#overlayTemp span.content');
    
    form = $(document.createElement('form')).attr({
        'id':'messageFormTemp'
    })
    .append(title)
    .append(inputMsg)
    .append(inputSubject)
    .append(inputTo)
    .append(submit)
    .appendTo(container)
    .ntForm({
        'submitButton':false,
        'cancelButton':false,
        'lockButton':false
    });
    
    div = $(document.createElement('div')).attr({
        'class':'grid_7'
    })
    .appendTo('#overlayTemp span.content');
    
    resizeLightbox(420);
    $(window).on('resize',function(e){
        resizeLightbox(420);
    });
}

function productContact(isLogged,httpHome,token,data) {console.log('storeproduct.js line 326');
    overlayHelper();
    if (!isLogged) {
        loginForm(httpHome,token);
    } else {
        contactForm(data,httpHome,token);
    }
}

