<!DOCTYPE html><html lang="es"><head>
    <title><?php echo $title; ?></title>
    <!--[if IE]><meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"><![endif]-->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
    <base href="<?php echo $base; ?>" />
    <link rel="stylesheet" type="text/css" href="<?php echo str_replace('%theme%', $Config->get('config_admin_template'), HTTP_ADMIN_THEME_CSS); ?>vendor.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo str_replace('%theme%', $Config->get('config_admin_template'), HTTP_ADMIN_THEME_CSS); ?>filemanager.css" />
    
    <script src="<?php echo str_replace('%theme%', $Config->get('config_admin_template'), HTTP_ADMIN_THEME_JS); ?>vendor/modernizr.min.js"></script>
</head>
<body>
    <div class="container">
    
        <div id="tabs" class="grid_12">
            <ul>
                <li id="browser">Browser</li>
                <li id="frompc">Desde el PC</li>
                <!--<li id="fromurl">Desde URL</li>-->
            </ul>
        </div>
        
        <div classW="clear"></div>
        
        <div class="tabs grid_12" id="tabbrowser">
            <div id="menu">
                <a id="create" class="button" style="background-image: url('<?php echo str_replace('%theme%', $Config->get('config_admin_template'), HTTP_ADMIN_THEME_IMAGE); ?>filemanager/folder.png');">
                    <span class="hideOnMobile hideOnTablet"><?php echo $l('button_folder'); ?></span>
                </a>
                <a id="delete" class="button" style="background-image: url('<?php echo str_replace('%theme%', $Config->get('config_admin_template'), HTTP_ADMIN_THEME_IMAGE); ?>filemanager/edit-delete.png');">
                    <span class="hideOnMobile hideOnTablet"><?php echo $l('button_delete'); ?></span>
                </a>
                <!--
                <a id="move" class="button" style="background-image: url('<?php echo str_replace('%theme%', $Config->get('config_admin_template'), HTTP_ADMIN_THEME_IMAGE); ?>filemanager/edit-cut.png');">
                    <span class="hideOnMobile hideOnTablet"><?php echo $l('button_move'); ?></span>
                </a>
                <a id="copy" class="button" style="background-image: url('<?php echo str_replace('%theme%', $Config->get('config_admin_template'), HTTP_ADMIN_THEME_IMAGE); ?>filemanager/edit-copy.png');">
                    <span class="hideOnMobile hideOnTablet"><?php echo $l('button_copy'); ?></span>
                </a>
                <a id="rename" class="button" style="background-image: url('<?php echo str_replace('%theme%', $Config->get('config_admin_template'), HTTP_ADMIN_THEME_IMAGE); ?>filemanager/edit-rename.png');">
                    <span class="hideOnMobile hideOnTablet"><?php echo $l('button_rename'); ?></span>
                </a>
                -->
                <a onclick="$('.tabs').hide();$('#tabfrompc').show();" class="button" style="background-image: url('<?php echo str_replace('%theme%', $Config->get('config_admin_template'), HTTP_ADMIN_THEME_IMAGE); ?>filemanager/upload.png');">
                    <span class="hideOnMobile hideOnTablet"><?php echo $l('button_upload'); ?></span>
                </a>
                <a id="refresh" class="button" style="background-image: url('<?php echo str_replace('%theme%', $Config->get('config_admin_template'), HTTP_ADMIN_THEME_IMAGE); ?>filemanager/refresh.png');">
                    <span class="hideOnMobile hideOnTablet"><?php echo $l('button_refresh'); ?></span>
                </a>
                <a id="selectFiles" class="button" style="background-image: url('<?php echo str_replace('%theme%', $Config->get('config_admin_template'), HTTP_ADMIN_THEME_IMAGE); ?>filemanager/accept.png');">
                    <span class="hideOnMobile hideOnTablet"><?php echo $l('Add Selected Files To Form'); ?></span>
                </a>
                <input type="text" name="qfiles" value="" onkeyup="searchFiles(this);" placeholder="<?php echo $l('Search Files'); ?>" />
            </div>
            
            <div class="clear"></div>
            
            <div class="grid_3" id="column_left"></div>
            <form id="form">
                <div class="grid_8" id="column_right"></div>
            </form>
        </div>
        
        <div class="clear"></div>
        
        <div class="tabs" id="tabfrompc">
            <p>
                <b>Instruccioes:</b>
                Antes de subir los archivos, debes seleccionar la carpeta donde quieres guardarlos. Haz click 
                <a href="#" title="Seleccionar carpeta" onclick="$('.tabs').hide();$('#tabfrompc').show();return false;">aqu&iacute;</a> para seleccionar la carpeta.
            </p>
            
            <a class="uploadStart">Comenzar a Subir</a>
            
            <div class="clear"></div>
            
            <input type="hidden" id="directoryForUpload" value="" />
            
            <div id="dropHere">
                <input id="fileupload" type="file" name="files[]" multiple="multiple" accept="images/*" capture="camera" />
                <p>
                    Arrastra los archivos hasta aqu&iacute;
                    <br />
                    <span>Tambi&eacute;n puedes hacer click en el bot&oacute;n para <a class="buttonBlue">Examinar</a> y elegir tus archivos</span>
                </p>
            </div>
            
            <ul id="filesUploaded"></ul>
            
            <div id="scrollDown"></div>
            
        </div>
        
        <div class="clear"></div>
        <!--
        <div class="tabs" id="tabfromurl">
        
        </div>
        -->
    </div>
<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
<script>window.$ || document.write('<script src="<?php echo str_replace('%theme%', $Config->get('config_admin_template'), HTTP_ADMIN_THEME_JS); ?>vendor/jquery.min.js"><\/script>')</script>
<script type="text/javascript" src="<?php echo str_replace('%theme%', $Config->get('config_admin_template'), HTTP_ADMIN_THEME_JS); ?>vendor/jquery-ui.min.js"></script>
<script type="text/javascript" src="<?php echo str_replace('%theme%', $Config->get('config_admin_template'), HTTP_ADMIN_THEME_JS); ?>vendor/jstree/jstree.min.js"></script>
<script type="text/javascript" src="<?php echo str_replace('%theme%', $Config->get('config_admin_template'), HTTP_ADMIN_THEME_JS); ?>vendor/fileUploader/jquery.iframe-transport.js"></script>
<script type="text/javascript" src="<?php echo str_replace('%theme%', $Config->get('config_admin_template'), HTTP_ADMIN_THEME_JS); ?>vendor/fileUploader/jquery.fileupload.js"></script>
<script type="text/javascript" src="<?php echo str_replace('%theme%', $Config->get('config_admin_template'), HTTP_ADMIN_THEME_JS); ?>commonfilemanager.js"></script>
<script type="text/javascript">
$(function(){
    $(".tabs").hide();
    $("#tabbrowser").show();
    $("#tabs li").click(function(){
        $(".tabs").hide();
        $("#tab" + this.id).show();  
    });
    
    var windowHeight = $(window).height();
    $("#dropHere").css({
        height:(windowHeight * 60 / 100) + 'px'
    });
    
    window.baseImageUrl = '<?php echo HTTP_IMAGE; ?>';
    window.field = '<?php echo $field; ?>';
    window.preview = '<?php echo $preview; ?>';
    window.isFckeditor = '<?php echo $fckeditor; ?>';

    window.errorSelect = '<?php echo $error_select; ?>';

    loadDirectories('<?php echo $GET['token']; ?>');
});
function searchFiles(e) {
    var q = '';
    q = e.value.toLowerCase().trim();
    $('#column_right li p').each(function(){
        name = $(this).text().replace(/-/g, ' ');
        if (name.indexOf(q) === -1) {
            $(this).closest('li').hide();
        } else {
            $(this).closest('li').show();
        }
    });
}
</script>
</body>
</html>