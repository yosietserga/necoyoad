<?php 
    require_once(dirname(__FILE__)."/../shared/form/data_allow_reviews.tpl");
    require_once(dirname(__FILE__)."/../shared/form/data_publish.tpl");
    require_once(dirname(__FILE__)."/../shared/form/data_date_start_end.tpl");
    require_once(dirname(__FILE__)."/../shared/form/data_view.tpl"); 
?>
            
<div class="row">
    <label><?php echo $l('entry_parent'); ?></label>
    <select name="parent_id" style="width:40%">
        <option value="0"><?php echo $l('text_none'); ?></option>
   <?php foreach ($pages as $page) {
        if (isset($post_id) && $post_id == $page['post_id']) continue; 
        ?>
        <?php if ($page['post_id']==$parent_id) { ?>
		<option value="<?php echo $page['post_id']; ?>" selected="selected"><?php echo $page['title']; ?></option>
        <?php } else { ?>
		<option value="<?php echo $page['post_id']; ?>"><?php echo $page['title']; ?></option>
        <?php } ?>
   <?php } ?>
   </select>
</div>

<?php
    $object_id = $post_id;
    require_once(dirname(__FILE__)."/../shared/form/data_customergroups.tpl");
    require_once(dirname(__FILE__)."/../shared/form/data_stores.tpl");
?>