<div class="box">
    <div class="header">
        <hgroup><h1>Gr&aacute;fico de Visitas</h1></hgroup>
    </div>
    <div class="clear"></div><br />
    <div id="chart_order_line" style="width:100%; height: 300px; margin: 0 auto"></div>
</div>
<script type="text/javascript">
$(function () {
    var chart;
    $(document).ready(function() {
        chart = new Highcharts.Chart({
            chart: {
                renderTo: 'chart_order_line',
                type: 'line',
                marginRight: 130,
                marginBottom: 25
            },
            title: {
                text: 'Pedidos',
                x: -20 
            },
            subtitle: {
                text: 'Powered by NecoTienda',
                x: -20
            },
            xAxis: {
                categories: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic']
            },
            yAxis: {
                title: {
                    text: 'Cant. de Pedidos'
                },
                plotLines: [{
                    value: 0,
                    width: 1,
                    color: '#808080'
                }]
            },
            tooltip: {
                formatter: function() {
                        return '<b>'+ this.series.name +'</b><br/>'+
                        this.x +': '+ this.y +' Pedidos Realizados';
                }
            },
            legend: {
                layout: 'vertical',
                align: 'right',
                verticalAlign: 'top',
                x: -10,
                y: 100,
                borderWidth: 0
            },
            series: [{
                name: 'Pedidos',
                data: [
                    <?php echo (int)$orders[0]; ?>,
                    <?php echo (int)$orders[1]; ?>,
                    <?php echo (int)$orders[2]; ?>,
                    <?php echo (int)$orders[3]; ?>,
                    <?php echo (int)$orders[4]; ?>,
                    <?php echo (int)$orders[5]; ?>,
                    <?php echo (int)$orders[6]; ?>,
                    <?php echo (int)$orders[7]; ?>,
                    <?php echo (int)$orders[8]; ?>,
                    <?php echo (int)$orders[9]; ?>,
                    <?php echo (int)$orders[10]; ?>,
                    <?php echo (int)$orders[11]; ?>
                ]
            }]
        });
    });
    
});
</script>