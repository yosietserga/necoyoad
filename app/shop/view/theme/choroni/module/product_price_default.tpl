<?php if ($display_price && $Config->get('config_store_mode')== 'store') { ?>
<div class="offers" itemprop="offers" itemscope itemtype="https://schema.org/Offer">
<?php if (!$special) { ?>
    <span itemprop="price" class="price nt-editable" id="<?php echo $widgetName; ?>_productPrice"><?php echo $price; ?></span>
<?php } else { ?>
    <span itemprop="price" class="price nt-editable" id="<?php echo $widgetName; ?>_productPrice"><?php echo $special; ?></span>
    <span itemprop="price" class="old_price nt-editable" id="<?php echo $widgetName; ?>_productOldPrice"><?php echo $price; ?></span>
<?php } ?>
</div>
<?php } ?>