<div class="box">
    <div id="serverStatusWidgetPieChart" style="width: 100%; height: 190px; margin: 0 auto"></div>
</div>
<script type="text/javascript">
$(function () {
    if (!$.fn.highcharts) {
        $(document.createElement('script')).attr({
            src:"js/vendor/highcharts-4.0.1/highcharts.js",
            type:"text/javascript"
        }).appendTo('head');
    }
    
    if ($.fn.highcharts) {
        $('#serverStatusWidgetPieChart').highcharts({
            chart: {
                plotBackgroundColor: null,
                plotBorderWidth: 0,
                plotShadow: false
            },
            title: {
                text: '<?php echo $l('heading_server_health'); ?>',
                align: 'center',
                verticalAlign: 'top',
                y: 10
            },
            subtitle: {
                text: 'Powered by NecoTienda',
                align: 'center',
                y: 20
            },
            tooltip: {
                pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
            },
            plotOptions: {
                pie: {
                    dataLabels: {
                        enabled: true,
                        distance: 0,
                        style: {
                            fontWeight: 'bold',
                            color: 'white',
                            textShadow: '0px 1px 2px black'
                        }
                    },
                    startAngle: -90,
                    endAngle: 90,
                    center: ['50%', '75%']
                }
            },
            series: [{
                type: 'pie',
                name: '<?php echo $l('heading_server_health'); ?>',
                innerSize: '50%',
                data: [
                    ['Bien', <?php echo (float)$percent; ?>],
                    ['Mal', <?php echo (float)$percent_diff; ?>]
                ]
            }]
        });
    }
});
</script>