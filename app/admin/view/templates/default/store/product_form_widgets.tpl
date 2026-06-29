<a class="button" onclick="loadFormWidgets();">Reload Widgets</a>
<div id="widgetsFormWrapper">
<?php if ($product_id) { ?>
<img src="<?php echo str_replace('%theme%',$this->config->get('config_admin_template'),HTTP_ADMIN_THEME_IMAGE); ?>loader.gif" alt="Cargando" />
<script>
$(function(){
    window.widgetsLoadUrl = '<?php echo $Url::createAdminUrl("style/widget/load",array("ot"=>"product","oid"=>(int)$product_id)); ?>';
    window.ot = "product";
    window.oid = '<?php echo (int)$product_id; ?>';
    window.imageFolderUrl = '<?php echo str_replace('%theme%', $Config->get('config_admin_template'), HTTP_ADMIN_THEME_IMAGE); ?>';
});
</script>
<?php } else { ?>
<p>Debe guardar primero!</p>
<?php } ?>
</div>