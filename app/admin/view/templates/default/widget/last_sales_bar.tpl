<div class="box">
    <div id="orderStatsLastSalesWidgetBarChart" style="width: 100%; height: 120px; margin: 0 auto;"></div>
</div>
<script type="text/javascript">
$(function () {
    var chart;
    $(document).ready(function() {
        chart = new Highcharts.Chart({
            
            chart: {
                renderTo: 'orderStatsLastSalesWidgetBarChart',
                type: 'area',
                plotShadow: false
            },
            title: {
                text: 'Ventas',
                x: -30 
            },
            legend: {
                enabled: false
            },
            xAxis: {
                categories: ['<?php echo implode("','", array_keys($sales)); ?>']
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
                name: 'Ventas',
                data: [
                    <?php echo implode(',', $sales); ?>
                ]
            }
            ]
        });
    });
    
});
</script>