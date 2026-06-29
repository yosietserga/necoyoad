<!-- catalog  picture -->

<!--<?php echo $product['thumb']; ?>-->
<figure class="picture">
    <a href="<?php echo $Url::createUrl('store/product', array('product_id'=>$product['product_id'])); ?>" class="thumb" title="<?php echo $product['name']; ?>">
        <img src="<?php echo $product['thumb']; ?>" alt="<?php echo $product['name']; ?>"/>
    </a>
    <a href="javascript:;" class="quick-view" onclick="return quickView('product', '<?php echo $product['product_id']; ?>', this);"><?php echo $l('text_quick_view'); ?>
    </a>
    <?php include(DIR_TEMPLATE. $this->config->get('config_template') ."/shared/product/sticker.tpl"); ?>
</figure>
<!--/catalog -picture-->