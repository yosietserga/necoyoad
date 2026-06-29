<div id="<?php echo $widgetName; ?>_msgReview"></div>
<div class="clearfix"></div>
<div class="content">
    <div>
        <div class="detail">
            <textarea name="text" id="<?php echo $widgetName; ?>_text" placeholder="Escribe tu pregunta o comentario aqu&iacute;"></textarea>
        </div>
    </div>
        <div class="label">
            <strong><?php echo $l('entry_rating'); ?><strong>
            <div class="detail">
                <a class="star_review" data-rating="1"></a>
                <a class="star_review" data-rating="2"></a>
                <a class="star_review" data-rating="3"></a>
                <a class="star_review" data-rating="4"></a>
                <a class="star_review" data-rating="5"></a>
                <input type="hidden" name="rating" id="<?php echo $widgetName; ?>_review_rating" value="0"/>
            </div>
        </div> 
    <div class="btn">
        <a title="<?php echo $l('button_continue'); ?>" onclick="review(window.nt.review.widgetName);"><?php echo $l('button_continue'); ?></a>
    </div>
</div> 
<script>
$(function(){
    initCommentForm(window.nt.review.widgetName);
});
</script>