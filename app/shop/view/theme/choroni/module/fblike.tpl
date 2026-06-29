<?php $tpl = is_dir(DIR_TEMPLATE. $this->config->get('config_template') ."/shared") ? $this->config->get('config_template') : "choroni"; ?>
<?php include(DIR_TEMPLATE. $tpl ."/shared/widget-head.tpl");?> 

    <iframe src="https://www.facebook.com/plugins/likebox.php?id=<?php echo $pageid;?>&amp;width=<?php echo $width;?>&amp;connections=<?php echo $totalconnection;?>&amp;stream=<?php echo $stream;?>&amp;header=<?php echo $header;?>&amp;height=<?php echo $height;?>" scrolling="no" frameborder="0px" style="border:none; padding:0px; overflow:hidden; width:<?php echo $width;?>px; height:<?php echo $height;?>px;" allowtransparency="true"></iframe>
<?php include(DIR_TEMPLATE. $tpl ."/shared/widget-footer.tpl");?>