<?php $tpl = is_dir(DIR_TEMPLATE. $this->config->get('config_template') ."/shared") ? $this->config->get('config_template') : "choroni"; ?> 
<!-- catalog-latest -->
<?php if($products) { ?>
<li nt-editable="1" class="widget-product-list widget-product-list-<?php echo $settings['view']; ?> widget-product-list-home productListWidget<?php echo ($settings['class']) ? ' ' .$settings['class'] : ''; ?>" id="<?php echo $widgetName; ?>">

<?php include(DIR_TEMPLATE. $tpl ."/shared/module-heading.tpl");?>
<?php include(DIR_TEMPLATE. $tpl ."/shared/sort.tpl"); ?>
<?php include(DIR_TEMPLATE. $tpl ."/shared/pagination.tpl"); ?>
<?php include(DIR_TEMPLATE. $tpl ."/shared/blockgrid-start.tpl"); ?>
<?php foreach($products as $product) { ?>
<li class="catalog-item">
    <?php include(DIR_TEMPLATE. $tpl ."/shared/catalog-picture.tpl"); ?>
    <?php include(DIR_TEMPLATE. $tpl ."/shared/catalog-info.tpl"); ?>
</li>
<?php } ?>

<?php include(DIR_TEMPLATE. $tpl ."/shared/blockgrid-end.tpl"); ?>
<?php include(DIR_TEMPLATE. $tpl ."/shared/pagination.tpl"); ?>
</li>
<!-- /catalog -->
<?php include(DIR_TEMPLATE. $tpl ."/shared/scripts/quickview-deps.tpl"); ?>
<?php } ?>
