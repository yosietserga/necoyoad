<?php $tpl = is_dir(DIR_TEMPLATE. $this->config->get('config_template') ."/shared") ? $this->config->get('config_template') : "choroni"; ?>
<?php include(DIR_TEMPLATE. $tpl ."/shared/widget-head.tpl");?> 

    <h2><?php echo $heading_title; ?></h2>
    <div class="fb-comments" data-href="<?php echo $url; ?>" data-num-posts="2">

    <div id="fb-root"></div>

    <script>
        /*
        settings:
            - language
            - theming
         */
    (function (d, s, id) {
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) {return;}
            js = d.createElement(s);
        js.id = id;
        js.src = "//connect.facebook.net/es_ES/all.js#xfbml=1&appId=223173687752863";
        fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));

    $(function(){
        $('#<?php echo $widgetName; ?> .fb-comments').attr('data-width', $('#<?php echo $widgetName; ?>').width());

        $(window).on('resize', function() {
            $('#<?php echo $widgetName; ?> .fb-comments').attr('data-width', $('#<?php echo $widgetName; ?>').width());
        });
    });
    </script>
<?php include(DIR_TEMPLATE. $tpl ."/shared/widget-footer.tpl");?>