<div class="clear"></div>

<?php if (!isset($object_category)) throw new Exception("Must declare variable object_category before include categories form partial"); ?>
<?php if (isset($categories) && is_array($categories) && !empty($categories)) { ?>
<div class="clear"></div>
<div class="row">
    <label><?php echo $l('Categories'); ?></label><br />
    <div class="clear"></div>
    <input type="text" title="Filtrar listado de catgorías" value="" name="q" id="q" placeholder="Filtrar categorías" />
    <div class="clear"></div>
    <a onclick="$('#categoriesWrapper input[type=checkbox]').attr('checked','checked');">Seleccionar Todos</a>&nbsp;&nbsp;&nbsp;&nbsp;
    <a onclick="$('#categoriesWrapper input[type=checkbox]').removeAttr('checked');">Seleccionar Ninguno</a>
    <div class="clear"></div>
    
    <ul id="categoriesWrapper" class="scrollbox necoCategory">
        <?php foreach ($categories as $category) { ?>
        <li class="categories">
            <input id="scrollboxCategories<?php echo (int)$category['category_id']; ?>" title="<?php echo $l('help_category'); ?>" type="checkbox" name="categories[]" value="<?php echo $category['category_id']; ?>"<?php if (in_array($category['category_id'], $object_category)) { ?> checked="checked"<?php } ?> showquick="off" />
            <label for="scrollboxCategories<?php echo (int)$category['category_id']; ?>"><?php echo $category['title']; ?></label>
        </li>
        <?php } ?>
    </ul>
</div> 
<?php } ?>
