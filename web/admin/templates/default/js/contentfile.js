if (!$.ui) {
    $(document.createElement('script')).attr({
        type:"text/javascript",
        src:"js/vendor/jquery-ui.min.js"
     }).appendTo('head');
}
if (!$.fn.tree) {
    $(document.createElement('script')).attr({
        type:"text/javascript",
        src:"js/vendor/jstree/jstree.min.js"
     }).appendTo('head');
}
if (!$('link[href="css/vendor/jstree/default/style.min.css"]').length) {
    $(document.createElement('link')).attr({
        rel:"stylesheet",
        href:"css/vendor/jstree/default/style.min.css"
     }).appendTo('head');
}
if (!$.fn.fileupload) {
    $(document.createElement('script')).attr({
        type:"text/javascript",
        src:"js/vendor/fileUploader/jquery.iframe-transport.js"
     }).appendTo('head');
     
    $(document.createElement('script')).attr({
        type:"text/javascript",
        src:"js/vendor/fileUploader/jquery.fileupload.js"
     }).appendTo('head');
}
if (!$.fn.cookie) {
    $(document.createElement('script')).attr({
        type:"text/javascript",
        src:"https://cdnjs.cloudflare.com/ajax/libs/jquery-cookie/1.4.1/jquery.cookie.min.js"
     }).appendTo('head');
}
function getUrlVars() {
    var vars = {};
    var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m,key,value) {
        vars[key] = value;
    });
    return vars;
}

var token = getUrlVars()["token"];

function renderFiles(json) {
    html = '<ul>';
    if (json) {
        for (i = 0; i < json.length; i++) {
            html += '<li id="file_' + i + '">';
            name = '';
            filename = json[i]['filename'];
            for (j = 0; j < filename.length; j = j + 15) {
                name += filename.substr(j, 15) + '<br>';
            }
            name += json[i]['size'];
            html += '<a file="' + json[i]['file'] + '">';
            html += '<img src="' + json[i]['thumb'] + '" title="' + json[i]['filename'] + '" />';
            html += '<p>' + name + '</p>';
            html += '</a>';
            html += '<input type="checkbox" name="filess[]" value="' + json[i]['file'] + '" style="display:none" />';
            html += '<a class="selected"></a>';
            html += '<a class="copy" onclick="copy(\'file_' + i + '\',\'' + json[i]['file'] + '\')"></a>';
            html += '<a class="rename" onclick="rename(\'file_' + i + '\',\'' + json[i]['file'] + '\')"></a>';
            html += '<a class="move"></a>';
            html += '<a class="delete" onclick="ntDeleteFileFromMgr(\'file_' + i + '\',\'' + json[i]['file'] + '\')"></a>';
            html += '</li>';
        }
    }
    html += '</ul>';
                        
    $('#fileMgrFiles').html(html);
                        
    bindEventsRenderedFiles();               
}

function loadFiles(path) {
    $.getJSON('index.php?r=content/file/files&token='+ token,
    {
        directory:encodeURIComponent(path)
    })
    .then(function(json){
        renderFiles(json);
    });
}

function ntDeleteFileFromMgr(path) {
    $.post('index.php?r=content/file/delete&token='+ token,
    {
        path:path
    }).then(function(json) {
        if (json.success) {
            var tree = $.tree.focused();
            tree.select_branch(tree.selected);
        }
        if (json.error) {
            alert(json.error);
        }
    });
}
    
function bindEventsRenderedFiles() {
    $('#fileMgrFiles li').on('click', function (e) {
        if (e.shiftKey) {
            var firstLi = ($('#fileMgrFiles .liSelected:first-child').index()) ? $('#fileMgrFiles .liSelected:first-child').index() : $('#fileMgrFiles li:first-child').index();
            var lastLi = $(this).index();
            $('#fileMgrFiles li').each(function(){
                if ($(this).index() >= firstLi && $(this).index() <= lastLi) {
                    $(this).addClass('liSelected').find('.selected').show();
                    $(this).find('input').attr('checked', 'checked');
                } else {
                    $(this).removeClass('liSelected').find('.selected').hide();
                    $(this).find('input').removeAttr('checked');
                }
            });
        } else {
            $(this).toggleClass('liSelected');
            $(this).find('.selected').toggle();
            var inputCheck = $(this).find('input');
            if (inputCheck.attr('checked')) {
                inputCheck.removeAttr('checked');
            } else {
                inputCheck.attr('checked', 'checked');
            }
        }
    });
                        
    $('#fileMgrFiles li').on('dblclick', function () {
        var filename = $(this).find('a:eq(0)').attr('file');
        parent.$('#'+ window.field).attr('value', 'data/' + filename);
        parent.$('#' + window.field).trigger('change');
        parent.$('#dialog').dialog('close');
        parent.$('#dialog').remove();
    });
}

function deleteFile() {
    $.post('index.php?r=content/file/delete&token='+ token,
    {
        data:$("#form").serialize()
    })
    .done(function(resp){
        data = $.parseJSON(resp);
        if (data.error) {
            alert(data.error);
            return false;
        } else {
            return true;
        }
    });
}

function deleteDirectory(path, node) {
    $.get('index.php?r=content/file/delete&token='+ token,
    {
        path:path
    })
    .done(function(resp){
        data = $.parseJSON(resp);
        if (data.error) {
            alert(data.error);
            return false;
        } else {
            return true;
        }
    })
    .fail(function () {
        return false;
    });
}
function loadDirectories() {
    var parents = [], nodeId;
    $('#fileMgrTree').jstree({
        core: {
            data:{
                url:'index.php?r=content/file/directory&token='+ token,
                dataType: 'json',
                data:function(node){
                    return { directory: $(node).attr('directory') };
                }
            },
            check_callback:true,
            themes:{
		responsive:false
            }
	},
	plugins:['state','dnd','contextmenu','wholerow']
    })
    .on('loaded.jstree', function(e, data){
        data.instance.open_all();
    })
    .on('delete_node.jstree', function (e, data) {
        if ($("#form").serialize()) {
            if (confirm('Est\u00E1s seguro que deseas eliminar estos ficheros?')) {
                deleteFile(data);
            }
        } else {
            var path;
            if (typeof $('#'+ data.node.id).attr('directory') !== 'undefined') {
                path = $('#'+ data.node.id).attr('directory') +'/';
            } else if (typeof $.cookie('jstree_'+ data.node.id) != 'undefined') {
                path = $.cookie('jstree_'+ data.node.id) +'/';
            }
            
            if (path.length > 0 && !$.cookie('buttonDeleteClicked')) {
                if (confirm('Est\u00E1s seguro que deseas eliminar estos ficheros?')) {
                    deleteDirectory(path, data);
                }
            }
        }
    })
    .on('create_node.jstree', function (e, data) {
        data.node.text = 'nueva-carpeta';
        var directory = '';
        if (typeof $('#'+ data.node.parent).attr('directory') !== 'undefined') {
            directory = $('#'+ data.node.parent).attr('directory');
        } else if (typeof $.cookie('jstree_'+ data.node.parent) != 'undefined') {
            directory = $.cookie('jstree_'+ data.node.parent);
        }
        
        if (directory.length > 0 || data.node.parent == 'j1_1') {
            $.get('index.php?r=content/file/create&directory='+ directory +'&id='+ data.node.id +'&token='+ token,
            {
                position:data.position,
                name:data.node.text
            })
            .done(function (d) {
                d = $.parseJSON(d);
                data.instance.set_id(data.node, d.id);
                $('#'+ d.id).attr({
                    directory:d.directory
                });
                $.cookie('jstree_'+ d.id, d.directory);
            })
            .fail(function () {
                data.instance.refresh();
            });
        }
    })
    .on('rename_node.jstree', function (e, data) {
        var path;
        
        if (typeof $('#'+ data.node.parent).attr('directory') !== 'undefined') {
            path = $('#'+ data.node.parent).attr('directory') +'/';
        } else if (typeof $.cookie('jstree_'+ data.node.parent) != 'undefined') {
            path = $.cookie('jstree_'+ data.node.parent) +'/';
        }
        
        var directory = path;
        
        if (directory.length > 0 || data.node.parent !== 'j1_1') {
            path += data.old;

            $.get('index.php?r=content/file/rename&path='+ path +'&token='+ token,
            {
                id:data.node.id,
                name:data.text
            })
            .done(function(d){
                d = $.parseJSON(d);
                data.instance.set_id(data.node, d.id);
                directory += d.name;
                $('#'+ data.node.id).attr({
                    directory:directory
                });
                $.cookie('jstree_'+ d.id, directory);
            })
            .fail(function () {
                data.instance.refresh();
            });
        }
    })
    .on('move_node.jstree', function (e, data) {
        $.get('index.php?r=content/file/move&path='+ path +'&token='+ token,
        {
            id:data.node.id,
            parent:data.parent,
            position:data.position
        })
        .fail(function () {
            data.instance.refresh();
	});
    })
    .on('copy_node.jstree', function (e, data) {
        $.get('index.php?r=content/file/copy&path='+ path +'&token='+ token,{
            id:data.original.id,
            parent:data.parent,
            position:data.position
        })
	.always(function () {
            data.instance.refresh();
	});
    })
    .on('changed.jstree', function (e, node) {
        if(node && node.selected && node.selected.length) {
            directory = $('#'+ node.selected).attr('directory');
            $("#directoryForUpload").val(directory);
            loadFiles(directory);
	} else {
            $('#data .content').hide();
            $('#data .default').html('Select a file from the tree.').show();
	}
    });
}

$(function(){
    $("#tabbrowser").show();
    $("#tabs a").on('click', function(){
        $(".tabs").hide();
        $("#tab" + this.id).show();
    });
    
    var windowHeight = $(window).height();
    
    $("#dropHere").css({
        height:(windowHeight * 60 / 100) + 'px'
    });
    
    if (window.FileReader && Modernizr.draganddrop) {
        $('#fileupload').hide().fileupload({
            dataType: 'json',
            url: 'index.php?r=content/file/uploader&token='+ token,
            add: function (e, data) {
                $('#fileupload').fileupload({
                    formData:{directory:encodeURIComponent($("#directoryForUpload").val())}
                });

                $("#dropHere").fadeOut();
                $.each(data.files, function (index, file) {
                    var html = '';
                    if ((file.size / 1024) > 1000) {
                        var size = Math.round(file.size / 1024 / 1024) + ' MB';
                    } else {
                        var size = Math.round(file.size / 1024) + ' KB';
                    }

                    if (file.size > 5000000) {
                        var clase = 'good';
                    } else {
                        var clase = 'error';
                    }

                    var ext = (file.name.substring(file.name.lastIndexOf("."))).toLowerCase();
                    var allowed = [
                        ".gif",
                        ".jpg",
                        ".png",
                        ".doc",
                        ".docx",
                        ".xls",
                        ".xlsx",
                        ".txt",
                        ".csv",
                        ".swf",
                        ".flv",
                        ".mp3",
                        ".pdf"
                    ];
                    var goon = false;
                    for (var i = 0; i < allowed.length; i++) {
                        if (allowed[i] == ext) {
                            goon = true;
                            break;
                        }
                    }

                    if (goon) {
                        goon = false;
                        allowed = [
                            'image/jpg',
                            'image/jpeg',
                            'image/pjpeg',
                            'image/png',
                            'image/x-png',
                            'image/gif',
                            "text/csv",
                            "text/comma-separated-values",
                            "text/tab-separated-values",
                            "text/plain",
                            'application/x-shockwave-flash',
                            'application/msword',
                            'application/pdf',
                            'application/x-pdf',
                            'application/msexcel',
                            'audio/x-mpeg'
                        ];
                        for (var i = 0; i < allowed.length; i++) {
                            if (file.type.toLowerCase() == 'image/jpg' || file.type.toLowerCase() == 'image/jpeg' || file.type.toLowerCase() == 'image/pjpeg' || file.type.toLowerCase() == 'image/png' || file.type.toLowerCase() == 'image/x-png' || file.type.toLowerCase() == 'image/gif' && file.size > 3000) {
                                fileSuggest = "El tamano en KB del archivo es muy grande para utilizarlo en la web. Esto puede causar que el sitio web tarde cargando los contenidos.";
                            }

                            if (allowed[i] == file.type.toLowerCase()) {
                                goon = true;
                                break;
                            }
                        }
                    }

                    if (goon) {
                        var clase = 'good';
                    } else {
                        var clase = 'error';
                    }

                    /* 
                     html += '<div class="grid_2">';
                     html += '<img src="'+ file.mozFullPath +'" alt="'+ file.name +'" />'; 
                     html += '</div>';
                     */

                    html += '<div class="grid_8">';
                    html += file.name;
                    html += '</div>';
                    html += '<div class="grid_2">';
                    html += '<p class="' + clase + '">' + size + '</p>';
                    html += '</div>';
                    html += '<div class="grid_2">';
                    html += '<p class="' + clase + '">' + file.type + '</p>';
                    html += '</div>';
                    html += '<div class="progress-bar blue stripes grid_6">';
                    html += '<span style="width:0%"></span>';
                    html += '</div>';
                    html += '<div class="grid_2"><a onclick="">[ Eliminar ]</a></div>';
                    var li = $(document.createElement('li'))
                        .css({display:'none'})
                        .html(html)
                        .appendTo(('#filesUploaded'));

                    data.context = li;
                    li.fadeIn();
                });

                $(".uploadStart").on('click', function(e){
                    data.submit();
                });
            },
            progress: function (e, data) {
                var progress = parseInt(data.loaded / data.total * 100, 10);
                data.context.find('.progress-bar span').css({
                    'width':progress + '%',
                    display:'block'
                });
            },
            dragover: function(e) {
                $("#dropHere").addClass('dropHere');
            },
            done: function (e, data) {
                var that = $(this).data('fileupload');
                if (data.context) {
                    data.context.each(function(index) {
                        $('html,body').animate(
                            {
                                scrollTo:$("#scrollDown").offset().top
                            }, 
                            500
                        );
                        var preview = $(this).find('div:nth-child(1)');
                        var msgWrapper = $(this).find('div:nth-child(4)');
                        $(this).find('.progress-bar span').css({
                            'width':'100%',
                        }).parent().fadeOut();
                        file = (typeof (data.result) == 'object') ? data.result : {error:'Error: no se pudo obtener el resultado del archivo'};
                        if (file.error) {
                            msgWrapper.removeClass('progress-bar').html('<b>Error: ' + file.error + '</b>').fadeIn();
                        } else {
                            msgWrapper.removeClass('progress-bar').html('<b>' + file.success + '</b>').fadeIn();
                            preview.fadeOut(function(e){
                                $(this).html('<img src="' + file.thumbnail_url + '" alt="' + file.name + '" />&nbsp;' + file.name + '').fadeIn();
                            });
                        }
                    });
                } else {
                    if ($.isArray(data.result)) {
                        $.each(data.result, function (index, file) {
                            if (data.maxNumberOfFilesAdjusted && file.error) {
                                that._adjustMaxNumberOfFiles(1);
                            } else if (!data.maxNumberOfFilesAdjusted && !file.error) {
                                that._adjustMaxNumberOfFiles( - 1);
                            }
                        });
                        data.maxNumberOfFilesAdjusted = true;
                    }

                    that._transition(template).done(function(){
                        data.context = $(this);
                        that._trigger('completed', e, data);
                    });
                }
            }
        });
    } else {
        $("#dropHere").hide();
    }

    $('#delete').on('click', function (e) {
        if (confirm('Est\u00E1s seguro que deseas eliminar estos ficheros?')) {
            if ($("#form").serialize()) {
                if (deleteFile()) {
                    $('#fileMgrTree').jstree('refresh');
                }
            } else {
                $.cookie('buttonDeleteClicked', 1, {path:'/'});
                n = $.jstree.reference('#fileMgrTree');
                var nodes = n.get_selected();
                
                var path = '';
                $.each(nodes, function(i, node) {
                    path += $('#'+ node).attr('directory') +':';
                });
                
                if (path) {
                    $.get('index.php?r=content/file/delete&token='+ token +'&path='+ path,
                    function(json) {
                        if (json.error) {
                            alert(json.error);
                        } else {
                            n.delete_node(nodes);
                            $.removeCookie('buttonDeleteClicked', {path:'/'});
                        }
                    });
                } else {
                    alert(window.errorSelect);
                }
            }
        }
    });

    $('#copy').bind('click', function () {
        $('#dialog').remove();
        html = '<div id="dialog">';
        html += '<input type="text" name="name" value="" placeholder="Nombre del Archivo"> <input type="button" value="Submit">';
        html += '</div>';
        $('#fileMgrFiles').prepend(html);
        $('#dialog').dialog({
            title: 'Copiar Carpeta o Archivo',
            resizable: false
        });

        $('#dialog select[name=\'to\']').load('index.php?r=content/file/folders&token='+ token);
        $('#dialog input[type=\'button\']').bind('click', function () {
            path = $('#fileMgrFiles a.selected').attr('file');
            if (path) {
                $.ajax({
                    url: 'index.php?r=content/file/copy&token='+ token,
                    type: 'POST',
                    data: 'path=' + encodeURIComponent(path) + '&name=' + encodeURIComponent($('#dialog input[name=\'name\']').val()),
                    dataType: 'json',
                    success: function(json) {
                        if (json.success) {
                            $('#dialog').remove();
                            var tree = $.tree.focused();
                            tree.select_branch(tree.selected);
                            alert(json.success);
                        }

                        if (json.error) {
                            alert(json.error);
                        }
                    }
                });
            }
        });
    });

    $('#rename').bind('click', function () {
        $('#dialog').remove();
        html = '<div id="dialog">';
        html += '<input type="text" name="name" value="" placeholder="Nuevo Nombre"> <input type="button" value="Submit">';
        html += '</div>';
        $('#fileMgrFiles').prepend(html);
        $('#dialog').dialog({
            title: 'Cambiar Nombre',
            resizable: false
        });
        $('#dialog input[type=\'button\']').bind('click', function () {
            path = $('#fileMgrFiles a.selected').attr('file');
            if (path) {
                $.ajax({
                    url: 'index.php?r=content/file/rename&token='+ token,
                    type: 'POST',
                    data: 'path=' + encodeURIComponent(path) + '&name=' + encodeURIComponent($('#dialog input[name=\'name\']').val()),
                    dataType: 'json',
                    success: function(json) {
                        if (json.success) {
                            $('#dialog').remove();
                            var tree = $.tree.focused();
                            tree.select_branch(tree.selected);
                            alert(json.success);
                        }

                        if (json.error) {
                            alert(json.error);
                        }
                    }
                });
            }
        });
    });

    $('#refresh').on('click', function () {
        var tree = $.tree.focused();
        tree.refresh(tree.selected);
    });
});