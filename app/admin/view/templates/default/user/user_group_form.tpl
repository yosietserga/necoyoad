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
                <label><?php echo $l('entry_name'); ?></label>
                <input id="name" name="name" value="<?php echo isset($name) ? $name : ''; ?>" required="true" style="width:40%" />
            </div>
                        
            <div class="clear"></div><br />
            
            <a onclick="$('td input[type=checkbox]').attr('checked','checked');">Seleccionar Todos</a>&nbsp;&nbsp;&nbsp;&nbsp;
            <a onclick="$('td input[type=checkbox]').removeAttr('checked');">Seleccionar Ninguno</a>
            <div class="clear"></div><br />
                        
            <table class="list">
            
                <thead>
                    <tr>
                        <th><?php echo $l('column_module'); ?></th>
                        <th><?php echo $l('column_access'); ?></th>
                        <th><?php echo $l('column_create') ." / ". $l('column_modidy'); ?></th>
                    </tr>
                </thead>
                
                <tbody>
                <?php if (!is_array($permission)) { $permission = unserialize($permission); } ?>
                <?php foreach ($permissions as $perm) { ?>
                    <tr>
                        <td><?php echo $perm; ?></td>
                        <td style="text-align: center;"><input type="checkbox" name="permission[access][]" value="<?php echo $perm; ?>"<?php if (isset($permission['access']) && is_array($permission['access']) && in_array($perm, $permission['access'])) { ?> checked="checked"<?php } ?> showquick="off" /></td>
                        <td style="text-align: center;"><input type="checkbox" name="permission[modify][]" value="<?php echo $perm; ?>"<?php if (isset($permission['modify']) && is_array($permission['modify']) && in_array($perm, $permission['modify'])) { ?> checked="checked"<?php } ?> showquick="off" /></td>
                    </tr>
              <?php } ?>
                </tbody>
                
            </table>
        </form>
    </div>
</div>
<?php echo $footer; ?>