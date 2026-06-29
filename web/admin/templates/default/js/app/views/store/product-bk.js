(function($, window){
    var App = window.App;

    App.Data.Product = {};

    App.Models.Product = function(){
        /** private functions and vars **/
        var data = {},
            modelId = 0;

        function compare() {
            if (attributes !== this.attributes) {
                push('update', this.attributes);
            }
        }

        function fetch(action, data, cb) {
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
        }

        function push(type, action, data, cb) {
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
        }

        function SaveDeferred(type, action, dbIndex) {
            console.log('db request deferred');
            App.Utils.Tasks.AddQueue('DB:push:Product:'+ modelId, {
                type:'connect',
                requestUrl:App.Route.CreateAdminUrl('store/product/api', {resp:'json', action:action, id:modelId}),
                requestType:type,
                requestDataType:'json',
                beforeSend:this.beforeSave,
                onSuccess:this.afterSave,
                onError:this.errorHandler
            }, dbIndex);
        }

        function errorHandler(jqXHR, textStatus, errorThrown) {

        }

        function get(attribute) {
            return data[attribute] || null;
        }

        function set(attribute, value) {
            if (attribute === 'id') return;
            data[attribute] = value;
        }

        function save() {
            var type, response;

            $('body').on('Product:OnSave',App.Events.Product.OnSave);
            console.log('92');
            console.log(modelId);
            beforeSave();

            if (modelId === 0) {
                type = 'POST';
            } else {
                type = 'PUT';
            }
            push(type, '_products', data).done(function(data) {
                if (data.error) {

                } else {
                    console.log(data);
                    $('body').trigger('Product:OnSave');
                    afterSave(data);
                }
            }).fail(function(data) {
                SaveDeferred(type, action, dbIndex);
            });
            console.log('102');

            $('body').off('Product:OnSave', App.Events.Product.OnSave);
        }

        function beforeSave() {

        }

        function afterSave(data) {
            if (typeof data == 'undefined') {
                data = null;
            }
            if (typeof data.data.id !== 'undefined') {
                modelId = data.data.id;
            }
        }

        function getById(id, data) {
            if (typeof id == 'undefined') { return null; }
            if (typeof data == 'undefined') { null; }
            data.id = id;
            fetch('_products', data, null).done(function(resp){
                console.log(resp);
            }).fail();
        }

        function getAll(data) {
            if (typeof data == 'undefined') { null; }
            fetch('_products', data, null).done(function(resp){
                console.log(resp);
            }).fail();
        }

        /** public functions and vars **/

        this.get = function(attribute) {
            get(attribute);
        };

        this.set = function(attribute, value) {
            set(attribute, value);
        };

        this.getId = function() {
            return modelId;
        };

        this.setId = function(id) {
            modelId = id;
        };

        this.save = function() {
            return save();
        };

        this.getById = function(id, data) {
            return getById(id, data);
        };

        this.getAll = function(data) {
            return getAll();
        };
    };

    App.Views.ProductForm = {
        /**
         * @return {boolean}
         */
        Create: function(el) {
            if ($('#' + el).length) {
                App.Data.Product.FormContainer = $('#' + el);
            } else {
                console.log('no se pudo crear el formulario de producto, no se definio el contenedor');
                return false;
            }

            App.Data.Product.FormContainer
                .append(App.Views.ProductForm.Render())
                .on('submit', App.Events.Product.OnSubmit);

            App.Utils.Image.Render({
                preview: 'ProductForm_preview',
                field: 'ProductForm_image'
            });

            $('#ProductForm_form').ntForm({
                lockButton:false
            });

            console.log(this);
        },
        Render: function() {
            console.log(this);
            var el = $(document.createElement('form'))
                .attr({
                    id: 'ProductForm_form',
                    class:'neco-form'
                })
                .append(
                '<div class="grid_3">'+
                '<a class="filemanager" data-fancybox-type="iframe" href="'+ App.Route.CreateAdminUrl("common/filemanager") +'&amp;field=image&amp;preview=preview">'+
                '<img id' +
                '="ProductForm_preview" class="image necoImage" width="100" />'+
                '</a>'+
                '<input type="hidden" name="image" value="" id="ProductForm_image" onchange="$(\'#preview\').attr(\'src\', this.value);" />'+
                '</div>'+

                '<div class="grid_7">'+
                '<input class="neco-input-text" type="text" name="name" placeholder="Product Name" />'+
                '<input class="neco-input-text" type="text" name="model" placeholder="Product Model" />'+
                '<input class="neco-input-text" type="price" name="price" placeholder="Product Price" />'+
                '<input class="neco-input-text" type="number" name="quantity" placeholder="Product Quantity" />'+
                '</div>'
            );
            return el;

        }
    };

    App.Events.Product = {
        OnSubmit: function(e) {
            /**
             - validar que el modelo es único
             - validar tamaño y tipo de archivo
             - subir archivo
             - enviar post data
             */
        },
        OnSave: function(e, o, data) {
            /**
             - reset form
             - show success message
             - add product to list
             - reset cache product files
             - publicar en redes sociales
             */
        }
    };

    window.App = App;
})(jQuery, window);

App.Views.ProductForm.Create('ProductForm');


var Product1 = new App.Models.Product;
var Product2 = new App.Models.Product;

Product1.setId(1);
Product2.set('name', 'IPad');
console.log(Product2.getId());
$('body').on('click', function() {
    Product2.save();
});
$('body').on('Product:OnSave',function(e){
    console.log(Product2.getById(Product2.getId()));
    console.log(Product2.getAll());
});

function set(attribute, value) {
    console.log('duplicated');
}