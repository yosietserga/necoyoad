<div class="row"><label for="q">Filtrar listado de productos:</label><input type="text" value="" name="q" id="q" /></div><div class="clear"></div><br />
<ul id="adds">
<?php foreach ($products as $product) { ?>
    <?php if (!empty($products_by_category) && in_array($product->product_id,$products_by_category)) {
        $class = 'added';
        $value = 1;
    } else {
        $class = 'add';
        $value = 0;
    } ?>
    <li>
        <img src="<?php echo $Image::resizeAndSave($product->pimage,50,50); ?>" alt="<?php $product->pname; ?>" />
        <b class="<?php echo $class; ?>"><?php echo $product->pname; ?></b>
        <input type="hidden" name="Products[<?php echo $product->product_id; ?>]" value="<?php echo $value; ?>" />
    </li>
<?php } ?>
</ul>