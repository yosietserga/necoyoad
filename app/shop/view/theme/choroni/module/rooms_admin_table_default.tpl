<?php $tpl = is_dir(DIR_TEMPLATE. $this->config->get('config_template') ."/shared") ? $this->config->get('config_template') : "choroni"; ?> 
<!-- catalog-latest -->


<?php if($rooms) { ?>

<div class="row">
<div class="row">
    <div class="grid_12">
        <a href="<?php echo $Url::createUrl("rooms/account/create"); ?>" class="button"><?php echo $l("New Room"); ?></a>
    </div>
</div>

<div class="row">
    <div class="grid_12">
        <form>[filters and toolbar]</form>
    </div>
</div>


<?php include(DIR_TEMPLATE. $tpl ."/shared/module-heading.tpl");?>
<?php if (isset($pagination) && $pagination) { ?><div class="pagination"><?php echo $pagination; ?></div><?php } ?>
<ul>
    <?php foreach($rooms as $room) { ?>
    <li class="table-item">
        <div>
        <!--<?php echo $room['thumb']; ?>-->
        <figure class="picture">
            <a href="<?php echo $Url::createUrl('store/room', array('product_id'=>$room['product_id'])); ?>" class="thumb" title="<?php echo $room['name']; ?>">
                <img src="<?php echo $room['thumb']; ?>" alt="<?php echo $room['name']; ?>"/>
            </a>
            <a href="javascript:;" class="quick-view" onclick="return quickView('room', '<?php echo $room['product_id']; ?>', this);"><?php echo $l('Quick View'); ?>
            </a>
            <?php include(DIR_TEMPLATE. $this->config->get('config_template') ."/shared/room/sticker.tpl"); ?>
        </figure>
        </div>
        <!--/catalog -picture-->
        <!-- catalog-info -->
        <div class="info nt-hoverdir">

            <a href="<?php echo $Url::createUrl('store/room',array('product_id'=>$room['product_id'])); ?>" title="<?php echo $room['name']; ?>" class="name">
                <?php echo $room['name']; ?>
            </a>

            <p class="model">
                <?php echo $room['model']; ?>
            </p>

            <div class="rating">
                <img src="<?php echo HTTP_IMAGE; ?>stars_<?php echo (int)$room['rating'] . '.png'; ?>" alt="<?php echo $room['stars']; ?>" />
            </div>

            <?php if (isset($room['price']) && $display_price && $Config->get('config_store_mode')=='store') { ?>
            <p class="price"><?php echo $room['price']; ?></p>
            <?php } ?>

            <div class="group-btn">

                <div class="btn btn-detail">
                    <a href="<?php echo $Url::createUrl('store/room',array('product_id'=>$room['product_id'])); ?>"><?php echo $l('button_see_product'); ?></a>
                </div>

            </div>
        </div>
        <!-- /catalog-info -->


    </li>
    <?php } //end foreach ?>

</ul>
</div>

<?php if (isset($pagination) && $pagination) { ?><div class="pagination"><?php echo $pagination; ?></div><?php } ?>
<!-- /catalog -->
<?php } //end if ?>
