<?php
// Text
$_['text_title']       = 'Transferencia Bancaria';
$_['text_instruction'] = 'Por fa transfiera el importe total a la siguiente cuenta.';
$_['text_payment']     = 'Su pedido no se tramitar&aacute; hasta que se reciba el pago.';
$_['text_new_payment'] = 'Nuevo Pago';
$_['text_date_of_payment'] = 'D&iacute;a en el que ir&aacute; a pagar';
$_['text_payment_method_on_delivery']  = 'Forma de pago al recibir la mercanc&iacute;a';
$_['text_success']     = '<p>Felicitaciones! Hemos registrado su pago con &eacute;xito, en breves minutos procederemos a la confirmaci&oacute;n de los datos registrados y le enviaremos la mercanc&iacute;a.</p><p>Gracias por su compra, vuelva pronto!</p>';

// Entry
$_['entry_bank_transfer_order_id']      = 'Seleccione el pedido:';
$_['entry_bank_transfer_bank']          = 'Desde que banco hizo la transferencia:';
$_['entry_bank_transfer_bank_account']  = 'Hacia que cuenta hizo la transferencia:';
$_['entry_bank_transfer_transact']      = 'Indique el n&uacute;mero de transacci&oacute;n:';
$_['entry_bank_transfer_date_added']    = 'Fecha de emisi&oacute;n de la transferencia:';
$_['entry_bank_transfer_amount']        = 'Monto de la transferencia:';
$_['entry_bank_transfer_comment']       = 'Comentarios u Observaciones:';

// Help
$_['help_bank_transfer_order_id']      = 'Seleccione el pedido que desea pagar. Antes de realizar el pago verifique el monto total del pedido.';
$_['help_bank_transfer_bank']          = 'Indique desde que banco hizo la transferencia. Dependiendo del banco emisor, la comprobaci&oacute;n del pago puede tardar hasta 72 horas.';
$_['help_bank_transfer_bank_account']  = 'Seleccione la cuenta a la cual hizo la transferencia. Tenga en cuenta que de indicar mal la cuenta destino, no se podr� comprobar su pago y la mercanc&iacute;a no ser&aacute; entregada.';
$_['help_bank_transfer_transact']      = 'Indique el n&uacute;mero de transacci&oacute;n emitido por la entidad bancaria al concretar la transferencia. A trav&eacute;s de este n&uacute;mero confirmaremos su pago, si lo escribe incorrectamente, no podremos validar su pago y la mercanc&iacute;a no ser&aacute; entregada.';
$_['help_bank_transfer_date_added']    = 'Indique cuando hizo la transferencia. Dependiendo del banco emisor, la comprobaci&oacute;n del pago puede tardar hasta 72 horas.';
$_['help_bank_transfer_amount']        = 'Indique el monto de la transferencia, si el total pagado no concuerda con el total del pedido, le notificaremos la diferencia.';
$_['help_bank_transfer_comment']       = 'Agregue cualquier comentario u observaci&oacute;n que crea pertinente.';

// Error
$_['error_payment'] = 'Error: No se pudo procesar el pedido, por favor seleccione otra forma de pago o intente de nuevo m&aacute;s tarde.';
$_['error_not_order']      = 'No se encontr&oacute; el pedido! Por favor vuelva al listado de sus pedidos e intente de nuevo';
$_['error_not_logged']     = 'Debe iniciar sesi&oacute;n para registrar pagos';
$_['error_moneyless']      = '<p>El pago realizado se ha registrado con &eacute;xito. Sin embargo <b>NO ALCANZA</b> para cubrir el monto del pedido asociado, por favor revise la diferencia y haga los pagos necesarios. Puede ver la informaci&oacute;n del pedido ingresando a <a href="{%invoice%}">Mis Pedidos</a> o revisar los pagos asociados ingresando a <a href="{%payment_receipt%}">Mis Pagos</a>.</p><p>El monto de la deuda es:<h2>{%diff%}</h2></p>';
$_['error_moneymore']      = '<p>El pago realizado se ha registrado con &eacute;xito. Sin embargo <b>HA EXCEDIDO</b> el monto del pedido asociado, por favor revise la diferencia y cont&aacute;ctenos de inmediato. Puede ver la informaci&oacute;n del pedido ingresando a <a href="{%invoice%}">Mis Pedidos</a> o revisar los pagos asociados ingresando a <a href="{%payment_receipt%}">Mis Pagos</a>.</p><p>El monto del exceso es:<h2>{%diff%}</h2></p>';
