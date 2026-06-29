;(function($, window){
    'use strict';
    var App = window.App;

    /**
     *
     * @returns {{}}
     * @constructor
     */
    App.Route.GetUrlVars = function() {
        var vars = {};
        var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function (m, key, value) {
            vars[key] = value;
        });
        return vars;
    };

    /**
     *
     * @param route
     * @param params
     * @returns {string}
     * @constructor
     */
    App.Route.CreateAdminUrl = function(route, params) {
        var url = window.nt.http_home + 'index.php?r=' + route + '&token=' + App.Route.GetUrlVars()["token"];
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
    };

    /**
     *
     * @returns {*}
     * @constructor
     */
    App.Route.GetCurrentRoute = function() {
        return App.Route.GetUrlVars()["r"];
    };

    /**
     *
     * @returns {*}
     * @constructor
     */
    App.Route.GetRealRoute = function() {
        return window.nt.route;
    };

    /**
     *
     * @returns {string[]}
     * @constructor
     */
    App.Route.GetIgnoredRoutes = function() {
        return [
            'common/login',
            'common/login/login',
            'common/login/recover',
            'common/login/ping',
            'common/logout',
            'error/not_found',
            'error/permission'
        ];
    };

    window.App = App;
})(jQuery, window);