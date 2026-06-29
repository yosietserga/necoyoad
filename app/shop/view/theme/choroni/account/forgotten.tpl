<?php echo $header; ?>
<?php $tpl = is_dir(DIR_TEMPLATE. $this->config->get('config_template') ."/shared") ? $this->config->get('config_template') : "choroni"; ?>

<!--contentContainer -->
<div id="contentContainer" class="tpl-account-forgotten" nt-editable>

    <?php include(DIR_TEMPLATE. $tpl ."/shared/widgets-featured.tpl");?>

    <!--mainContentContainer -->
    <div id="mainContentContainer" nt-editable>
        <div class="row">

            <!-- left-column -->
            <?php if ($column_left) { ?>
            <?php include(DIR_TEMPLATE. $tpl ."/shared/widgets-column-left.tpl");?>
            <?php } ?>
            <!--/left-column -->

            <form class="simple-form" action="<?php echo str_replace('&', '&amp;', $action); ?>" method="post" enctype="multipart/form-data" id="forgotten">
                <p><?php echo $l('text_email'); ?></p>
                <div class="email-entry form-entry">
                    <label for="email"><?php echo $l('text_your_email'); ?></label>
                    <input type="email" name="email" placeholder="Ingrese su email. E.j: miemail@xxx.com">
                </div>
                <div class="group group--btn">
                    <div class="btn btn--primary">
                        <a onclick="location = '<?php echo str_replace('&', '&amp;', $back); ?>'"><?php echo $l('button_back'); ?></a>
                    </div>
                    <div class="btn btn--primary">
                        <a onclick="$('#forgotten').submit();" class="button"><?php echo $l('button_continue'); ?></a>
                    </div>
                </div>
            </form>

            <!--center-column -->
            <?php include(DIR_TEMPLATE. $tpl ."/shared/widgets-column-center.tpl");?>
            <!--/center-column -->

            <!-- right-column -->
            <?php if ($column_right) { ?>
            <?php include(DIR_TEMPLATE. $tpl ."/shared/widgets-column-right.tpl");?>
            <?php } ?>
            <!--/right-column -->

        </div>
    </div>
    <!--/mainContentContainer -->

    <!--featuredFooterContainer -->
    <?php include(DIR_TEMPLATE. $tpl ."/shared/widgets-featured-footer.tpl");?>
    <!--/featuredFooterContainer -->

</div>
<!--/contentContainer -->

<?php echo $footer; ?>