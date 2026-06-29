<?php $tpl = is_dir(DIR_TEMPLATE. $this->config->get('config_template') ."/shared") ? $this->config->get('config_template') : "choroni"; ?> 
<?php include(DIR_TEMPLATE. $tpl ."/shared/widget-head.tpl");?> 
    <?php include(DIR_TEMPLATE. $tpl ."/shared/module-heading.tpl");?> 

<?php if (count($banner['items'])) { ?>
<div class="content" id="<?php echo $widgetName; ?>Content">
    <ul id="<?php echo $widgetName; ?>slicebox" class="sb-slider">
    <?php foreach ($banner['items'] as $item) { ?>
        <?php if (empty($item['image'])) continue; ?>
        <li>
            <?php if (!empty($item['link'])) { ?><a href="<?php echo $item['link']; ?>" title="<?php echo $item['descriptions'][$Config->get('config_language_id')]['title']; ?>"><?php } ?>
            <img src="<?php echo HTTP_IMAGE . $item['image']; ?>" />
            <?php if (!empty($item['link'])) { ?></a><?php } ?>
            <?php if (!empty($item['descriptions'][$Config->get('config_language_id')]['title']) || !empty($item['descriptions'][$Config->get('config_language_id')]['description'])) { ?>
            <div class="sb-description">
                <?php if (!empty($item['descriptions'][$Config->get('config_language_id')]['title'])) { echo '<h3>'. $item['descriptions'][$Config->get('config_language_id')]['title'] .'</h3>'; } ?>
                <?php if (!empty($item['descriptions'][$Config->get('config_language_id')]['description'])) { echo '<em>'. htmlentities($item['descriptions'][$Config->get('config_language_id')]['description']) .'</em>'; } ?>"
            </div>
            <?php } ?>
        </li>
    <?php } ?>
    </ul>

    <div id="<?php echo $widgetName; ?>nav-arrows" class="nav-arrows">
        <a href="#">Next</a>
		<a href="#">Previous</a>
    </div>

    <div id="<?php echo $widgetName; ?>nav-dots" class="nav-dots">
        <?php foreach ($banner['items'] as $item) { ?>
        <span></span>
        <?php } ?>
    </div>

</div>
<div class="clear"></div><br />
<script>
    /**script:<?php echo $widgetName; ?>Scripts**/
    $(function(){
        ntPlugins = window.ntPlugins || [];
        
        ntPlugins.push({
            id:"#<?php echo $widgetName; ?>slicebox",
            config:{
                onReady: function(){
                    $('#<?php echo $widgetName; ?>nav-arrows').show();
                    $('#<?php echo $widgetName; ?>nav-dots').show();
                    $('#<?php echo $widgetName; ?>nav-options').show();
                },
                onBeforeChange: function(pos){
                    $('#<?php echo $widgetName; ?>nav-options').children('span').removeClass('nav-dot-current');
                    $('#<?php echo $widgetName; ?>nav-options').children('span').eq(pos).addClass('nav-dot-current');
                },
                orientation: 'r',
                cuboidsRandom: true
            },
            plugin:'slicebox',
            fn: function(slider, el){
                
                $('#<?php echo $widgetName; ?>nav-arrows').children(':first').on('click', function(){
                    slider.next();
                    return false;
                });
                
                $('#<?php echo $widgetName; ?>nav-arrows').children(':last').on('click', function(){
                    slider.previous();
                    return false;
                });
                
                $('#<?php echo $widgetName; ?>nav-options').children('span').each(function(i){
                    $(this).on('click', function (event) {
                        var $dot = $(this);
                        if (!slider.isActive()) {
                            $('#<?php echo $widgetName; ?>nav-options').children('span').removeClass('nav-dot-current');
                            $dot.addClass('nav-dot-current');
                        }
                        slider.jump(i + 1);
                        return false;
                    });
                });
                
                $('#<?php echo $widgetName; ?>navPlay').on('click', function () {
                    slider.play();
                    return false;
                });
                
                $('#<?php echo $widgetName; ?>navPause').on('click', function () {
                    slider.pause();
                    return false;
                });
                
                slider.play();
            }
        });
        window.ntPlugins = ntPlugins;
        
        if (typeof loadNTPlugins !== 'undefined' && typeof loadNTPlugins === 'function') {
            loadNTPlugins();
        }
    });
</script>
<?php } ?>
<?php include(DIR_TEMPLATE. $tpl ."/shared/widget-footer.tpl");?>