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
                <input type="text" name="name" id="name" value="<?php echo $name; ?>" placeholder="Espa&ntilde;ol Venezuela" required="required" style="width:40%" />
            </div>
            
            <div class="clear"></div>
            
            <div class="row">
                <label><?php echo $l('entry_code'); ?></label>
                <input type="text" name="code" id="code" value="<?php echo $code; ?>" placeholder="ve" required="required" style="width:40%" />
            </div>
            
            <div class="clear"></div>
            
            <div class="row">
                <label><?php echo $l('entry_locale'); ?></label>
                <input type="text" name="locale" id="locale" value="<?php echo $locale; ?>" placeholder="es_ve" required="required" style="width:40%" />
            </div>
            
            <div class="clear"></div>
            
            <div class="row">
                <label><?php echo $l('entry_image'); ?></label>
                <input type="text" name="image" id="image" value="<?php echo $image; ?>" placeholder="ve.png" required="required" style="width:40%" />
            </div>
            
            <div class="clear"></div>
            
            <div class="row">
                <label><?php echo $l('entry_directory'); ?></label>
                <input type="text" name="directory" id="directory" value="<?php echo $directory; ?>" placeholder="es_ve" style="width:40%" />
            </div>
            
            <div class="clear"></div>
            
            <div class="row">
                <label><?php echo $l('entry_filename'); ?></label>
                <input type="text" name="filename" id="filename" value="<?php echo $filename; ?>" placeholder="spanish.php" style="width:40%" />
            </div>
            
            <div class="clear"></div>
            
            <div class="row">
                <label><?php echo $l('entry_directory'); ?></label>
                <select title="Seleccione el estado del idioma" name="status">
                  <option value="1"<?php if ($status) { ?> selected="selected"<?php } ?>><?php echo $l('text_enabled'); ?></option>
                  <option value="0"<?php if (!$status) { ?> selected="selected"<?php } ?>><?php echo $l('text_disabled'); ?></option>
                </select>
            </div>
            
            <div class="clear"></div><br />
            
        </form>
    </div>
</div>
<?php echo $footer; ?>