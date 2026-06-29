$(function(){
    /*
    addAttribute
    checkAttribute
    removea\Attribute
     */
    loadFormWidgets();
});

function loadFormWidgets() {
    $('#widgetsFormWrapper').html('<img src="'+ window.imageFolderUrl +'loader.gif" alt="Cargando" />');
    $('#widgetsFormWrapper').load(window.widgetsLoadUrl);
}

function addImage(files, path) {
    _row = ($('#images tr:last-child').index() + 1);
    for(let i=0; files.length>i; i++) {
        __addImage();
        $('#preview'+_row).attr( 'src', path + files[i] );
        $('#image'+_row).val( 'data/'+ files[i] );
        _row++;
    }
}

function __addAttribute(category_id, product_id) {
    url = createAdminUrl("store/category/attributes") + '&category_id=' + category_id +"&product_id="+ product_id;
    $.getJSON(url)
        .done(function(resp){
            if (resp.success) {
                $.each(resp.results,function(i,d){
                    var data = d;
                    if ($('#product_attribute_group_id_'+ data.product_attribute_group_id).length === 0) {
                        var divGroup = $(document.createElement('div')).attr({
                            id:'product_attribute_group_id_'+ data.product_attribute_group_id,
                            class:'product_attribute_groups',
                            'data-categories':data.categoriesAttributes.join()
                        }),
                        attribute_group_id = data.product_attribute_group_id;
                        categories = data.categoriesAttributes.join();

                        $(divGroup).append('<input type="hidden" name="categoriesAttributes" value="'+ categories +'" class="categoriesAttributes" />');
                        $(document.createElement('h3')).html(data.title??data.name).appendTo(divGroup);

                        $.each(data.attributes, function(i,item){
                            div = $(document.createElement('div')).addClass('row');
                            input = $(document.createElement('input'));

                            if (item.name) {
                                input.attr('name', 'attributes['+ attribute_group_id +']['+ item.name +':'+ item.product_attribute_id +']');
                                input.attr('placeholder', item.name);
                                var inputId = 'Attributes_'+ item.name;
                            } else if (item.label) {
                                input.attr('name', 'attributes['+ attribute_group_id +']['+ item.label +':'+ item.product_attribute_id +']');
                                input.attr('placeholder', item.label);
                                var inputId = 'Attributes_'+ item.label;
                            } else {
                                input.attr('name', 'attributes['+ attribute_group_id +'][attribute_'+ ($('.attributes:last-child').index() + 1) +']');
                                var inputId = 'Attributes_'+ ($('.attributes:last-child').index() + 1);
                            }

                            input.attr('id', inputId);

                            if (item.type) {
                                input.attr('type', item.type);
                            } else {
                                input.attr('type', 'text');
                            }

                            if (item.value) {
                                input.attr('value', item.value);
                            }
                            
                            if (item.default && !input.attr("value")) {
                                input.attr('value', item.default);
                            }

                            if (typeof data.admin_attributes != "undefined" && !!data.admin_attributes[item.product_attribute_id]) {
                                input.attr('value', data.admin_attributes[item.product_attribute_id]["value"]);
                            }

                            if (item.required) {
                                input.attr('required', 'required');
                            }

                            if (item.class) {
                                input.attr('class', item.class);
                            }

                            $(div).append('<label for=\"'+ inputId +'\" class="neco-label">'+ item.label +'</label>');
                            $(div).append(input);

                            $(divGroup).append(div);
                            input.ntInput();
                        });

                        $(divGroup).prepend('<div class=\"clear\"></div>');
                        $(divGroup).append('<div class=\"clear\"></div>');
                        $('#formAttributes').append(divGroup);
                    }
                });
            }
        });
}

function addAttribute(el) {
    var el = el;
    if (typeof el == 'undefined') {
        return;
    }

    var category_id = $(el).val();

    if ($(el).prop('checked')) {
        __addAttribute(category_id);
    } else {
        /*
         - check if there is another category that has the same attributes
         - remove attributes of this category
         */
    }
}

function removeAttribute(el) {
    var canDelete = true,
        category_id = $(el).val(),
        categories = {},
        arr = [],
        categoriesSelected = [],
        groupsToDelete = {};

    $('.product_attribute_groups .categoriesAttributes').each(function(){
        arr = $(this).val().split(',');
        if ($.inArray( category_id, arr ) >= 0) {
            id = $(this).parent('.product_attribute_groups').attr('id');
            categories[id] = $.unique( arr );
        }
    });

    $('.categories input').each(function(){
        if ($(this).prop('checked')) {
            categoriesSelected.push( $(this).val() );
        }
    });

    if (categoriesSelected.length == 0) {
        $('.product_attribute_groups').remove();
    } else {
        $.each(categoriesSelected, function (key, value) {
            $.each(categories, function (attr_group_id, categoryIDs) {
                var index = $.inArray(value, categoryIDs);
                if (index == -1) {
                    groupsToDelete['#' + attr_group_id] = true;
                } else {
                    groupsToDelete['#' + attr_group_id] = false;
                }
            });
        });

        $.each(groupsToDelete, function (i, item) {
            if (item) $(i).remove();
        });
    }
}