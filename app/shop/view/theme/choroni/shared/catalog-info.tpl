<!-- catalog-info -->
<div class="info nt-hoverdir">

    <div class="rating">
        <img src="<?php echo HTTP_IMAGE; ?>stars_<?php echo (int)$product['rating'] . '.png'; ?>" alt="<?php echo $product['stars']; ?>" />
    </div>

    <a href="<?php echo $Url::createUrl('store/product',array('product_id'=>$product['product_id'])); ?>" title="<?php echo $product['name']; ?>" class="name">
        <?php echo $product['name']; ?>
    </a>

    <p class="model">
        <?php echo $product['model']; ?>
    </p>

    <p class="overview"><?php echo substr($product['overview'],0,100); ?></p>

    <?php if (isset($product['price']) && $display_price && $Config->get('config_store_mode')=='store') { ?>
    <p class="price"><?php echo $product['price']; ?></p>
    <?php } ?>

    <div class="group-btn">

        <div class="btn btn-detail">
            <a href="<?php echo $Url::createUrl('store/product',array('product_id'=>$product['product_id'])); ?>"><?php echo $l('button_see_product'); ?></a>
        </div>

        <?php if ($Config->get('config_store_mode') === 'store') { ?>
        <div class="btn btn-add" data-action="addToCart" role="button" aria-label="AddToCart">
            <a title="<?php echo $l('button_add_to_cart'); ?>" class="action-add" <?php if (!$this->config->get("cart_ajax")) { ?>href="<?php echo $Url::createUrl('checkout/cart',array('product_id'=>$product['product_id'])); ?>"<?php } else { ?>onclick="addToCart('<?php echo $product['product_id']; ?>')"<?php } ?>>
                <?php echo $l('Add To Cart'); ?>
            </a>
        </div>
        <?php } ?>
    </div>
</div>
<!-- /catalog-info -->

