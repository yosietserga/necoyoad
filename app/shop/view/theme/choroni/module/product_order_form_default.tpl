<?php $tpl = is_dir(DIR_TEMPLATE. $this->config->get('config_template') ."/shared") ? $this->config->get('config_template') : "choroni"; ?> 
<?php if ($Config->get('config_store_mode') == 'store') { ?>
<form action="<?php echo str_replace('&', '&amp;', $action); ?>" method="post" enctype="multipart/form-data" id="<?php echo $widgetName; ?>_productForm">

    <!-- product-options -->
    <?php if ($options) { ?>
    <div class="options nt-editable" id="<?php echo $widgetName; ?>_productOptions">
        <!--<span><?php echo $l('text_options'); ?></span>-->
        <ul>
            <?php foreach ($options as $option) { ?>
            <li>
                <label for="?php echo $widgetName; ?>_option_<?php echo $option['option_id']; ?>" class="label"><?php echo $option['name']; ?>:</label>
                <select id="<?php echo $widgetName; ?>_option_<?php echo $option['option_id']; ?>" name="option[<?php echo $option['option_id']; ?>]">
                    <?php foreach ($option['option_value'] as $option_value) { ?>
                    <option value="<?php echo $option_value['option_value_id']; ?>"><?php echo $option_value['name']; ?>
                        <?php if ($option_value['price'] && $display_price) { ?>(<?php echo $option_value['prefix']; ?><?php echo $option_value['price']; ?>)<?php } ?>
                    </option>
                    <?php } ?>
                </select>
            </li>
            <?php } ?>
        </ul>
    </div>
    <?php } ?>
    <!-- /prdduct-options -->

    <!-- product-quantity -->
    <div class="quantity nt-editable" id="<?php echo $widgetName; ?>_productQty">
        <input type="text" id="<?php echo $widgetName; ?>_quantity" name="quantity" value="<?php echo $minimum; ?>" />
        <?php if ($minimum> 1) { ?><small><?php echo $l('text_minimum'); ?></small><?php } ?>
        <a href="javascript:;"  class="arrow-up">
            <i data-action-count="inc" class="icon">
                +
            </i>
        </a>
        <a href="javascript:;"  class="arrow-down"  >
            <i data-action-count="dec" class="icon">
               -
            </i>
        </a>
    </div>
    <input type="hidden" name="product_id" value="<?php echo $product_id; ?>" />
    <input type="hidden" name="redirect" value="<?php echo str_replace('&', '&amp;', $redirect); ?>" />

    <!--/product-quantity -->
    <div class="group group--btn" role="group">

        <div class="btn btn-add btn--secondary" data-action="addToCart" role="button" aria-label="AddToCart">
            <a title="<?php echo $l('button_add_to_cart'); ?>"<?php if (isset($settings['cart_ajax'])) { ?> onclick="addToCart('<?php echo $Url::createUrl("checkout/cart/json") .'&product_id='. $product_id; ?>', this)"<?php } else { ?> href="<?php echo $Url::createUrl("checkout/cart") .'&product_id='. $product_id; ?>" <?php } ?> id="<?php echo $widgetName; ?>_add_to_cart">
                <?php echo $l('button_add_to_cart'); ?>
            </a>
        </div>
    </div>
</form>
<?php } ?>