<div class="box">
    <div id="orderStatsWidgetAreaChart" style="width: 100%; height: 446px; margin: 0 auto;"></div>
</div>
<script type="text/javascript">
$(function () {
    var chart;
    $(document).ready(function() {
        chart = new Highcharts.Chart({
            
            chart: {
                renderTo: 'orderStatsWidgetAreaChart',
                zoomType: 'xy',
                plotShadow: false
            },
            title: {
                text: 'Visitas, Pedidos y Ventas',
                x: -30 
            },
            subtitle: {
                text: 'Powered by NecoTienda',
                x: -12
            },
            xAxis: {
                categories: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic']
            },
            yAxis: [{
                labels: {
                    format: '{value}',
                    style: {
                        color: Highcharts.getOptions().colors[0]
                    }
                },
                title: {
                    enabled: false
                },
                opposite: true
            },{
                labels: {
                    format: '<?php echo $this->currency->getCode(); ?> {value}',
                    style: {
                        color: Highcharts.getOptions().colors[1]
                    }
                },
                title: {
                    enabled: false
                }
            },{
                labels: {
                    format: '{value}',
                    style: {
                        color: Highcharts.getOptions().colors[2]
                    }
                },
                title: {
                    enabled: false
                },
                opposite: true
            }],
            tooltip: {
                shared: true
            },
            series: [{
                name: 'Pedidos',
                type: 'area',
                data: [
                    <?php echo implode(',', $orders); ?>
                ]
            },{
                name: 'Ventas',
                type: 'area',
                yAxis: 1,
                data: [
                    <?php echo implode(',', $sales); ?>
                ]
            },{
                name: 'Visitas',
                type: 'area',
                yAxis: 2,
                data: [
                    <?php echo implode(',', $visits); ?>
                ]
            }
            ]
        });
    });
    
});
</script>