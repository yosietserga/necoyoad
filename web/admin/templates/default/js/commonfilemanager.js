/*! jquery.cookie v1.4.1 | MIT */
!function(a){"function"==typeof define&&define.amd?define(["jquery"],a):"object"==typeof exports?a(require("jquery")):a(jQuery)}(function(a){function b(a){return h.raw?a:encodeURIComponent(a)}function c(a){return h.raw?a:decodeURIComponent(a)}function d(a){return b(h.json?JSON.stringify(a):String(a))}function e(a){0===a.indexOf('"')&&(a=a.slice(1,-1).replace(/\\"/g,'"').replace(/\\\\/g,"\\"));try{return a=decodeURIComponent(a.replace(g," ")),h.json?JSON.parse(a):a}catch(b){}}function f(b,c){var d=h.raw?b:e(b);return a.isFunction(c)?c(d):d}var g=/\+/g,h=a.cookie=function(e,g,i){if(void 0!==g&&!a.isFunction(g)){if(i=a.extend({},h.defaults,i),"number"==typeof i.expires){var j=i.expires,k=i.expires=new Date;k.setTime(+k+864e5*j)}return document.cookie=[b(e),"=",d(g),i.expires?"; expires="+i.expires.toUTCString():"",i.path?"; path="+i.path:"",i.domain?"; domain="+i.domain:"",i.secure?"; secure":""].join("")}for(var l=e?void 0:{},m=document.cookie?document.cookie.split("; "):[],n=0,o=m.length;o>n;n++){var p=m[n].split("="),q=c(p.shift()),r=p.join("=");if(e&&e===q){l=f(r,g);break}e||void 0===(r=f(r))||(l[q]=r)}return l};h.defaults={},a.removeCookie=function(b,c){return void 0===a.cookie(b)?!1:(a.cookie(b,"",a.extend({},c,{expires:-1})),!a.cookie(b))}});

if (!$.fn.cookie) {
    $(document.createElement('script')).attr({
        type: "text/javascript",
        src: "https://cdnjs.cloudflare.com/ajax/libs/jquery-cookie/1.4.1/jquery.cookie.min.js"
    }).appendTo('body');
}

function getUrlVars() {
    var vars = {};
    var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function (m, key, value) {
        vars[key] = value;
    });
    return vars;
}

var token = getUrlVars()["token"];
var theme_editor = getUrlVars()["theme_editor"];

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
            html += '<a file="' + json[i]['file'] + '">'
            + '<p><img src="' + json[i]['thumb'] + '" title="' + json[i]['filename'] + '" /></p>'
            + '<p>' + name + '</p>'
            + json[i]['size']
            + '</a>'
            + '<input type="checkbox" name="filess[]" value="' + json[i]['file'] + '" style="display:none" />'
            + '<a class="selected"></a>'
            + '<a class="copy" onclick="copy(\'file_' + i + '\',\'' + json[i]['file'] + '\')"></a>'
            + '<a class="rename" onclick="rename(\'file_' + i + '\',\'' + json[i]['file'] + '\')"></a>'
            + '<a class="move"></a>'
            + '<a class="delete" onclick="ntDeleteFileFromMgr(\'file_' + i + '\',\'' + json[i]['file'] + '\')"></a>'
            + '</li>';
        }
    }
    html += '</ul>';
    $('#column_right').html(html);

    bindEventsRenderedFiles();
}

function loadFiles(path) {
    $.getJSON('index.php?r=content/file/files&token=' + token,
            {
                directory: encodeURIComponent(path)
            })
            .then(function (json) {
                renderFiles(json);
            });
}

function ntDeleteFileFromMgr(id,path) {
    if (confirm('Est\u00E1s seguro que deseas eliminar estos ficheros?')) {
        $.post('index.php?r=content/file/delete&token=' + token, 
        {
            filess:[path]
        })
        .done(function (resp) {
            data = $.parseJSON(resp);
            if (data.error) {
                alert(data.error);
                return false;
            } else {
                $('#'+ id).remove();
                /* $('#column_left').jstree('refresh'); */
                return true;
            }
        });
    }
}

function bindEventsRenderedFiles() {
    $('#column_right li').on('click', function (e) {
        if (e.shiftKey) {
            var firstLi = ($('#column_right .liSelected:first-child').index()) ? $('#column_right .liSelected:first-child').index() : $('#column_right li:first-child').index();
            var lastLi = $(this).index();
            $('#column_right li').each(function () {
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

    $('#column_right li').on('dblclick', function () {
        var filename = $(this).find('a:eq(0)').attr('file');
        if (window.isFckeditor) {
            var filePath = window.baseImageUrl +'data/';
            /*
            if ($.cookie('jstree_directory').length > 0) {
                filePath += $.cookie('jstree_directory') +'/';
            }
             */
            filePath += filename;
            window.opener.CKEDITOR.tools.callFunction(1, filePath);
            self.close();
        /*} else if (theme_editor) {
            parent.setImage('data/' + filename);
            parent.$.fancybox.close();*/
        } else {
            parent.$('#' + window.preview).attr('src', window.baseImageUrl + 'data/' + filename);
            parent.$('#' + window.field).attr('value', 'data/' + filename);
            parent.$('#' + window.field).trigger('change');
            parent.$.fancybox.close();
        }
    });
}

function deleteFile() {
    $.post('index.php?r=content/file/delete&token=' + token, $("#form").serialize())
    .done(function (resp) {
        data = $.parseJSON(resp);
        if (data.error) {
            alert(data.error);
            return false;
        } else {
            $('#column_right li.liSelected').remove();
            /* $('#column_left').jstree('refresh'); */
            return true;
        }
    });
}

function deleteDirectory(path, node) {
    $.get('index.php?r=content/file/delete&token=' + token,
            {
                path: path
            })
            .done(function (resp) {
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
    $('#column_left').jstree({
        core: {
            data: {
                url: 'index.php?r=content/file/directory&token=' + token,
                dataType: 'json',
                data: function (node) {
                    return {directory: $(node).attr('directory')};
                }
            },
            check_callback: true,
            themes: {
                responsive: false
            }
        },
        plugins: ['state', 'dnd', 'contextmenu', 'wholerow']
    })
    .on('loaded.jstree', function (e, data) {
        data.instance.open_all();
    })
    .on('delete_node.jstree', function (e, data) {
        if ($("#form").serialize()) {
            if (confirm('Est\u00E1s seguro que deseas eliminar estos ficheros?')) {
                deleteFile(data);
            }
        } else {
            var path;
            if (typeof $('#' + data.node.id).attr('directory') !== 'undefined') {
                path = $('#' + data.node.id).attr('directory') + '/';
            } else if (typeof $.cookie('jstree_' + data.node.id) != 'undefined') {
                path = $.cookie('jstree_' + data.node.id) + '/';
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
        if (typeof $('#' + data.node.parent).attr('directory') !== 'undefined') {
            directory = $('#' + data.node.parent).attr('directory');
        } else if (typeof $.cookie('jstree_' + data.node.parent) != 'undefined') {
            directory = $.cookie('jstree_' + data.node.parent);
        }

        if (directory.length > 0 || data.node.parent == 'j1_1') {
            $.get('index.php?r=content/file/create&directory=' + directory + '&id=' + data.node.id + '&token=' + token,
                    {
                        position: data.position,
                        name: data.node.text
                    })
                    .done(function (d) {
                        d = $.parseJSON(d);
                        data.instance.set_id(data.node, d.id);
                        $('#' + d.id).attr({
                            directory: d.directory
                        });
                        $.cookie('jstree_' + d.id, d.directory);
                        $.cookie('jstree_directory', d.directory);
                    })
                    .fail(function () {
                        data.instance.refresh();
                    });
        }
    })
    .on('rename_node.jstree', function (e, data) {
        var path;

        if (typeof $('#' + data.node.parent).attr('directory') !== 'undefined') {
            path = $('#' + data.node.parent).attr('directory') + '/';
        } else if (typeof $.cookie('jstree_' + data.node.parent) != 'undefined') {
            path = $.cookie('jstree_' + data.node.parent) + '/';
        }

        var directory = path;

        if (directory.length > 0 || data.node.parent !== 'j1_1') {
            path += data.old;

            $.get('index.php?r=content/file/rename&path=' + path + '&token=' + token,
                    {
                        id: data.node.id,
                        name: data.text
                    })
                    .done(function (d) {
                        d = $.parseJSON(d);
                        data.instance.set_id(data.node, d.id);
                        directory += d.name;
                        $('#' + data.node.id).attr({
                            directory: directory
                        });
                        $.cookie('jstree_' + d.id, directory);
                    })
                    .fail(function () {
                        data.instance.refresh();
                    });
        }
    })
    .on('move_node.jstree', function (e, data) {
        $.get('index.php?r=content/file/move&path=' + path + '&token=' + token,
                {
                    id: data.node.id,
                    parent: data.parent,
                    position: data.position
                })
                .fail(function () {
                    data.instance.refresh();
                });
    })
    .on('copy_node.jstree', function (e, data) {
        $.get('index.php?r=content/file/copy&path=' + path + '&token=' + token, {
            id: data.original.id,
            parent: data.parent,
            position: data.position
        })
                .always(function () {
                    data.instance.refresh();
                });
    })
    .on('changed.jstree', function (e, node) {
        if (node && node.selected && node.selected.length) {
            directory = $('#' + node.selected).attr('directory');
            $("#directoryForUpload").val(directory);

            loadFiles(directory);
        } else {
            $('#data .content').hide();
            $('#data .default').html('Select a file from the tree.').show();
        }
    });
}

$(function () {
    $("#tabbrowser").show();
    $("#tabs a").on('click', function () {
        $(".tabs").hide();
        $("#tab" + this.id).show();
    });

    var windowHeight = $(window).height();

    $("#dropHere").css({
        height: (windowHeight * 60 / 100) + 'px'
    });

    if (window.FileReader && Modernizr.draganddrop) {
        $('#fileupload').hide().fileupload({
            dataType: 'json',
            url: 'index.php?r=content/file/uploader&token=' + token,
            add: function (e, data) {
                $('#fileupload').fileupload({
                    formData: {directory: encodeURIComponent($("#directoryForUpload").val())}
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

                    html += '<div class="grid_3">';
                    html += '<img id="fileuploaded_'+ index +'" alt="'+ file.name +'" width="200" />';
                    html += '</div>';
                    html += '<div class="progress-bar blue stripes grid_3">';
                    html += '<span style="width:0%"></span>';
                    html += '</div>';
                    var li = $(document.createElement('li'))
                            .css({display: 'none'})
                            .addClass('grid_3')
                            .html(html)
                            .appendTo(('#filesUploaded'));

                    data.context = li;
                    li.fadeIn();
                    
                    if (file.type.toLowerCase() === 'text/csv' || file.type.toLowerCase() === 'text/comma-separated-values' || file.type.toLowerCase() === 'text/tab-separated-values') {
                        $('#fileuploaded_'+ index).attr('src', '/image/icons/csv.png');
                    } else if (file.type.toLowerCase() === 'text/plain') {
                        $('#fileuploaded_'+ index).attr('src', '/image/icons/txt.png');
                    } else if (file.type.toLowerCase() === 'application/x-shockwave-flash') {
                        $('#fileuploaded_'+ index).attr('src', '/image/icons/_blank.png');
                    } else if (file.type.toLowerCase() === 'application/msword') {
                        $('#fileuploaded_'+ index).attr('src', '/image/icons/doc.png');
                    } else if (file.type.toLowerCase() === 'application/pdf' || file.type.toLowerCase() === 'application/x-pdf') {
                        $('#fileuploaded_'+ index).attr('src', '/image/icons/pdf.png');
                    } else if (file.type.toLowerCase() === 'application/msexcel') {
                        $('#fileuploaded_'+ index).attr('src', '/image/icons/xls.png');
                    } else if (file.type.toLowerCase() === 'audio/x-mpeg') {
                        $('#fileuploaded_'+ index).attr('src', '/image/icons/_blank.png');
                    } else {
                        reader = new FileReader();
                        reader.onload = function (e) {
                            $('#fileuploaded_'+ index).attr('src', e.target.result);	
                        };
                        reader.readAsDataURL(file);
                    }
                });

                $(".uploadStart").on('click', function (e) {
                    data.submit();
                });
            },
            progress: function (e, data) {
                var progress = parseInt(data.loaded / data.total * 100, 10);
                data.context.find('.progress-bar span').css({
                    'width': progress + '%',
                    display: 'block'
                });
            },
            dragover: function (e) {
                $("#dropHere").addClass('dropHere');
            },
            done: function (e, data) {
                var that = $(this).data('fileupload');
                if (data.context) {
                    $('#column_left').jstree('refresh');
                    data.context.each(function (index) {
                        $('html,body').animate(
                                {
                                    scrollTo: $("#scrollDown").offset().top
                                },
                        500);
                        var preview = $(this).find('div:nth-child(1)');
                        var msgWrapper = $(this).find('div:nth-child(4)');
                        $(this).find('.progress-bar span').css({
                            'width': '100%',
                        }).parent().fadeOut();
                        file = (typeof (data.result) == 'object') ? data.result : {error: 'Error: no se pudo obtener el resultado del archivo'};
                        if (file.error) {
                            msgWrapper.removeClass('progress-bar').html('<b>Error: ' + file.error + '</b>').fadeIn();
                        } else {
                            msgWrapper.removeClass('progress-bar').html('<b>' + file.success + '</b>').fadeIn();
                            preview.fadeOut(function (e) {
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
                                that._adjustMaxNumberOfFiles(-1);
                            }
                        });
                        data.maxNumberOfFilesAdjusted = true;
                    }

                    that._transition(template).done(function () {
                        data.context = $(this);
                        that._trigger('completed', e, data);
                    });
                }
            }
        });
    } else {
        $("#dropHere").hide();
    }

    $('#create').on('click', function () {
        n = $.jstree.reference('#column_left');
        var data= {};
        data.nodes = n.get_selected();
        data.node = data.nodes[0];
        
        var i = 1;
        $('#'+ data.node +' > .jstree-children > li').each(function(){
            if ($(this).attr('directory').indexOf('nueva-carpeta') !== -1) {
                i++;
            }
        });
        newFolder = 'nueva-carpeta';
        if (i>1) {
            newFolder += i;
        }
        newNodeId = n.create_node(data.node, newFolder);
        
        if (newNodeId) {
            $('#'+ data.node +' > .jstree-children li:last-child').attr('id', newNodeId);
            
            var directory = '';
            if (typeof $('#' + data.node).attr('directory') !== 'undefined') {
                directory = $('#' + data.node).attr('directory');
            } else if (typeof $.cookie('jstree_' + data.node) !== 'undefined') {
                directory = $.cookie('jstree_' + data.node);
            }
            
            if (directory.length > 0 || data.node === 'j1_1') {
                $.get('index.php?r=content/file/create&directory=' + directory + '&id=' + newNodeId + '&token=' + token,
                {
                    position: data.position,
                    name: newFolder
                })
                .done(function (d) {
                    d = $.parseJSON(d);
                    if (!d.error) {
                        $('#' + d.id).attr({
                            directory: d.directory
                        });

                        n.edit(newNodeId, newFolder);

                        $.cookie('jstree_' + d.id, d.directory);
                        $.cookie('jstree_directory', d.directory);
                    } else {
                        alert('Ya existe un directorio con el nombre "nueva-carpeta". Por favor renombre el directorio existente e intente de nuevo');
                    }
                })
                .fail(function () {
                    /* mostrar mensaje y registrar error */
                    alert('No se pudo crear la carpeta. Por favor intente de nuevo, si el la falla persiste, comuniquese con el administrador');
                });
            }
        } else {
            alert('Ya existe una carpeta con el nombre "nueva-carpeta". Por favor renombre la carpeta existente e intente de nuevo');
        }
    });

    $('#delete').on('click', function (e) {
        if (confirm('Est\u00E1s seguro que deseas eliminar estos ficheros?')) {
            if ($("#form").serialize()) {
                deleteFile();
            } else {
                $.cookie('buttonDeleteClicked', 1, {path: '/'});
                n = $.jstree.reference('#column_left');
                var nodes = n.get_selected();

                var path = '';
                $.each(nodes, function (i, node) {
                    path += $('#' + node).attr('directory') + ':';
                });

                if (path) {
                    $.get('index.php?r=content/file/delete&token=' + token + '&path=' + path,
                            function (json) {
                                if (json.error) {
                                    alert(json.error);
                                } else {
                                    n.delete_node(nodes);
                                    $.removeCookie('buttonDeleteClicked', {path: '/'});
                                }
                            });
                } else {
                    alert('No se ha seleccionado ninguna carpeta o archivo');
                }
            }
        }
    });

    $('#copy').bind('click', function () {
        $('#dialog').remove();
        html = '<div id="dialog">';
        html += '<input type="text" name="name" value="" placeholder="Nombre del Archivo"> <input type="button" value="Submit">';
        html += '</div>';
        $('#column_right').prepend(html);
        $('#dialog').dialog({
            title: 'Copiar Carpeta o Archivo',
            resizable: false
        });

        $('#dialog select[name=\'to\']').load('index.php?r=content/file/folders&token=' + token);
        $('#dialog input[type=\'button\']').bind('click', function () {
            path = $('#column_right a.selected').attr('file');
            if (path) {
                $.ajax({
                    url: 'index.php?r=content/file/copy&token=' + token,
                    type: 'POST',
                    data: 'path=' + encodeURIComponent(path) + '&name=' + encodeURIComponent($('#dialog input[name=\'name\']').val()),
                    dataType: 'json',
                    success: function (json) {
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
        $('#column_right').prepend(html);
        $('#dialog').dialog({
            title: 'Cambiar Nombre',
            resizable: false
        });
        $('#dialog input[type=\'button\']').bind('click', function () {
            path = $('#column_right a.selected').attr('file');
            if (path) {
                $.ajax({
                    url: 'index.php?r=content/file/rename&token=' + token,
                    type: 'POST',
                    data: 'path=' + encodeURIComponent(path) + '&name=' + encodeURIComponent($('#dialog input[name=\'name\']').val()),
                    dataType: 'json',
                    success: function (json) {
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
       $('#column_left').jstree('refresh');
    });

    $('#selectFiles').on('click', function () {
        let files = $('#column_right input[name="filess[]"]:checked');
        let filePath = window.baseImageUrl +'data/';
        if (window.isFckeditor) {
            for (f in files) {
                window.opener.CKEDITOR.tools.callFunction(1, filePath + f.val());
            }
            self.close();
        } else if (theme_editor) {
            let filename = files[0].val();
            parent.setImage('data/' + filename);
            parent.$.fancybox.close();
            
        } else {
            parent.addImage(files.map((k,v) => {
                return v.value;
            }), filePath);
            parent.$.fancybox.close();
        }
    });
});