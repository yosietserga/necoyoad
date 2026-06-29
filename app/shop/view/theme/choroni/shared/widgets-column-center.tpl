<!--center-column-->
<?php if ($column_left && $column_right) { ?>
<div class="large-6 medium-6 small-12">
<?php } elseif ($column_left || $column_right) { ?>
<div class="large-9 medium-9 small-12">
<?php } else { ?>
<div class="large-12 medium-12 small-12">
<?php } ?>
    <div id="columnCenter" nt-editable>
        <?php $position = 'main'; ?>
        <?php include("widgets-rows.tpl");?>
    </div>
</div>