<div class="nt-editable" id="<?php echo $widgetName; ?>_images">
    <div id="<?php echo $widgetName; ?>_product-popup">

        <div class="nt-editable product-gallery" id="<?php echo $widgetName; ?>_productImages">

            <img itemprop="image" itemscope itemtype="https://schema.org/Thing" class="product-preview view" id="<?php echo $widgetName; ?>_mainImage" src="<?php echo $images[0]['preview']; ?>" alt="<?php echo $heading_title; ?>" data-zoom-image="<?php echo $images[0]['popup']; ?>"/>

            <div id="<?php echo $widgetName; ?>_productGallery" data-slick='{
                "slidesToShow": 5,
                "swipeToSlide": true,
                "arrows": false,
                "swipe": true
                }'>

                <?php if (count($images) > 1) { ?>
                <?php foreach ($images as $k => $image) { ?>
                <div data-item="thumb">
                    <a class="thumb" data-item="thumb" href="#" data-image="<?php echo $image['preview']; ?>" data-zoom-image="<?php echo $image['popup']; ?>">
                        <img id="<?php echo $widgetName; ?>_thumb_<?php echo $k; ?>" src="<?php echo $image['thumb']; ?>" />
                    </a>
                </div>
                <?php } ?>
                <?php } ?>
            </div>

        </div>
    </div>
</div>

<script>
    $(function(){
        ntPlugins = window.ntPlugins || [];

        ntPlugins.push({
            id:'#<?php echo $widgetName; ?>_mainImage',
            config:{
                gallery: "<?php echo $widgetName; ?>_productGallery",
                cursor: 'crosshair' ,
                responsive: true,
                zoomType: 'window',
                zoomWindowOffetx: 16,
                zoomLevel: 1,
                lensSize: 100,
                galleryActiveClass: 'elevate-active',
                imageCrossfade: true ,
                zoomWindowFadeIn: 450,
                zoomWindowFadeOut: 450,
                lensFadeIn: 450,
                lensFadeOut: 450,
                borderSize: 1,
                loadingIcon: false
            },
            plugin:'elevateZoom',

            fn: function(slider, el){
                var $el = el;
                $el.on("click", function(e) {
                    e.stopPropagation();
                    $.fancybox($el.data('elevateZoom').getGalleryList());
                    return false;
                });
            }
        });

        ntPlugins.push({
            id:'#<?php echo $widgetName; ?>_productGallery',
            config:{
                infinite: true,
                prevArrow: '<button type="button" class="slick-prev product-gallery-control">' + '<?php include(DIR_TEMPLATE. $tpl . "/shared/icons/angle-left.tpl"); ?>' + '</button>',
                nextArrow: '<button type="button" class="slick-next product-gallery-control">' + '<?php include(DIR_TEMPLATE. $tpl . "/shared/icons/angle-right.tpl"); ?>' + '</button>',
            },
            plugin:'slick'
        });

        window.ntPlugins = ntPlugins;
    });
</script>