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
            <table class="form">
                <tr>
                  <td><?php echo $l('entry_total'); ?></td>
                  <td><input type="text" name="low_order_fee_total" value="<?php echo $low_order_fee_total; ?>"></td>
                </tr>
                <tr>
                  <td><?php echo $l('entry_fee'); ?></td>
                  <td><input type="text" name="low_order_fee_fee" value="<?php echo $low_order_fee_fee; ?>"></td>
                </tr>
                <tr>
                  <td><?php echo $l('entry_tax'); ?></td>
                  <td><select name="low_order_fee_tax_class_id">
                      <option value="0"><?php echo $l('text_none'); ?></option>
                      <?php foreach ($tax_classes as $tax_class) { ?>
                      <?php if ($tax_class['tax_class_id'] == $low_order_fee_tax_class_id) { ?>
                      <option value="<?php echo $tax_class['tax_class_id']; ?>" selected="selected"><?php echo $tax_class['title']; ?></option>
                      <?php } else { ?>
                      <option value="<?php echo $tax_class['tax_class_id']; ?>"><?php echo $tax_class['title']; ?></option>
                      <?php } ?>
                      <?php } ?>
                    </select></td>
                </tr>
                <tr>
                  <td><?php echo $l('entry_status'); ?></td>
                  <td><select name="low_order_fee_status">
                      <?php if ($low_order_fee_status) { ?>
                      <option value="1" selected="selected"><?php echo $l('text_enabled'); ?></option>
                      <option value="0"><?php echo $l('text_disabled'); ?></option>
                      <?php } else { ?>
                      <option value="1"><?php echo $l('text_enabled'); ?></option>
                      <option value="0" selected="selected"><?php echo $l('text_disabled'); ?></option>
                      <?php } ?>
                    </select></td>
                </tr>
                <tr>
                  <td><?php echo $l('entry_sort_order'); ?></td>
                  <td><input type="text" name="low_order_fee_sort_order" value="<?php echo $low_order_fee_sort_order; ?>" size="1"></td>
                </tr>
            </table>
        </form>
    </div>
</div>
<?php echo $footer; ?>