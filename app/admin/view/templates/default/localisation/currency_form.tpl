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
        
            <div id="languages" class="htabs2">
                <?php foreach ($languages as $language) { ?>
                <a tab="#language<?php echo $language['language_id']; ?>" class="htab2"><img src="images/flags/<?php echo $language['image']; ?>" title="<?php echo $language['name']; ?>" /> <?php echo $language['name']; ?></a>
                <?php } ?>
                <?php foreach ($languages as $language) { ?>
                <div id="language<?php echo $language['language_id']; ?>">

                    <div class="row">
                        <label><?php echo $l('entry_title'); ?></label>
                        <input class="page" id="descriptions_<?php echo $language['language_id']; ?>_title" name="descriptions[<?php echo $language['language_id']; ?>][title]" value="<?php echo isset($descriptions[$language['language_id']]) ? $descriptions[$language['language_id']]['title'] : ''; ?>" required="true" style="width:40%" />
                    </div>

                </div>
                <?php } ?>
            </div>

            <div class="clear"></div>
            
            <div class="row">
                <label><?php echo $l('entry_code'); ?></label>
                <input type="text" name="code" id="code" value="<?php echo $code; ?>" placeholder="VEB" required="required" style="width:40%" />
            </div>
            
            <div class="clear"></div>
            
            <div class="row">
                <label><?php echo $l('entry_symbol_left'); ?></label>
                <input type="text" name="symbol_left" id="symbol_left" value="<?php echo $symbol_left; ?>" placeholder="Bs." required="required" style="width:40%" />
            </div>
            
            <div class="clear"></div>
            
            <div class="row">
                <label><?php echo $l('entry_symbol_right'); ?></label>
                <input type="text" name="symbol_right" id="symbol_right" value="<?php echo $symbol_right; ?>" required="required" style="width:40%" />
            </div>
            
            <div class="clear"></div>
            
            <div class="row">
                <label><?php echo $l('entry_decimal_place'); ?></label>
                <input type="text" name="decimal_place" id="decimal_place" value="<?php echo $decimal_place; ?>" placeholder="2" required="required" style="width:40%" />
            </div>
            
            <div class="clear"></div>
            
            <div class="row">
                <label><?php echo $l('entry_value'); ?></label>
                <input type="text" name="value" id="value" value="<?php echo $value; ?>" placeholder="1" required="required" style="width:40%" />
            </div>
            
            <div class="clear"></div>
            
            <div class="row">
                <label><?php echo $l('entry_status'); ?></label>
                <select title="Seleccione el estado de la moneda" name="status">
                  <option value="1"<?php if ($status) { ?> selected="selected"<?php } ?>><?php echo $l('text_enabled'); ?></option>
                  <option value="0"<?php if (!$status) { ?> selected="selected"<?php } ?>><?php echo $l('text_disabled'); ?></option>
                </select>
            </div>
            
            <div class="clear"></div>
            
        </form>
    </div>
</div>

<?php echo $footer; ?>