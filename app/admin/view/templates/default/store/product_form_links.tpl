<div>

    <div class="clear"></div>
            
    <div class="row">
        <label><?php echo $l('entry_manufacturer'); ?></label>
        <select class="necoManufacturer" title="<?php echo $l('help_manufacturer'); ?>" name="manufacturer_id">
            <option value="0" selected="selected"><?php echo $l('text_none'); ?></option>
            <?php foreach ($manufacturers as $manufacturer) { ?>
            <option value="<?php echo $manufacturer['manufacturer_id']; ?>"<?php if ($manufacturer['manufacturer_id'] == $manufacturer_id) { ?> selected="selected"<?php } ?>><?php echo $manufacturer['name']; ?></option>
            <?php } ?>
        </select>
    </div>
        
    <?php 
        require_once(dirname(__FILE__)."/../shared/form/data_view.tpl");

        $object_category = $category;
        require_once(dirname(__FILE__)."/../shared/form/data_categories.tpl");
        
        $object_id = $product_id;
        require_once(dirname(__FILE__)."/../shared/form/data_customergroups.tpl");
        require_once(dirname(__FILE__)."/../shared/form/data_stores.tpl");
    ?>
         
    <div class="clear"></div>
    
    <div class="row">
        <label><?php echo $l('entry_download'); ?></label>
        <div class="clear"></div>
        <div class="scrollbox necoDownload">
        <?php $class = 'odd'; ?>
        <?php foreach ($downloads as $download) { ?>
            <?php $class = ($class == 'even' ? 'odd' : 'even'); ?>
            <div class="<?php echo $class; ?>">
            
                <input title="<?php echo $l('help_download'); ?>" type="checkbox" name="downloads[]" value="<?php echo $download['download_id']; ?>"<?php if (in_array($download['download_id'], $product_download)) { ?> checked="checked"<?php } ?> showquick="off" /><?php echo $download['filename']; ?>
            
            </div>
        <?php } ?>
        </div>
    </div>
                    
</div>