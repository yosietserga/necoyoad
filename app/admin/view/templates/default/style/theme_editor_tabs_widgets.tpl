<div class="row">
<input type="text" id="qWidgets" placeholder="<?php echo $l('text_filter'); ?>" />
</div>
<ul id="widgetsPanel" class="widget widgetsPanel">
    <?php foreach ($modules as $module) { ?>
    <li class="neco-widget" data-title="<?php echo $module['name']; ?>" data-widget="<?php echo $module['widget']; ?>">
        <b><?php echo $module['name']; ?></b><br />
        <?php echo $module['description']; ?>
    </li>
    <?php } ?>
</ul>