<div class="grid_12">
    <table class="commentsChart" data-graph-container-before="1" data-graph-type="pie" data-graph-datalabels-enabled="1">
        <thead>
            <tr>
            <?php foreach ($browsers as $browser) { ?>
                <th><?php echo empty($browser['browser']) ? 'Desconocido' : $browser['browser']; ?></th>
            <?php } ?>
            </tr>
         </thead>
         <tbody>
            <?php foreach ($browsers as $browser) { ?>                           
            <tr>
                <td><?php echo empty($browser['browser']) ? 'Desconocido' : $browser['browser']; ?></td>
                <td><?php echo $browser['total']; ?></td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
<script>$('table.commentsChart').highchartTable();</script>