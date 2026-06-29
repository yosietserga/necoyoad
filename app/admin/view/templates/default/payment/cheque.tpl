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
            <a onclick="location = '<?php echo $cancel; ?>';" class="button"><?php echo $l('button_cancel'); ?></a>
        </div>
        
        <div class="clear"></div>
                                
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">

            <div class="row">
                <label><?php echo $l('entry_image'); ?></label>
                <input type="hidden" name="cheque_image" value="<?php echo $cheque_image; ?>" id="image" />
                <img alt="Logo" src="<?php echo $preview; ?>" id="preview" class="image" onclick="image_upload('image', 'preview');" width="100" height="100" />
                <br />
                <a onclick="image_upload('image', 'preview');" style="margin-left: 220px;color:#FFA500;font-size:10px">[ Cambiar ]</a>
                <a onclick="image_delete('image', 'preview');" style="color:#FFA500;font-size:10px">[ Quitar ]</a>
            </div>
                   
            <div class="clear"></div>
            
            <div class="row">
                <label><?php echo $l('entry_newsletter'); ?></label>
                <select name="cheque_newsletter_id" title="<?php echo $l('help_newsletter_id'); ?>">
                    <option value="0"><?php echo $l('text_none'); ?></option>
                    <?php foreach ($newsletters as $newsletter) { ?>
                    <option value="<?php echo $newsletter['newsletter_id']; ?>"<?php if ($newsletter['newsletter_id'] == $cheque_newsletter_id) { ?> selected="selected"<?php } ?>><?php echo $newsletter['name']; ?></option>
                    <?php } ?>
                </select>
            </div>

            <div class="clear"></div>
             
            <div class="row">
                <label><?php echo $l('entry_payable'); ?></label>
                <textarea style="width: 40%;height:250px;" name="cheque_payable"><?php echo $cheque_payable; ?></textarea>
            </div>
                        
            <div class="clear"></div>
                     
            <div class="row">
                <label><?php echo $l('entry_order_status'); ?></label>
                <select name="cheque_order_status_id">
                <?php foreach ($order_statuses as $order_status) { ?>
                    <option value="<?php echo $order_status['order_status_id']; ?>"<?php if ($order_status['order_status_id'] == $cheque_order_status_id) { ?> selected="selected"<?php } ?>><?php echo $order_status['name']; ?></option>
                <?php } ?>
                </select>
            </div>
                        
            <div class="clear"></div>
                           
            <div class="row">
                <label><?php echo $l('entry_geo_zone'); ?></label>
                <select name="cheque_geo_zone_id">
                    <option value="0"><?php echo $l('text_all_zones'); ?></option>
                    <?php foreach ($geo_zones as $geo_zone) { ?>
                    <option value="<?php echo $geo_zone['geo_zone_id']; ?>"<?php if ($geo_zone['geo_zone_id'] == $cheque_geo_zone_id) { ?> selected="selected"<?php } ?>><?php echo $geo_zone['name']; ?></option>
                    <?php } ?>
                </select>
            </div>
            
            <div class="clear"></div>
            
            <div class="row">
                <label><?php echo $l('entry_status'); ?></label>
                <select title="<?php echo $l('help_status'); ?>" name="cheque_status">
                    <option value="1"<?php if ($cheque_status) { ?> selected="selected"<?php } ?>><?php echo $l('text_enabled'); ?></option>
                    <option value="0"<?php if (!$cheque_status) { ?> selected="selected"<?php } ?>><?php echo $l('text_disabled'); ?></option>
                </select>
            </div>
            
            <div class="clear"></div>
            
            <div class="row">
                <label><?php echo $l('entry_sort_order'); ?></label>
                <input title="<?php echo $l('help_sort_order'); ?>" type="text" name="cheque_sort_order" value="<?php echo $cheque_sort_order; ?>" style="width: 40%;" />
            </div>
                   
        </form>
    </div>
</div>
<?php echo $footer; ?>