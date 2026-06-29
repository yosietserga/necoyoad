<?php $tpl = (is_dir(DIR_TEMPLATE. $tpl ."/shared")) ? $tpl : "choroni"; ?> 
<!-- catalog-latest -->
<?php if($products) { ?>
<div nt-editable="1" class="widget-product-list widget-product-list-<?php echo $settings['view']; ?> widget-product-list-home productListWidget<?php echo ($settings['class']) ? ' ' .$settings['class'] : ''; ?>" id="<?php echo $widgetName; ?>Content">

    <?php include(DIR_TEMPLATE. $tpl ."/shared/module-heading.tpl");?>
    <?php include(DIR_TEMPLATE. $tpl ."/shared/blockgrid-start.tpl"); ?>

    <div id="<?php echo $widgetName; ?>Carousel" class="owl-carousel">
        <?php foreach($products as $product) { ?>
        <div>
            <?php include(DIR_TEMPLATE. $tpl ."/shared/catalog-picture.tpl"); ?>
            <?php include(DIR_TEMPLATE. $tpl ."/shared/catalog-info.tpl"); ?>
        </div>
        <?php } ?>
    </div>

    <?php include(DIR_TEMPLATE. $tpl ."/shared/blockgrid-end.tpl"); ?>
</div>
<?php } ?>

<script type="text/javascript">
    $(function(){
        $("#<?php echo $widgetName; ?>Carousel").owlCarousel({
            loop:true,
            margin:10,
            nav:true,
            autoplay:true,
            autoplayTimeout:3000,
            responsiveClass:true,
            responsive:{
                0:{
                    items:1
                },
                600:{
                    items:3
                },
                1000:{
                    items:4
                }
            }
        });
    });
</script>