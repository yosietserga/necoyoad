<?php echo $header; ?>
<?php echo $navigation; ?>
<div class="container">
    
    <?php if (isset($breadcrumbs) && is_array($breadcrumbs)) { ?>
    <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
    </ul>
    <?php } ?>
    
    <?php if (isset($success) && $success) { ?><div class="grid_12"><div class="message success"><?php echo $success; ?></div></div><?php } ?>
    <?php if ((isset($msg) && $msg) || (isset($error_warning) && $error_warning)) { ?><div class="grid_12"><div class="message warning"><?php echo $msg ?? $error_warning; ?></div></div><?php } ?>
    <?php if (isset($error) && $error) { ?><div class="grid_12"><div class="message error"><?php echo $error; ?></div></div><?php } ?>
    <div class="grid_12" id="msg"></div>
    
    <div class="box">
        <h1><?php echo $l('heading_title'); ?></h1>
        <div class="buttons">
            <a onclick="saveAndExit();$('#form').submit();" class="button"><?php echo $l('button_save_and_exit'); ?></a>
            <a onclick="saveAndKeep();$('#form').submit();" class="button"><?php echo $l('button_save_and_keep'); ?></a>
            <a onclick="saveAndNew();$('#form').submit();" class="button"><?php echo $l('button_save_and_new'); ?></a>
            <a onclick="location = '<?php echo $cancel; ?>';" class="button"><?php echo $l('button_cancel'); ?></a>
        </div>
        
        <div class="clear"></div>
                                
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">

            <div class="row">
                <label><?php echo $l('entry_author'); ?></label>
                <input id="author" name="author" value="<?php echo $author; ?>" required="true" style="width:40%" />
            </div>
                        
            <div class="clear"></div>
                            
            <div class="row">
                <label><?php echo $l('entry_product'); ?></label>
                <a href="<?php echo $Url::createAdminUrl($object_url ."&amp;{$object_type}_id=$object_id"); ?>"><b id="object_name"><?php echo $object_name; ?></b></a>
                <input type="hidden" name="object_id" id="object_id" value="<?php echo (int)$object_id; ?>" />
            </div>
            
            <div class="clear"></div>
            
            <?php if ((int)$parent_id > 0) { ?>
            <div class="row">
                <label><?php echo $l('entry_review_id'); ?></label>
                <a href="<?php echo $Url::createAdminUrl("store/review/update",array('review_id'=>$parent_id)); ?>"><b><?php echo $parent_id; ?></b></a>
            </div>
            <div class="clear"></div>
            <?php } ?>
            
            <div class="row">
                <label><?php echo $l('entry_text'); ?></label>
                <textarea id="text" name="text" style="width:40%"><?php echo $text; ?></textarea>
            </div>
                   
            <div class="clear"></div><br />
            
            <?php if ($parent_id == 0) { ?>
            <div class="row">
                <label><?php echo $l('entry_rating'); ?></label>
                <a class="star_review<?php if ($rating >= 1) echo ' star_clicked'; ?>" id="1"<?php if ($rating >= 1) echo ' style="background-position: left top;"'; ?>></a>
                <a class="star_review<?php if ($rating >= 2) echo ' star_clicked'; ?>" id="2"<?php if ($rating >= 2) echo ' style="background-position: left top;"'; ?>></a>
                <a class="star_review<?php if ($rating >= 3) echo ' star_clicked'; ?>" id="3"<?php if ($rating >= 3) echo ' style="background-position: left top;"'; ?>></a>
                <a class="star_review<?php if ($rating >= 4) echo ' star_clicked'; ?>" id="4"<?php if ($rating >= 4) echo ' style="background-position: left top;"'; ?>></a>
                <a class="star_review<?php if ($rating >= 5) echo ' star_clicked'; ?>" id="5"<?php if ($rating >= 5) echo ' style="background-position: left top;"'; ?>></a>
                <input type="hidden" name="rating" id="rating" value="<?php echo (int)$rating; ?>" />
            </div>
            <div class="clear"></div><br />
            <?php } ?>
            
            <div class="row">
                <label><?php echo $l('entry_status'); ?></label>
                <select name="status">
                      <option value="1"<?php if ($status) { ?> selected="selected"<?php } ?>><?php echo $l('text_enabled'); ?></option>
                      <option value="0"<?php if (!$status) { ?> selected="selected"<?php } ?>><?php echo $l('text_disabled'); ?></option>
                </select>
            </div>
                   
            <div class="clear"></div>
            
            <?php if ($parent_id == 0) { ?>
            <div class="row">
                <label><?php echo $l('entry_reply'); ?></label>
                <textarea name="reply" id="reply" style="width:40%"></textarea>
                <div class="clear"></div>
                <label>&nbsp;</label>
                <a class="button" onclick="addReply(this,'<?php echo $object_id; ?>','<?php echo $object_type; ?>','<?php echo $review_id; ?>')"><?php echo $l('button_submit'); ?></a>
            </div>
            
            <div class="clear"></div><br />
            
            <div id="replies">
                <ul>
            <?php if (count($replies)) { ?>
                    <?php foreach ($replies as $reply) { ?>
                    <li id="reply_<?php echo $reply['review_id']; ?>">
                        <div class="grid_3">
                            <b><?php echo $reply['author']; ?></b><br />
                            <small><?php echo date('d-m-Y h:i A',strtotime($reply['date_added'])); ?></small>
                        </div>
                        <div class="grid_9">
                            <?php echo $reply['text']; ?>
                        </div>
                        <div class="clear"></div>
                    </li>
                    <?php } ?>
            <?php } ?>
                </ul>
            </div>
            
            <div class="clear"></div>
            <?php } ?>
        </form>
    </div>
</div>
<script>
$(function(){
    $('#reply').on('focus',function(e){
        $(this).animate({
            height:'100px'
        });
    }).on('blur',function(e){
        if ($(this).val().length == 0) {
            $(this).animate({
                height:'40px'
            });
        }
    });
    $('.star_review').hover(
        function() {
            var idThis = $(this).attr('id');
            $('.star_review').each (function() {
                var idStar = $(this).attr('id');
                if (idStar <= idThis) {
                    $(this).css({'background-position':'left top'});
                }
            });
        },
        function() {
            $('.star_review').each (function() {
                if (!$(this).hasClass('star_clicked'))
                    $(this).css({'background-position':'right top'});
            });
        }
    );
    $('.star_review').on('click',function(e) {
        var idThis = $(this).attr('id');
        $('input[name=rating]').val(idThis);
        $('.star_review').each(function() {
            var idStar = $(this).attr('id');
            if (idStar <= idThis) {
                $(this).addClass('star_clicked');
                $(this).css({'background-position':'left top'});
            } else {
                $(this).removeClass('star_clicked');
                $(this).css({'background-position':'right top'});
            }
        });
    });
});
function addReply(e,oid, ot, r) {
    $(e).hide();
    $('.message').fadeOut();
    if ($('#reply').val().length > 0) {
        $.post('<?php echo $Url::createAdminUrl("store/review/reply"); ?>&object_type='+ ot +'&object_id='+ oid +'&review_id='+ r,
        {
            'object_id':oid,
            'object_type':ot,
            'review_id':r,
            'text':encodeURIComponent($('#reply').val())
        },
        function(response){
            data = $.parseJSON(response);
            if (typeof data.success != 'undefined' && data.success != 0) {
                html = '<li>';
                html += '<div class="grid_3">';
                html += '<b>'+ data.author +'</b><br />';
                html += '<small>'+ data.date_added +'</small>';
                html += '</div>';
                html += '<div class="grid_9">'+ data.text +'</div>';
                html += '<div class="clear"></div>';
                html += '</li>';
                $('#replies ul').prepend(html);
            } else {
                $('#reply').before('<div class="message warning">No se pudo agregar la r&eacute;plica. Por favor intente m&aacute;s tarde.</div>');
            }
            $('textarea[name=reply]').val('');
            $(e).show();
        });
    } else {
        $(e).show();
        $('#reply').addClass('neco-input-error');
    }
}
</script>
<?php echo $footer; ?>