<?php echo $header; ?>
<?php if ($error_warning) { ?>
<div class="grid_12 warning"><?php echo $error_warning; ?></div>
<?php } ?>
<?php if (isset($success) && $success) { ?>
<div class="grid_12 success"><?php echo $success; ?></div>
<?php } ?>

<div class="grid_12" id="msg"></div>

<div class="grid_12">
    <div class="box">
        <div class="header">
            <h1><?php echo $l('heading_title'); ?></h1>
            <div class="buttons">
                <a onclick="location = '<?php echo $insert; ?>'" class="button">Agregar Producto</a>
                <a onclick="location = '<?php echo $import; ?>'" class="button">Importar</a>
                <a onclick="location = '<?php echo $export; ?>'" class="button">Exportar</a>
            </div>
        </div>    
          
        <div class="clear"></div><br />
        
        <h3>Filtros<span id="filters">[ Mostrar ]</span></h3>
        <form action="<?php echo $search; ?>" method="post" enctype="multipart/form-data" id="formFilter">        
            <div class="grid_11">
                <div class="row">       
                    <label>T&iacute;tulo de la P&aacute;gina:</label>
                    <input type="text" name="filter_name" value="" />
                </div>
                    
                <div class="row">
                    <label>Agrupado Por:</label>
                    <select name="filter_group">
                        <?php foreach ($groups as $groups) { ?>
                            <option value="<?php echo $groups['value']; ?>"<?php if ($groups['value'] == $filter_group) { ?> selected="selected"<?php } ?>><?php echo $groups['text']; ?></option>
                      <?php } ?>
                    </select>
                </div>
                
                <div class="row">
                    <label>Estado del Pedido:</label>
                    <select name="filter_order_status_id" style="margin-top: 4px;">
                        <option value="0"><?php echo $l('text_all_status; ?></option>
                        <?php foreach ($order_statuses as $order_status) { ?>
                        <option value="<?php echo $order_status['order_status_id']; ?>"<?php if ($order_status['order_status_id'] == $filter_order_status_id) { ?> selected="selected"<?php } ?>><?php echo $order_status['name']; ?></option>
                        <?php } ?>
                    </select>
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
                    <input type="necoNumber" name="limit" value="" />
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
<?php echo $footer; ?>