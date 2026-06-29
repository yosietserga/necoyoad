
<div class="row">
    <label for="<?php echo $name; ?>SettingsTitle"><?php echo $l('entry_title'); ?></label>
    <input name="Widgets[<?php echo $name; ?>][settings][title]" value="<?php echo isset($settings['title']) ? $settings['title'] : ""; ?>" />
</div>

<?php if (!isset($settings['offsetY'])) { ?>
    <div class="row">
        <label for="<?php echo $name; ?>SettingsAutoload"><?php echo $l('entry_load'); ?></label>
        <div class="checkbox">
            <input id="<?php echo $name; ?>SettingsAutoload" type="checkbox" name="Widgets[<?php echo $name; ?>][settings][autoload]" value="1"<?php if (isset($settings['autoload']) && !empty($settings['autoload'])) echo ' checked="checked"'; ?> />
            <span></span>
        </div>
    </div>

    <div class="row">
        <label for="<?php echo $name; ?>SettingsShowonmobile"><?php echo $l('Show On Mobile'); ?></label>
        <div class="checkbox">
            <input id="<?php echo $name; ?>SettingsShowonmobile" type="checkbox" name="Widgets[<?php echo $name; ?>][settings][showonmobile]" value="1"<?php if (isset($settings['showonmobile']) && !empty($settings['showonmobile'])) echo ' checked="checked"'; ?> />
            <span></span>
        </div>
    </div>

    <div class="row">
        <label for="<?php echo $name; ?>SettingsShowontablets"><?php echo $l('Show On Tablets'); ?></label>
        <div class="checkbox">
            <input id="<?php echo $name; ?>SettingsShowontablets" type="checkbox" name="Widgets[<?php echo $name; ?>][settings][showontablet]" value="1"<?php if (isset($settings['showontablet']) && !empty($settings['showontablet'])) echo ' checked="checked"'; ?> />
            <span></span>
        </div>
    </div>

    <div class="row">
        <label for="<?php echo $name; ?>SettingsShowondesktop"><?php echo $l('Show On Desktop'); ?></label>
        <div class="checkbox">
            <input id="<?php echo $name; ?>SettingsShowondesktop" type="checkbox" name="Widgets[<?php echo $name; ?>][settings][showondesktop]" value="1"<?php if (isset($settings['showondesktop']) && !empty($settings['showondesktop'])) echo ' checked="checked"'; ?> />
            <span></span>
        </div>
    </div>

    <div class="row">
        <label for="<?php echo $name; ?>SettingsShowonfacebook"><?php echo $l('Show On Facebook'); ?></label>
        <div class="checkbox">
            <input id="<?php echo $name; ?>SettingsShowonfacebook" type="checkbox" name="Widgets[<?php echo $name; ?>][settings][showonfacebook]" value="1"<?php if (isset($settings['showonfacebook']) && !empty($settings['showonfacebook'])) echo ' checked="checked"'; ?> />
            <span></span>
        </div>
    </div>

    <div class="row">
        <label for="<?php echo $name; ?>SettingsSticky"><?php echo $l('Sticky'); ?></label>
        <div class="checkbox">
            <input id="<?php echo $name; ?>SettingsSticky" type="checkbox" name="Widgets[<?php echo $name; ?>][settings][sticky]" value="1"<?php if (isset($settings['sticky']) && !empty($settings['sticky'])) echo ' checked="checked"'; ?> />
            <span></span>
        </div>
    </div>

    <div class="row">
        <label for="<?php echo $name; ?>SettingsShrinkable"><?php echo $l('Shrinkable'); ?></label>
        <div class="checkbox">
            <input id="<?php echo $name; ?>SettingsShrinkable" type="checkbox" name="Widgets[<?php echo $name; ?>][settings][shrinkable]" value="1"<?php if (isset($settings['shrinkable']) && !empty($settings['shrinkable'])) echo ' checked="checked"'; ?> />
            <span></span>
        </div>
    </div>
    
    <div class="row" id="<?php echo $name; ?>SettingsShrinkableWidthContainer">
        <label for="<?php echo $name; ?>SettingsShrinkableWidth"><?php echo $l('Shrinkable Width'); ?></label>
        <div>
            <input id="<?php echo $name; ?>SettingsShrinkableWidth" type="number" step="1" name="Widgets[<?php echo $name; ?>][settings][shrinkable_width]" value="<?php echo (isset($settings['shrinkable_width']) && !empty($settings['shrinkable_width'])) ? $settings['shrinkable_width'] : 200; ?>" />
        </div>
    </div>

    <script>
        const toggleShrinkableWidth = () => {
            console.log($("#<?php echo $name; ?>SettingsShrinkable").prop("checked"));
            if ($("#<?php echo $name; ?>SettingsShrinkable").prop("checked")) {
                $("#<?php echo $name; ?>SettingsShrinkableWidthContainer").show();
            } else {
                $("#<?php echo $name; ?>SettingsShrinkableWidthContainer").hide();
            }
        };
        toggleShrinkableWidth();
        $("#<?php echo $name; ?>SettingsShrinkable").on("click", toggleShrinkableWidth);
    </script>

    <div class="row">
        <label><?php echo $l('Layout Width'); ?></label>
        <select name="Widgets[<?php echo $name; ?>][settings][layout_width]">
            <option value="fluid"<?php if (isset($settings['layout_width']) && 'fluid' === $settings['layout_width']) { ?> selected="selected"<?php } ?>><?php echo $l('Fluid'); ?></option>
            <option value="fixed"<?php if (isset($settings['layout_width']) && 'fixed' === $settings['layout_width']) { ?> selected="selected"<?php } ?>><?php echo $l('Fixed'); ?></option>
        </select>
    </div>

    <div class="row">
        <label><?php echo $l('Customer Session Mode'); ?></label>
        <select name="Widgets[<?php echo $name; ?>][settings][customer_session_mode]">
            <option value="any"<?php if (isset($settings['customer_session_mode']) && 'any' === $settings['customer_session_mode']) { ?> selected="selected"<?php } ?>><?php echo $l('Any'); ?></option>
            <option value="logon"<?php if (isset($settings['customer_session_mode']) && 'logon' === $settings['customer_session_mode']) { ?> selected="selected"<?php } ?>><?php echo $l('Needs to be Log On'); ?></option>
            <option value="logoff"<?php if (isset($settings['customer_session_mode']) && 'logoff' === $settings['customer_session_mode']) { ?> selected="selected"<?php } ?>><?php echo $l('Needs to be Log Off'); ?></option>
        </select>
    </div>
    
    <div class="row">
        <label><?php echo $l('Logic Conditions Action'); ?></label>
        <select name="Widgets[<?php echo $name; ?>][settings][conditional_logic_action]">
            <option value="show"<?php if (isset($settings['conditional_logic_action']) && 'show' === $settings['conditional_logic_action']) { ?> selected="selected"<?php } ?>><?php echo $l('Show'); ?></option>
            <option value="hide"<?php if (isset($settings['conditional_logic_action']) && 'hide' === $settings['conditional_logic_action']) { ?> selected="selected"<?php } ?>><?php echo $l('Hide'); ?></option>
        </select>
    </div>

    <div class="row">
        <label><?php echo $l('When Route contains:'); ?></label>
        <input type="text" name="Widgets[<?php echo $name; ?>][settings][conditional_logic_when_route_contains]" value="<?php if (isset($settings['conditional_logic_when_route_contains'])) echo $settings['conditional_logic_when_route_contains']; ?>" />
    </div>

<?php } else { ?>
    <input id="<?php echo $name; ?>SettingsAutoload" type="hidden" name="Widgets[<?php echo $name; ?>][settings][autoload]" value="1" />
    <input id="<?php echo $name; ?>SettingsCustomerSessionMode" type="hidden" name="Widgets[<?php echo $name; ?>][settings][customer_session_mode]" value="any" />
    <input id="<?php echo $name; ?>SettingsShowondesktop" type="hidden" name="Widgets[<?php echo $name; ?>][settings][showondesktop]" value="on" />
    <input id="<?php echo $name; ?>SettingsShowonmobile" type="hidden" name="Widgets[<?php echo $name; ?>][settings][showonmobile]" value="on" />
    <input id="<?php echo $name; ?>SettingsShowonmobile" type="hidden" name="Widgets[<?php echo $name; ?>][settings][conditional_logic_action]" value="show" />
    <input id="<?php echo $name; ?>SettingsShowonmobile" type="hidden" name="Widgets[<?php echo $name; ?>][settings][conditional_logic_action]" value="show" />
<?php } ?>

<?php if ($views) { ?>
<div class="row">
    <label><?php echo $l('View'); ?></label>
    <select name="Widgets[<?php echo $name; ?>][settings][view]">
        <?php foreach ($views as $view) { ?>

        <option value="<?php echo $view; ?>"<?php if ($view == $settings['view']) { ?> selected="selected"<?php } ?>><?php echo $view; ?></option>
        <?php } ?>
    </select>
</div>
<?php } ?>
