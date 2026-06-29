<?php $tpl = is_dir(DIR_TEMPLATE. $this->config->get('config_template') ."/shared") ? $this->config->get('config_template') : "choroni"; ?>
<span<?php if ($settings['width'] || $settings['height']) { ?> style="<?php echo ($settings['width']) ? "width:".$settings['width'] : ""; ?>;<?php echo ($settings['height']) ? "height:".$settings['height'] : ""; ?>"<?php } ?>>


    <?php include(DIR_TEMPLATE. $tpl ."/shared/widgets-featured.tpl");?>

    <!--mainContentContainer -->
    <div id="mainContentContainer" nt-editable>
        <div class="row">

            <div class="large-12">
                <?php include(DIR_TEMPLATE. $tpl ."/shared/module-heading.tpl");?>

                <div class="content" id="<?php echo $widgetName; ?>Content">
                    <?php echo html_entity_decode($page['pdescription']); ?>
                </div>
            </div>

            <!--center-column -->
            <?php include(DIR_TEMPLATE. $tpl ."/shared/widgets-column-center.tpl");?>
            <!--/center-column -->

        </div>
    </div>
    <!--/mainContentContainer -->

    <?php include(DIR_TEMPLATE. $tpl ."/shared/widgets-featured-footer.tpl");?>

</span>
<div class="close">X</div>

<script>
$(function(){
    $('#<?php echo $widgetName; ?> .close').on('click',function(){
        $('#<?php echo $widgetName; ?>').remove();
    });

    /*
    $('body').on('click', function(e) {
        var target = $(e.target);
        if(target.is('#<?php echo $widgetName; ?>')) {
           if ( $('#<?php echo $widgetName; ?>').is(':visible') ) $('#<?php echo $widgetName; ?>').remove();
        }
    });
    */
    
    if (getCookie('<?php echo $widgetName; ?>')) {
       /* $('#<?php echo $widgetName; ?>').remove(); */
    }
    
    lightBoxWindowResize('#<?php echo $widgetName; ?>');
    
    $(window).resize(function() {
        lightBoxWindowResize('#<?php echo $widgetName; ?>');
    });
});
function lightBoxWindowResize(e) {
    var container = $(e).find('span');

    var width  = (window.innerWidth - $(container).width() - 100) / 2;
    var height = (window.innerHeight - $(container).height() - 100) / 2;

    $(container).css({
        'marginTop': height +'px',
        'marginLeft': width +'px'
    });
}
function getCookie(name) {
    var value = "; " + document.cookie;
    var parts = value.split("; " + name + "=");
    if (parts.length == 2) return parts.pop().split(";").shift();
}
</script>