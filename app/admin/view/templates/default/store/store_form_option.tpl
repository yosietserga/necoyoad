<div>
    <h2>Opciones</h2>
    <table class="form">
        <tr>
            <td><?php echo $l('entry_redirect_when_mobile'); ?></td>
            <td>
                <input title="Marque esta casilla si desea redireccionar la tienda hacia otra url cuando detecte acceso desde un móvil" type="checkbox" name="config_redirect_when_mobile" value="1"<?php if ($config_redirect_when_mobile) { echo ' checked="checked"'; } ?> />
            </td>
        </tr>
        <tr>
            <td><?php echo $l('entry_redirect_when_tablet'); ?></td>
            <td>
                <input title="Marque esta casilla si desea redireccionar la tienda hacia otra url cuando detecte acceso desde una Tablet" type="checkbox" name="config_redirect_when_tablet" value="1"<?php if ($config_redirect_when_tablet) { echo ' checked="checked"'; } ?> />
            </td>
        </tr>
        <tr>
            <td><?php echo $l('entry_redirect_when_facebook'); ?></td>
            <td>
                <input title="Marque esta casilla si desea redireccionar la tienda hacia otra url cuando detecte acceso desde un móvil" type="checkbox" name="config_redirect_when_facebook" value="1"<?php if ($config_redirect_when_facebook) { echo ' checked="checked"'; } ?> />
            </td>
        </tr>
        <tr>
            <td><?php echo $l('Render HTML Minified'); ?></td>
            <td>
                <input type="checkbox" name="config_minified_html" value="1"<?php if ($config_minified_html) { echo ' checked="checked"'; } ?> />
            </td>
        </tr>
        <tr>
            <td><?php echo $l('Generate CSS Into Files'); ?></td>
            <td>
                <input type="checkbox" name="config_render_css_in_file" value="1"<?php if ($config_render_css_in_file) { echo ' checked="checked"'; } ?> />
            </td>
        </tr>
        <tr>
            <td><?php echo $l('Generate JS Into Files'); ?></td>
            <td>
                <input type="checkbox" name="config_render_js_in_file" value="1"<?php if ($config_render_js_in_file) { echo ' checked="checked"'; } ?> />
            </td>
        </tr>
        <tr>
            <td><?php echo $l('entry_store_mode'); ?></td>
            <td>
                <select name="config_store_mode">
                    <option value="store"<?php if ($config_store_mode=='store') { echo ' selected="selected"'; } ?>><?php echo $l('text_store'); ?></option>
                    <option value="catalog"<?php if ($config_store_mode=='catalog') { echo ' selected="selected"'; } ?>><?php echo $l('text_catalog'); ?></option>
                    <option value="blog"<?php if ($config_store_mode=='blog') { echo ' selected="selected"'; } ?>><?php echo $l('text_blog'); ?></option>
                    <option value="company_website"<?php if ($config_store_mode=='company_website') { echo ' selected="selected"'; } ?>><?php echo $l('text_company_website'); ?></option>
                </select>
            </td>
          </tr>
        <tr>
            <td><?php echo $l('entry_admin_limit'); ?></td>
            <td><input class="necoAdminItems<?php if (isset($error_admin_limit)) echo ' neco-input-error'; ?>" title="Ingrese la cantidad de items por p&aacute;gina que se mostrar&aacute;n en los diversos listados de la administraci&oacute;n" type="necoNumber" name="config_admin_limit" value="<?php echo $config_admin_limit; ?>" size="3" required="true">
              <?php if ($error_admin_limit) { ?>
              <span class="error"><?php echo $error_admin_limit; ?></span>
              <?php } ?></td>
        </tr>
	<tr>
            <td><?php echo $l('entry_catalog_limit'); ?></td>
            <td><input class="necoShopItems<?php if (isset($error_catalog_limit)) echo ' neco-input-error'; ?>" title="Ingrese la cantidad de items por p&aacute;gina que se mostrar&aacute;n en los diversos listados de la tienda" type="necoNumber" name="config_catalog_limit" value="<?php echo $config_catalog_limit; ?>" size="3" required="true">
              <?php if ($error_catalog_limit) { ?>
              <span class="error"><?php echo $error_catalog_limit; ?></span>
              <?php } ?></td>
          </tr>
		  <tr>
            <td><?php echo $l('entry_new_days'); ?></td>
            <td><input class="necoNewProduct<?php if (isset($error_new_days)) echo ' neco-input-error'; ?>" title="<?php echo $l('help_new_days'); ?>" type="necoNumber" name="config_new_days" value="<?php echo $config_new_days; ?>" size="3" required="true" /></td>
          </tr>
          <tr>
            <td><?php echo $l('entry_tax'); ?></td>
            <td>
              <input class="necoTax" title="Seleccione si desea que los precios de los productos se muestren con el impuesto inclu&iacute;do" type="checkbox" showquick="off" name="config_tax" value="1"<?php if ($config_tax) { ?> checked="checked"<?php } ?>>
              </td>
          </tr>
          </tr>
          <tr>
            <td><?php echo $l('entry_customer_group'); ?><br><span class="help">Grupo de Clientes por defecto.</span></td>
            <td><select class="necoCustomerGroup" name="config_customer_group_id" title="Seleccione el grupo de clientes predeterminado. Todos los clientes nuevos ser&aacute;n registrados con este grupo">
                <?php foreach ($customer_groups as $customer_group) { ?>
                <?php if ($customer_group['customer_group_id'] == $config_customer_group_id) { ?>
                <option value="<?php echo $customer_group['customer_group_id']; ?>" selected="selected"><?php echo $customer_group['name']; ?></option>
                <?php } else { ?>
                <option value="<?php echo $customer_group['customer_group_id']; ?>"><?php echo $customer_group['name']; ?></option>
                <?php } ?>
                <?php } ?>
              </select></td>
          </tr>
          <tr>
            <td><?php echo $l('entry_customer_price'); ?><br><span class="help">S&oacute;lo muestra precios cuando un cliente se loguea.</span></td>
            <td>
              <input class="necoShowPrice" title="Seleccione si desea que se muestren los precios de los productos despu&eacute;s que el cliente haya iniciado sesi&oacute;n" type="checkbox" showquick="off" name="config_customer_price" value="1"<?php if ($config_customer_price) { ?> checked="checked"<?php } ?>></td>
          </tr>
          <tr>
            <td><?php echo $l('entry_customer_approval'); ?><br><span class="help">No permite a un nuevo cliente loguearse hasta que su cuenta sea aprobada.</span></td>
            <td>
              <input class="necoNewCustomer" title="Seleccione si desea que los clientes nuevos sean activados inmediatamente despu&eacute;s de registrarse. Si elige no, el sistema enviar&aacute; un email de verificaci&oacute;n a la direcci&oacute;n especificada por el cliente para confirmar su auntenticidad. Le recomendamos que seleccione la opci&oacute;n No" type="checkbox" showquick="off" name="config_customer_approval" value="1"<?php if ($config_customer_approval) { ?> checked="checked"<?php } ?>></td>
          </tr>
          <tr>
            <td><?php echo $l('entry_account'); ?><br><span class="help">Forza a los clientes a estar de acuerdo con los t&eacute;rminos antes de que la cuenta sea creada.</span></td>
            <td><select class="necoAccountTerms" name="config_account_id" title="Seleccione los t&eacute;rminos legales que el cliente debe aceptar para completar el registro">
                <option value="0"><?php echo $l('text_none'); ?></option>
                <?php foreach ($pages as $page) { ?>
                <option value="<?php echo $page['post_id']; ?>"<?php if ($page['post_id'] == $config_account_id) { ?> selected="selected"<?php } ?>><?php echo $page['title']; ?></option>
                <?php } ?>
              </select></td>
          </tr>
          <tr>
            <td><?php echo $l('entry_checkout'); ?><br><span class="help">Forza a los clientes a estar de acuerdo con los t&eacute;rminos antes de realizar una compra.</span></td>
            <td><select class="necoShoppingTerms" name="config_checkout_id" title="Seleccione los t&eacute;rminos legales que el cliente debe aceptar para completar una compra">
                <option value="0"><?php echo $l('text_none'); ?></option>
                <?php foreach ($pages as $page) { ?>
                <option value="<?php echo $page['post_id']; ?>"<?php if ($page['post_id'] == $config_checkout_id) { ?> selected="selected"<?php } ?>><?php echo $page['title']; ?></option>
                <?php } ?>
              </select></td>
          </tr>
          <tr>
            <td><?php echo $l('entry_stock_display'); ?><br><span class="help">Muestra el stock disponible del producto.</span></td>
            <td>
          <input class="necoShowStock" title="Seleccione si desea mostrar al cliente el stock disponible de cada producto" type="checkbox" showquick="off" name="config_stock_display" value="1"<?php if ($config_stock_display) { ?> checked="checked"<?php } ?>></td>
          </tr>
          <tr>
            <td><?php echo $l('entry_stock_checkout'); ?><br><span class="help">Permite a los clientes comprar a&uacute;n cuando el producto est&aacute; agotado.</span></td>
            <td>
              <input class="necoCheckStock" title="Seleccione si desea que el cliente pueda comprar, a&uacute;n cuando el producto est&eacute; agotado" type="checkbox" showquick="off" name="config_stock_checkout" value="1"<?php if ($config_stock_checkout) { ?> checked="checked"<?php } ?>></td>
          </tr>
          <tr>
            <td><?php echo $l('entry_order_status'); ?><br><span class="help">Establecer el estado predeterminado cuando un pedido es realizada.</span></td>
            <td><select class="necoOrderStatus" name="config_order_status_id" title="Seleccione el estado predeterminado de los pedidos">
                <?php foreach ($order_statuses as $order_status) { ?>
                <option value="<?php echo $order_status['order_status_id']; ?>"<?php if ($order_status['order_status_id'] == $config_order_status_id) { ?> selected="selected"<?php } ?>><?php echo $order_status['name']; ?></option>
                <?php } ?>
              </select></td>
          </tr>
          <tr>
            <td><?php echo $l('entry_stock_status'); ?></td>
            <td><select class="necoStockStatus" name="config_stock_status_id" title="Seleccione el estado predeterminado del stock">
                <?php foreach ($stock_statuses as $stock_status) { ?>
                <option value="<?php echo $stock_status['stock_status_id']; ?>"<?php if ($stock_status['stock_status_id'] == $config_stock_status_id) { ?> selected="selected"<?php } ?>><?php echo $stock_status['name']; ?></option>
                <?php } ?>
              </select></td>
          </tr>
		  <tr>
            <td><?php echo $l('entry_review'); ?></td>
            <td>
              <input class="necoAllowComments" title="Seleccione si desea que los clientes hagan comentarios sobre sus productos y servicios. Le recomendamos que elija la opci&oacute;n Si, ya que con esa informaci&oacute;n puede realizar estudios y an&aacute;lisis de mercados" type="checkbox" showquick="off" name="config_review" value="1"<?php if ($config_review) { ?> checked="checked"<?php } ?>></td>
          </tr>
		  <tr>
            <td><?php echo $l('entry_review_approve'); ?></td>
            <td>
              <input class="necoAproveComments" title="Indique si desea aprobar automaticamente todos los comentarios y preguntas realizadas por los clientes" type="checkbox" showquick="off" name="config_review_approve" value="1"<?php if ($config_review_approve) { ?> checked="checked"<?php } ?>></td>
          </tr>
          <tr>
            <td><?php echo $l('entry_download'); ?></td>
            <td>
              <input class="necoAllowDownloads" title="Seleccione si desea que los clientes puedan descargar productos. Solo aplica para productos descargables a trav&eacute;s de la tienda (p. ej. Softwares)" type="checkbox" showquick="off" name="config_download" value="1"<?php if ($config_download) { ?> checked="checked"<?php } ?>></td>
          </tr>
          <tr>
            <td><?php echo $l('entry_download_status'); ?><br><span class="help">El cliente no puede descargar productos antes de cancelar el precio del pedido.</span></td>
            <td><select class="necoDownloadStatus" name="config_download_status" title="Seleccione el estado predeterminado de las descargas. Las decargas solo estar&aacute;n disponibles cuando un pedido tenga el estado aqu&iacute; establecido">
                <?php foreach ($order_statuses as $order_status) { ?>
                <option value="<?php echo $order_status['order_status_id']; ?>"<?php if ($order_status['order_status_id'] == $config_download_status) { ?> selected="selected"<?php } ?>><?php echo $order_status['name']; ?></option>
                <?php } ?>
              </select></td>
          </tr>
		  <tr>
            <td><?php echo $l('entry_cart_weight'); ?></td>
            <td>
              <input class="necoCartWeight" title="Seleccione si desea que se muestre el peso total del pedido. Esto es &uacute;til para indicarle al cliente cu&aacute;l es el peso estimado de toda la compra. Le recomendamos que elija la opci&oacute;n Si" type="checkbox" showquick="off" name="config_cart_weight" value="1"<?php if ($config_cart_weight) { ?> checked="checked"<?php } ?>>
              </td>
          </tr>
		  <tr>
            <td><?php echo $l('entry_shipping_session'); ?></td>
            <td>
              <input class="necoCartWeightCost" title="Seleccione si desea que se muestre un estimado del costo por env&iacute; de las mercanc&iacute;s del pedido. Le recomendamos que elija la opci&oacute;n No" type="checkbox" showquick="off" name="config_shipping_session" value="1"<?php if ($config_shipping_session) { ?> checked="checked"<?php } ?>>
              </td>
          </tr>
    </table>
</div>