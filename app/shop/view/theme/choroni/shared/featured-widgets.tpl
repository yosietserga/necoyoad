<!--featuredContentContainer -->
<div id="featuredContentContainer" nt-editable>
<?php $position = 'featuredContent'; ?>
<?php foreach($rows[$position] as $j => $row) { ?>
    <?php if (!$row['key']) continue; ?>
    <?php $row_id = $row['key']; ?>
    <?php $row_settings = unserialize($row['value']); ?>
    <div class="row" id="<?php echo $position; ?>_<?php echo $row_id; ?>" nt-editable>
    <?php foreach($row['columns'] as $k => $column) { ?>
        <?php if (!$column['key']) continue; ?>
        <?php $column_id = $column['key']; ?>
        <?php $column_settings = unserialize($column['value']); ?>
        <div class="large-<?php echo $column_settings['grid_large']; ?> medium-<?php echo $column_settings['grid_medium']; ?> small-<?php echo $column_settings['grid_small']; ?>" id="<?php echo $position; ?>_<?php echo $column_id; ?>" nt-editable>
            <ul class="widgets">
                <?php foreach($column['widgets'] as $l => $widget) { ?> {%<?php echo $widget['name']; ?>%} <?php } ?>
            </ul>
        </div>
    <?php } ?>
    </div>
<?php } ?>
</div>
<!--/featuredContentContainer -->
