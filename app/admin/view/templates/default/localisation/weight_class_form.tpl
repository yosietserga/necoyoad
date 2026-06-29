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
        
            <?php require_once(dirname(__FILE__)."/../shared/form/general_descriptions.tpl"); ?>
            <div class="row">
                <label><?php echo $l('entry_value'); ?></label>
                <input type="text" name="value" id="value" value="<?php echo $value ?? ""; ?>" placeholder="Valor de la Medida" required="required" style="width:40%" />
            </div>
            
            <div class="clear"></div><br />
            
        </form>
    </div>
</div>
<script type="text/javascript">
$(function(){
    $('._tab').hide();
    $($('.htab:first-child').attr('tab')).show();
    $('.htab').on('click',function(e){
        $('._tab').hide();
        $($(this).attr('tab')).show();
    }); 
});
</script>
<?php echo $footer; ?>