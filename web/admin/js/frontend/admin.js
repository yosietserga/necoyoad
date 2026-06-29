if(!$.fn.ajaxQueue)  renderJSLink( 'vendor/jquery.ajaxqueue.min.js' );
if(!$.fn.ace)  renderJSLink( 'vendor/ace/src-min/ace.js' );
if(!$.fn.fancybox)  renderJSLink( 'vendor/jquery.fancybox.pack.js' );
if(!$.fn.chosen)  renderJSLink( 'vendor/jquery.chosen/chosen.jquery.min.js' );
if(!$.fn.ui)  renderJSLink( 'vendor/jquery-ui.min.js' );
if(typeof CKEDITOR == 'undefined') renderJSLink( 'vendor/ckeditor/ckeditor.js' );
if(!getUrlVars()['theme_id']) renderJSLink( 'functions/widgets.js' );


let data = {};

$(function(){

    if (getUrlVars()['admin_tools']) {
        $('.panel-lateral-tab:not(:first-child)').hide();
        $('[data-tab]').on('click', function(){
            $('.panel-lateral-tab').hide();
            $('#'+ $(this).data('tab')).show();
        });

        $('body').addClass('admin_tools');
        
        var currentMarginLeft = 0;
        var currentWidth = 0;

        addAdminControls();

        $('#showAdminPanel').sidr({
            displace: false,
            onOpen: function() {
                currentMarginLeft = $("html").css("marginLeft");
                currentWidth = $("html").width();
                $("html").css({
                    "marginLeft":300 +'px',
                    width: (currentWidth - 300) +'px'
                });
            },
            onClose: function() {
                $("html").css({
                    "marginLeft":currentMarginLeft,
                    width: currentWidth +'px'
                });
            }
        });

        $('window').on('resize', function(){
            currentMarginLeft = $("body").css("marginLeft");
            $.sidr('close', 'sidr');
        });

        $('.panel-lateral-tab').hide();
        $('#tabWidgetConfigurator').show();
        $('.panel-lateral-tabs span').on('click', function(){
            $('.panel-lateral-tab').hide();
            $('#'+ $(this).data('tab')).show();
        });

        parent.setStyle = setStyle;
        parent.resetPanel = resetPanel;
        parent.resetBackground = resetBackground;
        parent.resetMargin = resetMargin;
        parent.resetDimensions = resetDimensions;
        parent.resetPadding = resetPadding;
        parent.resetBorder = resetBorder;
        parent.resetBorderRadius = resetBorderRadius;
        parent.resetBoxShadow = resetBoxShadow;
        parent.saveStyle = saveStyle;
        parent.copyStyle = copyStyle;
        parent.pasteStyle = pasteStyle;
        parent.cleanStyle = cleanStyle;

        initSortable( false );
    }
});

/**
 * Reconoce todos los elementos administrables y le asigna los botones de las acciones
 *
 * @param elements object con los elementos a administrar
 * @param areChildrens boolean si los elementos pasados son hijos de otro
 * @return void.
 */
function addAdminControls(el) {
    if (typeof el !== 'undefined') {
        _addAdminControls(el);
    } else {
        $('.nt-editable, [nt-editable]').each(function () {
            _addAdminControls(this);
        });
    }
}

function _addAdminControls(el) {
    var that = $(el);

    if (that.hasClass('administrable')) {
        return true;
    }

    if (!that.attr('id') || that.attr('id').length == 0) {
        that.attr('id', 'widget-' + getParentId(that) + '-' + el.tagName.toLowerCase() + '-' + that.index());
    }

    var html = "";
    html += '<div class="actions actions">';
    
    html += '<a class="admin-icons style" onclick="renderPanels(\'#' + that.attr('id') + '\');return false;"></a>';
    
    if (that.attr('configurable')) {
        html += '<a class="admin-icons config"></a>';
    }

    if (that.attr('movable')) {
        html += '<a class="admin-icons move"></a>';
    }

    if (that.attr('removable')) {
        html += '<a class="admin-icons delete" onclick="deleteWidget(this);return false;"></a>';
    }

    /*  */
    html += '</div>';

    that.addClass('administrable').prepend(html);
    _bindEventsAddminControls();

    loadWidgets(that.attr('data-necotienda_module'), that.attr('data-widget'), that.attr('data-landing_page'), 1);
}

function _bindEventsAddminControls() {
    $('.administrable')
        .off('mouseover')
        .off('mouseout')
        .on('mouseover', function(e) {
            $('.actions').hide();
            let trigger = $(e.target).closest('.administrable');
            if (e.currentTarget === this || trigger === e.currentTarget)
                $(trigger).find('> .actions').show();
            
        })
        .on('mouseout', function(e) {
            $('.actions').hide();
        });
}

function renderPanels(el, mainEl) {
    parent.$('a[tab="#cssEditorLeftPanel"]').trigger('click');

    if (typeof el == 'undefined') return false;

    if (typeof mainEl == 'undefined' || mainEl.length == 0) mainEl = el;
    
    parent.$('#selector').val( el );
    parent.$('#mainselector').val( mainEl );
    parent.$('#mainselector').off( 'change' ).on( 'change', function(){
        renderPanels( this.value );
    });

    var cookieName = "";
    cookieName = parent.$('#selector').val().replace(/\s/g, '');
    cookieName = parent.$('#selector').val().replace(/[,#\.]/g, '');
    $.jStorage.set('currentBlock', cookieName);

    loadStyle();
    renderPanel();
}

function loadStyle() {
    var elements = {};
    elements = $.jStorage.get('elements', false);
    if (elements) {
        $.each(elements, function (el, style) {
            that = parent.$('#' + el);

            if (IsNumber(style.font.size)) {style.font.size += 'px';}
            if (IsNumber(style.font.letterspacing)) {style.font.letterspacing += 'px';}
            if (IsNumber(style.font.wordspacing)) {style.font.wordspacing += 'px';}
            if (IsNumber(style.font.lineheight)) {style.font.lineheight += 'px';}

            if (IsNumber(style.boxshadow.x)) {style.boxshadow.x = parseInt(style.boxshadow.x) + 'px ';}
            if (IsNumber(style.boxshadow.y)) {style.boxshadow.y = parseInt(style.boxshadow.y) + 'px ';}
            if (IsNumber(style.boxshadow.blur)) {style.boxshadow.blur = parseInt(style.boxshadow.blur) + 'px ';}
            if (IsNumber(style.boxshadow.spread)) {style.boxshadow.spread = parseInt(style.boxshadow.spread) + 'px ';}

            if (IsNumber(style.margin.all)) {style.margin.all += 'px';}
            if (IsNumber(style.margin.top)) {style.margin.top += 'px';}
            if (IsNumber(style.margin.right)) {style.margin.right += 'px';}
            if (IsNumber(style.margin.bottom)) {style.margin.bottom += 'px';}
            if (IsNumber(style.margin.left)) {style.margin.left += 'px';}

            if (IsNumber(style.padding.all)) {style.padding.all += 'px';}
            if (IsNumber(style.padding.top)) {style.padding.top += 'px';}
            if (IsNumber(style.padding.right)) {style.padding.right += 'px';}
            if (IsNumber(style.padding.bottom)) {style.padding.bottom += 'px';}
            if (IsNumber(style.padding.left)) {style.padding.left += 'px';}
            
            if (style.background.image) {
                that.css({
                    'backgroundImage': 'url(' + style.background.image + ')'
                });
            }
            if (style.background.color) {
                that.css({
                    'backgroundColor': style.background.color
                });
            }
            if (style.background.repeat) {
                that.css({
                    'backgroundRepeat': style.background.repeat
                });
            }
            if (style.background.attachment) {
                that.css({
                    'backgroundAttachment': style.background.attachment
                });
            }
            if (IsNumber(style.background.positionX) && IsNumber(style.background.positionY)) {
                that.css({
                    'backgroundPosition': style.background.positionX + ' ' + style.background.positionY
                });
            } else if (IsNumber(style.background.positionX)) {
                that.css({
                    'backgroundPosition': style.background.positionX + ' 0%'
                });
            } else if (IsNumber(style.background.positionY)) {
                that.css({
                    'backgroundPosition': '0% ' + style.background.positionY
                });
            }

            if (IsNumber(style.dimensions.top)) {
                that.css({
                    'top': style.dimensions.top
                });
            }
            if (IsNumber(style.dimensions.left)) {
                that.css({
                    'left': style.dimensions.left
                });
            }
            if (IsNumber(style.dimensions.width)) {
                that.css({
                    'width': style.dimensions.width
                });
            }
            if (IsNumber(style.dimensions.height)) {
                that.css({
                    'height': style.dimensions.height
                });
            }
            if (style.dimensions.float) {
                that.css({
                    'float': style.dimensions.float
                });
            }
            if (style.dimensions.position) {
                that.css({
                    'float': style.dimensions.position
                });
            }
            if (style.dimensions.overflow) {
                that.css({
                    'float': style.dimensions.overflow
                });
            }

            that.css({
                'fontColor': style.font.color,
                'fontFamily': style.font.family,
                'fontWeight': style.font.weight,
                'fontStyle': style.font.style,
                'fontSize': style.font.size,
                'textAlign': style.font.align,
                'textDecoration': style.font.decoration,
                'textTransform': style.font.transform,
                'letterSpacing': style.font.letterspacing,
                'wordSpacing': style.font.wordspacing,
                'lineHeight': style.font.lineheight
            });

            parent.$('#bold').removeClass('boldOn');
            parent.$('#italic').removeClass('italicOn');
            parent.$('#underline').removeClass('underlineOn');
            parent.$('#lineThrough').removeClass('line-throughOn');
            parent.$('#upper').removeClass('uppercaseOn');
            parent.$('#lower').removeClass('lowercaseOn');
            parent.$('#alignLeft').removeClass('align-leftOn');
            parent.$('#alignCenter').removeClass('align-centerOn');
            parent.$('#alignRight').removeClass('align-rightOn');
            parent.$('#alignJustify').removeClass('align-justifyOn');

            if (style.font.weight == 'bold' || style.font.weight == '700') {
                parent.$('#bold').addClass('boldOn');
            } else {
                parent.$('#bold').removeClass('boldOn');
            }
            if (style.font.style == 'italic') {
                parent.$('#italic').addClass('italicOn');
            } else {
                parent.$('#italic').removeClass('italicOn');
            }
            if (style.font.decoration == 'underline') {
                parent.$('#underline').addClass('underlineOn');
            }
            if (style.font.decoration == 'line-through') {
                parent.$('#lineThrough').addClass('line-throughOn');
            }
            if (style.font.transform == 'uppercase') {
                parent.$('#upper').addClass('uppercaseOn');
            }
            if (style.font.transform == 'lowercase') {
                parent.$('#lower').addClass('lowercaseOn');
            }
            if (style.font.align == 'left') {
                parent.$('#alignLeft').addClass('align-leftOn');
            }
            if (style.font.align == 'center') {
                parent.$('#alignCenter').addClass('align-centerOn');
            }
            if (style.font.align == 'right') {
                parent.$('#alignRight').addClass('align-rightOn');
            }
            if (style.font.align == 'justify') {
                parent.$('#alignJustify').addClass('align-justifyOn');
            }

            var cssBoxShadow = "";
            if (style.boxshadow.inset) {
                cssBoxShadow += 'inset ';
            }
            cssBoxShadow += style.boxshadow.x;
            cssBoxShadow += style.boxshadow.y;
            cssBoxShadow += style.boxshadow.blur;
            cssBoxShadow += style.boxshadow.spread;
            cssBoxShadow += style.boxshadow.color;
            $(that).css('boxShadow', cssBoxShadow);

            if ((typeof (style.border.topcolor) == 'undefined' || !style.border.topcolor)
                    && (typeof (style.border.rightcolor) == 'undefined' || !style.border.rightcolor)
                    && (typeof (style.border.bottomcolor) == 'undefined' || !style.border.bottomcolor)
                    && (typeof (style.border.leftcolor) == 'undefined' || !style.border.leftcolor)) {
                that.css({
                    'borderColor': style.border.color,
                    'borderStyle': style.border.style,
                    'borderWidth': style.border.width
                });
            } else {
                that.css({
                    'borderTopColor': style.border.topcolor,
                    'borderTopStyle': style.border.topstyle,
                    'borderTopWidth': style.border.topwidth,
                    'borderRightColor': style.border.rightcolor,
                    'borderRightStyle': style.border.rightstyle,
                    'borderRightWidth': style.border.rightwidth,
                    'borderBottomColor': style.border.botomcolor,
                    'borderBottomStyle': style.border.botomstyle,
                    'borderBottomWidth': style.border.botomwidth,
                    'borderLeftColor': style.border.leftcolor,
                    'borderLeftStyle': style.border.leftstyle,
                    'borderLeftWidth': style.border.leftwidth
                });
            }

            if (!style.borderradius.topleft
                    && !style.borderradius.topright
                    && !style.borderradius.bottomleft
                    && !style.borderradius.bottomright) {
                that.css({
                    'borderRadius': style.borderradius.all
                });
            } else {
                that.css({
                    'borderRadius': style.borderradius.topleft + 'px ' + style.borderradius.topright + 'px ' + style.borderradius.bottomright + 'px ' + style.borderradius.bottomleft + 'px'
                });
            }

            if ((typeof (style.margin.top) == 'undefined' || !style.margin.top)
                    && (typeof (style.margin.right) == 'undefined' || !style.margin.right)
                    && (typeof (style.margin.bottom) == 'undefined' || !style.margin.bottom)
                    && (typeof (style.margin.left) == 'undefined' || !style.margin.left)) {
                that.css({
                    'margin': style.margin.all
                });
            } else {
                that.css({
                    'marginTop': style.margin.top,
                    'marginRight': style.margin.right,
                    'marginBottom': style.margin.bottom,
                    'marginLeft': style.margin.left,
                });
            }

            if ((typeof (style.padding.top) == 'undefined' || !style.padding.top)
                    && (typeof (style.padding.right) == 'undefined' || !style.padding.right)
                    && (typeof (style.padding.bottom) == 'undefined' || !style.padding.bottom)
                    && (typeof (style.padding.left) == 'undefined' || !style.padding.left)) {
                that.css({
                    'padding': style.padding.all
                });
            } else {
                that.css({
                    'paddingTop': style.padding.top,
                    'paddingRight': style.padding.right,
                    'paddingBottom': style.padding.bottom,
                    'paddingLeft': style.padding.left,
                });
            }
        });
    }
}

/*
 renderPanel()
 
 Inicializa los valores de cada propiedad en el panel de control
 que edita las propiedades CSS del elemento seleccionado
 */
function renderPanel() {
    var style = {};
    var cookieName = "";

    parent.$('#selectors').val('null');

    if (!$.jStorage.get('elements', null)) {
        var elements = {};
        $.jStorage.set('elements', elements);
    } else {
        var elements = $.jStorage.get('elements', {});
    }

    cookieName = parent.$('#selector').val().replace(/\s/g, '');
    cookieName = parent.$('#selector').val().replace(/[,#\.]/g, '');

    if (parent.$('#selector').val().length) {
        var currentCss = translateCssProperties($(parent.$('#selector').val()).getStyles());
    }

    /* if (typeof elements[cookieName] == 'undefined') {
     style = false;
     } else */ if (typeof currentCss !== 'undefined' && currentCss) {
        style = currentCss;
    } else {
        style = false;
    }
    resetPanel();

    if (style) {
        if (typeof style.background.color !== 'undefined') {
            parent.$('#backgroundColor').val(style.background.color);
        }
        if (typeof style.background.image !== 'undefined') {
            parent.$('#backgroundImage').val(style.background.image);
        }
        if (typeof style.background.repeat !== 'undefined') {
            parent.$('#backgroundRepeat').val(style.background.repeat);
        }
        if (typeof style.background.positionX !== 'undefined') {
            parent.$('#backgroundPositionX').val(style.background.positionX);
        }
        if (typeof style.background.positionY !== 'undefined') {
            parent.$('#backgroundPositionY').val(style.background.positionY);
        }
        if (typeof style.background.attachment !== 'undefined') {
            parent.$('#backgroundAttachment').attr('checked', 'checked');
        }

        if (typeof style.border.color !== 'undefined') {
            parent.$('#borderColor').val(style.border.color);
        }
        if (typeof style.border.style !== 'undefined') {
            parent.$('#borderStyle').val(style.border.style);
        }
        if (typeof style.border.width !== 'undefined') {
            parent.$('#borderWidth').val(style.border.width);
        }

        if (typeof style.border.topcolor !== 'undefined') {
            parent.$('#borderTopColor').val(style.border.topcolor);
        }
        if (typeof style.border.topstyle !== 'undefined') {
            parent.$('#borderTopStyle').val(style.border.topstyle);
        }
        if (typeof style.border.topwidth !== 'undefined') {
            parent.$('#borderTopWidth').val(style.border.topwidth);
        }

        if (typeof style.border.rightcolor !== 'undefined') {
            parent.$('#borderRightColor').val(style.border.rightcolor);
        }
        if (typeof style.border.rightstyle !== 'undefined') {
            parent.$('#borderRightStyle').val(style.border.rightstyle);
        }
        if (typeof style.border.rightwidth !== 'undefined') {
            parent.$('#borderRightWidth').val(style.border.rightwidth);
        }

        if (typeof style.border.bottomcolor !== 'undefined') {
            parent.$('#borderBottomColor').val(style.border.bottomcolor);
        }
        if (typeof style.border.bottomstyle !== 'undefined') {
            parent.$('#borderBottomStyle').val(style.border.bottomstyle);
        }
        if (typeof style.border.bottomwidth !== 'undefined') {
            parent.$('#borderBottomWidth').val(style.border.bottomwidth);
        }

        if (typeof style.border.leftcolor !== 'undefined') {
            parent.$('#borderLeftColor').val(style.border.leftcolor);
        }
        if (typeof style.border.leftstyle !== 'undefined') {
            parent.$('#borderLeftStyle').val(style.border.leftstyle);
        }
        if (typeof style.border.leftwidth !== 'undefined') {
            parent.$('#borderLeftWidth').val(style.border.leftwidth);
        }

        if (typeof style.borderradius.all !== 'undefined') {
            parent.$('#borderRadius').val(style.borderradius.all);
        }
        if (typeof style.borderradius.topleft !== 'undefined') {
            parent.$('#borderRadiusTopLeft').val(style.borderradius.topleft);
        }
        if (typeof style.borderradius.topright !== 'undefined') {
            parent.$('#borderRadiusTopRight').val(style.borderradius.topright);
        }
        if (typeof style.borderradius.bottomleft !== 'undefined') {
            parent.$('#borderRadiusBottomLeft').val(style.borderradius.bottomleft);
        }
        if (typeof style.borderradius.bottomright !== 'undefined') {
            parent.$('#borderRadiusBottomRight').val(style.borderradius.bottomright);
        }

        if (typeof style.margin.all !== 'undefined') {
            parent.$('#margin').val(style.margin.all);
        }
        if (typeof style.margin.top !== 'undefined') {
            parent.$('#marginTop').val(style.margin.top);
        }
        if (typeof style.margin.right !== 'undefined') {
            parent.$('#marginRight').val(style.margin.right);
        }
        if (typeof style.margin.bottom !== 'undefined') {
            parent.$('#marginBottom').val(style.margin.bottom);
        }
        if (typeof style.margin.left !== 'undefined') {
            parent.$('#marginLeft').val(style.margin.left);
        }

        if (typeof style.padding.all !== 'undefined') {
            parent.$('#padding').val(style.padding.all);
        }
        if (typeof style.padding.top !== 'undefined') {
            parent.$('#paddingTop').val(style.padding.top);
        }
        if (typeof style.padding.right !== 'undefined') {
            parent.$('#paddingRight').val(style.padding.right);
        }
        if (typeof style.padding.bottom !== 'undefined') {
            parent.$('#paddingBottom').val(style.padding.bottom);
        }
        if (typeof style.padding.left !== 'undefined') {
            parent.$('#paddingLeft').val(style.padding.left);
        }

        if (typeof style.dimensions.top !== 'undefined') {
            parent.$('#top').val(style.dimensions.top);
        }
        if (typeof style.dimensions.left !== 'undefined') {
            parent.$('#left').val(style.dimensions.left);
        }
        if (typeof style.dimensions.width !== 'undefined') {
            parent.$('#width').val(style.dimensions.width);
        }
        if (typeof style.dimensions.height !== 'undefined') {
            parent.$('#height').val(style.dimensions.height);
        }
        if (typeof style.dimensions.position !== 'undefined') {
            parent.$('#position').val(style.dimensions.position);
        }
        if (typeof style.dimensions.overflow !== 'undefined') {
            parent.$('#overflow').val(style.dimensions.overflow);
        }
        if (typeof style.dimensions._float !== 'undefined') {
            parent.$('#float').val(style.dimensions._float);
        }

        if (typeof style.boxshadow.color !== 'undefined') {
            parent.$('#boxColor').val(style.boxshadow.color);
        }
        if (typeof style.boxshadow.inset !== 'undefined') {
            parent.$('#boxShadowInset').attr('checked', 'checked');
        }
        if (typeof style.boxshadow.x !== 'undefined') {
            parent.$('#boxShadowX').val(style.boxshadow.x);
        }
        if (typeof style.boxshadow.y !== 'undefined') {
            parent.$('#boxShadowY').val(style.boxshadow.y);
        }
        if (typeof style.boxshadow.blur !== 'undefined') {
            parent.$('#boxShadowBlur').val(style.boxshadow.blur);
        }
        if (typeof style.boxshadow.spread !== 'undefined') {
            parent.$('#boxShadowSpread').val(style.boxshadow.spread);
        }

        if (typeof style.font.color !== 'undefined') {
            parent.$('#fontColor').val(style.font.color);
        }
        if (typeof style.font.family !== 'undefined') {
            parent.$('#fontFamily').val(style.font.family);
        }
        if (typeof style.font.weight !== 'undefined') {
            parent.$('#fontWeight').val(style.font.weight);
        }
        if (typeof style.font.style !== 'undefined') {
            parent.$('#fontStyle').val(style.font.style);
        }
        if (typeof style.font.size !== 'undefined') {
            parent.$('#fontSize').val(style.font.size);
        }
        if (typeof style.font.align !== 'undefined') {
            parent.$('#textAlign').val(style.font.align);
        }
        if (typeof style.font.decoration !== 'undefined') {
            parent.$('#textDecoration').val(style.font.decoration);
        }
        if (typeof style.font.transform !== 'undefined') {
            parent.$('#textTransform').val(style.font.transform);
        }
        if (typeof style.font.letterspacing !== 'undefined') {
            parent.$('#letterSpacing').val(style.font.letterspacing);
        }
        if (typeof style.font.wordspacing !== 'undefined') {
            parent.$('#wordSpacing').val(style.font.wordspacing);
        }
        if (typeof style.font.lineheight !== 'undefined') {
            parent.$('#lineHeight').val(style.font.lineheight);
        }

        if (style.font.weight == 'bold' || style.font.weight == '700') {
            parent.$('#bold').addClass('boldOn');
        }
        if (style.font.style == 'italic') {
            parent.$('#italic').addClass('italicOn');
        }
        if (style.font.decoration == 'underline') {
            parent.$('#underline').addClass('underlineOn');
        }
        if (style.font.decoration == 'line-through') {
            parent.$('#lineThrough').addClass('line-throughOn');
        }
        if (style.font.transform == 'uppercase') {
            parent.$('#upper').addClass('uppercaseOn');
        }
        if (style.font.transform == 'lowercase') {
            parent.$('#lower').addClass('lowercaseOn');
        }
        if (style.font.align == 'left') {
            parent.$('#alignLeft').addClass('align-leftOn');
        }
        if (style.font.align == 'center') {
            parent.$('#alignCenter').addClass('align-centerOn');
        }
        if (style.font.align == 'right') {
            parent.$('#alignRight').addClass('align-rightOn');
        }
        if (style.font.align == 'justify') {
            parent.$('#alignJustify').addClass('align-justifyOn');
        }

        setStyle();
    }

    parent.$('#background-colorpicker').ntColorPicker({
        type: 'rgba',
        change: function (color) {
            if (color.length > 0) {
                parent.$('#backgroundColor').val(color);
                setStyle();
            }
        }
    });
    parent.$('#font-colorpicker').ntColorPicker({
        type: 'hex',
        change: function (color) {
            if (color.length > 0) {
                parent.$('#fontColor').val(color);
                setStyle();
            }
        }
    });
    parent.$('#border-colorpicker').ntColorPicker({
        type: 'hex',
        change: function (color) {
            if (color.length > 0) {
                parent.$('#borderColor').val(color);
                setStyle();
            }
        }
    });
    parent.$('#border_top_colorpicker').ntColorPicker({
        type: 'hex',
        change: function (color) {
            if (color.length > 0) {
                parent.$('#borderTopColor').val(color);
                setStyle();
            }
        }
    });
    parent.$('#border-right-colorpicker').ntColorPicker({
        type: 'hex',
        change: function (color) {
            if (color.length > 0) {
                parent.$('#borderRightColor').val(color);
                setStyle();
            }
        }
    });
    parent.$('#border-bottom-colorpicker').ntColorPicker({
        type: 'hex',
        change: function (color) {
            if (color.length > 0) {
                parent.$('#borderBottomColor').val(color);
                setStyle();
            }
        }
    });
    parent.$('#border-left-colorpicker').ntColorPicker({
        type: 'hex',
        change: function (color) {
            if (color.length > 0) {
                parent.$('#borderLeftColor').val(color);
                setStyle();
            }
        }
    });
    parent.$('#box-colorpicker').ntColorPicker({
        type: 'hex',
        change: function (color) {
            if (color.length > 0) {
                parent.$('#boxColor').val(color);
                setStyle();
            }
        }
    });
}

function translateCssProperties($cssObject) {
    if (typeof $cssObject == 'undefined') {
        return false;
    }
    style = {};
    style.background = {};
    style.border = {};
    style.boxshadow = {};
    style.borderradius = {};
    style.margin = {};
    style.padding = {};
    style.position = {};
    style.dimensions = {};
    style.font = {};

    $.each($cssObject, function (prop, value) {
        if (prop == 'backgroundColor') {
            style.background.color = value;
        }
        if (prop == 'backgroundImage' 
                && value.indexOf('/none') === -1 
                && value.indexOf('/undefined') === -1 
                && value.indexOf('/null') === -1 
                && value.indexOf('theme_editor') === -1
                && value !== 'none'
                && value !== 'undefined'
                && value !== 'null') {
            style.background.image = value.replace('url("','').replace('")','');
        }
        if (prop == 'backgroundRepeat') {
            style.background.repeat = value;
        }
        if (prop == 'backgroundPosition') {
            position = value.split(" ");
            style.background.positionX = position[0]; // calcular de acuerdo al elemento relativo
            style.background.positionY = position[1];
        }
        if (prop == 'backgroundAttachment') {
            style.background.attachment = value;
        }

        if (prop == 'borderColor') {
            style.border.color = value;
        }
        if (prop == 'borderStyle') {
            style.border.style = value;
        }
        if (prop == 'borderWidth') {
            style.border.width = value;
        }

        if (prop == 'borderTopColor') {
            style.border.topcolor = value;
        }
        if (prop == 'borderTopStyle') {
            style.border.topstyle = value;
        }
        if (prop == 'borderTopWidth') {
            style.border.topwidth = value;
        }

        if (prop == 'borderRightColor') {
            style.border.rightcolor = value;
        }
        if (prop == 'borderRightStyle') {
            style.border.rightstyle = value;
        }
        if (prop == 'borderRightWidth') {
            style.border.rightwidth = value;
        }

        if (prop == 'borderBottomColor') {
            style.border.bottomcolor = value;
        }
        if (prop == 'borderBottomStyle') {
            style.border.bottomstyle = value;
        }
        if (prop == 'borderBottomWidth') {
            style.border.bottomwidth = value;
        }

        if (prop == 'borderLeftColor') {
            style.border.leftcolor = value;
        }
        if (prop == 'borderLeftStyle') {
            style.border.leftstyle = value;
        }
        if (prop == 'borderLeftWidth') {
            style.border.leftwidth = value;
        }

        if (prop == 'margin') {
            style.margin.all = value;
        }
        if (prop == 'marginTop') {
            style.margin.top = value;
        }
        if (prop == 'marginRight') {
            style.margin.right = value;
        }
        if (prop == 'marginBottom') {
            style.margin.bottom = value;
        }
        if (prop == 'marginLeft') {
            style.margin.left = value;
        }

        if (prop == 'padding') {
            style.padding.all = value;
        }
        if (prop == 'paddingTop') {
            style.padding.top = value;
        }
        if (prop == 'paddingRight') {
            style.padding.right = value;
        }
        if (prop == 'paddingBottom') {
            style.padding.bottom = value;
        }
        if (prop == 'paddingLeft') {
            style.padding.left = value;
        }

        if (prop == 'width') {
            style.dimensions.width = value;
        }
        if (prop == 'height') {
            style.dimensions.height = value;
        }
        if (prop == 'top') {
            style.dimensions.top = value;
        }
        if (prop == 'left') {
            style.dimensions.left = value;
        }
        if (prop == 'position') {
            style.dimensions.position = value;
        }
        if (prop == 'float') {
            style.dimensions._float = value;
        }
        if (prop == 'overflow') {
            style.dimensions.overflow = value;
        }

        if (prop == 'boxShadow') {

        }

        if (prop == 'fontColor') {
            style.font.color = value;
        }
        if (prop == 'fontFamily') {
            style.font.family = value;
        }
        if (prop == 'fontWeight') {
            style.font.weight = value;
        }
        if (prop == 'fontStyle') {
            style.font.style = value;
        }
        if (prop == 'fontSize') {
            style.font.size = value;
        }
        if (prop == 'textAlign') {
            style.font.align = value;
        }
        if (prop == 'textDecoration') {
            style.font.decoration = value;
        }
        if (prop == 'textTransform') {
            style.font.transform = value;
        }
        if (prop == 'letterSpacing') {
            style.font.letterspacing = value;
        }
        if (prop == 'wordSpacing') {
            style.font.wordspacing = value;
        }
        if (prop == 'lineHeight') {
            style.font.lineheight = value;
        }
    });
    console.log(style);
    return style;
}

function resetPanel() {
    // reiniciamos todos los campos del formulario
    parent.$('#selectors').val('null');
    parent.$('#backgroundCss').val('');
    parent.$('#backgroundColor').val('');
    parent.$('#backgroundImage').val('');
    parent.$('#backgroundRepeat').val('');
    parent.$('#backgroundPositionX').val('');
    parent.$('#backgroundPositionY').val('');
    parent.$('#backgroundAttachment').removeAttr('checked');

    parent.$('#borderColor').val('');
    parent.$('#borderStyle').val('');
    parent.$('#borderWidth').val('');

    parent.$('#borderTopColor').val('');
    parent.$('#borderTopStyle').val('');
    parent.$('#borderTopWidth').val('');

    parent.$('#borderRightColor').val('');
    parent.$('#borderRightStyle').val('');
    parent.$('#borderRightWidth').val('');

    parent.$('#borderBottomColor').val('');
    parent.$('#borderBottomStyle').val('');
    parent.$('#borderBottomWidth').val('');

    parent.$('#borderLeftColor').val('');
    parent.$('#borderLeftStyle').val('');
    parent.$('#borderLeftWidth').val('');

    parent.$('#borderRadius').val('');
    parent.$('#borderRadiusTopLeft').val('');
    parent.$('#borderRadiusTopRight').val('');
    parent.$('#borderRadiusBottomLeft').val('');
    parent.$('#borderRadiusBottomRight').val('');

    parent.$('#margin').val('');
    parent.$('#marginTop').val('');
    parent.$('#marginRight').val('');
    parent.$('#marginBottom').val('');
    parent.$('#marginLeft').val('');

    parent.$('#padding').val('');
    parent.$('#paddingTop').val('');
    parent.$('#paddingRight').val('');
    parent.$('#paddingBottom').val('');
    parent.$('#paddingLeft').val('');

    parent.$('#width').val('');
    parent.$('#height').val('');
    parent.$('#top').val('');
    parent.$('#left').val('');

    parent.$('#boxColor').val('');
    parent.$('#boxShadowInset').removeAttr('checked');
    parent.$('#boxShadowX').val(0);
    parent.$('#boxShadowY').val(0);
    parent.$('#boxShadowBlur').val(0);
    parent.$('#boxShadowSpread').val(0);

    parent.$('#fontColor').val('');
    parent.$('#fontFamily').val('');
    parent.$('#fontWeight').val('');
    parent.$('#fontStyle').val('');
    parent.$('#fontSize').val('');
    parent.$('#textAlign').val('');
    parent.$('#textDecoration').val('');
    parent.$('#textTransform').val('');
    parent.$('#letterSpacing').val('');
    parent.$('#wordSpacing').val('');
    parent.$('#lineHeight').val('');

    // reiniciamos todos los botones y helpers
    parent.$('#bold').removeClass('boldOn');
    parent.$('#italic').removeClass('italicOn');
    parent.$('#underline').removeClass('underlineOn');
    parent.$('#lineThrough').removeClass('line-throughOn');
    parent.$('#alignLeft').removeClass('align-leftOn');
    parent.$('#alignCenter').removeClass('align-centerOn');
    parent.$('#alignRight').removeClass('align-rightOn');
    parent.$('#alignJustify').removeClass('align-justifyOn');
}

/**
 * Establece los estilos del documento o del elemento seleccionado. Utiliza un campo oculto para saber cual elemento esta seleccionado
 *
 * @return void.
 */
function setStyle() {
    var that = $(parent.$('#selector').val()).not('.panel-lateral,.panel-lateral *, #adminTopNav, #adminTopNav *, .actions, .actions *'); /* $(parent.$('#selector').val()).get(0); */
    var cookieName = "";
    var style = {};
    var elements = {};

    if (!$.jStorage.get('elements', null)) {
        $.jStorage.set('elements', elements);
    } else {
        var elements = $.jStorage.get('elements', {});
    }

    cookieName = parent.$('#selector').val().replace(/\s/g, '');
    cookieName = parent.$('#selector').val().replace(/[,#\.]/g, '');

    if (typeof elements[cookieName] == 'undefined') {
        style = {};
    } else {
        style = elements[cookieName];
    }

    style.background = {};
    style.border = {};
    style.boxshadow = {};
    style.borderradius = {};
    style.margin = {};
    style.padding = {};
    style.position = {};
    style.dimensions = {};
    style.font = {};

    _IsNumber('backgroundPositionX');
    _IsNumber('backgroundPositionY');
    _IsNumber('top');
    _IsNumber('left');
    _IsNumber('width');
    _IsNumber('height');
    _IsNumber('fontSize');
    _IsNumber('letterSpacing');
    _IsNumber('wordSpacing');
    _IsNumber('lineHeight');
    _IsNumber('borderWidth');
    _IsNumber('borderTopWidth');
    _IsNumber('borderRightWidth');
    _IsNumber('borderBottomWidth');
    _IsNumber('borderLeftWidth');
    _IsNumber('borderRadius');
    _IsNumber('borderRadiusTopLeft');
    _IsNumber('borderRadiusTopRight');
    _IsNumber('borderRadiusBottomLeft');
    _IsNumber('borderRadiusBottomRight');
    _IsNumber('margin');
    _IsNumber('marginTop');
    _IsNumber('marginRight');
    _IsNumber('marginBottom');
    _IsNumber('marginLeft');
    _IsNumber('padding');
    _IsNumber('paddingTop');
    _IsNumber('paddingRight');
    _IsNumber('paddingBottom');
    _IsNumber('paddingLeft');
    _IsNumber('boxShadowX');
    _IsNumber('boxShadowY');
    _IsNumber('boxShadowBlur');
    _IsNumber('boxShadowSpread');

    // backgrounds
    if (parent.$('#backgroundColor').val().length) {
        style.background.color = parent.$('#backgroundColor').val();
        $(that).css({'backgroundColor': parent.$('#backgroundColor').val()});
    }/*
    if (parent.$('#backgroundImage').val().length) {
        style.background.image = parent.$('#backgroundImage').val();
        $(that).css('backgroundImage', 'url(' + parent.$('#backgroundImage').val() + ')');
    }
    */
        style.background.image = parent.$('#backgroundImage').val();
        $(that).css('backgroundImage', 'url(' + parent.$('#backgroundImage').val() + ')');
    if (parent.$('#backgroundRepeat').val().length) {
        style.background.repeat = parent.$('#backgroundRepeat').val();
        $(that).css('backgroundRepeat', parent.$('#backgroundRepeat').val());
    }
    if (parent.$('#backgroundPositionX').val().length) {
        style.background.positionX = parent.$('#backgroundPositionX').val();
    }
    if (parent.$('#backgroundPositionY').val().length) {
        style.background.positionY = parent.$('#backgroundPositionY').val();
    }
    if (parent.$('#backgroundPositionX').val().length && parent.$('#backgroundPositionY').val().length) {
        $(that).css({'backgroundPosition': parent.$('#backgroundPositionX').val() + ' ' + parent.$('#backgroundPositionY').val()});
    }
    if (parent.$('#backgroundAttachment:checked').length > 0) {
        style.background.attachment = 'fixed';
        $(that).css({'backgroundAttachment': 'fixed'});
    } else {
        style.background.attachment = null;
        $(that).css({'backgroundAttachment': 'scroll'});
    }

    // borders
    if (parent.$('#borderAdvanced').val() == 0) {
        if (parent.$('#borderColor').val().length) {
            style.border.color = parent.$('#borderColor').val();
            $(that).css('borderColor', parent.$('#borderColor').val());
        }
        if (parent.$('#borderStyle').val().length) {
            style.border.style = parent.$('#borderStyle').val();
            $(that).css('borderStyle', parent.$('#borderStyle').val());
        }
        if (parent.$('#borderWidth').val().length) {
            style.border.width = parent.$('#borderWidth').val();
            $(that).css('borderWidth', parent.$('#borderWidth').val());
        }
    } else {
        if (parent.$('#borderTopColor').val().length) {
            style.border.topcolor = parent.$('#borderTopColor').val();
            $(that).css('borderTopColor', parent.$('#borderTopColor').val());
        }
        if (parent.$('#borderTopStyle').val().length) {
            style.border.topstyle = parent.$('#borderTopStyle').val();
            $(that).css('borderTopStyle', parent.$('#borderTopStyle').val());
        }
        if (parent.$('#borderTopWidth').val().length) {
            style.border.topwidth = parent.$('#borderTopWidth').val();
            $(that).css('borderTopWidth', parent.$('#borderTopWidth').val());
        }

        if (parent.$('#borderRightColor').val().length) {
            style.border.rightcolor = parent.$('#borderRightColor').val();
            $(that).css('borderRightColor', parent.$('#borderRightColor').val());
        }
        if (parent.$('#borderRightStyle').val().length) {
            style.border.rightstyle = parent.$('#borderRightStyle').val();
            $(that).css('borderRightStyle', parent.$('#borderRightStyle').val());
        }
        if (parent.$('#borderRightWidth').val().length) {
            style.border.rightwidth = parent.$('#borderRightWidth').val();
            $(that).css('borderRightWidth', parent.$('#borderRightWidth').val());
        }

        if (parent.$('#borderBottomColor').val().length) {
            style.border.bottomcolor = parent.$('#borderBottomColor').val();
            $(that).css('borderBottomColor', parent.$('#borderBottomColor').val());
        }
        if (parent.$('#borderBottomStyle').val().length) {
            style.border.bottomstyle = parent.$('#borderBottomStyle').val();
            $(that).css('borderBottomStyle', parent.$('#borderBottomStyle').val());
        }
        if (parent.$('#borderBottomWidth').val().length) {
            style.border.bottomwidth = parent.$('#borderBottomWidth').val();
            $(that).css('borderBottomWidth', parent.$('#borderBottomWidth').val());
        }

        if (parent.$('#borderLeftColor').val().length) {
            style.border.leftcolor = parent.$('#borderLeftColor').val();
            $(that).css('borderLeftColor', parent.$('#borderLeftColor').val());
        }
        if (parent.$('#borderLeftStyle').val().length) {
            style.border.leftstyle = parent.$('#borderLeftStyle').val();
            $(that).css('borderLeftStyle', parent.$('#borderLeftStyle').val());
        }
        if (parent.$('#borderLeftWidth').val().length) {
            style.border.leftwidth = parent.$('#borderLeftWidth').val();
            $(that).css('borderLeftWidth', parent.$('#borderLeftWidth').val());
        }
    }

    // borderRadius
    if (parent.$('#borderRadiusAdvanced').val() == 0) {
        style.borderradius.all = parent.$('#borderRadius').val();
        $(that).css('borderRadius', parent.$('#borderRadius').val());
    } else {
        var cssBorderRadius = "";

        if (parent.$('#borderRadiusTopLeft').val()) {
            style.borderradius.topleft = parent.$('#borderRadiusTopLeft').val();
            cssBorderRadius += parent.$('#borderRadiusTopLeft').val();
        } else {
            style.borderradius.topleft = 0;
            cssBorderRadius += '0px ';
        }

        if (parent.$('#borderRadiusTopRight').val()) {
            style.borderradius.topright = parent.$('#borderRadiusTopRight').val();
            cssBorderRadius += parent.$('#borderRadiusTopRight').val();
        } else {
            style.borderradius.topright = 0;
            cssBorderRadius += '0px ';
        }

        if (parent.$('#borderRadiusBottomRight').val()) {
            style.borderradius.bottomright = parent.$('#borderRadiusBottomRight').val();
            cssBorderRadius += parent.$('#borderRadiusBottomRight').val();
        } else {
            style.borderradius.bottomright = 0;
            cssBorderRadius += '0px ';
        }

        if (parent.$('#borderRadiusBottomLeft').val()) {
            style.borderradius.bottomleft = parent.$('#borderRadiusBottomLeft').val();
            cssBorderRadius += parent.$('#borderRadiusBottomLeft').val();
        } else {
            style.borderradius.bottomleft = 0;
            cssBorderRadius += '0px ';
        }

        $(that).css({'borderRadius': cssBorderRadius});
    }

    // dimensions and positions
    style.dimensions.width = parent.$('#width').val();
    $(that).css('width', parent.$('#width').val());

    style.dimensions.height = parent.$('#height').val();
    $(that).css('height', parent.$('#height').val());

    style.dimensions.top = parent.$('#top').val();
    $(that).css('top', parent.$('#top').val());

    style.dimensions.left = parent.$('#left').val();
    $(that).css('left', parent.$('#left').val());

    style.dimensions.position = parent.$('#position').val();
    $(that).css('position', parent.$('#position').val());

    // margin
    if (parent.$('#marginAdvanced').val() == 0) {
        style.margin.all = parent.$('#margin').val();
        $(that).css('margin', parent.$('#margin').val());
    } else {
        style.margin.top = parent.$('#marginTop').val();
        $(that).css({'marginTop': parent.$('#marginTop').val()});

        style.margin.right = parent.$('#marginRight').val();
        $(that).css({'marginRight': parent.$('#marginRight').val()});

        style.margin.left = parent.$('#marginLeft').val();
        $(that).css({'marginLeft': parent.$('#marginLeft').val()});

        style.margin.bottom = parent.$('#marginBottom').val();
        $(that).css({'paddingBottom': parent.$('#paddingBottom').val()});
    }

    // padding
    if (parent.$('#paddingAdvanced').val() == 0) {
        style.padding.all = parent.$('#padding').val();
        $(that).css('padding', parent.$('#padding').val());
    } else {
        style.padding.top = parent.$('#paddingTop').val();
        $(that).css({'paddingTop': parent.$('#paddingTop').val()});

        style.padding.right = parent.$('#paddingRight').val();
        $(that).css({'paddingRight': parent.$('#paddingRight').val()});

        style.padding.left = parent.$('#paddingLeft').val();
        $(that).css({'paddingLeft': parent.$('#paddingLeft').val()});

        style.padding.bottom = parent.$('#paddingBottom').val();
        $(that).css({'paddingBottom': parent.$('#paddingBottom').val()});
    }

    // boxShadow
    var cssBoxShadow = "";
    if (parent.$('#boxShadowInset').is(':checked')) {
        style.boxshadow.inset = 1;
        cssBoxShadow += 'inset ';
    }

    style.boxshadow.x = parent.$('#boxShadowX').val();
    cssBoxShadow += parseInt(parent.$('#boxShadowX').val());

    style.boxshadow.y = parent.$('#boxShadowY').val();
    cssBoxShadow += parseInt(parent.$('#boxShadowY').val());

    style.boxshadow.blur = parent.$('#boxShadowBlur').val();
    cssBoxShadow += parseInt(parent.$('#boxShadowBlur').val());

    style.boxshadow.spread = parent.$('#boxShadowSpread').val();
    cssBoxShadow += parseInt(parent.$('#boxShadowSpread').val());

    style.boxshadow.color = parent.$('#boxColor').val();
    cssBoxShadow += parent.$('#boxColor').val();

    $(that).css('boxShadow', cssBoxShadow);

    // fonts
    var cssFont = "";
    if (parent.$('#fontColor').val()) {
        style.font.color = parent.$('#fontColor').val();
        $(that).css('color', parent.$('#fontColor').val());
    }
    if (parent.$('#fontFamily option').is(':selected')) {
        style.font.family = parent.$('#fontFamily').val();
        $(that).css('fontFamily', parent.$('#fontFamily').val());
    }
    if (parent.$('#fontSize').val()) {
        style.font.size = parent.$('#fontSize').val();
        $(that).css('fontSize', parent.$('#fontSize').val());
    }
    if (parent.$('#fontStyle').val()) {
        style.font.style = 'italic';
        $(that).css('fontStyle', 'italic');
    } else {
        style.font.style = 'normal';
        $(that).css('fontStyle', 'normal');
    }
    if (parent.$('#fontWeight').val()) {
        style.font.weight = 'bold';
        $(that).css('fontWeight', 'bold');
    } else {
        style.font.weight = 'normal';
        $(that).css('fontWeight', 'normal');
    }
    if (parent.$('#textDecoration').val()) {
        style.font.decoration = parent.$('#textDecoration').val();
        $(that).css('textDecoration', parent.$('#textDecoration').val());
    }
    if (parent.$('#textAlign').val()) {
        style.font.align = parent.$('#textAlign').val();
        $(that).css('textAlign', parent.$('#textAlign').val());
    }
    if (parent.$('#textTransform').val()) {
        style.font.transform = parent.$('#textTransform').val();
        $(that).css('textTransform', parent.$('#textTransform').val());
    }
    if (parent.$('#letterSpacing').val()) {
        style.font.letterspacing = parent.$('#letterSpacing').val();
        $(that).css('letterSpacing', parent.$('#letterSpacing').val());
    }
    if (parent.$('#wordSpacing').val()) {
        style.font.wordspacing = parent.$('#wordSpacing').val();
        $(that).css('wordSpacing', parent.$('#wordSpacing').val());
    }
    if (parent.$('#lineHeight').val()) {
        style.font.lineheight = parent.$('#lineHeight').val();
        $(that).css('lineHeight', parent.$('#lineHeight').val());
    }

    elements[cookieName] = style;
    $.jStorage.set('elements', elements);
}

/**
 * Helper que establece el valor de la alineacion del texto del elemento seleccionado.
 *
 * @return void.
 */
function setAlign(e, v, c) {
    parent.$('#alignLeft').removeClass('align-leftOn');
    parent.$('#alignCenter').removeClass('align-centerOn');
    parent.$('#alignRight').removeClass('align-rightOn');
    parent.$('#alignJustify').removeClass('align-justifyOn');
    $(e).toggleClass(c);
    parent.$('#textAlign').val(v);
    setStyle();
}

/**
 * Helper que establece el valor de la decoracion del texto del elemento seleccionado.
 *
 * @return void.
 */
function setDecoration(e, v) {
    if (v == 'underline') {
        parent.$('#underline').toggleClass('underlineOn');
        parent.$('#lineThrough').removeClass('line-throughOn');
        if (parent.$('#textDecoration').val() == 'underline') {
            parent.$('#textDecoration').val('none');
        } else {
            parent.$('#textDecoration').val(v);
        }
    }
    if (v == 'line-through') {
        parent.$('#lineThrough').toggleClass('line-throughOn');
        parent.$('#underline').removeClass('underlineOn');
        if (parent.$('#textDecoration').val() == 'line-through') {
            parent.$('#textDecoration').val('none');
        } else {
            parent.$('#textDecoration').val(v);
        }
    }
    setStyle();
}

/**
 * Helper que establece el valor de la decoracion del texto del elemento seleccionado.
 *
 * @return void.
 */
function setWeight(e, v, c) {
    $(e).toggleClass(c);
    if (parent.$('#fontWeight').val() == v) {
        parent.$('#fontWeight').val('');
    } else {
        parent.$('#fontWeight').val(v);
    }
    setStyle();
}

/**
 * Helper que establece el valor de la decoracion del texto del elemento seleccionado.
 *
 * @return void.
 */
function setItalic(e, v, c) {
    $(e).toggleClass(c);
    if (parent.$('#fontStyle').val() == v) {
        parent.$('#fontStyle').val('');
    } else {
        parent.$('#fontStyle').val(v);
    }
    setStyle();
}

/**
 * Helper que establece el valor de la decoracion del texto del elemento seleccionado.
 *
 * @return void.
 */
function setTransform(e, v) {
    if (v == 'uppercase') {
        parent.$('#upper').toggleClass('uppercaseOn');
        parent.$('#lower').removeClass('lowercaseOn');
        parent.$('#capitalize').removeClass('capitalizeOn');
        if (parent.$('#textTransform').val() == v) {
            parent.$('#textTransform').val('');
        } else {
            parent.$('#textTransform').val(v);
        }
    }
    if (v == 'lowercase') {
        parent.$('#lower').toggleClass('lowercaseOn');
        parent.$('#upper').removeClass('uppercaseOn');
        parent.$('#capitalize').removeClass('capitalizeOn');
        if (parent.$('#textTransform').val() == v) {
            parent.$('#textTransform').val('');
        } else {
            parent.$('#textTransform').val(v);
        }
    }
    if (v == 'capitalize') {
        parent.$('#capitalize').toggleClass('capitalizeOn');
        parent.$('#upper').removeClass('uppercaseOn');
        parent.$('#lower').removeClass('lowercaseOn');
        if (parent.$('#textTransform').val() == v) {
            parent.$('#textTransform').val('');
        } else {
            parent.$('#textTransform').val(v);
        }
    }
    setStyle();
}

/**
 * Limpia los campos y los estilos de los fuentes.
 *
 * @return void.
 */
function resetFont() {
    this.elements = $.jStorage.get('elements', null);
    if (elements) {
        $.each(elements, function (el, style) {
            if ('#' + el == parent.$('#selector').val()) {
                style.font.color = null;
                style.font.family = null;
                style.font.weight = null;
                style.font.style = null;
                style.font.size = null;
                style.font.align = null;
                style.font.decoration = null;
                style.font.transform = null;
                style.font.letterspacing = null;
                style.font.wordspacing = null;
                style.font.lineheight = null;
            }
        });
        $.jStorage.set('elements', this.elements);
    }
    $(parent.$('#selector').val()).css({
        'color': '#000',
        'font': 'none',
        'fontStyle': 'normal',
        'textDecoration': 'none',
        'textAlign': 'left',
        'letterSpacing': 'normal',
        'wordSpacing': 'normal',
        'lineHeight': 'normal'
    });
    $(parent.$('#selector').val()).find('h1').css({
        'color': '#000',
        'font': 'none',
        'fontStyle': 'normal',
        'textDecoration': 'none',
        'textAlign': 'left',
        'letterSpacing': 'normal',
        'wordSpacing': 'normal',
        'lineHeight': 'normal'
    });
    $(parent.$('#selector').val()).find('h2').css({
        'color': '#000',
        'font': 'none',
        'fontStyle': 'normal',
        'textDecoration': 'none',
        'textAlign': 'left',
        'letterSpacing': 'normal',
        'wordSpacing': 'normal',
        'lineHeight': 'normal'
    });
    $(parent.$('#selector').val()).find('h3').css({
        'color': '#000',
        'font': 'none',
        'fontStyle': 'normal',
        'textDecoration': 'none',
        'textAlign': 'left',
        'letterSpacing': 'normal',
        'wordSpacing': 'normal',
        'lineHeight': 'normal'
    });
    $(parent.$('#selector').val()).find('h4').css({
        'color': '#000',
        'font': 'none',
        'fontStyle': 'normal',
        'textDecoration': 'none',
        'textAlign': 'left',
        'letterSpacing': 'normal',
        'wordSpacing': 'normal',
        'lineHeight': 'normal'
    });
    $(parent.$('#selector').val()).find('h5').css({
        'color': '#000',
        'font': 'none',
        'fontStyle': 'normal',
        'textDecoration': 'none',
        'textAlign': 'left',
        'letterSpacing': 'normal',
        'wordSpacing': 'normal',
        'lineHeight': 'normal'
    });
    $(parent.$('#selector').val()).find('h6').css({
        'color': '#000',
        'font': 'none',
        'fontStyle': 'normal',
        'textDecoration': 'none',
        'textAlign': 'left',
        'letterSpacing': 'normal',
        'wordSpacing': 'normal',
        'lineHeight': 'normal'
    });
    $(parent.$('#selector').val()).find('p').css({
        'color': '#000',
        'font': 'none',
        'fontStyle': 'normal',
        'textDecoration': 'none',
        'textAlign': 'left',
        'letterSpacing': 'normal',
        'wordSpacing': 'normal',
        'lineHeight': 'normal'
    });
    $(parent.$('#selector').val()).find('b').css({
        'color': '#000',
        'font': 'none',
        'fontStyle': 'normal',
        'textDecoration': 'none',
        'textAlign': 'left',
        'letterSpacing': 'normal',
        'wordSpacing': 'normal',
        'lineHeight': 'normal'
    });
    parent.$('#fontColor').val('');
    parent.$('#fontFamily').val('');
    parent.$('#fontWeight').val('');
    parent.$('#fontStyle').val('');
    parent.$('#fontSize').val('');
    parent.$('#fontAlign').val('');
    parent.$('#fontDecoration').val('');
    parent.$('#letterSpacing').val('');
    parent.$('#wordSpacing').val('');
    parent.$('#lineHeight').val('');

    parent.$('#letterSpacingSlider').slider('option', 'value', 0);
    parent.$('#wordSpacingSlider').slider('option', 'value', 0);
    parent.$('#lineHeightSlider').slider('option', 'value', 0);

    parent.$('#bold').removeClass('boldOn');
    parent.$('#italic').removeClass('italicOn');
    parent.$('#underline').removeClass('underlineOn');
    parent.$('#lineThrough').removeClass('line-throughOn');
    parent.$('#upper').removeClass('uppercaseOn');
    parent.$('#lower').removeClass('lowercaseOn');
    parent.$('#alignLeft').removeClass('align-leftOn');
    parent.$('#alignCenter').removeClass('align-centerOn');
    parent.$('#alignRight').removeClass('align-rightOn');
    parent.$('#alignJustify').removeClass('align-justifyOn');
}

/**
 * Limpia los campos y los estilos del fondo.
 *
 * @return void.
 */
function resetBackground() {
    this.elements = $.jStorage.get('elements', null);
    if (elements) {
        $.each(elements, function (el, style) {
            if ('#' + el == parent.$('#selector').val()) {
                style.background.color = null;
                style.background.image = null;
                style.background.repeat = null;
                style.background.positionX = null;
                style.background.positionY = null;
                style.background.attachment = null;
            }
        });
        $.jStorage.set('elements', this.elements);
    }
    $(parent.$('#selector').val()).css({
        'background': 'none'
    });
    parent.$('#backgroundColor').val('');
    parent.$('#backgroundImage').val('');
    parent.$('#backgroundRepeat').val('');
    parent.$('#backgroundPositionX').val('');
    parent.$('#backgroundPositionY').val('');
    parent.$('#backgroundAttachment').removeAttr('checked');
}

/**
 * Limpia los campos y los margenes externos.
 *
 * @return void.
 */
function resetMargin() {
    this.elements = $.jStorage.get('elements', null);
    if (elements) {
        $.each(elements, function (el, style) {
            if ('#' + el == parent.$('#selector').val()) {
                style.margin.all = null;
                style.margin.top = null;
                style.margin.right = null;
                style.margin.bottom = null;
                style.margin.left = null;
            }
        });
        $.jStorage.set('elements', this.elements);
    }
    $(parent.$('#selector').val()).css({
        'margin': '0px auto'
    });
    parent.$("#marginSlider").slider('option', 'value', 0);
    parent.$("#marginTopSlider").slider('option', 'value', 0);
    parent.$("#marginBottomSlider").slider('option', 'value', 0);
    parent.$("#marginRightSlider").slider('option', 'value', 0);
    parent.$("#marginLeftSlider").slider('option', 'value', 0);

    parent.$('#margin').val('');
    parent.$('#marginTop').val('');
    parent.$('#marginBottom').val('');
    parent.$('#marginRight').val('');
    parent.$('#marginLeft').val('');
}

/**
 * Limpia los campos y los margenes externos.
 *
 * @return void.
 */
function resetDimensions() {
    this.elements = $.jStorage.get('elements', null);
    if (elements) {
        $.each(elements, function (el, style) {
            if ('#' + el == parent.$('#selector').val()) {
                style.dimensions.top = null;
                style.dimensions.left = null;
                style.dimensions.width = null;
                style.dimensions.height = null;
            }
        });
        $.jStorage.set('elements', this.elements);
    }
    $(parent.$('#selector').val()).css({
        'width': 'auto',
        'height': 'auto',
        'top': 'auto',
        'left': 'auto',
        'z-index': '1',
        'position': 'relative'
    });
    parent.$('#widthSlider').slider('option', 'value', 0);
    parent.$('#heightSlider').slider('option', 'value', 0);
    parent.$('#leftSlider').slider('option', 'value', 0);
    parent.$('#topSlider').slider('option', 'value', 0);

    parent.$('#width').val('');
    parent.$('#height').val('');
    parent.$('#position').val('');
    parent.$('#left').val('');
    parent.$('#top').val('');
}

/**
 * Limpia los campos y los margenes internos.
 *
 * @return void.
 */
function resetPadding() {
    this.elements = $.jStorage.get('elements', null);
    if (elements) {
        $.each(elements, function (el, style) {
            if ('#' + el == parent.$('#selector').val()) {
                style.padding.all = null;
                style.padding.top = null;
                style.padding.right = null;
                style.padding.bottom = null;
                style.padding.left = null;
            }
        });
        $.jStorage.set('elements', this.elements);
    }
    $(parent.$('#selector').val()).css({
        'padding': '0px'
    });
    parent.$("#paddingSlider").slider('option', 'value', 0);
    parent.$("#paddingTopSlider").slider('option', 'value', 0);
    parent.$("#paddingBottomSlider").slider('option', 'value', 0);
    parent.$("#paddingRightSlider").slider('option', 'value', 0);
    parent.$("#paddingLeftSlider").slider('option', 'value', 0);

    parent.$('#padding').val('');
    parent.$('#paddingTop').val('');
    parent.$('#paddingBottom').val('');
    parent.$('#paddingRight').val('');
    parent.$('#paddingLeft').val('');
}

/**
 * Limpia los bordes.
 *
 * @return void.
 */
function resetBorder() {
    this.elements = $.jStorage.get('elements', null);
    if (elements) {
        $.each(elements, function (el, style) {
            if ('#' + el == parent.$('#selector').val()) {
                style.border.color = null;
                style.border.style = null;
                style.border.width = null;

                style.border.topcolor = null;
                style.border.topstyle = null;
                style.border.topwidth = null;

                style.border.rightcolor = null;
                style.border.rightstyle = null;
                style.border.rightwidth = null;

                style.border.bottomcolor = null;
                style.border.bottomstyle = null;
                style.border.bottomwidth = null;

                style.border.leftcolor = null;
                style.border.leftstyle = null;
                style.border.leftwidth = null;
            }
        });
        $.jStorage.set('elements', this.elements);
    }
    $(parent.$('#selector').val()).css({
        'border': 'none'
    });
    parent.$('#borderWidthSlider').slider('option', 'value', 0);
    parent.$('#borderLeftWidthSlider').slider('option', 'value', 0);
    parent.$('#borderRightWidthSlider').slider('option', 'value', 0);
    parent.$('#borderBottomWidthSlider').slider('option', 'value', 0);

    parent.$('#borderColor').val('');
    parent.$('#borderStyle').val('');
    parent.$('#borderWidth').val('');
    parent.$('#borderTopColor').val('');
    parent.$('#borderTopStyle').val('');
    parent.$('#borderTopWidth').val('');
    parent.$('#borderRightColor').val('');
    parent.$('#borderRightWidth').val('');
    parent.$('#borderBottomColor').val('');
    parent.$('#borderBottomStyle').val('');
    parent.$('#borderBottomWidth').val('');
    parent.$('#borderLeftColor').val('');
    parent.$('#borderLeftStyle').val('');
    parent.$('#borderLeftWidth').val('');
}

/**
 * Limpia los bordes radius.
 *
 * @return void.
 */
function resetBorderRadius() {
    this.elements = $.jStorage.get('elements', null);
    if (elements) {
        $.each(elements, function (el, style) {
            if ('#' + el == parent.$('#selector').val()) {
                style.borderradius.all = null;
                style.borderradius.topleft = null;
                style.borderradius.topright = null;
                style.borderradius.bottomleft = null;
                style.borderradius.bottomright = null;
            }
        });
        $.jStorage.set('elements', this.elements);
    }
    $(parent.$('#selector').val()).css({
        'borderRadius': 'none'
    });

    parent.$('#borderRadiusSlider').slider('option', 'value', 0);
    parent.$('#borderRadiusTopLeftSlider').slider('option', 'value', 0);
    parent.$('#borderRadiusTopRightSlider').slider('option', 'value', 0);
    parent.$('#borderRadiusBottomRightSlider').slider('option', 'value', 0);
    parent.$('#borderRadiusBottomLeftSlider').slider('option', 'value', 0);

    parent.$('#borderRadius').val('');
    parent.$('#borderRadiusTopLeft').val('');
    parent.$('#borderRadiusTopRight').val('');
    parent.$('#borderRadiusBottomRight').val('');
    parent.$('#borderRadiusBottomLeft').val('');
}

/**
 * Limpia las sombras.
 *
 * @return void.
 */
function resetBoxShadow() {
    this.elements = $.jStorage.get('elements', null);
    if (elements) {
        $.each(elements, function (el, style) {
            if ('#' + el == parent.$('#selector').val()) {
                style.boxshadow.color = null;
                style.boxshadow.inset = null;
                style.boxshadow.x = null;
                style.boxshadow.y = null;
                style.boxshadow.blur = null;
                style.boxshadow.spread = null;
            }
        });
        $.jStorage.set('elements', this.elements);
    }
    $(parent.$('#selector').val()).css({
        'boxShadow': 'none',
    });

    parent.$('#boxShadowXSlider').slider('option', 'value', 0);
    parent.$('#boxShadowYSlider').slider('option', 'value', 0);
    parent.$('#boxShadowBlurSlider').slider('option', 'value', 0);
    parent.$('#boxShadowSpreadSlider').slider('option', 'value', 0);

    parent.$('#boxColor').val('');
    parent.$('#boxShadowX').val('');
    parent.$('#boxShadowY').val('');
    parent.$('#boxShadowBlur').val('');
    parent.$('#boxShadowSpread').val('');
    parent.$('#boxShadowInset').removeAttr('checked');
}

function cleanStyle() {
    var elements = {};
    elements = $.jStorage.get('elements', null);
    if (elements) {
        $.each(elements, function (el, style) {
            parent.$('#selector').val('#' + el);
            resetFont();
            resetBackground();
            resetMargin();
            resetPadding();
            resetDimensions();
            resetBorder();
            resetBorderRadius();
            resetBoxShadow();
        });
        $.removeCookie('elements');
        $.removeCookie('currentBlock');
        window.location.reload();
    }
}

function copyStyle() {
    var cookieName = $.jStorage.get('currentBlock', '');
    if (cookieName.length == 0 || !cookieName) {
        alert("Debes seleccionar un elemento de la pagina para copiar");
        return false;
    }
    var elements = $.jStorage.get('elements', {});
    if (typeof elements[cookieName] !== 'undefined') {
        $.jStorage.set('clipboardStyle', elements[cookieName]);
    } else {
        alert("No hay nada para copiar");
        return false;
    }
}

function pasteStyle() {
    var cookieName = $.jStorage.get('currentBlock', '');
    if (cookieName.length == 0 || !cookieName) {
        alert("Debes seleccionar un elemento de la pagina para pegar");
        return false;
    }
    if (!$.jStorage.get('clipboardStyle', null)) {
        alert("No hay nada para pegar");
        return false;
    } else {
        var elements = $.jStorage.get('elements', null);
        elements[cookieName] = $.jStorage.get('clipboardStyle', {});
        $.jStorage.set('elements', elements);
        renderPanels(parent.$("#selector").val());
        setStyle();
    }
}

function saveStyle(url) {
    var theme_id = getUrlVars()["theme_id"];

    if (typeof theme_id !== 'undefined') {
        var data = {};
        elementsToSave = $.jStorage.get('elements', null);
        $.each(elementsToSave, function (selector, properties) {
            selector = selector.replace(' ', '%20');
            data[selector] = properties;
            /* descomentar si la solicitud es muy grande
             $.post(url,data);
             */
        });
        $.post(url, data);
    }
}

/**
 * Establece una url en el campo backgroundImage y ejecuta setStyle()
 *
 * @return void.
 */
function setImage(a) {
    parent.$('#backgroundImage').val(a);
    setStyle();
}

function _IsNumber(_id) {
    if (IsNumber(parent.$('#' + _id).val())) {
        a = parent.$('#' + _id).val();
        parent.$('#' + _id).val(a + 'px ');
    }
}
function IsNumber(n) {
    return !isNaN(parseFloat(n)) && isFinite(n);
}

function initSortable( refresh ) {
    //if (!getUrlVars()['theme_id']) {
        $("ul.widgets").sortable({
            dropOnEmpty: true,
            placeholder: 'placeholder',
            forceHelperSize: true,
            forcePlaceholderSize: true,
            connectWith: $("ul.widgets"),
            revert: true,
            cursor: 'move',
            handle: $(".move"),
            receive: function(event, ui) {
                let that = $(this);
                loadWidgets(that.attr('data-necotienda-module'), that.attr('data-widget'), that.attr('data-landing_page'), 1);
            },
            stop: function () {
                $(this).find("input, select, textarea")
                .bind('mousedown.ui-disableSelection selectstart.ui-disableSelection', function (e) {
                    e.stopImmediatePropagation();
                });
            },
            update: function (event, ui) {
                let postData = {};

                $('[data-widget]').each(function () {
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
    //}

    if (refresh === true) {
        $("ul.widgets").sortable('refresh');
    }
}

function loadWidgets(...args) {
    let [moduleName, widgetName, landing_page, w, fromAdmin] = args;

    let that = $('#'+ widgetName);

    if (moduleName) {
        let position = that.closest('[data-row]').data('position');
        let sort_order = that.index();

        let inputs = 
        '<input class="widgetName" type="hidden" name="Widgets[' + widgetName + '][name]" id="' + widgetName + '_name" value="' + widgetName + '" />'
        +'<input class="widgetPosition" type="hidden" name="Widgets[' + widgetName + '][position]" id="' + widgetName + '_position" value="' + position + '" />'
        +'<input class="widgetSortOrder" type="hidden" name="Widgets[' + widgetName + '][order]" id="' + widgetName + '_order" value="' + sort_order + '" />';

        let row_id = that.closest('[data-row]').data('row');
        let col_id = that.closest('[data-column]').data('column');
        let store_id = parseInt(window.nt.sid);

        store_id = typeof store_id != 'undefined' ? parseInt(store_id) : 0;
        landing_page = landing_page ? landing_page : 'all';

        $.ajaxQueue({
            url: createAdminUrl('module/' + moduleName + '/widget',
                'store_id=' + store_id +
                '&landing_page=' + landing_page 
                +(typeof window.ot != 'undefined' ? '&ot='+ window.ot : '')
                +(typeof window.oid != 'undefined' ? '&oid='+ window.oid : '')
            ),
            dataType: "json",
            data: {
                w,//optional, only for frontend data flow
                'extension': moduleName,
                'name': widgetName,
                'order': sort_order,
                position,
                row_id,
                col_id
            }
        }).done(function (response) {
            if (typeof response.html != 'undefined') {

                let queryData = {
                    name: widgetName,
                    order: sort_order,
                    store_id,
                    landing_page,
                    row_id,
                    col_id,
                    position
                };

                function renderWidget( firstTime ) {
                    let asyncData = {
                        store_id,
                        route: landing_page,
                        w: widgetName,
                        order: sort_order,
                        row_id,
                        col_id,
                        position
                    };

                    if (firstTime===true) asyncData['cve'] = 1;

                    $.ajaxQueue({
                        url: createUrl('module/' + moduleName + '/async', asyncData),
                        dataType: "json"
                    })
                    .done(function(resp){
                        if (typeof resp.html != 'undefined') {
                            let formContainer;
                            let widgetEl;

                            const loadJS = url => {
                            	if ($(`script[src="${url}"`).length === 0) {
                            		let el = $(document.createElement('script')).attr({
                            			url,
                            			async:true,
                            			type:'text/javascript',
                            			'data-loader':'theme_editor',
                            			'data-theme_id':getUrlVars()['theme_id']
                            		}).appendTo('body');
                            	}
                            };
                            
                            const loadCSS = style => {
                            	if ($(`link[href="${style.href}"`).length === 0) {
                            		let el = $(document.createElement('link')).attr({
                            			href:style.href,
                            			media:(style.media ? style.media : 'all'),
                            			rel:'stylesheet',
                            			'data-loader':'theme_editor',
                            			'data-theme_id':getUrlVars()['theme_id']
                            		}).appendTo('head');
                            	}
                            };

                            if (Object.values(resp.javascripts).length > 0) {
                            	Object.values(resp.javascripts).map( url => { loadJS( url ); });
                            }
                            
                            if (Object.values(resp.styles).length > 0) {
                            	Object.values(resp.styles).map( style => { loadCSS( style ); });
                            }
                            
                            if (firstTime===true) {
                                formContainer = renderForm( widgetName, response.html );
                                if (fromAdmin) {
                                    widgetEl = $( resp.html );
                                    $('#' + widgetName).replaceWith( widgetEl );
                                    widgetEl.append( formContainer );
                                    addAdminControls( widgetEl );
                                } else {
                                    that.append( formContainer );
                                }
                            } else {
                                widgetEl = $( resp.html );
                                $('#' + widgetName)
                                    .children()
                                    .not( $( '#' + widgetName + '_attributes' ) )
                                    .not( $( '#' + widgetName + ' .actions' ) )
                                    .remove();

                                $( '#' + widgetName + ' .actions' ).after( widgetEl.html() );

                                if (widgetEl.attr('class').length > 0) {
                                    $( '#' + widgetName ).attr({
                                        class: widgetEl.attr('class')
                                    });
                                }

                                if (Object.keys(widgetEl.data()).length > 0) {
                                    let dataSet = {
                                        //...$( '#' + widgetName ).data(),
                                        ...widgetEl.data()
                                    };

                                    const removeData = (el) => {
                                        Object.keys(el.dataset).map(v => { $(el).removeAttr('data-'+ v); });
                                    }

                                    removeData( $('#'+ widgetName )[0] );

                                    for (let i in dataSet) {
                                        $( '#' + widgetName ).attr('data-'+ i, dataSet[i]);
                                    }
                                }

                                if (typeof widgetEl.data('animate') != 'undefined') {
                                    animateTransition('#' + widgetName);
                                }

                                that.addClass('administrable');                                
                            }

                            if (firstTime===true) {
                                if (fromAdmin) {
                                     updateWidget(moduleName, queryData, $('#' + widgetName + '_form').serialize(), false);
                                }

                                let height = $(window).height() * 1.9;
                                let width = $(window).width() * 0.9;

                                $('#' + widgetName + '_form').append( inputs );
                                
                                $('#' + widgetName + ' a.config')
                                .off('click')
                                .attr({
                                    href: '#' + widgetName + '_attributes'
                                }).on('click', function(e){
                                    e.preventDefault();
                                	let parentEl = parent.$('#widgetSettingsLeftPanel').length === 0 ? $('body') : parent.$('#widgetSettingsLeftPanel');

                                        $.fancybox({
                                            content: formContainer,
                                            maxWidth: width,
                                            maxHeight: height,
                                            fitToView: false,
                                            width: (parent.$('#widgetSettingsLeftPanel').length != 0 ? '280px' : '90%'),
                                            height: (parent.$('#widgetSettingsLeftPanel').length != 0 ? '580px' : '90%'),
                                            autoSize: false,
                                            closeClick: false,
                                            openEffect: 'none',
                                            closeEffect: 'none',
                                            type:'html',
                                            parent:parentEl,
                                            afterShow: function() {
                                            	if (parent.$('#widgetSettingsLeftPanel').length != 0) {
                                                    $.fancybox('close');
                                                    parent.$('#widgetSettingsLeftPanel').html( formContainer );
                                                    if (parent.$('textarea[name*="descriptions"]').length>0) {
                                                        if (typeof parent.initEditor == 'function') {
                                                            parent.initEditor();
                                                        } else if (typeof initEditor == 'function') {
                                                            parent.initEditor = initEditor;
                                                            parent.loadCKEditor = loadCKEditor;
                                                            parent.initEditor();
                                                        }
                                                    }
                                            		parent.$('a[tab="#widgetSettingsLeftPanel"]').trigger('click');
                                                	
                                                	bindFormWidget(widgetName, moduleName, queryData, renderWidget, true);
                                                	let el = $( '#'+ widgetName );
    				                                parent.$('#'+ widgetName +'_form p').on('mouseenter', ()=>{
    											        el.addClass('hover');
    											    }).on('mouseleave', ()=>{
    											        el.removeClass('hover');
    											    });
                                            	} else {
                                                	bindFormWidget(widgetName, moduleName, queryData, renderWidget);
                                            	}
                                            }
                                        });
                                    
                                    return false;                                    
                                });    
                                /*
                                if (typeof $.ui.sortable === "function") {
                                    if (typeof $("ul.widgets").sortable('instance') != 'undefined') {
                                        $('ul.widgets').sortable( 'refresh' );
                                    }
                                }
                                */
                            }
                        }
                    });
                }

                renderWidget( true );
            }

            $(".widgetWrapper")
            .find("input, select, textarea")
            .bind('mousedown.ui-disableSelection selectstart.ui-disableSelection', function (e) {
                e.stopImmediatePropagation();
            });
        });
    }
}

function renderForm( widgetName, html ) {
    if ($('#'+ widgetName + '_attributes').length == 0) {
        return $(document.createElement('div')).attr({
            id:widgetName + '_attributes',
            class:'attributes'
        }).append( html );
    } else {
        return $('#'+ widgetName + '_attributes');
    }
}

function bindFormWidget(widgetName, moduleName, queryData, renderWidget, __parent) {
        if (__parent) parent.$('#' + widgetName + '_form .chosen').chosen();
        else $('#' + widgetName + '_form .chosen').chosen();

        if (__parent) {
            parent.$('#' + widgetName + '_form .htabs .htab')
            .off('click')
            .on('click', function() {
                $(this).closest('.htabs').find('.htab').each(function () {
                    parent.$($(this).attr('tab')).hide();
                    $(this).removeClass('selected');
                });
                $(this).addClass('selected');
                parent.$($(this).attr('tab')).show();
            });

	        parent.$('#' + widgetName + '_form .htab').eq(0).trigger('click');

	        parent.$('#' + widgetName + '_form').find('input, select, textarea')
	        .off('change')
	        .on('change', function(e) {
	            
	            if (typeof data.forms == 'undefined') data.forms = {};
	            if (typeof data.forms[moduleName] == 'undefined') data.forms[moduleName] = '';

	            let postData = parent.$('#' + widgetName + '_form').serialize();
	            let postDataChecksum = hashCode(postData);

	            if (data.forms[moduleName].checksum != postDataChecksum) {
	                data.forms[moduleName].checksum = postDataChecksum;
	                updateWidget(moduleName, queryData, postData, renderWidget);
	            }
	        });
	    } else {
	        $('#' + widgetName + '_form .htabs .htab')
	        .off('click')
	        .on('click', function() {
	            $(this).closest('.htabs').find('.htab').each(function () {
	                $($(this).attr('tab')).hide();
	                $(this).removeClass('selected');
	            });
	            $(this).addClass('selected');
	            $($(this).attr('tab')).show();
	        });

	        $('#' + widgetName + '_form .htab').eq(0).trigger('click');

	        $('#' + widgetName + '_form').find('input, select, textarea')
	        .off('change')
	        .on('change', function(e) {
	            if (typeof data.forms == 'undefined') data.forms = {};
	            if (typeof data.forms[moduleName] == 'undefined') data.forms[moduleName] = '';

	            let postData = $('#' + widgetName + '_form').serialize();
	            let postDataChecksum = hashCode(postData);

	            if (data.forms[moduleName].checksum != postDataChecksum) {
	                data.forms[moduleName].checksum = postDataChecksum;
	                updateWidget(moduleName, queryData, postData, renderWidget);
	            }
	        });
	    }
}

function updateWidget(moduleName, queryData, postData, renderWidget) {
    $.post(
        createAdminUrl('module/' + moduleName + '/widget', queryData),
        postData
    ).done(function(respons) {
        let r = $.parseJSON(respons);
        if (typeof r.error != 'undefined') {

        }

        if (typeof r.success != 'undefined' && typeof renderWidget == 'function') {
            renderWidget( false );
        }
    });
}

function deleteWidget(triggerEl) {
    let widgetEl = $(triggerEl).closest('[data-widget]').get(0);
    $.getJSON(createAdminUrl('style/widget/delete', { name:$(widgetEl).attr('id') }));
    $(widgetEl).remove();
}

function getParentId(el) {
    var parent = el.parent();

    if (parent.length > 0
        && $(parent).prop('tagName').toLowerCase() !== 'html'
        && $(parent).prop('tagName').toLowerCase() !== 'body'
        && $(el).prop('tagName').toLowerCase() !== 'body')
    {
        var id = $(parent).attr('id');
        if (id === undefined || id === 0) {
            return getParentId($(parent));
        } else {
            return id;
        }
    } else {
        return false;
    }
}

/**
 * Muestra y oculta las opciones avanzadas de los paneles
 *
 * @param e el enlace que acciona el evento.
 * @return void.
 */
function showAdvanced(e) {
    if ($(e).hasClass('on')) {
        $(e).removeClass('on').text('Mostrar Opciones Avanzadas');
        $(e).parent().find('.advanced:eq(0)').val(0);
    } else {
        $(e).addClass('on').text('Ocultar Opciones Avanzadas');
        $(e).parent().find('.advanced:eq(0)').val(1);
    }
    $(e).parent().find('> div').slideToggle('fast');

}

/**
 * Obtiene los items de una lista y los seriliza
 *
 * @param id de la lista.
 * @return array lista serializada.
 */
function getItems(id) {
    return $('#' + id).sortable('toArray').join(',');
}

function getUrlVars() {
    var vars = {};
    var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function (m, key, value) {
        vars[key] = value;
    });
    return vars;
}

function createAdminUrl(route, params) {
    var url = window.nt.http_admin + 'index.php?r=' + route + '&token=' + window.nt.token;
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

/**
 * ajaxQueue for queue ajax requests
 * 
 */
function renderJSLink( url ) {
    if (!$('script[src="'+ window.nt.http_admin_theme_js + url +'"]').length) {
        $('body').append(
            $(document.createElement('script')).attr({
                src:window.nt.http_admin_theme_js + url,
                type:'text/javascript'
            })
        );
    }
}

/*
hash string function
 */
function hashCode(str) {
  var hash = 0, i, chr;
  if (str.length === 0) return hash;
  for (i = 0; i < str.length; i++) {
    chr   = str.charCodeAt(i);
    hash  = ((hash << 5) - hash) + chr;
    hash |= 0; // Convert to 32bit integer
  }
  return Math.abs(hash);
}

function rand(min, max) {
    if (!min && !max) {
        min = 0;
        max = 2147483647;
    }
    return Math.floor(Math.random() * (max - min + 1)) + min;
}

/*! Sidr - v1.2.1 - 2013-11-06
 * https://github.com/artberri/sidr
 * Copyright (c) 2013 Alberto Varela; Licensed MIT */
(function(e){var t=!1,i=!1,n={isUrl:function(e){var t=RegExp("^(https?:\\/\\/)?((([a-z\\d]([a-z\\d-]*[a-z\\d])*)\\.)+[a-z]{2,}|((\\d{1,3}\\.){3}\\d{1,3}))(\\:\\d+)?(\\/[-a-z\\d%_.~+]*)*(\\?[;&a-z\\d%_.~+=-]*)?(\\#[-a-z\\d_]*)?$","i");return t.test(e)?!0:!1},loadContent:function(e,t){e.html(t)},addPrefix:function(e){var t=e.attr("id"),i=e.attr("class");"string"==typeof t&&""!==t&&e.attr("id",t.replace(/([A-Za-z0-9_.\-]+)/g,"sidr-id-$1")),"string"==typeof i&&""!==i&&"sidr-inner"!==i&&e.attr("class",i.replace(/([A-Za-z0-9_.\-]+)/g,"sidr-class-$1")),e.removeAttr("style")},execute:function(n,s,a){"function"==typeof s?(a=s,s="sidr"):s||(s="sidr");var r,d,l,c=e("#"+s),u=e(c.data("body")),f=e("html"),p=c.outerWidth(!0),g=c.data("speed"),h=c.data("side"),m=c.data("displace"),v=c.data("onOpen"),y=c.data("onClose"),x="sidr"===s?"sidr-open":"sidr-open "+s+"-open";if("open"===n||"toggle"===n&&!c.is(":visible")){if(c.is(":visible")||t)return;if(i!==!1)return o.close(i,function(){o.open(s)}),void 0;t=!0,"left"===h?(r={left:p+"px"},d={left:"0px"}):(r={right:p+"px"},d={right:"0px"}),u.is("body")&&(l=f.scrollTop(),f.css("overflow-x","hidden").scrollTop(l)),m?u.addClass("sidr-animating").css({width:u.width(),position:"absolute"}).animate(r,g,function(){e(this).addClass(x)}):setTimeout(function(){e(this).addClass(x)},g),c.css("display","block").animate(d,g,function(){t=!1,i=s,"function"==typeof a&&a(s),u.removeClass("sidr-animating")}),v()}else{if(!c.is(":visible")||t)return;t=!0,"left"===h?(r={left:0},d={left:"-"+p+"px"}):(r={right:0},d={right:"-"+p+"px"}),u.is("body")&&(l=f.scrollTop(),f.removeAttr("style").scrollTop(l)),u.addClass("sidr-animating").animate(r,g).removeClass(x),c.animate(d,g,function(){c.removeAttr("style").hide(),u.removeAttr("style"),e("html").removeAttr("style"),t=!1,i=!1,"function"==typeof a&&a(s),u.removeClass("sidr-animating")}),y()}}},o={open:function(e,t){n.execute("open",e,t)},close:function(e,t){n.execute("close",e,t)},toggle:function(e,t){n.execute("toggle",e,t)},toogle:function(e,t){n.execute("toggle",e,t)}};e.sidr=function(t){return o[t]?o[t].apply(this,Array.prototype.slice.call(arguments,1)):"function"!=typeof t&&"string"!=typeof t&&t?(e.error("Method "+t+" does not exist on jQuery.sidr"),void 0):o.toggle.apply(this,arguments)},e.fn.sidr=function(t){var i=e.extend({name:"sidr",speed:200,side:"left",source:null,renaming:!0,body:"body",displace:!0,onOpen:function(){},onClose:function(){}},t),s=i.name,a=e("#"+s);if(0===a.length&&(a=e("<div />").attr("id",s).appendTo(e("body"))),a.addClass("sidr").addClass(i.side).data({speed:i.speed,side:i.side,body:i.body,displace:i.displace,onOpen:i.onOpen,onClose:i.onClose}),"function"==typeof i.source){var r=i.source(s);n.loadContent(a,r)}else if("string"==typeof i.source&&n.isUrl(i.source))e.get(i.source,function(e){n.loadContent(a,e)});else if("string"==typeof i.source){var d="",l=i.source.split(",");if(e.each(l,function(t,i){d+='<div class="sidr-inner">'+e(i).html()+"</div>"}),i.renaming){var c=e("<div />").html(d);c.find("*").each(function(t,i){var o=e(i);n.addPrefix(o)}),d=c.html()}n.loadContent(a,d)}else null!==i.source&&e.error("Invalid Sidr Source");return this.each(function(){var t=e(this),i=t.data("sidr");i||(t.data("sidr",s),"ontouchstart"in document.documentElement?(t.bind("touchstart",function(e){e.originalEvent.touches[0],this.touched=e.timeStamp}),t.bind("touchend",function(e){var t=Math.abs(e.timeStamp-this.touched);200>t&&(e.preventDefault(),o.toggle(s))})):t.click(function(e){e.preventDefault(),o.toggle(s)}))})}})(jQuery);