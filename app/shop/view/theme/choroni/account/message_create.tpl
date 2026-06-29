<?php echo $header; ?>
<?php $tpl = is_dir(DIR_TEMPLATE. $this->config->get('config_template') ."/shared") ? $this->config->get('config_template') : "choroni"; ?>

<!--contentContainer -->
<div id="contentContainer" class="tpl-account-message-create" nt-editable>

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


                            <form class="simple-form break" action="<?php echo $action; ?>" method="post" id="messageForm">
                                <div class="form-entry">
                                    <label for="to"><?php echo $entry_to; ?></label>
                                    <input type="text" id="addresses" name="addresses" value="<?php echo $to??""; ?>" required="required" title="Ingresa los nombres de los remitentes" style="width:400px" />
                                    <input type="hidden" id="to" name="to" value="<?php echo $addresses; ?>" />
                                    <?php if ($error_to) { ?><span class="error" id="error_to"><?php echo $error_to; ?></span><?php } ?>
                                </div>

                                <div class="form-entry">
                                    <label for="subject"><?php echo $entry_subject; ?></label>
                                    <input type="text" id="subject" name="subject" value="<?php echo $subject??""; ?>" required="required" title="Ingresa el asunto del mensaje" style="width:400px" />
                                    <?php if ($error_subject) { ?><span class="error" id="error_subject"><?php echo $error_subject; ?></span><?php } ?>
                                </div>

                                <div class="form-entry">
                                    <label for="message"><?php echo $entry_message; ?></label>
                                    <textarea id="message" name="message" required="required"><?php echo $message; ?></textarea>
                                    <?php if ($error_message) { ?><span class="error" id="error_message"><?php echo $error_message; ?></span><?php } ?>
                                </div>
                            </form>




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
                $('#messageForm').ntForm({
                ajax:true,
                url:'{$this->data['action']}',
                success:function(data) {
                    if (data.success) {
                        window.location.href = createUrl('account/message');
                    }
                    if (data.error) {
                        $('#messageForm').append(data.msg);
                    }
                }
            });

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
                        $.getJSON( createUrl('account/message/getcustomers'), {
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