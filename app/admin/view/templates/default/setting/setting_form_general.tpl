<div>
    <h2>General</h2>
    <div class="row">
        <label><?php echo $l('entry_name'); ?></label>
        <input type="text" title="<?php echo $l('help_name'); ?>" name="config_name" value="<?php echo $config_name; ?>" required="true"<?php if (isset($error_name)) echo ' class="neco-input-error'; ?> />
    </div>
                                          
    <div class="clear"></div>
    
    <div class="row">
        <label><?php echo $l('entry_rif'); ?></label>
        <input type="text" title="<?php echo $l('help_rif'); ?>" name="config_rif" value="<?php echo $config_rif; ?>" required="true"<?php if (isset($error_rif)) echo ' class="neco-input-error'; ?> />
    </div>
                                      
    <div class="clear"></div>
           
    <div class="row">
        <label><?php echo $l('entry_url'); ?></label>
        <input type="url" title="<?php echo $l('help_url'); ?>" name="config_url" value="<?php echo $config_url; ?>" required="true"<?php if (isset($error_url)) echo ' class="neco-input-error'; ?> />
    </div>
                                    
    <div class="clear"></div>
           
    <div class="row">
        <label><?php echo $l('entry_mobile_url'); ?></label>
        <input type="url" title="<?php echo $l('help_mobile_url'); ?>" name="config_mobile_url" value="<?php echo $config_mobile_url; ?>" />
    </div>
                                    
    <div class="clear"></div>
           
    <div class="row">
        <label><?php echo $l('entry_tablet_url'); ?></label>
        <input type="url" title="<?php echo $l('help_tablet_url'); ?>" name="config_tablet_url" value="<?php echo $config_tablet_url; ?>" />
    </div>
                                    
    <div class="clear"></div>
           
    <div class="row">
        <label><?php echo $l('entry_facebook_url'); ?></label>
        <input type="url" title="<?php echo $l('help_facebook_url'); ?>" name="config_facebook_url" value="<?php echo $config_facebook_url; ?>" />
    </div>
                                  
    <div class="clear"></div>
    
    <div class="row">
        <label><?php echo $l('entry_owner'); ?></label>
        <input type="text" title="<?php echo $l('help_owner'); ?>" name="config_owner" value="<?php echo $config_owner; ?>" required="true"<?php if (isset($error_owner)) echo ' class="neco-input-error'; ?> />
    </div>
                                     
    <div class="clear"></div>
               
    <div class="row">
        <label><?php echo $l('entry_address'); ?></label>
        <textarea name="config_address" cols="40" rows="5" required="true"<?php if (isset($error_address)) echo ' class="neco-input-error'; ?>><?php echo $config_address; ?></textarea>
    </div>
                                          
    <div class="clear"></div>
                    
    <div class="row">
        <label><?php echo $l('entry_email'); ?></label>
        <input type="email" title="<?php echo $l('help_email'); ?>" name="config_email" value="<?php echo $config_email; ?>" required="true"<?php if (isset($error_email)) echo ' class="neco-input-error'; ?> />
    </div>
                                         
    <div class="clear"></div>
                    
    <div class="row">
        <label><?php echo $l('entry_replyto_email'); ?></label>
        <input type="email" title="<?php echo $l('help_replyto_email'); ?>" name="config_replyto_email" value="<?php echo $config_replyto_email; ?>" />
    </div>
                                         
    <div class="clear"></div>
                    
    <div class="row">
        <label><?php echo $l('entry_bounce_email'); ?></label>
        <input type="email" title="<?php echo $l('help_bounce_email'); ?>" name="config_bounce_email" value="<?php echo $config_bounce_email; ?>" />
    </div>
                                         
    <div class="clear"></div>
                    
    <div class="row">
        <label><?php echo $l('entry_telephone'); ?></label>
        <input type="text" title="<?php echo $l('help_telephone'); ?>" name="config_telephone" value="<?php echo $config_telephone; ?>" required="true"<?php if (isset($error_telephone)) echo ' class="neco-input-error'; ?> />
    </div>  
</div>