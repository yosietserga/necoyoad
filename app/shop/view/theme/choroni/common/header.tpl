<?php $tpl = is_dir(DIR_TEMPLATE. $this->config->get('config_template') ."/shared") ? $this->config->get('config_template') : "choroni"; ?>
<!doctype html>
<head<?php if (isset($headAttributes)) { echo $headAttributes; } ?>>

    <?php if (isset($opengraph)) {
    foreach ($opengraph as $k=>$v) {
        if (empty($v)) continue; ?>
    <meta property="<?php echo $k; ?>" content="<?php echo $v; ?>" />
    <?php } } ?>

    <base href="<?php echo HTTP_HOME; ?>">

    <meta charset="UTF-8" />
    <title><?php echo $title; ?></title>
    <?php if ($keywords) { ?>
    <meta name="keywords" content="<?php echo $keywords; ?>" />
    <?php } ?>

    <meta name="author" content="Necoyoad">

    <?php if ($description) { ?>
    <meta name="description" content="<?php echo $description; ?>" />
    <?php } ?>

    <!-- Mobile viewport optimized: h5bp.com/viewport -->
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <?php if ($icon) { ?>
        <link href="<?php echo $icon; ?>" rel="icon" />
    <?php } ?>

    <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">

    <?php if (count($styles) > 0) {
    foreach ($styles as $style) {

    if (empty($style['href'])) continue; ?>
    <link href="<?php echo $style['href']; ?>" rel='stylesheet' type='text/css' media="<?php echo $style['media']; ?>">
    <?php } } ?>

    <?php if ($css) { ?><style><?php echo $css; ?></style><?php } ?>

    <?php include_once(DIR_TEMPLATE. $tpl . "/shared/fragment/header-start.tpl"); ?>

    <script>
        window.nt = {};
        window.nt.sid = '<?php echo STORE_ID; ?>';
        window.nt.http_home = '<?php echo HTTP_HOME; ?>';
        window.nt.http_image = '<?php echo HTTP_IMAGE; ?>';
        window.nt.http_theme_image = '<?php echo str_replace('%theme%', $Config->get('config_template'), HTTP_THEME_IMAGE); ?>';
        window.nt.http_theme_js = '<?php echo str_replace('%theme%', $Config->get('config_template'), HTTP_THEME_JS); ?>';
        window.nt.route = '<?php echo $this->Route; ?>';

        <?php if ($is_admin || $_GET['theme_id']) { ?>
        window.nt.uid = '<?php echo $this->session->get('user_id'); ?>';
        window.nt.token = '<?php echo $this->session->get('ukey'); ?>';

        window.nt.http_admin = '<?php echo HTTP_ADMIN; ?>';
        window.nt.http_admin_image = window.nt.http_admin +'images/';
        window.nt.http_admin_theme = window.nt.http_admin +'templates/<?php echo $Config->get('config_admin_template') ? $Config->get('config_admin_template') : 'default'; ?>/';
        window.nt.http_admin_theme_js = window.nt.http_admin_theme +'js/';
        window.nt.http_admin_theme_css = window.nt.http_admin_theme +'css/';
        window.nt.http_admin_theme_images = window.nt.http_admin_theme +'images/';

        window.nt.url_widgets_load = '<?php echo isset($url_widgets_load) && !empty($url_widgets_load) ? $url_widgets_load : ""; ?>';
        
        window.nt.url_widgets_save = '<?php echo isset($url_widgets_save) && !empty($url_widgets_save) ? $url_widgets_save : ""; ?>';
        window.nt.url_widgets_savecol = '<?php echo isset($url_widgets_savecol) && !empty($url_widgets_savecol) ? $url_widgets_savecol : ""; ?>';
        window.nt.url_widgets_saverow = '<?php echo isset($url_widgets_saverow) && !empty($url_widgets_saverow) ? $url_widgets_saverow : ""; ?>';

        window.nt.url_widgets_sortable = '<?php echo isset($url_widgets_sortable) && !empty($url_widgets_sortable) ? $url_widgets_sortable : ""; ?>';
        window.nt.url_widgets_sortrow = '<?php echo isset($url_widgets_sortrow) && !empty($url_widgets_sortrow) ? $url_widgets_sortrow : ""; ?>';
        window.nt.url_widgets_sortcol = '<?php echo isset($url_widgets_sortcol) && !empty($url_widgets_sortcol) ? $url_widgets_sortcol : ""; ?>';

        window.nt.url_widgets_delete = '<?php echo isset($url_widgets_delete) && !empty($url_widgets_delete) ? $url_widgets_delete : ""; ?>';
        window.nt.url_widgets_deletecolumn = '<?php echo isset($url_widgets_deletecolumn) && !empty($url_widgets_deletecolumn) ? $url_widgets_deletecolumn : ""; ?>';
        window.nt.url_widgets_deleterow = '<?php echo isset($url_widgets_deleterow) && !empty($url_widgets_deleterow) ? $url_widgets_deleterow : "";; ?>';
        <?php } ?>
    </script>
</head>

<body nt-editable="1">

<?php //if ($is_admin) { require_once('admin/admin.tpl'); } ?>

<!--mainContainer -->
<div id="mainContainer" class="container" nt-editable="1"><!--opening tag for <div id="mainContainer"> in footer.tpl-->

    <!--headerContainer -->
    <div id="headerContainer" nt-editable="1">

        <!--header -->
        <div id="header" nt-editable>
            <?php $position = 'header'; ?>
            <?php include(DIR_TEMPLATE. $tpl ."/shared/widgets-rows.tpl");?>
        </div>
        <!--/header -->

    <!--/headerContainer -->
    </div>
