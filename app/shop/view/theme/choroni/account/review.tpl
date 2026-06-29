<?php echo $header; ?>
<?php $tpl = is_dir(DIR_TEMPLATE. $this->config->get('config_template') ."/shared") ? $this->config->get('config_template') : "choroni"; ?>

<!--contentContainer -->
<div id="contentContainer" class="tpl-account-review" nt-editable>

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

                    <?php if ($reviews) { ?>
                    <ul id="reviews" class="reviews">
                        <?php foreach ($reviews as $value) { ?>
                        <li id="pid_<?php echo $value['review_id']; ?>" class="review_item row">
                                <div class="large-10 medium-9 small-12">
                                    <p class="review-body"><?php echo $value['text']; ?></p>
                                    <time class="review-date"><?php echo $value['date_added']; ?></time>
                                    <small><a href="<?php echo $value['href']; ?>"><?php echo $l('See '. $value['object_type']); ?></a></small>
                                </div>
                                <div class="group large-2 medium-3 small-12">
                                    <a class="btn" href="<?php echo $Url::createUrl('account/review/read', array('review_id'=>$value['review_id'])); ?>">
                                        <i class="fa fa-commenting-o" aria-hidden="true"></i>
                                    </a>
                                    <a class='btn' href="javascript:void(0);" onclick="revealChoices(this,'<?php echo $value['product_id']; ?>','<?php echo $value['review_id']; ?>');" title="Eliminar">
                                        <i class="fa fa-close" aria-hidden="true"></i>
                                    </a>
                                </div>
                        </li>
                        <?php } ?>
                    </ul>
                    <?php if ($pagination) { ?><div class="pagination"><?php echo $pagination; ?></div><?php } ?>
                    <?php } else { ?>
                    <div class="no-info">
                        <?php echo $l('text_empty_page');?>
                    </div>
                    <?php } ?>




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
        (function () {
            window.deferjQuery(function () {
                var revealChoices = function (element, productId, reviewId) {
                    var confirm = [
                        "<span class='confirm'><strong>¿Seguro?</strong>",
                        "<a href='javascript:void(0)' data-choice='accept' onClick='actionDeleteReview(this, " + productId + ", " + reviewId + ")'" + ">Si</a>",
                        "<a href='javascript:void(0)' data-choice='cancel' onClick='actionDeleteReview(this, " + productId + ", " + reviewId + ")'" + ">No</a>",
                        "</span>"
                    ].join("");
                    element.outerHTML = confirm;
                };
                var actionDeleteReview = function (element, productId, reviewId) {
                    var choice = element.dataset.choice;
                    var  parent = element.parentElement;
                    var  reviews = document.getElementById("reviews");
                    var  reviewItem = document.getElementById("pid_" + reviewId);

                    if (choice === 'accept') {
                        reviews.removeChild(reviewItem);
                        $.post('<?php echo $Url::createUrl("store/product/deleteReview"); ?>&product_id='+ productId +'&review_id='+ reviewId,
                            {
                                'product_id': productId,
                                'review_id':reviewId
                            });
                    } else {
                        parent.outerHTML = "<a class='action-choice' href='javascript:void(0)' onclick='revealChoices(this, " + productId + ", " + reviewId + ")'>Eliminar</a>";
                    }
                };
                window.revealChoices = revealChoices;
                window.actionDeleteReview = actionDeleteReview;
            });
        })();
    });
</script>

<?php echo $footer; ?>