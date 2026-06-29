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
                <label><?php echo $l('entry_payable'); ?></label>
                    <select name="debit_email_template" title="<?php echo $l('help_email_template'); ?>">
                        <option value="0"><?php echo $l('text_none'); ?></option>
                        <?php foreach ($newsletters as $newsletter) { ?>
                        <option value="<?php echo $newsletter['newsletter_id']; ?>"<?php if ($newsletter['newsletter_id'] == $debit_email_template) { ?> selected="selected"<?php } ?>><?php echo $newsletter['name']; ?></option>
                        <?php } ?>
                  </select>
            </div>
                        
            <div class="clear"></div>
                     
            <div class="row">
                <label><?php echo $l('entry_order_status'); ?></label>
                <select name="debit_order_status_id">
                <?php foreach ($order_statuses as $order_status) { ?>
                    <option value="<?php echo $order_status['order_status_id']; ?>"<?php if ($order_status['order_status_id'] == $debit_order_status_id) { ?> selected="selected"<?php } ?>><?php echo $order_status['name']; ?></option>
                <?php } ?>
                </select>
            </div>
                        
            <div class="clear"></div>
                           
            <div class="row">
                <label><?php echo $l('entry_geo_zone'); ?></label>
                <select name="debit_geo_zone_id">
                    <option value="0"><?php echo $l('text_all_zones'); ?></option>
                    <?php foreach ($geo_zones as $geo_zone) { ?>
                    <option value="<?php echo $geo_zone['geo_zone_id']; ?>"<?php if ($geo_zone['geo_zone_id'] == $debit_geo_zone_id) { ?> selected="selected"<?php } ?>><?php echo $geo_zone['name']; ?></option>
                    <?php } ?>
                </select>
            </div>
            
            <div class="clear"></div>
            
            <div class="row">
                <label><?php echo $l('entry_status'); ?></label>
                <select title="<?php echo $l('help_status'); ?>" name="debit_status">
                    <option value="1"<?php if ($debit_status) { ?> selected="selected"<?php } ?>><?php echo $l('text_enabled'); ?></option>
                    <option value="0"<?php if (!$debit_status) { ?> selected="selected"<?php } ?>><?php echo $l('text_disabled'); ?></option>
                </select>
            </div>
            
            <div class="clear"></div>
            
            <div class="row">
                <label><?php echo $l('entry_sort_order'); ?></label>
                <input title="<?php echo $l('help_sort_order'); ?>" type="text" name="debit_sort_order" value="<?php echo $debit_sort_order; ?>" style="width: 40%;" />
            </div>
                   
        </form>
    </div>
</div>
<?php echo $footer; ?>