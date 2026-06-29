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
        
            <?php 
            $object_id = $user_id;
            require_once(dirname(__FILE__)."/../shared/form/data_stores.tpl"); 
            require_once(dirname(__FILE__)."/../shared/form/data_image.tpl"); 
            ?>
            
            <div class="clear"></div>

            <div class="row">
                <label><?php echo $l('entry_username'); ?></label>
                <input type="text" name="username" id="username" value="<?php echo $username; ?>" required="required" style="width:40%" />
            </div>
            
            <div class="clear"></div>
            
            <div class="row">
                <label><?php echo $l('entry_firstname'); ?></label>
                <input type="text" name="firstname" id="firstname" value="<?php echo $firstname; ?>" required="required" style="width:40%" />
            </div>
            
            <div class="clear"></div>
            
            <div class="row">
                <label><?php echo $l('entry_lastname'); ?></label>
                <input type="text" name="lastname" id="lastname" value="<?php echo $lastname; ?>" required="required" style="width:40%" />
            </div>
            
            <div class="clear"></div>
            
            <div class="row">
                <label><?php echo $l('entry_email'); ?></label>
                <input type="email" name="email" id="email" value="<?php echo $email; ?>" required="required" style="width:40%" />
            </div>
            
            <div class="clear"></div>
            
            <div class="row">
                <label><?php echo $l('entry_user_group'); ?></label>
                <select name="user_group_id">
                <?php foreach ($user_groups as $user_group) { ?>
                    <option value="<?php echo $user_group['user_group_id']; ?>"<?php if ($user_group['user_group_id'] == $user_group_id) { ?> selected="selected"<?php } ?>><?php echo $user_group['name']; ?></option>
                <?php } ?>
                </select>
            </div>
            
            <div class="clear"></div>
            
            <div class="row">
                <label><?php echo $l('entry_password'); ?></label>
                <input type="password" name="password" id="password" value="" required="required" autocomplete="off" style="width:40%" />
            </div>
            
            <div class="clear"></div>
            
            <div class="row">
                <label><?php echo $l('entry_confirm'); ?></label>
                <input type="password" name="confirm" id="confirm" value="" required="required" autocomplete="off" style="width:40%" />
            </div>
            
            <div class="clear"></div>
            
            <div class="row">
                <label><?php echo $l('entry_status'); ?></label>
                <select name="status">
                    <option value="0"<?php if (!$status) { ?> selected="selected"<?php } ?>><?php echo $l('text_disabled'); ?></option>
                    <option value="1"<?php if ($status) { ?> selected="selected"<?php } ?>><?php echo $l('text_enabled'); ?></option>
                </select>
            </div>
            
            <div class="clear"></div>

        </form>
    </div>
</div>
<?php echo $footer; ?>