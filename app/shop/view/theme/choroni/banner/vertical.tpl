<!--VERTICAL BANNER-->
<?php $tpl = is_dir(DIR_TEMPLATE. $this->config->get('config_template') ."/shared") ? $this->config->get('config_template') : "choroni"; ?> 
<?php $settings['class'] = !empty($settings['class']) ? $settings['class'].' banner-hhe-10' : 'banner-hhe-10'; ?>
<?php include(DIR_TEMPLATE. $tpl ."/shared/widget-head.tpl");?> 
    <?php include(DIR_TEMPLATE. $tpl ."/shared/module-heading.tpl");?> 

    <?php if (count($banner['items'])) { ?>

    <!--VERTICAL BANNER CONTENT-->
    <div class="content widget-content" id="<?php echo $widgetName; ?>Content">
        <ul class="b-vertical-banner">
        <?php foreach ($banner['items'] as $item) { ?>
            <li>
                <!--title-->

                <?php if (!empty($item['descriptions'][$Config->get('config_language_id')]['title'])){ ?>
                    <a data-apply="parseHTML" class="b-vertical-title v-vertical-item"href="<?php echo $item['link']; ?>" title="<?php echo $item['descriptions'][$Config->get('config_language_id')]['title']; ?>"><?php echo $item['descriptions'][$Config->get('config_language_id')]['title']; ?></a>
                <?php } ?>

                <!--/title-->

                <!--image with link-->

                <?php if (!empty($item['image'])) { ?>
                    <?php if (!empty($item['link'])) { ?> <a class="b-vertical-picture b-vertical-item" href="<?php echo $item['link']; ?>" title="<?php echo $item['descriptions'][$Config->get('config_language_id')]['title']; ?>"><?php } ?>

                    <img src="<?php echo HTTP_IMAGE . $item['image']; ?>" data-thumb="<?php echo $item['thumb']; ?>" alt="<?php echo $item['descriptions'][$Config->get('config_language_id')]['title']; ?>" title="<?php echo $item['descriptions'][$Config->get('config_language_id')]['title']; ?>" />

                    <?php if (!empty($item['link'])) { ?> </a> <?php } ?>
                <?php } ?>

                <!--/image with link-->

                <!--description-->

                <?php if (!empty($item['descriptions'][$Config->get('config_language_id')]['description'])){ ?>
                    <p data-apply="parseHTML" class="b-vertical-description b-vertical-item"><?php echo $item['descriptions'][$Config->get('config_language_id')]['description']; ?></p>
                <?php } ?>

                <!--description-->

                <!--link-->

                <?php if (!empty($item['link'])){ ?>
                    <div class="b-vertical-link b-vertical-item btn" role="link">
                        <a href="<?php echo $item['link']; ?>">Leer más</a>
                    </div>
                <?php } ?>

                <!--/link-->
            </li>
        <?php } ?>
    </ul>
    </div>
    <!--/VERTICAL BANNER CONTENT-->
    <?php } ?>
<?php include(DIR_TEMPLATE. $tpl ."/shared/widget-footer.tpl");?>
<!--/VERTICAL BANNER-->
