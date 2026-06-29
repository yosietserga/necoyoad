<h2>Historia del Pedido</h2>
<?php foreach ($histories as $history) { ?>
<table class="list">
    <thead>
        <tr>
            <th><?php echo $l('column_date_added'); ?></th>
            <th><?php echo $l('column_status'); ?></th>
            <th><?php echo $l('column_notify'); ?></th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td><?php echo $history['date_added']; ?></td>
            <td><?php echo $history['status']; ?></td>
            <td><?php echo $history['notify']; ?></td>
        </tr>
    </tbody>
</table>

<table class="list">
    <?php if ($history['comment']) { ?>
    <thead>
        <tr>
            <th><?php echo $l('column_comment'); ?></th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td><?php echo $history['comment']; ?></td>
        </tr>
    </tbody>
    <?php } ?>
</table>
<?php } ?>

<table class="form">
    <tr>
        <td><?php echo $l('entry_status'); ?></td>
        <td>
            <select name="order_status_id">
                <option value="0"><?php echo $l('text_none'); ?></option>
				<?php foreach ($order_statuses as $order_statuses) { ?>
                <option value="<?php echo $order_statuses['order_status_id']; ?>"<?php if ($order_statuses['order_status_id'] == $order_status_id) { ?> selected="selected"<?php } ?>><?php echo $order_statuses['name']; ?></option>
                <?php } ?>
            </select>
        </td>
    </tr>
    <tr>
        <td><?php echo $l('entry_notify'); ?></td>
        <td><input type="checkbox" name="notify" value="1" /></td>
    </tr>
    <tr>
        <td><?php echo $l('entry_append'); ?></td>
        <td><input type="checkbox" name="append" value="1" checked="checked" /></td>
    </tr>
    <tr>
        <td><?php echo $l('entry_comment'); ?></td>
        <td>
            <textarea name="comment" cols="40" rows="8" style="width: 99%"></textarea>
            <div style="margin-top: 10px; text-align: right;">
            <a onclick="history();" id="history_button" class="button"><?php echo $l('button_add_history'); ?></a>
            </div>
        </td>
    </tr>
</table>
<script type="text/javascript">
function history() {
    $('.success, .warning').remove();
    $('#history_button').attr('disabled', 'disabled');
	$('#tab_history .form').before('<div class="attention"><img src="images/loading_1.gif"><?php echo $l('text_wait'); ?></div>');
    
    $.post('<?php echo $Url::createAdminUrl("sale/order/history",array('order_id'=>$order_id)); ?>',
    {
        'order_status_id':  encodeURIComponent($('select[name=\'order_status_id\']').val()),
        'notify':           encodeURIComponent($('input[name=\'notify\']').attr('checked') ? 1 : 0),
        'append':           encodeURIComponent($('input[name=\'append\']').attr('checked') ? 1 : 0),
        'comment':          $('textarea[name=\'comment\']').val()
    },
    function(response) {
        var data = $.parseJSON(response);
        if (data.error) {
            $('#tab_history .form').before('<div class="warning">' + data.error + '</div>');
            $('#history_button').attr('disabled', '');
			$('.attention').remove();
        }
        
        if (data.success && $('input[name=\'append\']').attr('checked')) {
            html  = '<div class="history" style="display: none;">';
			html += '<table class="list">';
			html += '<thead>';
			html += '<tr>';
			html += '<th><?php echo $l('column_date_added'); ?></th>';
			html += '<th><?php echo $l('column_status'); ?></th>';
			html += '<th><?php echo $l('column_notify'); ?></th>';
			html += '</tr>';
			html += '</thead>';
			html += '<tbody>';
			html += '<tr>';
			html += '<td>' + data.date_added + '</td>';
			html += '<td>' + data.order_status + '</td>';
			html += '<td>' + data.notify + '</td>';
			html += '</tr>';
			html += '</tbody>';
			html += '</table>';	
			
			if (data.comment) {
                html += '<table class="list">';
                html += '<thead>';
				html += '<tr>';
				html += '<td colspan="3"><b><?php echo $l('column_comment'); ?></b></td>';
				html += '</tr>';
				html += '</thead>';
				html += '<tbody>';
				html += '<tr>';
				html += '<td colspan="3">' + data.comment + '</td>';
				html += '</tr>';
				html += '</tbody>';	
                html += '</table>';	
			}
			html += '</div>';	
				
			$('#order_status').html(data.status);
			$('#tab_history .form').before(html);
			$('#tab_history .history').slideDown();
			$('#tab_history .form').before('<div class="success">' + data.success + '</div>');
			$('textarea[name=\'comment\']').val('');
        }
    });
}
</script>