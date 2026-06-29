<div class="row">
	<div class="links medium-12 small-12">
		<div class="heading">
			<h3>
				<?php echo $l('text_my_orders');?>
			</h3>
		</div>

		<ul>
			<li>
				<a href="<?php echo $Url::createUrl("account/order"); ?>" title="<?php echo $l('text_history'); ?>"><?php echo $l('text_history'); ?></a>
			</li>
			<li>
				<a href="<?php echo $Url::createUrl("account/payment"); ?>" title="<?php echo $l('text_payments'); ?>"><?php echo $l('text_payments'); ?></a>
			</li>
			<li>
				<a href="<?php echo $Url::createUrl("account/review"); ?>" title="<?php echo $l('Comments'); ?>"><?php echo $l('Comments'); ?></a>
			</li>

            <?php 
                $menus = $modelExtension->getMenuTemplates('account', 'orders');
                foreach ($menus as $tpl_file) {
                    if (file_exists($tpl_file)) {
                        include_once($tpl_file);
                    }
                }
            ?>
		</ul>
	</div>

	<div class="links medium-12 small-12">
		<div class="heading">
			<h3>
				<?php echo $l('text_account');?>

			</h3>
		</div>

		<ul>
			<li>
				<a href="<?php echo $Url::createUrl("account/edit"); ?>" title="<?php echo $l('text_my_account');?>"><?php echo $l('text_my_account'); ?></a>
			</li>
			<li>
				<a href="<?php echo $Url::createUrl("account/review"); ?>" title="<?php echo $l('text_my_comment');?>"><?php echo $l('text_my_comment'); ?></a>
			</li>
			<li>
				<a href="<?php echo $Url::createUrl("account/address"); ?>" title="<?php echo $l('text_address');?>"><?php echo $l('text_address'); ?></a>
			</li>
			<li>
				<a href="<?php echo $Url::createUrl("account/password"); ?>" title="<?php echo $l('text_password');?>"><?php echo $l('text_password');?></a>
			</li>

            <?php 
                $menus = $modelExtension->getMenuTemplates('account', 'account');
                foreach ($menus as $tpl_file) {
                    if (file_exists($tpl_file)) {
                        include_once($tpl_file);
                    }
                }
            ?>
		</ul>

	</div>
	
    <?php 
        $menus = $modelExtension->getMenuTemplates('account', 'custom');
        foreach ($menus as $tpl_file) {
            if (file_exists($tpl_file)) {
                include_once($tpl_file);
            }
        }
    ?>
</div>