(function($, window){
    'use strict';
    var App = window.App;

    App.Utils.Task = {};

    App.Utils.Task.AddQueue = function(path, params, dbIndex) {
        if (!App.Data.TasksQueue) {
            App.Data.TasksQueue = {};
        }
        App.Data.TasksQueue[path] = {
            path:path,
            params:params,
            dbIndex:dbIndex,
            status:'waiting'
        };
        App.DB.set('Tasks'+ App.Constants.uniqid, App.Data.TasksQueue);
        /**
         //TODO: PELIGRO!! asegurar, autentificar y validar cada tarea a ejecutar en ajax
         **/
        console.log('task added to the queue');
    };

    App.Utils.Task.Exec = function() {console.log('task execution begin');
        App.Data.TasksQueue = App.DB.get('Tasks'+ App.Constants.uniqid);
        console.log(App.Data.TasksQueue);
        console.log('task queue loaded');
        if (App.Data.TasksQueue) {
            console.log('task queue checked');
            $.each(App.Data.TasksQueue, function (i, task) {
                if (task.params.type == 'connect' && App.Utils.Connection.Live) {

                    if (!task.params.requestType) {
                        task.params.requestType = 'GET';
                    }
                    if (!task.params.requestDataType) {
                        task.params.requestDataType = 'json';
                    }
                    console.log('async request begin');
                    $.ajax({
                        url: task.params.requestUrl,
                        type: task.params.requestType.toUpperCase(),
                        data: App.DB.get(task.dbIndex),
                        beforeSend: function () {
                            console.log('async request beforeSend');
                            if (typeof task.params.beforeSend == 'function') {
                                task.params.beforeSend();
                            }
                        }
                    })
                    .done(function(response) {
                        console.log('async request success');
                        if (typeof task.params.onSuccess == 'function') {
                            task.params.onSuccess(response);
                        }
                        delete App.Data.TasksQueue[i];
                        App.DB.set('Tasks' + App.Constants.uniqid, App.Data.TasksQueue);
                    })
                    .fail(function(response) {
                        console.log('async request error');
                        console.log(response);
                        if (typeof task.params.onError == 'function') {
                            task.params.onError(response);
                        }
                    })
                    .always(function() {

                    });
                }
            });
        }
    };

    App.Utils.Task.Run = function() {
        setInterval(function() {
            App.Utils.Task.Exec();
        }, 3000);
    };

    window.App = App;
})(jQuery, window);