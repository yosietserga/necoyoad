<?php echo $header; ?>
<?php echo $navigation; ?>
<div class="container">
    
    <?php if (isset($breadcrumbs) && $breadcrumbs) { ?>
    <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
    </ul>
    <?php } ?>
    
    <?php if (isset($success) && $success) { ?><div class="grid_12"><div class="message success"><?php echo $success; ?></div></div><?php } ?>
    <?php if ((isset($msg) && $msg) || $error_warning) { ?><div class="grid_12"><div class="message warning"><?php echo $msg ?? $error_warning; ?></div></div><?php } ?>
    <?php if (isset($error) && $error) { ?><div class="grid_12"><div class="message error"><?php echo $error; ?></div></div><?php } ?>
    <div class="grid_12" id="msg"></div>
    
    <div class="box">
        <h1><?php echo $l('heading_title'); ?></h1>
        <?php if ($post_id) { ?><a href="<?php echo $Url::createUrl("content/page",array('page_id'=>$post_id),'NONSSL',HTTP_CATALOG); ?>" target="_blank"><?php echo $l('text_see_page_in_frontstore'); ?></a><?php } ?>
        <div class="buttons">
            <a onclick="saveAndExit();$('#form').submit();" class="button"><?php echo $l('button_save_and_exit'); ?></a>
            <a onclick="saveAndKeep();$('#form').submit();" class="button"><?php echo $l('button_save_and_keep'); ?></a>
            <a onclick="saveAndNew();$('#form').submit();" class="button"><?php echo $l('button_save_and_new'); ?></a>
            <a onclick="location = '<?php echo $cancel; ?>';" class="button"><?php echo $l('button_cancel'); ?></a>
        </div>
        
        <div class="clear"></div>
        
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">

            <div class="htabs">
                <a tab="#general" class="htab"><?php echo $l('tab_general'); ?></a>
                <a tab="#data" class="htab"><?php echo $l('tab_data'); ?></a>
                <a tab="#widgets" class="htab"><?php echo $l('Widgets'); ?></a>
            </div>

            <div id="general"><?php require_once(dirname(__FILE__)."/page_form_general.tpl"); ?></div>
            <div id="data"><?php require_once(dirname(__FILE__)."/page_form_data.tpl"); ?></div>
            <div id="widgets"><?php require_once(dirname(__FILE__)."/page_form_widgets.tpl"); ?></div>
            
        </form>
    </div>
</div>
<?php echo $footer; ?>