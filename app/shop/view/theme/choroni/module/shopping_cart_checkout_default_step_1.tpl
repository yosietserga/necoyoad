<?php $tpl = is_dir(DIR_TEMPLATE. $this->config->get('config_template') ."/shared") ? $this->config->get('config_template') : "choroni"; ?> 
<!-- recipe-info -->
<?php if ($display_price && $Config->get('config_store_mode') === 'store') { ?>
<section id="necoWizardStep_1" class="store" data-wizard="step">
<?php } else { ?>
<section id="necoWizardStep_1" class="not-store" data-wizard="step">
<?php } ?>

    <div class="cart-detail">
        <table class="cart-recipe">
            <thead>
                <tr>
                    <th><?php echo $l('column_image'); ?></th>
                    <th><?php echo $l('column_name'); ?></th>
                    <th><?php echo $l('column_model'); ?></th>
                    <th><?php echo $l('column_quantity'); ?></th>
                    <?php if ($display_price && $Config->get('config_store_mode') === 'store') { ?>
                    <th><?php echo $l('column_price'); ?></th>
                    <th><?php echo $l('column_total'); ?></th>
                    <?php } ?>
                    <th></th>
                </tr>
            </thead>

            <tbody>
            <?php foreach ($products as $product) { ?>
            <tr>
                <td>
                    <a title="<?php echo $product['name']; ?>" href="<?php echo str_replace('&', '&amp;', $product['href']); ?>">
                        <img src="<?php echo $product['thumb']; ?>" alt="<?php echo $product['name']; ?>" />
                    </a>
                </td>

                <td>
                    <a title="<?php echo $product['name']; ?>" href="<?php echo str_replace('&', '&amp;', $product['href']); ?>">
                        <?php echo $product['name']; ?>
                    </a>
                    <div>
                        <?php foreach ($product['option'] as $option) { ?>
                        - <small><?php echo $option['name']; ?> <?php echo $option['value']; ?></small>
                        <?php } ?>
                    </div>
                </td>

                <td data-label="<?php echo $l('column_model'); ?>"><?php echo $product['model']; ?></td>

                <td data-label="<?php echo $l('column_quantity'); ?>">
                    <input type="text" name="quantity[<?php echo $product['key']; ?>]" value="<?php echo $product['quantity']; ?>" showquick="off" size="3"  onchange="refreshCart(this,'<?php echo $product['key']; ?>')" />

                    <a class="update-product" onclick="refreshCart(this,'<?php echo $product['key']; ?>')" title="<?php echo $l('text_update'); ?>">
                    </a>
                </td>
                <?php if ($display_price && $Config->get('config_store_mode') === 'store') { ?>
                <td data-label="<?php echo $l('column_price'); ?>"><?php echo $product['price']; ?></td>
                <td data-label="<?php echo $l('column_total'); ?>"><?php echo $product['total']; ?></td>
                <?php } ?>
                <td>
                    <a class="delete-product" onclick="deleteCart(this,'<?php echo $product['key']; ?>')" title="<?php echo $l('text_delete'); ?>">
                        <i class="fa fa-close"></i>
                    </a>
                </td>
            </tr>
            <?php } ?>
            </tbody>
        </table>
        <?php if ($display_price && $Config->get('config_store_mode')=='store') { ?>
        <table id="totals" class="cart-totals">
            <?php foreach ($totals as $total) { ?>
            <tr>
                <td><?php echo $total['title']; ?></td>
                <td><?php echo $total['text']; ?></td>
            </tr>
            <?php } ?>
        </table>
        <?php } ?>

        <div class="clear"></div>

    </div>

    <div class="coupon-wrapper">
        <?php include(DIR_TEMPLATE. $tpl ."/shared/fields/coupon.tpl"); ?>
    </div>
</section>
<!-- /recipe info -->