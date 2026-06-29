/**
 *
 * NecoCSS
 * Author: Yosiet Serga
 * Version: 1.0.0
 * Powered By: CopyCSS (https://github.com/moagrius/copycss)
 * 
 * Dual licensed under the MIT and GPL licenses
 * 
 */
;(function ( $, window, document, undefined ) {
    
    var pluginName = "ntCSS",
        defaults = {};

    function _ntCss( element, options ) {
        this.element = element;
        this.options = $.extend( {}, defaults, options );
        this._defaults = defaults;
        this._name = pluginName;
        this.init();
    }

    _ntCss.prototype = {

        init: function() {
    	
        }
    };

    $.fn[pluginName] = function ( options ) {
        return this.each(function () {
            if (!$.data(this, "nt_" + pluginName)) {
                $.data(this, "nt_" + pluginName, new _ntCss( this, options ));
            }
        });
    };

	$.fn.getStyles = function(only, except){
		
		var product = {};
		
		var style;
		
		var name;
		
		if(only && only instanceof Array){
			
			for(var i = 0, l = only.length; i < l; i++){
				name = only[i];
				product[name] = this.css(name);
			}
			
		} else {
			var dom = this.get(0);
			
			if (window.getComputedStyle) {
				
				var pattern = /\-([a-z])/g;
				var uc = function (a, b) {
						return b.toUpperCase();
				};			
				var camelize = function(string){
					return string.replace(pattern, uc);
				};
				if (dom) {
				    style = window.getComputedStyle(dom, null)
				}
				if (style) {
					var camel, value;
					if (style.length) {
						for (var i = 0, l = style.length; i < l; i++) {
							name = style[i];
							camel = camelize(name);
							value = style.getPropertyValue(name);
							product[camel] = value;
						}
					} else {
						for (name in style) {
							camel = camelize(name);
							value = style.getPropertyValue(name) || style[name];
							product[camel] = value;
						}
					}
				}
			}
			else if (style = dom.currentStyle) {
    				    console.log(style);
				for (name in style) {
					product[name] = style[name];
				}
			}
			else if (style = dom.style) {
    				    console.log(style);
				for (name in style) {
					if (typeof style[name] != 'function') {
						product[name] = style[name];
					}
				}
			}
		}
		
		if(except && except instanceof Array){
			for(var i = 0, l = except.length; i < l; i++){
				name = except[i];
				delete product[name];
			}
		}
		
		return product;
	
	};
	
	$.fn.copyCSS = function(source, only, except){
		var styles = $(source).ntGetStyles({
		  'only':only,
          'except':except
        });
		this.css(styles);
	};
	
})( jQuery, window, document );