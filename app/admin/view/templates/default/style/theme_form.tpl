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
        <div class="buttons">
            <a onclick="saveAndExit();$('#form').submit();" class="button"><?php echo $l('button_save_and_exit'); ?></a>
            <a onclick="saveAndKeep();$('#form').submit();" class="button"><?php echo $l('button_save_and_keep'); ?></a>
            <a onclick="saveAndNew();$('#form').submit();" class="button"><?php echo $l('button_save_and_new'); ?></a>
            <a onclick="location = '<?php echo $cancel; ?>';" class="button"><?php echo $l('button_cancel'); ?></a>
        </div>
        
        <div class="clear"></div>
                                
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">

            <div class="row">
                <label><?php echo $l('entry_name'); ?></label>
                <input type="text" id="name" name="name" value="<?php echo $name; ?>" title="<?php echo $l('help_name'); ?>" required="true" style="width:40%" />
            </div>
                        
            <div class="clear"></div>
                       
            <div class="row">
                <label><?php echo $l('entry_default'); ?></label>
                <input type="checkbox" id="default" name="default" value="1" title="<?php echo $l('help_default'); ?>"<?php if ($default) { ?> checked="checked"<?php } ?> required="true" />
            </div>
                   
            <div class="clear"></div>
                   
            <div class="row">
                <label><?php echo $l('entry_date_start'); ?></label>
                <input type="necoDate" name="date_publish_start" id="date_publish_start" value="<?php echo isset($date_publish_start) ? $date_publish_start : ''; ?>" title="<?php echo $l('help_date_start'); ?>" style="width:40%" />
            </div>
            
            <div class="clear"></div>
            
            <div class="row">
                <label><?php echo $l('entry_date_end'); ?></label>
                <input type="necoDate" name="date_publish_end" id="date_publish_end" value="<?php echo isset($date_publish_end) ? $date_publish_end : ''; ?>" title="<?php echo $l('help_date_end'); ?>" style="width:40%" />
            </div>
            
            <div class="clear"></div>
                   
            <div class="row">
                <label><?php echo $l('entry_template'); ?></label>
                <select name="template" onchange="$('#template').load('<?php echo $Url::createAdminUrl("setting/setting/template"); ?>&template=' + encodeURIComponent(this.value));" title="<?php echo $l('help_template'); ?>">
                <?php foreach ($templates as $_template) { ?>
                    
                    <option value="<?php echo $_template; ?>"<?php if ($template == $_template) { ?> selected="selected"<?php } ?>><?php echo $_template; ?></option>
                <?php } ?>
                </select>
                <div class="clear"></div>
                <div style="margin-left: 220px;" id="template"></div>
            </div>
             
            <div class="clear"></div>
                   
            <div class="row">
             <?php if (isset($isSaved) && $isSaved) { ?>
                <label><?php echo $l('entry_theme_editor'); ?></label>
                <a href="<?php echo  HTTP_CATALOG; ?>/index.php?theme_editor=1&theme_id=<?php echo $this->request->getQuery('theme_id'); ?>&template=<?php echo $template; ?>" class="button" target="_blank"><?php echo $l('text_open_theme_editor'); ?></a>
             <?php }else { ?>
            <div class="warning">Debes guardar el tema primero para poder ir al editor.</div>
             <?php } ?>
            </div>
             
        </form>
    </div>
</div>
<?php echo $footer; ?>