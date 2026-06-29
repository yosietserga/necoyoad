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

    <div class="grid_12" style="margin: 0">
        <div class="box">

            <?php if ($stores) { ?>
            <div class="pull-right">
                <select onchange="window.location = '<?php echo $Url::createAdminUrl("style/theme/editor"); ?>&landing_page=<?php echo $Request->getQuery('landing_page'); ?>&store_id='+ this.value">
                    <option value="0"<?php if ($store_id==0) { echo ' selected="selected"'; } ?>><?php echo $l('text_default'); ?></option>
                    <?php foreach ($stores as $store) { ?>
                    <option value="<?php echo $store['store_id']; ?>"<?php if ($store_id==$store['store_id']) { echo ' selected="selected"'; } ?>><?php echo $store['name']; ?></option>
                    <?php } ?>
                </select>
            </div>
            <?php } ?>

            <div class="pull-right">
                <select onchange="window.location = '<?php echo $Url::createAdminUrl("style/theme/editor"); ?>&store_id=<?php echo $Request->getQuery('store_id'); ?>&landing_page='+ this.value">
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
    </div>

    <div class="grid_3" style="margin: 0">
        <div class="box" id="leftPanel">

            <div class="htabs">
                <a tab="#widgetsLeftPanel" class="htab"><?php echo $l('Widgets'); ?></a>
                <a tab="#cssEditorLeftPanel" class="htab"><?php echo $l('CSS Theme'); ?></a>
                <a tab="#widgetSettingsLeftPanel" class="htab"><?php echo $l('Settings'); ?></a>
            </div>

            <div id="widgetsLeftPanel"><?php require_once(dirname(__FILE__)."/theme_editor_tabs_widgets.tpl"); ?></div>
            <div id="cssEditorLeftPanel"><?php require_once(dirname(__FILE__)."/theme_editor_tabs_css.tpl"); ?></div>
            <div id="widgetSettingsLeftPanel"></div>
            
        </div>
    </div>

    <div class="grid_9">
            <div class="iframe-barnav">
                <!--
                <button id="iframe_backward">Back</button>
                <button id="iframe_forward">Forward</button>
                -->
                <button onclick="$('#themeVisualEditor').attr('src', $('#themeVisualEditor').attr('src'));">Refresh</button>
                <input type="url" name="iframe_url" width="600" />
                <button id="desktop_iframe_viewport">desktop</button>
                <button id="tablet_iframe_viewport">tablet</button>
                <button id="mobile_iframe_viewport">mobile</button>
                <button id="facebook_iframe_viewport">facebook</button>
            </div>
        <div class="iframe-container">
            <iframe 
            id="themeVisualEditor" 
            src="<?php echo $Url::createUrl('common/home', array(
                'store_id'=>$store_id,
                'admin_tools'=>1
            ), 'SSL', HTTP_CATALOG); ?>" 
            frameborder="no" 
            scrolling="auto" 
            sandbox="allow-same-origin allow-scripts allow-forms"
            width="1024" 
            height="768"></iframe>
        </div>
    </div>
</div>
<?php echo $footer; ?>