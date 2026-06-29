<script async>
    <?php if (isset($settings['google_analytics_code'])) { ?>
        var _gaq = [['_setAccount', '<?php echo $settings['google_analytics_code']; ?>'], ['_trackPageview']];
        (function (d, t){var g=d.createElement(t),s=d.getElementsByTagName(t)[0];
        g.src=('https:'===location.protocol?'//ssl':'//www')+'.google-analytics.com/ga.js';
        g.async = true;
        s.parentNode.insertBefore(g,s)}(document, 'script'));
        <?php }?>
</script>