<p><?php echo $settings['message']; ?></p>
<script>
	setTimeout(()=>{
		location.href = '<?php echo $settings['redirect_to']; ?>';
	}, <?php echo (int)$settings['delay']; ?> * 1000);
</script>