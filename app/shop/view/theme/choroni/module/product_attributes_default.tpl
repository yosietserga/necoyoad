<?php if ($attributes) { ?>
<div id="_attributes">
    <div itemprop="attributes" id="<?php echo $widgetName; ?>_productAttributes">
        <?php foreach ($attributes as $key => $attr) { ?>
        <h3><?php echo $attr['name']; ?></h3>

        <?php foreach ($attr['attributes'] as $k => $attribute) { ?>
        <div class="row">
            <div class="small-6 medium-6 large-6 columns"><?php echo $attribute['label']; ?></div>
            <div class="small-6 medium-6 large-6 columns"><?php echo $attribute['value']; ?></div>
        </div>
        <?php } ?>

        <?php } ?>
    </div>
</div>
<?php } ?>