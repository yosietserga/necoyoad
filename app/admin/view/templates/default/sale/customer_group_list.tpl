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
                <h1><?php echo $l('heading_title'); ?></h1>
                <div class="buttons">
                    <a onclick="location = '<?php echo $insert; ?>'" class="button"><?php echo $l('button_insert'); ?></a>
                </div>
            </div>    

            <div class="clear"></div><br />

            <h3>Filtros<span id="filters">[ Mostrar ]</span></h3>
            <form action="<?php echo $search; ?>" method="post" enctype="multipart/form-data" id="formFilter">        
                <div class="grid_11">
                    <div class="row">       
                        <label>Nombre del Grupo:</label>
                        <input type="text" name="filter_name" value="" />
                    </div>

                    <div class="row">
                        <label>Asociado Al Cliente:</label>
                        <input type="text" name="filter_customer" value="" />
                    </div>
                </div>

                <div class="grid_11">
                    <div class="row">
                        <label>Fecha Inicial:</label>
                        <input type="necoDate" name="filter_date_start" value="" />
                    </div>
                    <div class="row">
                        <label>Fecha Final:</label>
                        <input type="necoDate" name="filter_date_end" value="" />
                    </div>
                    <div class="row">
                        <label>Mostrar:</label>
                        <select name="limit">
                            <option value="">Selecciona una cantidad</option>
                            <option value="10">10 Resultados por p&aacute;gina</option>
                            <option value="25">25 Resultados por p&aacute;gina</option>
                            <option value="50">50 Resultados por p&aacute;gina</option>
                            <option value="100">100 Resultados por p&aacute;gina</option>
                            <option value="150">150 Resultados por p&aacute;gina</option>
                        </select>
                    </div>
                </div>

                <div class="clear"></div><br />
            </form>
        </div>
    </div>
    
    <div class="clear"></div>
    
    <div class="grid_12">
        <div class="box">
            <div id="gridPreloader"></div>
            <div id="gridWrapper"></div>
        </div>
    </div>
</div>
<?php echo $footer; ?>