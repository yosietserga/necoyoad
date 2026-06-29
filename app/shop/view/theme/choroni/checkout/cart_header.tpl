<?php echo $header; ?>

<div class="check-out-header row">
    <section id="maincontent">
        <section id="content" >

            <div class="columns">
                <div id="featuredContent">
                <ul class="widgets"><?php if($featuredWidgets) { foreach ($featuredWidgets as $widget) { ?>{%<?php echo $widget; ?>%}<?php } } ?></ul>
                </div>
            </div>

            <div class="neco-wizard columns">
                <ul class="neco-wizard-controls">
                    <li><?php echo $l('text_basket'); ?>
                        <span><?php echo $l('text_step_cart'); ?></span>
                    </li>
                     <?php if (!$isLogged) { ?>
                    <li><?php echo $l('text_billing'); ?>
                        <span><?php echo $l('text_step_billing'); ?></span>
                    </li>
                    <?php } ?>
                    <?php if ($shipping_methods || (!$isLogged || ($isLogged && !$shipping_country_id))) { ?>
                    <li><?php echo $l('text_shipping'); ?>
                        <span><?php echo $l('text_step_shipping'); ?></span>
                    </li>
                    <?php } ?>
                    <li><?php echo $l('text_confirm'); ?>
                        <span><?php echo $l('text_step_confirm'); ?></span>
                    </li>
                    <li><?php echo $l('text_complete'); ?>
                        <span><?php echo $l('text_step_success'); ?></span>
                    </li>
                </ul>


                <?php if (!empty($message)) { ?><div class="message warning"><?php echo $message; ?></div><?php } ?>


                <form action="<?php echo str_replace('&', '&amp;', $action); ?>" method="post" id="orderForm">
                <div class="neco-wizard-steps">
                    <div>
                        <h1><?php echo $heading_title; ?><?php if ($weight) { ?>&nbsp;(<span id="weight"><?php echo $weight; ?></span>)<?php } ?></h1>
                            <table class="cart">
                            <thead>
                                <tr>
                                    <th>&nbsp;</th>
                                    <th><?php echo $l('column_image'); ?></th>
                                    <th><?php echo $l('column_name'); ?></th>
                                    <th><?php echo $l('column_model'); ?></th>
                                    <th><?php echo $l('column_quantity'); ?></th>
                                            <?php if ($display_price && $Config->get('config_store_mode')=='store') { ?>
                                    <th><?php echo $l('column_price'); ?></th>
                                    <th><?php echo $l('column_total'); ?></th>
                            <?php } ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($products as $product) { ?>
                                <tr>
                                    <td><a class="delete-product" onclick="deleteCart(this,'<?php echo $product['key']; ?>')" title="<?php echo $l('text_delete'); ?>"></a></td>
                                    <td><a title="<?php echo $product['name']; ?>" href="<?php echo str_replace('&', '&amp;', $product['href']); ?>"><img src="<?php echo $product['thumb']; ?>" alt="<?php echo $product['name']; ?>" /></a></td>
                                    <td>
                                        <a title="<?php echo $product['name']; ?>" href="<?php echo str_replace('&', '&amp;', $product['href']); ?>"><?php echo $product['name']; ?></a>
                                        <?php if (!$product['stock']) { ?><span style="color: #FF0000; font-weight: bold;">***</span><?php } ?>
                                        <div><?php foreach ($product['option'] as $option) { ?>- <small><?php echo $option['name']; ?> <?php echo $option['value']; ?></small><br /><?php } ?></div>
                                    </td>
                                    <td><?php echo $product['model']; ?></td>
                                    <td>
                                        <input type="text" name="quantity[<?php echo $product['key']; ?>]" value="<?php echo $product['quantity']; ?>" showquick="off" size="3" style="float:left;width:30px;" onchange="refreshCart(this,'<?php echo $product['key']; ?>')" />
                                        <a class="update-product" onclick="refreshCart(this,'<?php echo $product['key']; ?>')" title="<?php echo $l('text_update'); ?>"></a>
                                    </td>
                                            <?php if ($display_price && $Config->get('config_store_mode')=='store') { ?>
                                    <td><?php echo $product['price']; ?></td>
                                    <td><?php echo $product['total']; ?></td>
                            <?php } ?>
                                </tr>
                                <?php } ?>
                            </tbody>
                            </table>

                                            <?php if ($display_price && $Config->get('config_store_mode')=='store') { ?>
                            <table id="totals">
                            <?php foreach ($totals as $total) { ?>
                                <tr>
                                    <td><b><?php echo $total['title']; ?></b></td>
                                    <td><?php echo $total['text']; ?></td>
                                </tr>
                            <?php } ?>
                            </table>
                            <?php } ?>

                    </div>

                    <?php if (!$isLogged) { ?>
                    <div>
                        <div class="recipe">
                            <fieldset>
                                <legend>Datos de Facturaci&oacute;n</legend>
                                <?php if ($isLogged) { ?><a href="index.php?r=account/account" title="<?php echo $l('text_update'); ?>"></a><?php } ?>
                                <div class="property">
                                    <label for="email"><?php echo $l('text_email'); ?>:</label>
                                    <input type="email" name="email" id="email" value="<?php echo isset($email) ? $email : ''; ?>" required="required" title="<?php echo $l('help_email'); ?>" style="width: 220px;" <?php if ($isLogged) echo 'disabled="disabled"'; ?> />
                                </div>

                                <div class="property">
                                    <label for="firstname"><?php echo $l('text_firstname'); ?>:</label>
                                    <input type="text" id="firstname" name="firstname" required="required" value="<?php echo isset($firstname) ? $firstname : ''; ?>" style="width: 220px;" <?php if ($isLogged) echo 'disabled="disabled"'; ?> />
                                </div>

                                <div class="property">
                                    <label for="lastname"><?php echo $l('text_lastname'); ?>:</label>
                                    <input type="text" id="lastname" name="lastname" required="required" value="<?php echo isset($lastname) ? $lastname : ''; ?>" style="width: 220px;" <?php if ($isLogged) echo 'disabled="disabled"'; ?> />
                                </div>

                                <div class="property">
                                    <label for="company"><?php echo $l('text_company'); ?>:</label>
                                    <input type="text" id="company" name="company" required="required" value="<?php echo isset($company) ? $company : ''; ?>" style="width: 220px;" <?php if ($isLogged) echo 'disabled="disabled"'; ?> />
                                </div>

                                <div class="property">
                                    <label for="rif"><?php echo $l('text_rif'); ?>:</label>
                                    <select name="riftype" title="<?php echo $l('help_riftype'); ?>">
                                        <option value="V" <?php if (strtolower($rif_type) == 'v') echo 'selected="selected"'; ?>>V</option>
                                        <option value="J" <?php if (strtolower($rif_type) == 'j') echo 'selected="selected"'; ?>>J</option>
                                        <option value="E" <?php if (strtolower($rif_type) == 'e') echo 'selected="selected"'; ?>>E</option>
                                        <option value="G" <?php if (strtolower($rif_type) == 'g') echo 'selected="selected"'; ?>>G</option>
                                    </select>
                                    <input type="text" id="rif" name="rif" value="<?php echo isset($rif) ? $rif : ''; ?>" required="required" maxlength="10" title="<?php echo $l('help_rif'); ?>" quicktip="Ingresa tu n�mero de c�dula si eres una persona natural y no posees RIF. Ingresa solo n�meros" <?php if ($isLogged) echo 'disabled="disabled"'; ?> />
                                </div>

                                <div class="property">
                                    <label for="telephone">T&eacute;lefono:</label>
                                    <input type="text" id="telephone" name="telephone" required="required" value="<?php echo isset($telephone) ? $telephone : ''; ?>" style="width: 220px;" <?php if ($isLogged) echo 'disabled="disabled"'; ?> />
                                </div>

                                <div class="property"<?php if ($isLogged) echo ' style="display:hidden"'; ?>>
                                    <label for="referencedBy"><?php echo $l('entry_referencedBy'); ?></label>
                                    <input type="text" id="referencedBy" name="referencedBy" value="<?php echo isset($referencedBy) ? $referencedBy : ''; ?>" style="width: 220px;" />
                                </div>

                                <div class="property">
                                    <label for="payment_country_id"><?php echo $l('entry_country'); ?></label>
                                    <select name="payment_country_id" id="payment_country_id" title="<?php echo $l('help_country'); ?>" onchange="$('select[name=\'payment_zone_id\']').load('index.php?r=account/register/zone&country_id=' + this.value + '&zone_id=<?php echo $payment_zone_id; ?>');">
                                        <option value="false">-- Por Favor Seleccione --</option>
                                        <?php foreach ($countries as $country) { ?>
                                            <?php if ($country['country_id'] == $payment_country_id) { ?>
                                        <option value="<?php echo $country['country_id']; ?>" selected="selected"><?php echo $country['name']; ?></option>
                                            <?php } else { ?>
                                        <option value="<?php echo $country['country_id']; ?>"><?php echo $country['name']; ?></option>
                                            <?php } ?>
                                        <?php } ?>
                                    </select>
                                </div>

                                <div class="property">
                                    <label for="payment_zone_id"><?php echo $l('entry_zone'); ?></label>
                                    <select name="payment_zone_id" id="payment_zone_id" title="<?php echo $l('help_zone'); ?>">
                                        <option value="false">-- Seleccione un pa&iacute;s --</option>
                                    </select>
                                </div>

                                <div class="property">
                                    <label for="payment_city"><?php echo $l('entry_city'); ?></label>
                                    <input type="text" id="payment_city" name="payment_city" value="<?php echo $payment_city; ?>" required="required" title="<?php echo $l('help_city'); ?>" />
                                </div>

                                <div class="property">
                                    <label for="payment_street"><?php echo $l('entry_street'); ?></label>
                                    <input type="text" id="payment_street" name="payment_street" value="<?php echo $payment_street; ?>" required="required" title="<?php echo $l('help_street'); ?>" />
                                </div>

                                <div class="property">
                                    <label for="payment_postcode"><?php echo $l('entry_postcode'); ?></label>
                                    <input type="necoNumber" id="payment_postcode" name="payment_postcode" value="<?php echo $payment_postcode; ?>" required="required" title="<?php echo $l('help_postcode'); ?>" />
                                </div>

                                <div class="property">
                                    <label for="payment_address_1"><?php echo $l('entry_address_1'); ?></label>
                                    <input type="text" id="payment_address_1" name="payment_address_1" value="<?php echo $payment_address_1; ?>" required="required" title="<?php echo $l('help_address'); ?>" />
                                </div>
                            </fieldset>
                            <p>Al continuar con el proceso de compra, usted est&aacute; aceptando los <a href="<?php echo $Url::createUrl('content/page',array('page_id'=>$Config->get('config_checkout_id'))); ?>">t&eacute;rminos legales y las condiciones de uso</a> de este sitio web.</p>

                        </div>
                    </div>
                    <?php } ?>

                    <!-- begin shipping section -->
                    <?php if ($shipping_methods || (!$isLogged || ($isLogged && !$shipping_country_id))) { ?>
                    <div>
                        <div class="address">
                            <?php if (!$isLogged || ($isLogged && !$shipping_country_id)) { ?>
                            <fieldset>
                                <legend>Direcci&oacute;n de Entrega</legend>

                                <div class="property">
                                    <label for="shipping_country_id"><?php echo $l('entry_country'); ?></label>
                                    <select name="shipping_country_id" id="shipping_country_id" title="<?php echo $l('help_country'); ?>" onchange="$('select[name=\'shipping_zone_id\']').load('index.php?r=account/register/zone&country_id=' + this.value + '&zone_id=<?php echo $zone_id; ?>');">
                                        <option value="false">-- Por Favor Seleccione --</option>
                                        <?php foreach ($countries as $country) { ?>
                                            <?php if ($country['country_id'] == $shipping_country_id) { ?>
                                        <option value="<?php echo $country['country_id']; ?>" selected="selected"><?php echo $country['name']; ?></option>
                                            <?php } else { ?>
                                        <option value="<?php echo $country['country_id']; ?>"><?php echo $country['name']; ?></option>
                                            <?php } ?>
                                        <?php } ?>
                                    </select>
                                </div>

                                <div class="property">
                                    <label for="shipping_zone_id"><?php echo $l('entry_zone'); ?></label>
                                    <select name="shipping_zone_id" id="shipping_zone_id" title="<?php echo $l('help_zone'); ?>">
                                        <option value="false">-- Seleccione un pa&iacute;s --</option>
                                    </select>
                                </div>

                                <div class="property">
                                    <label for="shipping_city"><?php echo $l('entry_city'); ?></label>
                                    <input type="text" id="shipping_city" name="shipping_city" value="<?php echo $shipping_city; ?>" required="required" title="<?php echo $l('help_city'); ?>" />
                                </div>

                                <div class="property">
                                    <label for="shipping_street"><?php echo $l('entry_street'); ?></label>
                                    <input type="text" id="shipping_street" name="shipping_street" value="<?php echo $shipping_street; ?>" required="required" title="<?php echo $l('help_street'); ?>" />
                                </div>

                                <div class="property">
                                    <label for="shipping_postcode"><?php echo $l('entry_postcode'); ?></label>
                                    <input type="text" id="shipping_postcode" name="shipping_postcode" value="<?php echo $shipping_postcode; ?>" required="required" title="<?php echo $l('help_postcode'); ?>" />
                                </div>

                                <div class="property">
                                    <label for="shipping_address_1"><?php echo $l('entry_address_1'); ?></label>
                                    <input type="text" id="shipping_address_1" name="shipping_address_1" value="<?php echo $shipping_address_1; ?>" required="required" title="<?php echo $l('help_address'); ?>" />
                                </div>

                                <input type="hidden" name="payment_country_id" id="payment_country_id" value="<?php echo $payment_country_id; ?>" />
                                <input type="hidden" name="payment_zone_id" id="payment_zone_id" value="<?php echo $payment_zone_id; ?>" />
                                <input type="hidden" name="payment_street" id="payment_street" value="<?php echo $payment_street; ?>" />
                                <input type="hidden" name="payment_city" id="payment_city" value="<?php echo $payment_city; ?>" />
                                <input type="hidden" name="payment_postcode" id="payment_postcode" value="<?php echo $payment_postcode; ?>" />
                                <input type="hidden" name="payment_address_1" id="payment_address_1" value="<?php echo $payment_address_1; ?>" />
                            </fieldset>
                            <?php } else { ?>
                                <input type="hidden" name="payment_country_id" id="payment_country_id" value="<?php echo $payment_country_id; ?>" />
                                <input type="hidden" name="payment_zone_id" id="payment_zone_id" value="<?php echo $payment_zone_id; ?>" />
                                <input type="hidden" name="payment_city" id="payment_city" value="<?php echo $payment_city; ?>" />
                                <input type="hidden" name="payment_street" id="payment_street" value="<?php echo $payment_street; ?>" />
                                <input type="hidden" name="payment_postcode" id="payment_postcode" value="<?php echo $payment_postcode; ?>" />
                                <input type="hidden" name="payment_address_1" id="payment_address_1" value="<?php echo $payment_address_1; ?>" />

                                <input type="hidden" name="shipping_country_id" id="shipping_country_id" value="<?php echo $shipping_country_id; ?>" />
                                <input type="hidden" name="shipping_zone_id" id="shipping_zone_id" value="<?php echo $shipping_zone_id; ?>" />
                                <input type="hidden" name="shipping_city" id="shipping_city" value="<?php echo $shipping_city; ?>" />
                                <input type="hidden" name="shipping_street" id="shipping_street" value="<?php echo $shipping_street; ?>" />
                                <input type="hidden" name="shipping_postcode" id="shipping_postcode" value="<?php echo $shipping_postcode; ?>" />
                                <input type="hidden" name="shipping_address_1" id="shipping_address_1" value="<?php echo $shipping_address_1; ?>" />
                            <?php } ?>

                            <?php if ($shipping_methods) { ?>
                            <fieldset>
                                <legend><?php echo $l('text_shipping_methods'); ?></legend>

                                <table>
                                    <thead>
                                        <tr>
                                            <th>Seleccionar</th>
                                            <th>M&eacute;todo de Env�o</th>
                                            <th>Precio</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach ($shipping_methods as $shipping_method) { ?>
                                        <?php foreach ($shipping_method['quote'] as $quote) { ?>
                                        <tr>
                                            <td><input type="radio" name="shipping_method" value="<?php echo $quote['id']; ?>" showquick="off" /></td>
                                            <td><b><?php echo $quote['title']; ?></b></td>
                                            <td><b style="font: bold 18px arial;"><?php echo $quote['text']; ?></b></td>
                                        </tr>
                                        <?php } ?>
                                    <?php } ?>
                                    </tbody>
                                </table>

                            </fieldset>
                            <?php } ?>
                        </div>
                    </div>
                    <?php } ?>
                    <!-- end shipping section -->

                    <!-- begin payment section -->
                    <div>
                        <div class="confirm">
                        <h1><?php echo $l('text_order_confirm'); ?></h1>
                            <h2>Datos de Facturaci&oacute;n</h2>
                            <table class="confirmOrder">
                                <tr>
                                    <td><?php echo $l('text_company'); ?>:</td>
                                    <td id="confirmCompany"><?php echo $company; ?></td>
                                </tr>
                                <tr>
                                    <td><?php echo $l('text_rif'); ?>:</td>
                                    <td id="confirmRif"><?php echo $riff; ?></td>
                                </tr>
                                <tr>
                                    <td><?php echo $l('text_address'); ?>:</td>
                                    <td id="confirmPaymentAddress"><?php echo $payment_address; ?></td>
                                </tr>
                            </table>
                            <h2><?php echo $l('text_shipping_address_and_method'); ?></h2>
                            <table class="confirmOrder">
                                <?php if ($shipping_methods) { ?>
                                <tr>
                                    <td>M&eacute;todo de Env&iacute;o:</td>
                                    <td id="shipping_method"></td>
                                </tr>
                                <?php } ?>
                                <tr>
                                    <td>Direcci&oacute;n:</td>
                                    <td id="confirmShippingAddress"><?php echo $shipping_address; ?></td>
                                </tr>
                            </table>

                            <table class="cart">
                                <thead>
                                    <tr>
                                        <th>Descripci&oacute;n</th>
                                        <th>Modelo</th>
                                        <th>Precio Unit.</th>
                                        <th>Cant.</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($products as $product) { ?>
                                    <tr id="confirmItem<?php echo $product['product_id']; ?>">
                                        <td>
                                            <?php echo $product['name']; ?>
                                            <div><?php foreach ($product['option'] as $option) { ?>- <small><?php echo $option['name']; ?> <?php echo $option['value']; ?></small><br /><?php } ?></div>
                                        </td>
                                        <td><?php echo $product['model']; ?></td>
                                        <td id="confirmQty<?php echo $product['product_id']; ?>"><?php echo $product['quantity']; ?></td>
                                        <td><?php echo $product['price']; ?></td>
                                        <td id="confirmTotal<?php echo $product['product_id']; ?>"><?php echo $product['total']; ?></td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>

                            <table id="totalsConfirm">
                            <?php foreach ($totals as $total) { ?>
                                <tr>
                                    <td><?php echo $total['title']; ?></td>
                                    <td><?php echo $total['text']; ?></td>
                                </tr>
                            <?php } ?>
                            </table>


                            <textarea name="comment" placeholder="Ingresa tus comentarios sobre el pedido aqu&iacute;"></textarea>

                        </div>
                    </div>
                    <!-- end payment section -->
                    <div>
                        <div style="width:300px;margin:20% auto;text-align: center;"><img src="<?php echo HTTP_IMAGE; ?>load.gif" alt="Cargando..." /></div>
                    </div>
                </form>
            </div>

            <div class="grid_12">
                    <?php if($widgets) { ?><ul class="widgets"><?php foreach ($widgets as $widget) { ?>{%<?php echo $widget; ?>%}<?php } ?></ul><?php } ?>
            </div>
        </section>
    </section>
</div>
<?php echo $footer; ?> 