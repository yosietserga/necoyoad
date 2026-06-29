$(function(){    
    $('#qTasks').on('change',function(e){
        var that = this;
        var valor = $(that).val().toLowerCase();
        if (valor.length <= 0) {
            $('#taskssPanel li').show();
        } else {
            $('#taskssPanel li b').each(function(){
                if ($(this).text().toLowerCase().indexOf( valor ) > 0) {
                    $(this).closest('li').show();
                } else {
                    $(this).closest('li').hide();
                }
            });
        }
    });
           
    var data = {};
    
    $(".tasksWrapper").sortable({
        placeholder: "tasksPlaceHolder",
        connectWith: '.tasksWrapper',
        cursor: 'move',
        start: function(event,ui){
            if ($(this).data().uiSortable) {
                data.item = $($(this).data().uiSortable.currentItem);
            } else if ($(this).data()['ui-sortable']) {
                data.item = $($(this).data()['ui-sortable'].currentItem);
            } else if ($(this).data().sortable) {
                data.item = $($(this).data().sortable.currentItem);
            } else {
                console.log('No se definió jquery ui sortable');
            }
        },
        receive:function(event,ui) {
                data.position = $(this).data('position');
                data.wrapper = $(this);
                data.sort_order = ($(data.item).index() + 1);         
                
                data.inputs = 
                '<input class="tasksName" type="hidden" name="Tasks['+ data.id +'][name]" id="'+ data.id +'_name" value="'+ data.id +'" />'+
                '<input class="tasksPosition" type="hidden" name="Tasks['+ data.id +'][position]" id="'+ data.id +'_position" value="'+ data.position +'" />'+
                '<input class="tasksSortOrder" type="hidden" name="Tasks['+ data.id +'][order]" id="'+ data.id +'_order" value="'+ data.sort_order +'" />';
                
                var tasksId = data.id;
                
                $.ajaxQueue({
                    url: createAdminUrl('module/crm/dashboard/save', 'store_id='+ getUrlVars()['store_id']),
                    dataType: "json",
                    data:{
                        'extension':data.extension,
                        'order':data.sort_order,
                        'position':data.position,
                        'name':data.id
                    }
                }).done(function( response ) {
                    if (typeof response.html != 'undefined') {
                        $('#'+ tasksId +' div.attributes').append(response.html);
                        $('#'+ tasksId +'_form').append(data.inputs);
                        
                        $('#'+ tasksId +' a.advanced').on('click', function(e) {
                            div = $(this).closest('li').find('div.attributes:eq(0)');
                            if (div.hasClass('on')) {
                                div.removeClass('on').slideUp();
                            } else {
                                div.addClass('on').slideDown();
                            }
                        });
                        
                        var tasksModule = data.extension;
                        var tasksName = tasksId;
                        var tasksLi = $('#'+ tasksId);
                        
                        $('#'+ tasksId).find('input, select, textarea').on('change',function(event){
                            $('.tasksTitle').after('<img src="'+ window.nt.http_admin_image +'small_loading.gif" class="saving" />');
                            $.post(createAdminUrl('module/'+ tasksModule +'/tasks', {
                                store_id:getUrlVars()['store_id'],
                                name:tasksName,
                                order:data.sort_order,
                                position:data.position
                            }), 
                            $('#'+ tasksId +'_form').serialize(),
                            function(respons){
                                $('.saving').remove();
                                resp = $.parseJSON(respons);
                                if (typeof resp.error != 'undefined') {
                                    
                                }
                                if (typeof resp.success != 'undefined') {
                                    
                                }
                            });
                        });
                    }
                    $(".tasksWrapper").find("input, select, textarea")
                    .bind('mousedown.ui-disableSelection selectstart.ui-disableSelection', function(e) {
                        e.stopImmediatePropagation();
                    });
                });
                data.name = null;
        },
        stop: function () {
            $(this).find("input, select, textarea")
            .bind('mousedown.ui-disableSelection selectstart.ui-disableSelection', function(e) {
                e.stopImmediatePropagation();
            });
        },
        update: function(event,ui){
                setOrder();
            
        }
    })/* .disableSelection() */;
    
});

function setOrder() {
    data = {};
    $('.tasksSet').each(function(){
        $(this).find('.tasksPosition').val( $(this).closest('.tasksWrapper').data('position') );
        $(this).find('.tasksSortOrder').val( ($(this).index() + 1) );
        data[$(this).attr('id')] = {
            'name':$(this).attr('id'),
            'position':$(this).closest('.tasksWrapper').data('position'),
            'order':($(this).index() + 1)
        };
    });
    $.post(createAdminUrl('style/tasks/sortable'),data);
}

function deleteTask(e) {
    if (confirm("\xbfEst\u00E1 seguro que desea eliminar este tasks?")) {
        var li = $(e).closest("li");
        var tasksName = $(li).attr('id');
        li.fadeOut(function(){
            li.remove();
        });
        $.getJSON(createAdminUrl('style/tasks/delete', 'name='+ tasksName));
    }
}

function rand (min, max) {
    if (!min && !max) {
        min = 0;
        max = 2147483647;
    }
    return Math.floor(Math.random() * (max - min + 1)) + min;
}

function loadNtTasks(tasks) {
    if (typeof tasks.extension == 'undefined' ||
        typeof tasks.position == 'undefined' ||
        typeof tasks.name == 'undefined' ||
        typeof tasks.order == 'undefined') {
        return false;
    }
    
    $.ajaxQueue({
        url: createAdminUrl("module/"+ tasks.extension +"/tasks", "w=1"),
        dataType: "json",
        data:tasks
    }).done(function( data ) {
        $('#'+ tasks.name +' .attributes').html(data.html);
        $('#'+ tasks.name +'_form').append('<input type="hidden" name="Tasks['+ tasks.name +'][position]" value="'+ tasks.position +'" /><input type="hidden" name="Tasks['+ tasks.name +'][order]" value="'+ tasks.order +'" /><input type="hidden" name="Tasks['+ tasks.name +'][name]" value="'+ tasks.name +'" />');
        $('.tasksWrapper').find("input, select, textarea, p")
        .bind('mousedown.ui-disableSelection selectstart.ui-disableSelection', function(e) {
            e.stopImmediatePropagation();
        });
        $('#'+ tasks.name +'').find('input, select, textarea').on('change',function(event){
            $('.saving').remove();
            $('#'+ tasks.name +' .tasksTitle').after('<img src="'+ window.nt.http_admin_image +'small_loading.gif" class="saving" />');
            $.post(createAdminUrl("module/"+ tasks.extension +"/tasks", {
                name:tasks.name,
                order:tasks.order,
                position:tasks.position
            }), 
            $('#'+ tasks.name +'_form').serialize(),
            function(respons){
                $('.saving').remove();
                resp = $.parseJSON(respons);
            });
        });
    });
}