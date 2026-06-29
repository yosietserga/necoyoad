<div id="adminTools">
    <div id="adminTopNav">
            <ul>
                <?php if ($is_admin && $_GET['theme_editor']) { ?>
                <li>
                    <a id="showAdminPanel" href="#sidr" onclick="if (!$.fn.sidr) return false;">Show Panel</a>
                </li>
                <?php } ?>
                <li class="dd">
                    <span><?php echo $l('text_create'); ?> &darr;</span>
                    <ul class="menu_body">
                        <li><a href="<?php echo $create_product; ?>" title="<?php echo $l('text_create_product'); ?>"><?php echo $l('text_create_product'); ?></a></li>
                        <li><a href="<?php echo $create_page; ?>" title="<?php echo $l('text_create_page'); ?>"><?php echo $l('text_create_page'); ?></a></li>
                        <li><a href="<?php echo $create_post; ?>" title="<?php echo $l('text_create_post'); ?>"><?php echo $l('text_create_post'); ?></a></li>
                        <li><a href="<?php echo $create_manufacturer; ?>" title="<?php echo $l('text_create_manufacturer'); ?>"><?php echo $l('text_create_manufacturer'); ?></a></li>
                        <li><a href="<?php echo $create_product_category; ?>" title="<?php echo $l('text_create_category'); ?>"><?php echo $l('text_create_category'); ?></a></li>
                        <li><a href="<?php echo $create_post_category; ?>" title="<?php echo $l('text_create_post_category'); ?>"><?php echo $l('text_create_post_category'); ?></a></li>
                    </ul>
                </li>
            </ul>
    </div>
    
    <form id="cssDataWrapper"></form>
    
    <?php if ($is_admin) { ?>
    
    <div class="panel-lateral" id="sidr" style="display: none; left: 0px;">
        <div class="panel-lateral-tabs">
            <?php if ($_GET['theme_editor']) { ?>
            <span data-tab="tabThemeConfigurator">Editor CSS</span>
            <span data-tab="tabWidgets">Widgets</span>
            <?php } ?>
            <span data-tab="tabWidgetsSettings">Configurar</span>
            <span>
    <a onclick="$.sidr('close', 'sidr')"><?php echo $l('text_close'); ?></a></span>
        </div>
        
        <?php if ($_GET['theme_editor']) { ?>
        <div class="panel-lateral-tab" id="tabThemeConfigurator"><?php require_once('admin-theme-configurator.tpl'); ?></div>
        <div class="panel-lateral-tab" id="tabWidgets"><?php require_once('admin-widgets.tpl'); ?></div>
        <?php } ?>

        <div class="panel-lateral-tab" id="tabWidgetsSettings"></div>
        
    </div>
</div>
<script>
function image_upload() {
    var height = $(window).height() * 0.8;
    var width = $(window).width() * 0.8;
                
    $('#dialog').remove();
    $('#mainbody').append('<div id="dialog" style="padding: 3px 0px 0px 0px;z-index:10000;"><iframe src="<?php echo $Url::createAdminUrl('common/filemanager',array(),'NONSSL',HTTP_ADMIN); ?>&field=backgroundImage" style="padding:0; margin: 0; display: block; width: 100%; height: 100%;z-index:10000" frameborder="no" scrolling="auto"></iframe></div>');

    $('#dialog').dialog({
        title: '<?php echo $l('text_image_manager'); ?>',
        close: function (event, ui) {
            if ($('#backgroundImage').val()) {
                $('#backgroundImage').val('<?php echo HTTP_IMAGE; ?>'+ $('#backgroundImage').val());
                setStyle();
            }
        },	
        bgiframe: false,
        width: width,
        height: height,
        resizable: false,
        modal: false
    });
}
</script>
<?php } ?>