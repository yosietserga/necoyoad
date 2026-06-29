<!doctype html>
<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="es"> <![endif]-->
<!--[if IE 7]>    <html class="no-js lt-ie9 lt-ie8" lang="es"> <![endif]-->
<!--[if IE 8]>    <html class="no-js lt-ie9" lang="es"> <![endif]-->
<!-- Consider adding a manifest.appcache: h5bp.com/d/Offline -->
<!--[if gt IE 8]><!--> <html class="no-js" lang="<?php echo $language; ?>"> <!--<![endif]-->
    <head>
        <meta charset="utf-8" />
        <title><?php echo $title; ?></title>
        <base href="<?php echo $base; ?>" />
        <link rel="stylesheet" type="text/css" href="<?php echo str_replace('%theme%', $Config->get('config_admin_template'), HTTP_ADMIN_THEME_CSS); ?>invoice.css" />
    </head>
    <body>
        <?php foreach ($orders as $order) { ?>
        <div style="page-break-after: always;">
            <h1><?php echo $l('text_invoice; ')?></h1>
            <div class="div1">
                <table>
                    <tr>
                        <td>
                            <?php echo $order['store_name']; ?><br />
                            <?php echo $order['address']; ?><br />
                            <?php echo $l('text_telephone'); ?>
                            <?php echo $order['telephone']; ?><br />
                            <?php echo $order['email']; ?><br />
                            <?php echo $order['store_url']; ?>
                        </td>
                        <td>
                            <table>
                                <tr>
                                    <td><b><?php echo $l('text_invoice_date'); ?></b></td>
                                    <td><?php echo $order['date_added']; ?></td>
                                </tr>
                                <tr>
                                    <td><b><?php echo $l('text_invoice_no'); ?></b></td>
                                    <td><?php echo $order['order_id']; ?></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </div>
            <table class="address">
                <tr class="heading">
                    <td><b><?php echo $l('text_to'); ?></b></td>
                    <td><b><?php echo $l('text_ship_to'); ?></b></td>
                </tr>
                <tr>
                    <td><?php echo $order['payment_address']; ?><br /><?php echo $order['customer_email']; ?></td>
                    <td><?php echo$order['shipping_address']; ?></td>
                </tr>
            </table>
            <table class="product">
                <tr class="heading">
                    <td><b><?php echo $l('column_product'); ?></b></td>
                    <td><b><?php echo $l('column_model'); ?></b></td>
                    <td><b><?php echo $l('column_quantity'); ?></b></td>
                    <td><b><?php echo $l('column_price'); ?></b></td>
                    <td><b><?php echo $l('column_total'); ?></b></td>
                </tr>
                <?php foreach ($order['product'] as $product) { ?>
                <tr>
                    <td>
                        <?php echo $product['name']; ?>
                        <?php foreach ($product['option'] as $option) { ?>
                        <br />&nbsp;<small>-<?php echo $option['name']; ?><?php echo $option['value']; ?></small>
                        <?php } ?>
                    </td>
                    <td><?php echo $product['model']; ?></td>
                    <td><?php echo $product['quantity']; ?></td>
                    <td><?php echo $product['price']; ?></td>
                    <td><?php echo $product['total']; ?></td>
                </tr>
                <?php } ?>
                <?php foreach ($order['total'] as $total) { ?>
                <tr>
                    <td colspan="4"><b><?php echo $total['title']; ?></b></td>
                    <td><?php echo $total['text']; ?></td>
                </tr>
                <?php } ?>
            </table>
        </div>
        <?php } ?>
    </body>
</html>