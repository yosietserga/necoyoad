<?php $tpl = is_dir(DIR_TEMPLATE. $this->config->get('config_template') ."/shared") ? $this->config->get('config_template') : "choroni"; ?> 
<?php foreach($tabs as $k => $tab) { ?>
<div id="_<?php echo 'tab_'. $k .'_'. $widgetName; ?>" nt-editable>



    <?php if($tab['products']) { ?>
    <div id="<?php echo $widgetName . $k; ?>Carousel" class="owl-carousel">
        <?php foreach($tab['products'] as $product) { ?>
        <div>
            <?php include(DIR_TEMPLATE. $tpl ."/shared/catalog-picture.tpl"); ?>
            <?php include(DIR_TEMPLATE. $tpl ."/shared/catalog-info.tpl"); ?>
        </div>
        <?php } ?>
    </div>

    <script type="text/javascript">
        $(function(){
            $("#<?php echo $widgetName. $k; ?>Carousel").owlCarousel({
                loop:true,
                margin:10,
                nav:true,
                autoplay:true,
                autoplayTimeout:3000,
                autoplayHoverPause:true,
                responsiveClass:true,
                responsive:{
                    0:{
                        items:2,
                        nav:true
                    },
                    600:{
                        items:4,
                        nav:false
                    }
                }
            });
        });
    </script>
    <?php } else { ?>
    <h2>No hay productos que cumplan con este criterio</h2>
    <?php } ?>



</div>
<?php } ?>