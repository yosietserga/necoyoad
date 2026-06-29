<div class="grid_12">
<?php if (!empty($browsers)) { ?>
    <table class="visitsChart" data-graph-container-before="1" data-graph-type="pie" data-graph-xaxis-type="percent">
        <caption>Visitas Por Navegador</caption>
        <thead>
            <tr>
                <th>Navegador</th>
                <th>Visitas</th>
            </tr>
         </thead>
         <tbody>
            <?php foreach ($browsers as $value) { ?>                           
            <tr>
                <td><a href="<?php echo $Url::createAdminUrl("report/visits/browser",array('browser'=>$value['name'])); ?>" title="Ver Detalles"><?php echo empty($value['name']) ? 'Desconocido' : $value['name']; ?></a></td>
                <td data-graph-name="<?php echo empty($value['name']) ? 'Desconocido' : $value['name']; ?>"><?php echo $value['total']; ?></td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
    <a href="<?php echo $Url::createAdminUrl("report/visits/browser"); ?>" title="Ver Todo"><small>Ver Todo</small></a>
<?php } else { ?>
No se encontraron datos de visitas por navegadores
<?php } ?>
</div>

<div class="grid_11">
<?php if (!empty($os)) { ?>
    <table class="visitsChart" data-graph-container-before="1" data-graph-type="pie" data-graph-xaxis-type="percent">
        <caption>Visitas Por S.O.</caption>
        <thead>
            <tr>
                <th>Sistema Operativo</th>
                <th>Visitas</th>
            </tr>
         </thead>
         <tbody>
            <?php foreach ($os as $value) { ?>                           
            <tr>
                <td><a href="<?php echo $Url::createAdminUrl("report/visits/os",array('os'=>$value['name'])); ?>" title="Ver Detalles"><?php echo empty($value['name']) ? 'Desconocido' : $value['name']; ?></a></td>
                <td data-graph-name="<?php echo empty($value['name']) ? 'Desconocido' : $value['name']; ?>"><?php echo $value['total']; ?></td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
    <a href="<?php echo $Url::createAdminUrl("report/visits/os"); ?>" title="Ver Todo"><small>Ver Todo</small></a>
<?php } else { ?>
No se encontraron datos de visitas por S.O.
<?php } ?>
</div>

<div class="clear"></div><br />

<div class="grid_12">
<?php if (!empty($customers)) { ?>
    <table class="visitsChart" data-graph-container-before="1" data-graph-type="pie" data-graph-xaxis-type="percent">
        <caption>Visitas Top 10 Cliente</caption>
        <thead>
            <tr>
                <th>Cliente</th>
                <th>Visitas</th>
            </tr>
         </thead>
         <tbody>
            <?php foreach ($customers as $value) { ?>                           
            <tr>
                <td><a href="<?php echo $Url::createAdminUrl("report/visits/customer",array('customer_id'=>$value['customer_id'])); ?>" title="Ver Detalles"><?php echo empty($value['name']) ? 'Desconocido' : $value['name']; ?></a></td>
                <td data-graph-name="<?php echo empty($value['name']) ? 'Desconocido' : $value['name']; ?>"><?php echo $value['total']; ?></td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
    <a href="<?php echo $Url::createAdminUrl("report/visits/customer"); ?>" title="Ver Todo"><small>Ver Todo</small></a>
<?php } else { ?>
No se encontraron datos de visitas por clientes
<?php } ?>
</div>

<div class="grid_11">
<?php if (!empty($ips)) { ?>
    <table class="visitsChart" data-graph-container-before="1" data-graph-type="pie" data-graph-xaxis-type="percent">
        <caption>Visitas Top 10 Direcci&oacute;n IP</caption>
        <thead>
            <tr>
                <th>Sistema Operativo</th>
                <th>Visitas</th>
            </tr>
         </thead>
         <tbody>
            <?php foreach ($ips as $value) { ?>                           
            <tr>
                <td><a href="<?php echo $Url::createAdminUrl("report/visits/ip",array('ip'=>$value['name'])); ?>" title="Ver Detalles"><?php echo empty($value['name']) ? 'Desconocido' : $value['name']; ?></a></td>
                <td data-graph-name="<?php echo empty($value['name']) ? 'Desconocido' : $value['name']; ?>"><?php echo $value['total']; ?></td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
    <a href="<?php echo $Url::createAdminUrl("report/visits/ip"); ?>" title="Ver Todo"><small>Ver Todo</small></a>
<?php } else { ?>
No se encontraron datos de visitas por IP
<?php } ?>
</div>

<script>$('table.visitsChart').highchartTable();</script>