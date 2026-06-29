<div class="row">
    <label><?php echo $l('entry_name'); ?></label>
    <input class="necoName" id="name" name="name" value="<?php echo $name; ?>" required="true" style="width:40%" />
</div>
    
<div class="clear"></div>
                
<div class="row">
    <input type="hidden" id="slug" name="keyword" value="<?php echo $keyword ?? ""; ?>" style="width:40%" class="necoSeoUrl" />
</div>