<?php echo $header; ?>
<?php $tpl = is_dir(DIR_TEMPLATE. $this->config->get('config_template') ."/shared") ? $this->config->get('config_template') : "choroni"; ?>

<!--contentContainer -->
<div id="contentContainer" class="tpl-account-address" nt-editable>

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

                            <form class="simple-form" action="<?php echo str_replace('&', '&amp;', $action); ?>" method="post" enctype="multipart/form-data" id="form">
                                <?php include(DIR_TEMPLATE. $tpl ."/shared/fields/country.tpl"); ?>
                                <?php include(DIR_TEMPLATE. $tpl ."/shared/fields/zone.tpl"); ?>
                                <?php include(DIR_TEMPLATE. $tpl ."/shared/fields/city.tpl"); ?>
                                <?php include(DIR_TEMPLATE. $tpl ."/shared/fields/postcode.tpl"); ?>
                                <?php include(DIR_TEMPLATE. $tpl ."/shared/fields/address.tpl"); ?>

                                <div class="entry-default-address form-entry">
                                    <label for="address_1"><?php echo $l('text_label_check_address'); ?></label>
                                    <input type="checkbox" id="default" name="default" value="1"<?php if ($default) { ?> checked="checked"<?php } ?> title="Seleccione si desea utilizar esta direcci&oacute;n como predeterminada" />
                                </div>
                                <input type="hidden" name="company" value="<?php echo $company??""; ?>" />
                                <input type="hidden" name="firstname" value="<?php echo $firstname??""; ?>" />
                                <input type="hidden" name="lastname" value="<?php echo $lastname??""; ?>" />
                                <div class="necoform-actions" data-actions="necoform"></div>
                            </form>


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

<script type="text/javascript">
    window.deferjQuery(function () {
        $('#form').ntForm();
        $('#zone_id').load('index.php?r=account/address/zone&country_id=<?php echo $country_id; ?>&zone_id=<?php echo $zone_id; ?>');
    });
</script>

<?php echo $footer; ?>