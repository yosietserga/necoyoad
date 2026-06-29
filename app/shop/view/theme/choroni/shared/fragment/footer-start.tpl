<?php if (count($javascripts) > 0) foreach ($javascripts as $js) { if (empty($js)) continue; ?>
<script type="text/javascript" src="<?php echo $js; ?>"></script>
<?php } ?>
<?php if ($scripts) echo $scripts; ?>

<?php if (isset($styles) && is_array($styles) && count($styles) > 0) { ?>
<script type="text/javascript">
$(function(){
	<?php foreach ($styles as $s) { if (empty($s)) continue; ?>
	$('head').append('<link href="<?php echo $s['href']; ?>" rel="stylesheet" type="text/css" media="<?php echo $s['media']; ?>">');
	<?php } ?>
});
</script>
<?php } ?>

<?php if (!empty($css)) { ?>
<script type="text/javascript">
$(function(){
	$('head').append('<style> <?php echo str_replace("'", "\'", $css); ?> </style>');
});
</script>
<?php } ?>

<script>
$(function() {
    $().UItoTop();
});
</script>

