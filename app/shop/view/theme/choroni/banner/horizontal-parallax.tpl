<?php $tpl = is_dir(DIR_TEMPLATE. $this->config->get('config_template') ."/shared") ? $this->config->get('config_template') : "choroni"; ?> 
<?php $settings['class'] = !empty($settings['class']) ? $settings['class'].' horizontal-parallax' : 'horizontal-parallax'; ?>
<?php include(DIR_TEMPLATE. $tpl ."/shared/widget-head.tpl");?> 
    <?php include(DIR_TEMPLATE. $tpl ."/shared/module-heading.tpl");?> 
    
<?php if (count($banner['items'])) { ?>
<div class="widget-content" id="<?php echo $widgetName; ?>Content" data-banner="horizontal-parallax">
    <ul class="container">
        <?php foreach ($banner['items'] as $item) { ?>
        <li class="item">
            <!--picture-->
            <?php if (!empty($item['image'])) { ?>
                <?php if (!empty($item['link'])) { ?><a href="<?php echo $item['link']; ?>"><?php } ?>
                <figure style="background-image: url('<?php echo HTTP_IMAGE . $item['image']; ?>')" class="b-horizontal-picture b-horizontal-item">
                </figure>
                <?php if (!empty($item['link'])) { ?></a><?php } ?>
            <?php } ?>
            <!--/picture-->

            <!--header-->
            <header class="b-horizontal-header">

                <!--title-->

                <?php if (!empty($item['descriptions'][$Config->get('config_language_id')]['title'])){ ?>
                <a data-apply="parseHTML" class="b-horizontal-title b-horizontal-item"href="<?php echo $item['link']; ?>" title="<?php echo $item['descriptions'][$Config->get('config_language_id')]['title']; ?>"><?php echo $item['descriptions'][$Config->get('config_language_id')]['title']; ?></a>
                <?php } ?>
                <!--/title-->

                <!--description-->

                <?php if (!empty($item['descriptions'][$Config->get('config_language_id')]['description'])){ ?>
                <p data-apply="parseHTML" class="b-horizontal-description b-horizontal-item"><?php echo $item['descriptions'][$Config->get('config_language_id')]['description']; ?></p>
                <?php } ?>
                <!--/description-->

                <!--read-more-->
                <?php if (!empty($item['link'])){ ?>
                <div class="b-horizontal-link b-horizontal-item action-button">
                    <a href="<?php echo $item['link']; ?>">Más detalles</a>
                </div>
                <?php } ?>

                <!--/read-more-->
            </header>
            <!--/header-->

        </li>
        <?php } ?>
    </ul>
</div>
<?php } ?>
<?php include(DIR_TEMPLATE. $tpl ."/shared/widget-footer.tpl");?>