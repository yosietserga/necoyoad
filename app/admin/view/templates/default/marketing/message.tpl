<?php echo $header; ?>
<?php echo $navigation; ?>
<div class="container">
    
    <?php if (isset($breadcrumbs) && is_array($breadcrumbs)) { ?>
    <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
    </ul>
    <?php } ?>
    
    <?php if (isset($success) && $success) { ?><div class="grid_12"><div class="message success"><?php echo $success; ?></div></div><?php } ?>
    <?php if ((isset($msg) && $msg) || (isset($error_warning) && $error_warning)) { ?><div class="grid_12"><div class="message warning"><?php echo $msg ?? $error_warning; ?></div></div><?php } ?>
    <?php if (isset($error) && $error) { ?><div class="grid_12"><div class="message error"><?php echo $error; ?></div></div><?php } ?>
    <div class="grid_12" id="msg"></div>
    
    <div class="box">
        <h1><?php echo $l('heading_title'); ?></h1>
        <div class="buttons">
            <a onclick="$('#form').submit();" class="button"><?php echo $l('button_save'); ?></a>
        </div>

        <div class="clear"></div>

        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
            <h2>Mensajes de Pedidos y Carrito de Compra</h2>
            <table class="form">
                <tr>
                    <td><?php echo $l('entry_page_new_payment'); ?></td>
                    <td>
                        <select name="marketing_mailserver_new_payment">
                            <option value="0"<?php if (!$marketing_mailserver_new_payment) echo ' selected="selected"'; ?>>Servidor Local</option>
                            <?php foreach($mail_servers as $id => $server) { ?>
                            <option value="<?php echo $id; ?>"<?php if ($marketing_mailserver_new_payment === $id) echo ' selected="selected"'; ?>>
                            <?php echo $server['server'] .' &lt;'. $server['username'] .'&gt;'; ?>
                            </option>
                            <?php } ?>
                        </select>
                    </td>
                    <td>
                        <select name="marketing_email_new_payment" title="<?php echo $l('help_page_new_payment'); ?>">
                            <option value="0"><?php echo $l('text_none'); ?></option>
                            <?php foreach ($newsletters as $newsletter) { ?>
                            <option value="<?php echo $newsletter['newsletter_id']; ?>"<?php if ($newsletter['newsletter_id'] == $marketing_email_new_payment) { ?> selected="selected"<?php } ?>><?php echo $newsletter['name']; ?></option>
                            <?php } ?>
                      </select>
                    </td>
                </tr>
                <tr>
                    <td><?php echo $l('entry_page_new_order'); ?></td>
                    <td>
                        <select name="marketing_mailserver_new_order">
                            <option value="0"<?php if (!$marketing_mailserver_new_order) echo ' selected="selected"'; ?>>Servidor Local</option>
                            <?php foreach($mail_servers as $id => $server) { ?>
                            <option value="<?php echo $id; ?>"<?php if ($marketing_mailserver_new_order === $id) echo ' selected="selected"'; ?>>
                            <?php echo $server['server'] .' &lt;'. $server['username'] .'&gt;'; ?>
                            </option>
                            <?php } ?>
                        </select>
                    </td>
                    <td>
                        <select name="marketing_email_new_order" title="<?php echo $l('help_page_new_order'); ?>">
                            <option value="0"><?php echo $l('text_none'); ?></option>
                            <?php foreach ($newsletters as $newsletter) { ?>
                            <option value="<?php echo $newsletter['newsletter_id']; ?>"<?php if ($newsletter['newsletter_id'] == $marketing_email_new_order) { ?> selected="selected"<?php } ?>><?php echo $newsletter['name']; ?></option>
                            <?php } ?>
                      </select>
                    </td>
                </tr>
                <tr>
                    <td><?php echo $l('entry_page_order_pdf'); ?></td>
                    <td>
                        <select name="marketing_mailserver_order_pdf">
                            <option value="0"<?php if (!$marketing_mailserver_order_pdf) echo ' selected="selected"'; ?>>Servidor Local</option>
                            <?php foreach($mail_servers as $id => $server) { ?>
                            <option value="<?php echo $id; ?>"<?php if ($marketing_mailserver_order_pdf === $id) echo ' selected="selected"'; ?>>
                            <?php echo $server['server'] .' &lt;'. $server['username'] .'&gt;'; ?>
                            </option>
                            <?php } ?>
                        </select>
                    </td>
                    <td>
                        <select name="marketing_email_order_pdf" title="<?php echo $l('help_page_order_pdf'); ?>">
                            <option value="0"><?php echo $l('text_none'); ?></option>
                            <?php foreach ($newsletters as $newsletter) { ?>
                            <option value="<?php echo $newsletter['newsletter_id']; ?>"<?php if ($newsletter['newsletter_id'] == $marketing_email_order_pdf) { ?> selected="selected"<?php } ?>><?php echo $newsletter['name']; ?></option>
                            <?php } ?>
                      </select>
                    </td>
                </tr>
                <tr>
                    <td><?php echo $l('entry_page_update_order'); ?></td>
                    <td>
                        <select name="marketing_mailserver_update_order">
                            <option value="0"<?php if (!$marketing_mailserver_update_order) echo ' selected="selected"'; ?>>Servidor Local</option>
                            <?php foreach($mail_servers as $id => $server) { ?>
                            <option value="<?php echo $id; ?>"<?php if ($marketing_mailserver_update_order === $id) echo ' selected="selected"'; ?>>
                            <?php echo $server['server'] .' &lt;'. $server['username'] .'&gt;'; ?>
                            </option>
                            <?php } ?>
                        </select>
                    </td>
                    <td>
                        <select name="marketing_email_update_order" title="<?php echo $l('help_page_update_order'); ?>">
                            <option value="0"><?php echo $l('text_none'); ?></option>
                            <?php foreach ($newsletters as $newsletter) { ?>
                            <option value="<?php echo $newsletter['newsletter_id']; ?>"<?php if ($newsletter['newsletter_id'] == $marketing_email_update_order) { ?> selected="selected"<?php } ?>><?php echo $newsletter['name']; ?></option>
                            <?php } ?>
                      </select>
                    </td>
                </tr>
                <tr>
                    <td><?php echo $l('entry_page_old_order'); ?></td>
                    <td>
                        <select name="marketing_mailserver_old_order">
                            <option value="0"<?php if (!$marketing_mailserver_old_order) echo ' selected="selected"'; ?>>Servidor Local</option>
                            <?php foreach($mail_servers as $id => $server) { ?>
                            <option value="<?php echo $id; ?>"<?php if ($marketing_mailserver_old_order === $id) echo ' selected="selected"'; ?>>
                            <?php echo $server['server'] .' &lt;'. $server['username'] .'&gt;'; ?>
                            </option>
                            <?php } ?>
                        </select>
                    </td>
                    <td>
                        <select name="marketing_email_old_order" title="<?php echo $l('help_page_old_order'); ?>">
                            <option value="0"><?php echo $l('text_none'); ?></option>
                            <?php foreach ($newsletters as $newsletter) { ?>
                            <option value="<?php echo $newsletter['newsletter_id']; ?>"<?php if ($newsletter['newsletter_id'] == $marketing_email_old_order) { ?> selected="selected"<?php } ?>><?php echo $newsletter['name']; ?></option>
                            <?php } ?>
                      </select>
                    </td>
                </tr>
            </table>

            <h2>Mensajes de Clientes</h2>
            <table class="form">
                <tr>
                    <td><?php echo $l('entry_email_new_customer'); ?></td>
                    <td>
                        <select name="marketing_mailserver_new_customer">
                            <option value="0"<?php if (!$marketing_mailserver_new_customer) echo ' selected="selected"'; ?>>Servidor Local</option>
                            <?php foreach($mail_servers as $id => $server) { ?>
                            <option value="<?php echo $id; ?>"<?php if ($marketing_mailserver_new_customer === $id) echo ' selected="selected"'; ?>>
                            <?php echo $server['server'] .' &lt;'. $server['username'] .'&gt;'; ?>
                            </option>
                            <?php } ?>
                        </select>
                    </td>
                    <td>
                        <select name="marketing_email_new_customer" title="<?php echo $l('help_email_new_customer'); ?>">
                            <option value="0"><?php echo $l('text_none'); ?></option>
                            <?php foreach ($newsletters as $newsletter) { ?>
                            <option value="<?php echo $newsletter['newsletter_id']; ?>"<?php if ($newsletter['newsletter_id'] == $marketing_email_new_customer) { ?> selected="selected"<?php } ?>><?php echo $newsletter['name']; ?></option>
                            <?php } ?>
                      </select>
                    </td>
                </tr>
                <tr>
                    <td><?php echo $l('New Customer From Scoial Netowork'); ?></td>
                    <td>
                        <select name="marketing_mailserver_send_password_and_welcome">
                            <option value="0"<?php if (!$marketing_mailserver_send_password_and_welcome) echo ' selected="selected"'; ?>>Servidor Local</option>
                            <?php foreach($mail_servers as $id => $server) { ?>
                            <option value="<?php echo $id; ?>"<?php if ($marketing_mailserver_send_password_and_welcome === $id) echo ' selected="selected"'; ?>>
                            <?php echo $server['server'] .' &lt;'. $server['username'] .'&gt;'; ?>
                            </option>
                            <?php } ?>
                        </select>
                    </td>
                    <td>
                        <select name="marketing_email_send_password_and_welcome">
                            <option value="0"><?php echo $l('text_none'); ?></option>
                            <?php foreach ($newsletters as $newsletter) { ?>
                            <option value="<?php echo $newsletter['newsletter_id']; ?>"<?php if ($newsletter['newsletter_id'] == $marketing_email_send_password_and_welcome) { ?> selected="selected"<?php } ?>><?php echo $newsletter['name']; ?></option>
                            <?php } ?>
                      </select>
                    </td>
                </tr>
                <tr>
                    <td><?php echo $l('entry_email_activate_customer'); ?></td>
                    <td>
                        <select name="marketing_mailserver_activate_customer">
                            <option value="0"<?php if (!$marketing_mailserver_activate_customer) echo ' selected="selected"'; ?>>Servidor Local</option>
                            <?php foreach($mail_servers as $id => $server) { ?>
                            <option value="<?php echo $id; ?>"<?php if ($marketing_mailserver_activate_customer === $id) echo ' selected="selected"'; ?>>
                            <?php echo $server['server'] .' &lt;'. $server['username'] .'&gt;'; ?>
                            </option>
                            <?php } ?>
                        </select>
                    </td>
                    <td>
                        <select name="marketing_email_activate_customer" title="<?php echo $l('help_email_activate_customer'); ?>">
                            <option value="0"><?php echo $l('text_none'); ?></option>
                            <?php foreach ($newsletters as $newsletter) { ?>
                            <option value="<?php echo $newsletter['newsletter_id']; ?>"<?php if ($newsletter['newsletter_id'] == $marketing_email_activate_customer) { ?> selected="selected"<?php } ?>><?php echo $newsletter['name']; ?></option>
                            <?php } ?>
                      </select>
                    </td>
                </tr>
                <tr>
                    <td><?php echo $l('Password Recover'); ?></td>
                    <td>
                        <select name="marketing_mailserver_new_password">
                            <option value="0"<?php if (!$marketing_mailserver_new_password) echo ' selected="selected"'; ?>>Servidor Local</option>
                            <?php foreach($mail_servers as $id => $server) { ?>
                            <option value="<?php echo $id; ?>"<?php if ($marketing_mailserver_new_password === $id) echo ' selected="selected"'; ?>>
                            <?php echo $server['server'] .' &lt;'. $server['username'] .'&gt;'; ?>
                            </option>
                            <?php } ?>
                        </select>
                    </td>
                    <td>
                        <select name="marketing_email_new_password" title="<?php echo $l('help_email_activate_customer'); ?>">
                            <option value="0"><?php echo $l('text_none'); ?></option>
                            <?php foreach ($newsletters as $newsletter) { ?>
                            <option value="<?php echo $newsletter['newsletter_id']; ?>"<?php if ($newsletter['newsletter_id'] == $marketing_email_new_password) { ?> selected="selected"<?php } ?>><?php echo $newsletter['name']; ?></option>
                            <?php } ?>
                      </select>
                    </td>
                </tr>
                <tr>
                    <td><?php echo $l('entry_page_happy_birthday'); ?></td>
                    <td>
                        <select name="marketing_mailserver_happy_birthday">
                            <option value="0"<?php if (!$marketing_mailserver_happy_birthday) echo ' selected="selected"'; ?>>Servidor Local</option>
                            <?php foreach($mail_servers as $id => $server) { ?>
                            <option value="<?php echo $id; ?>"<?php if ($marketing_mailserver_happy_birthday === $id) echo ' selected="selected"'; ?>>
                            <?php echo $server['server'] .' &lt;'. $server['username'] .'&gt;'; ?>
                            </option>
                            <?php } ?>
                        </select>
                    </td>
                    <td>
                        <select name="marketing_email_happy_birthday" title="<?php echo $l('help_page_happy_birthday'); ?>">
                            <option value="0"><?php echo $l('text_none'); ?></option>
                            <?php foreach ($newsletters as $newsletter) { ?>
                            <option value="<?php echo $newsletter['newsletter_id']; ?>"<?php if ($newsletter['newsletter_id'] == $marketing_email_happy_birthday) { ?> selected="selected"<?php } ?>><?php echo $newsletter['name']; ?></option>
                            <?php } ?>
                      </select>
                    </td>
                </tr>
                <tr>
                    <td><?php echo $l('entry_page_add_balance'); ?></td>
                    <td>
                        <select name="marketing_mailserver_add_balance">
                            <option value="0"<?php if (!$marketing_mailserver_add_balance) echo ' selected="selected"'; ?>>Servidor Local</option>
                            <?php foreach($mail_servers as $id => $server) { ?>
                            <option value="<?php echo $id; ?>"<?php if ($marketing_mailserver_add_balance === $id) echo ' selected="selected"'; ?>>
                            <?php echo $server['server'] .' &lt;'. $server['username'] .'&gt;'; ?>
                            </option>
                            <?php } ?>
                        </select>
                    </td>
                    <td>
                        <select name="marketing_email_add_balance" title="<?php echo $l('help_page_add_balance'); ?>">
                            <option value="0"><?php echo $l('text_none'); ?></option>
                            <?php foreach ($newsletters as $newsletter) { ?>
                            <option value="<?php echo $newsletter['newsletter_id']; ?>"<?php if ($newsletter['newsletter_id'] == $marketing_email_add_balance) { ?> selected="selected"<?php } ?>><?php echo $newsletter['name']; ?></option>
                            <?php } ?>
                      </select>
                    </td>
                </tr>
                <tr>
                    <td><?php echo $l('entry_page_subtract_balance'); ?></td>
                    <td>
                        <select name="marketing_mailserver_subtract_balance">
                            <option value="0"<?php if (!$marketing_mailserver_subtract_balance) echo ' selected="selected"'; ?>>Servidor Local</option>
                            <?php foreach($mail_servers as $id => $server) { ?>
                            <option value="<?php echo $id; ?>"<?php if ($marketing_mailserver_subtract_balance === $id) echo ' selected="selected"'; ?>>
                            <?php echo $server['server'] .' &lt;'. $server['username'] .'&gt;'; ?>
                            </option>
                            <?php } ?>
                        </select>
                    </td>
                    <td>
                        <select name="marketing_email_subtract_balance" title="<?php echo $l('help_page_subtract_balance'); ?>">
                            <option value="0"><?php echo $l('text_none'); ?></option>
                            <?php foreach ($newsletters as $newsletter) { ?>
                            <option value="<?php echo $newsletter['newsletter_id']; ?>"<?php if ($newsletter['newsletter_id'] == $marketing_email_subtract_balance) { ?> selected="selected"<?php } ?>><?php echo $newsletter['name']; ?></option>
                            <?php } ?>
                      </select>
                    </td>
                </tr>
            </table>

            <h2>Mensajes de Promociones y Marketing</h2>
            <table class="form">
                <tr>
                    <td><?php echo $l('entry_page_recommended_products'); ?></td>
                    <td>
                        <select name="marketing_mailserver_recommended_products">
                            <option value="0"<?php if (!$marketing_mailserver_recommended_products) echo ' selected="selected"'; ?>>Servidor Local</option>
                            <?php foreach($mail_servers as $id => $server) { ?>
                            <option value="<?php echo $id; ?>"<?php if ($marketing_mailserver_recommended_products === $id) echo ' selected="selected"'; ?>>
                            <?php echo $server['server'] .' &lt;'. $server['username'] .'&gt;'; ?>
                            </option>
                            <?php } ?>
                        </select>
                    </td>
                    <td>
                        <select name="marketing_email_recommended_products" title="<?php echo $l('help_page_recommended_products'); ?>">
                            <option value="0"><?php echo $l('text_none'); ?></option>
                            <?php foreach ($newsletters as $newsletter) { ?>
                            <option value="<?php echo $newsletter['newsletter_id']; ?>"<?php if ($newsletter['newsletter_id'] == $marketing_email_recommended_products) { ?> selected="selected"<?php } ?>><?php echo $newsletter['name']; ?></option>
                            <?php } ?>
                      </select>
                    </td>
                </tr>
                <tr>
                    <td><?php echo $l('entry_email_promote_product'); ?></td>
                    <td>
                        <select name="marketing_mailserver_promote_product">
                            <option value="0"<?php if (!$marketing_mailserver_promote_product) echo ' selected="selected"'; ?>>Servidor Local</option>
                            <?php foreach($mail_servers as $id => $server) { ?>
                            <option value="<?php echo $id; ?>"<?php if ($marketing_mailserver_promote_product === $id) echo ' selected="selected"'; ?>>
                            <?php echo $server['server'] .' &lt;'. $server['username'] .'&gt;'; ?>
                            </option>
                            <?php } ?>
                        </select>
                    </td>
                    <td>
                        <select name="marketing_email_promote_product" title="<?php echo $l('help_email_promote_product'); ?>">
                            <option value="0"><?php echo $l('text_none'); ?></option>
                            <?php foreach ($newsletters as $newsletter) { ?>
                            <option value="<?php echo $newsletter['newsletter_id']; ?>"<?php if ($newsletter['newsletter_id'] == $marketing_email_promote_product) { ?> selected="selected"<?php } ?>><?php echo $newsletter['name']; ?></option>
                            <?php } ?>
                      </select>
                    </td>
                </tr>
                <tr>
                    <td><?php echo $l('entry_email_invite_friends'); ?></td>
                    <td>
                        <select name="marketing_mailserver_invite_friends">
                            <option value="0"<?php if (!$marketing_mailserver_invite_friends) echo ' selected="selected"'; ?>>Servidor Local</option>
                            <?php foreach($mail_servers as $id => $server) { ?>
                            <option value="<?php echo $id; ?>"<?php if ($marketing_mailserver_invite_friends === $id) echo ' selected="selected"'; ?>>
                            <?php echo $server['server'] .' &lt;'. $server['username'] .'&gt;'; ?>
                            </option>
                            <?php } ?>
                        </select>
                    </td>
                    <td>
                        <select name="marketing_email_invite_friends" title="<?php echo $l('help_email_invite_friends'); ?>">
                            <option value="0"><?php echo $l('text_none'); ?></option>
                            <?php foreach ($newsletters as $newsletter) { ?>
                            <option value="<?php echo $newsletter['newsletter_id']; ?>"<?php if ($newsletter['newsletter_id'] == $marketing_email_invite_friends) { ?> selected="selected"<?php } ?>><?php echo $newsletter['name']; ?></option>
                            <?php } ?>
                      </select>
                    </td>
                </tr>
            </table>

            <h2>Mensajes de la Tienda</h2>
            <table class="form">
                <tr>
                    <td><?php echo $l('entry_page_new_comment'); ?></td>
                    <td>
                        <select name="marketing_mailserver_new_comment">
                            <option value="0"<?php if (!$marketing_mailserver_new_comment) echo ' selected="selected"'; ?>>Servidor Local</option>
                            <?php foreach($mail_servers as $id => $server) { ?>
                            <option value="<?php echo $id; ?>"<?php if ($marketing_mailserver_new_comment === $id) echo ' selected="selected"'; ?>>
                            <?php echo $server['server'] .' &lt;'. $server['username'] .'&gt;'; ?>
                            </option>
                            <?php } ?>
                        </select>
                    </td>
                    <td>
                        <select name="marketing_email_new_comment" title="<?php echo $l('help_page_new_comment'); ?>">
                            <option value="0"><?php echo $l('text_none'); ?></option>
                            <?php foreach ($newsletters as $newsletter) { ?>
                            <option value="<?php echo $newsletter['newsletter_id']; ?>"<?php if ($newsletter['newsletter_id'] == $marketing_email_new_comment) { ?> selected="selected"<?php } ?>><?php echo $newsletter['name']; ?></option>
                            <?php } ?>
                      </select>
                    </td>
                </tr>
                <tr>
                    <td><?php echo $l('entry_page_new_reply'); ?></td>
                    <td>
                        <select name="marketing_mailserver_new_reply">
                            <option value="0"<?php if (!$marketing_mailserver_new_reply) echo ' selected="selected"'; ?>>Servidor Local</option>
                            <?php foreach($mail_servers as $id => $server) { ?>
                            <option value="<?php echo $id; ?>"<?php if ($marketing_mailserver_new_reply === $id) echo ' selected="selected"'; ?>>
                            <?php echo $server['server'] .' &lt;'. $server['username'] .'&gt;'; ?>
                            </option>
                            <?php } ?>
                        </select>
                    </td>
                    <td>
                        <select name="marketing_email_new_reply" title="<?php echo $l('help_page_new_reply'); ?>">
                            <option value="0"><?php echo $l('text_none'); ?></option>
                            <?php foreach ($newsletters as $newsletter) { ?>
                            <option value="<?php echo $newsletter['newsletter_id']; ?>"<?php if ($newsletter['newsletter_id'] == $marketing_email_new_reply) { ?> selected="selected"<?php } ?>><?php echo $newsletter['name']; ?></option>
                            <?php } ?>
                      </select>
                    </td>
                </tr>
                <tr>
                    <td><?php echo $l('entry_page_new_contact'); ?></td>
                    <td>
                        <select name="marketing_mailserver_new_contact">
                            <option value="0"<?php if (!$marketing_mailserver_new_contact) echo ' selected="selected"'; ?>>Servidor Local</option>
                            <?php foreach($mail_servers as $id => $server) { ?>
                            <option value="<?php echo $id; ?>"<?php if ($marketing_mailserver_new_contact === $id) echo ' selected="selected"'; ?>>
                            <?php echo $server['server'] .' &lt;'. $server['username'] .'&gt;'; ?>
                            </option>
                            <?php } ?>
                        </select>
                    </td>
                    <td>
                        <select name="marketing_email_new_contact" title="<?php echo $l('help_page_new_contact'); ?>">
                            <option value="0"><?php echo $l('text_none'); ?></option>
                            <?php foreach ($newsletters as $newsletter) { ?>
                            <option value="<?php echo $newsletter['newsletter_id']; ?>"<?php if ($newsletter['newsletter_id'] == $marketing_email_new_contact) { ?> selected="selected"<?php } ?>><?php echo $newsletter['name']; ?></option>
                            <?php } ?>
                      </select>
                    </td>
                </tr>
            </table>
        </form>
    </div>
</div>
<?php echo $footer; ?>