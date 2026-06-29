<div id="formAttributes">
    <p>Debe seleccionar al menos una categor&iacute;a para mostrar los atributos y dicha categoría debe tener atributos asociados</p>
<hr />
    <?php /*if (isset($attributes) && is_array($attributes)) { ?>
        <?php foreach ($attributes as $k => $attr) { ?>
        <div id="product_attribute_group_id_<?php echo $k; ?>" class="product_attribute_groups" data-categories="<?php echo implode(',', $attr['categoriesAttributes']); ?>">
            <div class="clear"></div>
            <input name="categoriesAttributes" class="categoriesAttributes" type="hidden" value="<?php echo implode(',', $attr['categoriesAttributes']); ?>" />
            <h3><?php echo $attr['name']; ?></h3>

            <?php foreach ($attr['attributes'] as $key => $attribute) { ?>
            <div class="row">
                <label for="Attributes_<?php echo $k.$key; ?>"><?php echo $attribute['label']; ?></label>
                <input id="Attributes_<?php echo $k.$key; ?>" name="attributes[<?php echo $k; ?>][<?php echo $attribute['label'] .':'. $attribute['product_attribute_id']; ?>]" type="text" value="<?php echo $attribute['value']; ?>" class="neco-input-text" placeholder="<?php echo $attribute['label']; ?>" />
            </div>
            <?php } ?>
            <div class="clear"></div>
        </div>
        <?php } ?>
    <?php }*/ ?>
</div>

<div class="clear"></div>

<script>
$(function(){

    let categoriesSelected = [];
    $('.categories input').each(function(){
        if ($(this).prop('checked')) {
            categoriesSelected.push( $(this).val() );
        }
    });
    if (categoriesSelected.length>0) {
            __addAttribute(categoriesSelected.join("_"), <?php echo (int)$product_id; ?>);
    }
    

    $('.categories input').on('change',function(e){
        if ($(this).prop('checked')) {
            addAttribute(this);
        } else {
            removeAttribute(this);
        }
    });
});
</script>