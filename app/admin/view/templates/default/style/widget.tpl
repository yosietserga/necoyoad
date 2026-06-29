<?php echo $header; ?>
<?php echo $navigation; ?>
<?php include_once('widget_helpers.tpl'); ?>

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
                <h1>Widgets</h1>
                
                <?php if ($stores) { ?>
                <div class="pull-right">
                    <label><?php echo $l('entry_store'); ?></label><br />
                    <select onchange="window.location = '<?php echo $Url::createAdminUrl("style/widget"); ?>&landing_page=<?php echo $Request->getQuery('landing_page'); ?>&store_id='+ this.value">
                        <option value="all"<?php if (!$store_id && $store_id!=0) { echo ' selected="selected"'; } ?>><?php echo $l('All'); ?></option>
                        <option value="0"<?php if ($store_id==0) { echo ' selected="selected"'; } ?>><?php echo $l('text_default'); ?></option>
                        <?php foreach ($stores as $store) { ?>
                        <option value="<?php echo $store['store_id']; ?>"<?php if ($store_id==$store['store_id']) { echo ' selected="selected"'; } ?>><?php echo $store['name']; ?></option>
                        <?php } ?>
                    </select>
                </div>
                <?php } ?>

                <div class="pull-right">
                    <label><?php echo $l('Landing Page'); ?></label><br />
                    <select onchange="window.location = '<?php echo $Url::createAdminUrl("style/widget"); ?>&store_id=<?php echo $Request->getQuery('store_id'); ?>&landing_page='+ this.value">
                        <option value="all"<?php if (!$Request->hasQuery('landing_page') || $Request->getQuery('landing_page')=='all') { echo ' selected="selected"'; } ?>><?php echo $l('All'); ?></option>
                        
                        <?php foreach ($routes as $group => $landing_pages) { ?>
                        <optgroup label="<?php echo $group; ?>">

                            <?php foreach ($landing_pages as $text_var => $landing_page) { ?>
                            <option value="<?php echo $landing_page; ?>"<?php if ($landing_page==$Request->getQuery('landing_page')) { echo ' selected="selected"'; } ?>><?php echo $l($text_var) ?></option>
                            <?php } ?>

                        </optgroup>
                        <?php } ?>
                    </select>
                </div>
            </div>

            <div class="clear"></div><br />

            <div class="grid_2" id="widgetsWrapper" style="margin:0px !important;">
                <input type="text" id="qWidgets" placeholder="<?php echo $l('text_filter'); ?>" />
                <ul id="widgetsPanel" class="widget widgetsPanel">
                    <?php foreach ($modules as $module) { ?>
                    <li class="neco-widget" data-title="<?php echo $module['name']; ?>" data-widget="<?php echo $module['widget']; ?>">
                        <b><?php echo $module['name']; ?></b><br />
                        <?php echo $module['description']; ?>
                    </li>
                    <?php } ?>
                </ul>
            </div>

            <div class="grid_10" id="blocksWrapper" data-widgets-wrapper style="margin:0px !important;padding:0px !important;">
                <div class="grid_11">
                    <h2>Cabecera (Header)</h2>
                    <ul id="widgetHeader" class="widgetRowsWrapper" data-position="header">
                    <?php if (isset($rows['header']) && is_array($rows['header']) && !empty($rows['header'])) { ?>
                    <?php foreach ($rows['header'] as $row) { ?>
                        <?php $position='header'; ?>
                        <?php include(dirname(__FILE__) .'/../shared/row_widget_form_main.tpl'); ?>
                    <?php } //end foreach ?>
                    <?php } //end if ?>
                    </ul>

                    <a class="button" onclick="addRow(this)">Add Row</a>
                </div>
                
                <div class="clear"></div>
                
                <div class="grid_11">
                    <h2>Contenido Destacado (Featured Content)</h2>
                    <ul id="widgetFeaturedContent" class="widgetRowsWrapper" data-position="featuredContent">
                    <?php if (isset($rows['featuredContent']) && is_array($rows['featuredContent']) && !empty($rows['featuredContent'])) { ?>
                    <?php foreach ($rows['featuredContent'] as $row) { ?>
                        <?php $position='featuredContent'; ?>
                        <?php include(dirname(__FILE__) .'/../shared/row_widget_form_main.tpl'); ?>
                    <?php } //end foreach ?>
                    <?php } //end if ?>
                    </ul>
                    <a class="button" onclick="addRow(this)">Add Row</a>
                </div>
                
                <div class="clear"></div>
                
                <div class="grid_3">
                    <h2>Columna Izquierda</h2>
                    <ul id="widgetColumnLeft" class="widgetRowsWrapper" data-position="column_left">
                    <?php if (isset($rows['column_left']) && is_array($rows['column_left']) && !empty($rows['column_left'])) { ?>
                        <?php foreach ($rows['column_left'] as $row) { ?>
                        <?php $position='column_left'; ?>
                            <?php include(dirname(__FILE__) .'/../shared/row_widget_form_main.tpl'); ?>
                    <?php } //end foreach ?>
                    <?php } //end if ?>
                    </ul>
                    <a class="button" onclick="addRow(this)">Add Row</a>
                </div>
                
                <div class="grid_5" style="margin-left: 2%;">
                    <h2>Principal</h2>
                    <ul id="widgetMain" class="widgetRowsWrapper" data-position="main">
                    <?php if (isset($rows['main']) && is_array($rows['main']) && !empty($rows['main'])) { ?>
                        <?php foreach ($rows['main'] as $row) { ?>
                        <?php $position='main'; ?>
                            <?php include(dirname(__FILE__) .'/../shared/row_widget_form_main.tpl'); ?>
                    <?php } //end foreach ?>
                    <?php } //end if ?>
                    </ul>
                    <a class="button" onclick="addRow(this)">Add Row</a>
                </div>
                
                <div class="grid_3">
                    <h2>Columna Derecha</h2>
                    <ul id="widgetColumnRight" class="widgetRowsWrapper" data-position="column_right">
                    <?php if (isset($rows['column_right']) && is_array($rows['column_right']) && !empty($rows['column_right'])) { ?>
                        <?php foreach ($rows['column_right'] as $row) { ?>
                        <?php $position='column_right'; ?>
                            <?php include(dirname(__FILE__) .'/../shared/row_widget_form_main.tpl'); ?>
                    <?php } //end foreach ?>
                    <?php } //end if ?>
                    </ul>
                    <a class="button" onclick="addRow(this)">Add Row</a>
                </div>

                <div class="clear"></div>
                
                <div class="grid_11">
                    <h2>Antes del Pie de P&aacute;gina</h2>
                    <ul id="featuredFooter" class="widgetRowsWrapper" data-position="featuredFooter">
                    <?php if (isset($rows['featuredFooter']) && is_array($rows['featuredFooter']) && !empty($rows['featuredFooter'])) { ?>
                        <?php foreach ($rows['featuredFooter'] as $row) { ?>
                        <?php $position='featuredFooter'; ?>
                            <?php include(dirname(__FILE__) .'/../shared/row_widget_form_main.tpl'); ?>
                    <?php } //end foreach ?>
                    <?php } //end if ?>
                    </ul>
                    <a class="button" onclick="addRow(this)">Add Row</a>
                </div>
                
                <div class="clear"></div>
                
                <div class="grid_11">
                    <h2>Pie de P&aacute;gina</h2>
                    <ul id="widgetFooter" class="widgetRowsWrapper" data-position="footer">
                    <?php if (isset($rows['footer']) && is_array($rows['footer']) && !empty($rows['footer'])) { ?>
                        <?php foreach ($rows['footer'] as $row) { ?>
                        <?php $position='footer'; ?>
                            <?php include(dirname(__FILE__) .'/../shared/row_widget_form_main.tpl'); ?>
                    <?php } //end foreach ?>
                    <?php } //end if ?>
                    </ul>
                    <a class="button" onclick="addRow(this)">Add Row</a>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="jsWrapper"></div>
<?php echo $footer; ?>