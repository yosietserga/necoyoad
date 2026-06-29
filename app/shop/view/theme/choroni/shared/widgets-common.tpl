<?php include("widgets-featured.tpl");?>

<!--mainContentContainer -->
<div id="mainContentContainer" nt-editable>
    <div class="row">

        <?php include("breadcrumbs.tpl");?>

        <div class="clear"></div>

        <!-- left-column -->
        <?php if ($column_left) { ?>
        <?php include("widgets-column-left.tpl");?>
        <?php } ?>
        <!--/left-column -->

        <!--center-column -->
        <?php include("widgets-column-center.tpl");?>
        <!--/center-column -->

        <!-- right-column -->
        <?php if ($column_right) { ?>
        <?php include("widgets-column-right.tpl");?>
        <?php } ?>
        <!--/right-column -->

    </div>
</div>
<!--/mainContentContainer -->

<!--featuredFooterContainer -->
<?php include("widgets-featured-footer.tpl");?>
<!--/featuredFooterContainer -->
