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
    
    <?php /*
    <div class="grid_4">
        <!-- gauge con la salud del servidor y el sitio web -->
        <div class="box" style="text-align: center;">
            <div id="chartHealth"><i class="fa fa-refresh fa-2x fa-spin"></i></div>
        </div>
    </div>
    
    <div class="grid_4">
        <!-- gráfico de stack comparando los correos enviados por motivos de publicidad y promoción y las visitas registradas -->
        <div class="box" style="text-align: center;">
            <div id="chartAdvertise"><i class="fa fa-refresh fa-2x fa-spin"></i></div>
        </div>
    </div>
    
    <div class="grid_4">
        <!-- las medallas o pines y los puntos obtenidos por las buenas prácticas en rangos de 7 días -->
        <div class="box" style="text-align: center;">
            <div id="chartPoints"><i class="fa fa-refresh fa-2x fa-spin"></i></div>
        </div>
    </div>
    
    <div class="clear"></div>
    */ ?>
    <div class="grid_6">
        <!-- gráfico de area comparando cantidad de visitas, bs. en ventas, cantidad de pedidos en los últimos 10 días -->
        <div class="box" style="text-align: center;">
            <div id="chartSales"><i class="fa fa-refresh fa-2x fa-spin"></i></div>
        </div>
    </div>
    
    <div class="grid_6">
        <!-- contador y grafico de barra con totales de clientes registrados -->
        <div class="box" style="text-align: center;">
            <div id="chartTotalVisits"><i class="fa fa-refresh fa-2x fa-spin"></i></div>
        </div>
    </div>
    
    <div class="grid_6">
        <!-- contador totales de productos y categorias -->
        <div class="box" style="text-align: center;">
            <div id="chartTotalOrders"><i class="fa fa-refresh fa-2x fa-spin"></i></div>
        </div>
    </div>
    
    <div class="grid_6">
        <!-- gráfico de barra con los totales de pedidos por mes y total en lo que va de año -->
        <div class="box" style="text-align: center;">
            <div id="chartCustomers"><div style="margin:20% auto;width:50px;text-align: center;"><i class="fa fa-refresh fa-2x fa-spin"></i></div></div>
        </div>
    </div>
    
    <div class="clear"></div>
    
    <div class="grid_6">
        <div class="box">
            <div class="header">
                <hgroup><h1><?php echo $l('heading_title'); ?></h1></hgroup>
            </div>
            <div class="clear"></div><br />
            <table style="width: 100%;">
                <tr>
                  <td class="hideOnMobile" style="width: 80%;"><?php echo $l('text_total_sale'); ?></td>
                  <td data-title="<?php echo $l('text_total_sale'); ?>"><?php echo $total_sale; ?></td>
                <tr>
                  <td class="hideOnMobile"><?php echo $l('text_total_sale_year'); ?></td>
                  <td data-title="<?php echo $l('text_total_sale_year'); ?>"><?php echo $total_sale_year; ?></td>
                </tr>
                <tr>
                  <td class="hideOnMobile"><?php echo $l('text_total_order'); ?></td>
                  <td data-title="<?php echo $l('text_total_order'); ?>"><?php echo $total_order; ?></td>
                </tr>
                <tr>
                  <td class="hideOnMobile"><?php echo $l('text_total_customer'); ?></td>
                  <td data-title="<?php echo $l('text_total_customer'); ?>"><?php echo $total_customer; ?></td>
                </tr>
                <tr>
                  <td class="hideOnMobile"><?php echo $l('text_total_customer_approval'); ?></td>
                  <td data-title="<?php echo $l('text_total_customer_approval'); ?>"><?php echo $total_customer_approval; ?></td>
                </tr>
                <tr>
                  <td class="hideOnMobile"><?php echo $l('text_total_product'); ?></td>
                  <td data-title="<?php echo $l('text_total_product'); ?>"><?php echo $total_product; ?></td>
                </tr>
                <tr>
                  <td class="hideOnMobile"><?php echo $l('text_total_review'); ?></td>
                  <td data-title="<?php echo $l('text_total_review'); ?>"><?php echo $total_review; ?></td>
                </tr>
                <tr>
                  <td class="hideOnMobile"><?php echo $l('text_total_review_approval'); ?></td>
                  <td data-title="<?php echo $l('text_total_review_approval'); ?>"><?php echo $total_review_approval; ?></td>
                </tr>
              </table>
        </div>
    </div>
    
    <div class="grid_6">
        <div class="box">
            <div class="header">
                <hgroup><h1><?php echo $l('text_latest_10_orders'); ?></h1></hgroup>
            </div>
            <div class="clear"></div><br />
            <table class="list resize">
                <thead>
                    <tr>
                        <th><?php echo $l('column_order'); ?></th>
                        <th><?php echo $l('column_name'); ?></th>
                        <th class="left"><?php echo $l('column_status'); ?></th>
                        <th class="left"><?php echo $l('column_date_added'); ?></th>
                        <th class="right"><?php echo $l('column_total'); ?></th>
                        <th class="right"><?php echo $l('column_action'); ?></th>
                    </tr>
                </thead>
                <tbody>
                <?php if ($orders) { ?>
                    <?php foreach ($orders as $order) { ?>
                    <tr>
                        <td data-title="<?php echo $l('column_order'); ?>" class="right"><?php echo $order['order_id']; ?></td>
                        <td data-title="<?php echo $l('column_name'); ?>" class="left"><?php echo $order['name']; ?></td>
                        <td data-title="<?php echo $l('column_status'); ?>" class="left"><?php echo $order['status']; ?></td>
                        <td data-title="<?php echo $l('column_date_added'); ?>" class="left"><?php echo $order['date_added']; ?></td>
                        <td data-title="<?php echo $l('column_total'); ?>" class="right"><?php echo $order['total']; ?></td>
                        <td data-title="<?php echo $l('column_action'); ?>" class="right">
                        <?php foreach ($order['action'] as $action) { ?>
                            [ <a href="<?php echo $action['href']; ?>"><?php echo $action['text']; ?></a> ]
                        <?php } ?>
                        </td>
                    </tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr><td class="center" colspan="6"><?php echo $l('text_no_results'); ?></td></tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
    <script>
    $(function(){
        $('#chartHealth').load("<?php echo $Url::createAdminUrl("widgets/server_status/widget"); ?>");
        $('#chartSales').load("<?php echo $Url::createAdminUrl("widgets/order_stats/widget"); ?>");
        
        $('#chartTotalVisits').load("<?php echo $Url::createAdminUrl("widgets/order_stats/lastsales"); ?>");
        $('#chartTotalOrders').load("<?php echo $Url::createAdminUrl("widgets/order_stats/lastorders"); ?>");
        $('#chartCustomers').load("<?php echo $Url::createAdminUrl("chart/customer"); ?>");
    });
    </script>
</div>
<?php echo $footer; ?>