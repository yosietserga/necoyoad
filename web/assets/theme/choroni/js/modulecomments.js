function initCommentForm(widgetName) {
    $('#'+ widgetName +'_text')
    .on('focus',function(e){
        $(this).animate({
            height:'100px'
        });
    })
    .on('blur',function(e){
        if ($(this).val().length == 0) {
            $(this).animate({
                height:'40px'
            });
        }
    });
    
    $('#'+ widgetName +' .star_review')
        .on('mouseover',function() {
            var idThis = $(this).attr('data-rating');
            $('#'+ widgetName +' .star_review').each (function() {
                var idStar = $(this).attr('data-rating');
                if (idStar <= idThis) {
                    $(this).addClass('star_active');
                }
            });
        })
        .on('mouseout', function() {
            $('#'+ widgetName +' .star_review').each (function() {
                $(this).removeClass('star_active');
            });
        })
        .on('click', function() {
            var idThis = $(this).attr('data-rating');
            $('#'+ widgetName +' input[name=rating]').val(idThis);
            $('#'+ widgetName +' .star_review').each (function() {
                var idStar = $(this).attr('data-rating');
                if (idStar <= idThis) {
                    $(this).addClass('star_clicked');
                } else {
                    $(this).removeClass('star_clicked');
                    $(this).removeClass('star_active');
                }
            });
        });
}

function review(widgetName) {
    if (window.nt.review.isLogged) {
        if ($('#'+ widgetName +'_text').val().length > 0) {
            $('.success, .warning').remove();
            $('#'+ widgetName +'_review_button').hide();
            $('#'+ widgetName +'_msgReview').html('<div class="message warning">'+ window.nt.review.txtWait +'</div>');

            var params = {
                object_id:window.nt.review.oid,
                ot:window.nt.review.ot,
            };

            var postData = {
                object_id:window.nt.review.oid,
                ot:window.nt.review.ot,
                text: encodeURIComponent($('textarea[name="text"]').val()),
                rating: encodeURIComponent($('input[name="rating"]').val()) || ''
            };

            $.post(createUrl('store/review/write', params), postData)
            .done(function(response) {
                    data = $.parseJSON(response);

                    $('#'+ widgetName +'_review_button').show();
                    $('#'+ widgetName +'_msgReview').html('');

                    if (typeof data.error != 'undefined') {
                        $('#'+ widgetName +'_msgReview').html('<div class="message warning">' + data.error + '</div>');
                    }

                    if (typeof data.success != 'undefined') {
                        $('#'+ widgetName +'_msgReview').html('<div class="message success">' + window.nt.review.txtSuccess + '</div>');

                        $('#'+ widgetName +'_text').val('').animate({
                            height:'40px'
                        });

                        $('#'+ widgetName +'_content .detail a').removeClass('star_clicked').addClass('star_review');

                        $('#'+ widgetName +'_content .star_review').each(function() {
                            $(this).css({'background-position':'right top'});
                        });

                        if (typeof data.show != 'undefined') {
                            html = '<li id="'+ widgetName +'_review_'+ data.review_id +'" class="review_item row">';
                            html += '<div class="column">';
                            html += '<strong>'+ data.author +'</strong>';
                            html += '<time>'+ data.date_added +'</time>';
                            html += '</div>';
                            html += '<div class="column">';
                            html += '<img src="'+ window.nt.http_image +'stars_'+ data.rating +'.png" />';
                            html += '</div>';
                            html += '<div class="column">'+ data.text +'</div>';

                            html += '<div class="review-buttons">';
                            html += '<a class="review-reply" onclick="addReply(this,\''+ data.object_id +'\',\''+ data.review_id +'\')">Replicar</a>';
                            html += '<a class="review-delete" onclick="deleteComment(this,\''+ data.object_id +'\',\''+ data.review_id +'\')" >Eliminar</a>';
                            html += '<a class="review-dislike" onclick="dislikeComment(this,\''+ data.object_id +'\',\''+ data.review_id +'\')"></a>';
                            html += '<a class="review-like" onclick="likeComment(this,\''+ data.object_id +'\',\''+ data.review_id +'\')"></a>';
                            html += '</div>';
                            html += '<ul class="replies"></ul>';
                            html += '</li>';
                            $('.review_item:first-child').before(html);

                            cloned = $('#'+ widgetName +'_review_'+ data.review_id).clone();

                            $(cloned).css({
                                'background':'#AEDF4F',
                                'position':'absolute',
                                'top':'0px',
                                'left':'0px',
                                'marginTop':'210px'
                            });

                            $('#'+ widgetName +'_review_'+ data.review_id).closest('ul').prepend(cloned);

                            $(cloned).animate({
                                'background':'#f0f0f0',
                                'width':'102%',
                                'opacity':0
                            },1200,function(){
                                $(this).remove();
                            });
                        }
                        if ($('#'+ widgetName +'_noComments'.legnth > 0)) {
                            $('#'+ widgetName +'_noComments').remove();
                        }
                    }
                });
        } else {
            $('#'+ widgetName +'_msgReview').html('<div class="message warning">'+ window.nt.review.txtErrorText +'</div>');
        }
    } else {
        $('#'+ widgetName +'_msgReview').html('<div class="message warning">'+ window.nt.review.txtErrorLogin +'</div>');
    }
}

function addReply(e, p, r) {
    $('textarea[name=replyText]').remove();
    $('input[name=replySubmit]').remove();

    var textarea = $(document.createElement('textarea')).attr({
        'name':'replyText',
        'id':window.nt.review.widgetName +'_replyText',
        'placeholder':'Agrega tu comentario'
    }).css({
        'height':'40px'
    }).focus(function(e) {
        $(textarea).removeClass('neco-input-error');
        $(this).animate({
            'width':'90%',
            'height':'100px',
        });
    }).blur(function(e) {
        if ($(this).val().length == 0) {
            $(this).slideUp(function(){
                $('textarea[name=replyText]').remove();
                $('input[name=replySubmit]').remove();
            });
        }
    });

    var button = $(document.createElement('input')).attr({
        'name':'replySubmit',
        'id':window.nt.review.widgetName +'_replySubmit',
        'type':'button'
    }).val(window.nt.review.txtButtonContinue)
.on('click', function(e) {
        $(this).hide();
        if ($('#'+ window.nt.review.widgetName +'_replyText').val().length > 0) {
            $('#'+ window.nt.review.widgetName +'_replyText').before('<div class="message success">'+ window.nt.review.txtWait +'</div>');


            var params = {
                object_id:p,
                ot:window.nt.review.ot,
                review_id:r,
            };

            var postData = {
                object_id:window.nt.review.oid,
                ot:window.nt.review.ot,
                review_id:r,
                text: encodeURIComponent($('#'+ window.nt.review.widgetName +'_replyText').val())
            };
            
            $.post(createUrl("store/review/reply", params), postData)
            .done(function(response){
                    $('.message').remove();
                    data = $.parseJSON(response);
                    if (typeof data.success != 'undefined') {
                        if (typeof data.show != 'undefined') {
                            $('#'+ window.nt.review.widgetName +'_replyText').before('<div class="message success">Ha replicado con &eacute;xito</div>');
                            html = '<li>';
                            html += '<div class="grid_2">';
                            html += '<b>' + data.author + '</b><br />';
                            html += '<small>' + data.date_added + '</small>';
                            html += '</div>';
                            html += '<div class="grid_10">' + data.text + '</div>';
                            html += '<div class="clear"></div>';
                            html += '</li>';
                            $('#'+ window.nt.review.widgetName +'_review_' + r + ' .replies').append(html);
                        } else {
                            $('#'+ window.nt.review.widgetName +'_replyText').before('<div class="message success">Ha replicado con &eacute;xito, estamos verificando el contenido para publicarlo</div>');
                        }
                    } else {
                        $('#'+ window.nt.review.widgetName +'_replyText').before('<div class="message warning">No se pudo agregar la r&eacute;plica. Por favor intente m&aacute;s tarde.</div>');
                    }
                    $('textarea[name=replyText]').remove();
                    $('input[name=replySubmit]').remove();
                });
        } else {
            $(this).show();
            $('#'+ window.nt.review.widgetName +'_replyText').addClass('neco-input-error');
        }
    });
    if ($('textarea[name=replyText]').length == 0) {
        $(e).before(textarea);
        $(e).before(button);
    }
}

function deleteReview(e, p, r) {
    if (confirm(window.nt.review.txtConfirmDelete)) {
        $('#'+ window.nt.review.widgetName +'_review_' + r).slideUp(function(){
            $(this).remove();
        });

        var params = {
            'object_id':p,
            'ot':window.nt.review.ot,
            'review_id':r
        };

        $.post(createUrl("store/review/deleteReview", params), params);
    }
}

function likeReview(e, p, r) {
    var params = {
        'object_id':p,
        'ot':window.nt.review.ot,
        'review_id':r
    };

    $.post(createUrl("store/review/likeReview", params), params)
    .done(function(response) {
        var data = $.parseJSON(response);
        if (typeof data.success != 'undefined') {
            likes = $('#'+ window.nt.review.widgetName +'_review_' + r).find('.likes').html(data.likes);
            dislikes = $('#'+ window.nt.review.widgetName +'_review_' + r).find('.dislikes').html(data.dislikes);
        }
    });
}

function dislikeReview(e, p, r) {
    var params = {
        'object_id':p,
        'ot':window.nt.review.ot,
        'review_id':r
    };

    $.post(createUrl("store/review/dislikeReview", params), params)
    .done(function(response) {
        var data = $.parseJSON(response);
        if (typeof data.success != 'undefined') {
            likes = $('#'+ window.nt.review.widgetName +'_review_' + r).find('.likes').html(data.likes);
            dislikes = $('#'+ window.nt.review.widgetName +'_review_' + r).find('.dislikes').html(data.dislikes);
        }
    });
}