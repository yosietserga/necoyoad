<div class="row">
    <label><?php echo $l('Internal Name'); ?></label>
    <input type="text" onchange="updateRowUI('<?php echo $name; ?>');jQuery('#<?php echo $name; ?> .internal_name').html(this.value);" name="internal_name" value="<?php if (isset($row['settings']['internal_name'])) echo $row['settings']['internal_name']; ?>" />
</div>

<div class="row">
    <label for="<?php echo $name; ?>SettingsShowonmobile"><?php echo $l('Show On Mobile'); ?></label>
    <div class="checkbox">
        <input id="<?php echo $name; ?>SettingsShowonmobile" type="checkbox" onchange="updateRowUI('<?php echo $name; ?>')" name="show_in_mobile" <?php if (isset($row['settings']['show_in_mobile']) && $row['settings']['show_in_mobile'] != 'off') echo ' checked="checked"'; ?> />
        <span></span>
    </div>
</div>

<div class="row">
    <label for="<?php echo $name; ?>SettingsShowontablet"><?php echo $l('Show On Tablets'); ?></label>
    <div class="checkbox">
        <input id="<?php echo $name; ?>SettingsShowontablet" type="checkbox" onchange="updateRowUI('<?php echo $name; ?>')" name="show_in_tablet" <?php if (isset($row['settings']['show_in_tablet']) && $row['settings']['show_in_tablet'] != 'off') echo ' checked="checked"'; ?> />
        <span></span>
    </div>
</div>

<div class="row">
    <label for="<?php echo $name; ?>SettingsShowondesktop"><?php echo $l('Show On Desktop'); ?></label>
    <div class="checkbox">
        <input id="<?php echo $name; ?>SettingsShowondesktop" type="checkbox" onchange="updateRowUI('<?php echo $name; ?>')" name="show_in_desktop" <?php if (isset($row['settings']['show_in_desktop']) && $row['settings']['show_in_desktop'] != 'off') echo ' checked="checked"'; ?> />
        <span></span>
    </div>
</div>

<div class="row">
    <label for="<?php echo $name; ?>SettingsShowonfacebook"><?php echo $l('Show On Facebook'); ?></label>
    <div class="checkbox">
        <input id="<?php echo $name; ?>SettingsShowonfacebook" type="checkbox" onchange="updateRowUI('<?php echo $name; ?>')" name="show_in_facebook" <?php if (isset($row['settings']['show_in_facebook']) && $row['settings']['show_in_facebook'] != 'off') echo ' checked="checked"'; ?> />
        <span></span>
    </div>
</div>

<div class="row">
    <label for="<?php echo $name; ?>SettingsSticky"><?php echo $l('Sticky'); ?></label>
    <div class="checkbox">
        <input id="<?php echo $name; ?>SettingsSticky" type="checkbox" onchange="updateRowUI('<?php echo $name; ?>')" name="sticky" value="1"<?php if (isset($row['settings']['sticky']) && !empty($row['settings']['sticky'])) echo ' checked="checked"'; ?> />
        <span></span>
    </div>
</div>

<div class="row">
    <label><?php echo $l('Layout Width'); ?></label>
    <select name="layout_width" onchange="updateRowUI('<?php echo $name; ?>')">
        <option value="fluid"<?php if ('fluid' === $row['settings']['layout_width']) { ?> selected="selected"<?php } ?>><?php echo $l('Fluid'); ?></option>
        <option value="fixed"<?php if ('fixed' === $row['settings']['layout_width']) { ?> selected="selected"<?php } ?>><?php echo $l('Fixed'); ?></option>
    </select>
</div>

<div class="row">
    <label><?php echo $l('Customer Session Mode'); ?></label>
    <select name="customer_session_mode" onchange="updateRowUI('<?php echo $name; ?>')">
        <option value="any"<?php if (isset($row['settings']['customer_session_mode']) && 'any' === $row['settings']['customer_session_mode']) { ?> selected="selected"<?php } ?>><?php echo $l('Any'); ?></option>
        <option value="logon"<?php if (isset($row['settings']['customer_session_mode']) && 'logon' === $row['settings']['customer_session_mode']) { ?> selected="selected"<?php } ?>><?php echo $l('Needs to be Log On'); ?></option>
        <option value="logoff"<?php if (isset($row['settings']['customer_session_mode']) && 'logoff' === $row['settings']['customer_session_mode']) { ?> selected="selected"<?php } ?>><?php echo $l('Needs to be Log Off'); ?></option>
    </select>
</div>


<div class="row">
    <label><?php echo $l('Logic Conditions Action'); ?></label>
    <select name="conditional_logic_action" onchange="updateRowUI('<?php echo $name; ?>')">
        <option value="show"<?php if (isset($row['settings']['conditional_logic_action']) && 'show' === $row['settings']['conditional_logic_action']) { ?> selected="selected"<?php } ?>><?php echo $l('Show'); ?></option>
        <option value="hide"<?php if (isset($row['settings']['conditional_logic_action']) && 'hide' === $row['settings']['conditional_logic_action']) { ?> selected="selected"<?php } ?>><?php echo $l('Hide'); ?></option>
    </select>
</div>

<div class="row">
    <label><?php echo $l('When Route contains:'); ?></label>
    <input type="text" onchange="updateRowUI('<?php echo $name; ?>')" name="conditional_logic_when_route_contains" value="<?php if (isset($row['settings']['conditional_logic_when_route_contains'])) echo $row['settings']['conditional_logic_when_route_contains']; ?>" />
</div>
