<?php $tpl = is_dir(DIR_TEMPLATE. $this->config->get('config_template') ."/shared") ? $this->config->get('config_template') : "choroni"; ?> 
<?php $settings['class'] = !empty($settings['class']) ? $settings['class'].' banner-hhe-08' : 'banner-hhe-08'; ?>
<?php include(DIR_TEMPLATE. $this->config->get('config_template') ."/shared/widget-head.tpl");?> 
    <?php include(DIR_TEMPLATE. $this->config->get('config_template') ."/shared/module-heading.tpl");?> 

<?php if (count($banner['items'])) { ?>
<div class="widget-content" id="<?php echo $widgetName; ?>Content" data-banner="horizontal">
    <ul>
        <?php foreach ($banner['items'] as $item) { ?>
        <li class="item"<?php if (isset($style)) { echo ' style="'. $style .'"'; } ?>>


            <!--picture-->
            <?php if (!empty($item['image'])) { ?>
            <?php if (!empty($item['link'])) { ?><a href="<?php echo $item['link']; ?>"><?php } ?>
            <div class="stack">
                <div class="stack__deco"></div>
                <div class="stack__deco"></div>
                <div class="stack__deco"></div>
                <div class="stack__deco"></div>
                <figure class="stack__figure">
                    <img class="stack__img" src="<?php echo HTTP_IMAGE . $item['image']; ?>" alt="<?php $item['descriptions'][$Config->get('config_language_id')]['title']; ?>" />
                </figure>
            </div>
            <?php if (!empty($item['link'])) { ?></a><?php } ?>
            <?php } ?>
            <!--/picture-->

            <!--header-->
            <section class="details">
                <!--title-->
                <?php if (!empty($item['descriptions'][$Config->get('config_language_id')]['title'])){ ?>
                <a class="title" href="<?php echo $item['link']; ?>" title="<?php echo $item['descriptions'][$Config->get('config_language_id')]['title']; ?>" data-apply="parseHTML"><?php echo $item['descriptions'][$Config->get('config_language_id')]['title']; ?></a>
                <?php } ?>
                <!--/title-->

                <!--description-->
                <?php if (!empty($item['descriptions'][$Config->get('config_language_id')]['description'])){ ?>
                <p class="body" data-apply="parseHTML"><?php echo $item['descriptions'][$Config->get('config_language_id')]['description']; ?></p>
                <?php } ?>
                <!--/description-->

                <!--read-more-->
                <?php if (!empty($item['link']) && (!empty($item['descriptions'][$Config->get('config_language_id')]['title']) || !empty($item['descriptions'][$Config->get('config_language_id')]['description']))){ ?>
                <div class="link">
                    <a href="<?php echo $item['link']; ?>" title="<?php echo $item['descriptions'][$Config->get('config_language_id')]['title']; ?>">
                        M&aacute;s detalles
                    </a>
                </div>
                <?php } ?>
                <!--/read-more-->
            </section>
            <!--/header-->
        </li>
        <?php } ?>
    </ul>
</div>
<script>
    $(function(){
        ntPlugins = window.ntPlugins || [];

        ntPlugins.push({
            id:"#<?php echo $widgetName; ?>",
            config:{
                el: '#<?php echo $widgetName; ?> li'
            },
            fn:function( config ) {
                [].slice.call(document.querySelectorAll( config.el )).forEach(function(stackEl) {
                    new PolarisFx(stackEl);
                });
            }
        });
        window.ntPlugins = ntPlugins;
    });
</script>
<?php } ?>
<?php include(DIR_TEMPLATE. $this->config->get('config_template') ."/shared/widget-footer.tpl");?>