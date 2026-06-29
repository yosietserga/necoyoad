<input type="text" id="qWidgets" placeholder="<?php echo $l('text_filter'); ?>" />
<ul id="widgetsPanel" class="widget widgetsPanel">
    <?php foreach ($modules as $module) { ?>
    <li class="neco-widget" data-title="<?php echo $module['name']; ?>" data-widget="<?php echo $module['widget']; ?>">
        <b><?php echo $module['name']; ?></b><br />
        <?php echo $module['description']; ?>
    </li>
    <?php } ?>
</ul>