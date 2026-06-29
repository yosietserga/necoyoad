<div class="row">
    <label for="<?php echo $name; ?>SettingsClass"><?php echo $l('entry_class'); ?></label>
    <input id="<?php echo $name; ?>SettingsClass" name="Widgets[<?php echo $name; ?>][settings][class]" value="<?php echo isset($settings['class']) ? $settings['class'] : ''; ?>" />
</div>

<div id="dataSection">
<?php if (!$settings['component']) { ?>
	<?php include('widget_form_data_components.tpl'); ?>
<?php } else { ?>
	<?php echo dirname(__FILE__) . '/components/'. $settings['component'] .'.tpl'; ?>
	<?php include(dirname(__FILE__) . '/components/'. $settings['component'] .'.tpl'); ?>
<?php } ?>
</div>

<script>
	if (typeof renderTextField === 'undefined' && typeof renderTextField !== 'function') {
		function renderTextField( widgetName, title, fieldName, value='' ) {
			let row = createRow();

			let label = createLabel( title, {
				id:`${widgetName}Settings${title}`
			});

			let inputText = createInputText({
				id:`${widgetName}Settings${title}`,
				name:`Widgets[${widgetName}][settings][${fieldName}]`,
				value
			});

			row.append( label );
			row.append( inputText );

			$('#'+ widgetName +' #dataSection .fieldsComponent').append( row );
		}
	}

	if (typeof createRow === 'undefined' && typeof createRow !== 'function') {
		function createRow() {
			return $(document.createElement('div')).addClass( 'row' );
		}
	}


	if (typeof createLabel === 'undefined' && typeof createLabel !== 'function') {
		function createLabel( text, attributes ) {
			if (typeof text == 'undefined') return false;
			let el = $(document.createElement('label')).html( text );
			if (typeof attributes === 'object') el.attr( attributes );
			return el;
		}
	}

	if (typeof createInputText === 'undefined' && typeof createInputText !== 'function') {
		function createInputText( attributes ) {
			let el = $(document.createElement('input'));
			if (typeof attributes === 'object') el.attr( attributes );
			return el;
		}
	}

	$(function(){
		if (typeof componentFields != 'undefined' && componentFields.length > 0) {
			componentFields.map( (v, k) => {
				if (v && typeof v.type != 'undefined') window['render'+ v.type +'Field'](v.widgetName, v.title, v.name, v.value);
				componentFields[ k ] = null;
			});
		}
	});

</script>