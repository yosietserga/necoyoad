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
        <p><b>NOTA:</b> Para disfrutar de las nuevas virtudes de la aplicaci&oacute;n, debes utilizar <a href="https://www.mozilla.org/es-ES/download/?product=firefox-16.0.2&os=win&lang=es-ES" title="Descargar Mozilla" target="_blank">Mozilla Firefox</a> o <a href="https://www.google.com/intl/es/chrome/browser/?hl=es" title="Descargar Google Chrome" target="_blank">Google Chrome</a> en sus &uacute;ltimas versiones.</p>
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
        
            <div class="row">
                <label><?php echo $l('entry_name'); ?></label>
                <input type="text" id="name" name="name" value="<?php echo $name; ?>" required="required" style="width:40%" />
            </div>
            
            <div class="row">
                <label><?php echo $l('entry_template'); ?></label>
                <select name="email_template" id="email_template" size="10" onchange="readPremadeTemplate();" style="width:40%;height:150px;">
                <?php foreach ($templates as $key => $template) { ?>                    
                    <?php if (is_array($template)) { ?>
                        <optgroup label="<?php echo $key; ?>">
                        <?php foreach ($template as $tpl) { ?>
                            <option value="<?php echo $key .'.'. $tpl; ?>"><?php echo $tpl; ?></option>
                        <?php } ?>
                       </optgroup>
                    <?php } else { ?>
                        <option value="<?php echo $template; ?>"<?php if ($template == $config_template) { ?> selected="selected"<?php } ?>><?php echo $template; ?></option>
                   <?php } ?>
                   
                <?php } ?>
                </select>
            </div>
            
            <div class="row">
                <label><?php echo $l('entry_category'); ?></label>
                <select name="category" onchange="getAll()" style="width:40%">
                    <option value="">Selecciona un categor&iacute;a</option>
                    <?php foreach ($categories as $category) { ?>
                    <option value="<?php echo $category['category_id']; ?>"><?php echo $category['name']; ?></option>
                    <?php } ?>
                </select>
                <div id="products"></div>
            </div>
            
            <div class="row">
                <label><?php echo $l('entry_html_content'); ?></label>
                <div class="clear"></div><br />
                <textarea name="htmlbody" id="htmlbody" required="required"><?php if (isset($htmlbody)) echo $htmlbody; ?></textarea>
            </div>
            
            <div class="row">
                <label><?php echo $l('entry_text_content'); ?></label>
                <div class="clear"></div><br />
                <textarea name="textbody" id="textbody" rows="15" style="width:80%" required="required"><?php if (isset($textbody)) echo $textbody; ?></textarea>
            </div>
            
            <div class="clear"></div><br />
        </form>
    </div>
</div>
<?php echo $footer; ?>