/**
 *
 * NecoSlug
 * Author: Yosiet Serga
 * Version: 1.0.0
 * Powered By: carouFredSel (https://caroufredsel.frebsite.nl/)
 * 
 * Dual licensed under the MIT and GPL licenses
 * 
 */
(function($) {
    $.fn.ntSlug = function(method) {
        var defaults = {
            url:        '',
            type:       'get',
            dataType:   'json',
            slug:       'neco-slug',
            target:     'slug',
            create:     function(){},
            beforeSend: function(){},
            stop:       function(){},
            complete:   function(){},
            success:    function(){},
            dblclick:   function(){}
        };
        
        var settings = {};
        var data = {};
        var methods = {
            init : function(options) {
                return this.each(function() {
                    settings = $.extend({}, defaults, options)
                    data.element = $(this);
                    helpers._create();
                    helpers._stop();
                });
            }
        };
 
        var helpers = {
            _create: function() {
                data.slug = data.element.val();
    			data.slug = data.slug.replace(/\s/g,'-');
    			data.slug = data.slug.replace(/^[��]/g,'a');
    			data.slug = data.slug.replace(/^[��]/g,'e');
    			data.slug = data.slug.replace(/^[��]/g,'i');
    			data.slug = data.slug.replace(/^[��]/g,'o');
    			data.slug = data.slug.replace(/^[����]/g,'u');
    			data.slug = data.slug.replace(/^[��]/g,'n');
    			data.slug = data.slug.replace(/[^a-zA-Z0-9\-]/g,'');
                
                
                $.ajax({
                        type:settings.type,
                        dataType:settings.dataType,
                        url:settings.url,
                        data: {
                          slug:data.slug
                        },
                        beforeSend:function() {
                            helpers._beforeSend();
                        },
                        complete:function() {
                            helpers._complete();
                        },
                        success:function(json) {
                            helpers._success(json);
                        }
                    });
                    
                if (typeof settings.create == 'function') {
                    settings.create();
                }
            },
            _beforeSend: function() {
                if (typeof settings.beforeSend == "function") {
                    settings.beforeSend();
                }
            },
            _complete: function() {
                console.log(settings.slug);
        			$('#' + settings.target).val(data.slug.toLowerCase());
        			$('#' + settings.slug).text(data.slug.toLowerCase());
                if (typeof settings.complete == "function") {
                    settings.complete();
                }
            },
            _success: function(json) {
                    $(settings.slug).on('dblclick',helpers._dblclick);
        			$('#' + settings.target).val(data.slug.toLowerCase());
        			$('#' + settings.slug).text(data.slug.toLowerCase());
                if (json.error==1) {
                    $(data.container).html($(document.createElement('div')).addClass(settings.error.classname).text(settings.error.text));
                } else {
                    if (typeof settings.success == "function") {
                        settings.success();
                    }
                }
            },
            _stop: function() {
                if (typeof settings.stop == "function") {
                    settings.stop();
                }
            },
            _dblclick: function() {
                console.log(settings.target);
                $(settings.target).attr({type:'text'});
                if (typeof settings.dblclick == "function") {
                    settings.dblclick(this,data.li);
                }
            },
            count: function(obj) {
                var size = 0, key;
                for (key in obj) {
                    if (obj.hasOwnProperty(key)) size++;
                }
                return size;
            }
        };
        
        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || !method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error( 'Method "' +  method + '" does not exist in ntSlug plugin!');
        }
    }
})(jQuery);