function initiWidgetVars(options) {
    defaults = {
        containers: {
            preloader:'[data-preloader]',
            extension:'[data-widget]',
            widgets:'[data-widgets-wrapper]',
        }
    }
    window['widgetConfig'] = $.extend({}, defaults, options);

}

function initWidgetUI() {

    $('#qWidgets').on('keyup',function(e){
        var that = this;
        var valor = $(that).val().toLowerCase();
        if (valor.length <= 0) {
            $(window['widgetConfig'].containers.extension).show();
        } else {
            $(window['widgetConfig'].containers.extension +' b').each(function(){
                if ($(this).text().toLowerCase().indexOf( valor ) >= 0) {
                    $(this).closest('li').show();
                } else {
                    $(this).closest('li').hide();
                }
            });
        }
    });

    bgPreloader = $(document.createElement('div'))
        .attr({
            id:'bgPreloader'
        })
        .appendTo(window['widgetConfig'].containers.widgets);

    divPreloader = $(document.createElement('div')).attr({
        id:'divPreloader'
    }).html('<b style="color:#000;">Por favor espere mientras se cargan los complementos.</b><br /><img src="'+ window.nt.http_admin_image +'loader.gif" alt="Cargando..." />').appendTo(window['widgetConfig'].containers.widgets);

    var realHeight = 0;
    $(window['widgetConfig'].containers.widgets).children().each(function(i, item) {
        realHeight = $(this).height() + realHeight;
    });

    bgPreloader.css({
        height:realHeight +'px'
    });

    $.ajaxQueue().then(function(response){
        divPreloader.remove();
        bgPreloader.remove();
    });
}

function loadWidgetFile(url, widgetName) {
    if (typeof url == 'undefined') return false;

    var _url = url.replace('file', 'save');

    $.getJSON(url)
    .done(function(data){
        $('#'+ widgetName +'_editor_filename').html(data.filename);

        var textarea = $('#'+ widgetName +'_editor_code');
        var editor = ace.edit(widgetName +"_editor");

        textarea.val(data.code).hide();

        editor.setTheme("ace/theme/twilight");
        editor.getSession().setValue(textarea.val());

        if (data.ext=='tpl') {
            editor.getSession().setMode('ace/mode/php');
        } else if (data.ext=='css') {
            editor.getSession().setMode('ace/mode/css');
        } else if (data.ext=='js') {
            editor.getSession().setMode('ace/mode/jsx');
        }

        editor.getSession().setUseWrapMode(true);
        editor.getSession().on('change', function(){
            textarea.val( editor.getSession().getValue() );
        });

        $('#'+ widgetName +'_editor_save').off('click').on('click',function(){
            console.log('_url',_url);
            saveWidgetFile( _url, widgetName, editor.getSession().getValue());
        });
    });
}

function saveWidgetFile(url, widgetName, code) {
    $.post(url,{
        code:code
    })
    .done(function(resp){
        var data = $.parseJSON(resp);
        if (data.success) {
            var msg = $(document.createElement('div')).addClass("msg success").html(data.success);
            $('#'+ widgetName +'_editor_filename').after(msg);
            setTimeout(function(){
                msg.fadeOut(function(){
                    $(this).remove();
                });
            }, 2000);
        }
    });
    return false;
}

function addWidgetFile(filename, folder, prefix, widgetName, containerID) {
    if (typeof filename === 'undefined' || filename.length === 0 || !filename) return false;
    $.getJSON(createAdminUrl("style/editor/save"),
    {
        f: folder + prefix + filename
    }).done(function(data){
        if (data.success) {
            var href = createAdminUrl("style/editor/file",
                {
                    f: folder + prefix + filename
                });

            $('#'+ containerID)
                .find('hr')
                .before('<a href="#" onclick="loadWidgetFile(\''+ href +'\', \''+ widgetName +'\');return false;">'+ prefix + filename +'</a><br />');

            $('#'+ containerID)
                .find('input')
                .val('');

            if (filename.indexOf('.tpl') >= 0)
                $('select[name="Widgets['+ widgetName +'][settings][view]"').append('<option value="'+ filename.replace('.tpl', '') +'">'+ filename.replace('.tpl', '') +'</option>');

            var msg = $(document.createElement('div')).addClass("msg success").html(data.success);
            $('#'+ containerID).before(msg);
            setTimeout(function(){
                msg.fadeOut(function(){
                    $(this).remove();
                });
            }, 2000);
        }
    });
}

function initDragNDrop() {

    var data = {};

    $("#widgetsPanel li").draggable({
        connectToSortable:'.widgetWrapper',
        revert: "invalid",
        helper: function(event){
            data.name = $(this).data('title');
            data.extension = $(this).data('widget');
            data.id = "widget_" + data.extension + "_" + rand();
            output = '';
            output += '<b class="widgetTitle">' + data.name + '</b><br />';
            output += '<a style="display:none;" class="advanced">Advanced</a><br />';
            output += '<div class="attributes"></div>';
            output += '<div style="float:right">';
            output += '<a class="moveWidget button" style="padding:2px;cursor:move">Mover</a>';
            output += '<a class="deleteWidget button" onclick="deleteWidget(this)" style="padding:2px;">Eliminar</a>';
            output += '</div>';

            data.html = output;
            data.widget = $(this).clone();
            data.widget.attr('id',data.id).addClass('widgetSet').html(output);

            return data.widget;
        }
    });

    $(".widgetWrapper").sortable({
        placeholder: "widgetPlaceHolder",
        connectWith: '.widgetWrapper',
        revert: true,
        cursor: 'move',
        handle: '.moveWidget',
        start: function(event,ui){
            if ($(this).data().uiSortable) {
                data.item = $($(this).data().uiSortable.currentItem);
            } else if ($(this).data()['ui-sortable']) {
                data.item = $($(this).data()['ui-sortable'].currentItem);
            } else if ($(this).data().sortable) {
                data.item = $($(this).data().sortable.currentItem);
            } else {
                console.log('No se definió jquery ui sortable');
            }

            if (data.name) {
                data.item.attr('id',data.id).removeClass('neco-widget').addClass('widgetSet').html(output);
            }
        },
        receive:function(event,ui) {
            if (data.name) {
                data.position = $(this).closest('.widgetRowsWrapper').data('position');
                data.wrapper = $(this);
                data.sort_order = ($(data.item).index() + 1);

                data.inputs = 
                '<input class="widgetName" type="hidden" name="Widgets[' + data.id + '][name]" id="' + data.id + '_name" value="' + data.id + '" />'+
                '<input class="widgetPosition" type="hidden" name="Widgets[' + data.id + '][position]" id="' + data.id + '_position" value="' + data.position + '" />'+
                '<input class="widgetSortOrder" type="hidden" name="Widgets[' + data.id + '][order]" id="' + data.id + '_order" value="'+ data.sort_order +'" />';

                var widgetId = data.id;
                var row_id = $(data.item).closest('.widgetRow').attr('id');
                var col_id = $(data.item).closest('.widgetColumn').attr('id');
                var store_id = getUrlVars()['store_id'];
                var landing_page = getUrlVars()['landing_page'];
                store_id = typeof store_id !== 'undefined' ? store_id : 0;
                landing_page = typeof landing_page !== 'undefined' ? landing_page : 'all';

                $.ajaxQueue({
                    url: createAdminUrl('module/'+ data.extension +'/widget', 'store_id='+ store_id +'&landing_page='+ landing_page),
                    dataType: "json",
                    data:{
                        'extension':data.extension,
                        'order':data.sort_order,
                        'position':data.position,
                        'store_id':store_id,
                        'row_id':row_id,
                        'col_id':col_id,
                        'name':data.id
                    }
                }).done(function( response ) {
                    if (typeof response.html != 'undefined') {

                        $('#'+ widgetId +' div.attributes').attr({
                            id: widgetId +'_attributes'
                        }).append(response.html);

                        $('#'+ widgetId +'_form').append(data.inputs);

                        $('#'+ widgetId +' .advanced').attr({
                            href: '#'+ widgetId +'_attributes'
                        }).show();

                        var height = $(window).height() * 1.9;
                        var width = $(window).width() * 0.9;

                        $('#'+ widgetId +' a.advanced').fancybox({
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

                        var widgetModule = data.extension;
                        var widgetName = widgetId;

                        saveWidget(widgetName, widgetModule);

                        $('#'+ widgetId).find('input, select, textarea').on('change',function(event){
                            saveWidget(widgetName, widgetModule);
                        });
                    }
                    $(".widgetWrapper").find("input, select, textarea")
                        .bind('mousedown.ui-disableSelection selectstart.ui-disableSelection', function(e) {
                            e.stopImmediatePropagation();
                        });
                });
                data.name = null;
            }
        },
        stop: function () {
            $(this).find("input, select, textarea")
                .bind('mousedown.ui-disableSelection selectstart.ui-disableSelection', function(e) {
                    e.stopImmediatePropagation();
                });
        },
        update: function(event,ui){
            if (!data.name) {
                setOrder();
            }
        }
    });

}

function saveWidget(widgetName, widgetModule) {

    var position    = $('#'+ widgetName).closest('.widgetRowsWrapper').data('position');
    var sort_order  = ($('#'+ widgetName).index() + 1);
    var row_id      = $('#'+ widgetName).closest('.widgetRow').attr('id');
    var col_id      = $('#'+ widgetName).closest('.widgetColumn').attr('id');
    var store_id    = getUrlVars()['store_id'];
    var landing_page= getUrlVars()['landing_page'];

    store_id        = typeof store_id !== 'undefined' ? store_id : 0;
    landing_page    = typeof landing_page !== 'undefined' ? landing_page : 'all';
    position        = typeof position !== 'undefined' ? position : 'main';
    sort_order      = typeof sort_order !== 'undefined' ? sort_order : 0;

    $('.saving').remove();
    $('#'+ widgetName +'_form').before('<img src="'+ window.nt.http_admin_image +'small_loading.gif" class="saving" />');
    $.post(createAdminUrl('module/'+ widgetModule +'/widget', {
        store_id:store_id,
        landing_page:landing_page,
        name:widgetName,
        order:sort_order,
        row_id:row_id,
        col_id:col_id,
        position:position
    }),
    $('#'+ widgetName +'_form').serialize(),
    function(respons){
        $('.saving').remove();

        resp = $.parseJSON(respons);

        if (typeof resp.error != 'undefined') {

        }

        if (typeof resp.success != 'undefined') {

        }
    });
}

function sortCols(source, target) {
    _sortCols(source);
    if (source != target) {
        _sortCols(target);
    }
}

function _sortCols(position) {
    if (typeof position == 'undefined') return false;
    let pos = position;
    if (pos.indexOf('widget')==-1) pos = "widget" + position.charAt(0).toUpperCase() + position.slice(1);
    let cols = {};
    let j = 0;

    $('#'+ position +' .widgetColumn').each(function(){
        var row_id = $(this).closest('.widgetRow').attr('id');
        var col_id = $(this).attr('id');

        cols[col_id] = {
            position:position,
            row_id:row_id,
            col_id:col_id,
            id:col_id,
            order:j
        };

        j = (j+1);
    });

    $.post(createAdminUrl("style/widget/sortcol"), cols);
    //setOrder();
}

function sortRows(source, target) {
    _sortRows(source);
    if (source != target) {
        _sortRows(target);
    }
}

function _sortRows(position) {
    if (typeof position == 'undefined') return false;
    let pos = position;
    if (pos.indexOf('widget')==-1) pos = "widget" + position.charAt(0).toUpperCase() + position.slice(1);

    var rows = {};
    var j = 0;

    $("#" + pos + " .widgetRow").each(function () {
      var row_id = $(this).attr("id");

      rows[row_id] = {
        position: position,
        row_id: row_id,
        id: row_id,
        order: j,
      };
      j = j + 1;
    });
    $.post(createAdminUrl('style/widget/sortrow'), rows);
    // _sortCols(position);
}

function setOrder() {
    data = {};
    $('.widgetSet').each(function(){
        $(this).find('.widgetPosition').val( $(this).closest('.widgetRowsWrapper').data('position') );
        $(this).find('.widgetSortOrder').val( ($(this).index() + 1) );
        data[$(this).attr('id')] = {
            'name':$(this).attr('id'),
            'position':$(this).closest('.widgetRowsWrapper').data('position'),
            'row_id':$(this).closest('.widgetRow').attr('id'),
            'col_id':$(this).closest('.widgetColumn').attr('id'),
            'order':($(this).index() + 1)
        };
    });
    $.post(createAdminUrl('style/widget/sortable'),data);
}

function deleteWidget(e) {
    if (confirm("\xbfEst\u00E1 seguro que desea eliminar este widget?")) {
        var li = $(e).closest("li");
        var widgetName = $(li).attr('id');
        li.fadeOut(function(){
            li.remove();
        });
        $.getJSON(createAdminUrl('style/widget/delete', 'name='+ widgetName));
    }
}

function rand (min, max) {
    if (!min && !max) {
        min = 0;
        max = 2147483647;
    }
    return Math.floor(Math.random() * (max - min + 1)) + min;
}

function addRow(e) {
    var guid = generateGuid();
    var _id    = 'widgetRow_'+ guid;
    var position = $(e).closest('.widgetRowsWrapper').data('position');

    var header_tpl =
        '<header>'+
            '<span>'+
                '<small class="widgetRowMove">[mover]</small>'+
                '<small>'+ _id +'</small>'+
                '<div class="submenu">'+
                    '<i class="fa fa-bars"></i>'+
                    '<div>'+
                        '<em>'+
                            '<a class="advanced" href="#'+ _id +'_config">Settings</a>'+
                            '<div style="display:none;" id="'+ _id +'_config">' +

                                '<form id="'+ _id +'_config_form">' +

                                    '<p style="text-align:center;font-size:10px;">'+ _id +'</p>' +

                                    '<div id="'+ _id +'_htabs" class="htabs">' +
                                        '<a tab="#'+ _id +'_form_general" class="htab">General</a>' +
                                        '<a tab="#'+ _id +'_form_style" class="htab">Style</a>' +
                                    '</div>' +

                                    '<div id="'+ _id +'_form_general">' +

                                        '<div class="row">' +
                                            '<label for="'+ _id +'SettingsShowonmobile">Show In Mobiles</label>' +
                                            '<div class="checkbox">' +
                                                '<input id="'+ _id +'SettingsShowonmobile" type="checkbox" onchange="updateRowUI(\''+ _id +'\')" name="show_in_mobile" checked="checked" />' +
                                                '<span></span>' +
                                            '</div>' +
                                        '</div>' +

                                        '<div class="row">' +
                                            '<label for="'+ _id +'SettingsShowontablets">Show In Tablets</label>' +
                                            '<div class="checkbox">' +
                                                '<input id="'+ _id +'SettingsShowontablets" type="checkbox" onchange="updateRowUI(\''+ _id +'\')" name="show_in_tablet" checked="checked" />' +
                                                '<span></span>' +
                                            '</div>' +
                                        '</div>' +

                                        '<div class="row">' +
                                            '<label for="'+ _id +'SettingsShowondesktop">Show In Desktops</label>' +
                                            '<div class="checkbox">' +
                                                '<input id="'+ _id +'SettingsShowondesktop" type="checkbox" onchange="updateRowUI(\''+ _id +'\')" name="show_in_desktop" checked="checked" />' +
                                                '<span></span>' +
                                            '</div>' +
                                        '</div>' +

                                        '<div class="row">' +
                                            '<label for="'+ _id +'SettingsShowonfacebook">Show In Facebook</label>' +
                                            '<div class="checkbox">' +
                                                '<input id="'+ _id +'SettingsShowonfacebook" type="checkbox" onchange="updateRowUI(\''+ _id +'\')" name="show_in_facebook" checked="checked" />' +
                                                '<span></span>' +
                                            '</div>' +
                                        '</div>' +
                                        
                                        '<div class="row">' +
                                            '<label for="'+ _id +'SettingsSticky">Sticky</label>' +
                                            '<div class="checkbox">' +
                                                '<input id="'+ _id +'SettingsSticky" type="checkbox" onchange="updateRowUI(\''+ _id +'\')" name="sticky" />' +
                                                '<span></span>' +
                                            '</div>' +
                                        '</div>' +

                                        '<div class="row">' +
                                            '<label for="'+ _id +'SettingsLayoutWidth">Layout Width</label>' +
                                            '<select name="layout_width" onchange="updateRowUI(\''+ _id +'\')">' +
                                                '<option value="fluid">Fluid</option>' +
                                                '<option value="fixed">Fixed</option>' +
                                            '</select>' +
                                        '</div>' +


                                        '<div class="row">' +
                                            '<label>Customer Session Mode</label>' +
                                            '<select name="customer_session_mode" onchange="updateRowUI(\''+ _id +'\')">' +
                                                '<option value="any">Any</option>' +
                                                '<option value="logon">Needs to be Log On</option>' +
                                                '<option value="logoff">Needs to be Log Off</option>' +
                                            '</select>' +
                                        '</div>' +

                                        '<div class="row">' +
                                            '<label>Logic Conditions Action</label>' +
                                            '<select name="conditional_logic_action" onchange="updateRowUI(\''+ _id +'\')">' +
                                                '<option value="show">Show</option>' +
                                                '<option value="hide">Hide</option>' +
                                            '</select>' +
                                        '</div>' +

                                        '<div class="row">' +
                                            '<label>When Route contains</label>' +
                                            '<input type="text" name="conditional_logic_when_route_contains" onchange="updateRowUI(\''+ _id +'\')" />' +
                                        '</div>' +

                                    '</div>'+

                                    '<div id="'+ _id +'_form_style">'+
                                        '<div class="row">'+
                                            '<a class="button" onclick="updateRowUI(\''+ _id +'\');">Save Style</a>'+

                                            '<textarea showquick="off" name="style" id="'+ _id +'_style_code" style="display:none;">/** \nWrite your own css style only for this widget \nWe recommend use the widget name as a wrapper for styling, \nfor example #'+ _id +' .someClass { ... } \n**/</textarea>'+
                                            '<div id="'+ _id +'_style_editor" style="width:90%;height:800px;display:block;"></div>'+
                                        '</div>'+
                                    '</div>'+


                                    '<input id="'+ _id +'_position" name="position" type="hidden" onchange="updateRowUI(\''+ _id +'\')" value="'+ position +'">'+
                                '</form>'+
                            '</div>'+
                        '</em>'+
                        '<em><a onclick="removeRow(\'#'+ _id +'\')">Delete</a></em>'+
                    '</div>'+
                '</div>'+
            '</span>'+
        '</header>';

    var button = $('<button>').addClass('button').attr({
        onclick:'addColumn(this)'
    }).html('Add Column');

    var input = $('<input>')
        .addClass('widgetRowInput')
        .attr({
            name:'row_'+ guid,
            value:'row_'+ guid,
            id:'input_row_'+ guid,
            type:'hidden'
        });

    var div = $('<div>')
        .addClass('grid_3')
        .append(input)
        .append(button);

    var widgetColsWrapper = $('<div>')
        .addClass('widgetColsWrapper');

    var row = $('<li>')
        .addClass('row widgetRow widgetBox')
        .attr('id', _id)
        .append( header_tpl )
        .append( widgetColsWrapper )
        .append( div );

    $(e).closest('div').find('ul.widgetRowsWrapper').append( row );

    rowSortableUI();

    var styleTextarea = $('#'+ _id +'_style_code');
    if (document.getElementById(_id + "_style_editor")) {
        try {
            var styleEditor = ace.edit(_id + "_style_editor");

            styleEditor.setTheme("ace/theme/twilight");
            styleEditor.getSession().setValue(styleTextarea.val());
            styleEditor.getSession().setMode("ace/mode/css");

            styleEditor.getSession().setUseWrapMode(true);
            styleEditor.getSession().on("change", function () {
                styleTextarea.val(styleEditor.getSession().getValue());
            });
            } catch (err) {}
    }
    
    $('#'+ _id +' .htab').on('click', function () {
        $(this).closest('.htabs').find('.htab').each(function () {
            $($(this).attr('tab')).hide();
            $(this).removeClass('selected');
        });
        $(this).addClass('selected');
        $($(this).attr('tab')).show();
    });
    $('#'+ _id +' .htab').eq(0).trigger('click');
    saveRow(_id);
    setOrderRows();
}

function setOrderRows() {

}

function saveRow(id) {
    var data = {};

    data.position = $('#'+ id).closest('.widgetRowsWrapper').data('position');
    data.row_id = id;
    data.order = $('#'+ id).index();
    data.settings = $('#'+ id +'_config_form').serialize();
    data.landing_page = getUrlVars()['landing_page'];
    data.store_id = getUrlVars()['store_id'];

    data.landing_page = typeof data.landing_page !== 'undefined' && data.landing_page.length > 0 ? data.landing_page : 'all';
    data.store_id = typeof data.store_id !== 'undefined' && data.store_id > 0 ? data.store_id : 0;

    $.post(createAdminUrl('style/widget/saverow'), data).done(function(resp){

    });
}

function updateRowUI(id) {
    var data = {};

    saveRow(id);
}

function rowSortableUI() {
    var data = {};

    $(".widgetRowsWrapper").sortable({
        placeholder: "widgetRowPlaceHolder",
        connectWith: '.widgetRowsWrapper',
        revert: true,
        cursor: 'move',
        handle: '.widgetRowMove',
        start: function(event,ui){
            if ($(this).data().uiSortable) {
                data.item = $($(this).data().uiSortable.currentItem);
            } else if ($(this).data()['ui-sortable']) {
                data.item = $($(this).data()['ui-sortable'].currentItem);
            } else if ($(this).data().sortable) {
                data.item = $($(this).data().sortable.currentItem);
            } else {
                console.log('No se definió jquery ui sortable');
            }

            data.source = data.item.data('position');
        },
        receive:function(event,ui) {
            data.target = $(this).data('position');
            sortRows(data.source, data.target);
        },
        update: function(event,ui){
            data.target = $(this).data('position');
            sortRows(data.source, data.target);
        }
    });
}

function removeRow(id) {
    if(confirm('Esta seguro de eliminar este contenedor?')) {
        $(id).remove();
        $.getJSON(createAdminUrl('style/widget/deleterow'), {
            row_id:id
        });
    }
}

function removeColumn(id) {
    if(confirm('Esta seguro de eliminar esta columna?')) {
        $(id).remove();
        $.getJSON(createAdminUrl('style/widget/deletecolumn'), {
            col_id:id
        });
    }
}

function addColumn(e) {
    var guid = generateGuid();
    var row_id = $(e).closest('li').attr('id');
    var _id = 'widgetColumn_'+ guid;
    var position = $(e).closest('.widgetRowsWrapper').data('position');

    var header_tpl =
        '<header>'+
            '<span>'+
                '<small class="colMove"><i class="fa fa-arrows fa-lg"></i></small>'+
                '<small class="submenu">'+
                    '<i class="fa fa-bars"></i>'+
                    '<div>'+
                        '<em>'+
                            '<a class="advanced" href="#'+ _id +'_config">Settings</a>'+
                            '<div style="display:none;" id="'+ _id +'_config">'+

                                '<form id="'+ _id +'_config_form">' +

                                    '<p style="text-align:center;font-size:10px;">'+ _id +'</p>' +

                                    '<div id="'+ _id +'_htabs" class="htabs">' +
                                        '<a tab="#'+ _id +'_form_general" class="htab">General</a>' +
                                        '<a tab="#'+ _id +'_form_style" class="htab">Style</a>' +
                                    '</div>' +

                                    '<div id="'+ _id +'_form_general">' +

                                        '<div class="row">' +
                                            '<label for="'+ _id +'SettingsShowonmobile">Show In Mobiles</label>' +
                                            '<div class="checkbox">' +
                                                '<input id="'+ _id +'SettingsShowonmobile" type="checkbox" onchange="updateColUI(\''+ _id +'\')" name="show_in_mobile" checked="checked" />' +
                                                '<span></span>' +
                                            '</div>' +
                                        '</div>' +

                                        '<div class="row">' +
                                            '<label for="'+ _id +'SettingsShowontablets">Show In Tablets</label>' +
                                            '<div class="checkbox">' +
                                                '<input id="'+ _id +'SettingsShowontablets" type="checkbox" onchange="updateColUI(\''+ _id +'\')" name="show_in_tablet" checked="checked" />' +
                                                '<span></span>' +
                                            '</div>' +
                                        '</div>' +

                                        '<div class="row">' +
                                            '<label for="'+ _id +'SettingsShowondesktop">Show In Desktops</label>' +
                                            '<div class="checkbox">' +
                                                '<input id="'+ _id +'SettingsShowondesktop" type="checkbox" onchange="updateColUI(\''+ _id +'\')" name="show_in_desktop" checked="checked" />' +
                                                '<span></span>' +
                                            '</div>' +
                                        '</div>' +

                                        '<div class="row">' +
                                            '<label for="'+ _id +'SettingsShowonfacebook">Show In Facebook</label>' +
                                            '<div class="checkbox">' +
                                                '<input id="'+ _id +'SettingsShowonfacebook" type="checkbox" onchange="updateColUI(\''+ _id +'\')" name="show_in_facebook" checked="checked" />' +
                                                '<span></span>' +
                                            '</div>' +
                                        '</div>' +

                                        '<div class="row">' +
                                            '<label for="'+ _id +'SettingsSticky">Sticky</label>' +
                                            '<div class="checkbox">' +
                                                '<input id="'+ _id +'SettingsSticky" type="checkbox" onchange="updateColUI(\''+ _id +'\')" name="sticky" />' +
                                                '<span></span>' +
                                            '</div>' +
                                        '</div>' +

                                        '<div class="row">' +
                                            '<label for="'+ _id +'SettingsLayoutWidth">Layout Width</label>' +
                                            '<select name="layout_width" onchange="updateColUI(\''+ _id +'\')">' +
                                                '<option value="fluid">Fluid</option>' +
                                                '<option value="fixed">Fixed</option>' +
                                            '</select>' +
                                        '</div>' +

                                        '<div class="row">' +
                                            '<label for="'+ _id +'_grid_large">Grid Large</label>'+
                                            '<select id="'+ _id +'_grid_large" name="grid_large" onchange="updateColUI(\''+ _id +'\')">' +
                                                '<option value="1">large-1</option>' +
                                                '<option value="2">large-2</option>' +
                                                '<option value="3">large-3</option>' +
                                                '<option value="4">large-4</option>' +
                                                '<option value="5">large-5</option>' +
                                                '<option value="6">large-6</option>' +
                                                '<option value="7">large-7</option>' +
                                                '<option value="8">large-8</option>' +
                                                '<option value="9">large-9</option>' +
                                                '<option value="10">large-10</option>' +
                                                '<option value="11">large-11</option>' +
                                                '<option value="12" selected>large-12</option>' +
                                            '</select>'+
                                        '</div>'+

                                        '<div class="row">' +
                                            '<label for="'+ _id +'_grid_medium">Grid Medium</label>'+
                                            '<select id="'+ _id +'_grid_medium" name="grid_medium" onchange="updateColUI(\''+ _id +'\')">' +
                                                '<option value="1">medium-1</option>' +
                                                '<option value="2">medium-2</option>' +
                                                '<option value="3">medium-3</option>' +
                                                '<option value="4">medium-4</option>' +
                                                '<option value="5">medium-5</option>' +
                                                '<option value="6">medium-6</option>' +
                                                '<option value="7">medium-7</option>' +
                                                '<option value="8">medium-8</option>' +
                                                '<option value="9">medium-9</option>' +
                                                '<option value="10">medium-10</option>' +
                                                '<option value="11">medium-11</option>' +
                                                '<option value="12" selected>medium-12</option>' +
                                            '</select>'+
                                        '</div>'+

                                        '<div class="row">' +
                                            '<label for="'+ _id +'_grid_small">Grid Small</label>'+
                                            '<select id="'+ _id +'_grid_small" name="grid_small" onchange="updateColUI(\''+ _id +'\')">' +
                                                '<option value="1">small-1</option>' +
                                                '<option value="2">small-2</option>' +
                                                '<option value="3">small-3</option>' +
                                                '<option value="4">small-4</option>' +
                                                '<option value="5">small-5</option>' +
                                                '<option value="6">small-6</option>' +
                                                '<option value="7">small-7</option>' +
                                                '<option value="8">small-8</option>' +
                                                '<option value="9">small-9</option>' +
                                                '<option value="10">small-10</option>' +
                                                '<option value="11">small-11</option>' +
                                                '<option value="12" selected>small-12</option>' +
                                            '</select>'+
                                        '</div>'+

                                        
                                        '<div class="row">' +
                                            '<label>Customer Session Mode</label>' +
                                            '<select name="customer_session_mode" onchange="updateColUI(\''+ _id +'\')">' +
                                                '<option value="any">Any</option>' +
                                                '<option value="logon">Needs to be Log On</option>' +
                                                '<option value="logoff">Needs to be Log Off</option>' +
                                            '</select>' +
                                        '</div>' +

                                        '<div class="row">' +
                                            '<label>Logic Conditions Action</label>' +
                                            '<select name="conditional_logic_action" onchange="updateColUI(\''+ _id +'\')">' +
                                                '<option value="show">Show</option>' +
                                                '<option value="hide">Hide</option>' +
                                            '</select>' +
                                        '</div>' +
                                        
                                        '<div class="row">' +
                                            '<label>When Route contains</label>' +
                                            '<input type="text" name="conditional_logic_when_route_contains" onchange="updateColUI(\''+ _id +'\')" />' +
                                        '</div>' +
                                    '</div>'+

                                    '<div id="'+ _id +'_form_style">'+
                                        '<div class="row">'+
                                            '<a class="button" onclick="updateColUI(\''+ _id +'\');">Save Style</a>'+

                                            '<textarea showquick="off" name="style" id="'+ _id +'_style_code" style="display:none;">/** \nWrite your own css style only for this widget \nWe recommend use the widget name as a wrapper for styling, \nfor example #'+ _id +' .someClass { ... } \n**/</textarea>'+
                                            '<div id="'+ _id +'_style_editor" style="width:90%;height:800px;display:block;"></div>'+
                                        '</div>'+
                                    '</div>'+

                                    '<input id="'+ _id +'_position" name="position" type="hidden" onchange="updateColUI(\''+ _id +'\')" value="'+ position +'">'+

                                '</form>'+
                            '</div>'+
                        '</em>'+
                        '<em><a onclick="removeColumn(\'#'+ _id +'\')">Delete</a></em>'+
                    '</div>'+
                '</small>'+
            '</span>'+
        '</header>';

    var ul = $('<ul>')
        .addClass('widgetWrapper ui-sortable')
        .attr('id', 'widgetColumn_ul_'+ guid);

    var input = $('<input>')
        .addClass('widgetColumnInput')
        .attr({
            name:'column_'+ guid,
            value:row_id +':column_'+ guid,
            id:'input_column_'+ guid,
            type:'hidden'
        });

    var column = $('<div>')
        .addClass('grid_12 widgetColumn widgetBox')
        .attr('id', _id)
        .attr('data-grid-small', 'grid_12')
        .attr('data-grid-medium', 'grid_12')
        .attr('data-grid-large', 'grid_12')
        .append( header_tpl )
        .append( ul )
        .append( input );

    $(e).closest('li').find('.widgetColsWrapper').append( column );

    colSortableUI();
    initDragNDrop();

    var styleTextarea = $('#'+ _id +'_style_code');
    var styleEditor   = ace.edit(_id +"_style_editor");

    styleEditor.setTheme("ace/theme/twilight");
    styleEditor.getSession().setValue(styleTextarea.val());
    styleEditor.getSession().setMode('ace/mode/css');

    styleEditor.getSession().setUseWrapMode(true);
    styleEditor.getSession().on('change', function(){
        styleTextarea.val( styleEditor.getSession().getValue() );
    });

    $('#'+ _id +' .htab').on('click', function () {
        $(this).closest('.htabs').find('.htab').each(function () {
            $($(this).attr('tab')).hide();
            $(this).removeClass('selected');
        });
        $(this).addClass('selected');
        $($(this).attr('tab')).show();

    });
    $('#'+ _id +' .htab').eq(0).trigger('click');
    saveCol(_id);
}

function saveCol(id) {
    var data = {};

    data.position = $('#'+ id).closest('.widgetRowsWrapper').data('position');
    data.row_id = $('#'+ id).closest('.widgetRow').attr('id');
    data.col_id = id;
    data.order = $('#'+ id).index();
    data.settings = $('#'+ id +'_config_form').serialize();
    data.landing_page = getUrlVars()['landing_page'];
    data.store_id = getUrlVars()['store_id'];

    data.landing_page = typeof data.landing_page !== 'undefined' && data.landing_page.length > 0 ? data.landing_page : 'all';
    data.store_id = typeof data.store_id !== 'undefined' && data.store_id > 0 ? data.store_id : 0;

    $.post(createAdminUrl('style/widget/savecol'), data).done(function(resp){

    });
}

function updateColUI(id) {
    var data = {};

    $('#'+ id)
        .removeClass( 'grid_'+ $('#'+ id).data('grid-large') )
        .addClass( 'grid_'+ $('#'+ id +'_grid_large').val() );

    saveCol(id);
}

function colSortableUI() {
    var data = {};

    $(".widgetColsWrapper").sortable({
        placeholder: "widgetColPlaceHolder",
        connectWith: '.widgetColsWrapper',
        revert: true,
        cursor: 'move',
        handle: '.colMove',
        items: '.widgetColumn ',
        start: function(event,ui){
            if ($(this).data().uiSortable) {
                data.item = $($(this).data().uiSortable.currentItem);
            } else if ($(this).data()['ui-sortable']) {
                data.item = $($(this).data()['ui-sortable'].currentItem);
            } else if ($(this).data().sortable) {
                data.item = $($(this).data().sortable.currentItem);
            } else {
                console.log('No se definió jquery ui sortable');
            }

            data.source = data.item.closest('.widgetRowsWrapper').data('position');
        },
        receive:function(event,ui) {
            data.target = $(this).closest('.widgetRowsWrapper').data('position');
            sortCols(data.source, data.target);
        },
        update: function(event,ui){
            data.target = $(this).closest('.widgetRowsWrapper').data('position');
            sortCols(data.source, data.target);
        }
    });
}

function generateGuid() {
    var S4 = function() {
        return (((1+Math.random())*0x10000)|0).toString(16).substring(1);
    };
    return (S4()+S4()+S4());
}

function addWidgetTransition(widgetId) {
    let index = $('#'+ widgetId +'TransitionsWrapper .row').length;
    let tpl = 
        '<div class="row">'
            +'<small style="float:left;" class="colMove">'
                +'<i class="fa fa-arrows fa-lg"></i>'
            +'</small>'
            +'<small style="float:right;margin-right:50px;" class="deleteTransition" onclick="deleteTransition(this, \''+ widgetId +'\')">'
                +'<i class="fa fa-times fa-lg"></i>'
            +'</small>'
            +'<div class="grid_3">'
                +'<label>Delay (Seconds)</label>'
                +'<input name="Widgets['+ widgetId +'][settings][transitions]['+ index +'][delay]" value="0" />'
            +'</div>'

            +'<div class="grid_3">'
                +'<label>Duration (Seconds)</label>'
                +'<input name="Widgets['+ widgetId +'][settings][transitions]['+ index +'][duration]" value="0" />'
            +'</div>'

            +'<div class="grid_4">'
                +'<label>Effect</label>'
                +'<select name="Widgets['+ widgetId +'][settings][transitions]['+ index +'][effect]">'
                    +'<option value="">Select transition effect</option>';
            
                if (!!window["transition_effects"]) {
                  for (let engine in window['transition_effects']["animate.css"]) {
                    tpl += `<optgroup label="${engine}">`;

                    for (let k in window['transition_effects']["animate.css"][engine]) {
                      tpl += `<option value="${window["transition_effects"]["animate.css"][engine][k]}">${k}</option>`;
                    }

                    tpl += `</optgroup>`;
                  }
                }

                tpl += '</select>'
            +'</div>'
            +'<input type="hidden" name="Widgets['+ widgetId +'][settings][transitions]['+ index +'][order]" value="'+ index +'" />'
        +'</div>';
    $('#'+ widgetId +'TransitionsWrapper').append( tpl );

    bindFormOnChange( widgetId );
    initSortableTransitions( widgetId );
}

function deleteTransition(el, widgetId) {
    $(el).closest('.row').remove();
    updateSortOrdersTransitions(widgetId);
}

function updateSortOrdersTransitions(widgetId) {
    $("#"+ widgetId +"TransitionsWrapper .row").each(function(){
        $(this).find('input[name*=order]').val( $(this).index() );
    });
    $("#"+ widgetId +"TransitionsWrapper input").eq(0).trigger('change');
}

function initSortableTransitions(widgetId) {

    $("#"+ widgetId +"TransitionsWrapper").sortable({
        placeholder: "widgetColPlaceHolder",
        revert: true,
        cursor: 'move',
        handle: '.colMove',
        receive:function(event,ui) {
            updateSortOrdersTransitions( widgetId );
        },
        update: function(event,ui){
            updateSortOrdersTransitions( widgetId );
        }
    });
}

function bindFormOnChange(widgetId) {
    let name = widgetId;
    let order = $('#'+ widgetId +'_order').val();
    let store_id = getUrlVars()['store_id'];
    let landing_page = $('#'+ widgetId +'_landing_page').val();
    let position = $('#'+ widgetId +'_position').val();
    let route = $('#'+ widgetId +'_route').val();

    if (typeof store_id == 'undefined') store_id = 0;
    if (typeof landing_page == 'undefined') landing_page = 'all';
    if (typeof position == 'undefined') position = 'main';

    landing_page = landing_page.replace('landing_page=', '');

    $('#'+ widgetId +'_form').find('input, select, textarea').off('change').on('change', function(e){
        $('.saving').remove();

        $('#'+ widgetId +'_form').before('<img src="'+ window.nt.http_admin_image +'small_loading.gif" class="saving" />');

        $.post(createAdminUrl(route +"/widget",
        {
            name,
            order,
            store_id,
            landing_page,
            position
        }),
        $('#'+ widgetId +'_form').serialize(),
        function(){
            $('.saving').remove();
        });
    });
}

function loadNtWidgets(widget) {
    if (typeof widget.extension == 'undefined' ||
        typeof widget.position == 'undefined' ||
        typeof widget.name == 'undefined' ||
        typeof widget.order == 'undefined') {
        return false;
    }

    widget.store_id = getUrlVars()['store_id'];
    widget.landing_page = getUrlVars()['landing_page'];

    $.ajaxQueue({
        url: createAdminUrl("module/"+ widget.extension +"/widget", "w=1"),
        dataType: "json",
        data:widget
    }).done(function( data ) {
        $('#'+ widget.name +'_attributes').html(data.html);
        $('#'+ widget.name +'_form').append(
            '<input id="'+ widget.name +'_position" type="hidden" name="Widgets['+ widget.name +'][position]" value="'+ widget.position +'" />'+
            '<input id="'+ widget.name +'_order" type="hidden" name="Widgets['+ widget.name +'][order]" value="'+ widget.order +'" />'+
            '<input id="'+ widget.name +'_name" type="hidden" name="Widgets['+ widget.name +'][name]" value="'+ widget.name +'" />');
        $('.widgetWrapper').find("input, select, textarea, p")
            .bind('mousedown.ui-disableSelection selectstart.ui-disableSelection', function(e) {
                e.stopImmediatePropagation();
            });

        initSortableTransitions(widget.name);

        $('#'+ widget.name +'').find('input, select, textarea').off('change').on('change',function(event){
            $('.saving').remove();
            $('#'+ widget.name +'_form').before('<img src="'+ window.nt.http_admin_image +'small_loading.gif" class="saving" />');
            $.post(createAdminUrl("module/"+ widget.extension +"/widget", {
                    name:widget.name,
                    order:widget.order,
                    store_id:widget.store_id,
                    landing_page:widget.landing_page,
                    position:widget.position
                }),
                $('#'+ widget.name +'_form').serialize(),
                function(respons){
                    $('.saving').remove();
                    resp = $.parseJSON(respons);
                });
        });
    });
}