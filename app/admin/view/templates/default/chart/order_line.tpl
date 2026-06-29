<div class="box">
    <div id="chart_order_line" style="width:100%; height: 120px; margin: 0 auto"></div>
</div>
<script type="text/javascript">
$(function () {
    var chart;
    $(function() {
        chart = new Highcharts.Chart({
            chart: {
                renderTo: 'chart_order_line',
                type: 'bar',
                marginBottom: 25,
                plotShadow: false
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
                enabled: false
            },
            series: [
                {
                    name: 'Pedidos',
                    data: [
                        <?php echo implode(',', $orders); ?>
                    ]
                }
            ]
        });
    });
});
</script>