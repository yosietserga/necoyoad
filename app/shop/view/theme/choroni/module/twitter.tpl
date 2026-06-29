<?php $tpl = is_dir(DIR_TEMPLATE. $this->config->get('config_template') ."/shared") ? $this->config->get('config_template') : "choroni"; ?>
<?php include(DIR_TEMPLATE. $tpl ."/shared/widget-head.tpl");?> 
<?php if ($heading_title) { ?><div class="header" id="<?php echo $widgetName; ?>Header"><hgroup><h1><?php echo $heading_title; ?></h1></hgroup></div><?php } ?>
    <div class="content" id="<?php echo $widgetName; ?>Content">
        <div id="twitterUserTimeline" class="tweets"></div>
        <div id="twitterSearch" class="tweets"></div>
    </div>
    <div class="clear"></div><br />
<?php include(DIR_TEMPLATE. $tpl ."/shared/widget-footer.tpl"); ?>
<script type="text/javascript" src="<?php echo HTTP_JS; ?>vendor/jquery.liveTwitter.js"></script>
<script type="text/javascript">
$('#twitterSearch').liveTwitter('<?php echo $twitter_search; ?>', {limit: <?php echo $twitter_search_limit; ?>, rate: <?php echo $twitter_search_rate; ?>});
$('#twitterUserTimeline').liveTwitter('<?php echo $twitter_time; ?>', {limit: <?php echo $twitter_time_limit; ?>, refresh: <?php echo $twitter_time_refresh; ?>, mode: '<?php echo $twitter_time_mode; ?>'});
$('#searchLinks a').each(function(){
    var query = $(this).text();
    $(this).click(function(){
    	$('#twitterSearch').liveTwitter(query);
    	$('#searchTerm').text(query);
    	return false;
     });
});
</script>