<?php foreach($rows[$position] as $j => $row) { ?>

    <?php if (!$row['key']) continue; ?>
    <?php $row_id = $row['key']; ?>
    <?php $row_settings = unserialize($row['value']); ?>

    <div data-row="<?php echo $row_id; ?>" data-position="<?php echo $position; ?>"<?php if (isset($row_settings['sticky']) && $row_settings['sticky']) {  ?> data-sticky="1"<?php } ?> class="row<?php if (isset($row_settings['layout_width']) && $row_settings['layout_width']==='fixed') {  ?> container<?php } if (!empty($row_settings['classnames'])) echo ' '. $row_settings['classnames']; ?>" id="<?php echo $position; ?>_<?php echo $row_id; ?>" nt-editable>

        <?php foreach($row['columns'] as $k => $column) { ?>
            <?php if (!$column['key']) continue; ?>

            <?php $column_id = $column['key']; ?>
            <?php $column_settings = unserialize($column['value']); ?>

            <div data-column="<?php echo $column_id; ?>" data-position="<?php echo $position; ?>"<?php if (isset($column_settings['sticky']) && $column_settings['sticky']) {  ?> data-sticky="1"<?php } ?> class="large-<?php echo $column_settings['grid_large']; ?> medium-<?php echo $column_settings['grid_medium']; ?> small-<?php echo $column_settings['grid_small']; if (!empty($row_settings['classnames'])) echo ' '. $column_settings['classnames']; ?>" id="<?php echo $position; ?>_<?php echo $column_id; ?>" nt-editable>
                <ul class="widgets">
                    <?php foreach($column['widgets'] as $l => $widget) { ?> {%<?php echo $widget['name']; ?>%} <?php } ?>
                </ul>
            </div>

        <?php } ?>

    </div>
<?php } ?>