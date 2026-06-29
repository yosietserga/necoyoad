<?php
// Locale
$_['code']                  = 'es';
$_['direction']             = 'ltr';
$_['date_format_short']     = 'd/m/Y';
$_['date_format_long']      = 'l dS F Y';
$_['time_format']           = 'h:i:s A';
$_['decimal_point']         = ',';
$_['thousand_point']        = '.';

// Heading 
$_['accountregister_heading_title'] = 'Crear Cuenta';
$_['accountmessage_heading_title'] = 'Mensajes';
$_['accountmessage_heading_title_create'] = 'Redactar Mensaje';
$_['accountmessage_heading_title_outbounce'] = 'Mensajes Enviados';
$_['accountmessage_heading_title_inbounce'] = 'Mensajes Recibidos';


// Tabs
$_['tab_home']             = 'Inicio';
$_['tab_products']         = 'Productos';
$_['tab_us']               = 'Nosotros';
$_['tab_categories']       = 'Categor&iacute;as';
$_['tab_blog']             = 'Blog';
$_['tab_pages']            = 'P&aacute;ginas';
$_['tab_specials']         = 'Ofertas';
$_['tab_contact']          = 'Contacto';

// Text
$_['form_warning_empty']         = 'Este campo es obligatorio';
$_['form_warning_email']         = 'Dirección inválida';
$_['text_email']         = 'Email';
$_['legend_recipe_form']         = 'Datos de Facturaci&oacute;n';

$_['placeholder_search']       = 'Buscar';
$_['select_all']       = 'Seleccionar Todos';
$_['select_five_per_page']       = '5 por p&aacute;gina';
$_['select_ten_per_page']       = '10 por p&aacute;gina';
$_['select_twenty_per_page']       = '20 por p&aacute;gina';
$_['select_fifty_per_page']       = '50 por p&aacute;gina';

$_['filter_order_id']       = 'ID del Pedido';
$_['text_recommendations']       = 'Recomendaciones';
$_['text_filter']       = 'Filtrar';
$_['text_pay']       = 'Pagar';
$_['text_see']       = 'Ver';
$_['text_currencies']       = 'Monedas';
$_['text_languages']        = 'Idiomas';
$_['text_language']         = 'Idioma';
$_['text_home']             = 'Inicio';
$_['text_cart_weight']         = 'Peso del Carrito';
$_['cart_loading']         = 'Cargando...';
$_['text_products']         = 'Productos';
$_['text_telephone']         = 'Tel&eacute;fonos';
$_['text_register']         = 'Registrarse';
$_['text_new_comment']      = 'Nuevo Comentario';
$_['text_time']             = 'P&aacute;gina creada en %s segundos';
$_['text_yes']              = 'Si';
$_['text_no']               = 'No';
$_['text_none']             = ' --- Ninguo --- ';
$_['text_select']           = '-- Por favor Seleccione --';
$_['text_all_zones']        = 'Todas las Zonas';
$_['text_pagination']       = 'Mostrando {start} a {end} de {total} ({pages} P&aacute;ginas)';
$_['text_separator']        = ' &gt; ';
$_['text_bookmark']         = ' Favoritos';
$_['text_quick_view']       = 'Vista R&aacute;pida';
// Account Register
$_['text_account']         = 'Cuenta';
$_['text_create']          = 'Registrar';
$_['text_account_already'] = 'Si ya posee una cuenta con nosotros, por favor inicie sesi&oacute;n <a href="%s">Aqu&iacute;</a>.';
$_['text_your_details']    = 'Datos Personales';
$_['text_your_address']    = 'Direcci&oacute;n';
$_['text_newsletter']      = 'Subscripci&oacute;n';
$_['text_your_password']   = 'Contrase&ntilde;a';
$_['text_agree']           = 'He lei&iacute;do y estoy de acuerdo con las <a onclick="window.open(\'%s\');">%s</a>';
$_['text_datos_fiscales']  = 'Datos Fiscales';
// Account Message
$_['text_read'] = 'Le&iacute;dos';
$_['text_non_read'] = 'No Le&iacute;dos';
$_['text_spam'] = 'No Deseados';


// Entry
$_['entry_firstname']       = 'Nombres:';
$_['entry_lastname']        = 'Apellidos:';
$_['entry_sexo']            = 'Sexo:';
$_['entry_nacimiento']      = 'Fecha de Nacimiento:';
$_['entry_email']           = 'E-Mail:';
$_['entry_telephone']       = 'Tel&eacute;fono:';
$_['entry_fax']             = 'Fax:';
$_['entry_company']         = 'Raz&oacute;n Social:';
$_['entry_address_1']       = 'Direcci&oacute;n Principal:';
$_['entry_address_2']       = 'Direcci&oacute;n Alterna:';
$_['entry_postcode']        = 'C&oacute;digo Postal:';
$_['entry_city']            = 'Ciudad:';
$_['entry_country']         = 'Pa&iacute;s:';
$_['entry_zone']            = 'Estado/Provincia/Departamento:';
$_['entry_newsletter']      = 'Subscribirme:';
$_['entry_password']        = 'Contrase&ntilde;a:';
$_['entry_confirm']         = 'Confirmar Contrase&ntilde;a:';
$_['entry_rif']             = 'RIF:';
$_['entry_captcha']         = 'Ingrese el resultado de la ecuaci&oacute;n:';
$_['entry_to']              = 'Destinatarios:';
$_['entry_subject']         = 'Asunto:';
$_['entry_message']         = 'Mensaje:';

// Error
$_['error_exists']         = 'El email ya existe! Si ya posees una cuenta con nosotros, por favor <a href="'. Url::createUrl("account/login") .'" title="Iniciar sesi&oacute;n" style="color:#000 !important">Ingresa Aqu&iacute;</a>';
$_['error_firstname']      = 'El nombre debe poseer entre 3 y 32 caracteres!';
$_['error_lastname']       = 'El apellido debe poseer entre 3 y 32 caracteres!';
$_['error_sexo']           = 'Debe seleccionar su sexo!';
$_['error_nacimiento']     = 'Debe ingresar una fecha v&aacute;lida y ser mayor de edad!';
$_['error_email']          = 'E-Mail inv&aacute;lido!';
$_['error_password']       = 'La contrase&ntilde;a debe poseer al menos 6 caracteres de longitud, 1 may&uacute;scula, 1 min&uacute;scula, 1 n&uacute;mero y 1 carcater especial (#$@%&+*-_)';
$_['error_confirm']        = 'La conatrse&ntilde;a no concuerda con la confirmaci&oacute;n!';
$_['error_address_1']      = 'La direcci&oacute;n debe poseer entre 3 y 128 caracteres!';
$_['error_city']           = 'La ciudad debe poseer entre 3 y 128 caracteres!';
$_['error_country']        = 'Por favor seleccione un pa&iacute;s';
$_['error_zone']           = 'Por favor seleccione un Estado/Provincia/Departamento';
$_['error_telephone']      = 'El tel&eacute;fono debe poseer entre 3 y 32 caracteres!';
$_['error_agree']          = 'Error: Debe leer y aceptar las %s para continuar!';
$_['error_rif']            = 'Debe ingresar un RIF v&aacute;lido';
$_['error_company']        = 'Debe poseer al menos 3 caracteres';
$_['error_captcha']        = 'El resultado de la ecuaci&oacute;n es incorrecta!';


// Buttons
$_['button_login_with_google'] = 'Login con <b>Google</b>';
$_['button_login_with_live'] = 'Login con <b>Outlook</b>';
$_['button_login_with_facebook'] = 'Login con <b>Facebook</b>';
$_['button_login_with_twitter'] = 'Login con <b>Twitter</b>';
$_['button_promote_in_google'] = 'Promocionar en <b>Google</b>';
$_['button_promote_in_live'] = 'Promocionar en <b>Outlook</b>';
$_['button_promote_in_facebook'] = 'Promocionar en <b>Facebook</b>';
$_['button_promote_in_twitter'] = 'Promocionar en <b>Twitter</b>';
$_['button_pay']            = 'Pagar';
$_['button_next']            = 'Siguiente';
$_['button_prev']            = 'Anterior';
$_['button_continue']       = 'Continuar';
$_['button_back']           = 'Volver';
$_['button_close']           = 'Cerrar';
$_['button_add_to_cart']    = 'Agregar Al Carrito';
$_['button_see_product']    = 'Ver Detalles';
$_['button_add_address']    = 'A&ntilde;adir Direcci&oacute;n';
$_['button_new_address']    = 'Nueva Direcci&oacute;n';
$_['button_change_address'] = 'Cambiar Direcci&oacute;n';
$_['button_edit']           = 'Editar';
$_['button_delete']         = 'Eliminar';
$_['button_reviews']        = 'Comentarios';
$_['button_write']          = 'Comentar';
$_['button_login']          = 'Login';
$_['button_update']         = 'Actualizar';
$_['button_shopping']       = 'Volver a la Tienda';
$_['button_checkout']       = 'Procesar Pedido';
$_['button_confirm']        = 'Confirmar Pedido';
$_['button_view']           = 'Ver';
$_['button_search']         = 'Buscar';
$_['button_go']             = 'Ir';
$_['button_coupon']         = 'Aplicar cup&oacute;n';
$_['button_guest']          = 'Resultado del pedido';
