/**
 *
 * NecoTolltips
 * Author: Yosiet Serga
 * Version: 1.0.0
 * 
 * Dual licensed under the MIT and GPL licenses
 * 
 */
;(function ( $, window, document, undefined ) {
    
    var pluginName = "ntTips",
        defaults = {
            classname:  'neco-tips',
            closeButton: true,
            ajax:       false,
            modal:       true,
            type:       'get',
            dataType:   'json',
            loading:    {
                title:'Cargando...',
                image:'../loader.gif',
                classname:'neco-tips-loading'
            },
            error:      {
                classname:'neco-tips-error',
                text:'Lo sentimos pero no se pudo cargar el contenido',
            },
            start:     function(){},
            stop:     function(){},
            beforeSend: function(){},
            complete:   function(){},
            success:    function(){}
        },
        data = {};

    function _ntTips( element, options ) {
        this.element = element;
        this.options = $.extend( {}, this.defaults, options );
        this._name = pluginName;
        this.init();
    }

    _ntTips.prototype = {
        init: function() {
            this._start();
            this._render();
            this._stop();
        },
        _start: function() {
            if (typeof this.options.start == "function") {
                this.options.start();
            }
        },
        _stop: function() {
            if (typeof this.options.stop == "function") {
                this.options.stop();
            }
        },
        _render: function() {
            this._renderBox();
        },
        _renderBox: function() {
            this.container = $(document.createElement('div'))
            .addClass('neco-tips-container')
            .appendTo('body');
            
            if ($(this.element).data('target')) {
                clone = $($(this.element).data('target')).get(0);
                this.container.html(clone);
            } else if (typeof this.options.target != 'undefined') {
                clone = $(this.options.target).get(0);
                this.container.html(clone);
            } else {
                this.container.html($(this.element).attr('title'));
                $(this.element).removeAttr('title');
            }
            
            
            windowButtons = '<ul class="neco-tips-window-btn">';
            windowButtons += '<li><a class="neco-tips-min-window">_</a></li>';
            windowButtons += '<li><a class="neco-tips-max-window">-</a></li>';
            windowButtons += '<li><a class="neco-tips-close" onclick="$(this).closest(\'.neco-tips-container\').find(\'.neco-tips-container\').hide();$(this).closest(\'.neco-tips-container\').find(\'.neco-tips-modal\').remove();">X</a></li>';
            windowButtons += '</ul>';
            
            $(this.container).append(windowButtons);
            
            if (this.options.modal) {
                $(this.modal).on('click', function(e) {
                    $('.neco-tips-container').hide();
                    $('.neco-tips-modal').remove();
                });
            }
            
            var helper = this._helper;
            var that = this;
            
            $(this.element).on('click',function(e){
                if (that.options.modal) 
                    helper.close(that.container, that.options);
                helper.open(that.container, that.options);
            });
        },
        _helper: {
            close: function(el, options) {
                $('.neco-tips-container').hide();
                $('.neco-tips-modal').remove();
            },
            open: function(el, options) {
                $(el).show();
                    
                if ($(el).hasClass('on')) {
                    $(el).hide();
                    $('.neco-tips-modal').remove();
                } else {
                    if (options.modal) {
                        var modal = $(document.createElement('div'))
                        .addClass('neco-tips-modal')
                        .css({
                            width: $(window).width(),
                            height: $(window).height()
                        })
                        .appendTo('body');
                    }
                }
            }
        }
    };

    $.fn[pluginName] = function ( options ) {
        return this.each(function () {
            if (!$.data(this, "nt_" + pluginName)) {
                $.data(this, "nt_" + pluginName, new _ntTips( this, options ));
            }
        });
    };
})( jQuery, window, document );