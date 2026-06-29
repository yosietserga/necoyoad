$(function () {

    if (getUrlVars()['admin_tools']) {

        
        var width = $(window).width();
        var height = $(window).height();
        var tooltip = parent.$('<div id="tooltip" />').css({
            position: 'absolute',
            top: -25,
            left: -10
        }).hide();

        var ttip = tooltip.clone();
        parent.$("#marginSlider").find(".ui-slider-handle").append(ttip);
        parent.$("#marginSlider").slider({
            range: "min",
            min: -width,
            max: width,
            slide: function (event, ui) {
                parent.$("#margin").val(ui.value);
                parent.$("#marginTop").val(ui.value);
                parent.$("#marginRight").val(ui.value);
                parent.$("#marginBottom").val(ui.value);
                parent.$("#marginLeft").val(ui.value);

                parent.$("#marginTopSlider").slider('option', 'value', parseInt(ui.value));
                parent.$("#marginRightSlider").slider('option', 'value', parseInt(ui.value));
                parent.$("#marginBottomSlider").slider('option', 'value', parseInt(ui.value));
                parent.$("#marginLeftSlider").slider('option', 'value', parseInt(ui.value));

                setStyle();
            }
        });
        parent.$("#margin").on('change', function (event) {
            parent.$("#marginSlider").slider('option', 'value', parseInt(this.value));
        });
        parent.$("#marginTop").on('change', function (event) {
            parent.$("#marginTopSlider").slider('option', 'value', parseInt(this.value));
        });
        parent.$("#marginRight").on('change', function (event) {
            parent.$("#marginRightSlider").slider('option', 'value', parseInt(this.value));
        });
        parent.$("#marginBottom").on('change', function (event) {
            parent.$("#marginBottomSlider").slider('option', 'value', parseInt(this.value));
        });
        parent.$("#marginLeft").on('change', function (event) {
            parent.$("#marginLeftSlider").slider('option', 'value', parseInt(this.value));
        });


        $parent.("#marginTopSlider").slider({
            range: "min",
            min: -height,
            max: height,
            slide: function (event, ui) {
                parent.$("#marginTop").val(ui.value);
                setStyle();
            }
        });

        parent.$("#marginRightSlider").slider({
            range: "min",
            min: -width,
            max: width,
            slide: function (event, ui) {
                parent.$("#marginRight").val(ui.value);
                setStyle();
            }
        });

        parent.$("#marginBottomSlider").slider({
            range: "min",
            min: -height,
            max: height,
            slide: function (event, ui) {
                parent.$("#marginBottom").val(ui.value);
                setStyle();
            }
        });

        parent.$("#marginLeftSlider").slider({
            range: "min",
            min: -width,
            max: width,
            slide: function (event, ui) {
                parent.$("#marginLeft").val(ui.value);
                setStyle();
            }
        });

        parent.$("#paddingSlider").slider({
            range: "min",
            min: 0,
            max: 250,
            slide: function (event, ui) {
                parent.$("#padding").val(ui.value);
                parent.$("#paddingTop").val(ui.value);
                parent.$("#paddingRight").val(ui.value);
                parent.$("#paddingBottom").val(ui.value);
                parent.$("#paddingLeft").val(ui.value);

                parent.$("#paddingTopSlider").slider('option', 'value', parseInt(ui.value));
                parent.$("#paddingRightSlider").slider('option', 'value', parseInt(ui.value));
                parent.$("#paddingBottomSlider").slider('option', 'value', parseInt(ui.value));
                parent.$("#paddingLeftSlider").slider('option', 'value', parseInt(ui.value));

                setStyle();
            }
        });
        parent.$("#padding").on('change', function (event) {
            parent.$("#paddingSlider").slider('option', 'value', parseInt(this.value));
        });
        parent.$("#paddingTop").on('change', function (event) {
            parent.$("#paddingTopSlider").slider('option', 'value', parseInt(this.value));
        });
        parent.$("#paddingRight").on('change', function (event) {
            parent.$("#paddingRightSlider").slider('option', 'value', parseInt(this.value));
        });
        parent.$("#paddingBottom").on('change', function (event) {
            parent.$("#paddingBottomSlider").slider('option', 'value', parseInt(this.value));
        });
        parent.$("#paddingLeft").on('change', function (event) {
            parent.$("#paddingLeftSlider").slider('option', 'value', parseInt(this.value));
        });
        parent.$("#paddingTopSlider").slider({
            range: "min",
            min: 0,
            max: height,
            slide: function (event, ui) {
                parent.$("#paddingTop").val(ui.value);
                setStyle();
            }
        });

        parent.$("#paddingRightSlider").slider({
            range: "min",
            min: 0,
            max: width,
            slide: function (event, ui) {
                parent.$("#paddingRight").val(ui.value);
                setStyle();
            }
        });

        parent.$("#paddingBottomSlider").slider({
            range: "min",
            min: 0,
            max: height,
            slide: function (event, ui) {
                parent.$("#paddingBottom").val(ui.value);
                setStyle();
            }
        });

        parent.$("#paddingLeftSlider").slider({
            range: "min",
            min: 0,
            max: width,
            slide: function (event, ui) {
                parent.$("#paddingLeft").val(ui.value);
                setStyle();
            }
        });

        parent.$("#topSlider").slider({
            range: "min",
            min: -height,
            max: height,
            slide: function (event, ui) {
                parent.$("#top").val(ui.value);
                setStyle();
            }
        });
        parent.$("#top").on('change', function (event) {
            parent.$("#topSlider").slider('option', 'value', parseInt(this.value));
        });
        parent.$("#leftSlider").slider({
            range: "min",
            min: -width,
            max: width,
            slide: function (event, ui) {
                parent.$("#left").val(ui.value);
                setStyle();
            }
        });
        parent.$("#left").on('change', function (event) {
            parent.$("#leftSlider").slider('option', 'value', parseInt(this.value));
        });
        parent.$("#widthSlider").slider({
            range: "min",
            min: 0,
            max: width,
            slide: function (event, ui) {
                parent.$("#width").val(ui.value);
                setStyle();
            }
        });
        parent.$("#width").on('change', function (event) {
            parent.$("#widthSlider").slider('option', 'value', parseInt(this.value));
        });
        parent.$("#heightSlider").slider({
            range: "min",
            min: 0,
            max: height,
            slide: function (event, ui) {
                parent.$("#height").val(ui.value);
                setStyle();
            }
        });
        parent.$("#height").on('change', function (event) {
            parent.$("#heightSlider").slider('option', 'value', parseInt(this.value));
        });

        parent.$("#borderRadiusSlider").slider({
            range: "min",
            min: 0,
            max: 250,
            slide: function (event, ui) {
                parent.$("#borderRadius").val(ui.value);
                parent.$("#borderRadiusTopLeft").val(ui.value);
                parent.$("#borderRadiusTopRight").val(ui.value);
                parent.$("#borderRadiusBottomLeft").val(ui.value);
                parent.$("#borderRadiusBottomRight").val(ui.value);

                parent.$("#borderRadiusTopLeftSlider").slider('option', 'value', parseInt(ui.value));
                parent.$("#borderRadiusTopRightSlider").slider('option', 'value', parseInt(ui.value));
                parent.$("#borderRadiusBottomLeftSlider").slider('option', 'value', parseInt(ui.value));
                parent.$("#borderRadiusBottomRightSlider").slider('option', 'value', parseInt(ui.value));

                setStyle();
            }
        });
        parent.$("#borderRadius").on('change', function (event) {
            parent.$("#borderRadiusSlider").slider('option', 'value', parseInt(this.value));
        });
        parent.$("#borderRadiusTopLeft").on('change', function (event) {
            parent.$("#borderRadiusTopLeftSlider").slider('option', 'value', parseInt(this.value));
        });
        parent.$("#borderRadiusTopRight").on('change', function (event) {
            parent.$("#borderRadiusTopRightSlider").slider('option', 'value', parseInt(this.value));
        });
        parent.$("#borderRadiusBottomLeft").on('change', function (event) {
            parent.$("#borderRadiusBottomLeftSlider").slider('option', 'value', parseInt(this.value));
        });
        parent.$("#borderRadiusBottomRight").on('change', function (event) {
            parent.$("#borderRadiusBottomRightSlider").slider('option', 'value', parseInt(this.value));
        });
        parent.$("#borderRadiusTopLeftSlider").slider({
            range: "min",
            min: 0,
            max: 250,
            slide: function (event, ui) {
                parent.$("#borderRadiusTopLeft").val(ui.value);
                setStyle();
            }
        });

        parent.$("#borderRadiusTopRightSlider").slider({
            range: "min",
            min: 0,
            max: 250,
            slide: function (event, ui) {
                parent.$("#borderRadiusTopRight").val(ui.value);
                setStyle();
            }
        });

        parent.$("#borderRadiusBottomLeftSlider").slider({
            range: "min",
            min: 0,
            max: 250,
            slide: function (event, ui) {
                parent.$("#borderRadiusBottomLeft").val(ui.value);
                setStyle();
            }
        });

        parent.$("#borderRadiusBottomRightSlider").slider({
            range: "min",
            min: 0,
            max: 250,
            slide: function (event, ui) {
                parent.$("#borderRadiusBottomRight").val(ui.value);
                setStyle();
            }
        });

        parent.$("#borderWidthSlider").slider({
            range: "min",
            min: 0,
            max: 250,
            slide: function (event, ui) {
                parent.$("#borderWidth").val(ui.value);
                setStyle();
            }
        });
        parent.$("#borderWidth").on('change', function (event) {
            parent.$("#borderWidthSlider").slider('option', 'value', parseInt(this.value));
        });

        parent.$("#borderTopWidthSlider").slider({
            range: "min",
            min: 0,
            max: 250,
            slide: function (event, ui) {
                parent.$("#borderTopWidth").val(ui.value);
                setStyle();
            }
        });
        parent.$("#borderTopWidth").on('change', function (event) {
            parent.$("#borderTopWidthSlider").slider('option', 'value', parseInt(this.value));
        });

        parent.$("#borderRightWidthSlider").slider({
            range: "min",
            min: 0,
            max: 250,
            slide: function (event, ui) {
                parent.$("#borderRightWidth").val(ui.value);
                setStyle();
            }
        });
        parent.$("#borderRightWidth").on('change', function (event) {
            parent.$("#borderRightWidthSlider").slider('option', 'value', parseInt(this.value));
        });

        parent.$("#borderBottomWidthSlider").slider({
            range: "min",
            min: 0,
            max: 250,
            slide: function (event, ui) {
                parent.$("#borderBottomWidth").val(ui.value);
                setStyle();
            }
        });
        parent.$("#borderBottomWidth").on('change', function (event) {
            parent.$("#borderBottomWidthSlider").slider('option', 'value', parseInt(this.value));
        });

        parent.$("#borderLeftWidthSlider").slider({
            range: "min",
            min: 0,
            max: 250,
            slide: function (event, ui) {
                parent.$("#borderLeftWidth").val(ui.value);
                setStyle();
            }
        });
        parent.$("#borderLeftWidth").on('change', function (event) {
            parent.$("#borderLeftWidthSlider").slider('option', 'value', parseInt(this.value));
        });

        parent.$("#boxShadowXSlider").slider({
            range: "min",
            min: 0,
            max: 250,
            slide: function (event, ui) {
                parent.$("#boxShadowX").val(ui.value);
                setStyle();
            }
        });
        parent.$("#boxShadowX").on('change', function (event) {
            parent.$("#boxShadowXSlider").slider('option', 'value', parseInt(this.value));
        });

        parent.$("#boxShadowYSlider").slider({
            range: "min",
            min: 0,
            max: 250,
            slide: function (event, ui) {
                parent.$("#boxShadowY").val(ui.value);
                setStyle();
            }
        });
        parent.$("#boxShadowY").on('change', function (event) {
            parent.$("#boxShadowYSlider").slider('option', 'value', parseInt(this.value));
        });

        parent.$("#boxShadowBlurSlider").slider({
            range: "min",
            min: 0,
            max: 250,
            slide: function (event, ui) {
                parent.$("#boxShadowBlur").val(ui.value);
                setStyle();
            }
        });
        parent.$("#boxShadowBlur").on('change', function (event) {
            parent.$("#boxShadowBlurSlider").slider('option', 'value', parseInt(this.value));
        });

        parent.$("#boxShadowSpreadSlider").slider({
            range: "min",
            min: 0,
            max: 250,
            slide: function (event, ui) {
                parent.$("#boxShadowSpread").val(ui.value);
                setStyle();
            }
        });
        parent.$("#boxShadowSpread").on('change', function (event) {
            parent.$("#boxShadowSpreadSlider").slider('option', 'value', parseInt(this.value));
        });

        parent.$("#letterSpacingSlider").slider({
            range: "min",
            min: 0,
            max: 250,
            slide: function (event, ui) {
                parent.$("#letterSpacing").val(ui.value);
                setStyle();
            }
        });
        parent.$("#letterSpacing").on('change', function (event) {
            parent.$("#letterSpacingSlider").slider('option', 'value', parseInt(this.value));
        });

        parent.$("#wordSpacingSlider").slider({
            range: "min",
            min: 0,
            max: 250,
            slide: function (event, ui) {
                parent.$("#wordSpacing").val(ui.value);
                setStyle();
            }
        });
        parent.$("#wordSpacing").on('change', function (event) {
            parent.$("#wordSpacingSlider").slider('option', 'value', parseInt(this.value));
        });

        parent.$("#lineHeightSlider").slider({
            range: "min",
            min: 0,
            max: 250,
            slide: function (event, ui) {
                parent.$("#lineHeight").val(ui.value);
                setStyle();
            }
        });
        parent.$("#lineHeight").on('change', function (event) {
            parent.$("#lineHeightSlider").slider('option', 'value', parseInt(this.value));
        });

        parent.$('#selectors').on('change', function (e) {
            setElementToStyle(parent.$('#selectors:selected').val());
        });
        parent.$('.style-panel').each(function () {
            that = this;
            $(this).on('change', function (e) {
                var slider = parent.$('#' + that.id + 'Slider');
                if (typeof slider !== 'undefined' && slider.length > 0) {
                    $(slider).slider('option', 'value', parseInt($(that).val()));
                }
                setStyle();
            });
        });

        if ($.jStorage.get('currentBlock', null)) {
            renderPanels('#' + $.jStorage.get('currentBlock', ''));
        } else if (parent.$('#selector').val().length > 0) {
            renderPanels(parent.$('#selector').val());
        } else {
            renderPanels('body');
        }
    }
});

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

function renderPanels(el, mainEl) {
    if (typeof el == 'undefined') {
        return false;
    } else {
        if (typeof mainEl == 'undefined' || mainEl.length == 0) {
            mainEl = el;
        }
        parent.$('#selector').val(el);
        parent.$('#mainselector').val(mainEl);

        var cookieName = "";
        cookieName = parent.$('#selector').val().replace(/\s/g, '');
        cookieName = parent.$('#selector').val().replace(/[,#\.]/g, '');
        $.jStorage.set('currentBlock', cookieName);

        parent.$('#el').text(el);
        loadStyle();
        renderPanel();

        /** widget settings **/
        var settings = $(mainEl +'_form');
        if (settings) {
            console.log( settings );
            parent.$('#tabWidgetsSettings')
                .append( settings )
                .find('input, select, textarea')
                .on('change', function(){
                    $.post(settings.attr('action'), settings.serialize())
                    .done(function(r){
                        /* //TODO: show loader with updating message */
                        $.getJSON(window.nt.url_widgets_load.replace('{%widgetModule%}', settings.data('module')),
                        {
                            w:settings.data('id'),
                            theme_editor:1,
                            template:getUrlVars()['template'],
                            theme_id:getUrlVars()['theme_id'],
                            store_id:window.nt.sid,
                            route:window.nt.route
                        }).done(function(r) {
                            $(mainEl).replaceWith( r.html ).show();

                            var loadJS = function(id,js) {
                                var exists = parent.$('script[src~="'+ id +'"]');
                                if (!exists) {
                                    let ex = parent.$('html').text().indexOf('/**script:'+ id +'**/');
                                    exists = (ex>=0) ? true : false;
                                }

                                if (!exists) {
                                    console.log('id',id);
                                    console.log('js',js);
                                    $(document.createElement('script')).attr({
                                        src:js,
                                        type:'text/javascript'
                                    }).appendTo('body');
                                }
                            };

                            var loadScripts = function(id,js) {
                                let ex = parent.$('html').text().indexOf('/**script:'+ id +'**/');
                                var exists = (ex>=0) ? true : false;
                                

                                if (!exists) {
                                    console.log('id',id);
                                    console.log('js',js);
                                    $(document.createElement('script')).attr({
                                        type:'text/javascript'
                                    })
                                    .html(js)
                                    .appendTo('body');
                                }
                            };

                            var loadStyleSheets = function(id,css) {
                                var exists = parent.$('link[href~="'+ id +'"]');
                                if (!exists) {
                                    let ex = parent.$('html').text().indexOf('/**link:'+ id +'**/');
                                    exists = (ex>=0) ? true : false;
                                }
                                
                                if (!exists) {
                                    console.log('id',id);
                                    console.log('js',js);
                                    $(document.createElement('link')).attr({
                                        href:css.href,
                                        media:css.media,
                                        rel:'stylesheet'
                                    }).appendTo('head');
                                }
                            };

                            var loadCSS = function(id,css) {
                                let ex = parent.$('style').text().indexOf('/**'+ id +'**/');
                                var exists = (ex>=0) ? true : false;
                                
                                if (!exists) {
                                    $(document.createElement('style'))
                                    .html(css)
                                    .appendTo('head');
                                }
                            };

                            $(mainEl).find('script').each(function(){
                                loadScripts(settings.data('id') +'Scripts', $(this).html());
                            });

                            if (r.javascrips) {
                                $.each(r.javascrips, (k,v) => {
                                    loadJS(k,v);
                                });
                            }

                            if (r.styles) {
                                $.each(r.styles, (k,v) => {
                                    loadStyleSheets(k,v);
                                });
                            }

                            if (r.css) {
                                $.each(r.css, (k,v) => {
                                    loadCSS(k,v);
                                });
                            }

                            if (r.scripts) {
                                $.each(r.scripts, (k,v) => {
                                    loadScripts(k,v);
                                });
                            }
                        });
                    });
                });
            parent.$('#tabWidgetsSettings')
                .find('form')
                .show()
                .attr({
                    id:''
                });
        }
        /** /widget settings **/
    }
}

function loadStyle() {
    var elements = {};
    elements = $.jStorage.get('elements', false);
    if (elements) {
        $.each(elements, function (el, style) {
            that = parent.$('#' + el);

            if (IsNumber(style.font.size)) {
                style.font.size += 'px';
            }
            if (IsNumber(style.font.letterspacing)) {
                style.font.letterspacing += 'px';
            }
            if (IsNumber(style.font.wordspacing)) {
                style.font.wordspacing += 'px';
            }
            if (IsNumber(style.font.lineheight)) {
                style.font.lineheight += 'px';
            }

            if (IsNumber(style.boxshadow.x)) {
                style.boxshadow.x = parseInt(style.boxshadow.x) + 'px ';
            }
            if (IsNumber(style.boxshadow.y)) {
                style.boxshadow.y = parseInt(style.boxshadow.y) + 'px ';
            }
            if (IsNumber(style.boxshadow.blur)) {
                style.boxshadow.blur = parseInt(style.boxshadow.blur) + 'px ';
            }
            if (IsNumber(style.boxshadow.spread)) {
                style.boxshadow.spread = parseInt(style.boxshadow.spread) + 'px ';
            }

            if (IsNumber(style.margin.all)) {
                style.margin.all += 'px';
            }
            if (IsNumber(style.margin.top)) {
                style.margin.top += 'px';
            }
            if (IsNumber(style.margin.right)) {
                style.margin.right += 'px';
            }
            if (IsNumber(style.margin.bottom)) {
                style.margin.bottom += 'px';
            }
            if (IsNumber(style.margin.left)) {
                style.margin.left += 'px';
            }

            if (IsNumber(style.padding.all)) {
                style.padding.all += 'px';
            }
            if (IsNumber(style.padding.top)) {
                style.padding.top += 'px';
            }
            if (IsNumber(style.padding.right)) {
                style.padding.right += 'px';
            }
            if (IsNumber(style.padding.bottom)) {
                style.padding.bottom += 'px';
            }
            if (IsNumber(style.padding.left)) {
                style.padding.left += 'px';
            }
            
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

function setElementToStyle(el) {
    var ele = parent.$('#mainselector').val();
    var mainEl = ele;
    if (typeof el !== 'undefined' || el.length > 0) {
        if (el == 'subtitle') {
            ele = mainEl + ' h2, ';
            ele += mainEl + ' h3, ';
            ele += mainEl + ' h4, ';
            ele += mainEl + ' h5, ';
            ele += mainEl + ' h6';
        } else if (el !== 'null') {
            ele += ' ' + el;
        } else {
            mainEl = '';
        }
    }
    parent.$('body').css({
        'marginLeft': '20%'
    });
    renderPanels(ele, mainEl);
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

function _IsNumber(_id) {
    if (IsNumber(parent.$('#' + _id).val())) {
        a = parent.$('#' + _id).val();
        parent.$('#' + _id).val(a + 'px ');
    }
}
function IsNumber(n) {
    return !isNaN(parseFloat(n)) && isFinite(n);
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

function printStyle() {
    /** @todo funciones para imprimir el estilo convertir el objeto json a string con estructura ecmas */
}

function rand (min, max) {
    if (!min && !max) {
        min = 0;
        max = 2147483647;
    }
    return Math.floor(Math.random() * (max - min + 1)) + min;
}

function generateGuid() {
    var S4 = function() {
        return (((1+Math.random())*0x10000)|0).toString(16).substring(1);
    };
    return (S4()+S4()+S4());
}

;(function(a){var b=a({});a.ajaxQueue=function(c){function g(b){d=a.ajax(c).done(e.resolve).fail(e.reject).then(b,b)}var d,e=a.Deferred(),f=e.promise();b.queue(g),f.abort=function(h){if(d)return d.abort(h);var i=b.queue(),j=a.inArray(g,i);j>-1&&i.splice(j,1),e.rejectWith(c.context||c,[f,h,""]);return f};return f}})(jQuery);