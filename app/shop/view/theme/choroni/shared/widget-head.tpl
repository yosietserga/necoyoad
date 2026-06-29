<?php 
if (!isset($settings['module']) || empty($settings['module'])) throw new Exception("FATAL ERROR: The index module is not set for ". $settings['route']);

$t = '';
if (!empty($settings['transitions'])) {
	foreach ($settings['transitions'] as $key => $transition) {
		$t .= isset($transition['effect']) && !empty($transition['effect']) ? ' data-transition_'. $key .'_effect="'. $transition['effect'] .'"' : '';
		$t .= isset($transition['delay']) && !empty($transition['delay']) ? ' data-transition_'. $key .'_delay="'. $transition['delay'] .'"' : '';
		$t .= isset($transition['duration']) && !empty($transition['duration']) ? ' data-transition_'. $key .'_duration="'. $transition['duration'] .'"' : '';
		$t .= isset($transition['beforeStart']) && !empty($transition['beforeStart']) ? ' data-transition_'. $key .'_beforeStart="'. $transition['beforeStart'] .'"' : '';
		$t .= isset($transition['onStart']) && !empty($transition['onStart']) ? ' data-transition_'. $key .'_onStart="'. $transition['onStart'] .'"' : '';
		$t .= isset($transition['onStop']) && !empty($transition['onStop']) ? ' data-transition_'. $key .'_onStop="'. $transition['onStop'] .'"' : '';
	}
}

echo 
'<li '. 
	'data-necotienda_module="'. $settings['module'] .'" '.
	'data-landing_page="'. str_replace('landing_page=', '', $settings['landing_page']) .'" '.
	'data-widget="'. $widgetName .'" '.
	'nt-editable="1" '.
	'movable="1" '.
	'removable="1" '.
	'configurable="1" '.
	'class="box '. 
		$settings['module'] .'-widget'. 
		(isset($settings['class']) && !empty($settings['class']) ? ' '.$settings['class'] : '') .
		(isset($settings['shrinkable']) && !empty($settings['shrinkable']) ? ' shrinkable' : '') .
	'" '.
	'id="'. $widgetName .'" '.

	((isset($settings['offsetX']) && !empty($settings['offsetX'])) || (isset($settings['offsetY']) && !empty($settings['offsetY'])) ? ' style="position:absolute;z-index: 9;top:'. $settings['offsetY'] .';left:'. $settings['offsetX'] .';"' : '') .
	
	(isset($settings['sticky']) && !empty($settings['sticky']) ? ' data-sticky="1"' : '') .
	(isset($settings['shrinkable']) && !empty($settings['shrinkable']) ? ' data-shrink="'. (isset($settings['shrinkable_width']) && !empty($settings['shrinkable_width']) ? $settings['shrinkable_width'] : 200) .'"' : '') .

	(isset($settings['transition_active']) && !empty($settings['transition_active']) ? ' data-animate' : '') .
	(isset($settings['transition_repeat']) && !empty($settings['transition_repeat']) ? ' data-repeat="1"' : '') .
	(isset($settings['transition_async']) && !empty($settings['transition_async']) ? ' data-async="1"' : '') .

	//set attributes for all transitions
	$t .
'>';
	
	$div = '';

	if (isset($settings['layout_width']) && $settings['layout_width']==='fixed' || (isset($settings['transition_active']) && !empty($settings['transition_active'])) || (isset($settings['transitions']) && !empty($settings['transitions']))) {
		$div .= '<div';
	}

	if (isset($settings['layout_width']) && $settings['layout_width']==='fixed') { 
		$div .= ' class="container"';
	}

	if (isset($settings['transition_active']) && !empty($settings['transition_active']) && isset($settings['transitions']) && !empty($settings['transitions'])) { 
		$div .= ' style="display:none;"';
	}

	if (isset($settings['layout_width']) && $settings['layout_width']==='fixed' || (isset($settings['transition_active']) && !empty($settings['transition_active'])) || (isset($settings['transitions']) && !empty($settings['transitions']))) {
		$div .= '>';
	}

	echo $div;
