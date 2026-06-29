<?php echo $header; ?>
<?php $tpl = is_dir(DIR_TEMPLATE. $this->config->get('config_template') ."/shared") ? $this->config->get('config_template') : "choroni"; ?>

<!--contentContainer -->
<div id="contentContainer" class="tpl-account-addresses" nt-editable>

    <?php include(DIR_TEMPLATE. $tpl ."/shared/widgets-featured.tpl");?>

    <!--mainContentContainer -->
    <div id="mainContentContainer" nt-editable>
        <div class="row">

            <!-- left-column -->
            <div class="large-3 medium-3 small-12">
                <div id="columnLeft" nt-editable>
                    <?php echo $account_column_left; ?>
                    <?php if ($column_left) { echo $column_left; } ?>
                </div>
            </div>
            <!--/left-column -->

            <!--center-column -->
            <?php if ($column_left && $column_right) { ?>
            <div class="large-6 medium-6 small-12">
            <?php } else { ?>
            <div class="large-9 medium-9 small-12">
            <?php } ?>

                <div id="columnCenter" nt-editable>

                    <div class="row">
                        <?php foreach ($addresses as $result) { ?>
                        <div class="address-item large-4 medium-6 small-12">

                            <span>
                            <?php echo $result['address']; ?>
                            <?php if ($result['default']) { ?>
                                <b><?php echo $l('text_default'); ?></b>
                            <?php } ?>
                            </span>

                            <div class="clear"></div>

                            <div class="group" role="group">

                                <div class="btn" aria-label="Edit" role="button">
                                    <a title="<?php echo $l('button_edit'); ?>" href="<?php echo str_replace('&', '&amp;', $result['update']); ?>"><?php echo $l('button_edit'); ?></a>
                                </div>

                                <div class="btn" aria-label="Delete" role="button">
                                    <a title="<?php echo $l('button_delete'); ?>" href="<?php echo str_replace('&', '&amp;', $result['delete']); ?>"><?php echo $l('button_delete'); ?></a>
                                </div>

                            </div>

                        </div>
                        <?php } ?>
                    </div>

                    <div class="clear"></div>

                    <div class="btn" aria-label="New" role="button">
                        <a title="<?php echo $l('button_new_address'); ?>" href="<?php echo str_replace('&', '&amp;', $Url::createUrl("account/address/insert")); ?>"><?php echo $l('button_new_address'); ?></a>
                    </div>



                    <?php $position = 'main'; ?>
                    <?php foreach($rows[$position] as $j => $row) { ?>
                    <?php if (!$row['key']) continue; ?>
                    <?php $row_id = $row['key']; ?>
                    <?php $row_settings = unserialize($row['value']); ?>
                    <div class="row" id="<?php echo $position; ?>_<?php echo $row_id; ?>" nt-editable>
                        <?php foreach($row['columns'] as $k => $column) { ?>
                        <?php if (!$column['key']) continue; ?>
                        <?php $column_id = $column['key']; ?>
                        <?php $column_settings = unserialize($column['value']); ?>
                        <div class="large-<?php echo $column_settings['grid_large']; ?> medium-<?php echo $column_settings['grid_medium']; ?> small-<?php echo $column_settings['grid_small']; ?>" id="<?php echo $position; ?>_<?php echo $column_id; ?>" nt-editable>
                            <ul class="widgets">
                                <?php foreach($column['widgets'] as $l => $widget) { ?> {%<?php echo $widget['name']; ?>%} <?php } ?>
                            </ul>
                        </div>
                        <?php } ?>
                    </div>
                    <?php } ?>

                </div>
            </div>
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