<?php $tpl = is_dir(DIR_TEMPLATE. $this->config->get('config_template') ."/shared") ? $this->config->get('config_template') : "choroni"; ?> 
<?php include(DIR_TEMPLATE. $tpl ."/shared/widget-head.tpl");?> 
    <?php include(DIR_TEMPLATE. $tpl ."/shared/module-heading.tpl");?> 

    <?php if (count($banner['items'])) { ?>
        <div class="widget-content fancybox-gallery fancybox" id="<?php echo $widgetName; ?>Content">
            <ul class="catalog block-grid">
            <?php foreach ($banner['items'] as $item) { ?>
                <!--picture-->
                <li class="catalog-item">
                    <?php if (!empty($item['image'])) { ?>
                        <figure class="picture">
                            <a data-item="fancybox" title="<?php echo $item['descriptions'][$Config->get('config_language_id')]['title']; ?>" rel="<?php echo $widgetName; ?>group" data-fancybox="<?php echo $widgetName; ?>gallery" class="thumb fancybox b-horizontal-picture b-horizontal-item" href="<?php echo $Image->resizeAndSave($item['image'],550,550); ?>">
                                <img src="<?php echo $Image->resizeAndSave($item['image'],275,275); ?>" alt=""/>
                            </a>
                        </figure>
                    <?php } ?>
                    <?php if (!empty($item['descriptions'][$Config->get('config_language_id')]['title']) || !empty($item['descriptions'][$Config->get('config_language_id')]['description']) ) { ?>
                        <section class="info">
                                <!--title-->
                                <div class="rating" style="min-height: 1.063em; width: 100%;"></div>

                                <?php if (!empty($item['descriptions'][$Config->get('config_language_id')]['title'])){ ?>
                                    <a data-apply="parseHTML" class="name" href="<?php echo $item['link']; ?>" title="<?php echo $item['descriptions'][$Config->get('config_language_id')]['title']; ?>"><?php echo $item['descriptions'][$Config->get('config_language_id')]['title']; ?></a>
                                <?php } ?>
                                <!--/title-->

                                <!--description-->

                                <?php if (!empty($item['descriptions'][$Config->get('config_language_id')]['description'])){ ?>
                                    <p data-apply="parseHTML" class="overview"><?php echo $item['descriptions'][$Config->get('config_language_id')]['description']; ?></p>
                                <?php } ?>
                                <!--/description-->

                                <!--read-more-->
                                <?php if (!empty($item['link'])){ ?>
                                    <div class="group group--btn" role="group">
                                        <div class="btn btn-add btn--secondary" role="button">
                                            <a href="<?php echo $item['link']; ?>">
                                                <?php echo $l('text_more_details'); ?>
                                            </a>
                                        </div>
                                    </div>
                                <?php } ?> 
                                <!--/read-more-->
                        </section>
                    <?php } ?>
                </li>
                <?php } ?>
            </ul>
        </div>
    <?php } ?>
<?php include(DIR_TEMPLATE. $tpl ."/shared/widget-footer.tpl");?>

<script>
    /**script:<?php echo $widgetName; ?>Scripts**/
    $(function(){
        ntPlugins = window.ntPlugins || [];

        ntPlugins.push({
            id:"<?php echo $widgetname; ?>",
            config:{
                ajax: {
                    dataType : 'html',
                    headers  : { 'X-fancyBox': true }
                }
            },
            plugin:'fancybox'
        });
        window.ntPlugins = ntPlugins;
        
        if (typeof loadNTPlugins !== 'undefined' && typeof loadNTPlugins === 'function') {
            loadNTPlugins();
        }
    });
</script>
