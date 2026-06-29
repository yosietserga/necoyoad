<?php foreach ($productos as $producto) { ?>
<div style="float:left;border:solid 1px #666;width:150px;display:block;text-align:center">
    <?php echo $producto['name']; ?>
    <?php echo $producto['image']; ?>
    <input type="hidden" name="<?php echo $producto['product_id']; ?>" value="<?php echo $producto['product_id']; ?>">
</div>
<?php } ?>
