<?php $tpl = is_dir(DIR_TEMPLATE. $this->config->get('config_template') ."/shared") ? $this->config->get('config_template') : "choroni"; ?>
<?php include(DIR_TEMPLATE. $tpl ."/shared/widget-head.tpl");?> 

    <?php include("comments_". $settings['view'] .'.tpl'); ?>
<?php include(DIR_TEMPLATE. $tpl ."/shared/widget-footer.tpl");?>

<script>
    window.nt.review = window.nt.review || {};
    if (!window.nt.review.txtButtonContinue) window.nt.review.txtButtonContinue = '<?php echo $l('button_continue'); ?>';
    if (!window.nt.review.txtConfirmDelete) window.nt.review.txtConfirmDelete = '<?php echo $l('text_confirm_delete'); ?>';
    if (!window.nt.review.txtWait) window.nt.review.txtWait = '<?php echo $l('text_wait'); ?>';
    if (!window.nt.review.txtSuccess) window.nt.review.txtSuccess = '<?php echo $l('text_success'); ?>';
    if (!window.nt.review.txtErrorText) window.nt.review.txtErrorText = '<?php echo $l('error_text'); ?>';
    if (!window.nt.review.txtErrorLogin) window.nt.review.txtErrorLogin = '<?php echo $l('error_login'); ?>';

    if (!window.nt.review.oid) window.nt.review.oid = '<?php echo $oid; ?>';
    if (!window.nt.review.ot) window.nt.review.ot = '<?php echo $ot; ?>';
    if (!window.nt.review.widgetName) window.nt.review.widgetName = '<?php echo $widgetName; ?>';
    if (!window.nt.review.isLogged) window.nt.review.isLogged = '<?php echo $this->customer->isLogged(); ?>';

    $(function(){
        $('#<?php echo $widgetName; ?>_review').load('<?php echo $Url::createUrl("store/review") ."&wid=$widgetName&ot=$ot&object_id=$oid"; ?>');
        $('#<?php echo $widgetName; ?>_comment').load('<?php echo $Url::createUrl("store/review/comment")  ."&wid=$widgetName&ot=$ot&object_id=$oid" ?>');
    });
</script>