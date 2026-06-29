<!doctype html>
<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="es"> <![endif]-->
<!--[if IE 7]>    <html class="no-js lt-ie9 lt-ie8" lang="es"> <![endif]-->
<!--[if IE 8]>    <html class="no-js lt-ie9" lang="es"> <![endif]-->
<!-- Consider adding a manifest.appcache: h5bp.com/d/Offline -->
<!--[if gt IE 8]><!--> <html class="no-js" lang="es"> <!--<![endif]-->
<head>
    <meta charset="utf-8" />

    <!-- Use the .htaccess and remove these lines to avoid edge case issues.
       More info: h5bp.com/i/378 -->
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />

    <title><?php echo $title; ?></title>
    <?php if ($keywords) { ?>
    <meta name="keywords" content="<?php echo $keywords; ?>" />
    <?php } ?>
    
    <?php if ($description) { ?>
    <meta name="description" content="<?php echo $description; ?>" />
    <?php } ?>
    
    <!-- Mobile viewport optimized: h5bp.com/viewport -->
    <meta name="viewport" content="width=device-width" />

    <?php if ($icon) { ?>
    <link href="<?php echo $icon; ?>" rel="icon" />
    <?php } ?>
    
    <?php if (count($styles) > 0) { ?>
        <?php foreach ($styles as $style) { ?>
        <?php if (empty($style['href'])) continue; ?>
    <link rel="stylesheet" type="text/css" media="<?php echo $style['media']; ?>" href="<?php echo $style['href']; ?>" />
        <?php } ?>
    <?php } ?>
    
    <script src="<?php echo HTTP_JS; ?>modernizr.js"></script>
    <!-- <script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script> -->
    <script>window.$ || document.write('<script src="<?php echo HTTP_JS; ?>vendor/jquery.min.js"><\/script>')</script>
</head>
<body id="mainbody">
  <!--[if lt IE 7]><p class=chromeframe>Your browser is <em>ancient!</em> <a href="https://browsehappy.com/">Upgrade to a different browser</a> or <a href="https://www.google.com/chromeframe/?redirect=true">install Google Chrome Frame</a> to experience this site.</p><![endif]-->
  
<header id="header">
    <div>
        <?php if ($logo) { ?><a title="<?php echo $store; ?>" href="<?php echo $Url::createUrl("common/home"); ?>"><img src="<?php echo $logo; ?>" title="<?php echo $store; ?>" alt="<?php echo $store; ?>" /></a><?php } else { ?><a title="<?php echo $store; ?>" href="<?php echo $Url::createUrl("common/home"); ?>"><?php echo $text_store; ?></a><?php } ?>
    </div>
</header>

<!--main-section -->
<section id="maincontent" class="row">
    <div class="large-12 medium-12 small-12 columns">
        <?php echo $message; ?>
    </div>
</section>
<!--main-section-->

<!-- footer/underbottom -->
    <footer class="underbottom">
        <section class="row">
            <div class="large-12 medium-12 small-12 columns">
                <ul class="underbottom-widgets widgets row">
                    <?php if ($widgets) {
                        foreach ($widgets as $widget) { ?>{%<?php echo $widget; ?>%}
                    <?php } } ?>
                </ul>
            </div>
        </section>
    </footer>
<!-- footer/underbottom -->

<!-- terms -->
<section class="terms">
    <div class="row">
        <div class="large-12 medium-12 small-12 columns">
            <em><?php echo $text_powered_by; ?></em>
        </div>
    </div>
</section>
<!-- /terms -->

</body>
</html>