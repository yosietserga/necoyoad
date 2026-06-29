<?php echo $header; ?>
<?php $tpl = is_dir(DIR_TEMPLATE. $this->config->get('config_template') ."/shared") ? $this->config->get('config_template') : "choroni"; ?>

<!--contentContainer -->
<div id="contentContainer" class="tpl-account-review-read" nt-editable>

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

            <!--center-column -->
            <?php if ($column_left && $column_right) { ?>
            <div class="large-6 medium-6 small-12">
            <?php } else { ?>
            <div class="large-9 medium-9 small-12">
            <?php } ?>

                <div id="columnCenter" nt-editable>

                    <div class="review">
                        <span class="review-author">
                            <?php echo $review['author']; ?>
                            <img src="<?php echo HTTP_IMAGE; ?>stars_<?php echo $review['rating'] . '.png'; ?>" alt="<?php echo $review['stars']; ?>" />
                            <small><?php echo date('d-m-Y h:i A',strtotime($review['date_added'])); ?></small>
                        </span>

                        <div class="review-content"><?php echo $review['text']; ?></div>
                        <?php if ($review['pid']) { ?>
                        <a href="<?php echo $Url::createUrl("store/product",array('product_id'=>$review['product_id'])); ?>">
                        <img src="<?php echo $review['thumb']; ?>" alt="<?php echo $review['name'];?>" />
                        <?php echo $review['name']; ?>
                        </a>
                        <?php } ?>
                    </div>

                    <?php if ($replies) { ?>
                    <div class="replies">
                        <h2><?php echo $l("text_replies"); ?></h2>
                        <ul class="replies">
                            <?php foreach ($replies as $reply) { ?>
                            <li id="reply_<?php echo $review['review_id']; ?>_<?php echo $reply['review_id']; ?>" class="reply_<?php echo $review['review_id']; ?> row">
                                <div class="large-3 medium-3 small-12 columns">
                                    <b><?php echo $reply['author']; ?></b>
                                    <small><?php echo date('d-m-Y h:i A',strtotime($reply['date_added'])); ?></small>
                                </div>
                                <div class="large-9 medium-9 small-12 columns">
                                    <?php echo $reply['text']; ?>
                                </div>
                            </li>
                            <?php } ?>
                        </ul>
                    </div>
                    <?php } ?>

                    <div class="review-actions">
                        <a class="review-delete" onclick="deleteReview(this,'<?php echo $review['product_id']; ?>','<?php echo $review['review_id']; ?>')">Eliminar</a>
                        <a class="dislikes"><?php echo (int)$review['dislikes']; ?></a>
                        <a class="review-dislike" title="<?php echo $l('text_dislike'); ?>"></a>
                        <a class="likes"><?php echo (int)$review['likes']; ?></a>
                        <a class="review-like" title="<?php echo $l('text_like'); ?>"></a>
                    </div>





                    <?php $position = 'main'; ?>
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
            </div>
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

<script type="text/javascript">
    window.deferjQuery(function () {
        var deleteReview = function deleteReview(e,p,r) {
            if (confirm('<?php echo $l('text_confirm_delete'); ?>')) {
                $('#review_'+ r).slideUp(function(){
                    $(this).remove();
                });
                $.post(createUrl("store/review/delete") +'&review_id='+ r,
                    {
                        'review_id':r
                    },function(){
                        window.location = createUrl("account/review");
                    });
            }
        };

        window.deleteReview = deleteReview;
        $('#reviewForm').ntForm({
            ajax:true,
            url:createUrl('account/review/reply'),
            success:function(data) {
                if (data.success) {
                    window.location.href = createUrl('account/review/read', {review_id: '<?php echo $review_id; ?>'});
                }
                if (data.error) {
                    $('#reviewForm').append(data.msg);
                }
            }
        });

        $('#reviewForm textarea').ntInput();

        var cache = {};
        $( '#addresses' ).on( 'keydown', function( event ) {
            if ( event.keyCode === $.ui.keyCode.TAB && $( this ).data( 'autocomplete' ).menu.active ) {
                event.preventDefault();
            }
        })
            .autocomplete({
                source: function( request, response ) {
                    var term = request.term;
                    if ( term in cache ) {
                        response( cache[ term ] );
                        return;
                    }
                    $.getJSON( createUrl('account/review/getcustomers'), {
                        term: extractLast( request.term )
                    },
                        function( data, status, xhr ) {
                            cache[ term ] = data;
                            response( data );
                        });
                },
                search: function() {
                    var term = extractLast( this.value );
                    if ( term.length < 2 ) {
                        return false;
                    }
                },
                focus: function() {
                    return false;
                },
                select: function( event, ui ) {

                    var ids = split( $('#to').val() );
                    ids.pop();
                    ids.push( ui.item.id );
                    ids.push( '' );
                    $('#to').val(ids.join( '; ' ));

                    var terms = split( this.value );
                    terms.pop();
                    terms.push( ui.item.value );
                    terms.push( '' );
                    this.value = terms.join( '; ' );

                    return false;
                }
            });

        function split( val ) {
            return val.split( /;\s*/ );
        }
        function extractLast( term ) {
            return split( term ).pop();
        }
    });
</script>

<?php echo $footer; ?>