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
                <a onclick="location = '<?php echo $Url::createAdminUrl('extension/module/generate'); ?>'" class="button"><?php echo $l('Generate'); ?></a>
                </div>
            </div>    

            <div class="clear"></div><br />

            <h3>Filtros<span id="filters">[ Mostrar ]</span></h3>
            <form action="<?php echo $search; ?>" method="post" enctype="multipart/form-data" id="formFilter">        
                <div class="grid_11">
                    <div class="row">       
                        <label>Nombre del Tema:</label>
                        <input type="text" name="filter_name" value="" />
                    </div>

                    <div class="row">
                        <label>Asociado a la Plantilla:</label>
                        <input type="text" name="filter_template" value="" />
                    </div>

                    <div class="row">
                        <label>Ordernar Por:</label>
                        <select name="sort">
                            <option value="">Selecciona un campo</option>
                            <option value="name">Nombre</option>
                            <option value="sort_order">Posici&oacute;n</option>
                            <option value="date_added">Fecha cuando se cre&oacute;</option>
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
            <div id="gridWrapper">
                <?php foreach($modules as $template) { ?>
                <div class="grid_4" style="margin:30px 5px;padding:5px;">
                    <a href="javascript:void(0)">
                        <img src="<?php echo $template['thumb']; ?>" alt="<?php echo $template['name']; ?>" width="200" />
                    </a>
                    <div class="product_desc">
                        <h2><?php echo $template['name']; ?></h2>
                        
                        <p>
                            Ref: <?php echo $template['ref']; ?>&nbsp;
                            <?php if (in_array($template['ref'], $templates_installed)) { ?>
                            <b>Plantilla Instalada</b>
                            <?php } ?>
                        </p>
                        <span class="tempalte_review">
                            <img src="images/stars_<?php echo (int)$template['review']; ?>.png" alt="Review: <?php echo (int)$template['review']; ?>" />
                        </span>
                        <p>
                            <i class="fa fa-home"></i>&nbsp;Autor: <?php echo $template['author']['name']; ?> <a href="<?php echo $template['author']['profile']; ?>"><i class="fa fa-home"></i></a><br />
                            <i class="fa fa-home"></i>&nbsp;Versión: <?php echo $template['version']; ?><br />
                            <i class="fa fa-home"></i>&nbsp;Compatibilidad: NTS <?php echo implode(', ',$template['compatibility']); ?><br />
                            <i class="fa fa-home"></i>&nbsp;Descargas: <?php echo $template['downloads']; ?><br />
                            <i class="fa fa-home"></i>&nbsp;Status: <?php echo ($template['status']) ? $l('Disponible') : $l('Indisponible'); ?><br />
                        </p>
                        <p class="price" data-price="<?php echo $template['price']; ?>" data-currency="<?php echo $template['VEF']; ?>"><?php echo $template['price_text']; ?></p>
                        <br />
                        <a class="button" href="<?php echo $template['demourl']; ?>">Demo</a>
                        <a class="button" href="<?php echo $template['url']; ?>" target="_blank">Más Detalles</a>
                    </div>
                </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>
<?php echo $footer; ?>