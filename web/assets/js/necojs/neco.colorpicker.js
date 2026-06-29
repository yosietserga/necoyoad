/**
 *
 * NecoColorPicker
 * Author: Yosiet Serga
 * Version: 1.0.3
 * Powered By: Color Picker (Stefan Petre www.eyecon.ro/colorpicker)
 * Powered By: Farbtastic Color Picker (https://acko.net/blog/farbtastic-jquery-color-picker-plug-in)
 * 
 * Dual licensed under the MIT and GPL licenses
 * 
 */
(function($) {
    $.fn.ntColorPicker = function(method) {
        var defaults = {
            target:     null,
            tpl:        '<div class="neco-color-picker-launch"></div><div class="neco-color-picker-sample"></div><div class="neco-color-picker-wrapper"><div class="neco-color-picker"><div class="color"></div><div class="wheel"></div><div class="overlay"></div><div class="h-marker marker"></div><div class="sl-marker marker"></div></div><div class="neco-color-picker-buttons"><a class="neco-color-picker-submit"></a><a class="neco-color-picker-cancel"></a></div></div>',
            type:       'rgb',
            textSubmit: 'OK',
            textCancel:  'Cancelar',
            create:     function(){},
            start:      function(){},
            stop:       function(){},
            change:     function(){},
            update:     function(){},
            cancel:     function(){}
        }
        
        var targetEl;
        var settings = {};
        var data = {};
        
        var methods = {
            init : function(options) {
                return this.each(function() {
                    settings = $.extend({}, defaults, options)
                    data.element = $(this);
                    data.container = $(this).get(0);
                    
                    data.target = settings.target;
                    targetEl = $(settings.target).get(0);
                    helpers._create();
                    
                    data.wheel = $('.wheel', data.container).get(0);
                    // Dimensions
                    data.radius = 84;
                    data.square = 100;
                    data.width = 194;
                    // Fix background PNGs in IE6
                      if (navigator.appVersion.match(/MSIE [0-6]\./)) {
                        $('*', e).each(function () {
                          if (this.currentStyle.backgroundImage != 'none') {
                            var image = this.currentStyle.backgroundImage;
                            image = this.currentStyle.backgroundImage.substring(5, image.length - 2);
                            $(this).css({
                              'backgroundImage': 'none',
                              'filter': "progid:DXImageTransform.Microsoft.AlphaImageLoader(enabled=true, sizingMethod=crop, src='" + image + "')"
                            });
                          }
                        });
                      }
                    // Install mousedown handler (the others are set on the document on-demand)
                      $('*', data.e).mousedown(helpers._mousedown);
                    
                        // Init color
                      helpers._setColor('#000000');
                    
                      // Set linked elements/callback
                      if (settings.target) {
                        helpers._linkTo();
                      }
                    
                    data.element.on('mousedown',function(event){
                        if (typeof settings.start == 'function') {
                            $(targetEl).val(data.element.text());
                            settings.start();
                            
                        }
                    });
                    
                    data.element.on('mouseup',function(event){
                        if (typeof settings.start == 'function') {
                            settings.stop(data.element);
                        }
                    });
                });
            },
            open: function() {
                $(data.element).find('.neco-color-picker-wrapper').fadeIn();
            },
            close: function() {
                $(data.element).find('.neco-color-picker-wrapper').fadeOut();
            }
        }
 
        var helpers = {
            _create: function() {
                // reemplazar contenedor
                if (data.element.length > 0 ) {
                    data.element.html(settings.tpl);
                }
                if (!data.target) {
                    data.target = $(document.createElement("input"))
                        .attr({
                            'type'  :'hidden',
                            'name'  :'neco-color-picker-value',
                            'id'    :'neco-color-picker-value',
                            'value' :''
                        });
                    $(data.element).after(data.target);
                }
                data.e = $('.neco-color-picker', data.container);
                data.wrapper = $(data.element).find('.neco-color-picker-wrapper');
                $(data.element).find(".neco-color-picker-submit").text(settings.textSubmit).on('click', function() {
                    $(data.wrapper).fadeOut();
                    helpers._update();
                });
                $(data.element).find(".neco-color-picker-cancel").text(settings.textCancel).on('click', function() {
                    $(data.wrapper).fadeOut();
                    helpers._cancel();
                });
                launcher = $(data.element).find(".neco-color-picker-launch").get(0);
                $(launcher).on('click',function(){
                    console.log('ColorPicker Launched');
                    if ($(this).hasClass('neco-color-picker-selected')) {
                        $(this).removeClass('neco-color-picker-selected');
                    }
                    $(this).addClass('neco-color-picker-selected');
                    $(data.wrapper).fadeToggle();
                });
                if (settings.type == 'rgba') {
                    var slider = $(document.createElement("div")).attr({'class':'neco-color-picker-transparency'});
                    $(data.wrapper).append(slider);
                    $(slider).slider({
                        range: 'min',
                        min:0,
                        max:10,
                        value:10,
                        orientation:'vertical',
                        slide:function(event, ui) {
                            data.transparency = ui.value;
                            helpers._change();
            			}
                    });
                }
                if (typeof settings.create == 'function') {
                    settings.create();
                }
 
            },
            _linkTo: function () {
                // Unbind previous nodes
                if (typeof data.target == 'object') {
                  $(data.target).off('keyup', helpers._updateValue);
                }
            
                // Reset color
                data.color = null;
            
                // Bind callback or elements
                if (typeof data.target == 'object' || typeof data.target == 'string') {
                  data.target = $(data.target);
                  data.target.off('keyup', helpers._updateValue);
                  if (data.target.get(0).value) {
                    helpers._setColor(data.target.get(0).value);
                  }
                }
                return this;
            },
            _updateValue: function(event) {
                if (this.value && this.value != data.color) {
                    helpers._setColor(this.value);
                }
            },
            _setColor: function(color) {
                var unpack = helpers._unpack(color);
                if (data.color != color && unpack) {
                  data.color = color;
                  data.rgb = unpack;
                  data.hsl = helpers._RGBToHSL(data.rgb);
                  helpers._updateDisplay();
                }
                return this;
            },
            _setHSL: function (hsl) {
                data.hsl = hsl;
                data.rgb = helpers._HSLToRGB(hsl);
                data.color = helpers._pack(data.rgb);
                helpers._updateDisplay();
                return this;
            },
            _widgetCoords: function (event) {
                var offset = $(data.wheel).offset();
                return { x: (event.pageX - offset.left) - data.width / 2, y: (event.pageY - offset.top) - data.width / 2 };
            },
            _mousedown: function (event) {
                // Capture mouse
                if (!document.dragging) {
                  $(document).on('mousemove', helpers._mousemove).on('mouseup', helpers._mouseup);
                  document.dragging = true;
                }
            
                // Check which area is being dragged
                var pos = helpers._widgetCoords(event);
                data.circleDrag = Math.max(Math.abs(pos.x), Math.abs(pos.y)) * 2 > data.square;
            
                helpers._start();
                
                // Process
                helpers._mousemove(event);
                return false;
            },
            _mousemove: function (event) {
                // Get coordinates relative to color picker center
                var pos = helpers._widgetCoords(event);
            
                // Set new HSL parameters
                if (data.circleDrag) {
                  var hue = Math.atan2(pos.x, -pos.y) / 6.28;
                  if (hue < 0) hue += 1;
                  helpers._setHSL([hue, data.hsl[1], data.hsl[2]]);
                }
                else {
                  var sat = Math.max(0, Math.min(1, -(pos.x / data.square) + .5));
                  var lum = Math.max(0, Math.min(1, -(pos.y / data.square) + .5));
                  helpers._setHSL([data.hsl[0], sat, lum]);
                }
                
                helpers._change();
                
                return false;
            },
            _mouseup: function () {
                $(document).off('mousemove', helpers._mousemove);
                $(document).off('mouseup', helpers._mouseup);
                document.dragging = false;
                helpers._stop();
            },
            _start: function() {
                if (typeof settings.start == "function") {
                    
                    if (settings.type == 'hex') {
                        $(data.target).val(data.color);  
                        settings.start(data.color);
                    } else if (settings.type == 'hsl') {
                        //TODO: fix hsl colors 
                        /*
                        var hsb = helpers._fixHSB(helpers._HexToHSB(data.color));
                        settings.stop("hsl(" + hsb.h + "," + hsb.s + "%," + hsb.b + "%)");
                        $(data.target).val("hsl(" + hsb.h + "," + hsb.s + "%," + hsb.b + "%)"); 
                        */
                    } else if (settings.type == 'rgba') {
                        var rgb = helpers._HexToRGB(data.color);
                        transparency = data.transparency / 10;
                        if (!transparency && transparency != 0) {transparency = 1}
                        settings.start("rgba(" + rgb.r + "," + rgb.g + "," + rgb.b + "," + transparency + ")");
                        $(data.target).val("rgba(" + rgb.r + "," + rgb.g + "," + rgb.b + "," + transparency + ")");
                    } else {
                        var rgb = helpers._HexToRGB(data.color);
                        settings.start("rgb(" + rgb.r + "," + rgb.g + "," + rgb.b + ")");
                        $(data.target).val("rgb(" + rgb.r + "," + rgb.g + "," + rgb.b + ")");  
                    }
                }
            },
            _change: function() {
                if (typeof settings.change == "function") {
                    
                    if (settings.type == 'hex') {
                        $(data.target).val(data.color);  
                        settings.change(data.color);
                        $(data.element).find('.neco-color-picker-sample').css({ background:data.color });
                    } else if (settings.type == 'hsl') {
                        //TODO: fix hsl colors 
                        /*
                        var hsb = helpers._fixHSB(helpers._HexToHSB(data.color));
                        settings.stop("hsl(" + hsb.h + "," + hsb.s + "%," + hsb.b + "%)");
                        $(data.target).val("hsl(" + hsb.h + "," + hsb.s + "%," + hsb.b + "%)"); 
                        */
                    } else if (settings.type == 'rgba') {
                        var rgb = helpers._HexToRGB(data.color);
                        transparency = data.transparency / 10;
                        if (!transparency && transparency != 0) {transparency = 1}
                        settings.change("rgba(" + rgb.r + "," + rgb.g + "," + rgb.b + "," + transparency + ")");
                        $(data.target).val("rgba(" + rgb.r + "," + rgb.g + "," + rgb.b + "," + transparency + ")");
                        $(data.element).find('.neco-color-picker-sample').css({ background:"rgba(" + rgb.r + "," + rgb.g + "," + rgb.b + "," + transparency + ")" });
                    } else {
                        var rgb = helpers._HexToRGB(data.color);
                        settings.change("rgb(" + rgb.r + "," + rgb.g + "," + rgb.b + ")");
                        $(data.target).val("rgb(" + rgb.r + "," + rgb.g + "," + rgb.b + ")");
                        $(data.element).find('.neco-color-picker-sample').css({ background:"rgb(" + rgb.r + "," + rgb.g + "," + rgb.b + ")" });
                    }
                }
            },
            _update: function() {
                if (typeof settings.update == "function") {
                    if (settings.type == 'hex') {
                        $(data.target).val(data.color);  
                        settings.update(data.color);
                    } else if (settings.type == 'hsl') {
                        //TODO: fix hsl colors 
                        /*
                        var hsb = helpers._fixHSB(helpers._HexToHSB(data.color));
                        settings.stop("hsl(" + hsb.h + "," + hsb.s + "%," + hsb.b + "%)");
                        $(data.target).val("hsl(" + hsb.h + "," + hsb.s + "%," + hsb.b + "%)"); 
                        */
                    } else if (settings.type == 'rgba') {
                        var rgb = helpers._HexToRGB(data.color);
                        transparency = data.transparency / 10;
                        if (!transparency && transparency != 0) {transparency = 1}
                        settings.update("rgba(" + rgb.r + "," + rgb.g + "," + rgb.b + "," + transparency + ")");
                        $(data.target).val("rgba(" + rgb.r + "," + rgb.g + "," + rgb.b + "," + transparency + ")");
                    } else {
                        var rgb = helpers._HexToRGB(data.color);
                        settings.update("rgb(" + rgb.r + "," + rgb.g + "," + rgb.b + ")");
                        $(data.target).val("rgb(" + rgb.r + "," + rgb.g + "," + rgb.b + ")");  
                    }
                }
            },
            _stop: function() {
                if (typeof settings.stop == "function") {
                    if (settings.type == 'hex') {
                        $(data.target).val(data.color);  
                        settings.stop(data.color);
                    } else if (settings.type == 'hsl') {
                        //TODO: fix hsl colors 
                        /*
                        var hsb = helpers._fixHSB(helpers._HexToHSB(data.color));
                        settings.stop("hsl(" + hsb.h + "," + hsb.s + "%," + hsb.b + "%)");
                        $(data.target).val("hsl(" + hsb.h + "," + hsb.s + "%," + hsb.b + "%)"); 
                        */
                    } else if (settings.type == 'rgba') {
                        var rgb = helpers._HexToRGB(data.color);
                        transparency = data.transparency / 10;
                        if (!transparency && transparency != 0) {transparency = 1}
                        settings.change("rgba(" + rgb.r + "," + rgb.g + "," + rgb.b + "," + transparency + ")");
                        $(data.target).val("rgba(" + rgb.r + "," + rgb.g + "," + rgb.b + "," + transparency + ")");
                    } else {
                        var rgb = helpers._HexToRGB(data.color);
                        settings.stop("rgb(" + rgb.r + "," + rgb.g + "," + rgb.b + ")");
                        $(data.target).val("rgb(" + rgb.r + "," + rgb.g + "," + rgb.b + ")");  
                    }
                }
            },
            _cancel: function() {
                if (typeof settings.cancel == "function") {
                    settings.cancel();
                }
            },
            _updateDisplay: function () {
                // Markers
                var angle = data.hsl[0] * 6.28;
                $('.h-marker', data.e).css({
                  left: Math.round(Math.sin(angle) * data.radius + data.width / 2) + 'px',
                  top: Math.round(-Math.cos(angle) * data.radius + data.width / 2) + 'px'
                });
            
                $('.sl-marker', data.e).css({
                  left: Math.round(data.square * (.5 - data.hsl[1]) + data.width / 2) + 'px',
                  top: Math.round(data.square * (.5 - data.hsl[2]) + data.width / 2) + 'px'
                });
            
                // Saturation/Luminance gradient
                $('.color', data.e).css('backgroundColor', helpers._pack(helpers._HSLToRGB([data.hsl[0], 1, 0.5])));
            },
            _absolutePosition: function (el) {
                var r = { x: el.offsetLeft, y: el.offsetTop };
                // Resolve relative to offsetParent
                if (el.offsetParent) {
                  var tmp = helpers._absolutePosition(el.offsetParent);
                  r.x += tmp.x;
                  r.y += tmp.y;
                }
                return r;
            },
            _pack: function (rgb) {
                var r = Math.round(rgb[0] * 255);
                var g = Math.round(rgb[1] * 255);
                var b = Math.round(rgb[2] * 255);
                return '#' + (r < 16 ? '0' : '') + r.toString(16) +
                       (g < 16 ? '0' : '') + g.toString(16) +
                       (b < 16 ? '0' : '') + b.toString(16);
            },
            _unpack: function (color) {
                if (color.length == 7) {
                  return [parseInt('0x' + color.substring(1, 3)) / 255,
                    parseInt('0x' + color.substring(3, 5)) / 255,
                    parseInt('0x' + color.substring(5, 7)) / 255];
                }
                else if (color.length == 4) {
                  return [parseInt('0x' + color.substring(1, 2)) / 15,
                    parseInt('0x' + color.substring(2, 3)) / 15,
                    parseInt('0x' + color.substring(3, 4)) / 15];
                }
            },
            _HSLToRGB: function (hsl) {
                var m1, m2, r, g, b;
                var h = hsl[0], s = hsl[1], l = hsl[2];
                m2 = (l <= 0.5) ? l * (s + 1) : l + s - l*s;
                m1 = l * 2 - m2;
                return [helpers._hueToRGB(m1, m2, h+0.33333),
                    helpers._hueToRGB(m1, m2, h),
                    helpers._hueToRGB(m1, m2, h-0.33333)];
            },
            _hueToRGB: function (m1, m2, h) {
                h = (h < 0) ? h + 1 : ((h > 1) ? h - 1 : h);
                if (h * 6 < 1) return m1 + (m2 - m1) * h * 6;
                if (h * 2 < 1) return m2;
                if (h * 3 < 2) return m1 + (m2 - m1) * (0.66666 - h) * 6;
                return m1;
            },
            _RGBToHSL: function (rgb) {
                var min, max, delta, h, s, l;
                var r = rgb[0], g = rgb[1], b = rgb[2];
                min = Math.min(r, Math.min(g, b));
                max = Math.max(r, Math.max(g, b));
                delta = max - min;
                l = (min + max) / 2;
                s = 0;
                if (l > 0 && l < 1) {
                  s = delta / (l < 0.5 ? (2 * l) : (2 - 2 * l));
                }
                h = 0;
                if (delta > 0) {
                  if (max == r && max != g) h += (g - b) / delta;
                  if (max == g && max != b) h += (2 + (b - r) / delta);
                  if (max == b && max != r) h += (4 + (r - g) / delta);
                  h /= 6;
                }
                return [h, s, l];
            },
			_fixHSB: function (hsb) {
				return {
					h: Math.min(360, Math.max(0, hsb.h)),
					s: Math.min(100, Math.max(0, hsb.s)),
					b: Math.min(100, Math.max(0, hsb.b))
				};
			}, 
			_fixRGB: function (rgb) {
				return {
					r: Math.min(255, Math.max(0, rgb.r)),
					g: Math.min(255, Math.max(0, rgb.g)),
					b: Math.min(255, Math.max(0, rgb.b))
				};
			},
			_fixHex: function (hex) {
				var len = 6 - hex.length;
				if (len > 0) {
					var o = [];
					for (var i=0; i<len; i++) {
						o.push('0');
					}
					o.push(hex);
					hex = o.join('');
				}
				return hex;
			},
            _HexToRGB: function (hex) {
				var hex = parseInt(((hex.indexOf('#') > -1) ? hex.substring(1) : hex), 16);
				return {r: hex >> 16, g: (hex & 0x00FF00) >> 8, b: (hex & 0x0000FF)};
			},
			_HexToHSB: function (hex) {
				return helpers._RGBToHSB(helpers._HexToRGB(hex));
			},
			_RGBToHSB: function (rgb) {
				var hsb = {
					h: 0,
					s: 0,
					b: 0
				};
				var min = Math.min(rgb.r, rgb.g, rgb.b);
				var max = Math.max(rgb.r, rgb.g, rgb.b);
				var delta = max - min;
				hsb.b = max;
				if (max != 0) {
					
				}
				hsb.s = max != 0 ? 255 * delta / max : 0;
				if (hsb.s != 0) {
					if (rgb.r == max) {
						hsb.h = (rgb.g - rgb.b) / delta;
					} else if (rgb.g == max) {
						hsb.h = 2 + (rgb.b - rgb.r) / delta;
					} else {
						hsb.h = 4 + (rgb.r - rgb.g) / delta;
					}
				} else {
					hsb.h = -1;
				}
				hsb.h *= 60;
				if (hsb.h < 0) {
					hsb.h += 360;
				}
				hsb.s *= 100/255;
				hsb.b *= 100/255;
				return hsb;
			},
			_HSBToRGB: function (hsb) {
				var rgb = {};
				var h = Math.round(hsb.h);
				var s = Math.round(hsb.s*255/100);
				var v = Math.round(hsb.b*255/100);
				if(s == 0) {
					rgb.r = rgb.g = rgb.b = v;
				} else {
					var t1 = v;
					var t2 = (255-s)*v/255;
					var t3 = (t1-t2)*(h%60)/60;
					if(h==360) h = 0;
					if(h<60) {rgb.r=t1;	rgb.b=t2; rgb.g=t2+t3}
					else if(h<120) {rgb.g=t1; rgb.b=t2;	rgb.r=t1-t3}
					else if(h<180) {rgb.g=t1; rgb.r=t2;	rgb.b=t2+t3}
					else if(h<240) {rgb.b=t1; rgb.r=t2;	rgb.g=t1-t3}
					else if(h<300) {rgb.b=t1; rgb.g=t2;	rgb.r=t2+t3}
					else if(h<360) {rgb.r=t1; rgb.g=t2;	rgb.b=t1-t3}
					else {rgb.r=0; rgb.g=0;	rgb.b=0}
				}
				return {r:Math.round(rgb.r), g:Math.round(rgb.g), b:Math.round(rgb.b)};
			},
			_RGBToHex: function (rgb) {
				var hex = [
					rgb.r.toString(16),
					rgb.g.toString(16),
					rgb.b.toString(16)
				];
				$.each(hex, function (nr, val) {
					if (val.length == 1) {
						hex[nr] = '0' + val;
					}
				});
				return hex.join('');
			},
			_HSBToHex: function (hsb) {
				return helpers._RGBToHex(helpers._HSBToRGB(hsb));
			}
        }
        
        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || !method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error( 'Method "' +  method + '" does not exist in ntColorPicker plugin!');
        }
    }
})(jQuery);