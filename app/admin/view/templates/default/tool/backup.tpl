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

    <div class="grid_12">
        <div class="box">
            <div class="header">
                <hgroup><h1>Mantenimiento BD</h1></hgroup>
                <div class="buttons">
                    <a onclick="location = '<?php echo $insert; ?>'" class="button">Comprobar BD</a>
                    <a onclick="$('#restore').submit();" class="button">Restaurar</a>
                    <a onclick="$('#backup').submit();" class="button">Respaldar</a>
                </div>
            </div>    

            <div class="clear"></div><br />

            <h3>Restaurar BD</h3>
            <form action="<?php echo $restore; ?>" method="post" enctype="multipart/form-data" id="restore">  
                <div class="row">       
                    <label><?php echo $l('entry_restore'); ?></label>
                    <input title="<?php echo $l('help_restore'); ?>" type="file" name="import" />
                </div>


            </form>

            <div class="clear"></div><br />

            <h3>Respaldar BD</h3>
            <form action="<?php echo $backup; ?>" method="post" enctype="multipart/form-data" id="backup">
                <div class="row">
                    <label><?php echo $l('entry_backup'); ?>
                        <a onclick="$('input[name*=\'backup\']').attr('checked', 'checked');"><?php echo $l('text_select_all'); ?></a> / 
                        <a onclick="$('input[name*=\'backup\']').attr('checked', '');"><?php echo $l('text_unselect_all'); ?></a>
                    </label>
                    <table>
                        <thead>
                            <tr>
                                <th><input type="checkbox" checked="checked" onclick="$('input[name*=\'backup\']').attr('checked', this.checked);" /></th>
                                <th>Nombre de la Tabla</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($tables as $table) { ?>
                            <tr>
                                <td>
                                    <input type="checkbox" name="backup[]" value="<?php echo $table; ?>" checked="checked" />
                                </td>
                                <td>
                                    <?php echo $table; ?>
                                </td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
            </form>
        </div>
    </div>
</div>
<?php echo $footer; ?>