<div>
    <h2>Im&aacute;genes</h2>
    <div style="
        float: left;
        width: 200px;
        margin: 5px;
        padding: 5px;
        background: #fff;
        border: solid 1px #eee;
        border-radius: 3px;
        box-shadow: #d0d0d0 0px 0px 15px 4px;
    ">
        <div class="row">
            <label><?php echo $l('entry_logo'); ?></label>
            
            <a class="filemanager" data-fancybox-type="iframe" href="<?php echo $Url::createAdminUrl("common/filemanager"); ?>&amp;field=logo&amp;preview=preview_logo">
            <img src="<?php echo $preview_logo; ?>" id="preview_logo" class="image necoLogo" width="100" />
            </a>
            <br />
            <a class="filemanager" data-fancybox-type="iframe" href="<?php echo $Url::createAdminUrl("common/filemanager"); ?>&amp;field=logo&amp;preview=preview_logo" style="margin-left: 220px;color:#FFA500;font-size:10px">[ Cambiar ]</a>
            <a onclick="image_delete('logo', 'preview_logo');" style="color:#FFA500;font-size:10px">[ Quitar ]</a>
            <input type="hidden" showquick="off" id="logo" name="config_logo" value="<?php echo $config_logo; ?>" />
        </div>
    </div>
            
    <div style="
        float: left;
        width: 200px;
        margin: 5px;
        padding: 5px;
        background: #fff;
        border: solid 1px #eee;
        border-radius: 3px;
        box-shadow: #d0d0d0 0px 0px 15px 4px;
    ">
        <div class="row">
            <label><?php echo $l('entry_icon'); ?></label>
            <a class="filemanager" data-fancybox-type="iframe" href="<?php echo $Url::createAdminUrl("common/filemanager"); ?>&amp;field=icon&amp;preview=preview_icon">
            <img src="<?php echo $preview_icon; ?>" id="preview_icon" class="image necoIcon" width="100" />
            </a>
            <br />
            <a class="filemanager" data-fancybox-type="iframe" href="<?php echo $Url::createAdminUrl("common/filemanager"); ?>&amp;field=icon&amp;preview=preview_icon" style="margin-left: 220px;color:#FFA500;font-size:10px">[ Cambiar ]</a>
            <a onclick="image_delete('icon', 'preview_icon');" style="color:#FFA500;font-size:10px">[ Quitar ]</a>
            <input type="hidden" showquick="off" id="icon" name="config_icon" value="<?php echo $config_icon; ?>" />
        </div>
    </div>

    <div style="
        float: left;
        width: 200px;
        margin: 5px;
        padding: 5px;
        background: #fff;
        border: solid 1px #eee;
        border-radius: 3px;
        box-shadow: #d0d0d0 0px 0px 15px 4px;
    ">
        <div class="row">
            <label><?php echo $l('entry_email_logo'); ?></label>
            
            <a class="filemanager" data-fancybox-type="iframe" href="<?php echo $Url::createAdminUrl("common/filemanager"); ?>&amp;field=email_logo&amp;preview=preview_email_logo">
            <img src="<?php echo $preview_email_logo; ?>" id="preview_email_logo" class="image necoEmailLogo" width="100" />
            </a>
            <br />
            <a class="filemanager" data-fancybox-type="iframe" href="<?php echo $Url::createAdminUrl("common/filemanager"); ?>&amp;field=email_logo&amp;preview=preview_email_logo" style="margin-left: 220px;color:#FFA500;font-size:10px">[ Cambiar ]</a>
            <a onclick="image_delete('email_logo', 'preview_email_logo');" style="color:#FFA500;font-size:10px">[ Quitar ]</a>
            <input type="hidden" showquick="off" id="email_logo" name="config_email_logo" value="<?php echo $config_email_logo; ?>" />
        </div>
    </div>
                          
    <div style="
        float: left;
        width: 200px;
        margin: 5px;
        padding: 5px;
        background: #fff;
        border: solid 1px #eee;
        border-radius: 3px;
        box-shadow: #d0d0d0 0px 0px 15px 4px;
    ">
        <div class="row">
            <label><?php echo $l('entry_mobile_logo'); ?></label>
            
            <a class="filemanager" data-fancybox-type="iframe" href="<?php echo $Url::createAdminUrl("common/filemanager"); ?>&amp;field=mobile_logo&amp;preview=preview_mobile_logo">
            <img src="<?php echo $preview_mobile_logo; ?>" id="preview_mobile_logo" class="image necoMobileLogo" width="100" />
            </a>
            <br />
            <a class="filemanager" data-fancybox-type="iframe" href="<?php echo $Url::createAdminUrl("common/filemanager"); ?>&amp;field=mobile_logo&amp;preview=preview_mobile_logo" style="margin-left: 220px;color:#FFA500;font-size:10px">[ Cambiar ]</a>
            <a onclick="image_delete('mobile_logo', 'preview_mobile_logo');" style="color:#FFA500;font-size:10px">[ Quitar ]</a>
            <input type="hidden" showquick="off" id="mobile_logo" name="config_mobile_logo" value="<?php echo $config_mobile_logo; ?>" />
        </div>
    </div>
               
    <div class="clear"></div>
    <hr />
    <div class="clear"></div>
              
    <div style="
        float: left;
        width: 200px;
        margin: 5px;
        padding: 5px;
        background: #fff;
        border: solid 1px #eee;
        border-radius: 3px;
        box-shadow: #d0d0d0 0px 0px 15px 4px;
    ">
        <div class="row necoImage01">
            <label><?php echo $l('entry_image_thumb'); ?></label>
            <input type="necoNumber" name="config_image_thumb_width" value="<?php echo $config_image_thumb_width; ?>" size="3" required="true"<?php if (isset($error_image_thumb)) echo ' class="neco-input-error'; ?> />
            <input type="necoNumber" name="config_image_thumb_height" value="<?php echo $config_image_thumb_height; ?>" size="3" required="true"<?php if (isset($error_image_thumb)) echo ' class="neco-input-error'; ?> />
        </div>
    </div>
                    
    <div style="
        float: left;
        width: 200px;
        margin: 5px;
        padding: 5px;
        background: #fff;
        border: solid 1px #eee;
        border-radius: 3px;
        box-shadow: #d0d0d0 0px 0px 15px 4px;
    ">
        <div class="row necoImage02">
            <label><?php echo $l('entry_image_popup'); ?></label>
            <input type="necoNumber" name="config_image_popup_width" value="<?php echo $config_image_popup_width; ?>" size="3" required="true"<?php if (isset($error_image_popup)) echo ' class="neco-input-error'; ?> />
            <input type="necoNumber" name="config_image_popup_height" value="<?php echo $config_image_popup_height; ?>" size="3" required="true"<?php if (isset($error_image_popup)) echo ' class="neco-input-error'; ?> />
        </div>
    </div>
                               
    <div style="
        float: left;
        width: 200px;
        margin: 5px;
        padding: 5px;
        background: #fff;
        border: solid 1px #eee;
        border-radius: 3px;
        box-shadow: #d0d0d0 0px 0px 15px 4px;
    ">
        <div class="row necoImage03">
            <label><?php echo $l('entry_image_category'); ?></label>
            <input type="necoNumber" name="config_image_category_width" value="<?php echo $config_image_category_width; ?>" size="3" required="true"<?php if (isset($error_image_category)) echo ' class="neco-input-error'; ?> />
            <input type="necoNumber" name="config_image_category_height" value="<?php echo $config_image_category_height; ?>" size="3" required="true"<?php if (isset($error_image_category)) echo ' class="neco-input-error'; ?> />
        </div>
    </div>
                          
    <div style="
        float: left;
        width: 200px;
        margin: 5px;
        padding: 5px;
        background: #fff;
        border: solid 1px #eee;
        border-radius: 3px;
        box-shadow: #d0d0d0 0px 0px 15px 4px;
    ">
        <div class="row necoImage04">
            <label><?php echo $l('entry_image_post'); ?></label>
            <input type="necoNumber" name="config_image_post_width" value="<?php echo $config_image_post_width; ?>" size="3" required="true"<?php if (isset($error_image_post)) echo ' class="neco-input-error'; ?> />
            <input type="necoNumber" name="config_image_post_height" value="<?php echo $config_image_post_height; ?>" size="3" required="true"<?php if (isset($error_image_post)) echo ' class="neco-input-error'; ?> />
        </div>
    </div>
                        
    <div style="
        float: left;
        width: 200px;
        margin: 5px;
        padding: 5px;
        background: #fff;
        border: solid 1px #eee;
        border-radius: 3px;
        box-shadow: #d0d0d0 0px 0px 15px 4px;
    ">
        <div class="row necoImage05">
            <label><?php echo $l('entry_image_product'); ?></label>
            <input type="necoNumber" name="config_image_product_width" value="<?php echo $config_image_product_width; ?>" size="3" required="true"<?php if (isset($error_image_product)) echo ' class="neco-input-error'; ?> />
            <input type="necoNumber" name="config_image_product_height" value="<?php echo $config_image_product_height; ?>" size="3" required="true"<?php if (isset($error_image_product)) echo ' class="neco-input-error'; ?> />
        </div>
    </div>
                      
    <div style="
        float: left;
        width: 200px;
        margin: 5px;
        padding: 5px;
        background: #fff;
        border: solid 1px #eee;
        border-radius: 3px;
        box-shadow: #d0d0d0 0px 0px 15px 4px;
    ">
        <div class="row necoImage06">
            <label><?php echo $l('entry_image_additional'); ?></label>
            <input type="necoNumber" name="config_image_additional_width" value="<?php echo $config_image_additional_width; ?>" size="3" required="true"<?php if (isset($error_image_additional)) echo ' class="neco-input-error'; ?> />
            <input type="necoNumber" name="config_image_additional_height" value="<?php echo $config_image_additional_height; ?>" size="3" required="true"<?php if (isset($error_image_additional)) echo ' class="neco-input-error'; ?> />
        </div>
    </div>
                       
    <div style="
        float: left;
        width: 200px;
        margin: 5px;
        padding: 5px;
        background: #fff;
        border: solid 1px #eee;
        border-radius: 3px;
        box-shadow: #d0d0d0 0px 0px 15px 4px;
    ">
        <div class="row necoImage07">
            <label><?php echo $l('entry_image_related'); ?></label>
            <input type="necoNumber" name="config_image_related_width" value="<?php echo $config_image_related_width; ?>" size="3" required="true"<?php if (isset($error_image_related)) echo ' class="neco-input-error'; ?> />
            <input type="necoNumber" name="config_image_related_height" value="<?php echo $config_image_related_height; ?>" size="3" required="true"<?php if (isset($error_image_related)) echo ' class="neco-input-error'; ?> />
        </div>
    </div>
                     
    <div style="
        float: left;
        width: 200px;
        margin: 5px;
        padding: 5px;
        background: #fff;
        border: solid 1px #eee;
        border-radius: 3px;
        box-shadow: #d0d0d0 0px 0px 15px 4px;
    ">
        <div class="row necoImage08">
            <label><?php echo $l('entry_image_cart'); ?></label>
            <input type="necoNumber" name="config_image_cart_width" value="<?php echo $config_image_cart_width; ?>" size="3" required="true"<?php if (isset($error_image_cart)) echo ' class="neco-input-error'; ?> />
            <input type="necoNumber" name="config_image_cart_height" value="<?php echo $config_image_cart_height; ?>" size="3" required="true"<?php if (isset($error_image_cart)) echo ' class="neco-input-error'; ?> />
        </div>
    </div>
</div>