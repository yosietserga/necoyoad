<!doctype html>
<html class="no-js" lang="<?php echo ($l('code')) ? $l('code') : 'es'; ?>">
<head>
    <meta charset="<?php echo ($l('charset')) ? $l('charset') : 'utf-8'; ?>" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

    <title><?php echo $title; ?></title>

    <?php if (!empty($keywords)) { ?><meta name="keywords" content="<?php echo $keywords; ?>"><?php } ?>

    <?php if (!empty($description)) { ?><meta name="description" content="<?php echo $description; ?>"><?php } ?>

    <base href="<?php echo HTTP_HOME; ?>">

    <?php if (!empty($icon)) { ?><link href="<?php echo $icon; ?>" rel="icon"><?php } ?>

    <!-- <link href="https://www.necotienda.org/assets/images/data/favicon.png" rel="icon" /> -->

    <meta name="HandheldFriendly" content="true" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />

    <?php if ($css) { ?><style><?php echo $css; ?></style><?php } ?>

    <?php if (count($styles) > 0) { ?>
        <?php foreach ($styles as $style) { ?>
        <?php if (empty($style['href'])) continue; ?>
    <link rel="stylesheet" type="text/css" media="<?php echo $style['media']; ?>" href="<?php echo $style['href']; ?>" />
        <?php } ?>
    <?php } ?>

    <script>
        window.nt = {};
    <?php if (isset($_GET['token']) && !empty($_GET['token'])) { ?>
        window.nt.token = '<?php echo $_GET['token']; ?>';
        window.nt.uid = '<?php echo $this->session->get('user_id'); ?>';
    <?php } ?>
        window.nt.http_home = '<?php echo HTTP_HOME; ?>';
        window.nt.http_image = '<?php echo HTTP_IMAGE; ?>';
        window.nt.http_catalog = '<?php echo HTTP_CATALOG; ?>';
        window.nt.http_admin_image = '<?php echo str_replace('%theme%', $Config->get('config_admin_template'), HTTP_ADMIN_THEME_IMAGE); ?>';
        window.nt.route = '<?php echo $this->Route; ?>';
    </script>
    <!-- <script src="//cdnjs.cloudflare.com/ajax/libs/modernizr/2.8.1/modernizr.min.js"></script> -->
    <!-- <script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script> -->
    <script>window.Modernizr || document.write('<script src="<?php echo str_replace('%theme%', $Config->get('config_admin_template'), HTTP_ADMIN_THEME_JS); ?>vendor/modernizr.min.js"><\/script>')</script>
    <script>window.$ || document.write('<script src="<?php echo str_replace('%theme%', $Config->get('config_admin_template'), HTTP_ADMIN_THEME_JS); ?>vendor/jquery.min.js"><\/script>')</script>
    <script type="text/javascript">window.CKEDITOR_BASEPATH = '<?php echo str_replace('%theme%', $Config->get('config_admin_template'), HTTP_ADMIN_THEME_JS); ?>vendor/ckeditor/';</script>

</head>
<body>

<?php if ($logged) { ?>
    <!-- Top navigation bar -->
    <div id="topNav">
        <div class="fixed">
            <div class="wrapper">
                <a id="simple-menu" href="#sidr" style="margin:5px 10px;float:left;" onclick="if (!$.fn.sidr) { return false; }"><i class="fa fa-bars fa-2x" style="color:#fff"></i></a>
                <!--
                <a id="right-menu" href="#sidr-right" style="margin:5px 10px;float:right;"><i class="fa fa-bars fa-2x" style="color:#900"></i></a>
                -->

                <div class="userNav">
                    <ul>
                        <li class="hideOnMobile">
                            <a href="<?php echo $Url::createAdminUrl("setting/cache/deletefilecache"); ?>" title="<?php echo $l('text_delete_cache'); ?>">
                                <span><i class="fa fa-trash-o fa-2x"></i></span>
                                <span class="hideOnTablet"><?php echo $l('text_delete_cache'); ?></span>
                            </a>
                        </li>
                        <!--
                        <li>
                            <a href="<?php echo $Url::createAdminUrl("sale/order"); ?>" title="<?php echo $l('text_orders'); ?>">
                                <span><i class="fa fa-bell fa-2x"></i></span>
                                <span class="numberTop">3</span>
                            </a>
                        </li>
                        <li class="dd hideOnMobile">
                            <span><i class="fa fa-comments fa-2x"></i></span>
                        </li>
                        -->
                        <li class="dd hideOnMobile">
                            <span><i class="fa fa-shopping-cart fa-2x"></i></span>
                            <ul class="menu_body">
                                <li><a href="<?php echo HTTP_CATALOG; ?>" title="<?php echo $l('text_main_store'); ?>" target="_blank"><?php echo $l('text_main_store'); ?></a></li>
                                <?php foreach ($stores as $store) { ?>
                                <li><a href="<?php echo str_replace("www",$store['folder'],HTTP_CATALOG); ?>" target="_blank" title="Ir a la tienda"><?php echo $store['name']; ?></a></li>
                                <?php } ?>
                            </ul>
                        </li>
                        <!--
                        <li class="hideOnMobile">
                            <a href="<?php echo $Url::createAdminUrl("setting/setting"); ?>" title="<?php echo $l('tab_help'); ?>">
                               <span><i class="fa fa-life-ring fa-2x"></i></span>
                            </a>
                        </li>
                        -->
                        <li class="hideOnMobile">
                            <a href="<?php echo $Url::createAdminUrl("setting/setting"); ?>" title="<?php echo $l('text_setting'); ?>">
                               <span><i class="fa fa-cog fa-2x"></i></span>
                            </a>
                        </li>
                        <li><a href="<?php echo $Url::createAdminUrl('common/logout'); ?>" title="<?php echo $l('text_logout'); ?>">
                                <img src="<?php echo str_replace('%theme%', $Config->get('config_admin_template'), HTTP_ADMIN_THEME_IMAGE); ?>icons/topnav/logout.png" alt="<?php echo $l('text_logout'); ?>" />
                                <span class="hideOnMobile hideOnTablet"><?php echo $l('text_logout'); ?></span>
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="fix"></div>
            </div>
        </div>
    </div>
    <?php } ?>