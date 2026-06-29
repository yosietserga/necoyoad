<div itemprop="aggregateRating" itemscope itemtype="https://schema.org/AggregateRating" id="<?php echo $widgetName; ?>_average">
    <span itemprop="ratingValue"><?php echo (float)$average; ?></span>
    <span>Rating</span>
    <img src="<?php echo HTTP_IMAGE; ?>stars_<?php echo (int)$average . '.png'; ?>" alt="<?php echo $l('text_stars'); ?>" />
</div>