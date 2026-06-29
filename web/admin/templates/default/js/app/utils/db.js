(function($, window){
    'use strict';
    var App = window.App;

    App.DB.get = function(index) {
        return $.jStorage.get(index, null);
    };

    App.DB.set = function(index, value) {
        $.jStorage.set(index, value);
    };

    App.DB.clear = function(index) {
        $.jStorage.deleteKey(index);
    };

    window.App = App;
})(jQuery, window);