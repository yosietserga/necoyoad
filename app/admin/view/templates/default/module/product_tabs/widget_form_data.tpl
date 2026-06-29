<div class="row">
    <label for="<?php echo $name; ?>SettingsClass"><?php echo $l('entry_class'); ?></label>
    <input id="<?php echo $name; ?>SettingsClass" name="Widgets[<?php echo $name; ?>][settings][class]" value="<?php echo isset($settings['class']) ? $settings['class'] : ''; ?>" />
</div>


<div class="row">
    <label for="<?php echo $name; ?>SettingsLimit"><?php echo $l('entry_limit'); ?></label>
    <input id="<?php echo $name; ?>SettingsLimit" name="Widgets[<?php echo $name; ?>][settings][limit]" value="<?php echo isset($settings['limit']) ? (int)$settings['limit'] : 4; ?>" />
</div>

<?php foreach ($settings['tabs'] as $k => $tab) { ?>

<div class="row">
    <label for="<?php echo $name; ?>SettingsTabs<?php echo $k; ?>Name"><?php echo $l('Tab Label'); ?></label>
    <input id="<?php echo $name; ?>SettingsTabs<?php echo $k; ?>Name" name="Widgets[<?php echo $name; ?>][settings][tabs][<?php echo $k; ?>][name]" value="<?php echo isset($tab['name']) ? $tab['name'] : 'Tab Label'; ?>" />
</div>

<!--
<div class="row">
    <label for="<?php echo $name; ?>SettingsTabs<?php echo $k; ?>ObjectType"><?php echo $l('Object Type'); ?></label>
    <select id="<?php echo $name; ?>SettingsTabs<?php echo $k; ?>ObjectType" name="Widgets[<?php echo $name; ?>][settings][tabs][<?php echo $k; ?>][object_type]">
        <option value="random"<?php if ($tab['object_type'] == 'product') echo ' selected="selected"'; ?>>Productos</option>
        <option value="latest"<?php if ($tab['object_type'] == 'post') echo ' selected="selected"'; ?>>Art&iacute;culos</option>
        <option value="featured"<?php if ($tab['object_type'] == 'page') echo ' selected="selected"'; ?>>P&aacute;ginas</option>
    </select>
</div>
-->

<div class="row">
    <label><?php echo $l('entry_module'); ?></label>
    <select name="Widgets[<?php echo $name; ?>][settings][tabs][<?php echo $k; ?>][module]">
        <option value="random"<?php if ($tab['module'] == 'random') echo ' selected="selected"'; ?>>Aleatorio</option>
        <option value="latest"<?php if ($tab['module'] == 'latest') echo ' selected="selected"'; ?>>Recientes</option>
        <option value="featured"<?php if ($tab['module'] == 'featured') echo ' selected="selected"'; ?>>Populares</option>
        <option value="special"<?php if ($tab['module'] == 'special') echo ' selected="selected"'; ?>>En Ofertas</option>
        <option value="bestseller"<?php if ($tab['module'] == 'bestseller') echo ' selected="selected"'; ?>>M&aacute;s Vendidos</option>
        <option value="recommended"<?php if ($tab['module'] == 'recommended') echo ' selected="selected"'; ?>>Recomendados</option>
        <option value="related"<?php if ($tab['module'] == 'related') echo ' selected="selected"'; ?>>Relacionados</option>
    </select>
</div>

<div class="row">
    <label><?php echo $l('View'); ?></label>
    <select name="Widgets[<?php echo $name; ?>][settings][view]">
        <?php foreach ($views as $view) { ?>

        <option value="<?php echo $view; ?>"<?php if ($view == $settings['view']) { ?> selected="selected"<?php } ?>><?php echo $view; ?></option>
        <?php } ?>
    </select>
</div>

<?php if ($tab['categories']) { ?>
<div class="row">
    <label><?php echo $l('Select Categories'); ?></label>
    <ul class="scrollbox" data-scrollbox="1">
        <?php echo $tab['categories']; ?>
    </ul>
</div>
<?php } ?>

<?php if ($tab['manufacturers']) { ?>
<div class="row">
    <label><?php echo $l('Select Manufacturer'); ?></label>
    <ul class="scrollbox" data-scrollbox="1">
        <?php echo $tab['manufacturers']; ?>
    </ul>
</div>
<?php } ?>

<?php } ?>
