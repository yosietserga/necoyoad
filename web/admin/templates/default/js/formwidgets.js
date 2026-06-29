$(function () {
    initWidgetUI();
    rowSortableUI();
    colSortableUI();
    initDragNDrop();

    var height = $(window).height() * 1.9;
    var width = $(window).width() * 0.9;

    $(".advanced").fancybox({
        maxWidth: width,
        maxHeight: height,
        fitToView: false,
        width: '90%',
        height: '90%',
        autoSize: false,
        closeClick: false,
        openEffect: 'none',
        closeEffect: 'none'
    });

});

function initWidgetUI() {

    $('#widgetsFormWrapper').css({
        position: 'relative',
        overflow: 'hidden'
    });

    $('#qWidgets').on('keyup', function (e) {
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

    bgPreloader = $(document.createElement('div')).attr({
        id: 'bgPreloader'
    }).appendTo('#widgetsFormWrapper');

    divPreloader = $(document.createElement('div')).attr({
        id: 'divPreloader'
    }).html('<b style="color:#000;">Por favor espere mientras se cargan los complementos.</b><br /><img src="' + window.nt.http_admin_image + 'loader.gif" alt="Cargando..." />').appendTo('#widgetsFormWrapper');

    $.ajaxQueue().then(function (response) {
        divPreloader.remove();
        bgPreloader.remove();
    });

}

function initDragNDrop() {

    var data = {};

    $("#widgetsPanel li").draggable({
        connectToSortable: '.widgetWrapper',
        revert: "invalid",
        helper: function (event) {
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
            data.widget.attr('id', data.id).addClass('widgetSet').html(output);

            return data.widget;
        }
    });

    $(".widgetWrapper").sortable({
        placeholder: "widgetPlaceHolder",
        connectWith: '.widgetWrapper',
        revert: true,
        cursor: 'move',
        handle: '.moveWidget',
        start: function (event, ui) {
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
                data.item.attr('id', data.id).removeClass('neco-widget').addClass('widgetSet').html(output);
            }
        },
        receive: function (event, ui) {
            if (data.name) {
                data.position = $(this).closest('.widgetRowsWrapper').data('position');
                data.wrapper = $(this);
                data.sort_order = ($(data.item).index() + 1);

                data.inputs = '<input class="widgetName" type="hidden" name="Widgets[' + data.id + '][name]" id="' + data.id + '_name" value="' + data.id + '" /><input class="widgetPosition" type="hidden" name="Widgets[' + data.id + '][position]" id="' + data.id + '_position" value="' + data.position + '" /><input class="widgetSortOrder" type="hidden" name="Widgets[' + data.id + '][order]" id="' + data.id + '_order" value="' + data.sort_order + '" />';

                var widgetId = data.id;
                var row_id = $(data.item).closest('.widgetRow').attr('id');
                var col_id = $(data.item).closest('.widgetColumn').attr('id');
                var store_id = getUrlVars()['store_id'];
                var landing_page = getUrlVars()['landing_page'];
                store_id = typeof store_id !== 'undefined' ? store_id : 0;
                landing_page = typeof landing_page !== 'undefined' ? landing_page : 'all';

                $.ajaxQueue({
                    url: createAdminUrl('module/' + data.extension + '/widget',
                        'store_id=' + store_id +
                        '&landing_page=' + landing_page +
                        '&ot='+ window.ot+
                        '&oid='+ window.oid
                    ),
                    dataType: "json",
                    data: {
                        'extension': data.extension,
                        'order': data.sort_order,
                        'position': data.position,
                        'row_id': row_id,
                        'col_id': col_id,
                        'name': data.id
                    }
                }).done(function (response) {
                    if (typeof response.html != 'undefined') {

                        $('#' + widgetId + ' div.attributes').attr({
                            id: widgetId + '_attributes'
                        }).append(response.html);

                        $('#' + widgetId + '_form').append(data.inputs);

                        $('#' + widgetId + ' .advanced').attr({
                            href: '#' + widgetId + '_attributes'
                        }).show();

                        var height = $(window).height() * 1.9;
                        var width = $(window).width() * 0.9;

                        $('#' + widgetId + ' a.advanced').fancybox({
                            maxWidth: width,
                            maxHeight: height,
                            fitToView: false,
                            width: '90%',
                            height: '90%',
                            autoSize: false,
                            closeClick: false,
                            openEffect: 'none',
                            closeEffect: 'none'
                        });

                        var widgetModule = data.extension;
                        var widgetName = widgetId;

                        $.post(createAdminUrl('module/' + widgetModule + '/widget', {
                                store_id: getUrlVars()['store_id'],
                                landing_page: landing_page,
                                name: widgetName,
                                order: data.sort_order,
                                row_id: row_id,
                                col_id: col_id,
                                position: data.position
                            }),
                            $('#' + widgetId + '_form').serialize());

                        $('#' + widgetId).find('input, select, textarea').on('change', function (event) {
                            $('.saving').remove();
                            $('#' + widgetId + '_form').before('<img src="' + window.nt.http_admin_image + 'small_loading.gif" class="saving" />');
                            $.post(createAdminUrl('module/' + widgetModule + '/widget', {
                                    store_id: getUrlVars()['store_id'],
                                    landing_page: landing_page,
                                    name: widgetName,
                                    order: data.sort_order,
                                    row_id: row_id,
                                    col_id: col_id,
                                    position: data.position
                                }),
                                $('#' + widgetId + '_form').serialize(),
                                function (respons) {
                                    $('.saving').remove();
                                    resp = $.parseJSON(respons);
                                    if (typeof resp.error != 'undefined') {

                                    }
                                    if (typeof resp.success != 'undefined') {

                                    }
                                });
                        });
                    }
                    $(".widgetWrapper").find("input, select, textarea")
                        .bind('mousedown.ui-disableSelection selectstart.ui-disableSelection', function (e) {
                            e.stopImmediatePropagation();
                        });
                });
                data.name = null;
            }
        },
        stop: function () {
            $(this).find("input, select, textarea")
                .bind('mousedown.ui-disableSelection selectstart.ui-disableSelection', function (e) {
                    e.stopImmediatePropagation();
                });
        },
        update: function (event, ui) {
            if (!data.name) {
                setOrder();
            }
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

    var cols = {};
    var widgets = {};
    var j = 0;

    $('#' + position + ' .widgetColumn').each(function () {
        var row_id = $(this).closest('.widgetRow').attr('id');
        var col_id = $(this).attr('id');

        cols.push({
            position: position,
            row_id: row_id,
            col_id: col_id,
            order: j
        });
        j = (j + 1);

        var k = 0;
        $(this).find('.widgetSet').each(function () {
            widgets.push({
                position: position,
                row_id: row_id,
                col_id: col_id,
                order: k,
                name: $(this).attr('id')
            });
            k = (k + 1);
        });
    });
    $.post(createAdminUrl('style/widget/sortcol'), cols);
    $.post(createAdminUrl('style/widget/sortable'), widgets);
}

function sortRows(source, target) {
    _sortRows(source);
    if (source != target) {
        _sortRows(target);
    }
}

function _sortRows(position) {
    if (typeof position == 'undefined') return false;

    var rows = {};
    var widgets = {};
    var j = 0;

    $('#' + position + ' .widgetRow').each(function () {
        var row_id = $(this).attr('id');

        rows.push({
            position: position,
            row_id: row_id,
            order: j
        });
        j = (j + 1);

        var k = 0;
        $(this).find('.widgetSet').each(function () {
            widgets.push({
                position: position,
                row_id: row_id,
                order: k,
                name: $(this).attr('id')
            });
            k = (k + 1);
        });
    });
    $.post(createAdminUrl('style/widget/sortrow'), rows);
    $.post(createAdminUrl('style/widget/sortable'), widgets);
}

function setOrder() {
    data = {};
    $('.widgetSet').each(function () {
        $(this).find('.widgetPosition').val($(this).closest('.widgetRowsWrapper').data('position'));
        $(this).find('.widgetSortOrder').val(($(this).index() + 1));
        data[$(this).attr('id')] = {
            'name': $(this).attr('id'),
            'position': $(this).closest('.widgetRowsWrapper').data('position'),
            'row_id': $(this).closest('.widgetRow').attr('id'),
            'col_id': $(this).closest('.widgetColumn').attr('id'),
            'order': ($(this).index() + 1)
        };
    });
    $.post(createAdminUrl('style/widget/sortable'), data);
}

function deleteWidget(e) {
    if (confirm("\xbfEst\u00E1 seguro que desea eliminar este widget?")) {
        var li = $(e).closest("li");
        var widgetName = $(li).attr('id');
        li.fadeOut(function () {
            li.remove();
        });
        $.getJSON(createAdminUrl('style/widget/delete', 'name=' + widgetName));
    }
}

function rand(min, max) {
    if (!min && !max) {
        min = 0;
        max = 2147483647;
    }
    return Math.floor(Math.random() * (max - min + 1)) + min;
}

function addRow(e) {
    var guid = generateGuid();
    var _id = 'widgetRow_'+ guid;
    var position = $(e).closest('.widgetRowsWrapper').data('position');

    var header_tpl =
        '<header>' +
        '<span>' +
        '<small class="widgetRowMove">[mover]</small>' +
        '<small>widgetRow_' + guid + '</small>' +
        '<div class="submenu">' +
        '<i class="fa fa-bars"></i>' +
        '<div>' +
        '<em>' +
        '<a class="advanced" href="#widgetRow_' + guid + '_config">Settings</a>' +
        '<div style="display:none;" id="widgetRow_' + guid + '_config">' +

        '<form id="widgetRow_' + guid + '_config_form">' +
        '<div class="row">' +
        '<label for="widgetRow_' + guid + '_show_in_mobile">Show In Mobiles</label>' +
        '<input id="widgetRow_' + guid + '_show_in_mobile" name="show_in_mobile" type="checkbox" onchange="updateRowUI(\'widgetRow_' + guid + '\')" checked />' +
        '</div>' +

        '<div class="row">' +
        '<label for="widgetRow_' + guid + '_show_in_tablet">Show In Tablets</label>' +
        '<input id="widgetRow_' + guid + '_show_in_tablet" name="show_in_tablet" type="checkbox" onchange="updateRowUI(\'widgetRow_' + guid + '\')" checked />' +
        '</div>' +

        '<div class="row">' +
        '<label for="widgetRow_' + guid + '_show_in_desktop">Show In Desktops</label>' +
        '<input id="widgetRow_' + guid + '_show_in_desktop" name="show_in_desktop" type="checkbox" onchange="updateRowUI(\'widgetRow_' + guid + '\')" checked />' +
        '</div>' +

        '<div class="row">' +
        '<label for="widgetRow_' + guid + '_show_in_facebook">Show In Facebook</label>' +
        '<input id="widgetRow_' + guid + '_show_in_facebook" name="show_in_facebook" type="checkbox" onchange="updateRowUI(\'widgetRow_' + guid + '\')" checked />' +
        '</div>' +

        '<div class="row">' +
        '<label for="widgetRow_' + guid + '_async">Async / Ajax</label>' +
        '<input id="widgetRow_' + guid + '_async" name="async" type="checkbox" onchange="updateRowUI(\'widgetRow_' + guid + '\')" checked />' +
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

        '<input id="widgetRow_' + guid + '_position" name="position" type="hidden" onchange="updateRowUI(\'widgetRow_' + guid + '\')" value="' + position + '">' +
        '</form>' +
        '</div>' +
        '</em>' +
        '<em><a onclick="removeRow(\'#widgetRow_' + guid + '\')">Delete</a></em>' +
        '</div>' +
        '</div>' +
        '</span>' +
        '</header>';

    var button = $('<button>').addClass('button').attr({
        onclick: 'addColumn(this);return false;'
    }).html('Add Column');

    var input = $('<input>')
        .addClass('widgetRowInput')
        .attr({
            name: 'row_' + guid,
            value: 'row_' + guid,
            id: 'input_row_' + guid,
            type: 'hidden'
        });

    var div = $('<div>')
        .addClass('grid_3')
        .append(input)
        .append(button);

    var widgetColsWrapper = $('<div>')
        .addClass('widgetColsWrapper');

    var row = $('<li>')
        .addClass('row widgetRow widgetBox')
        .attr('id', 'widgetRow_' + guid)
        .append(header_tpl)
        .append(widgetColsWrapper)
        .append(div);

    $(e).closest('div').find('ul.widgetRowsWrapper').append(row);

    rowSortableUI();
    saveRow('widgetRow_' + guid);
    setOrderRows();
}

function setOrderRows() {

}

function saveRow(id) {
    var data = {};

    data.position = $('#' + id).closest('.widgetRowsWrapper').data('position');
    data.row_id = id;
    data.object_type = window.ot;
    data.object_id = window.oid;
    data.order = $('#' + id).index();
    data.settings = $('#' + id + '_config_form').serialize();
    data.landing_page = getUrlVars()['landing_page'];

    data.landing_page = typeof data.landing_page !== 'undefined' ? data.landing_page : 'all';

    $.post(createAdminUrl('style/widget/saverow'), data).done(function (resp) {

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
        start: function (event, ui) {
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
        receive: function (event, ui) {
            data.target = $(this).data('position');
            sortRows(data.source, data.target);
        },
        update: function (event, ui) {
            data.target = $(this).data('position');
            sortRows(data.source, data.target);
        }
    });
}

function removeRow(id) {
    if (confirm('Esta seguro de eliminar este contenedor?')) {
        $(id).remove();
        $.getJSON(createAdminUrl('style/widget/deleterow'), {
            row_id: id
        });
    }
}

function removeColumn(id) {
    if (confirm('Esta seguro de eliminar esta columna?')) {
        $(id).remove();
        $.getJSON(createAdminUrl('style/widget/deletecolumn'), {
            col_id: id
        });
    }
}

function addColumn(e) {
    var guid = generateGuid();
    var row_id = $(e).closest('li').attr('id');
    var position = $(e).closest('.widgetRowsWrapper').data('position');

    var header_tpl =
        '<header>' +
        '<span>' +
        '<small class="colMove"><i class="fa fa-arrows fa-lg"></i></small>' +
        '<small>widgetColumn_' + guid + '</small>' +
        '<small class="submenu">' +
        '<i class="fa fa-bars"></i>' +
        '<div>' +
        '<em>' +
        '<a class="advanced" href="#widgetColumn_' + guid + '_config">Settings</a>' +
        '<div style="display:none;" id="widgetColumn_' + guid + '_config">' +

        '<form id="widgetColumn_' + guid + '_config_form">' +
        '<div class="row">' +
        '<label for="widgetColumn_' + guid + '_show_in_mobile">Show In Mobiles</label>' +
        '<input id="widgetColumn_' + guid + '_show_in_mobile" name="show_in_mobile" type="checkbox" onchange="updateColUI(\'widgetColumn_' + guid + '\')" checked />' +
        '</div>' +

        '<div class="row">' +
        '<label for="widgetColumn_' + guid + '_show_in_tablet">Show In Tablets</label>' +
        '<input id="widgetColumn_' + guid + '_show_in_tablet" name="show_in_tablet" type="checkbox" onchange="updateColUI(\'widgetColumn_' + guid + '\')" checked />' +
        '</div>' +

        '<div class="row">' +
        '<label for="widgetColumn_' + guid + '_show_in_desktop">Show In Desktops</label>' +
        '<input id="widgetColumn_' + guid + '_show_in_desktop" name="show_in_desktop" type="checkbox" onchange="updateColUI(\'widgetColumn_' + guid + '\')" checked />' +
        '</div>' +

        '<div class="row">' +
        '<label for="widgetColumn_' + guid + '_show_in_facebook">Show In Facebook</label>' +
        '<input id="widgetColumn_' + guid + '_show_in_facebook" name="show_in_facebook" type="checkbox" onchange="updateColUI(\'widgetColumn_' + guid + '\')" checked />' +
        '</div>' +

        '<div class="row">' +
        '<label for="widgetColumn_' + guid + '_async">Async / Ajax</label>' +
        '<input id="widgetColumn_' + guid + '_async" name="async" type="checkbox" onchange="updateColUI(\'widgetColumn_' + guid + '\')" checked />' +
        '</div>' +

        '<div class="row">' +
        '<label for="widgetColumn_' + guid + '_grid_large">Grid Large</label>' +
        '<select id="widgetColumn_' + guid + '_grid_large" name="grid_large" onchange="updateColUI(\'widgetColumn_' + guid + '\')">' +
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
        '</select>' +
        '</div>' +

        '<div class="row">' +
        '<label for="widgetColumn_' + guid + '_grid_medium">Grid Medium</label>' +
        '<select id="widgetColumn_' + guid + '_grid_medium" name="grid_medium" onchange="updateColUI(\'widgetColumn_' + guid + '\')">' +
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
        '</select>' +
        '</div>' +

        '<div class="row">' +
        '<label for="widgetColumn_' + guid + '_grid_small">Grid Small</label>' +
        '<select id="widgetColumn_' + guid + '_grid_small" name="grid_small" onchange="updateColUI(\'widgetColumn_' + guid + '\')">' +
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
        '</select>' +
        '</div>' +

        '<div class="row">' +
            '<label>Customer Session Mode</label>' +
            '<select name="customer_session_mode" onchange="updateColUI(\'widgetColumn_'+ guid +'\')">' +
                '<option value="any">Any</option>' +
                '<option value="logon">Needs to be Log On</option>' +
                '<option value="logoff">Needs to be Log Off</option>' +
            '</select>' +
        '</div>' +

        '<div class="row">' +
            '<label>Logic Conditions Action</label>' +
            '<select name="conditional_logic_action" onchange="updateColUI(\'widgetColumn_'+ guid +'\')">' +
                '<option value="show">Show</option>' +
                '<option value="hide">Hide</option>' +
            '</select>' +
        '</div>' +
        
        '<div class="row">' +
            '<label>When Route contains</label>' +
            '<input type="text" name="conditional_logic_when_route_contains" onchange="updateColUI(\'widgetColumn_'+ guid +'\')" />' +
        '</div>' +

        '<input id="widgetColumn_' + guid + '_position" name="position" type="hidden" onchange="updateColUI(\'widgetColumn_' + guid + '\')" value="' + position + '">' +

        '</form>' +
        '</div>' +
        '</em>' +
        '<em><a onclick="removeColumn(\'#widgetColumn_' + guid + '\')">Delete</a></em>' +
        '</div>' +
        '</small>' +
        '</span>' +
        '</header>';

    var ul = $('<ul>')
        .addClass('widgetWrapper ui-sortable')
        .attr('id', 'widgetColumn_ul_' + guid);

    var input = $('<input>')
        .addClass('widgetColumnInput')
        .attr({
            name: 'column_' + guid,
            value: row_id + ':column_' + guid,
            id: 'input_column_' + guid,
            type: 'hidden'
        });

    var column = $('<div>')
        .addClass('grid_12 widgetColumn widgetBox')
        .attr('id', 'widgetColumn_' + guid)
        .attr('data-grid-small', 'grid_12')
        .attr('data-grid-medium', 'grid_12')
        .attr('data-grid-large', 'grid_12')
        .append(header_tpl)
        .append(ul)
        .append(input);

    $(e).closest('li').find('.widgetColsWrapper').append(column);

    colSortableUI();
    initDragNDrop();
    saveCol('widgetColumn_' + guid);
    return false;
}

function saveCol(id) {
    var data = {};

    data.position = $('#' + id).closest('.widgetRowsWrapper').data('position');
    data.row_id = $('#' + id).closest('.widgetRow').attr('id');
    data.col_id = id;
    data.object_type = window.ot;
    data.object_id = window.oid;
    data.order = $('#' + id).index();
    data.settings = $('#' + id + '_config_form').serialize();
    data.landing_page = getUrlVars()['landing_page'];

    data.landing_page = typeof data.landing_page !== 'undefined' ? data.landing_page : 'all';

    $.post(createAdminUrl('style/widget/savecol'), data).done(function (resp) {

    });
}

function updateColUI(id) {
    var data = {};

    $('#' + id)
        .removeClass('grid_' + $('#' + id).data('grid-large'))
        .addClass('grid_' + $('#' + id + '_grid_large').val());

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
        start: function (event, ui) {
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
        receive: function (event, ui) {
            data.target = $(this).closest('.widgetRowsWrapper').data('position');
            sortCols(data.source, data.target);
        },
        update: function (event, ui) {
            data.target = $(this).closest('.widgetRowsWrapper').data('position');
            sortCols(data.source, data.target);
        }
    });
}

function generateGuid() {
    var S4 = function () {
        return (((1 + Math.random()) * 0x10000) | 0).toString(16).substring(1);
    };
    return (S4() + S4() + S4());
}

function loadNtWidgets(widget) {
    if (typeof widget.extension == 'undefined' ||
        typeof widget.position == 'undefined' ||
        typeof widget.name == 'undefined' ||
        typeof widget.order == 'undefined') {
        return false;
    }

    $.ajaxQueue({
        url: createAdminUrl("module/" + widget.extension + "/widget",
            "w=1"+
            '&ot='+ window.ot+
            '&oid='+ window.oid
        ),
        dataType: "json",
        data: widget
    }).done(function (data) {
        $('#' + widget.name + '_attributes').html(data.html);
        $('#' + widget.name + '_form').append('<input type="hidden" name="Widgets[' + widget.name + '][position]" value="' + widget.position + '" /><input type="hidden" name="Widgets[' + widget.name + '][order]" value="' + widget.order + '" /><input type="hidden" name="Widgets[' + widget.name + '][name]" value="' + widget.name + '" />');
        $('.widgetWrapper').find("input, select, textarea, p")
            .bind('mousedown.ui-disableSelection selectstart.ui-disableSelection', function (e) {
                e.stopImmediatePropagation();
            });
        $('#' + widget.name + '').find('input, select, textarea').on('change', function (event) {
            $('.saving').remove();
            $('#' + widget.name + '_form').before('<img src="' + window.nt.http_admin_image + 'small_loading.gif" class="saving" />');
            $.post(createAdminUrl("module/" + widget.extension + "/widget", {
                    name: widget.name,
                    order: widget.order,
                    position: widget.position
                }),
                $('#' + widget.name + '_form').serialize(),
                function (respons) {
                    $('.saving').remove();
                    resp = $.parseJSON(respons);
                });
        });
    });
}