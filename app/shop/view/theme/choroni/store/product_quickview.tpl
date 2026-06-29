<?php $tpl = is_dir(DIR_TEMPLATE. $this->config->get('config_template') ."/shared") ? $this->config->get('config_template') : "choroni"; ?> 
<div class="tpl-product_quickview" nt-editable>
    <?php include(DIR_TEMPLATE. $tpl ."/shared/widgets-common.tpl");?>
</div>
