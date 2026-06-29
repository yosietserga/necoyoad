<div class="clear"></div>
<?php if (!isset($object_id)) throw new Exception("Must declare variable object_id before include customer groups form partial"); ?>
<?php if (isset($customerGroups) && is_array($customerGroups) && !empty($customerGroups)) { ?>
<div class="row">
    <label><?php echo $l('entry_customer_group'); ?></label>
    <div class="clear"></div>
    <input type="text" placeholder="Filtrar listado" value="" name="q" id="q" />
    <div class="clear"></div>
    <ul id="customerGroupsWrapper" class="scrollbox" data-scrollbox="1">
        <li>
            <input id="scrollboxCustomerGroups0" type="checkbox" name="customer_groups[]" value="0"<?php if (in_array(0, (array)$customer_groups) || !$object_id) { ?> checked="checked"<?php } ?> showquick="off" onchange="$('.customerGroups input').prop('checked', this.checked);" />
            <label for="scrollboxCustomerGroups0"><?php echo $l('text_all_public'); ?></label>
        </li>
        <?php foreach ($customerGroups as $group) { ?>
        <li class="customerGroups">

            <input id="scrollboxCustomerGroups<?php echo (int)$group['customer_group_id']; ?>" type="checkbox" name="customer_groups[]" value="<?php echo $group['customer_group_id']; ?>"<?php if (in_array($group['customer_group_id'], (array)$customer_groups) || in_array(0, (array)$customer_groups) || !$object_id) { ?> checked="checked"<?php } ?> showquick="off" />
            <label for="scrollboxCustomerGroups<?php echo (int)$group['customer_group_id']; ?>">
                <?php echo $group['name']; ?></label>
        </li>
        <?php } ?>
    </ul>
</div>
<?php } else { ?>
<input type="hidden" name="customer_groups[]" value="0" />
<?php } ?>