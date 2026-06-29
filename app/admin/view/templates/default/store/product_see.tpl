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
    
    <div class="grid_12">
        <div class="box">
            <h1><?php echo $l('heading_title'); ?></h1>

            <div class="clear"></div><br />

            <ul id="vtabs" class="vtabs">
                <li><a data-target="#tab_visits" onclick="showTab(this)">Visitas</a></li>
                <li><a data-target="#tab_orders" onclick="showTab(this)">Pedidos</a></li>
                <li><a data-target="#tab_sales" onclick="showTab(this)">Ventas</a></li>
                <li><a data-target="#tab_comments" onclick="showTab(this)">Comentarios</a></li>
            </ul>

            <div id="tabs">

                <div id="tab_visits" class="vtabs_page">
                    <div class="grid_12">
                        <div class="box">
                            <div class="header">
                                <hgroup><h1>Estad&iacute;sticas de Visitas</h1></hgroup>
                            </div>
                            <div class="clear"></div><br />
                            <div id="chartVisits" style="height: 300px; min-width: 500px"></div>
                            <div class="clear"></div><br />
                            <div id="visitsStats">Cargando...</div>
                        </div>
                    </div>
                </div>

                <div id="tab_orders" class="vtabs_page">
                    <div class="grid_12">
                        <div class="box">
                            <div class="header">
                                <hgroup><h1>Estad&iacute;sticas de Visitas</h1></hgroup>
                            </div>
                            <div class="clear"></div><br />
                            <div id="chartOrders"></div>
                            <div class="clear"></div><br />
                            <div id="ordersStats">Cargando...</div>
                        </div>
                    </div>
                </div>

                <div id="tab_sales" class="vtabs_page">
                    <div class="grid_12">
                        <div class="box">
                            <div class="header">
                                <hgroup><h1>Estad&iacute;sticas de Visitas</h1></hgroup>
                            </div>
                            <div class="clear"></div><br />
                            <div id="chartSales" style="height: 300px; min-width: 500px"></div>
                            <div class="clear"></div><br />
                            <div id="salesStats">Cargando...</div>
                        </div>
                    </div>
                </div>

                <div id="tab_comments" class="vtabs_page">
                    <div class="grid_12">
                        <div class="box">
                            <div class="header">
                                <hgroup><h1>Estad&iacute;sticas de Visitas</h1></hgroup>
                            </div>
                            <div class="clear"></div><br />
                            <div id="chartComments" style="height: 300px; min-width: 500px"></div>
                            <div class="clear"></div><br />
                            <div id="commentsStats">Cargando...</div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
<?php echo $footer; ?>