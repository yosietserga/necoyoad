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

            <ul id="vtabs" class="vtabs">
                <li><a data-target="#tab_general" onclick="showTab(this)"><?php echo $l('tab_general'); ?></a></li>
                <?php foreach ($geo_zones as $geo_zone) { ?>
                <li><a data-target="#tab_geo_zone<?php echo $geo_zone['geo_zone_id']; ?>" onclick="showTab(this)"><?php echo $geo_zone['name']; ?></a></li>
                <?php } ?>
            </ul> 
            
            <div id="tab_general" class="vtabs_page" style="float:left">
                <h2>General</h2>
                <div class="row">
                    <label><?php echo $l('entry_tax'); ?></label>
                    <select name="weight_tax_class_id">
                        <option value="0"><?php echo $l('text_none'); ?></option>
                        <?php foreach ($tax_classes as $tax_class) { ?>
                        <option value="<?php echo $tax_class['tax_class_id']; ?>" <?php if ($tax_class['tax_class_id'] == $weight_tax_class_id) { ?> selected="selected"<?php } ?>><?php echo $tax_class['title']; ?></option>
                        <?php } ?>
                    </select>
                </div>
                            
                <div class="row">
                    <label><?php echo $l('entry_status'); ?></label>
                    <select name="weight_status">
                        <option value="1"<?php if ($weight_status) { ?> selected="selected"<?php } ?>><?php echo $l('text_enabled'); ?></option>
                        <option value="0"<?php if (!$weight_status) { ?> selected="selected"<?php } ?>><?php echo $l('text_disabled'); ?></option>
                    </select>
                </div>
                   
                <div class="clear"></div>
                        
                <div class="row">
                    <label><?php echo $l('entry_sort_order'); ?></label>
                    <input title="<?php echo $l('help_sort_order'); ?>" type="text" name="weight_sort_order" value="<?php echo $weight_sort_order; ?>" style="width: 40%;" />
                </div>
                   
            </div>
        
            <?php foreach ($geo_zones as $geo_zone) { ?>
            <div id="tab_geo_zone<?php echo $geo_zone['geo_zone_id']; ?>" class="vtabs_page" style="float:left">
                <h2><?php echo $geo_zone['name']; ?></h2>
                    <div class="row">
                        <label><?php echo $l('entry_rate'); ?></label>
                        <textarea name="weight_<?php echo $geo_zone['geo_zone_id']; ?>_rate" cols="40" rows="5"><?php echo ${'weight_' . $geo_zone['geo_zone_id'] . '_rate'}; ?></textarea>
                    </div>

                    <div class="row">
                        <label><?php echo $l('entry_status'); ?></label>
                        <select name="weight_<?php echo $geo_zone['geo_zone_id']; ?>_status">
                            <option value="1"<?php if (${'weight_' . $geo_zone['geo_zone_id'] . '_status'}) { ?> selected="selected"<?php } ?>><?php echo $l('text_enabled'); ?></option>
                            <option value="0"<?php if (!${'weight_' . $geo_zone['geo_zone_id'] . '_status'}) { ?> selected="selected"<?php } ?>><?php echo $l('text_disabled'); ?></option>
                        </select>
                    </div>

            </div>  
            <?php } ?>
        </form>
    </div>
</div>
<?php echo $footer; ?>