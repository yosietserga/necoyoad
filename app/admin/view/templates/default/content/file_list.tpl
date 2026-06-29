<?php echo $header; ?>
<?php echo $navigation; ?>
<div class="container">
    
    <?php if (isset($breadcrumbs) && is_array($breadcrumbs)) { ?>
    <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
    </ul>
    <?php } ?>
    
    <?php if (isset($success) && $success) { ?><div class="grid_12"><div class="message success"><?php echo $success; ?></div></div><?php } ?>
    <?php if ((isset($msg) && $msg) || (isset($error_warning) && $error_warning)) { ?><div class="grid_12"><div class="message warning"><?php echo $msg ?? $error_warning; ?></div></div><?php } ?>
    <?php if (isset($error) && $error) { ?><div class="grid_12"><div class="message error"><?php echo $error; ?></div></div><?php } ?>
    <div class="grid_12" id="msg"></div>

    <div class="box">
        <div class="header">
            <h1><?php echo $l('heading_title'); ?></h1>
            <div class="buttons">
            <a onclick="location = '<?php echo $insert; ?>'" class="button"><?php echo $l('button_insert'); ?></a>
            </div>
        </div>    
          
        <div class="clear"></div><br />
        
    </div>

    <div class="clear"></div>

    <div class="box" id="fileMgr">
        
        <div class="grid_12" id="tabs">
            <a id="browser" class="button"><i class="fa fa-browser"></i>Browser</a>
            <a id="frompc" class="button"><i class="fa fa-browser"></i>Desde el PC</a>
            <a class="button"><i class="fa fa-browser"></i>Desde URL</a>
        </div>
        
        <div class="clear"></div><hr />
        
        <div class="tabs grid_12" id="tabbrowser">
            <div id="menu">
                <a id="delete" class="button"><?php echo $l('button_delete'); ?></a>
                <a id="copy" class="button"><?php echo $l('button_copy'); ?></a>
                <a id="rename" class="button"><?php echo $l('button_rename'); ?></a>
                <a onclick="$('.tabs').hide();$('#tabfrompc').show();" class="button"><?php echo $l('button_upload'); ?></a>
                <a id="refresh" class="button"><?php echo $l('button_refresh'); ?></a>
            </div>
            
            <div class="clear"></div>
            
            <div class="grid_3">
                <div class="column" id="fileMgrTree"></div>
            </div>
            <form id="form">
                <div class="grid_9">
                    <div class="column" id="fileMgrFiles"></div>
                </div>
            </form>
        </div>
        
        <div class="clear"></div>
        
        <div class="tabs" id="tabfrompc">
            <div class="clear"></div>
            <p><b>Instruccioes:</b> Antes de subir los archivos, debes seleccionar la carpeta donde quieres guardarlos. Haz click <a href="#" title="Seleccionar carpeta" onclick="$('.tabs').hide();$('#tabfrompc').show();return false;">aqu&iacute;</a> para seleccionar la carpeta.</p>
            <a class="uploadStart">Comenzar a Subir</a>
            <div class="clear"></div>
            <input id="fileupload" type="file" name="files[]" multiple="multiple" />
            <input type="hidden" id="directoryForUpload" value="" />
            <div id="dropHere"><p>Arrastra los archivos hasta aqu&iacute;<br /><span>Selecciona los archivos que quieres subir y arr&aacute;stralos hasta aqu&iacute;</span></p></div>
            <ul id="filesUploaded"></ul>
            <div id="scrollDown"></div>
        </div>
        
        <div class="clear"></div>
        <!--
        <div class="tabs" id="tabfromurl">
        
        </div>
        -->
        
<!--[if gte IE 8]><script src="js/cors/jquery.xdr-transport.js"></script><![endif]-->
    </div>
</div>
<script>
$(function(){
    loadDirectories('<?php echo $GET['token']; ?>');
});
</script>
<?php echo $footer; ?>