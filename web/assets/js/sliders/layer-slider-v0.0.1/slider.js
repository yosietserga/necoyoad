// below is the plugin code
'use strict';

const hasClass = (el, className) => {
    return (typeof el != 'undefined' && typeof el.classList != 'undefined') ? 
        el.classList.contains(className) : new RegExp('(^| )' + className + '( |$)', 'gi').test(el.className);
};

const addClass = (el, className) => {
    if (typeof el != 'undefined' && typeof el.classList != 'undefined') {
        el.classList.add(className);
    } else if (typeof el != 'undefined') {
        el.className += ' ' + className;
    }
};

const removeClass = (el, className) => {
    if (typeof el != 'undefined' && typeof el.classList != 'undefined') {
        el.classList.remove(className);
    } else if (typeof el != 'undefined') {
        el.className = el.className.replace(new RegExp('(^|\\b)' + className.split(' ').join('|') + '(\\b|$)', 'gi'), ' ');
    }
};

const extendObj = (_def, addons) => {
    if (typeof addons !== "undefined") {
        for (var prop in _def) {
            if (addons[prop] != undefined) {
                _def[prop] = addons[prop];
            }
        }
    }
};

class slider_plugin {

    constructor(settings) {
        this.__ = {};

        // always loop
        this.__.def = {
            parentSelector: '.layer-slider-container',
            targetSelector: '.layer-slider',
            dotsSelector: '.dots-wrapper',
            arrowLeftSelector: '.arrow-left',
            arrowRightSelector: '.arrow-right',
            transition: {
                speed: 300,
                easing: ''
            },
            swipe: true,
            autoHeight: true,
            beforeChangeSlide: () => {},
            afterChangeSlide: () => {}
        };

        extendObj(this.__.def, settings);
    }

    start() {
        this.__.parent = document.querySelector( this.__.def.parentSelector );
        this.__.target = this.__.parent.querySelector( this.__.def.targetSelector );
        this.__.dotsWrapper = this.__.parent.querySelector( this.__.def.dotsSelector );
        this.__.arrowLeft = this.__.parent.querySelector( this.__.def.arrowLeftSelector );
        this.__.arrowRight = this.__.parent.querySelector( this.__.def.arrowRightSelector );
        this.init();
    }

    beforeChangeSlide() {
        let prevSlide = this.getPrevSlide();
        let nextSlide = this.getNextSlide();
        let curSlide  = this.getCurSlide();
        let items  = curSlide.querySelectorAll('[data-animate]');

        if (typeof prevSlide != 'undefined') {
            prevSlide.querySelectorAll('[data-animate]').forEach(function(v,k){
                resetAnimation( v );
            });
        }

        if (typeof nextSlide != 'undefined') {
            nextSlide.querySelectorAll('[data-animate]').forEach(function(v,k){
                resetAnimation( v );
            });
        }

        items.forEach(function(v,k){
            resetAnimation( v );
        });

        if (this.__.def.beforeChangeSlide && typeof this.__.def.beforeChangeSlide == 'function') {
            this.__.def.beforeChangeSlide(this);
        }
    }

    afterChangeSlide() {
        let curSlide  = this.getCurSlide();
        let items  = curSlide.querySelectorAll('[data-animate]');

        items.forEach(function(v,k){
            animateTransition( v, true );
        });

        if (this.__.def.afterChangeSlide && typeof this.__.def.afterChangeSlide == 'function') {
            this.__.def.afterChangeSlide(this);
        }
    }

    buildDots() {
        if (this.__.dotsWrapper) {
            for (let i = 0; i < this.__.totalSlides; i++) {
                let dot = document.createElement('li');
                dot.setAttribute('data-slide', i);
                this.__.dotsWrapper.appendChild(dot);
            }
            let that = this;
            this.__.dotsWrapper.addEventListener('click', function(e) {
                if (e.target && e.target.nodeName == "LI") {
                    that.__.curSlide = e.target.getAttribute('data-slide');
                    that.gotoSlide();
                }
            }, false);
        }
    }

    getCurLeft() {
        this.__.curLeft = parseInt(this.__.sliderInner.style.left.split('px')[0]);
    }

    gotoSlide() {
        this.beforeChangeSlide();

        if (this.__.curSlide < 0) {
            this.__.curSlide = this.__.totalSlides - 1;
        } else if (this.__.curSlide >= this.__.totalSlides) {
            this.__.curSlide = 0;
        }

        let allSlides = this.getSlides();
        let that = this;

        this.__.sliderInner.style.transition = 'left ' + this.__.def.transition.speed / 1000 + 's ' + this.__.def.transition.easing;
        this.__.sliderInner.style.left = -parseInt(allSlides[this.__.curSlide].offsetLeft) + 'px';
        addClass(this.__.target, 'isAnimating');
        setTimeout(function() {
            that.__.sliderInner.style.transition = '';
            removeClass(that.__.target, 'isAnimating');
        }, that.__.def.transition.speed);
        this.setDot();
        if (this.__.def.autoHeight && allSlides[this.__.curSlide]) {
            this.__.target.style.height = allSlides[this.__.curSlide].offsetHeight + "px";
        }

        this.afterChangeSlide();
    }

    getCurSlideIndex() {
        if (this.__.curSlide < 0) {
            this.__.curSlide = this.__.totalSlides - 1;
        } else if (this.__.curSlide >= this.__.totalSlides) {
            this.__.curSlide = 0;
        }
        return this.__.curSlide;
    }

    getCurSlide() {
        let allSlides = this.getSlides();
        return allSlides[this.getCurSlideIndex()];
    }

    getNextSlide() {
        let allSlides = this.getSlides();
        return allSlides[ (this.getCurSlideIndex() + 1) ];
    }
    
    getPrevSlide() {
        let allSlides = this.getSlides();
        return allSlides[ this.getCurSlideIndex() - 1 ];
    }
    
    getSlides() {
        this.__.allSlides = this.__.target.querySelectorAll('[data-slide]');
        return this.__.allSlides;
    }
    
    getSlidesTotal() {
        this.__.totalSlides = this.__.target.querySelectorAll('[data-slide]').length;
        return this.__.totalSlides;
    }

    init() {
        const on_resize = (c, t) => {
            return resetTimer(c, t);
        };

        const resetTimer = (c, t) => {
            clearTimeout(t);
            t = setTimeout(c, 100);
        };

        let that = this;
        const loadedImg = (el) => {
            let loaded = false;

            const loadHandler = () => {
                if (loaded) return;

                loaded = true;
                that.__.loadedCnt++;
                if (that.__.loadedCnt >= that.__.totalSlides + 2) {
                    that.updateSliderDimension();
                }
            };

            let img = el.querySelector('img');

            if (img) {
                img.onload = loadHandler;
                img.src = img.getAttribute('data-src');
                img.style.display = 'block';
                if (img.complete) {
                    loadHandler();
                }
            } else {
                that.updateSliderDimension();
            }
        };

        window.addEventListener("resize", (e) => {
            on_resize(that.updateSliderDimension, false);
        });

        // wrap slider-inner
        let nowHTML = this.__.target.innerHTML;
        this.__.target.innerHTML = '<div class="layer-slider-inner">' + nowHTML + '</div>';

        this.__.curSlide = 0;
        this.__.curLeft = 0;

        this.__.sliderInner = this.__.target.querySelector('.layer-slider-inner');
        this.__.loadedCnt = 0;

        if (this.getSlidesTotal() > 0) {
            let allSlides = this.getSlides();

            this.__.target.style.height = allSlides[0].offsetHeight + "px";
            this.__.sliderInner.style.width = (this.__.totalSlides + 2) * 100 + "%";
            for (let _i = 0; _i < this.__.totalSlides + 2; _i++) {
                if (!allSlides[_i]) continue;
                allSlides[_i].style.width = 100 / (this.__.totalSlides + 2) + "%";
                loadedImg(allSlides[_i]);
            }
            if (this.getSlidesTotal() > 1) {
                this.buildDots();
                this.setDot();
                this.initArrows();
            } else {
                this.__.dotsWrapper.remove();
                this.__.arrowLeft.remove();
                this.__.arrowRight.remove();
            }

            this.__.target.style.height = allSlides[0].offsetHeight + "px";
            if (allSlides[0].offsetHeight == 0) this.__.target.style.height = allSlides[0].children[0].offsetHeight + "px";
        };

        const addListenerMulti = (el, s, fn) => {
            s.split(' ').forEach(function(e) {
                return el.addEventListener(e, fn, false);
            });
        };

        const removeListenerMulti = (el, s, fn) => {
            s.split(' ').forEach(function(e) {
                return el.removeEventListener(e, fn, false);
            });
        };

        const startSwipe = (e) => {
            let touch = e;
            that.getCurLeft();
            if (!that.__.isAnimating) {
                if (e.type == 'touchstart') {
                    touch = e.targetTouches[0] || e.changedTouches[0];
                }
                that.__.startX = touch.pageX;
                that.__.startY = touch.pageY;

                addListenerMulti(that.__.sliderInner, 'mousemove touchmove', swipeMove);
                addListenerMulti(document.querySelector('body'), 'mouseup touchend', swipeEnd);
            }
        };

        const swipeMove = (e) => {
            let touch = e;
            if (e.type == 'touchmove') {
                touch = e.targetTouches[0] || e.changedTouches[0];
            }
            that.__.moveX = touch.pageX;
            that.__.moveY = touch.pageY;

            // for scrolling up and down
            if (Math.abs(that.__.moveX - that.__.startX) < 40) return;

            that.__.isAnimating = true;
            addClass(that.__.target, 'isAnimating');
            e.preventDefault();

            if (that.__.curLeft + that.__.moveX - that.__.startX > 0 && that.__.curLeft == 0) {
                that.__.curLeft = -that.__.totalSlides * that.__.slideW;
            } else if (that.__.curLeft + that.__.moveX - that.__.startX < -(that.__.totalSlides + 1) * that.__.slideW) {
                that.__.curLeft = -that.__.slideW;
            }
            that.__.sliderInner.style.left = that.__.curLeft + that.__.moveX - that.__.startX + "px";
        };

        const swipeEnd = (e) => {
            let touch = e;
            that.getCurLeft();

            if (Math.abs(that.__.moveX - that.__.startX) === 0) return;

            that.__.stayAtCur = Math.abs(that.__.moveX - that.__.startX) < 40 || typeof that.__.moveX === "undefined" ? true : false;
            that.__.dir = that.__.startX < that.__.moveX ? 'left' : 'right';

            if (that.__.stayAtCur) {} else {
                that.__.dir == 'left' ? that.__.curSlide-- : that.__.curSlide++;
                if (that.__.curSlide < 0) {
                    that.__.curSlide = that.__.totalSlides-1;
                } else if (that.__.curSlide == that.__.totalSlides) {
                    that.__.curSlide = 0;
                }
            }

            that.gotoSlide();

            delete that.__.startX;
            delete that.__.startY;
            delete that.__.moveX;
            delete that.__.moveY;

            that.__.isAnimating = false;
            removeClass(that.__.target, 'isAnimating');
            removeListenerMulti(that.__.sliderInner, 'mousemove touchmove', swipeMove);
            removeListenerMulti(document.querySelector('body'), 'mouseup touchend', swipeEnd);
        };

        if (this.__.def.swipe) {
            addListenerMulti(this.__.sliderInner, 'mousedown touchstart', startSwipe);
        }

        this.__.isAnimating = false;
        this.updateSliderDimension();
    }

    initArrows() {
        let that = this;
        if (this.__.arrowLeft) {
            this.__.arrowLeft.addEventListener('click', function() {
                if (!hasClass(that.__.target, 'isAnimating')) {
                    that.__.curSlide--;
                    setTimeout(function() {
                        that.gotoSlide();
                    }, 20);
                }
            }, false);
        }

        if (this.__.arrowRight) {
            this.__.arrowRight.addEventListener('click', function() {
                if (!hasClass(that.__.target, 'isAnimating')) {
                    that.__.curSlide++;
                    setTimeout(function() {
                        that.gotoSlide();
                    }, 20);
                }
            }, false);
        }
    }

    setDot() {
        if (this.__.dotsWrapper) {
            let tardot = this.__.curSlide;

            for (let j = 0; j < this.__.totalSlides; j++) {
                removeClass(this.__.dotsWrapper.querySelectorAll('li')[j], 'active');
            }

            if (this.__.curSlide - 1 < 0) {
                tardot = 0;
            } else if (this.__.curSlide >= this.__.totalSlides) {
                tardot = this.__.totalSlides - 1;
            }
            addClass(this.__.dotsWrapper.querySelectorAll('li')[tardot], 'active');
        }
    }

    updateSliderDimension() {
        let w = this.__.target.offsetWidth;
        let allSlides = this.getSlides();

        if (allSlides.length > 0) {
            let curSlide = this.getCurSlide();
            allSlides.forEach((v,k)=>{
                allSlides[k].style.width = w +'px';
            });

            this.__.slideW = parseInt(allSlides[0].offsetWidth);
            this.__.sliderInner.style.left = -this.__.slideW * this.__.curSlide + "px";

            if (this.__.def.autoHeight) {
                this.__.target.style.height = curSlide.offsetHeight + "px";
            } else {
                for (var i = 0; i < this.__.totalSlides + 2; i++) {
                    if (allSlides[i].offsetHeight > this.__.target.offsetHeight) {
                        this.__.target.style.height = allSlides[i].offsetHeight + "px";
                    }
                }
            }
        }
    }
}

(function ($) {
    'use strict';
    $.fn.slider_plugin = function (method) {
        var methods = {
            init: function (options) {
                return this.each(function () {
                    let slider = new slider_plugin(options);
                    slider.start();
                });
            }
        };

        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || !method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error('Method "' + method + '" does not exist in ntForm plugin!');
        }
    };
})(jQuery);