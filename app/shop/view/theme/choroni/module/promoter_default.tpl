<?php if ($google_client_id) { ?>
<a class="socialSmallButton googleButton" href="<?php echo $google_link; ?>">
    <?php echo $l('text_google_promote'); ?>
</a>
<?php } ?>

<?php if ($live_client_id) { ?>
<a class="socialSmallButton liveButton" href="<?php echo $live_link; ?>">
    <?php echo $l('text_live_promote'); ?>
</a>
<?php } ?>

<?php if ($facebook_app_id) { ?>
<a class="socialSmallButton facebookButton" href="<?php echo $facebook_link; ?>">
    <?php echo $l('text_facebook_promote'); ?>
</a>
<?php } ?>

<?php if ($twitter_oauth_token_secret) { ?>
<a class="socialSmallButton twitterButton" href="<?php echo $twitter_link; ?>">
    <?php echo $l('text_twitter_promote'); ?>
</a>
<?php } ?>