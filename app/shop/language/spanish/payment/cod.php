<?php
// Text
$_['text_title'] = 'Pagar Al Recibir';
$_['text_payment']     = 'Su pedido no se tramitar&aacute; hasta que se reciba el pago.';
$_['text_new_payment'] = 'Nuevo Pago';
$_['text_success']     = '<p>Felicitaciones! Hemos registrado su pago con &eacute;xito, en breves minutos procederemos a la confirmaci&oacute;n de los datos registrados y le enviaremos la mercanc&iacute;a.</p><p>Gracias por su compra, vuelva pronto!</p>';

// Entry
$_['entry_cod_order_id']      = 'Seleccione el pedido:';
$_['entry_cod_amount']        = 'Monto del pago:';
$_['entry_cod_comment']       = 'Comentarios u Observaciones:';
$_['entry_cod_payment_method_on_delivery']      = 'Forma de pago al recibir la mercanc&iacute;a:';
$_['entry_cod_date_of_payment']    = 'Fecha que va a realizar el pago:';

// Help
$_['help_cod_order_id']      = 'Seleccione el pedido que desea pagar. Antes de realizar el pago verifique el monto total del pedido.';
$_['help_cod_payment_method_on_delivery']  = 'Indique la forma de pago que a utilizar al recibir la mercanc&iacute;a.';
$_['help_cod_date_of_payment'] = 'Indique la fecha de cuando realizará el pago.';
$_['help_cod_amount']        = 'Indique el monto del pago que va a realizar al recibir la mercanc&iacute;a, si el total pagado no concuerda con el total del pedido, le notificaremos la diferencia.';
$_['help_cod_comment']       = 'Agregue cualquier comentario u observaci&oacute;n que crea pertinente.';

// Error
$_['error_payment'] = 'Error: No se pudo procesar el pedido, por favor seleccione otra forma de pago o intente de nuevo m&aacute;s tarde.';
$_['error_not_order']      = 'No se encontr&oacute; el pedido! Por favor vuelva al listado de sus pedidos e intente de nuevo';
$_['error_not_logged']     = 'Debe iniciar sesi&oacute;n para registrar pagos';
$_['error_moneyless']      = '<p>El pago realizado se ha registrado con &eacute;xito. Sin embargo <b>NO ALCANZA</b> para cubrir el monto del pedido asociado, por favor revise la diferencia y haga los pagos necesarios. Puede ver la informaci&oacute;n del pedido ingresando a <a href="{%invoice%}">Mis Pedidos</a> o revisar los pagos asociados ingresando a <a href="{%payment_receipt%}">Mis Pagos</a>.</p><p>El monto de la deuda es:<h2>{%diff%}</h2>';
$_['error_moneymore']      = '<p>El pago realizado se ha registrado con &eacute;xito. Sin embargo <b>HA EXCEDIDO</b> el monto del pedido asociado, por favor revise la diferencia y cont&aacute;ctenos de inmediato. Puede ver la informaci&oacute;n del pedido ingresando a <a href="{%invoice%}">Mis Pedidos</a> o revisar los pagos asociados ingresando a <a href="{%payment_receipt%}">Mis Pagos</a>.</p><p>El monto del exceso es:<h2>{%diff%}</h2>';