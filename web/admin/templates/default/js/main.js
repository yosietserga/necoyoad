/* App.Utils.Connection.PingCheck();
App.Utils.Task.Run();
*/
$(function () {

    var height = $(window).height() * 0.8;
    var width = $(window).width() * 0.8;

    $(".filemanager").fancybox({
        maxWidth	: width,
        maxHeight	: height,
        fitToView	: false,
        width	: '90%',
        height	: '90%',
        autoSize	: false,
        closeClick	: false,
        openEffect	: 'none',
        closeEffect	: 'none'
    });

    addScrollboxBehavior();

    $('#form').submit(function () {
        if ($(this).attr('action').indexOf('delete', 1) != -1) {
            if (!confirm('Est\xe1 seguro que desea eliminar los objetos seleccionados?')) {
                return false;
            }
        }
    });

    $('a').click(function () {
        if ($(this).attr('href') != null && $(this).attr('href').indexOf('uninstall', 1) != -1) {
            if (!confirm('Est\xe1 seguro que desea desinstalar el m\xf3dulo?')) {
                return false;
            }
        }
    });

    $('.dd').click(function () {
        if ($('ul.menu_body').hasClass('open')) {
            $('ul.menu_body').removeClass('open').slideUp(200);
        } else {
            $('ul.menu_body').addClass('open').slideDown(200);
        }
    });

    $(document).on('click', function (e) {
        var $clicked = $(e.target);
        if (!$clicked.parents().hasClass("dd")) {
            $('ul.menu_body').removeClass('open').slideUp(200);
        }
    });

    $('.htabs .htab').on('click', function () {
        $(this).closest('.htabs').find('.htab').each(function () {
            $($(this).attr('tab')).hide();
            $(this).removeClass('selected');
        });
        $(this).addClass('selected');
        $($(this).attr('tab')).show();
    });
    $('.htabs .htab:first-child').trigger('click');

    $('#form').ntForm({
        submitButton: false,
        cancelButton: false,
        lockButton: false
    });

    $('textarea').ntTextArea();

    $('#formFilter').hide();

    var form_clean = $('#form').serialize();

    window.onbeforeunload = function (e) {
        var form_dirty = $('#form').serialize();
        if (form_clean !== form_dirty) {
            return 'There is unsaved form data.';
        }
    };
    setTimeout(function () {
        $('.message').fadeOut('slow');
    }, 10000);

    if (typeof window.imageFolderUrl != 'undefined') $('#gridPreloader').html('<img src="'+ window.imageFolderUrl +'loader.gif" alt="Cargando..." />');

    $('#filters').on('click', function () {
        if ($(this).hasClass('show')) {
            $('#formFilter').slideUp();
            $(this).removeClass('show').addClass('hidded').text('[ Mostrar ]');
        } else {
            $('#formFilter').slideDown();
            $(this).removeClass('hidded').addClass('show').text('[ Ocultar ]');
        }
    });
    
    var currentData = {};
    $("td[contenteditable=true]").blur(function(e){
        if (currentData.value !== $(this).text()) {
            $.post(createAdminUrl($(this).attr('data-route')),
            {
                value: $(this).text(),
                field: $(this).attr('data-field'),
                id: $(this).attr('data-id')
            },
            function(resp){
                data = $.parseJSON(resp);
                if(data.error || !data) {
                    /* show error message and get back the original value */
                } else {
                    /* show success message */
                }
            });
        }
    }).focus(function(e){
        currentData.value = $(this).text();
        currentData.field = $(this).attr('data-field');
        currentData.id = $(this).attr('data-id');
    });
    
});

function addScrollboxBehavior() {


}

function getUrlVars() {
    var vars = {};
    var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function (m, key, value) {
        vars[key] = value;
    });
    return vars;
}

function createUrl(route, params) {
    var url = window.nt.http_catalog + 'index.php?r=' + route;
    if (typeof params !== 'undefined') {
        if (typeof params === 'object') {
            $.each(params, function (k, v) {
                url += '&' + k + '=' + encodeURIComponent(v);
            });
        } else {
            url += '&' + params;
        }
    }
    return url;
}

function createAdminUrl(route, params) {
    var url = window.nt.http_home + 'index.php?r=' + route + '&token=' + getUrlVars()["token"];
    if (typeof params !== 'undefined') {
        if (typeof params === 'object') {
            $.each(params, function (k, v) {
                url += '&' + k + '=' + encodeURIComponent(v);
            });
        } else {
            url += '&' + params;
        }
    }
    return url;
}

function sendFeedback() {
    $.post(createAdminUrl("support/feedback"),
            $('#feedbackForm').serialize(),
            function (response) {
                var data = $.parseJSON(response);
                if (data.success) {
                    alert(data.msg);
                }
                if (data.error) {
                    alert(data.msg);
                }
            });
}

function saveAndExit() {
    window.onbeforeunload = null;
    $('#form').append('<input type="hidden" name="to" value="saveAndExit">').submit();
}

function saveAndKeep() {
    window.onbeforeunload = null;
    $('#form').append('<input type="hidden" name="to" value="saveAndKeep">').submit();
}

function saveAndNew() {
    window.onbeforeunload = null;
    $('#form').append('<input type="hidden" name="to" value="saveAndNew">').submit();
}

function showTab(a) {
    $('.vtabs_page').hide();
    $($(a).attr('data-target')).show();
}

function ntSearch(q, targetId) {
    var valor = q.toLowerCase();
    if (valor.length <= 0) {
        $('#' + targetId + ' li').show();
    } else {
        $('#' + targetId + ' li b').each(function () {
            if ($(this).text().toLowerCase().indexOf(valor) != -1) {
                $(this).closest('li').show();
            } else {
                $(this).closest('li').hide();
            }
        });
    }
}

/** jQuery FullCalendar **/
/*
 var date = new Date();
 var d = date.getDate();
 var m = date.getMonth();
 var y = date.getFullYear();
 
 $('#calendar').fullCalendar({
 header: {
 left: 'prev,next',
 center: 'title',
 right: 'month,basicWeek,basicDay'
 },
 editable: true,
 events: [
 {
 title: 'All day event',
 start: new Date(y, m, 1)
 },
 {
 title: 'Long event',
 start: new Date(y, m, 5),
 end: new Date(y, m, 8)
 },
 {
 id: 999,
 title: 'Repeating event',
 start: new Date(y, m, 2, 16, 0),
 end: new Date(y, m, 3, 18, 0),
 allDay: false
 },
 {
 id: 999,
 title: 'Repeating event',
 start: new Date(y, m, 9, 16, 0),
 end: new Date(y, m, 10, 18, 0),
 allDay: false
 },
 {
 title: 'Actually any color could be applied for background',
 start: new Date(y, m, 30, 10, 30),
 end: new Date(y, m, d + 1, 14, 0),
 allDay: false,
 color: '#B55D5C'
 },
 {
 title: 'Lunch',
 start: new Date(y, m, 14, 12, 0),
 end: new Date(y, m, 15, 14, 0),
 allDay: false
 },
 {
 title: 'Birthday PARTY',
 start: new Date(y, m, 18),
 end: new Date(y, m, 20),
 allDay: false
 },
 {
 title: 'Click for Google',
 start: new Date(y, m, 27),
 end: new Date(y, m, 29),
 url: 'https://google.com/'
 }
 ]
 });
 */
	