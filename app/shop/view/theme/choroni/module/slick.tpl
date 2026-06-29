<!-- slick-carousel-widget -->
<li nt-editable="1" class="slickWidget<?php echo ($settings['class']) ? " " . $settings['class'] : ''; ?>" id="<?php echo $widgetName; ?>">
  <?php include(DIR_TEMPLATE. $tpl ."/shared/module-heading.tpl");?> 


  <script src="<?php echo HTTP_HOME . 'assets/theme/' . $this->config->get('config_template') . '/js/vendor/slick/slick/slick.min.js';?>"></script>
  <script async>
    (function () {
    var slickScript;
    if (typeof $.fn.slick === 'undefined') {
    slickScript = document.createElement("script");
    slickScript.src = '<?php echo HTTP_HOME . 'assets / theme / ' . $this->config->get('config_template') . ' / js / vendor / slick / slick / slick.min.js';?>';
    slickScript.async = true;
    $("head").append(slickScript);
    }
    })();
  </script>

  <script defer>
    (function(){
    var initSlickCarousel;
    $.ajax({
    url: '<?php echo Url::createUrl("module/". $settings['module'] ."/carousel");
      if ((int)$settings['limit']) echo '&limit='.(int)$settings['limit'] ? > '

    })
      .done(function (data) {
      var products = $.parseJSON(data).results,
        slickTarget = $('[data-widget="slick"]'),
        productList = products.map(function (product, ignored) {
        var output = "";
        output += '<div id="' + product.product_id + '" class="slick-item">';
        output += '<div class="picture">';
        output += '<a class="product-link" href="' + product.seeProduct_url + '">';
        output += '<img src="<?php echo HTTP_IMAGE ?>' + product.image + '">';
        output += '</a>';
        output += '</div>';
        output += '<div class="info">';
        if (~~product.rating !== 0) {
        output += '<span class="rating">';
        output += '<img src="<?php echo HTTP_IMAGE; ?>stars_' + ~~product.rating + '.png">';
        output += '</span>';
        } else {
        output += '<span class="rating" style="min-height: 0.875em; display: block; width: 100%">';
        output += '</span>';
        }
        output += '<span class="name">' + product.name + '</span>';
        output += '<span class="price">' + product.price + '</span>';
        output += '</div>';
        output += '</div>';
        return output;
        });
      slickTarget.html(productList.join(""));
      initSlickCarousel('[data-widget="slick"]');
      });
    /**
     * init the slick carusel plugin
     * @param string target - slick plugin target
     */

    initSlickCarousel = function (target) {
    $(target).slick({
    slidesToShow: < ?php echo (int)    $settings["slideToShow"]; ? >
      , slidesToScroll: < ?php echo (int)    $settings["slideToScroll"]; ? >
      , responsive: [
      {
      breakpoint: 1025,
        settings: {
        arrows: true
          , slidesToShow:     3
          , slidesToScroll:   3
          , swipe: true

        }
      },
      {
      breakpoint: 481,
        settings: {
        arrows: true
          , centerMode: true
          , slidesToShow: 1
          , slidesToScroll: 1
          , vertical: true
          , verticalSwiping: true
          , swipe: true
        }
      }
      ]
      /*, infinite:          <?php echo (int)   $settings["infinite"];?>
       , dots:              <?php echo (int)   $settings["dots"];?>
       , arrows:            <?php echo (int)    $settings["arrows"];?>
       , asNavFor:          <?php echo (string) $settings["asNavFor"];?>
       , centerMode:        <?php echo (int)   $settings["centerMode"];?>
       , focusOnSelect:     <?php echo (int)   $settings["focusOnSelect"];?>
       , slide:             <?php echo (string) $settings["slide"];?>
       , autoplaySpeed:     <?php echo (int)    $settings["autoplaySpeed"];?>*/
    });
    }
    /*
     responsive: [
     {
     breakpoint: 768,
     settings: {
     arrows: false,
     centerMode: true,
     centerPadding: '40px',
     slidesToShow: 3
     }
     },
     {
     breakpoint: 480,
     settings: {
     arrows: false,
     centerMode: true,
     centerPadding: '40px',
     slidesToShow: 1
     }
     }
     ]
     * */
    })();
  </script>