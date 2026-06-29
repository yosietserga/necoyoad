<link rel="dns-prefetch" href="www.necoyoad.com">

<style>
    .oferta:before {
        content: "<?php echo $l('sticker_offer'); ?>";
    }
    .nuevo:before {
        content: "<?php echo $l('sticker_new'); ?>";
    }
    .descuento:before {
        content: "<?php echo $l('sticker_special');?>";
    }
</style>

<?php if (count($header_javascripts) > 0) foreach ($header_javascripts as $js) { if (empty($js)) continue; ?>
<script type="text/javascript" src="<?php echo $js; ?>"></script>
<?php } ?>
<?php if (!empty($scripts)) echo $scripts; ?>

<script>
    (function (w) {
        w.I18n = Object.freeze({
            Common: Object.freeze({
                addToCart: '<?php echo $l('add_to_cart'); ?>',
                goToCart: '<?php echo $l('go_to_cart'); ?>',
                accept: '<?php echo $l('accept'); ?>',
                cancel: '<?php echo $l('cancel'); ?>',
            }),
            Product: Object.freeze({
                model: '<?php echo $l('product_model'); ?>',
                successfulAddToCart: '<?php echo $l(' successful_add_to_cart'); ?>',

            }),
            Form: Object.freeze({
                Warnings: Object.freeze({
                    alphaNumeric: '<?php echo $l('form_warning_alphanumeric'); ?>',
                    rif: '<?php echo $l('form_warning_rif'); ?>',
                    date: '<?php echo $l('form_warning_date'); ?>',
                    float: '<?php echo $l('form_warning_float'); ?>',
                    email: '<?php echo $l('form_warning_email'); ?>',
                    numeric: '<?php echo $l('form_warning_numeric'); ?>',
                    phone: '<?php echo $l('form_warning_phone'); ?>',
                    password: '<?php echo $l('form_warning_password'); ?>',
                    confirm: '<?php echo $l('form_warning_confirm'); ?>',
                    plain: '<?php echo $l('form_warning_empty'); ?>',
                    empty: '<?php echo $l('form_warning_empty'); ?>',
                })
            })
        });
        w.Context = Object.freeze({
            User: {
                name: '<?php echo $this->customer->getFirstName(); ?>',
                lastname: '<?php echo $this->customer->getLastName(); ?>',
                isLogged: '<?php echo $isLogged; ?>',
            },
            Product: {
                id: '<?php echo $product_id ?>',
            },
        });
        w.Constants = Object.freeze({
                CSS_PATH: '<?php echo HTTP_CSS; ?>',
                JS_PATH : '<?php echo HTTP_JS; ?>',
                IMAGES_PATH : '<?php echo HTTP_IMAGE; ?>',

                THEMECSS_PATH: '<?php echo HTTP_THEME_CSS; ?>',
                THEMEJS_PATH: '<?php echo HTTP_THEME_JS; ?>',
                THEMEFONTS_PATH : '<?php echo HTTP_THEME_FONT; ?>',
                THEMEIMAGES_PATH : '<?php echo HTTP_THEME_IMAGE; ?>',
                THEMESVG_PATH : '<?php echo HTTP_HOME . 'assets/themes/' . $this->config->get('config_template ') . '/svg-build/'?>',
                IS_STORE: Number('<?php echo ($Config->get('config_store_mode') === 'store'); ?>')
    });
        w.Requests = Object.freeze({
            QUICK_VIEW: "<?php echo $Url::createUrl('store/product/quickviewjson');?>"
        });
        w.Mq = Object.freeze({
            small: window.matchMedia("only screen and (max-width: 39.9375em)").matches,
            smallUp: window.matchMedia("only screen").matches,
            mediumUp: window.matchMedia("only screen and (min-width: 40em)").matches,
            medium: window.matchMedia("only screen and (min-width: 40em) and (max-width: 63.9375em)").matches,
            smallToMedium: window.matchMedia("only screen and (max-width: 63.9375em)").matches,
            large: window.matchMedia("only screen and (min-width: 64em) and (max-width: 74.9375em)").matches,
            largeUp: window.matchMedia("only screen and (min-width: 64em)").matches,
        });
    })(window);


    /* @@cc_on
     @@if (@@_jscript_version <= 6)
     (function (f) {window.setTimeout = f(window.setTimeout)})(function (f) {
     return function (c, t) {
     var a = [].slice.call(arguments, 2);
     return f(function () {
     c.apply(this, a) }, t);
     }
     }
     );
     @@end
     @@*/

    /*window.requestAnimationFrame Polyfill*/

    (function () {
        var x;
        var lastTime = 0;
        var vendors = ['ms', 'moz', 'webkit', 'o'];
        for (x = 0; x < vendors.length && !window.requestAnimationFrame; x++) {
            window.requestAnimationFrame = window[vendors[x] + 'RequestAnimationFrame'];
            window.cancelAnimationFrame = window[vendors[x] + 'CancelAnimationFrame'] || window[vendors[x] + 'CancelRequestAnimationFrame'];
        }

        if (!window.requestAnimationFrame) {
            window.requestAnimationFrame = function (callback, element) {
                var currTime = new Date().getTime();
                var timeToCall = Math.max(0, 16 - (currTime - lastTime));
                var id = window.setTimeout(function () {
                            callback(currTime + timeToCall);
                        },
                        timeToCall);
                lastTime = currTime + timeToCall;
                return id;
            };
        }

        if (!window.cancelAnimationFrame) {
            window.cancelAnimationFrame = function (id) {
                clearTimeout(id);
            };
        }
        window.useRAF = (window.requestAnimationFrame !== undefined);

        window.popupWindow = function (url, title, w, h) {
            var dualScreenLeft = screen.left;
            var dualScreenTop = screen.top;

            var width = screen.width;
            var height = screen.height;

            var left = ((width / 2) - (w / 2)) + dualScreenLeft;
            var top = ((height / 2) - (h / 2)) + dualScreenTop;

            var newWindow = window.open(url, title, 'scrollbars=yes, width=' + w + ', height=' + h + ', top=' + top + ', left=' + left);
            newWindow.focus();
        };
    })();


    (function (styleSheetLinks, documentStyleSheets, w) {
        'use strict';

        var mobileFactor = "(max-width: 64em)";
        w.isModernBrowser = ("querySelector" in document && "addEventListener" in w && "localStorage" in w && "sessionStorage" in w && "bind" in Function && ( ("XMLHttpRequest" in w && "withCredentials" in new XMLHttpRequest()) || "XDomainRequest" in w ) );

        w.doSync = function (/*fn, args*/) {
            var fn = [].slice.call(arguments, 0, 1)[0];
            var args = [].slice.call(arguments, 1);
            if (w.useRAF) {
                w.requestAnimationFrame(function () {
                    fn.apply(this, args);
                });
            } else {
                setTimeout(fn, null, args);
            }
        };

        w.deferjQuery = function (fn) {
            if (w.$) {
                $(function () {
                    fn();
                    return true;
                });
            } else {
                w.doSync(w.deferjQuery, fn);
            }
        };

        w.deferPlugin = function (context, fn) {
            w.deferjQuery(function deferPlugin() {
                if (jQuery[context] || jQuery.fn[context]) {
                    fn();
                    return true;
                } else {
                    w.doSync(w.deferPlugin, context, fn);
                }
            });
        };

        w.makeStyle = function (href, media) {
            var l = document.createElement("link");
            l.setAttribute('href', href);
            l.setAttribute('rel', 'stylesheet');
            l.setAttribute('media', media);
            return l;
        };

        w.appendStyle = function (style) {
            document.head.appendChild(style);
        };

        w.inStyleSheets = function (href) {
            return [].some.call(document.styleSheets, function (s) {
                var sh = s.href;
                return (sh && sh.indexOf(href) > -1);
            });
        };

        w.setMedia = function (style, media) {
            var setIn = w.inStyleSheets(style.href);
            if (setIn) {
                style.media = media;
                return true;
            }
            w.doSync(setMedia, style, media);
        };

        w.setMediaMobile = function (style) {
            w.setMedia(style, mobileFactor);
        };

        w.setMediaDesktop = function (style) {
            w.setMedia(style, "screen");
        };

        w.fetchStyle = function (href) {
            var style;
            if (w.inStyleSheets(href)) {
                return true;
            }
            style = w.makeStyle(href, 'only x');
            w.appendStyle(style);
            w.setMediaDesktop(style);
        };

        /*
         * The same as with but first check is the browser is modern
         * it is then it will just append the script with src and the async keyword
         * if not then it will just make a ajax request to fetch the script text and append it to the head
         */
        w.inScripts = function (src) {
            return [].some.call(document.scripts, function (s) {
                var ss = s.src;
                return (ss && ss.indexOf(src) > -1);
            });
        };

        w.makeScript = function () {
            var sp = document.createElement("script");
            sp.async = true;
            return sp;
        };

        w.makeScriptWithText = function (text) {
            var sp = w.makeScript();
            sp.text = text;
            return sp;
        };

        w.makeScriptWithSrc = function (src) {
            var sp;
            sp = w.makeScript();
            sp.src = src;
            return sp;
        };

        w.appendToHead = function (el) {
            try {
                document.head.appendChild(el);
                return true;
            } catch (e) {
                console.log(e);
                return false;
            }
        };
        w.appendToBody = function (el) {
            try {
                document.body.appendChild(el);
                return true;
            } catch (e) {
                console.log(e);
                return false;
            }
        };

        w.appendScriptSource = function (src) {
            if (w.inScripts(src)) {
                return true;
            }
            w.appendToBody(w.makeScriptWithSrc(src));
            return true;
        };

        w.fetchScript = (function (src) {
            if (w.inScripts(src)) {
                return true;
            }
            var req = new XMLHttpRequest();

            if (w.isModernBrowser) {
                w.appendToHead(w.makeScriptWithSrc(src));
            } else {
                req.open('GET', src, true);
                req.onreadystatechange = function (e) {
                    if (req.readyState === 4) {
                        if (req.status === 200) {
                            w.appendToHead(makeScriptWithText(req.responseText));
                        } else {
                            console.log("Error: couldn't load script from: " + src);
                        }
                    }
                };
                req.send(null);
            }
        });

        [].forEach.call(styleSheetLinks, function (s) {
            var media = s.media;
            if (media === 'only x') w.setMediaDesktop(s);
            if (media === 'only m') w.setMediaMobile(s);
        });
    })(document.getElementsByTagName('link'), window.document.styleSheets, window);
</script>