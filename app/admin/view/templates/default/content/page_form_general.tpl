<div class="row">
    <label><?php echo $l('Internal Name'); ?></label>
    <input name="internal_name" title="Just for internal purposes, this does not show to customers" value="<?php echo isset($internal_name) ? $internal_name : ''; ?>" style="width:40%" />
</div>
<br />
<hr />

<?php require_once(dirname(__FILE__)."/../shared/form/general_descriptions.tpl"); ?>