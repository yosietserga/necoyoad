<div class="clear"></div>
<div class="row">
    <label><?php echo $l('entry_category'); ?></label>
    <select name="parent_id" style="width:40%">
        <option value="0"><?php echo $l('text_none'); ?></option>
   <?php foreach ($categories as $category) { 
        if (isset($category_id) && $category_id == $category['category_id']) continue; 
    ?>
		<option value="<?php echo $category['category_id']; ?>"<?php if ($category['category_id']==$parent_id) { ?> selected="selected"<?php } ?>><?php echo $category['title']; ?></option>
   <?php } ?>
   </select>
</div>