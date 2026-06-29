<div class="row">
    <label><?php echo $l('Internal Name'); ?></label>
    <input type="text" onchange="updateColUI('<?php echo $name; ?>');jQuery('#<?php echo $name; ?> .internal_name').html(this.value);" name="internal_name" value="<?php if (isset($column['settings']['internal_name'])) echo $column['settings']['internal_name']; ?>" />
</div>

<div class="row">
    <label for="<?php echo $name; ?>SettingsShowonmobile"><?php echo $l('Show On Mobile'); ?></label>
    <div class="checkbox">
        <input id="<?php echo $name; ?>SettingsShowonmobile" type="checkbox" onchange="updateColUI('<?php echo $name; ?>')" name="show_in_mobile" <?php if (isset($column['settings']['show_in_mobile']) && $column['settings']['show_in_mobile'] != 'off') echo ' checked="checked"'; ?> />
        <span></span>
    </div>
</div>

<div class="row">
    <label for="<?php echo $name; ?>SettingsShowontablet"><?php echo $l('Show On Tablets'); ?></label>
    <div class="checkbox">
        <input id="<?php echo $name; ?>SettingsShowontablet" type="checkbox" onchange="updateColUI('<?php echo $name; ?>')" name="show_in_tablet" <?php if (isset($column['settings']['show_in_tablet']) && $column['settings']['show_in_tablet'] != 'off') echo ' checked="checked"'; ?> />
        <span></span>
    </div>
</div>

<div class="row">
    <label for="<?php echo $name; ?>SettingsShowondesktop"><?php echo $l('Show On Desktop'); ?></label>
    <div class="checkbox">
        <input id="<?php echo $name; ?>SettingsShowondesktop" type="checkbox" onchange="updateColUI('<?php echo $name; ?>')" name="show_in_desktop" <?php if (isset($column['settings']['show_in_desktop']) && $column['settings']['show_in_desktop'] != 'off') echo ' checked="checked"'; ?> />
        <span></span>
    </div>
</div>

<div class="row">
    <label for="<?php echo $name; ?>SettingsShowonfacebook"><?php echo $l('Show On Facebook'); ?></label>
    <div class="checkbox">
        <input id="<?php echo $name; ?>SettingsShowonfacebook" type="checkbox" onchange="updateColUI('<?php echo $name; ?>')" name="show_in_facebook" <?php if (isset($column['settings']['show_in_facebook']) && $column['settings']['show_in_facebook'] != 'off') echo ' checked="checked"'; ?> />
        <span></span>
    </div>
</div>

<div class="row">
    <label for="<?php echo $name; ?>SettingsSticky"><?php echo $l('Sticky'); ?></label>
    <div class="checkbox">
        <input id="<?php echo $name; ?>SettingsSticky" type="checkbox" onchange="updateColUI('<?php echo $name; ?>')" name="sticky" value="1"<?php if (isset($column['settings']['sticky']) && !empty($column['settings']['sticky'])) echo ' checked="checked"'; ?> />
        <span></span>
    </div>
</div>

<div class="row">
    <label><?php echo $l('Layout Width'); ?></label>
    <select name="layout_width" onchange="updateColUI('<?php echo $name; ?>')">
        <option value="fluid"<?php if ('fluid' === $column['settings']['layout_width']) { ?> selected="selected"<?php } ?>><?php echo $l('Fluid'); ?></option>
        <option value="fixed"<?php if ('fixed' === $column['settings']['layout_width']) { ?> selected="selected"<?php } ?>><?php echo $l('Fixed'); ?></option>
    </select>
</div>

<?php $grids = range(1,12); ?>

<div class="row">
    <label><?php echo $l('Grid Large'); ?></label>
    <select name="grid_large" onchange="updateColUI('<?php echo $name; ?>')">
        <?php foreach ($grids as $g) { ?>
        <option value="<?php echo $g; ?>"<?php echo ($column['settings']['grid_large'] == $g || (!$column['settings']['grid_large'] && $g==12)) ? ' selected="selected"' : ''; ?>>large-<?php echo $g; ?></option>
        <?php } ?>
    </select>
</div>

<div class="row">
    <label><?php echo $l('Grid Medium'); ?></label>
    <select name="grid_medium" onchange="updateColUI('<?php echo $name; ?>')">
        <?php foreach ($grids as $g) { ?>
        <option value="<?php echo $g; ?>"<?php echo ($column['settings']['grid_medium'] == $g || (!$column['settings']['grid_medium'] && $g==12)) ? ' selected="selected"' : ''; ?>>medium-<?php echo $g; ?></option>
        <?php } ?>
    </select>
</div>

<div class="row">
    <label><?php echo $l('Grid Small'); ?></label>
    <select name="grid_small" onchange="updateColUI('<?php echo $name; ?>')">
        <?php foreach ($grids as $g) { ?>
        <option value="<?php echo $g; ?>"<?php echo ($column['settings']['grid_small'] == $g || (!$column['settings']['grid_small'] && $g==12)) ? ' selected="selected"' : ''; ?>>small-<?php echo $g; ?></option>
        <?php } ?>
    </select>
</div>

<div class="row">
    <label><?php echo $l('Customer Session Mode'); ?></label>
    <select name="customer_session_mode" onchange="updateColUI('<?php echo $name; ?>')">
        <option value="any"<?php if (isset($column['settings']['customer_session_mode']) && 'any' === $column['settings']['customer_session_mode']) { ?> selected="selected"<?php } ?>><?php echo $l('Any'); ?></option>
        <option value="logon"<?php if (isset($column['settings']['customer_session_mode']) && 'logon' === $column['settings']['customer_session_mode']) { ?> selected="selected"<?php } ?>><?php echo $l('Needs to be Log On'); ?></option>
        <option value="logoff"<?php if (isset($column['settings']['customer_session_mode']) && 'logoff' === $column['settings']['customer_session_mode']) { ?> selected="selected"<?php } ?>><?php echo $l('Needs to be Log Off'); ?></option>
    </select>
</div>

<div class="row">
    <label><?php echo $l('Logic Conditions Action'); ?></label>
    <select name="conditional_logic_action" onchange="updateColUI('<?php echo $name; ?>')">
        <option value="show"<?php if (isset($column['settings']['conditional_logic_action']) && 'show' === $column['settings']['conditional_logic_action']) { ?> selected="selected"<?php } ?>><?php echo $l('Show'); ?></option>
        <option value="hide"<?php if (isset($column['settings']['conditional_logic_action']) && 'hide' === $column['settings']['conditional_logic_action']) { ?> selected="selected"<?php } ?>><?php echo $l('Hide'); ?></option>
    </select>
</div>

<div class="row">
    <label for="<?php echo $name; ?>SettingsConditional_logic_when_route_contains"><?php echo $l('When Route contains:'); ?></label>
    <input id="<?php echo $name; ?>SettingsConditional_logic_when_route_contains" type="text" onchange="updateColUI('<?php echo $name; ?>')" name="conditional_logic_when_route_contains" value="<?php if (isset($column['settings']['conditional_logic_when_route_contains'])) echo $column['settings']['conditional_logic_when_route_contains']; ?>" />
</div>
