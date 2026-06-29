<div class="row">
    <label for="<?php echo $name; ?>SettingsClass"><?php echo $l('entry_class'); ?></label>
    <input id="<?php echo $name; ?>SettingsClass" name="Widgets[<?php echo $name; ?>][settings][class]" value="<?php echo isset($settings['class']) ? $settings['class'] : ''; ?>" />
</div>

<div class="row">
    <label><?php echo $l('entry_module'); ?></label>
    <select name="Widgets[<?php echo $name; ?>][settings][module]">
        <option value="random"<?php if ($settings['module'] == 'random') echo ' selected="selected"'; ?>>Productos Aleatorios</option>
        <option value="latest"<?php if ($settings['module'] == 'latest') echo ' selected="selected"'; ?>>Productos Recientes</option>
        <option value="featured"<?php if ($settings['module'] == 'featured') echo ' selected="selected"'; ?>>Productos Populares</option>
        <option value="special"<?php if ($settings['module'] == 'special') echo ' selected="selected"'; ?>>Productos En Ofertas</option>
        <option value="bestseller"<?php if ($settings['module'] == 'bestseller') echo ' selected="selected"'; ?>>Productos M&aacute;s Vendidos</option>
        <option value="recommended"<?php if ($settings['module'] == 'recommended') echo ' selected="selected"'; ?>>Productos Recomendados</option>
        <option value="related"<?php if ($settings['module'] == 'related') echo ' selected="selected"'; ?>>Productos Relacionados</option>
    </select>
</div>

<div class="row">
    <label for="<?php echo $name; ?>SettingsLimit"><?php echo $l('entry_limit'); ?></label>
    <input id="<?php echo $name; ?>SettingsLimit" name="Widgets[<?php echo $name; ?>][settings][limit]" value="<?php echo isset($settings['limit']) ? (int)$settings['limit'] : 4; ?>" />
</div>

<div class="row">
    <label for="<?php echo $name; ?>SettingsShowSearchResults"><?php echo $l('Show Search Results'); ?></label>
    <div class="checkbox">
        <input id="<?php echo $name; ?>SettingsShowSearchResults" type="checkbox" name="Widgets[<?php echo $name; ?>][settings][show_search_results]" value="1" />
        <span></span>
    </div>
</div>

<div class="row">
    <label for="<?php echo $name; ?>SettingsShowPagination"><?php echo $l('Show Pagination'); ?></label>
    <div class="checkbox">
        <input id="<?php echo $name; ?>SettingsShowPagination" type="checkbox" name="Widgets[<?php echo $name; ?>][settings][show_pagination]" value="1" />
        <span></span>
    </div>
</div>

<div class="row">
    <label for="<?php echo $name; ?>SettingsEndlessScroll"><?php echo $l('Endless Scroll'); ?></label>
    <div class="checkbox">
        <input id="<?php echo $name; ?>SettingsEndlessScroll" type="checkbox" name="Widgets[<?php echo $name; ?>][settings][endless_scroll]" value="1" />
        <span></span>
    </div>
</div>

<?php if ($categories) { ?>
<div class="row">
    <label><?php echo $l('Select Categories'); ?></label>
    <ul class="scrollbox" data-scrollbox="1">
        <?php echo $categories; ?>
    </ul>
</div>
<?php } ?>
