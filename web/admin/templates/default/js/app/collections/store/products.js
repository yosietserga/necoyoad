;(function($, window){
    'use strict';
    var App = window.App;

    App.Collections.Products = Backbone.Collection.extend({
        model: App.Models.Product,
        url: App.Route.CreateAdminUrl('store/product/api'),
        parse: function(response, options) {
            return response.data;
        }
    });

    window.App = App;
})(jQuery, window);