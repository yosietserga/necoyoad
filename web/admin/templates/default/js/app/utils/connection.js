;(function($, window){
    'use strict';
    var App = window.App;

    App.Utils.Connection = {};

    /**
     *
     * @type {boolean}
     */
    App.Utils.Connection.Live = true;

    /**
     *
     * @constructor
     */
    App.Utils.Connection.Ping = function() {
        if ($.inArray(App.Route.GetRealRoute(), App.Route.GetIgnoredRoutes()) < 0) {
            $.ajax({
                url:App.Route.CreateAdminUrl('common/home/ping'),
                type:'GET',
                dataType:'json',
                statusCode: {
                    200: function() {
                        App.Utils.Connection.Live = true;
                    },
                    201: function() {
                        App.Utils.Connection.Live = true;
                    },
                    400: function() {
                        App.Utils.Connection.Warn();
                    },
                    500: function() {
                        App.Utils.Connection.Warn();
                    },
                    503: function() {
                        App.Utils.Connection.Warn();
                    }
                }
            })
            .done(function() {
                App.Utils.Connection.Live = true;
            })
            .fail(function(response) {
                App.Utils.Connection.Warn(response);
            });
        }
    };

    /**
     *
     * @constructor
     */
    App.Utils.Connection.PingCheck = function() {
        setInterval(function() {
            App.Utils.Connection.PingCheck();
        }, 3000);
    }

    /**
     *
     * @param response
     * @constructor
     */
    App.Utils.Connection.Warn = function(response) {
        App.Utils.Connection.Live = false;
        console.log('No hay conexion con el servidor');
        /**
         //TODO: show message warning that connection is off
         //TODO: send email to necoyoad with complete report
         **/
    };

    /**
     *
     * @param action
     * @param data
     * @param cb
     * @returns {*}
     * @constructor
     */
    App.Utils.Connection.Fetch = function(action, data, cb) {
        if (typeof data == 'undefined') {
            data = null;
        }
        if (typeof cb == 'undefined') {
            cb = null;
        }
        var data = data,
            action = action,
            Model = this,
            dbIndex = 'Product:'+ modelId;

        App.DB.set(dbIndex, data);

        if (App.Utils.Connection.Live) {
            return $.ajax({
                url:App.Route.CreateAdminUrl('store/product/api', {resp:'json', action:action}),
                type:'GET',
                dataType:'json',
                data:data
            });
        } else {
            /** //TODO: handle no connection **/
        }
    };

    /**
     *
     * @param type
     * @param action
     * @param data
     * @param cb
     * @returns {*}
     * @constructor
     */
    App.Utils.Connection.Push = function(type, action, data, cb) {
        if (typeof cb == 'undefined') {
            cb = null;
        }

        var data = data,
            action = action,
            type = type,
            Model = this,
            dbIndex = 'Product:'+ modelId;

        App.DB.set(dbIndex, data);

        if (App.Utils.Connection.Live) {
            return $.ajax({
                url:App.Route.CreateAdminUrl('store/product/api', {resp:'json', action:action, id:modelId}),
                type:type,
                dataType:'json',
                data:data
            });
        } else {
            SaveDeferred(type, action, dbIndex);
        }
    };

    window.App = App;
})(jQuery, window);