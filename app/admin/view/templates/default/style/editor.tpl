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

    <div class="grid_3">
        <div class="box">
            <div class="header">
                <h1><?php echo $l('Template Editor'); ?></h1>
            </div>
            <div id="views">
            <?php foreach ($views as $key => $value) { ?>
                <br />
                <h3><?php echo $value['folder']; ?></h3>
                <div>
                    <?php foreach ($value['files'] as $k => $v) { ?>
                    <a href="<?php echo $Url::createAdminUrl("style/editor",array("t"=>"tpl","f"=>basename($value['folder']) ."/". basename($v),"tpl"=>$template)); ?>"><?php echo basename($v); ?></a><br />
                    <?php } ?>
                </div>
            <?php } ?>
            </div>

            <div class="clear"></div><br />
            <h2>Estilos (CSS)</h2>
            <div id="styles">
            <?php foreach ($css_files as $key => $value) { ?>
                <a href="<?php echo $Url::createAdminUrl("style/editor",array("t"=>"css","f"=>$value,"tpl"=>$template)); ?>"><?php echo $value; ?></a><br />
            <?php } ?>
            </div>
        </div>
    </div>

    <div class="grid_8">
        <?php if (!$error) { ?>
        <h1><?php echo $filename; ?></h1>
        <a class="button" onclick="$('#form').submit()">Guardar</a>
        <a class="button" onclick="window.location='<?php echo str_replace("&","&amp;",$cancel); ?>'">Cancelar</a>
        <div class="clear"></div><br />
        <form action="<?php echo str_replace("&","&amp;",$action); ?>" method="post" enctype="multipart/form-data" id="form">  
            <textarea name="code" id="code"><?php echo $code; ?></textarea>
            <div id="editor" style="border: solid 1px #900;width:800px;height:1800px;display:block;float:left"></div>
        </form>
        <?php } else { ?>
        <div class="grid_12"><div class="message warning"><?php echo $msg; ?></div></div>
        <?php } ?>
    </div>
</div>
<script>
$(function(){
    var editor = ace.edit("editor");
    var textarea = $('#code').hide();
    editor.setTheme("ace/theme/twilight");

    <?php if ($_GET['t']=='tpl') { ?>
    editor.getSession().setValue(textarea.val());
    editor.getSession().setMode('ace/mode/php');

    <?php } elseif ($_GET['t']=='css') { ?>
    editor.getSession().setValue(textarea.val());
    editor.getSession().setMode('ace/mode/css');
    <?php } ?>

    editor.getSession().setUseWrapMode(true);
    editor.getSession().on('change', function(){
        textarea.val(editor.getSession().getValue());
    });
    $('#footer').css({
        marginTop:'200px'
    });
});
</script>
<?php echo $footer; ?>