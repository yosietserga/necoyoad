
<div class="row">
    <label for="<?php echo $name; ?>SettingsTransitionActivate"><?php echo $l('Activate Transition'); ?></label>
    <div class="checkbox">
        <input id="<?php echo $name; ?>SettingsTransitionActivate" type="checkbox" name="Widgets[<?php echo $name; ?>][settings][transition_active]" value="1"<?php if (isset($settings['transition_active']) && !empty($settings['transition_active'])) echo ' checked="checked"'; ?> />
        <span></span>
    </div>
</div>

<div class="row">
    <label for="<?php echo $name; ?>SettingsTransitionRepeat"><?php echo $l('Repeat Forever'); ?></label>
    <div class="checkbox">
        <input id="<?php echo $name; ?>SettingsTransitionRepeat" type="checkbox" name="Widgets[<?php echo $name; ?>][settings][transition_repeat]" value="1"<?php if (isset($settings['transition_repeat']) && !empty($settings['transition_repeat'])) echo ' checked="checked"'; ?> />
        <span></span>
    </div>
</div>

<div class="row">
    <label for="<?php echo $name; ?>SettingsTransitionAsync"><?php echo $l('Async'); ?></label>
    <div class="checkbox">
        <input id="<?php echo $name; ?>SettingsTransitionAsync" type="checkbox" name="Widgets[<?php echo $name; ?>][settings][transition_async]" value="1"<?php if (isset($settings['transition_async']) && !empty($settings['transition_async'])) echo ' checked="checked"'; ?> />
        <span></span>
    </div>
</div>


<div class="row">
    <div class="grid_12">
        <h2><?php echo $l('Transitions'); ?></h2>
        <span class="button" onclick="addWidgetTransition('<?php echo $name; ?>');return false;"><?php echo $l('Add New Transition'); ?></span>
    </div>
</div>

<div id="<?php echo $name; ?>TransitionsWrapper">
    <?php if (isset($settings['transitions']) && !empty($settings['transitions'])) { ?>
    <?php foreach($settings['transitions'] as $key => $transition) { ?>
    <div class="row">
        <small style="float:left;" class="colMove">
            <i class="fa fa-arrows fa-lg"></i>
        </small>
        <small style="float:right;margin-right:50px;" class="deleteTransition" onclick="deleteTransition(this, '<?php echo $name; ?>')">
            <i class="fa fa-times fa-lg"></i>
        </small>
        <div class="grid_3">
            <label><?php echo $l('Delay (Seconds)'); ?></label>
            <input name="Widgets[<?php echo $name; ?>][settings][transitions][<?php echo $key; ?>][delay]" value="<?php echo (int)$transition['delay']; ?>" />
        </div>

        <div class="grid_3">
            <label><?php echo $l('Duration (Seconds)'); ?></label>
            <input name="Widgets[<?php echo $name; ?>][settings][transitions][<?php echo $key; ?>][duration]" value="<?php echo (int)$transition['duration']; ?>" />
        </div>

        <div class="grid_4">
            <label><?php echo $l('Effect'); ?></label>
            <select name="Widgets[<?php echo $name; ?>][settings][transitions][<?php echo $key; ?>][effect]">
                <option value=""><?php echo $l('Select one'); ?></option>

                <?php foreach ($transition_effects['animate.css'] as $engine=>$effects) { ?>
                <optgroup label="<?php echo $engine; ?>">

                    <?php foreach ($effects as $k=>$v) { ?>
                    <option value="<?php echo $v; ?>"<?php if (isset($transition['effect']) && $v === $transition['effect']) { ?> selected="selected"<?php } ?>><?php echo $k; ?></option>
                    <?php } ?>

                </optgroup>
                <?php } ?>
            </select>
        </div>

        <input type="hidden" name="Widgets[<?php echo $name; ?>][settings][transitions][<?php echo $key; ?>][order]" value="<?php echo $key; ?>" />
    </div>
    <?php } ?>
    <?php } ?>
</div>

<?php if (isset($transition_effects) && !empty($transition_effects)) { ?>
<script>

    if (typeof window['transition_effects'] == 'undefined' || !window['transition_effects']) {
        window['transition_effects'] = <?php echo json_encode($transition_effects); ?>;
    }
</script>
<?php } ?>