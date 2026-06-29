<?php echo $header; ?>
<?php $tpl = is_dir(DIR_TEMPLATE. $this->config->get('config_template') ."/shared") ? $this->config->get('config_template') : "choroni"; ?>

<!--contentContainer -->
<div id="contentContainer" class="tpl-account-account" nt-editable>

    <?php include(DIR_TEMPLATE. $tpl ."/shared/widgets-featured.tpl");?>

    <!--mainContentContainer -->
    <div id="mainContentContainer" nt-editable>
        <div class="row">

            <!-- left-column -->
            <div class="large-3 medium-3 small-12">
                <div id="columnLeft" nt-editable>
                    <?php echo $account_column_left; ?>
                    <?php if ($column_left) { echo $column_left; } ?>
                </div>
            </div>

            <!--/left-column -->
            <form id="rooms_create_form" action="<?php echo $Url::createUrl('rooms/account/create'); ?>" method="post" enctype="multipart/form-data">
                <div class="clear"></div>
<div class="row">
    <label><?php echo $l('Image'); ?></label>
    <a class="filemanager" data-fancybox-type="iframe" href="<?php echo $Url::createAdminUrl("common/filemanager"); ?>&amp;field=image&amp;preview=preview">
    <img src="<?php echo isset($preview) ? $preview : ''; ?>" id="preview" class="image necoImage" width="100" />
    </a>
    <input type="hidden" name="image" value="<?php echo isset($image) ? $image : ''; ?>" id="image" onchange="$('#preview').attr('src',  window.nt.http_image + this.value);" />
    <br />
    <a class="filemanager" data-fancybox-type="iframe" href="<?php echo $Url::createAdminUrl("common/filemanager"); ?>&amp;field=image&amp;preview=preview" style="margin-left: 220px;color:#FFA500;font-size:10px">[ Cambiar ]</a>
    <a onclick="image_delete('image', 'preview');" style="color:#FFA500;font-size:10px">[ Quitar ]</a>
</div>
                <div class="row">

                    <table id="images" class="list">
                        <tbody>
                        <?php foreach ($images as $image_row => $product_image) { ?>
                            <tr id="image_row<?php echo $image_row; ?>">
                                <td>
                                    <input type="hidden" name="images[<?php echo $image_row; ?>]" value="<?php echo $product_image['file']; ?>" id="image<?php echo $image_row; ?>">
                                    <a class="filemanager" data-fancybox-type="iframe" href="<?php echo $Url::createAdminUrl("common/filemanager"); ?>&amp;field=image<?php echo $image_row; ?>&amp;preview=preview<?php echo $image_row; ?>">
                                       
                                        <img src="<?php echo $product_image['preview']; ?>" id="preview<?php echo $image_row; ?>" class="image" width="100" />
                                    </a>
                                </td>
                                <td>
                                    <a onclick="$('#image_row<?php echo $image_row; ?>').remove();" class="button"><?php echo $l('button_remove'); ?></a>
                                </td>
                            </tr>
                            
                            <?php $image_row++; ?>
                        <?php } ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td></td>
                                <td><a onclick="__addImage();" class="button"><?php echo $l('button_add_image'); ?></a></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <script type="text/javascript">
                var image_row = <?php echo isset($image_row) ? (int)$image_row++ : 1; ?>;

                function __addImage() {
                    html  = '<tr id="image_row' + image_row + '">'
                    +'<td class="left">'
                        +'<input type="hidden" name="images[' + image_row + ']" value="" id="image' + image_row + '">'
                        +'<a class="filemanager" data-fancybox-type="iframe" href="<?php echo $Url::createAdminUrl("common/filemanager"); ?>&amp;field=image' + image_row + '&amp;preview=preview' + image_row + '">'
                        +'<img src="<?php echo $no_image; ?>" id="preview' + image_row + '" class="image" width="100" />'
                        +'</a>'
                        +'</td>'
                        +'<td class="left">'
                        +'<a onclick="$(\'#image_row' + image_row  + '\').remove();" class="button"><span><?php echo $l('button_remove'); ?></span></a>'
                        +'</td>'
                        +'</tr>';
                    
                    $('#images tbody').append(html);
                    
                    image_row++;
                }
                </script>

                <div class="row">
                    <label><?php echo $l('Title'); ?></label>
                    <input name="descriptions[<?php echo $language_id; ?>][title]" value="<?php echo isset($descriptions[$language_id]['title']) ? $descriptions[$language_id]['title'] : ''; ?>" required="true" />
                </div>

                <div class="clear"></div>

                <div class="row">
                    <label><?php echo $l('Description'); ?></label>
                    <div class="clear"></div>
                    <textarea name="descriptions[<?php echo $language_id??1; ?>][description]"><?php echo isset($descriptions[$language_id??1]['description']) ? $descriptions[$language_id??1]['description'] : ''; ?></textarea>
                </div>
                 
    <div class="clear"></div>
                    
    <div class="row">
        <label><?php echo $l('entry_price'); ?></label>
        <input class="necoPrice" type="text" title="<?php echo $l('help_price'); ?>" name="price" value="<?php echo str_replace('.',',',$price); ?>" />
    </div>
    
                <div class="clear"></div>
                
                <input name="model" value="<?php echo isset($model) && !empty($model) ? $model : md5(nt_rand()); ?>" />
                <input type="submit" value="Send" />
            </form>

            <script>
            $(function(){
                $('#rooms_create_form').on('submit', function(e){
                    e.preventDefault();
                    e.stopImmediatePropagation();

                    animateForm('#rooms_create_form');
                    processForm('#rooms_create_form', 'rooms_create_form');

                    return false;
                });
            });
            </script>

            <!--center-column -->
            <?php include(DIR_TEMPLATE. $tpl ."/shared/widgets-column-center.tpl");?>
            <!--/center-column -->

            <!-- right-column -->
            <?php if ($column_right) { ?>
            <?php include(DIR_TEMPLATE. $tpl ."/shared/widgets-column-right.tpl");?>
            <?php } ?>
            <!--/right-column -->

        </div>
    </div>
    <!--/mainContentContainer -->

    <!--featuredFooterContainer -->
    <?php include(DIR_TEMPLATE. $tpl ."/shared/widgets-featured-footer.tpl");?>
    <!--/featuredFooterContainer -->

</div>
<!--/contentContainer -->

<?php echo $footer; ?>