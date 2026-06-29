<?php echo $header; ?>
<?php echo $navigation; ?>
<div class="container">
    
    <?php if (isset($breadcrumbs) && is_array($breadcrumbs)) { ?>
    <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
    </ul>
    <?php } ?>
    
    <?php if (isset($success) && $success) { ?><div class="grid_12"><div class="message success"><?php echo $success; ?></div></div><?php } ?>
    <?php if ((isset($msg) && $msg) || (isset($error_warning) && $error_warning)) { ?><div class="grid_12"><div class="message warning"><?php echo $msg ?? $error_warning; ?></div></div><?php } ?>
    <?php if (isset($error) && $error) { ?><div class="grid_12"><div class="message error"><?php echo $error; ?></div></div><?php } ?>
    <div class="grid_12" id="msg"></div>
    
    <div class="box">
        <h1><?php echo $l('heading_title'); ?></h1>
        <?php if ($manufacturer_id) { ?><a href="<?php echo $Url::createUrl("store/manufacturer",array('manufacturer_id'=>$manufacturer_id),'NONSSL',HTTP_CATALOG); ?>" target="_blank"><?php echo $l('text_see_manufacturer_in_storefront'); ?></a><?php } ?>
        <div class="buttons">
            <a id="necoBoy" style="margin: 0px 10px;" title="NecoBoy ay&uacute;dame!"><img src="<?php echo HTTP_IMAGE; ?>necoBoy.png" alt="NecoBoy" /></a>
            <a onclick="saveAndExit();$('#form').submit();" class="button"><?php echo $l('button_save_and_exit'); ?></a>
            <a onclick="saveAndKeep();$('#form').submit();" class="button"><?php echo $l('button_save_and_keep'); ?></a>
            <a onclick="saveAndNew();$('#form').submit();" class="button"><?php echo $l('button_save_and_new'); ?></a>
            <a onclick="location = '<?php echo $cancel; ?>';" class="button"><?php echo $l('button_cancel'); ?></a>
        </div>
        
        <div class="clear"></div>
                       
        <ol id="stepsForm" class="joyRideTipContent" style="display:none">
            <li data-button="<?php echo $l('button_next'); ?>">
                <h2><?php echo $l('heading_joyride_begin'); ?></h2>
                <p><?php echo $l('help_joyride_begin'); ?></p>
            </li>
            <li data-class="necoTemplate" data-button="<?php echo $l('button_next'); ?>" data-options="tipLocation:right">
                <h2><?php echo $l('heading_joyride_01'); ?></h2>
                <p><?php echo $l('help_joyride_01'); ?></p>
            </li>
            <li data-class="necoName" data-button="<?php echo $l('button_next'); ?>" data-options="tipLocation:right">
                <h2><?php echo $l('heading_joyride_02'); ?></h2>
                <p><?php echo $l('help_joyride_02'); ?></p>
            </li>
            <li data-class="necoSeoUrl" data-button="<?php echo $l('button_next'); ?>" data-options="tipLocation:right">
                <h2><?php echo $l('heading_joyride_03'); ?></h2>
                <p><?php echo $l('help_joyride_03'); ?></p>
            </li>
            <li data-class="necoImage" data-button="<?php echo $l('button_next'); ?>" data-options="tipLocation:right">
                <h2><?php echo $l('heading_joyride_04'); ?></h2>
                <p><?php echo $l('help_joyride_04'); ?></p>
            </li>
            <li data-class="necoStore" data-button="<?php echo $l('button_next'); ?>" data-options="tipLocation:right">
                <h2><?php echo $l('heading_joyride_05'); ?></h2>
                <p><?php echo $l('help_joyride_05'); ?></p>
            </li>
            <li data-class="necoPanel" data-button="<?php echo $l('button_next'); ?>" data-options="tipLocation:right">
                <h2><?php echo $l('heading_joyride_06'); ?></h2>
                <p><?php echo $l('help_joyride_06'); ?></p>
            </li>
            <li data-button="<?php echo $l('button_close'); ?>">
                <h2><?php echo $l('heading_joyride_final'); ?></h2>
                <p><?php echo $l('help_joyride_final'); ?></p>
            </li>
        </ol>
        <script>
            $(function(){
                $('#necoBoy').on('click', function(e){
                    $('#stepsForm').joyride({
                        autoStart : true,
                        modal:false,
                        expose:true
                    });
                });
            });
        </script>
                 
        <div class="clear"></div>
                     
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">

            <div class="htabs">
                <a tab="#general" class="htab"><?php echo $l('tab_general'); ?></a>
                <a tab="#data" class="htab"><?php echo $l('tab_data'); ?></a>
                <a tab="#widgets" class="htab"><?php echo $l('Widgets'); ?></a>
            </div>

            <div id="general"><?php require_once(dirname(__FILE__)."/manufacturer_form_general.tpl"); ?></div>
            <div id="data"><?php require_once(dirname(__FILE__)."/manufacturer_form_data.tpl"); ?></div>
            <div id="widgets"><?php require_once(dirname(__FILE__)."/manufacturer_form_widgets.tpl"); ?></div>
            
        </form>
    </div>
</div>
<?php echo $footer; ?>