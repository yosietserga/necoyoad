<ul>
    <li class="review_item"></li>
<?php if (!empty($reviews)) { ?>
    <?php foreach ($reviews as $review) { ?>
    <li id="<?php echo $widgetName; ?>_review_<?php echo $review['review_id']; ?>" class="review_item">
        <div class="row">
            <div class="column">
                <strong><?php echo $review['author']; ?></strong>
                <time><?php echo $review['date_added']; ?></time>
            </div>

            <div class="column">
                <img src="<?php echo HTTP_IMAGE; ?>stars_<?php echo $review['rating'] . '.png'; ?>" alt="<?php echo $review['stars']; ?>" />
            </div>

            <div class="column">
                <p><?php echo $review['text']; ?></p>
            </div>
        </div>

        <ul class="replies">
        <?php if ($review['replies']) { ?>

            <?php foreach ($review['replies'] as $reply) { ?>
            <li id="<?php echo $widgetName; ?>_reply_<?php echo $review['review_id']; ?>_<?php echo $reply['review_id']; ?>" class="reply_<?php echo $review['review_id']; ?>">
                <div class="row">
                    <div class="large-12 medium-12 small-12 column">
                        <strong><?php echo $reply['author']; ?></strong>
                        <time>
                            <?php echo date('d-m-Y h:i A',strtotime($reply['date_added'])); ?>
                        </time>
                    </div>
                    <div class="column">
                        <?php echo $reply['text']; ?>
                    </div>
                </div>
            </li>
            <?php } ?>

        <?php } ?>
        </ul>

        <?php if ($isLogged) { ?>
        <div class="review-buttons">
            <div class="btn">
                <a class="review-reply" onclick="addReply(this, '<?php echo $review['product_id']; ?>', '<?php echo $review['review_id']; ?>')">Replicar</a>
            </div>

            <footer>

                <?php if ($review['isOwner']) { ?>
                <a class="review-delete" onclick="deleteReview(this, '<?php echo $review['product_id']; ?>', '<?php echo $review['review_id']; ?>')">Eliminar</a>
                <?php } ?>

                <a class="dislikes"><?php echo (int)$review['dislikes']; ?></a>

                <a class="review-dislike" onclick="dislikeReview(this, '<?php echo $review['product_id']; ?>', '<?php echo $review['review_id']; ?>')" title="<?php echo $l('text_dislike'); ?>">
                    <?php echo $l('text_dislike'); ?>
                </a>

                <a class="likes"><?php echo (int)$review['likes']; ?></a>

                <a class="review-like" onclick="likeReview(this, '<?php echo $review['product_id']; ?>', '<?php echo $review['review_id']; ?>')" title="<?php echo $l('text_like'); ?>">
                    <?php echo $l('text_like'); ?>
                </a>

            </footer>
        </div>
        <?php } ?>

    </li>
    <?php } ?>

<?php } else { ?>
    <span id="noComments" class="no-info"><?php echo $l('text_no_reviews'); ?></span>
<?php } ?>
</ul>

<?php if (!empty($reviews) && $pagination) { ?>
<div class="pagination"><?php echo $pagination; ?></div>
<?php } ?>
