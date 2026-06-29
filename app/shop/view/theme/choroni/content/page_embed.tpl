<?php $tpl = is_dir(DIR_TEMPLATE. $this->config->get('config_template') ."/shared") ? $this->config->get('config_template') : "choroni"; ?> 
<!--contentContainer -->
<div class="tpl-page-embed">
    <?php include(DIR_TEMPLATE . $tpl ."/shared/widgets-featured.tpl");?>

    <!--mainContentContainer -->
    <div class="row">
        <!--center-column -->
        <div class="large-12 medium-12 small-12">
            <div nt-editable>
                <?php $position = 'main'; ?>
                <?php include(DIR_TEMPLATE . $tpl ."/shared/widgets-rows.tpl");?>
            </div>
        </div>
        <!--/center-column -->
    </div>
    <!--/mainContentContainer -->

    <!--featuredFooterContainer -->
    <?php include(DIR_TEMPLATE . $tpl ."/shared/widgets-featured-footer.tpl");?>
    <!--/featuredFooterContainer -->

</div>
<!--/contentContainer -->