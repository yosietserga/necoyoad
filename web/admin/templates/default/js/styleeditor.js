$(function(){
    var form_clean = $('#form').serialize();

    window.onbeforeunload = function (e) {
        var form_dirty = $('#form').serialize();
        if(form_clean != form_dirty) {
            return 'There is unsaved form data.';
        }
    };

    $('.sidebar .tab').on('click',function(){
        $(this).closest('.sidebar').addClass('show').removeClass('hide').animate({'right':'0px'});
    });
    $('.sidebar').mouseenter(function(){
        clearTimeout($(this).data('timeoutId'));
    }).mouseleave(function(){
        var e = this;
        var timeoutId = setTimeout(function(){
            if ($(e).hasClass('show')) {
                $(e).removeClass('show').addClass('hide').animate({'right':'-400px'});
            }
        }, 600);
        $(this).data('timeoutId', timeoutId);
    });
});