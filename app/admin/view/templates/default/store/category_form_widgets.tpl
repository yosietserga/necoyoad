<a class="button" onclick="loadFormWidgets();">Reload Widgets</a>
<div id="widgetsFormWrapper">
<?php if ($category_id) { ?>
<img src="<?php echo str_replace('%theme%',$this->config->get('config_admin_template'),HTTP_ADMIN_THEME_IMAGE); ?>loader.gif" alt="Cargando" />
<script>
$(function(){
    window.widgetsLoadUrl = '<?php echo $Url::createAdminUrl("style/widget/load",array("ot"=>"category","oid"=>(int)$category_id)); ?>';
    window.ot = "category";
    window.oid = '<?php echo (int)$category_id; ?>';
    window.imageFolderUrl = '<?php echo str_replace('%theme%', $Config->get('config_admin_template'), HTTP_ADMIN_THEME_IMAGE); ?>';
});
</script>
<?php } else { ?>
<p>Debe guardar primero!</p>
<?php } ?>
</div>