<div class="fieldsComponent">Seprator fields</div>

<script>
	var componentFields = componentFields || [];
	
	componentFields.push({
		type:'Text',
		title:'Separator',
		widgetName:'<?php echo $name; ?>',
		value:'<?php echo isset($settings['separator']) ? $settings['separator'] : ''; ?>',
		name:'separator'
	});
</script>