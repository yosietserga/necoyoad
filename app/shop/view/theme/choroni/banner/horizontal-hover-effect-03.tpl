<?php $tpl = is_dir(DIR_TEMPLATE. $this->config->get('config_template') ."/shared") ? $this->config->get('config_template') : "choroni"; ?> 
<?php $settings['class'] = !empty($settings['class']) ? $settings['class'].' banner-hhe-03' : 'banner-hhe-03'; ?>
<?php include(DIR_TEMPLATE. $tpl ."/shared/widget-head.tpl");?> 
<?php include(DIR_TEMPLATE. $tpl ."/shared/module-heading.tpl");?> 

<?php if (count($banner['items'])) { ?>
<div class="widget-content" id="<?php echo $widgetName; ?>Content" data-banner="horizontal">
    <ul>
        <?php foreach ($banner['items'] as $item) { ?>
        <li class="item"<?php if (isset($style)) { echo ' style="'. $style .'"'; } ?>>
        <div class="ch-item" style="background-image:url(<?php echo HTTP_IMAGE . $item['image']; ?>)">
            <div class="ch-info-wrap">
            <div class="ch-info">
                <div class="ch-info-front" style="background:url(<?php echo HTTP_IMAGE . $item['image']; ?>)"></div>
                <div class="ch-info-back">
                    <!--title-->
                    <?php if (!empty($item['descriptions'][$Config->get('config_language_id')]['title'])){ ?>
                    <a class="title" href="<?php echo $item['link']; ?>" title="<?php echo $item['descriptions'][$Config->get('config_language_id')]['title']; ?>"><h3><?php echo $item['descriptions'][$Config->get('config_language_id')]['title']; ?></h3></a>
                    <?php } ?>
                    <!--/title-->

                    <!--description-->
                    <?php if (!empty($item['descriptions'][$Config->get('config_language_id')]['description'])){ ?>
                    <p class="body" data-apply="parseHTML"><?php echo $item['descriptions'][$Config->get('config_language_id')]['description']; ?></p>
                    <?php } ?>
                    <!--/description-->

                    <?php if (!empty($item['link']) && (!empty($item['descriptions'][$Config->get('config_language_id')]['title']) || !empty($item['descriptions'][$Config->get('config_language_id')]['description']))){ ?>
                    <div class="link">
                        <a href="<?php echo $item['link']; ?>" title="<?php echo $item['descriptions'][$Config->get('config_language_id')]['title']; ?>">
                            M&aacute;s detalles
                        </a>
                    </div>
                    <?php } ?>

                </div>
            </div>
            </div>
        </div>
        </li>
        <?php } ?>
    </ul>
</div>
<?php } ?>
<?php include(DIR_TEMPLATE. $tpl ."/shared/widget-footer.tpl");?>