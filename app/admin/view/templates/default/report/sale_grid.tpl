<table class="list">
    <thead>
        <tr>
            <th><?php echo $l('column_date_start; ?></th>
            <th><?php echo $l('column_date_end; ?></th>
            <th><?php echo $l('column_orders; ?></th>
            <th><?php echo $l('column_total; ?></th>
        </tr>
    </thead>
    <tbody>
    <?php if ($orders) { ?>
        <?php foreach ($orders as $order) { ?>
        <tr>
            <td class="left"><?php echo $order['date_start']; ?></td>
            <td class="left"><?php echo $order['date_end']; ?></td>
            <td class="right"><?php echo $order['orders']; ?></td>
            <td class="right"><?php echo $order['total']; ?></td>
        </tr>
        <?php } ?>
    <?php } else { ?>
        <tr>
            <td class="center" colspan="4"><?php echo $l('text_no_results'); ?></td>
        </tr>
    <?php } ?>
    </tbody>
</table>
<div class="pagination"><?php echo $pagination; ?></div>
<?php echo $footer; ?>