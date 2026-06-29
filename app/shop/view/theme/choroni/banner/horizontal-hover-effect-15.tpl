<?php $tpl = is_dir(DIR_TEMPLATE. $this->config->get('config_template') ."/shared") ? $this->config->get('config_template') : "choroni"; ?> 
<?php $settings['class'] = !empty($settings['class']) ? $settings['class'].' banner-hhe-15' : 'banner-hhe-15'; ?>
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
                <figure class="thumb">
                    <img src="<?php echo HTTP_IMAGE . $item['image']; ?>" alt="<?php $item['image']; ?>"/>
                </figure>
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
                        <i class="fa fa-fw  fa-chevron-right"></i>
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
<?php } ?>
<?php include(DIR_TEMPLATE. $this->config->get('config_template') ."/shared/widget-footer.tpl");?>