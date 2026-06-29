<a class="button" onclick="loadFormWidgets();">Reload Widgets</a>
<div id="widgetsFormWrapper">
<?php if ($post_id) { ?>
<img src="<?php echo str_replace('%theme%',$this->config->get('config_admin_template'),HTTP_ADMIN_THEME_IMAGE); ?>loader.gif" alt="Cargando" />
<script>
$(function(){
    window.widgetsLoadUrl = '<?php echo $Url::createAdminUrl("style/widget/load",array("ot"=>"post","oid"=>(int)$post_id)); ?>';
    window.ot = "post";
    window.oid = '<?php echo (int)$post_id; ?>';
    window.imageFolderUrl = '<?php echo str_replace('%theme%', $Config->get('config_admin_template'), HTTP_ADMIN_THEME_IMAGE); ?>';
});
</script>
<?php } else { ?>
<p>Debe guardar primero!</p>
<?php } ?>
</div>