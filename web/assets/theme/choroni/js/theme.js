$(function(){
    $('[data-input="search"], #filterKeyword').on('keydown',function(e){
        var code = e.keyCode || e.which;
        if ($(this).val().length > 0 && code === 13) {
            moduleSearch($(this));
        }
    });

    var attachFastClick = Origami.fastclick;
    attachFastClick(document.body);

    $('img.nt-lazyload').lazyload();

    var $filters = $(".filter-options").addClass("inverse");
    setUpScrollbar($filters);
    
    loadNTPlugins();
    
    $('[data-sticky]').each(function(){ 
        addSticky(this);
    });

    $('[data-animate]').each(function(){ 
        if ($(this)!==$(window) && $(this)!==$(document) && $(this)!==$('html')) animateTransition(this);
    });

    const isShrinked = () => {
        const elements = document.querySelectorAll('.shrinkable');
        Array.from(elements).map(e => {
            const width = e.offsetWidth;
            const compareWidth = $(e).data("shrink") ?? 200;

            (width < compareWidth) ? 
                $(e).addClass("shrinked") : 
                $(e).removeClass("shrinked");
        })
    };
    isShrinked();
    $(window).on("resize", isShrinked);
});



function addSticky(el, parent) {
    if (typeof parent === 'undefined') parent = 'body';
    var $el = $(el);
    var pos = $el.offset();
    var $parent = $(parent);
    var paddingTop = parseInt($parent.css('paddingTop'), 10);
    var marginBottom = parseInt($parent.css('marginBottom'), 10);

    $parent.on('scroll', function(){
        $('#'+ $el.attr('id') +'_cloned').remove();
        if($(this).scrollTop() > pos.top && !$el.hasClass('sticky')){

            let $elCloned = $(el).clone();
            $parent.addClass('stickyWrapper');

            const marginTop = parseInt($el.css('marginTop'), 10);

            let h = 0;
            $('.sticky').each(function(){
                h = ( h + parseInt( $(this).height() ) );
            });
            
            $elCloned.html(' ');
            $elCloned.attr({ 
                id:$el.attr('id') +'_cloned'
            });
            $elCloned.css({ 
                height:$el.height() +'px',
                width:$el.width() +'px',
                display:'block'
            });

            $el.after( $elCloned );
            $el.addClass('sticky');
            $el.attr('data-margin_top', marginTop);

            $el.css('marginTop', ( h + marginTop ) +'px');
        } else if ($(this).scrollTop() <= pos.top && $el.hasClass('sticky')) {
            $parent.removeClass('stickyWrapper');

            $el.removeClass('sticky');
            $el.css('marginTop', $el.attr('data-margin_top') +'px');
            $el.removeAttr('data-margin_top');
        }
    });
}

function loadNTPlugins() {

    if (typeof window.ntPlugins != 'undefined') {
        $.each(window.ntPlugins, function(i, item){
            if (item && ($[item.plugin] || $.fn[item.plugin])) {
                if (typeof item.fn != 'undefined' && typeof item.fn == 'function') {
                    item.fn( $(item.id)[item.plugin](item.config), $(item.id) );
                } else {
                    $(item.id)[item.plugin](item.config);
                }
            }
            window.ntPlugins[i] = null;
        });
    }

}

function resetAnimation(el) {
    var that = $(el);
    that.removeClass('animated');
    let effects = that.attrWithFilter('effect');
    for (let i in effects) {
        if (isNaN(i)) continue;
        that.removeClass('animated-'+ i);
        that.removeClass(effects[i]);
    }
    that.children('div').hide();
}

$.fn.attrWithFilter = function(__filter){
    return [].slice.call(this.get(0).attributes).filter(function(attr) {
        return attr && attr.name && attr.name.indexOf(__filter) != -1
    });
};

async function animateTransition(el, now=null) {
    const getTransitions = el => {
        let that = $(el);

        let effects = that.attrWithFilter('effect');
        let transitions = [];

        for (let i in effects) {
            if (isNaN(i)) continue;

            transitions.push({
                effect:that.data('transition_'+ i +'_effect'),
                delay:that.data('transition_'+ i +'_delay'),
                duration:that.data('transition_'+ i +'_duration'),
                beforeStart:that.data('transition_'+ i +'_beforeStart'),
                onStart:that.data('transition_'+ i +'_onStart'),
                onStop:that.data('transition_'+ i +'_onStop')
            });
        }

        if (typeof window.transitions == 'undefined') window.transitions = {};
        if (typeof window.transitions[ that.attr('id') ] == 'undefined') window.transitions[ that.attr('id') ] = {};
        window.transitions[ that.attr('id') ].transitions = transitions;
        return transitions;
    };

    const __animate = async function(el, now=null) {
        let that = $(el);
        
        addScript('vendor/jquery-ui.min.js');

        let visible = isElementInViewport(el);
        let transitions = getTransitions(el);

        if (visible || now) {
            let totalDelay = 0;
            let queueTransitions= [];
            for (let i in transitions) {
                let transition = transitions[i];
                that.removeClass( transition.effect );

                if (that.data('repeat')) {
                    // si es la 'ultima transiion, eliminar todas las clases'
                    that.removeClass('animated-'+i);
                }

                if (!that.hasClass('animated-'+i)) {
                    that.addClass('animated');
                    that.addClass('animated-'+i);

                    /*
                    //TODO: setInterval for repeat including in and out transition in one single timeline
                    */
                    
                    let delay = (typeof transition.delay == 'undefined') ? 0.5 : transition.delay;
                    let duration = (typeof transition.duration == 'undefined') ? 1 : transition.duration;

                    console.log('beforeStart');
                    queueTransitions.push({
                        effect:transition.effect,
                        delay,
                        duration
                    });
                }
            }

            let prevEffect;
            for (let i in queueTransitions) {
                const { delay, duration, effect } = queueTransitions[i];
                
                that
                .queue(function(next){
                    that.removeClass( prevEffect );
                    console.log('onStart');
                    next();
                })
                //.delay( delay  * 1000 )
                .queue(function(next){
                    if (i==0) that.find('> div').show();
                    next();
                })                    
                .addClass(effect, duration * 1000, function(){
                    prevEffect = effect;
                    console.log('onStop');
                })                
                //.delay( delay  * 1000 )
                .queue(function(next){
                    if (!that.data('repeat') && (i*1+1)==queueTransitions.length && effect.toLowerCase().indexOf('out') != -1)  {
                        that.fadeOut(function(){
                            that.remove();
                        });
                    }

                    next();
                });
            }
        }
    }

    if (now) {
        __animate(el, now);
    } else {
        $(window).on('DOMContentLoaded load resize scroll', function (e) {
            __animate(el);
        });
    }
}

function addScript(uri) {
    if ($('script[src="'+ window.nt.http_theme_js + uri +'"]').length !== 0) return false;
    $('<script>').attr({
        src:window.nt.http_theme_js + uri
    }).appendTo('body');
}

function isElementInViewport (el) {

    if (typeof jQuery === "function" && el instanceof jQuery) {
        el = el[0];
    }

    var rect = el.getBoundingClientRect();

    return (
        rect.top >= 0 &&
        rect.left >= 0 &&
        rect.bottom <= $(window).height() &&
        rect.right <= $(window).width()
    );
}

var ICONS;
var DATA_SOCIAL_BUTTONS;
var OVERLAY_INITIAL_STYLE;
var OVERLAY_INITIAL_ATTRIBUTES;
var BODY_OVERLAY_CREATED_STYLE;
var BODY_OVERLAY_DESTROYED_STYLE;
var DOM;

var initCatalog;
var slideContent;
var crateShareButtonMarkup;
var createOverlay;
var appendOverlay;
var destroyOverlay;
var onTransitionEnd;
var safeEvent;

/*------------------
Constants
--------------------*/
DOM = Object.freeze({
    $root: $("html"),
    $body: $("body")
});

ICONS = Object.freeze({
    arrowDown: '<svg xmlns="https://www.w3.org/2000/svg" width="10" height="16" viewBox="0 0 10 16"><path d="M9.598 6.57q0 .117-.09.206l-4.16 4.16q-.09.09-.205.09t-.205-.09l-4.16-4.16q-.09-.09-.09-.205t.09-.204l.445-.446q.09-.09.205-.09t.205.09l3.51 3.51L8.65 5.92q.09-.09.206-.09t.205.09l.447.446q.09.09.09.205z" fill="#444"/></svg>',
    loader: '<svg xmlns="https://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16"><path d="M10.833 10.16q.27 0 .47.2l1.884 1.884q.198.198.198.474 0 .27-.198.47t-.47.197q-.275 0-.473-.198l-1.885-1.885q-.194-.193-.194-.474 0-.276.195-.47t.472-.196zm-5.66 0q.275 0 .47.196t.195.47-.198.475l-1.885 1.886q-.198.198-.47.198-.275 0-.47-.195t-.195-.472q0-.28.193-.474l1.885-1.885q.198-.2.474-.2zm-3.84-2.827H4q.276 0 .47.195t.196.47-.195.472-.47.195H1.333q-.276 0-.47-.195T.666 8t.195-.472.47-.195zm6.667 4q.276 0 .47.195t.196.47v2.668q0 .276-.195.47t-.47.196-.47-.195-.196-.47v-2.668q0-.277.195-.472t.47-.195zM3.286 2.615q.27 0 .47.198L5.64 4.698q.198.198.198.47 0 .275-.195.47t-.47.195q-.282 0-.475-.193L2.813 3.755q-.193-.193-.193-.474 0-.275.195-.47t.47-.195zM12 7.333h2.667q.276 0 .47.195t.196.47-.195.472-.47.195H12q-.276 0-.47-.195T11.333 8t.195-.472.47-.195zM8 .667q.276 0 .47.195t.196.47V4q0 .276-.195.47T8 4.667t-.47-.195T7.333 4V1.333q0-.276.195-.47T8 .666zm4.72 1.948q.27 0 .468.198t.198.47q0 .275-.198.473L11.303 5.64q-.193.194-.47.194-.285 0-.476-.19t-.19-.477q0-.276.193-.47l1.885-1.884q.198-.198.474-.198z" fill="#444"/></svg>',
    plus: '<svg xmlns="https://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16"><path d="M15.5 6H10V.5c0-.276-.224-.5-.5-.5h-3c-.276 0-.5.224-.5.5V6H.5c-.276 0-.5.224-.5.5v3c0 .276.224.5.5.5H6v5.5c0 .276.224.5.5.5h3c.276 0 .5-.224.5-.5V10h5.5c.276 0 .5-.224.5-.5v-3c0-.276-.224-.5-.5-.5z" fill="#444"/></svg>',
    minus: '<svg xmlns="https://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16"><path d="M0 6.5v3c0 .276.224.5.5.5h15c.276 0 .5-.224.5-.5v-3c0-.276-.224-.5-.5-.5H.5c-.276 0-.5.224-.5.5z" fill="#444"/></svg>',
    facebook: '<svg xmlns="https://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16"><path d="M10.5 3C9.12 3 8 4.12 8 5.5V7H6v2h2v7h2V9h2.25l.5-2H10V5.5c0-.276.224-.5.5-.5H13V3h-2.5z" fill="#444"/></svg>',
    twitter: '<svg xmlns="https://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16"><path d="M16 3.038c-.59.26-1.22.438-1.885.517.678-.406 1.198-1.05 1.443-1.816-.634.375-1.337.648-2.085.796-.6-.638-1.452-1.037-2.396-1.037-1.813 0-3.283 1.47-3.283 3.28 0 .258.03.51.085.75C5.15 5.39 2.73 4.084 1.112 2.1.83 2.583.67 3.147.67 3.75c0 1.138.578 2.143 1.46 2.73-.54-.016-1.045-.164-1.488-.41v.04c0 1.59 1.132 2.918 2.633 3.22-.275.075-.565.115-.865.115-.212 0-.417-.02-.618-.06.418 1.305 1.63 2.254 3.066 2.28-1.123.88-2.54 1.406-4.077 1.406-.264 0-.525-.015-.782-.045 1.453.93 3.178 1.475 5.032 1.475 6.038 0 9.34-5.002 9.34-9.34 0-.142-.003-.284-.01-.425.642-.463 1.198-1.04 1.638-1.7z" fill="#444"/></svg>',
    google: '<svg xmlns="https://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16"><path d="M6.828 12.784c0 1.173-.725 2.1-2.777 2.175-1.202-.685-2.21-1.672-2.92-2.858.37-.914 1.527-1.61 2.85-1.596.375.004.725.064 1.043.167.874.607 1.578.987 1.755 1.68.033.14.05.283.05.43zM8 0C5.688 0 3.606.98 2.146 2.548c.577-.32 1.258-.51 1.983-.51h4.006l-.895.94H6.19c.74.425 1.136 1.3 1.136 2.266 0 .886-.49 1.6-1.184 2.142-.676.528-.805.75-.805 1.2 0 .382.808.954 1.18 1.232 1.294.97 1.556 1.58 1.556 2.795 0 1.23-1.077 2.455-2.904 2.87.88.334 1.834.517 2.83.517 4.42 0 8-3.582 8-8s-3.58-8-8-8zm4 6v2h-1V6H9V5h2V3h1v2h2v1h-2zm-6.285-.696c.186 1.418-.435 2.33-1.515 2.3S2.094 6.58 1.907 5.16c-.186-1.42.538-2.504 1.618-2.472s2.003 1.196 2.19 2.614zM3.45 10.032c-1.166 0-2.157.403-2.856.998C.21 10.095 0 9.072 0 8c0-.887.145-1.74.41-2.537.116 1.554 1.21 2.753 3.016 2.753.133 0 .262-.007.39-.016-.125.238-.214.503-.214.78 0 .47.258.736.576 1.046-.24 0-.473.007-.727.007z" fill="#444"/></svg>',
    close: '<svg xmlns="https://www.w3.org/2000/svg" width="13" height="16" viewBox="0 0 13 16"><path d="M11.59 11.804q0 .357-.25.607l-1.215 1.215q-.25.25-.607.25t-.607-.25L6.287 11 3.66 13.625q-.25.25-.606.25t-.607-.25L1.233 12.41q-.25-.25-.25-.606t.25-.607l2.625-2.625-2.625-2.625q-.25-.25-.25-.607t.25-.607L2.447 3.52q.25-.25.607-.25t.607.25l2.626 2.624L8.91 3.52q.25-.25.608-.25t.607.25l1.214 1.213q.25.25.25.607t-.25.607L8.713 8.572l2.625 2.625q.25.25.25.607z" fill="#444"/></svg>',
    doubleArrowUp: '<svg xmlns="https://www.w3.org/2000/svg" width="10" height="16" viewBox="0 0 10 16"><path d="M9.598 11.714q0 .116-.09.205l-.445.445q-.09.09-.205.09t-.205-.09l-3.51-3.51-3.508 3.51q-.09.09-.205.09t-.205-.09L.78 11.92q-.09-.09-.09-.206t.09-.205l4.16-4.162q.09-.09.205-.09t.205.09l4.16 4.16q.09.09.09.206zm0-3.428q0 .116-.09.205l-.445.447q-.09.09-.205.09t-.205-.09l-3.51-3.51-3.508 3.51q-.09.09-.205.09t-.205-.09L.78 8.49q-.09-.088-.09-.204t.09-.205l4.16-4.16q.09-.09.205-.09t.205.09l4.16 4.16q.09.09.09.206z" fill="#444"/></svg>',
    angleLeft: '<svg xmlns="https://www.w3.org/2000/svg" width="6" height="16" viewBox="0 0 6 16"><path fill="#444" d="M5.598 4.857q0 .116-.09.205L2 8.572l3.51 3.508q.088.09.088.205t-.09.205l-.445.446q-.09.09-.205.09t-.205-.09l-4.16-4.16q-.09-.09-.09-.206t.09-.205l4.16-4.16q.09-.09.205-.09t.205.09l.446.445q.088.09.088.205z"/></svg>',
    angleRight: '<svg xmlns="https://www.w3.org/2000/svg" width="6" height="16" viewBox="0 0 6 16"><path fill="#444" d="M5.313 8.57q0 .117-.09.206l-4.16 4.16q-.09.09-.205.09t-.205-.09l-.446-.445q-.09-.088-.09-.204t.09-.205l3.51-3.508-3.51-3.51q-.09-.088-.09-.204t.09-.205l.446-.446q.09-.09.205-.09t.205.09l4.16 4.16q.09.09.09.206z"/></svg>'
});

DATA_SOCIAL_BUTTONS = Object.freeze({
    facebook: {
        url: "https://www.facebook.com/sharer/sharer.php?u=",
        class: "rrssb-facebook",
        icon: ICONS.facebook,
        text: "Facebook"
    },
    twitter: {
        url : "https://twitter.com/home?status=",
        class: "rrssb-twitter",
        icon: ICONS.twitter,
        text: "Twitter"
    },
    googleplus: {
        url:  "https://plus.google.com/share?url=",
        class: "rrssb-googleplus",
        icon: ICONS.google,
        text: "Google+"
    }
});

OVERLAY_INITIAL_ATTRIBUTES = Object.freeze({
    'data-action': 'quit',
    'data-section': 'view',
    'class': 'overlay-view'
});

OVERLAY_INITIAL_STYLE = Object.freeze({
    opacity: 1,
    transition: 'all 350ms ease-out',
    overflow: 'hidden',
   
});

BODY_OVERLAY_CREATED_STYLE = Object.freeze({
   marginRight: "1rem",
   overflow: 'hidden',
});

BODY_OVERLAY_DESTROYED_STYLE = Object.freeze({
    marginRight: "0rem",
    overflow: 'initial'
});





/*--------------------
Utilities
--------------------*/


onTransitionEnd = function ($target, callback) {
    $target.one('transitionend webkitTransitionEnd oTransitionEnd MSTransitionEnd', callback);
};

/*--------------------
Overlay
--------------------*/

destroyOverlay = function (e, view) {
    var $target = $(e.target),
        $view = $(view),
        $zoomContainer = $('.zoomContainer'),
        action = $target.attr('data-action');

    if (action === "quit") {
        $view.css('opacity', '0');
        onTransitionEnd($view, function () {
            DOM.$root.css('overflow', 'initial');
            DOM.$body.css(BODY_OVERLAY_DESTROYED_STYLE); 
            $zoomContainer.remove();
            $view.remove();
        });
    }
};

createOverlay = function () {
    var $overlay = $('<div>');
    $overlay
        .attr(OVERLAY_INITIAL_ATTRIBUTES)
        .css(OVERLAY_INITIAL_STYLE)
        .html('<i class="spinner-loader icon">' + ICONS.loader +'</i>');
    $overlay.click(function (e) {
        destroyOverlay(e, '[data-section="view"]');
    });
    return $overlay;
};

appendOverlay = function ($overlay) {
   DOM.$root.css('overflow', 'hidden');
   DOM.$body.css(BODY_OVERLAY_CREATED_STYLE);
   DOM.$body.append($overlay);
};

/*--------------------
DROP-DOWNS
--------------------*/

slideContent = function (trigger, content) {
    $(trigger).click(function () {
        var $this = $(this);
        var $content = $this.next(content);

        $this.toggleClass("active");
        $content.slideToggle();
        return false;
    });
};

crateShareButtonMarkup = function (type, url, data) {
    'use strict';
    var markUp = "";
    [].forEach.call(Object.keys(data), function (key, ignored) {
        if (key === type) {
            markUp += "<li class='" + data[key].class + "'>";
            markUp += "<a href='javascript:;' onclick=\"popupWindow('" + data[key].url + encodeURIComponent(url) + ", " + data[key].text + ", 600 , 480');\">";
            markUp += "<i class='rrssb-icon'>" + data[key].icon + "</i>";
            markUp += "<span class='rrssb-text'>" +  data[key].text + "</span>";
        }
    });
    markUp += "</a>";
    markUp += "</li>";
    return markUp;
};

var exists = function (x) {
    "use strict";
    return typeof x !== "undefined" || x !== null;
};

var orderQuantityAction = function (e, input) {
    var $target = $(e.target),
        value = ~~input.val(),
        action = ($target.attr('data-action-count') === 'inc') ? value++ : value--;
    input.val(Math.max(0, value));
    return false;
};

clearInput = function (trigger, input) {
    var $trigger = $(trigger),
        $input = $(input);

    ($input.val() !== "") ? $trigger.show() : $trigger.hide();

    $trigger.click(function (e) {
        if ($input.val() !== "") {
            $input.val("").focus();
            $trigger.hide();
        }
        return false;
    });
};

var hasParent = function ($target, parentClassName) {
    return $target.parents(parentClassName).length;
}; 

var setUpScrollbar = function ($el) {
   if ($el.length) { 
      $el.jScrollPane({autoReinitialise: true}); 
      $el.on('jsp-initialised', function (event, isScrollable) { 
         if (isScrollable) {
            var $this = $(this).addClass('hint bottom'); 
         }

      });
      $el.on('jsp-scroll-y', function (event, scrollPositionY, isAtTop, isAtBottom) {
         var $this = $(this);
         if (isAtTop) {
            if ($this.hasClass('top')){ 
               $this.removeClass('top');
            }
            $this.addClass('bottom'); 
         } 
         else if (isAtBottom) {
            if ($this.hasClass('bottom')){ 
               $this.removeClass('bottom');
            }
            $this.addClass('top'); 
         } 
         else { 
            $this.removeClass('top').removeClass('bottom');
            $this.addClass('top').addClass('bottom'); 
         } 
      }); 
   }
};

var initQuickViewElevate = function (id, galleryId) {
    var $gallery = $("#" + id);
    $gallery.elevateZoom({
         gallery: galleryId
        , cursor: 'crosshair'
        , responsive: true
        , zoomType: 'window'
        , zoomWindowOffetx: 16
        , lensSize: 100
        , galleryActiveClass: 'elevate-active'
        , imageCrossfade: true
        , zoomWindowFadeIn: 300
        , zoomWindowFadeOut: 300
        , lensFadeIn: 300
        , lensFadeOut: 300
        , borderSize: 1
        , loadingIcon: false});

    $gallery.on("click", function(e) {
        e.preventDefault();
        e.stopPropagation();
        $.fancybox($(id).data('elevateZoom').getGalleryList());
    });
};

function quickView(type, id, target) {
    'use strict';
    var request = window.Requests.QUICK_VIEW;
    id = ~~id;
     if (exists(type)) {
        if (type === 'product') {

                    $.fancybox({
                        content: createOverlay(),
                        fitToView: true,
                        width: '98%',
                        height: '98%',
                        autoSize: false,
                        type:'html'
                    });

            $.getJSON(request,
            {
                product_id: id,
                resp:'html'
            })
            .then(function(data){
                let tpl;
                let images = Object.keys(data.images).map(function (key) { return data.images[key]; });

                if (!data.error) {
                    if (typeof data.html != undefined && data.html) {
                        tpl = '<div class="row content-view">'+ data.html +'</div>';
                    } else {
                        tpl = '<div class="row content-view">';

                        tpl += '<div class="large-5 medium-5 small-12 columns">';
                        tpl += '<div id="qw_images">';
                        tpl += '<div id="product-popup">';
                        tpl += '<div class="product-gallery" id="productImages">';
                        if (images.length > 0) {
                            tpl += '<img id="quickViewMainImage" class="view" data-zoom-image="' + images[0].popup  + '" src="' + images[0].preview + '" alt="' + data.productInfo.name + '"  />';
                            tpl += '<div id="quickViewMainGallery" class="quickview-main-gallery">';
                                $.each(images, function(i, image) {
                                    tpl += '<div data-item="thumb">';
                                    tpl += '<a class="thumb" href="javascript:;" data-image="' + image.preview + '" data-zoom-image="' + image.popup + '">';
                                    tpl += '<img id="' + i + '" src="' + image.thumb + '" />';
                                    tpl += '</a>';
                                    tpl += '</div>';
                                });
                            tpl += '</div>';
                        } else {
                            tpl += '<img id="quickViewMainImage" class="view" style="width: 100%; height:auto; display:block;" src="../web/assets/images/no_image.jpg" alt="' + data.productInfo.name + '"  />';
                        }
                        tpl += '</div>';
                        tpl += '</div>';
                        tpl += '</div>';
                        tpl += '</div>';
                        /** Images **/

                        tpl += '<div class="single-product-info large-7 medium-7 small-12 columns">';
                        tpl += '<header class="page-heading"><h1 id="productName" class="name" id="productName">' + data.productInfo.name + '</h1></header>';
                        tpl += '<div class="share-buttons">';
                        tpl += '<ul class="rrssb-buttons clearfix">';
                        tpl += crateShareButtonMarkup('facebook', data.href, DATA_SOCIAL_BUTTONS);
                        tpl += crateShareButtonMarkup('twitter', data.href, DATA_SOCIAL_BUTTONS);
                        tpl += crateShareButtonMarkup('googleplus', data.href, DATA_SOCIAL_BUTTONS);
                        tpl += '</ul>';
                        tpl += '</div>';
                        if (data.average) {
                            tpl += '<div itemprop="aggregateRating"itemscope itemtype="https://schema.org/AggregateRating" class="average" id="productAverage">';
                            tpl += '<span class="rating-text">Rating</span>';
                            tpl += '<img class="rating-stars" src="assets/images/stars_' + data.average + '.png"/>';
                            tpl += '</div>';
                        }
                        tpl += '<div itemprop="model" class="model" id="productModel">' + window.I18n.Product.model + '<span>' + data.productInfo.model + '</span></div>';

                        if (data.productInfo.meta_description.length > 1) {
                            tpl += '<div itemprop="description" class="overview nt-editable" id="productDescription"><p>' + data.productInfo.meta_description + '</p></div>';
                        }
                        if (window.Constants.IS_STORE) {
                            tpl += '<div href="https://schema.org/InStock" class="availability" id="productAvailability">';
                            tpl += '<span>Disponibilidad:<small>'+ data.stock +'</small></span>';
                            tpl += '</div>';

                            tpl += '<div class="offers">';
                            if (data.display_price) {
                                if (!data.special) {
                                    tpl += '<span class="price nt-editable" id="productPrice">'+ data.price +'</span>';
                                } else {
                                    tpl += '<span class="new_price nt-editable" id="productNewPrice">'+ data.special +'</span>';
                                    tpl += '<span class="old_price nt-editable" id="productOldPrice">'+ data.price +'</span>';
                                }
                            }
                            tpl +="</div>";
                            tpl += '<form action="/carrito?product_id='+ data.product_id +'" method="post" enctype="multipart/form-data" id="productForm">';

                            /*if (data.discounts) {
                                tpl += '<div class="property discount nt-editable" id="productDiscount">';
                                tpl += '<span><small>Descuento</small></span>';
                                tpl += '<table>';
                                tpl += '<tr>';
                                tpl += '<th>Cant. Min.</th>';
                                tpl += '<th>Precio</th>';
                                tpl += '</tr>';
                                $.each(data.discounts, function(i,item) {
                                    tpl += '<tr>';
                                    tpl += '<td>'+ item.quantity +'</td>';
                                    tpl += '<td>'+ item.price +'</td>';
                                    tpl += '</tr>';
                                });
                                tpl += '</table>';
                                tpl += '</div>';
                            }*/
                            tpl += '<div class="quantity" id="productQty">'; tpl += '<input type="text" id="quantity" name="quantity" value="'+ data.minimum + '" />';
                            if (data.minimum > 1) {
                                tpl += '<small>Compra M&iacute;nima '+ data.minimum +'</small>';
                            }
                            tpl += '<a class="arrow-up"><i data-action-count="inc" class="icon">' + ICONS.plus + '</i></a>';
                            tpl += '<a class="arrow-down"><i data-action-count="dec" class="icon">' + ICONS.minus + '</i></a>';
                            tpl += '<input type="hidden" name="product_id" value="'+ data.product_id +'" />';
                            tpl += '</div>';

                            tpl += '<div class="actions">';
                            tpl += '<div class="action-button action-add"><a onclick="$(\'#productForm\').submit();" id="add_to_cart">';
                            tpl += data.button_add_to_cart;
                            tpl += '</a>';
                            tpl += '</div>';
                            tpl += '<div class="action-button action-see"><a class="action-see" href="'+ data.href +'">';
                            tpl +=  data.button_see_product;
                            tpl += '</a>';
                            tpl += '</div>';
                            tpl += '<input type="hidden" name="product_id" value="'+ data.productInfo.product_id +'" />';
                            tpl += "</div>";
                            tpl += '</form>';

                            if (data.google_client_id) {
                                tpl += '<a class="socialSmallButton googleButton" href="index.php?r=api/google&redirect=promoteproduct&product_id='+ data.productInfo.product_id +'">Promocionar en Google</a>';
                            }

                            if (data.live_client_id) {
                                tpl += '<a class="socialSmallButton liveButton" href="index.php?r=api/live&redirect=promoteproduct&product_id='+ data.productInfo.product_id +'">Promocionar en Hotmail</a>';
                            }

                            if (data.facebook_app_id) {
                                tpl += '<a class="socialSmallButton liveButton" href="index.php?r=api/facebook&redirect=promoteproduct&product_id='+ data.productInfo.product_id +'">Promocionar en Facebook</a>';
                            }

                            if (data.twitter_oauth_token_secret) {
                                tpl += '<a class="socialSmallButton liveButton" href="index.php?r=api/twitter&redirect=promoteproduct&product_id='+ data.productInfo.product_id +'">Promocionar en Twitter</a>';
                            }
                        }
                        tpl += '</div></div>';
                        tpl += '<a href="javascript:;" class="action-quit" data-action="quit"><i class="icon">' + ICONS.close + '</i></a>';
                    }
                    $('[data-section="view"]').html(tpl);
                }
            });
        }
    }
    return false;
}

function addToCart(url, btn) {
    'use strict';
    var quitButton = '<a href="javascript:;" class="action-quit" data-action="quit">' +
                        '<i class="icon">' + ICONS.close + '</i>' +
                    '</a>',
        $overlay = createOverlay();
    $.ajax({
        url: url,
        type: 'post',
        data: $(btn).closest('form').serialize(),
        success: function (response) {
            var data = $.parseJSON(response);

            if (exists(data.html)) {
                data.html += '<div class="page-heading">' + window.I18n.Product.successfulAddToCart + '</div>';
                data.html += '<div class="btn btn--primary">' +
                                '<a href="'+ data.urlToCart +'">' + window.I18n.Common.goToCart + '</a>' +
                             '</div>';
            }
            $overlay.html('<div class="row content-view cart-view-data">' + data.html + '</div>');
            $overlay.append(quitButton);
        },
        beforeSend: function () {
            appendOverlay($overlay);
        }
    });
}


function getUrlVars() {
    var vars = {};
    var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m, key, value) {
        vars[key] = value;
    });
    return vars; 
}

function createUrl(route, params) {
    var url = window.nt.http_home + 'index.php?r=' + route;
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





/*
 * Initialization Scripts
 */



/*FASTCLIKC*/
!function e(t,n,r){function i(s,a){if(!n[s]){if(!t[s]){var c="function"==typeof require&&require;if(!a&&c)return c(s,!0);if(o)return o(s,!0);var u=new Error("Cannot find module '"+s+"'");throw u.code="MODULE_NOT_FOUND",u}var l=n[s]={exports:{}};t[s][0].call(l.exports,function(e){var n=t[s][1][e];return i(n?n:e)},l,l.exports,e,t,n,r)}return n[s].exports}for(var o="function"==typeof require&&require,s=0;s<r.length;s++)i(r[s]);return i}({1:[function(e,t){!function(){"use strict";function e(t,n){function i(e,t){return function(){return e.apply(t,arguments)}}var o;if(n=n||{},this.trackingClick=!1,this.trackingClickStart=0,this.targetElement=null,this.touchStartX=0,this.touchStartY=0,this.lastTouchIdentifier=0,this.touchBoundary=n.touchBoundary||10,this.layer=t,this.tapDelay=n.tapDelay||200,this.tapTimeout=n.tapTimeout||700,!e.notNeeded(t)){for(var s=["onMouse","onClick","onTouchStart","onTouchMove","onTouchEnd","onTouchCancel"],a=this,c=0,u=s.length;u>c;c++)a[s[c]]=i(a[s[c]],a);r&&(t.addEventListener("mouseover",this.onMouse,!0),t.addEventListener("mousedown",this.onMouse,!0),t.addEventListener("mouseup",this.onMouse,!0)),t.addEventListener("click",this.onClick,!0),t.addEventListener("touchstart",this.onTouchStart,!1),t.addEventListener("touchmove",this.onTouchMove,!1),t.addEventListener("touchend",this.onTouchEnd,!1),t.addEventListener("touchcancel",this.onTouchCancel,!1),Event.prototype.stopImmediatePropagation||(t.removeEventListener=function(e,n,r){var i=Node.prototype.removeEventListener;"click"===e?i.call(t,e,n.hijacked||n,r):i.call(t,e,n,r)},t.addEventListener=function(e,n,r){var i=Node.prototype.addEventListener;"click"===e?i.call(t,e,n.hijacked||(n.hijacked=function(e){e.propagationStopped||n(e)}),r):i.call(t,e,n,r)}),"function"==typeof t.onclick&&(o=t.onclick,t.addEventListener("click",function(e){o(e)},!1),t.onclick=null)}}var n=navigator.userAgent.indexOf("Windows Phone")>=0,r=navigator.userAgent.indexOf("Android")>0&&!n,i=/iP(ad|hone|od)/.test(navigator.userAgent)&&!n,o=i&&/OS 4_\d(_\d)?/.test(navigator.userAgent),s=i&&/OS [6-7]_\d/.test(navigator.userAgent),a=navigator.userAgent.indexOf("BB10")>0;e.prototype.needsClick=function(e){switch(e.nodeName.toLowerCase()){case"button":case"select":case"textarea":if(e.disabled)return!0;break;case"input":if(i&&"file"===e.type||e.disabled)return!0;break;case"label":case"iframe":case"video":return!0}return/\bneedsclick\b/.test(e.className)},e.prototype.needsFocus=function(e){switch(e.nodeName.toLowerCase()){case"textarea":return!0;case"select":return!r;case"input":switch(e.type){case"button":case"checkbox":case"file":case"image":case"radio":case"submit":return!1}return!e.disabled&&!e.readOnly;default:return/\bneedsfocus\b/.test(e.className)}},e.prototype.sendClick=function(e,t){var n,r;document.activeElement&&document.activeElement!==e&&document.activeElement.blur(),r=t.changedTouches[0],n=document.createEvent("MouseEvents"),n.initMouseEvent(this.determineEventType(e),!0,!0,window,1,r.screenX,r.screenY,r.clientX,r.clientY,!1,!1,!1,!1,0,null),n.forwardedTouchEvent=!0,e.dispatchEvent(n)},e.prototype.determineEventType=function(e){return r&&"select"===e.tagName.toLowerCase()?"mousedown":"click"},e.prototype.focus=function(e){var t;i&&e.setSelectionRange&&0!==e.type.indexOf("date")&&"time"!==e.type&&"month"!==e.type?(t=e.value.length,e.setSelectionRange(t,t)):e.focus()},e.prototype.updateScrollParent=function(e){var t,n;if(t=e.fastClickScrollParent,!t||!t.contains(e)){n=e;do{if(n.scrollHeight>n.offsetHeight){t=n,e.fastClickScrollParent=n;break}n=n.parentElement}while(n)}t&&(t.fastClickLastScrollTop=t.scrollTop)},e.prototype.getTargetElementFromEventTarget=function(e){return e.nodeType===Node.TEXT_NODE?e.parentNode:e},e.prototype.onTouchStart=function(e){var t,n,r;if(e.targetTouches.length>1)return!0;if(t=this.getTargetElementFromEventTarget(e.target),n=e.targetTouches[0],i){if(r=window.getSelection(),r.rangeCount&&!r.isCollapsed)return!0;if(!o){if(n.identifier&&n.identifier===this.lastTouchIdentifier)return e.preventDefault(),!1;this.lastTouchIdentifier=n.identifier,this.updateScrollParent(t)}}return this.trackingClick=!0,this.trackingClickStart=e.timeStamp,this.targetElement=t,this.touchStartX=n.pageX,this.touchStartY=n.pageY,e.timeStamp-this.lastClickTime<this.tapDelay&&e.preventDefault(),!0},e.prototype.touchHasMoved=function(e){var t=e.changedTouches[0],n=this.touchBoundary;return Math.abs(t.pageX-this.touchStartX)>n||Math.abs(t.pageY-this.touchStartY)>n?!0:!1},e.prototype.onTouchMove=function(e){return this.trackingClick?((this.targetElement!==this.getTargetElementFromEventTarget(e.target)||this.touchHasMoved(e))&&(this.trackingClick=!1,this.targetElement=null),!0):!0},e.prototype.findControl=function(e){return void 0!==e.control?e.control:e.htmlFor?document.getElementById(e.htmlFor):e.querySelector("button, input:not([type=hidden]), keygen, meter, output, progress, select, textarea")},e.prototype.onTouchEnd=function(e){var t,n,a,c,u,l=this.targetElement;if(!this.trackingClick)return!0;if(e.timeStamp-this.lastClickTime<this.tapDelay)return this.cancelNextClick=!0,!0;if(e.timeStamp-this.trackingClickStart>this.tapTimeout)return!0;if(this.cancelNextClick=!1,this.lastClickTime=e.timeStamp,n=this.trackingClickStart,this.trackingClick=!1,this.trackingClickStart=0,s&&(u=e.changedTouches[0],l=document.elementFromPoint(u.pageX-window.pageXOffset,u.pageY-window.pageYOffset)||l,l.fastClickScrollParent=this.targetElement.fastClickScrollParent),a=l.tagName.toLowerCase(),"label"===a){if(t=this.findControl(l)){if(this.focus(l),r)return!1;l=t}}else if(this.needsFocus(l))return e.timeStamp-n>100||i&&window.top!==window&&"input"===a?(this.targetElement=null,!1):(this.focus(l),this.sendClick(l,e),i&&"select"===a||(this.targetElement=null,e.preventDefault()),!1);return i&&!o&&(c=l.fastClickScrollParent,c&&c.fastClickLastScrollTop!==c.scrollTop)?!0:(this.needsClick(l)||(e.preventDefault(),this.sendClick(l,e)),!1)},e.prototype.onTouchCancel=function(){this.trackingClick=!1,this.targetElement=null},e.prototype.onMouse=function(e){return this.targetElement?e.forwardedTouchEvent?!0:e.cancelable&&(!this.needsClick(this.targetElement)||this.cancelNextClick)?(e.stopImmediatePropagation?e.stopImmediatePropagation():e.propagationStopped=!0,e.stopPropagation(),e.preventDefault(),!1):!0:!0},e.prototype.onClick=function(e){var t;return this.trackingClick?(this.targetElement=null,this.trackingClick=!1,!0):"submit"===e.target.type&&0===e.detail?!0:(t=this.onMouse(e),t||(this.targetElement=null),t)},e.prototype.destroy=function(){var e=this.layer;r&&(e.removeEventListener("mouseover",this.onMouse,!0),e.removeEventListener("mousedown",this.onMouse,!0),e.removeEventListener("mouseup",this.onMouse,!0)),e.removeEventListener("click",this.onClick,!0),e.removeEventListener("touchstart",this.onTouchStart,!1),e.removeEventListener("touchmove",this.onTouchMove,!1),e.removeEventListener("touchend",this.onTouchEnd,!1),e.removeEventListener("touchcancel",this.onTouchCancel,!1)},e.notNeeded=function(e){var t,n,i,o;if("undefined"==typeof window.ontouchstart)return!0;if(n=+(/Chrome\/([0-9]+)/.exec(navigator.userAgent)||[,0])[1]){if(!r)return!0;if(t=document.querySelector("meta[name=viewport]")){if(-1!==t.content.indexOf("user-scalable=no"))return!0;if(n>31&&document.documentElement.scrollWidth<=window.outerWidth)return!0}}if(a&&(i=navigator.userAgent.match(/Version\/([0-9]*)\.([0-9]*)/),i[1]>=10&&i[2]>=3&&(t=document.querySelector("meta[name=viewport]")))){if(-1!==t.content.indexOf("user-scalable=no"))return!0;if(document.documentElement.scrollWidth<=window.outerWidth)return!0}return"none"===e.style.msTouchAction||"manipulation"===e.style.touchAction?!0:(o=+(/Firefox\/([0-9]+)/.exec(navigator.userAgent)||[,0])[1],o>=27&&(t=document.querySelector("meta[name=viewport]"),t&&(-1!==t.content.indexOf("user-scalable=no")||document.documentElement.scrollWidth<=window.outerWidth))?!0:"none"===e.style.touchAction||"manipulation"===e.style.touchAction?!0:!1)},e.attach=function(t,n){return new e(t,n)},"function"==typeof define&&"object"==typeof define.amd&&define.amd?define(function(){return e}):"undefined"!=typeof t&&t.exports?(t.exports=e.attach,t.exports.FastClick=e):window.FastClick=e}()},{}],2:[function(e){window.Origami={fastclick:e("./bower_components/fastclick/lib/fastclick.js")}},{"./bower_components/fastclick/lib/fastclick.js":1}]},{},[2]);;(function() {function trigger(){document.dispatchEvent(new CustomEvent('o.load'))};document.addEventListener('load',trigger);if (document.readyState==='ready') trigger();}());(function() {function trigger(){document.dispatchEvent(new CustomEvent('o.DOMContentLoaded'))};document.addEventListener('DOMContentLoaded',trigger);if (document.readyState==='interactive') trigger();}());

;(function(a,b,c,d){var e=a(b);a.fn.lazyload=function(c){function i(){var b=0;f.each(function(){var c=a(this);if(h.skip_invisible&&!c.is(":visible"))return;if(!a.abovethetop(this,h)&&!a.leftofbegin(this,h))if(!a.belowthefold(this,h)&&!a.rightoffold(this,h))c.trigger("appear"),b=0;else if(++b>h.failure_limit)return!1})}var f=this,g,h={threshold:0,failure_limit:0,event:"scroll",effect:"show",container:b,data_attribute:"original",skip_invisible:!0,appear:null,load:null};return c&&(d!==c.failurelimit&&(c.failure_limit=c.failurelimit,delete c.failurelimit),d!==c.effectspeed&&(c.effect_speed=c.effectspeed,delete c.effectspeed),a.extend(h,c)),g=h.container===d||h.container===b?e:a(h.container),0===h.event.indexOf("scroll")&&g.bind(h.event,function(a){return i()}),this.each(function(){var b=this,c=a(b);b.loaded=!1,c.one("appear",function(){if(!this.loaded){if(h.appear){var d=f.length;h.appear.call(b,d,h)}a("<img />").bind("load",function(){c.hide().attr("src",c.data(h.data_attribute))[h.effect](h.effect_speed),b.loaded=!0;var d=a.grep(f,function(a){return!a.loaded});f=a(d);if(h.load){var e=f.length;h.load.call(b,e,h)}}).attr("src",c.data(h.data_attribute))}}),0!==h.event.indexOf("scroll")&&c.bind(h.event,function(a){b.loaded||c.trigger("appear")})}),e.bind("resize",function(a){i()}),/iphone|ipod|ipad.*os 5/gi.test(navigator.appVersion)&&e.bind("pageshow",function(b){b.originalEvent.persisted&&f.each(function(){a(this).trigger("appear")})}),a(b).load(function(){i()}),this},a.belowthefold=function(c,f){var g;return f.container===d||f.container===b?g=e.height()+e.scrollTop():g=a(f.container).offset().top+a(f.container).height(),g<=a(c).offset().top-f.threshold},a.rightoffold=function(c,f){var g;return f.container===d||f.container===b?g=e.width()+e.scrollLeft():g=a(f.container).offset().left+a(f.container).width(),g<=a(c).offset().left-f.threshold},a.abovethetop=function(c,f){var g;return f.container===d||f.container===b?g=e.scrollTop():g=a(f.container).offset().top,g>=a(c).offset().top+f.threshold+a(c).height()},a.leftofbegin=function(c,f){var g;return f.container===d||f.container===b?g=e.scrollLeft():g=a(f.container).offset().left,g>=a(c).offset().left+f.threshold+a(c).width()},a.inviewport=function(b,c){return!a.rightoffold(b,c)&&!a.leftofbegin(b,c)&&!a.belowthefold(b,c)&&!a.abovethetop(b,c)},a.extend(a.expr[":"],{"below-the-fold":function(b){return a.belowthefold(b,{threshold:0})},"above-the-top":function(b){return!a.belowthefold(b,{threshold:0})},"right-of-screen":function(b){return a.rightoffold(b,{threshold:0})},"left-of-screen":function(b){return!a.rightoffold(b,{threshold:0})},"in-viewport":function(b){return a.inviewport(b,{threshold:0})},"above-the-fold":function(b){return!a.belowthefold(b,{threshold:0})},"right-of-fold":function(b){return a.rightoffold(b,{threshold:0})},"left-of-fold":function(b){return!a.rightoffold(b,{threshold:0})}})})(jQuery,window,document);

;(function(d){var k=d.scrollTo=function(a,i,e){d(window).scrollTo(a,i,e)};k.defaults={axis:'xy',duration:parseFloat(d.fn.jquery)>=1.3?0:1};k.window=function(a){return d(window)._scrollable()};d.fn._scrollable=function(){return this.map(function(){var a=this,i=!a.nodeName||d.inArray(a.nodeName.toLowerCase(),['iframe','#document','html','body'])!=-1;if(!i)return a;var e=(a.contentWindow||a).document||a.ownerDocument||a;return navigator.userAgent.indexOf("Safari") > -1||e.compatMode=='BackCompat'?e.body:e.documentElement})};d.fn.scrollTo=function(n,j,b){if(typeof j=='object'){b=j;j=0}if(typeof b=='function')b={onAfter:b};if(n=='max')n=9e9;b=d.extend({},k.defaults,b);j=j||b.speed||b.duration;b.queue=b.queue&&b.axis.length>1;if(b.queue)j/=2;b.offset=p(b.offset);b.over=p(b.over);return this._scrollable().each(function(){var q=this,r=d(q),f=n,s,g={},u=r.is('html,body');switch(typeof f){case'number':case'string':if(/^([+-]=)?\d+(\.\d+)?(px|%)?$/.test(f)){f=p(f);break}f=d(f,this);case'object':if(f.is||f.style)s=(f=d(f)).offset()}d.each(b.axis.split(''),function(a,i){var e=i=='x'?'Left':'Top',h=e.toLowerCase(),c='scroll'+e,l=q[c],m=k.max(q,i);if(s){g[c]=s[h]+(u?0:l-r.offset()[h]);if(b.margin){g[c]-=parseInt(f.css('margin'+e))||0;g[c]-=parseInt(f.css('border'+e+'Width'))||0}g[c]+=b.offset[h]||0;if(b.over[h])g[c]+=f[i=='x'?'width':'height']()*b.over[h]}else{var o=f[h];g[c]=o.slice&&o.slice(-1)=='%'?parseFloat(o)/100*m:o}if(/^\d+$/.test(g[c]))g[c]=g[c]<=0?0:Math.min(g[c],m);if(!a&&b.queue){if(l!=g[c])t(b.onAfterFirst);delete g[c]}});t(b.onAfter);function t(a){r.animate(g,j,b.easing,a&&function(){a.call(this,n,b)})}}).end()};k.max=function(a,i){var e=i=='x'?'Width':'Height',h='scroll'+e;if(!d(a).is('html,body'))return a[h]-d(a)[e.toLowerCase()]();var c='client'+e,l=a.ownerDocument.documentElement,m=a.ownerDocument.body;return Math.max(l[h],m[h])-Math.min(l[c],m[c])};function p(a){return typeof a==='object'?a:{top:a,left:a}}})(jQuery);

/*!
  jQuery Cookie Plugin v1.3
  https://github.com/carhartl/jquery-cookie
 
  Copyright 2011, Klaus Hartl
  Dual licensed under the MIT or GPL Version 2 licenses.
  https://www.opensource.org/licenses/mit-license.php
  https://www.opensource.org/licenses/GPL-2.0
 */

 (function($,document,undefined){var pluses=/\+/g;function raw(s){return s;}function decoded(s){return decodeURIComponent(s.replace(pluses,' '));}var config=$.cookie=function(key,value,options){if(value!==undefined){options=$.extend({},config.defaults,options);if(value===null){options.expires=-1;}if(typeof options.expires==='number'){var days=options.expires,t=options.expires=new Date();t.setDate(t.getDate()+days);}value=config.json?JSON.stringify(value):String(value);return(document.cookie=[encodeURIComponent(key),'=',config.raw?value:encodeURIComponent(value),options.expires?'; expires='+options.expires.toUTCString():'',options.path?'; path='+options.path:'',options.domain?'; domain='+options.domain:'',options.secure?'; secure':''].join(''));}var decode=config.raw?raw:decoded;var cookies=document.cookie.split('; ');for(var i=0,l=cookies.length;i<l;i++){var parts=cookies[i].split('=');if(decode(parts.shift())===key){var cookie=decode(parts.join('='));return config.json?JSON.parse(cookie):cookie;}}return null;};config.defaults={};$.removeCookie=function(key,options){if($.cookie(key)!==null){$.cookie(key,null,options);return true;}return false;};})(jQuery,document);
 
 /*!
  jQuery Image Preloader Plugin
  https://net.tutsplus.com/tutorials/javascript-ajax/how-to-create-an-awesome-image-preloader/
 */
 $.fn.preloader=function(options){var defaults={delay:200,preload_parent:"a",check_timer:300,ondone:function(){},oneachload:function(image){},fadein:500};var options=$.extend(defaults,options),root=$(this),images=root.find("img").css({"visibility":"hidden",opacity:0}),timer,counter=0,i=0,checkFlag=[],delaySum=options.delay,init=function(){timer=setInterval(function(){if(counter>=checkFlag.length){clearInterval(timer);options.ondone();return;}for(i=0;i<images.length;i++){if(images[i].complete==true){if(checkFlag[i]==false){checkFlag[i]=true;options.oneachload(images[i]);counter++;delaySum=delaySum+options.delay;}$(images[i]).css("visibility","visible").delay(delaySum).animate({opacity:1},options.fadein,function(){$(this).parent().removeClass("preloader");});}}},options.check_timer)};images.each(function(){if($(this).parent(options.preload_parent).length==0)$(this).wrap("<a class='preloader' />");else $(this).parent().addClass("preloader");checkFlag[i++]=false;});images=$.makeArray(images);var icon=jQuery("<img />",{id:'loadingicon',src:'assets/images/data/preloader.gif',alt:'Cargando...'}).hide().appendTo("body");timer=setInterval(function(){if(icon[0].complete==true){clearInterval(timer);init();icon.remove();return;}},100);};

 /*
 * jQuery Cryptography Plug-in
 * version: 1.0.0 (24 Sep 2008)
 * copyright 2008 Scott Thompson https://www.itsyndicate.ca - scott@itsyndicate.ca
 * https://www.opensource.org/licenses/mit-license.php
 *
 * A set of functions to do some basic cryptography encoding/decoding
 * I compiled from some javascripts I found into a jQuery plug-in.
 * Thanks go out to the original authors.
 *
 * Also a big thanks to Wade W. Hedgren https://homepages.uc.edu/~hedgreww
 * for the 1.1.1 upgrade to conform correctly to RFC4648 Sec5 url save base64
 *
 * Changelog: 1.1.0
 * - rewrote plugin to use only one item in the namespace
 *
 * Changelog: 1.1.1
 * - added code to base64 to allow URL and Filename Safe Alphabet (RFC4648 Sec5) 
 *
 * --- Base64 Encoding and Decoding code was written by
 *
 * Base64 code from Tyler Akins -- https://rumkin.com
 * and is placed in the public domain
 *
 *
 * --- MD5 and SHA1 Functions based upon Paul Johnston's javascript libraries.
 * A JavaScript implementation of the RSA Data Security, Inc. MD5 Message
 * Digest Algorithm, as defined in RFC 1321.
 * Version 2.1 Copyright (C) Paul Johnston 1999 - 2002.
 * Other contributors: Greg Holt, Andrew Kepert, Ydnar, Lostinet
 * Distributed under the BSD License
 * See https://pajhome.org.uk/crypt/md5 for more info.
 *
 * xTea Encrypt and Decrypt
 * copyright 2000-2005 Chris Veness
 * https://www.movable-type.co.uk
 *
 *
 * Examples:
 *
        var md5 = $().crypt({method:"md5",source:$("#phrase").val()});
        var sha1 = $().crypt({method:"sha1",source:$("#phrase").val()});
        var b64 = $().crypt({method:"b64enc",source:$("#phrase").val()});
        var b64dec = $().crypt({method:"b64dec",source:b64});
        var xtea = $().crypt({method:"xteaenc",source:$("#phrase").val(),keyPass:$("#passPhrase").val()});
        var xteadec = $().crypt({method:"xteadec",source:xtea,keyPass:$("#passPhrase").val()});
        var xteab64 = $().crypt({method:"xteab64enc",source:$("#phrase").val(),keyPass:$("#passPhrase").val()});
        var xteab64dec = $().crypt({method:"xteab64dec",source:xteab64,keyPass:$("#passPhrase").val()});

    You can also pass source this way.
    var md5 = $("#idOfSource").crypt({method:"md5"});
 *
 */
;(function($){$.fn.crypt=function(options){var defaults={b64Str:"ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789",strKey:"9333cc23245f2c47546719fd37e2d6f412d862d5222652336ee97bdd03ac2095",method:"md5",source:"",chrsz:8,hexcase:0};if(typeof(options.urlsafe)=='undefined'){defaults.b64Str+='+/=';options.urlsafe=false;}else if(options.urlsafe){defaults.b64Str+='-_=';}else{defaults.b64Str+='+/=';}var opts=$.extend(defaults,options);if(!opts.source){var $this=$(this);if($this.html())opts.source=$this.html();else if($this.val())opts.source=$this.val();else{alert("Please provide source text");return false;};};if(opts.method=='md5'){return md5(opts);}else if(opts.method=='sha1'){return sha1(opts);}else if(opts.method=='b64enc'){return b64enc(opts);}else if(opts.method=='b64dec'){return b64dec(opts);}else if(opts.method=='xteaenc'){return xteaenc(opts);}else if(opts.method=='xteadec'){return xteadec(opts);}else if(opts.method=='xteab64enc'){var tmpenc=xteaenc(opts);opts.method="b64enc";opts.source=tmpenc;return b64enc(opts);}else if(opts.method=='xteab64dec'){var tmpdec=b64dec(opts);opts.method="xteadec";opts.source=tmpdec;return xteadec(opts);}function b64enc(params){var output="";var chr1,chr2,chr3;var enc1,enc2,enc3,enc4;var i=0;do{chr1=params.source.charCodeAt(i++);chr2=params.source.charCodeAt(i++);chr3=params.source.charCodeAt(i++);enc1=chr1>>2;enc2=((chr1&3)<<4)|(chr2>>4);enc3=((chr2&15)<<2)|(chr3>>6);enc4=chr3&63;if(isNaN(chr2)){enc3=enc4=64;}else if(isNaN(chr3)){enc4=64;};output+=params.b64Str.charAt(enc1)
+params.b64Str.charAt(enc2)
+params.b64Str.charAt(enc3)
+params.b64Str.charAt(enc4);}while(i<params.source.length);return output;};function b64dec(params){var output="";var chr1,chr2,chr3;var enc1,enc2,enc3,enc4;var i=0;var re=new RegExp('[^A-Za-z0-9'+params.b64Str.substr(-3)+']','g');params.source=params.source.replace(re,"");do{enc1=params.b64Str.indexOf(params.source.charAt(i++));enc2=params.b64Str.indexOf(params.source.charAt(i++));enc3=params.b64Str.indexOf(params.source.charAt(i++));enc4=params.b64Str.indexOf(params.source.charAt(i++));chr1=(enc1<<2)|(enc2>>4);chr2=((enc2&15)<<4)|(enc3>>2);chr3=((enc3&3)<<6)|enc4;output=output+String.fromCharCode(chr1);if(enc3!=64){output=output+String.fromCharCode(chr2);}if(enc4!=64){output=output+String.fromCharCode(chr3);}}while(i<params.source.length);return output;};function md5(params){return binl2hex(core_md5(str2binl(params.source),params.source.length*params.chrsz));function binl2hex(binarray)
{var hex_tab=params.hexcase?"0123456789ABCDEF":"0123456789abcdef";var str="";for(var i=0;i<binarray.length*4;i++)
{str+=hex_tab.charAt((binarray[i>>2]>>((i%4)*8+4))&0xF)+hex_tab.charAt((binarray[i>>2]>>((i%4)*8))&0xF);};return str;};function core_hmac_md5(key,data)
{var bkey=str2binl(key);if(bkey.length>16)bkey=core_md5(bkey,key.length*params.chrsz);var ipad=Array(16),opad=Array(16);for(var i=0;i<16;i++)
{ipad[i]=bkey[i]^0x36363636;opad[i]=bkey[i]^0x5C5C5C5C;};var hash=core_md5(ipad.concat(str2binl(data)),512+data.length*params.chrsz);return core_md5(opad.concat(hash),512+128);};function str2binl(str)
{var bin=Array();var mask=(1<<params.chrsz)-1;for(var i=0;i<str.length*params.chrsz;i+=params.chrsz)bin[i>>5]|=(str.charCodeAt(i/params.chrsz)&mask)<<(i%32);return bin;}function bit_rol(num,cnt)
{return(num<<cnt)|(num>>>(32-cnt));}function md5_cmn(q,a,b,x,s,t)
{return safe_add(bit_rol(safe_add(safe_add(a,q),safe_add(x,t)),s),b);}function md5_ff(a,b,c,d,x,s,t)
{return md5_cmn((b&c)|((~b)&d),a,b,x,s,t);}function md5_gg(a,b,c,d,x,s,t)
{return md5_cmn((b&d)|(c&(~d)),a,b,x,s,t);}function md5_hh(a,b,c,d,x,s,t)
{return md5_cmn(b^c^d,a,b,x,s,t);}function md5_ii(a,b,c,d,x,s,t)
{return md5_cmn(c^(b|(~d)),a,b,x,s,t);}function core_md5(x,len)
{x[len>>5]|=0x80<<((len)%32);x[(((len+64)>>>9)<<4)+14]=len;var a=1732584193;var b=-271733879;var c=-1732584194;var d=271733878;for(var i=0;i<x.length;i+=16)
{var olda=a;var oldb=b;var oldc=c;var oldd=d;a=md5_ff(a,b,c,d,x[i+0],7,-680876936);d=md5_ff(d,a,b,c,x[i+1],12,-389564586);c=md5_ff(c,d,a,b,x[i+2],17,606105819);b=md5_ff(b,c,d,a,x[i+3],22,-1044525330);a=md5_ff(a,b,c,d,x[i+4],7,-176418897);d=md5_ff(d,a,b,c,x[i+5],12,1200080426);c=md5_ff(c,d,a,b,x[i+6],17,-1473231341);b=md5_ff(b,c,d,a,x[i+7],22,-45705983);a=md5_ff(a,b,c,d,x[i+8],7,1770035416);d=md5_ff(d,a,b,c,x[i+9],12,-1958414417);c=md5_ff(c,d,a,b,x[i+10],17,-42063);b=md5_ff(b,c,d,a,x[i+11],22,-1990404162);a=md5_ff(a,b,c,d,x[i+12],7,1804603682);d=md5_ff(d,a,b,c,x[i+13],12,-40341101);c=md5_ff(c,d,a,b,x[i+14],17,-1502002290);b=md5_ff(b,c,d,a,x[i+15],22,1236535329);a=md5_gg(a,b,c,d,x[i+1],5,-165796510);d=md5_gg(d,a,b,c,x[i+6],9,-1069501632);c=md5_gg(c,d,a,b,x[i+11],14,643717713);b=md5_gg(b,c,d,a,x[i+0],20,-373897302);a=md5_gg(a,b,c,d,x[i+5],5,-701558691);d=md5_gg(d,a,b,c,x[i+10],9,38016083);c=md5_gg(c,d,a,b,x[i+15],14,-660478335);b=md5_gg(b,c,d,a,x[i+4],20,-405537848);a=md5_gg(a,b,c,d,x[i+9],5,568446438);d=md5_gg(d,a,b,c,x[i+14],9,-1019803690);c=md5_gg(c,d,a,b,x[i+3],14,-187363961);b=md5_gg(b,c,d,a,x[i+8],20,1163531501);a=md5_gg(a,b,c,d,x[i+13],5,-1444681467);d=md5_gg(d,a,b,c,x[i+2],9,-51403784);c=md5_gg(c,d,a,b,x[i+7],14,1735328473);b=md5_gg(b,c,d,a,x[i+12],20,-1926607734);a=md5_hh(a,b,c,d,x[i+5],4,-378558);d=md5_hh(d,a,b,c,x[i+8],11,-2022574463);c=md5_hh(c,d,a,b,x[i+11],16,1839030562);b=md5_hh(b,c,d,a,x[i+14],23,-35309556);a=md5_hh(a,b,c,d,x[i+1],4,-1530992060);d=md5_hh(d,a,b,c,x[i+4],11,1272893353);c=md5_hh(c,d,a,b,x[i+7],16,-155497632);b=md5_hh(b,c,d,a,x[i+10],23,-1094730640);a=md5_hh(a,b,c,d,x[i+13],4,681279174);d=md5_hh(d,a,b,c,x[i+0],11,-358537222);c=md5_hh(c,d,a,b,x[i+3],16,-722521979);b=md5_hh(b,c,d,a,x[i+6],23,76029189);a=md5_hh(a,b,c,d,x[i+9],4,-640364487);d=md5_hh(d,a,b,c,x[i+12],11,-421815835);c=md5_hh(c,d,a,b,x[i+15],16,530742520);b=md5_hh(b,c,d,a,x[i+2],23,-995338651);a=md5_ii(a,b,c,d,x[i+0],6,-198630844);d=md5_ii(d,a,b,c,x[i+7],10,1126891415);c=md5_ii(c,d,a,b,x[i+14],15,-1416354905);b=md5_ii(b,c,d,a,x[i+5],21,-57434055);a=md5_ii(a,b,c,d,x[i+12],6,1700485571);d=md5_ii(d,a,b,c,x[i+3],10,-1894986606);c=md5_ii(c,d,a,b,x[i+10],15,-1051523);b=md5_ii(b,c,d,a,x[i+1],21,-2054922799);a=md5_ii(a,b,c,d,x[i+8],6,1873313359);d=md5_ii(d,a,b,c,x[i+15],10,-30611744);c=md5_ii(c,d,a,b,x[i+6],15,-1560198380);b=md5_ii(b,c,d,a,x[i+13],21,1309151649);a=md5_ii(a,b,c,d,x[i+4],6,-145523070);d=md5_ii(d,a,b,c,x[i+11],10,-1120210379);c=md5_ii(c,d,a,b,x[i+2],15,718787259);b=md5_ii(b,c,d,a,x[i+9],21,-343485551);a=safe_add(a,olda);b=safe_add(b,oldb);c=safe_add(c,oldc);d=safe_add(d,oldd);};return Array(a,b,c,d);};};function safe_add(x,y)
{var lsw=(x&0xFFFF)+(y&0xFFFF);var msw=(x>>16)+(y>>16)+(lsw>>16);return(msw<<16)|(lsw&0xFFFF);};function sha1(params){return binb2hex(core_sha1(str2binb(params.source),params.source.length*params.chrsz));function core_sha1(x,len)
{x[len>>5]|=0x80<<(24-len%32);x[((len+64>>9)<<4)+15]=len;var w=Array(80);var a=1732584193;var b=-271733879;var c=-1732584194;var d=271733878;var e=-1009589776;for(var i=0;i<x.length;i+=16)
{var olda=a;var oldb=b;var oldc=c;var oldd=d;var olde=e;for(var j=0;j<80;j++)
{if(j<16)w[j]=x[i+j];else w[j]=rol(w[j-3]^w[j-8]^w[j-14]^w[j-16],1);var t=safe_add(safe_add(rol(a,5),sha1_ft(j,b,c,d)),safe_add(safe_add(e,w[j]),sha1_kt(j)));e=d;d=c;c=rol(b,30);b=a;a=t;}a=safe_add(a,olda);b=safe_add(b,oldb);c=safe_add(c,oldc);d=safe_add(d,oldd);e=safe_add(e,olde);}return Array(a,b,c,d,e);}function rol(num,cnt)
{return(num<<cnt)|(num>>>(32-cnt));}function sha1_kt(t)
{return(t<20)?1518500249:(t<40)?1859775393:(t<60)?-1894007588:-899497514;}function sha1_ft(t,b,c,d)
{if(t<20)return(b&c)|((~b)&d);if(t<40)return b^c^d;if(t<60)return(b&c)|(b&d)|(c&d);return b^c^d;}function binb2hex(binarray)
{var hex_tab=params.hexcase?"0123456789ABCDEF":"0123456789abcdef";var str="";for(var i=0;i<binarray.length*4;i++)
{str+=hex_tab.charAt((binarray[i>>2]>>((3-i%4)*8+4))&0xF)+hex_tab.charAt((binarray[i>>2]>>((3-i%4)*8))&0xF);}return str;}function str2binb(str)
{var bin=Array();var mask=(1<<params.chrsz)-1;for(var i=0;i<str.length*params.chrsz;i+=params.chrsz)bin[i>>5]|=(str.charCodeAt(i/params.chrsz)&mask)<<(32-params.chrsz-i%32);return bin;}};function xteaenc(params){var v=new Array(2),k=new Array(4),s="",i;params.source=escape(params.source);for(var i=0;i<4;i++)k[i]=Str4ToLong(params.strKey.slice(i*4,(i+1)*4));for(i=0;i<params.source.length;i+=8){v[0]=Str4ToLong(params.source.slice(i,i+4));v[1]=Str4ToLong(params.source.slice(i+4,i+8));code(v,k);s+=LongToStr4(v[0])+LongToStr4(v[1]);}return escCtrlCh(s);function code(v,k){var y=v[0],z=v[1];var delta=0x9E3779B9,limit=delta*32,sum=0;while(sum!=limit){y+=(z<<4^z>>>5)+z^sum+k[sum&3];sum+=delta;z+=(y<<4^y>>>5)+y^sum+k[sum>>>11&3];}v[0]=y;v[1]=z;}};function xteadec(params){var v=new Array(2),k=new Array(4),s="",i;for(var i=0;i<4;i++)k[i]=Str4ToLong(params.strKey.slice(i*4,(i+1)*4));ciphertext=unescCtrlCh(params.source);for(i=0;i<ciphertext.length;i+=8){v[0]=Str4ToLong(ciphertext.slice(i,i+4));v[1]=Str4ToLong(ciphertext.slice(i+4,i+8));decode(v,k);s+=LongToStr4(v[0])+LongToStr4(v[1]);}s=s.replace(/\0+$/,'');return unescape(s);function decode(v,k){var y=v[0],z=v[1];var delta=0x9E3779B9,sum=delta*32;while(sum!=0){z-=(y<<4^y>>>5)+y^sum+k[sum>>>11&3];sum-=delta;y-=(z<<4^z>>>5)+z^sum+k[sum&3];}v[0]=y;v[1]=z;}};function Str4ToLong(s){var v=0;for(var i=0;i<4;i++)v|=s.charCodeAt(i)<<i*8;return isNaN(v)?0:v;};function LongToStr4(v){var s=String.fromCharCode(v&0xFF,v>>8&0xFF,v>>16&0xFF,v>>24&0xFF);return s;};function escCtrlCh(str){return str.replace(/[\0\t\n\v\f\r\xa0'"!]/g,function(c){return'!'+c.charCodeAt(0)+'!';});};function unescCtrlCh(str){return str.replace(/!\d\d?\d?!/g,function(c){return String.fromCharCode(c.slice(1,-1));});};};})(jQuery);

 /*
 * ----------------------------- JSTORAGE -------------------------------------
 * Simple local storage wrapper to save data on the browser side, supporting
 * all major browsers - IE6+, Firefox2+, Safari4+, Chrome4+ and Opera 10.5+
 *
 * Copyright (c) 2010 - 2012 Andris Reinman, andris.reinman@gmail.com
 * Project homepage: www.jstorage.info
 *
 * Licensed under MIT-style license:
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 
 https://www.jstorage.info/
 */
(function(){function C(){var a="{}";if("userDataBehavior"==h){d.load("jStorage");try{a=d.getAttribute("jStorage")}catch(b){}try{r=d.getAttribute("jStorage_update")}catch(c){}g.jStorage=a}D();x();E()}function u(){var a;clearTimeout(F);F=setTimeout(function(){if("localStorage"==h||"globalStorage"==h)a=g.jStorage_update;else if("userDataBehavior"==h){d.load("jStorage");try{a=d.getAttribute("jStorage_update")}catch(b){}}if(a&&a!=r){r=a;var k=l.parse(l.stringify(c.__jstorage_meta.CRC32)),p;C();p=l.parse(l.stringify(c.__jstorage_meta.CRC32));
var e,y=[],f=[];for(e in k)k.hasOwnProperty(e)&&(p[e]?k[e]!=p[e]&&"2."==String(k[e]).substr(0,2)&&y.push(e):f.push(e));for(e in p)p.hasOwnProperty(e)&&(k[e]||y.push(e));s(y,"updated");s(f,"deleted")}},25)}function s(a,b){a=[].concat(a||[]);if("flushed"==b){a=[];for(var c in j)j.hasOwnProperty(c)&&a.push(c);b="deleted"}c=0;for(var p=a.length;c<p;c++){if(j[a[c]])for(var e=0,d=j[a[c]].length;e<d;e++)j[a[c]][e](a[c],b);if(j["*"]){e=0;for(d=j["*"].length;e<d;e++)j["*"][e](a[c],b)}}}function v(){var a=
(+new Date).toString();"localStorage"==h||"globalStorage"==h?g.jStorage_update=a:"userDataBehavior"==h&&(d.setAttribute("jStorage_update",a),d.save("jStorage"));u()}function D(){if(g.jStorage)try{c=l.parse(String(g.jStorage))}catch(a){g.jStorage="{}"}else g.jStorage="{}";z=g.jStorage?String(g.jStorage).length:0;c.__jstorage_meta||(c.__jstorage_meta={});c.__jstorage_meta.CRC32||(c.__jstorage_meta.CRC32={})}function w(){if(c.__jstorage_meta.PubSub){for(var a=+new Date-2E3,b=0,k=c.__jstorage_meta.PubSub.length;b<
k;b++)if(c.__jstorage_meta.PubSub[b][0]<=a){c.__jstorage_meta.PubSub.splice(b,c.__jstorage_meta.PubSub.length-b);break}c.__jstorage_meta.PubSub.length||delete c.__jstorage_meta.PubSub}try{g.jStorage=l.stringify(c),d&&(d.setAttribute("jStorage",g.jStorage),d.save("jStorage")),z=g.jStorage?String(g.jStorage).length:0}catch(p){}}function q(a){if(!a||"string"!=typeof a&&"number"!=typeof a)throw new TypeError("Key name must be string or numeric");if("__jstorage_meta"==a)throw new TypeError("Reserved key name");
return!0}function x(){var a,b,k,d,e=Infinity,g=!1,f=[];clearTimeout(G);if(c.__jstorage_meta&&"object"==typeof c.__jstorage_meta.TTL){a=+new Date;k=c.__jstorage_meta.TTL;d=c.__jstorage_meta.CRC32;for(b in k)k.hasOwnProperty(b)&&(k[b]<=a?(delete k[b],delete d[b],delete c[b],g=!0,f.push(b)):k[b]<e&&(e=k[b]));Infinity!=e&&(G=setTimeout(x,e-a));g&&(w(),v(),s(f,"deleted"))}}function E(){var a;if(c.__jstorage_meta.PubSub){var b,k=A;for(a=c.__jstorage_meta.PubSub.length-1;0<=a;a--)if(b=c.__jstorage_meta.PubSub[a],
b[0]>A){var k=b[0],d=b[1];b=b[2];if(t[d])for(var e=0,g=t[d].length;e<g;e++)t[d][e](d,l.parse(l.stringify(b)))}A=k}}var n=window.jQuery||window.$||(window.$={}),l={parse:window.JSON&&(window.JSON.parse||window.JSON.decode)||String.prototype.evalJSON&&function(a){return String(a).evalJSON()}||n.parseJSON||n.evalJSON,stringify:Object.toJSON||window.JSON&&(window.JSON.stringify||window.JSON.encode)||n.toJSON};if(!l.parse||!l.stringify)throw Error("No JSON support found, include //cdnjs.cloudflare.com/ajax/libs/json2/20110223/json2.js to page");
var c={__jstorage_meta:{CRC32:{}}},g={jStorage:"{}"},d=null,z=0,h=!1,j={},F=!1,r=0,t={},A=+new Date,G,B={isXML:function(a){return(a=(a?a.ownerDocument||a:0).documentElement)?"HTML"!==a.nodeName:!1},encode:function(a){if(!this.isXML(a))return!1;try{return(new XMLSerializer).serializeToString(a)}catch(b){try{return a.xml}catch(c){}}return!1},decode:function(a){var b="DOMParser"in window&&(new DOMParser).parseFromString||window.ActiveXObject&&function(a){var b=new ActiveXObject("Microsoft.XMLDOM");b.async=
"false";b.loadXML(a);return b};if(!b)return!1;a=b.call("DOMParser"in window&&new DOMParser||window,a,"text/xml");return this.isXML(a)?a:!1}};n.jStorage={version:"0.4.3",set:function(a,b,d){q(a);d=d||{};if("undefined"==typeof b)return this.deleteKey(a),b;if(B.isXML(b))b={_is_xml:!0,xml:B.encode(b)};else{if("function"==typeof b)return;b&&"object"==typeof b&&(b=l.parse(l.stringify(b)))}c[a]=b;for(var g=c.__jstorage_meta.CRC32,e=l.stringify(b),j=e.length,f=2538058380^j,h=0,m;4<=j;)m=e.charCodeAt(h)&255|
(e.charCodeAt(++h)&255)<<8|(e.charCodeAt(++h)&255)<<16|(e.charCodeAt(++h)&255)<<24,m=1540483477*(m&65535)+((1540483477*(m>>>16)&65535)<<16),m^=m>>>24,m=1540483477*(m&65535)+((1540483477*(m>>>16)&65535)<<16),f=1540483477*(f&65535)+((1540483477*(f>>>16)&65535)<<16)^m,j-=4,++h;switch(j){case 3:f^=(e.charCodeAt(h+2)&255)<<16;case 2:f^=(e.charCodeAt(h+1)&255)<<8;case 1:f^=e.charCodeAt(h)&255,f=1540483477*(f&65535)+((1540483477*(f>>>16)&65535)<<16)}f^=f>>>13;f=1540483477*(f&65535)+((1540483477*(f>>>16)&
65535)<<16);g[a]="2."+((f^f>>>15)>>>0);this.setTTL(a,d.TTL||0);s(a,"updated");return b},get:function(a,b){q(a);return a in c?c[a]&&"object"==typeof c[a]&&c[a]._is_xml?B.decode(c[a].xml):c[a]:"undefined"==typeof b?null:b},deleteKey:function(a){q(a);return a in c?(delete c[a],"object"==typeof c.__jstorage_meta.TTL&& a in c.__jstorage_meta.TTL && delete c.__jstorage_meta.TTL[a],delete c.__jstorage_meta.CRC32[a],w(),v(),s(a,"deleted"),!0):!1},setTTL:function(a,b){var d=+new Date;q(a);b=Number(b)||0;return a in 
c ? (c.__jstorage_meta.TTL||(c.__jstorage_meta.TTL={}),0<b?c.__jstorage_meta.TTL[a]=d+b:delete c.__jstorage_meta.TTL[a],w(),x(),v(),!0):!1},getTTL:function(a){var b=+new Date;q(a);return a in c&&c.__jstorage_meta.TTL&&c.__jstorage_meta.TTL[a]?(a=c.__jstorage_meta.TTL[a]-b)||0:0},flush:function(){c={__jstorage_meta:{CRC32:{}}};w();v();s(null,"flushed");return!0},storageObj:function(){function a(){}a.prototype=c;return new a},index:function(){var a=[],b;for(b in c)c.hasOwnProperty(b)&&"__jstorage_meta"!=
b&&a.push(b);return a},storageSize:function(){return z},currentBackend:function(){return h},storageAvailable:function(){return!!h},listenKeyChange:function(a,b){q(a);j[a]||(j[a]=[]);j[a].push(b)},stopListening:function(a,b){q(a);if(j[a])if(b)for(var c=j[a].length-1;0<=c;c--)j[a][c]==b&&j[a].splice(c,1);else delete j[a]},subscribe:function(a,b){a=(a||"").toString();if(!a)throw new TypeError("Channel not defined");t[a]||(t[a]=[]);t[a].push(b)},publish:function(a,b){a=(a||"").toString();if(!a)throw new TypeError("Channel not defined");
c.__jstorage_meta||(c.__jstorage_meta={});c.__jstorage_meta.PubSub||(c.__jstorage_meta.PubSub=[]);c.__jstorage_meta.PubSub.unshift([+new Date,a,b]);w();v()},reInit:function(){C()}};a:{n=!1;if("localStorage"in window)try{window.localStorage.setItem("_tmptest","tmpval"),n=!0,window.localStorage.removeItem("_tmptest")}catch(H){}if(n)try{window.localStorage&&(g=window.localStorage,h="localStorage",r=g.jStorage_update)}catch(I){}else if("globalStorage"in window)try{window.globalStorage&&(g=window.globalStorage[window.location.hostname],
h="globalStorage",r=g.jStorage_update)}catch(J){}else if(d=document.createElement("link"),d.addBehavior){d.style.behavior="url(#default#userData)";document.getElementsByTagName("head")[0].appendChild(d);try{d.load("jStorage")}catch(K){d.setAttribute("jStorage","{}"),d.save("jStorage"),d.load("jStorage")}n="{}";try{n=d.getAttribute("jStorage")}catch(L){}try{r=d.getAttribute("jStorage_update")}catch(M){}g.jStorage=n;h="userDataBehavior"}else{d=null;break a}D();x();"localStorage"==h||"globalStorage"==
h?"addEventListener"in window?window.addEventListener("storage",u,!1):document.attachEvent("onstorage",u):"userDataBehavior"==h&&setInterval(u,1E3);E();"addEventListener"in window&&window.addEventListener("pageshow",function(a){a.persisted&&u()},!1)}})();

/*
|--------------------------------------------------------------------------
| UItoTop jQuery Plugin 1.2 by Matt Varone
| https://www.mattvarone.com/web-design/uitotop-jquery-plugin/
|--------------------------------------------------------------------------
*/
;(function($){
    $.fn.UItoTop = function(options) {

        var defaults = {
                text: '<i class="icon">' + ICONS.doubleArrowUp +'</i>',
                min: 600,
                inDelay:400,
                outDelay:400,
                containerID: 'toTop',
                containerHoverID: 'toTopHover',
                scrollSpeed: 1200,
                easingType: 'linear'
            },
            settings = $.extend(defaults, options),
            containerIDhash = '#' + settings.containerID,
            containerHoverIDHash = '#'+settings.containerHoverID;
        
        $('body').append('<a href="#" id="'+settings.containerID+'">'+settings.text+'</a>');
        $(containerIDhash).hide().on('click.UItoTop',function(){
            $('html, body').animate({scrollTop:0}, settings.scrollSpeed, settings.easingType);
            $('#'+settings.containerHoverID, this).stop().animate({'opacity': 0 }, settings.inDelay, settings.easingType);
            return false;
        })
        .prepend('<span id="'+settings.containerHoverID+'"></span>')
        .hover(function() {
                $(containerHoverIDHash, this).stop().animate({
                    'opacity': 1
                }, 600, 'linear');
            }, function() { 
                $(containerHoverIDHash, this).stop().animate({
                    'opacity': 0
                }, 700, 'linear');
            });
                    
        $(window).scroll(function() {
            var sd = $(window).scrollTop();
            if(typeof document.body.style.maxHeight === "undefined") {
                $(containerIDhash).css({
                    'position': 'absolute',
                    'top': sd + $(window).height() - 50
                });
            }
            if ( sd > settings.min ) {
                            $(containerIDhash).fadeIn(settings.inDelay);
                        } else {
                            $(containerIDhash).fadeOut(settings.Outdelay);
                        }
        });
};
})(jQuery);






function create_custom_dropdowns() {
  $("select.nts-dropdown").each(function (i, select) {
    if (!$(this).next().hasClass("dropdown-select")) {
      $(this).after(
        '<div class="dropdown-select wide ' +
          ($(this).attr("class") || "") +
          '" tabindex="0"><span class="current"></span><div class="list"><ul></ul></div></div>'
      );
      var dropdown = $(this).next();
      var options = $(select).find("option");
      var selected = $(this).find("option:selected");

      dropdown
        .find(".current")
        .html(
          selected.data("display-text") ||
            (selected.attr("data-tpl")
              ? selected.html(selected.attr("data-tpl")).html()
              : selected.text())
        );

      options.each(function (j, o) {
        var display = $(o).data("display-text") || "";
        dropdown
          .find("ul")
          .append(
            '<li class="option ' +
              ($(o).is(":selected") ? "selected" : "") +
              '" data-value="' +
              $(o).val() +
              '" data-display-text="' +
              display +
              '">' +
              ($(o).attr("data-tpl")
                ? $(o).html($(o).attr("data-tpl")).html()
                : $(o).text()) +
              "</li>"
          );
      });
    }
  });

  $(".dropdown-select ul").before(
    '<div class="dd-search"><input id="txtSearchValue" autocomplete="off" onkeyup="filter()" class="dd-searchbox" type="text"></div>'
  );
}

// Event listeners

// Open/close
$(document).on("click", ".dropdown-select", function (event) {
  if ($(event.target).hasClass("dd-searchbox")) {
    return;
  }
  $(".dropdown-select").not($(this)).removeClass("open");
  $(this).toggleClass("open");
  if ($(this).hasClass("open")) {
    $(this).find(".option").attr("tabindex", 0);
    $(this).find(".selected").focus();
  } else {
    $(this).find(".option").removeAttr("tabindex");
    $(this).focus();
  }
});

// Close when clicking outside
$(document).on("click", function (event) {
  if ($(event.target).closest(".dropdown-select").length === 0) {
    $(".dropdown-select").removeClass("open");
    $(".dropdown-select .option").removeAttr("tabindex");
  }
  event.stopPropagation();
});

function filter() {
  var valThis = $("#txtSearchValue").val();
  $(".dropdown-select ul > li").each(function () {
    var text = $(this).text();
    text.toLowerCase().indexOf(valThis.toLowerCase()) > -1
      ? $(this).show()
      : $(this).hide();
  });
}
// Search

// Option click
$(document).on("click", ".dropdown-select .option", function (event) {
  $(this).closest(".list").find(".selected").removeClass("selected");
  $(this).addClass("selected");
  var text = $(this).data("display-text") || $(this).text();
  $(this).closest(".dropdown-select").find(".current").text(text);
  $(this)
    .closest(".dropdown-select")
    .prev("select")
    .val($(this).data("value"))
    .trigger("change");
});

// Keyboard events
$(document).on("keydown", ".dropdown-select", function (event) {
  var focused_option = $(
    $(this).find(".list .option:focus")[0] ||
      $(this).find(".list .option.selected")[0]
  );
  // Space or Enter
  //if (event.keyCode == 32 || event.keyCode == 13) {
  if (event.keyCode == 13) {
    if ($(this).hasClass("open")) {
      focused_option.trigger("click");
    } else {
      $(this).trigger("click");
    }
    return false;
    // Down
  } else if (event.keyCode == 40) {
    if (!$(this).hasClass("open")) {
      $(this).trigger("click");
    } else {
      focused_option.next().focus();
    }
    return false;
    // Up
  } else if (event.keyCode == 38) {
    if (!$(this).hasClass("open")) {
      $(this).trigger("click");
    } else {
      var focused_option = $(
        $(this).find(".list .option:focus")[0] ||
          $(this).find(".list .option.selected")[0]
      );
      focused_option.prev().focus();
    }
    return false;
    // Esc
  } else if (event.keyCode == 27) {
    if ($(this).hasClass("open")) {
      $(this).trigger("click");
    }
    return false;
  }
});

$(document).ready(function () {
  create_custom_dropdowns();
});