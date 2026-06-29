<?php $tpl = is_dir(DIR_TEMPLATE. $this->config->get('config_template') ."/shared") ? $this->config->get('config_template') : "choroni"; ?> 
<?php include(DIR_TEMPLATE. $tpl ."/shared/widget-head.tpl");?> 
    <?php include(DIR_TEMPLATE. $tpl ."/shared/module-heading.tpl");?> 

<?php if (count($banner['items'])) { ?>
<div class="content owl-carousel" id="<?php echo $widgetName; ?>Content">
    <?php foreach($banner['items'] as $item) { ?>
    <div>

        <?php if (!empty($item['link'])) { ?><a href="<?php echo $item['link']; ?>"><?php } ?>

        <?php if (!empty($item['image'])) { ?>
        <figure>
            <img src="<?php echo HTTP_IMAGE . $item['image']; ?>" data-thumb="<?php echo $item['thumb']; ?>" alt="<?php echo $item['descriptions'][$Config->get('config_language_id')]['title']; ?>" title="<?php echo $item['descriptions'][$Config->get('config_language_id')]['title']; ?>" />
        </figure>
        <?php } ?>

        <?php if (!empty($item['link'])) { ?></a><?php } ?>

        <?php if (!empty($item['descriptions'][$Config->get('config_language_id')]['title']) || !empty($item['descriptions'][$Config->get('config_language_id')]['description'])) { ?>
        <div class="caption">
            <?php if (!empty($item['descriptions'][$Config->get('config_language_id')]['title'])) { ?><h4><?php echo $item['descriptions'][$Config->get('config_language_id')]['title']; ?></h4><?php } ?>

            <?php if (!empty($item['descriptions'][$Config->get('config_language_id')]['description'])) { ?>
            <p class="body">
                <?php echo $item['descriptions'][$Config->get('config_language_id')]['description']; ?>
            </p>
            <?php } ?>

            <?php if (!empty($item['link'])) { ?>
            <a href="<?php echo $item['link']; ?>" class="button"><?php echo $l('See More'); ?> </a>
            <?php } ?>
        </div>
        <?php } ?>

    </div>
    <?php } ?>

</div>
<script>
    /**script:<?php echo $widgetName; ?>Scripts**/
    $(function(){
        ntPlugins = window.ntPlugins || [];

        ntPlugins.push({
            id:'#<?php echo $widgetName; ?>Content',
            config:{
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
                        items:2
                    },
                    1000:{
                        items:4
                    }
                }
            },
            plugin:'owlCarousel'
        });
        window.ntPlugins = ntPlugins;
        
        if (typeof loadNTPlugins !== 'undefined' && typeof loadNTPlugins === 'function') {
            loadNTPlugins();
        }
    });
</script>
<?php } ?>
<?php include(DIR_TEMPLATE. $tpl ."/shared/widget-footer.tpl");?>