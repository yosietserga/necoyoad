<div class="box">
    <div id="orderStatsLastOrdersWidgetBarChart" style="width: 100%; height: 120px; margin: 0 auto;"></div>
</div>
<script type="text/javascript">
$(function () {
    var chart;
    $(document).ready(function() {
        chart = new Highcharts.Chart({
            
            chart: {
                renderTo: 'orderStatsLastOrdersWidgetBarChart',
                type: 'area',
                plotShadow: false
            },
            title: {
                text: 'Pedidos',
                x: -30 
            },
            legend: {
                enabled: false
            },
            xAxis: {
                categories: ['<?php echo implode("','", array_keys($orders)); ?>']
            },
            yAxis: [{
                title: {
                    enabled: false
                }
            }],
            tooltip: {
                shared: true
            },
            series: [{
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