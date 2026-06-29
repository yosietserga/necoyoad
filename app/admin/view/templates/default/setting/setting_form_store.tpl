<div>
    <h2>Tienda</h2>

    <div class="clear"></div>
    <hr />
    <div class="clear"></div>

    <div style="
        float: left;
        width: 22%;
        margin: 5px 1%;
        padding: 2px;
        background: #fff;
        border: solid 1px #eee;
        border-radius: 3px;
        box-shadow: #eee 0px 0px 15px 4px;
    ">
        <div class="row">
            <label><?php echo $l('entry_template'); ?></label>
            <select class="necoTemplate" name="config_template" onchange="$('#template').load('<?php echo $Url::createAdminUrl("store/store/template"); ?>&template=' + encodeURIComponent(this.value));">
            <?php foreach ($templates as $template) { ?>
                
                <option value="<?php echo $template; ?>"<?php if ($template == $config_template) { ?> selected="selected"<?php } ?>><?php echo $template; ?></option>
            <?php } ?>
            </select>
            <div class="clear"></div>
            <div id="template"></div>
        </div>
    </div>
                    
    <div style="
        float: left;
        width: 22%;
        margin: 5px 1%;
        padding: 2px;
        background: #fff;
        border: solid 1px #eee;
        border-radius: 3px;
        box-shadow: #eee 0px 0px 15px 4px;
    ">
        <div class="row">
        <label><?php echo $l('entry_mobile_template'); ?></label>
        <select name="config_mobile_template" onchange="$('#mobile_template').load('<?php echo $Url::createAdminUrl("setting/setting/template"); ?>&template=' + encodeURIComponent(this.value));">
        <?php foreach ($templates as $template) { ?>
            
            <option value="<?php echo $template; ?>"<?php if ($template == $config_mobile_template) { ?> selected="selected"<?php } ?>><?php echo $template; ?></option>
        <?php } ?>
        </select>
        <div class="clear"></div>
        <div id="mobile_template"></div>
    </div>
    </div>
          
    <div style="
        float: left;
        width: 22%;
        margin: 5px 1%;
        padding: 2px;
        background: #fff;
        border: solid 1px #eee;
        border-radius: 3px;
        box-shadow: #eee 0px 0px 15px 4px;
    ">
        <div class="row">
        <label><?php echo $l('entry_tablet_template'); ?></label>
        <select name="config_tablet_template" onchange="$('#tablet_template').load('<?php echo $Url::createAdminUrl("setting/setting/template"); ?>&template=' + encodeURIComponent(this.value));">
        <?php foreach ($templates as $template) { ?>
            
            <option value="<?php echo $template; ?>"<?php if ($template == $config_tablet_template) { ?> selected="selected"<?php } ?>><?php echo $template; ?></option>
        <?php } ?>
        </select>
        <div class="clear"></div>
        <div id="tablet_template"></div>
    </div>
    </div>
            
    <div style="
        float: left;
        width: 22%;
        margin: 5px 1%;
        padding: 2px;
        background: #fff;
        border: solid 1px #eee;
        border-radius: 3px;
        box-shadow: #eee 0px 0px 15px 4px;
    ">
        <div class="row">
        <label><?php echo $l('entry_facebook_template'); ?></label>
        <select name="config_facebook_template" onchange="$('#facebook_template').load('<?php echo $Url::createAdminUrl("setting/setting/template"); ?>&template=' + encodeURIComponent(this.value));">
        <?php foreach ($templates as $template) { ?>
            
            <option value="<?php echo $template; ?>"<?php if ($template == $config_facebook_template) { ?> selected="selected"<?php } ?>><?php echo $template; ?></option>
        <?php } ?>
        </select>
        <div class="clear"></div>
        <div id="facebook_template"></div>
    </div>
    </div>
                       
    <div class="clear"></div>
    <hr />
    <div class="clear"></div>
         
    <div id="languages" class="htabs">
    <?php foreach ($languages as $language) { ?>    
        <a tab="#language<?php echo $language['language_id']; ?>" class="htab"><img src="images/flags/<?php echo $language['image']; ?>" title="<?php echo $language['name']; ?>" /> <?php echo $language['name']; ?></a>
    <?php } ?>
    </div> 
    
    <?php foreach ($languages as $language) { ?>
    <div id="language<?php echo $language['language_id']; ?>">
    
        <div class="row">
            <label><?php echo $l('entry_title'); ?></label>
            <input type="text" name="config_title_<?php echo $language['language_id']; ?>" value="<?php echo ${'config_title_' . $language['language_id']}; ?>" required="true"<?php if (isset($error_title)) echo ' class="neco-input-error'; ?> />
        </div>
        
        <div class="clear"></div>
        
        <div class="row">
            <label><?php echo $l('entry_meta_description'); ?></label>
            <textarea name="config_meta_description_<?php echo $language['language_id']; ?>"><?php echo ${'config_meta_description_' . $language['language_id']}; ?></textarea>
        </div>
        
        <div class="clear"></div>
        
        <div class="row">
            <label><?php echo $l('entry_description'); ?></label>
            <div class="clear"></div>
            <textarea name="config_description_<?php echo $language['language_id']; ?>" id="description<?php echo $language['language_id']; ?>"><?php echo ${'config_description_' . $language['language_id']}; ?></textarea>
        </div>
        
        <div class="clear"></div>
    </div>
    <?php } ?>

    <div class="row">
        <label><?php echo $l('Footer Powered Text'); ?></label>
        <div class="clear"></div>
        <textarea name="config_text_powered_by"><?php echo $config_text_powered_by; ?></textarea>
    </div>

    <div class="clear"></div>
</div>         
