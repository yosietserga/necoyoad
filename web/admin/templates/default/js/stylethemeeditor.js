let data = {};

$(function () {
    let history = [];
    let backward = null;
    let forward = null;
    let currentURL = null;


    const handleChangeSrcIframe = function(e) {
        $iframe = $( getFrameDOM( $('#themeVisualEditor')[0] ) );
        src = $iframe.contents()[0].baseURI;

        let baseUrl = src.split('?')[0];
        let params = src.split('?')[1];

        /*
        remove force_[mobile|tablet|facebook] from url
         */
        if (params && params.indexOf('force_') != -1) {
            let __params = params.split('&');
            params = [];
            for (let i in __params) {
                if (__params[i].indexOf('force_mobile') != -1 
                    && __params[i].indexOf('force_tablet') != -1 
                    && __params[i].indexOf('force_facebook') != -1) {
                    params.push( __params[i] );
                }
            }
            src = baseUrl +'?'+ params.join('&');
        }

        let buttonId = $(this).attr('id');

        if (buttonId == 'tablet_iframe_viewport') {
            src += (src.indexOf('?') != -1) ? '&force_tablet=1' : '?force_tablet=1';

            $('.iframe-container')
            .removeClass('is_mobile')
            .removeClass('is_facebook')
            .addClass('is_tablet')
                .find('iframe')
                .removeClass('is_mobile')
                .removeClass('is_facebook')
                .addClass('is_tablet');
        }

        if (buttonId == 'mobile_iframe_viewport') {
            src += (src.indexOf('?') != -1) ? '&force_mobile=1' : '?force_mobile=1';

            $('.iframe-container')
            .removeClass('is_tablet')
            .removeClass('is_facebook')
            .addClass('is_mobile')
                .find('iframe')
                .removeClass('is_tablet')
                .removeClass('is_facebook')
                .addClass('is_mobile');
        }

        if (buttonId == 'facebook_iframe_viewport') {
            src += (src.indexOf('?') != -1) ? '&force_facebook=1' : '?force_facebook=1';

            $('.iframe-container')
            .removeClass('is_tablet')
            .removeClass('is_mobile')
            .addClass('is_facebook')
                .find('iframe')
                .removeClass('is_tablet')
                .removeClass('is_mobile')
                .addClass('is_facebook');
        }

        if (buttonId != 'tablet_iframe_viewport' && buttonId != 'mobile_iframe_viewport' && buttonId != 'facebook_iframe_viewport') {
            $('.iframe-container')
            .removeClass('is_tablet')
            .removeClass('is_mobile')
            .removeClass('is_facebook')
                .find('iframe')
                .removeClass('is_mobile')
                .removeClass('is_tablet')
                .removeClass('is_facebook');
        }

        src += (src.indexOf('?') != -1) ? '&admin_tools=1' : '?admin_tools=1';
        src += (src.indexOf('?') != -1) ? '&theme_editor=1' : '?theme_editor=1';
        
        $('[name="iframe_url"]').val( src );
        $('#themeVisualEditor').attr({
            src
        });
    }


    $('#themeVisualEditor').on('load', function (e) {
        initWidgetUI();
        initDragNDrop( $( getFrameDOM( this ) ).contents(), this );

        //TODO: simulate backward and forward browser navigation 
        //TODO: simulate custom http headers on request for mobile features

        currentURL = $( getFrameDOM( this ) ).contents()[0].baseURI;

        $('[name="iframe_url"]').val( currentURL );


        $('#desktop_iframe_viewport').on('click', handleChangeSrcIframe);
        $('#tablet_iframe_viewport').on('click', handleChangeSrcIframe);
        $('#mobile_iframe_viewport').on('click', handleChangeSrcIframe);
        $('#facebook_iframe_viewport').on('click', handleChangeSrcIframe);

    });

    $('[name="iframe_url"]').on( 'change', function(e){
        let src = this.value;
        $('#themeVisualEditor').attr({
            src
        });
    });

});

function initWidgetUI() {
    $('#qWidgets').off('keyup').on('keyup', function (e) {
        var that = this;
        var valor = $(that).val().toLowerCase();
        if (valor.length <= 0) {
            $('#widgetsPanel li').show();
        } else {
            $('#widgetsPanel li b').each(function () {
                if ($(this).text().toLowerCase().indexOf(valor) >= 0) {
                    $(this).closest('li').show();
                } else {
                    $(this).closest('li').hide();
                }
            });
        }
    });
}

function initDragNDrop( $iframe, iframe ) {
    initDraggable($("#widgetsPanel li"), $iframe.find("ul.widgets"), $iframe);
    initSortable( $iframe, iframe );
}

function initSortable( $iframe, iframe ) {
    $iframe.find("ul.widgets").sortable({
        connectWith: $iframe.find("ul.widgets"),
        handle: 	 $iframe.find(".move"),
        placeholder: 'placeholder',
        dropOnEmpty: true,
        revert: 	 true,
        forceHelperSize: true,
        forcePlaceholderSize: true,
        cursor: 	 'move',
        start: function(event, ui) {

            if ($(this).data().uiSortable) {
                data.item = $($(this).data().uiSortable.currentItem);
            } else if ($(this).data()['ui-sortable']) {
                data.item = $($(this).data()['ui-sortable'].currentItem);
            } else if ($(this).data().sortable) {
                data.item = $($(this).data().sortable.currentItem);
            } else {
                console.log('No se definió jquery ui sortable');
            }

            if (data.extension) {
                data.item.attr({
	                'id': data.id,
	                'data-widget': data.id,
	                'data-necotienda_module': data.extension
	            }).removeClass('neco-widget').addClass('widgetSet').html(data.html);
            }              
        },
        receive: function(event, ui) {
            let landing_page = getUrlVars()['landing_page'];
            landing_page = typeof landing_page != 'undefined' ? landing_page : 'all';

            iframe.contentWindow.loadWidgets(data.extension, data.id, landing_page, 0, true);
        },
        stop: function () {
            $(this).find("input, select, textarea")
            .bind('mousedown.ui-disableSelection selectstart.ui-disableSelection', function (e) {
                e.stopImmediatePropagation();
            });

            delete data.extension;
            delete data.id;
            delete data.name;
            delete data.widget;
        },
        update: function (event, ui) {
            let postData = {};
            
            $iframe.find('[data-widget]').each(function () {
                postData[$(this).attr('id')] = {
                    'name': $(this).attr('id'),
                    'position': $(this).closest('[data-row]').data('position'),
                    'row_id': $(this).closest('[data-row]').data('row'),
                    'col_id': $(this).closest('[data-column]').data('column'),
                    'order': ($(this).index() + 1)
                };
            });

            $.post(createAdminUrl('style/widget/sortable'), postData);
        }
    });
}

function initDraggable(draggableEl, sortableEl, $iframe) {
	draggableEl.draggable({
        connectToSortable: sortableEl,
        revert: "invalid",
        iframeFix: $iframe,   
        iframeScroll: true,
        zIndex: 101,
        helper: function (event) {
            data.name = $(this).data('title');
            data.extension = $(this).data('widget');
            data.id = "widget_" + data.extension + "_" + rand();

            data.widget = $(this).clone();

            data.html = 
            '<b class="widgetTitle">' + data.name + '</b><br />'+
            '<a style="display:none;" class="advanced">Advanced</a><br />'+
            '<div class="attributes"></div>';

            data.widget.attr({
                'id': data.id,
                'data-widget': data.id,
                'data-necotienda_module': data.extension
            }).addClass('widgetSet').html(data.html);

            return data.widget;
        }
    });
}

function getFrameDOM( el ) {
    return el.contentWindow
        ? el.contentWindow.document
        : el.contentDocument
}


function rand(min, max) {
    if (!min && !max) {
        min = 0;
        max = 2147483647;
    }
    return Math.floor(Math.random() * (max - min + 1)) + min;
}