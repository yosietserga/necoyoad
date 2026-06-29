<!-- /cart-widget-content -->
<div class="widget-content dd" id="<?php echo $widgetName; ?>Content">
    <b class="dd-trigger">
        <i class="fas fa-shopping-cart fa-2x"></i>
    </b>

    <div class="dd-content">
    <?php if ($products) { ?>
        <?php foreach ($products as $product) { ?>
        <div class="cartProduct">
            <span class="cartRemove" id="remove_<?php echo $product['key']; ?>"></span>
            <?php echo $product['quantity']; ?> x
            <a title="<?php echo $product['name']; ?>" href="<?php echo str_replace('&', '&amp;', $product['href']); ?>">
                <?php echo substr($product['name'],0,30).'...'; ?>
            </a>
            <?php foreach ($product['option'] as $option) { ?>
            <div class="cartProductOption">- <?php echo $option['name']; ?> <?php echo $option['value']; ?></div>
            <?php } ?>
        </div>
        <?php } ?>

        <?php foreach ($totals as $total) { ?>
        <div class="cartWidgetTotal"><?php echo $total['title']; ?></div>
        <div class="cartWidgetTotal"><?php echo $total['text']; ?></div>
        <?php } ?>

        <div class="cartLinks">
            <a title="<?php echo $l('text_view'); ?>" href="<?php echo $Url::createUrl('checkout/cart'); ?>">
                <?php echo $l('View Cart'); ?>
            </a>
            <a title="<?php echo $l('text_checkout'); ?>" href="<?php echo $Url::createUrl('checkout/confirm'); ?>">
                <?php echo $l('Get Quote'); ?>
            </a>
        </div>
        <?php } else { ?>
        <div style="text-align: center;"><?php echo $l('Cart Empty'); ?></div>
    <?php } ?>
    </div>
</div>

<script>
    window.nt.customer = window.nt.customer || {};
    window.nt.cart = window.nt.cart || {};

    window.nt.customer.isLogged = <?php echo (int)$this->customer->isLogged(); ?>;

</script>
<!-- /cart-widget-content -->