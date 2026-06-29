function animateForm(el) {
    $(el).hide();

    $(document.createElement('div')).attr({
        id:'temp',
        class:'loader'
    })
        .append('<img src="'+ window.nt.http_image +'loader.gif" alt="Loading..." />')
        .appendTo($(el).parent());
}

function processForm(el, wname) {
    var el = el;
    var f = $(el);
    $.post(
        createUrl('module/contact_form/processform', {
            async:1,
            w:wname,
        }),
        {
            name:$(el +' input[name=name]').val(),
            email:$(el +' input[name=email]').val(),
            enquiry:$(el +' textarea[name=enquiry]').val()
        })
        .done(function(resp){
            $('#temp').remove();
            f.show();

            var data = $.parseJSON(resp);
            $(el).find('.msg').remove();

            if (data.error) {
                $(el).prepend('<div class="msg error">'+ data.msg +'</div>');
            } else {
                $(el).prepend('<div class="msg success">'+ data.msg +'</div>');
                $(el +' input[name=name]').val('');
                $(el +' input[name=email]').val('');
                $(el +' textarea[name=enquiry]').val('');
            }
        }
    );
}