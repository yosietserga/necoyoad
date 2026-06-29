<div class="box">
    <div id="chart_customer_new_line" style="width:100%; height: 120px; margin: 0 auto"></div>
</div>
<script type="text/javascript">
$(function () {
    var chart;
    $(document).ready(function() {
        chart = new Highcharts.Chart({
            chart: {
                renderTo: 'chart_customer_new_line',
                type: 'area',
                marginBottom: 25
            },
            title: {
                text: 'Clientes Nuevos',
                x: -20 
            },
            xAxis: {
                categories: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic']
            },
            yAxis: {
                title: {
                enabled: false
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
                        this.x +': '+ this.y +' Clientes Registrados';
                }
            },
            legend: {
                enabled: false
            },
            series: [{
                name: 'Clientes Nuevos',
                data: [
                    <?php echo (int)$customers[1]; ?>,
                    <?php echo (int)$customers[2]; ?>,
                    <?php echo (int)$customers[3]; ?>,
                    <?php echo (int)$customers[4]; ?>,
                    <?php echo (int)$customers[5]; ?>,
                    <?php echo (int)$customers[6]; ?>,
                    <?php echo (int)$customers[7]; ?>,
                    <?php echo (int)$customers[8]; ?>,
                    <?php echo (int)$customers[9]; ?>,
                    <?php echo (int)$customers[10]; ?>,
                    <?php echo (int)$customers[11]; ?>,
                    <?php echo (int)$customers[12]; ?>
                ]
            }]
        });
    });
    
});
</script>