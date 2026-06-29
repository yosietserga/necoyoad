(function($, window){
    'use strict';
    var App = window.App;

    App.Utils.Image = {};

    App.Utils.Image.Render = function(options) {
        var o = $.extend({}, options);
        o.src = o.src || '/web/assets/images/cache/no_image-100x100.jpg';
        $('#'+ o.preview).replaceWith('<img src="'+ o.src +'" id="' + o.preview + '" class="image" onclick="App.Utils.UploadImage(\'' + o.field + '\', \'' + o.preview + '\');">');
    };

    App.Utils.Image.Delete = function(field, preview) {
        $('#' + field).val('');
        $('#' + preview).attr('src','/web/assets/images/cache/no_image-100x100.jpg');
    };

    App.Utils.Image.Upload = function(field, preview) {
        var height = $(window).height() * 0.8;
        var width = $(window).width() * 0.8;

        $('#dialog').remove();
        $('.box').prepend('<div id="dialog" style="padding: 3px 0px 0px 0px;z-index:10000;"><iframe src="'+ App.Route.CreateAdminUrl("common/filemanager") +'&field='+ encodeURIComponent(field) +'" style="padding:0; margin: 0; display: block; width: 100%; height: 100%;z-index:10000;" frameborder="no" scrolling="auto"></iframe></div>');

        $('#dialog').dialog({
            title: 'File Manager',
            close: function (event, ui) {
                if ($('#' + field).attr('value')) {
                    $.ajax({
                        url: App.Route.CreateAdminUrl("common/filemanager/image"),
                        type: 'POST',
                        data: 'image=' + encodeURIComponent($('#' + field).val()),
                        dataType: 'text'
                    }).done(function(data) {
                        App.Utils.Image.Render({
                            src: data,
                            preview: preview,
                            field: field
                        });
                        /** $('#' + preview).replaceWith('<img src="' + data + '" id="' + preview + '" class="image" onclick="App.Utils.Image.Upload(\'' + field + '\', \'' + preview + '\');">'); **/
                    }).fail(function(){

                    });
                }
            },
            bgiframe: false,
            width: width,
            height: height,
            resizable: false,
            modal: false
        });
    };

    window.App = App;
})(jQuery, window);