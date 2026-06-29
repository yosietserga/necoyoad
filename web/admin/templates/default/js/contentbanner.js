$(function(){
    if (getUrlVars()['id']) {
        resetUI();
        initiWidgetVars();
        initWidgetUI();

        $('[data-slider-settings] .htabs2 .htab2').on('click',function() {
            $('.htab2').each(function(){
                $($(this).attr('tab')).hide();
                $(this).removeClass('selected');
            });
            $(this).addClass('selected');
            $($(this).attr('tab')).show();
        });
        $('[data-slider-settings] .htabs2 .htab2:first-child').trigger('click');
    }
});

function addImage(files, path) {
    _row = ($('#items li:last-child').index() + 1);
    for(let i=0; files.length>i; i++) {
        addItem();
        $('#preview'+_row).attr( 'src', path + files[i] );
        $('#image'+_row).val( 'data/'+ files[i] );
        _row++;
    }
}

function loadSlideData(banner_id, banner_item_id, data) {
    if (!data) return false;
    /**
    render background field 
    render transitions fields
    load widgets async always
    **/

    /** fill form **/
    if (!data.image) {
        data.image = 'no_image.jpg';
    }
    $('#image').val( data.image ).trigger('change');

    $('#preview').attr({
        src: window.nt.http_catalog +'index.php?r=common/home/getimage&image='+ data.image +'&width=100'+'&height=100'
    });

    if (data.properties) {
        $.each(data.properties, function(k,v){
            if ($('input[name="properties['+ k +']"]').length > 0) {
                $('input[name="properties['+ k +']"]').val( v );
            }
        });
        unlockFormUI();
    }

    $('#banner_id').val( banner_id );
    $('#banner_item_id').val( banner_item_id );
    window['slideSettings_'+ banner_item_id] = data;

    /** load widgets **/
    $.getJSON(createAdminUrl('api/v1/widgets',
    { 
        object_id:banner_item_id,
        object_type:'banner_item'
    })).done(function(resp) {
        $.each(resp.payload.results, function(k, v) {
            let div = addPointer(v.settings.offsetX, v.settings.offsetY);
            div.attr({
                'data-widget':v.extension,
                id:v.name
            });

            $('[data-widget]').on('dragstart', onDragHandler); 

            $.ajaxQueue({
                url: createAdminUrl('module/'+ v.extension +'/widget'),
                dataType: "json",
                data:{
                    'w':1,
                    'name':v.name
                }
            }).done(function( response ) {
                if (typeof response.html != 'undefined') {
                    let a = $('<a>')
                            .addClass('advanced')
                            .css({
                                position:   'absolute',
                                top:        '0', 
                                background: 'transparent',
                                borderRadius:'20px',
                                width:      '20px',
                                height:     '20px',
                                display:    'block'
                            })
                            .attr({
                                href: '#'+ v.name +'_attributes'
                            });

                    let d = $('<div>')
                            .addClass('attributes')
                            .attr({
                                id: v.name +'_attributes'
                            })
                            .append(response.html)
                            .append(data.inputs);

                    div.append( a ).append( d );

                    var height = $(window).height() * 1.9;
                    var width = $(window).width() * 0.9;

                    $('#'+ v.name +' a.advanced').fancybox({
                        maxWidth    : width,
                        maxHeight   : height,
                        fitToView   : false,
                        width   : '90%',
                        height  : '90%',
                        autoSize    : false,
                        closeClick  : false,
                        openEffect  : 'none',
                        closeEffect : 'none'
                    });

                    $('#'+ v.name).find('input, select, textarea').on('change',function(event){
                        saveWidget(v.name, v.extension);
                    });
                }

                div.on('dblclick', function() {
                    if (confirm('Are you sure you want to delete this?')) {
                        $.getJSON(createAdminUrl('style/widget/delete', 'name='+ v.name));
                        div.remove();
                    }
                });
            });
        });
    });
}

function loadSlideSettings(banner_id, banner_item_id, li) {
    lockFormUI();
    resetUI();

    if (!window['slideSettings_'+ banner_item_id]) {
        $.getJSON(createAdminUrl('api/v1/banner_items',
        { 
            id:banner_id,
            banner_item_id:banner_item_id
        })).done(function(resp) {
            var data = resp.payload.results[0];
            loadSlideData(banner_id, banner_item_id, data);
        });
    } else {
        loadSlideData(banner_id, banner_item_id, window['slideSettings_'+ banner_item_id]);
    }

    var $li = $(li);
    $('#SlideNameInput').val( $li.find('a').text().trim() ).off('keyup').on('keyup', function() {
        var children = $li.find('a').children();
        $li.find('a').text( $(this).val() ).append( children );
    });
}

function lockFormUI() {
    $('#slide_form_background').attr({
        'data-lock-form':1
    });
}

function unlockFormUI() {
    $('#slide_form_background').removeAttr('data-lock-form');
}

function updateSlide(banner_id, banner_item_id) {
    if ($('#slide_form_background').attr('data-lock-form')) return;

    data = {};
    data['properties'] = {};

    $('[name^="properties"]').each(function(){
        var k = $(this).attr('name').replace('properties[','').replace(']','');
        var v = $(this).val();
        data['properties'][k] = v;
    });
    
    data['image'] = $('#image').val();

    if (banner_id && banner_item_id) {
        $.post(createAdminUrl('api/v1/banner_items',
            { 
                id:banner_id,
                banner_item_id:banner_item_id
            }),
            data
        );

        window['slideSettings_'+ banner_item_id] = data;
    }
}

function dropping(e) {
    e.preventDefault();
}

function resetUI() {
    window['slideConfig'] = null;
    $('[data-widget]').on('dragstart', onDragHandler);            
    $('#slide_background').on('dragover', onDragOverHandler).on('drop', onDropHandler);


    $('[name^="properties"]').each(function(){
        $(this).val('').removeAttr('checked').find('option').removeAttr('selected');
    });
    
    image_delete('image', 'preview');

    $('.mapPointer').remove();
}

function removeSlide(row_id) {
    var id = $('#'+ row_id).data('banner_item_id');
    $('#'+ row_id).remove();
    $.getJSON(createAdminUrl('content/banner/deleteitem'), { id:id });
    resetUI();
}

function addRow(button) {
    var _row = ( $('.vtab').last().index() + 1 );
    var banner_id = getUrlVars()['id'];
    var html  = '';
    
    var li = $('<li>').attr({
        id:'slide_'+ _row,
        class:'vtab'
    }).html(
        '<a onclick="return false;" href="#">'+
            'Slide '+ _row +   
            '<span onclick="removeSlide(\'slide_'+ _row +'\')" class="remove">&nbsp;</span>'+
        '</a>'
    );

    $(button).before(li);

    $.getJSON(createAdminUrl('content/banner/saveItem'), { id:banner_id })
    .done(function(resp) {
        li.attr({
            onclick:'loadSlideSettings("'+ banner_id +'", "'+ resp.banner_item_id +'", this)',
            'data-banner_id':banner_id,
            'data-banner_item_id':resp.banner_item_id
        });
    });
}

function addPointer(posX, posY) {
    return $('<div>').html('&nbsp;').css({
        background: '#900',
        border:     'solid 3px #fff',
        boxShadow:  '0px 0px 10px #000',
        borderRadius:'20px',
        position:   'absolute',
        top:        posY,
        left:       posX,
        width:      '20px',
        height:     '20px',
        display:    'block'
    }).attr({
        class:'mapPointer',
        draggable:true
    }).appendTo('#slide_background');

}

function onDragHandler(e) {
    e.originalEvent.dataTransfer.effectAllowed = "move";
    e.originalEvent.dataTransfer.setData( "text", $(this).data('widget') );

    window['widgetModule'] = $(this).data('widget');
    window['widgetId'] = $(this).attr('id');
}

function onDropHandler(e) {
    e.preventDefault();

    if (window['widgetModule']) {
        var that = this;
        var posX = parseInt( ( e.originalEvent.offsetX - 12 ) * 100 / $(that).innerWidth() ) +'%';
        var posY = parseInt( ( e.originalEvent.offsetY - 9 ) * 100 / $(that).innerHeight() ) +'%';

        if (!window['widgetId']) {
            $(that).css({
                position: 'relative'
            });

            var data = {};

            data.extension = e.originalEvent.dataTransfer.getData( "text" );
            data.id = "widget_" + data.extension + "_" + rand();
            data.inputs = '<input class="widgetName" type="hidden" name="Widgets[' + data.id + '][name]" id="' + data.id + '_name" value="' + data.id + '" />';

            var div = addPointer(posX, posY);

            div.attr({
                'data-widget':data.extension,
                id:data.id
            });

            $('[data-widget]').on('dragstart', onDragHandler); 

            var banner_item_id = $('#banner_item_id').val();

            $.ajaxQueue({
                url: createAdminUrl('module/'+ data.extension +'/widget', 'store_id=0&landing_page=all'),
                dataType: "json",
                data:{
                    'offsetY':posY,
                    'offsetX':posX,
                    'ot':'banner_item',
                    'oid':banner_item_id,
                    'extension':data.extension,
                    'order':0,
                    'position':'main',
                    'store_id':0,
                    'name':data.id
                }
            }).done(function( response ) {
                if (typeof response.html != 'undefined') {
                    let a = $('<a>')
                            .addClass('advanced')
                            .css({
                                position:   'absolute',
                                top:        '0', 
                                background: 'transparent',
                                borderRadius:'20px',
                                width:      '20px',
                                height:     '20px',
                                display:    'block'
                            })
                            .attr({
                                href: '#'+ data.id +'_attributes'
                            })
                            .on('dblclick', function() {
                                if (confirm('Are you sure you want to delete this?')) {
                                    $.getJSON(createAdminUrl('style/widget/delete', 'name='+ data.id));
                                    div.remove();
                                }
                            });

                    div.append( a );

                    div.append( 
                        $('<div>')
                            .addClass('attributes')
                            .attr({
                                id: data.id +'_attributes'
                            })
                            .append(response.html)
                            .append(data.inputs)
                    );

                    var height = $(window).height() * 1.9;
                    var width = $(window).width() * 0.9;

                    $('#'+ data.id +' a.advanced').fancybox({
                        maxWidth    : width,
                        maxHeight   : height,
                        fitToView   : false,
                        width   : '90%',
                        height  : '90%',
                        autoSize    : false,
                        closeClick  : false,
                        openEffect  : 'none',
                        closeEffect : 'none'
                    });

                    var widgetModule = data.extension;
                    var widgetName = data.id;

                    saveWidget(widgetName, widgetModule);

                    $('#'+ data.id).find('input, select, textarea').on('change',function(event){
                        saveWidget(widgetName, widgetModule);
                    });
                }
            });
        } else {
            $('#'+ window['widgetId']).css({
                top:        posY,
                left:       posX
            });
            
            $('input[name="Widgets['+ window['widgetId'] +'][settings][offsetY]"]').val( posY );
            $('input[name="Widgets['+ window['widgetId'] +'][settings][offsetX]"]').val( posX );

            saveWidget(window['widgetId'], window['widgetModule']);
        }
        window['widgetModule'] = null;
        window['widgetId'] = null;
    }
}

function onDragOverHandler(e) {
    e.preventDefault();
}

function unserialize (data) {
  var $global = (typeof window !== 'undefined' ? window : global);

  var utf8Overhead = function (str) {
    var s = str.length
    for (var i = str.length - 1; i >= 0; i--) {
      var code = str.charCodeAt(i)
      if (code > 0x7f && code <= 0x7ff) {
        s++
      } else if (code > 0x7ff && code <= 0xffff) {
        s += 2
      }
      // trail surrogate
      if (code >= 0xDC00 && code <= 0xDFFF) {
        i--
      }
    }
    return s - 1
  };
  
  var error = function (type,
    msg, filename, line) {
    throw new $global[type](msg, filename, line);
  };

  var readUntil = function (data, offset, stopchr) {
    var i = 2
    var buf = []
    var chr = data.slice(offset, offset + 1)

    while (chr !== stopchr) {
      if ((i + offset) > data.length) {
        error('Error', 'Invalid')
      }
      buf.push(chr)
      chr = data.slice(offset + (i - 1), offset + i)
      i += 1
    }
    return [buf.length, buf.join('')]
  }
  var readChrs = function (data, offset, length) {
    var i, chr, buf

    buf = []
    for (i = 0; i < length; i++) {
      chr = data.slice(offset + (i - 1), offset + i)
      buf.push(chr)
      length -= utf8Overhead(chr)
    }
    return [buf.length, buf.join('')]
  }
  function _unserialize (data, offset) {
    var dtype
    var dataoffset
    var keyandchrs
    var keys
    var contig
    var length
    var array
    var readdata
    var readData
    var ccount
    var stringlength
    var i
    var key
    var kprops
    var kchrs
    var vprops
    var vchrs
    var value
    var chrs = 0
    var typeconvert = function (x) {
      return x
    }

    if (!offset) {
      offset = 0
    }
    dtype = (data.slice(offset, offset + 1)).toLowerCase()

    dataoffset = offset + 2

    switch (dtype) {
      case 'i':
        typeconvert = function (x) {
          return parseInt(x, 10)
        }
        readData = readUntil(data, dataoffset, ';')
        chrs = readData[0]
        readdata = readData[1]
        dataoffset += chrs + 1
        break
      case 'b':
        typeconvert = function (x) {
          return parseInt(x, 10) !== 0
        }
        readData = readUntil(data, dataoffset, ';')
        chrs = readData[0]
        readdata = readData[1]
        dataoffset += chrs + 1
        break
      case 'd':
        typeconvert = function (x) {
          return parseFloat(x)
        }
        readData = readUntil(data, dataoffset, ';')
        chrs = readData[0]
        readdata = readData[1]
        dataoffset += chrs + 1
        break
      case 'n':
        readdata = null
        break
      case 's':
        ccount = readUntil(data, dataoffset, ':')
        chrs = ccount[0]
        stringlength = ccount[1]
        dataoffset += chrs + 2

        readData = readChrs(data, dataoffset + 1, parseInt(stringlength, 10))
        chrs = readData[0]
        readdata = readData[1]
        dataoffset += chrs + 2
        if (chrs !== parseInt(stringlength, 10) && chrs !== readdata.length) {
          error('SyntaxError', 'String length mismatch')
        }
        break
      case 'a':
        readdata = {}

        keyandchrs = readUntil(data, dataoffset, ':')
        chrs = keyandchrs[0]
        keys = keyandchrs[1]
        dataoffset += chrs + 2

        length = parseInt(keys, 10)
        contig = true

        for (i = 0; i < length; i++) {
          kprops = _unserialize(data, dataoffset)
          kchrs = kprops[1]
          key = kprops[2]
          dataoffset += kchrs

          vprops = _unserialize(data, dataoffset)
          vchrs = vprops[1]
          value = vprops[2]
          dataoffset += vchrs

          if (key !== i) {
            contig = false
          }

          readdata[key] = value
        }

        if (contig) {
          array = new Array(length)
          for (i = 0; i < length; i++) {
            array[i] = readdata[i]
          }
          readdata = array
        }

        dataoffset += 1
        break
      default:
        error('SyntaxError', 'Unknown / Unhandled data type(s): ' + dtype)
        break
    }
    return [dtype, dataoffset - offset, typeconvert(readdata)]
  }

  return _unserialize((data + ''), 0)[2]
}