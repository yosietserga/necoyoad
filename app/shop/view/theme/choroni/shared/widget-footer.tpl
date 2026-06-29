<?php
	if (isset($settings['layout_width']) && $settings['layout_width']==='fixed' || (isset($settings['transition_active']) && !empty($settings['transition_active']) && isset($settings['transition_effect_in']) && !empty($settings['transition_effect_in']))) { 
		echo '</div>';
	}
?>
</li>