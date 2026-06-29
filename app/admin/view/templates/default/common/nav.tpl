<?php if ($logged) { ?>
<div id="sidr" class="sidr left" style="display: none;">
    <div class="center">
        <a id="header_logo" href="<?php echo $Url::createAdminUrl('common/home'); ?>"></a>
    </div>
    
    <div class="clear"></div>
    
    <div class="center">
        <img class="avatar" src="<?php echo $avatar; ?>" alt="<?php echo $this->user->getUserName(); ?>" />
        <p><?php echo $this->user->getUserName(); ?></p>
    </div>
       
    <div class="clear"></div>
    <!--
    <div class="center">
        <div class="grid_1" style="margin:0px;margin-left:15px;">
            <i class="fa fa-envelope fa-3x"></i>
            <span class="numberTop">3</span>
        </div>
        <div class="grid_1" style="margin:0px;">
            <span class="numberTop">3</span>
            <i class="fa fa-bell fa-3x"></i>
        </div>
        <div class="grid_1" style="margin:0px;">
            <span class="numberTop">3</span>
            <i class="fa fa-shopping-cart fa-3x"></i>
        </div>
    </div>
        
    <div class="clear"></div>
    -->
    <h2><?php echo $l('tab_menu'); ?></h2>
    <ul class="menu">
        <li>
            <a href="<?php echo $Url::createAdminUrl('common/home'); ?>"><i class="fa fa-home"></i>&nbsp;&nbsp;<?php echo $l('tab_home'); ?></a>
        </li>
        <li>
            <a><i class="fa fa-shopping-cart"></i>&nbsp;&nbsp;<?php echo $l('tab_store'); ?><i class="fa fa-arrow-circle-right" style=float:right;margin-top:13px;margin-right:5px;"></i></a>
            <ul>
                <li>
                    <a href="<?php echo $Url::createAdminUrl('store/category'); ?>"><?php echo $l('text_category'); ?></a>
                </li>
                <li>
                    <a href="<?php echo $Url::createAdminUrl('store/product'); ?>"><?php echo $l('text_product'); ?></a>
                </li>
                <li>
                    <a href="<?php echo $Url::createAdminUrl('store/attribute'); ?>"><?php echo $l('Product Attributes'); ?></a>
                <li>
                    <a href="<?php echo $Url::createAdminUrl('store/manufacturer'); ?>"><?php echo $l('text_manufacturer'); ?></a>
                </li>
                <li>
                    <a href="<?php echo $Url::createAdminUrl('store/download'); ?>"><?php echo $l('text_download'); ?></a>
                </li>
                <li>
                    <a href="<?php echo $Url::createAdminUrl('store/review'); ?>"><?php echo $l('text_review'); ?></a>
                </li>
                <li>
                    <a href="<?php echo $Url::createAdminUrl('store/store'); ?>"><?php echo $l('text_shops'); ?></a>
                </li>

                <?php 
                    //TODO: use hooks to show injected menus
                    $menus = $modelExtension->getMenuTemplates('admin', 'store');
                    foreach ($menus as $tpl_file) {
                        if (file_exists($tpl_file)) {
                            include_once($tpl_file);
                        }
                    }
                ?>

            </ul>
        </li>
        <li>
            <a><i class="fa fa-desktop"></i>&nbsp;&nbsp;<?php echo $l('tab_content'); ?><i class="fa fa-arrow-circle-right" style=float:right;margin-top:13px;margin-right:5px;"></i></a>
            <ul>
                <li>
                    <a href="<?php echo $Url::createAdminUrl('content/page'); ?>"><?php echo $l('text_page'); ?></a>
                </li>
                <li>
                    <a href="<?php echo $Url::createAdminUrl('content/post_category'); ?>"><?php echo $l('text_post_category'); ?></a>
                </li>
                <li>
                    <a href="<?php echo $Url::createAdminUrl('content/post'); ?>"><?php echo $l('text_post'); ?></a>
                </li>
                <li>
                    <a href="<?php echo $Url::createAdminUrl('content/menu'); ?>"><?php echo $l('text_menu'); ?></a>
                </li>
                <li>
                    <a href="<?php echo $Url::createAdminUrl('content/banner'); ?>"><?php echo $l('text_banner'); ?></a>
                </li>
                <li>
                    <a href="<?php echo $Url::createAdminUrl('content/file'); ?>"><?php echo $l('text_filemanager'); ?></a>
                </li>

                <?php 
                    //TODO: use hooks to show injected menus
                    $menus = $modelExtension->getMenuTemplates('admin', 'contents');
                    foreach ($menus as $tpl_file) {
                        if (file_exists($tpl_file)) {
                            include_once($tpl_file);
                        }
                    }
                ?>

            </ul>
        </li>
        <li>
            <a><i class="fa fa-book"></i>&nbsp;&nbsp;<?php echo $l('tab_admon'); ?><i class="fa fa-arrow-circle-right" style=float:right;margin-top:13px;margin-right:5px;"></i></a>
            <ul>
                <!--
                <li>
                    <a href="<?php echo $Url::createAdminUrl('sale/sale'); ?>"><?php echo $l('text_sale'); ?></a>
                </li>
                -->
                <li>
                    <a href="<?php echo $Url::createAdminUrl('sale/order'); ?>"><?php echo $l('text_order'); ?></a>
                </li>
                <li>
                    <a href="<?php echo $Url::createAdminUrl('sale/payment'); ?>"><?php echo $l('text_payment'); ?></a>
                </li>
                <li>
                    <a href="<?php echo $Url::createAdminUrl('sale/coupon'); ?>"><?php echo $l('text_coupon'); ?></a>
                </li>
                <li>
                    <a href="<?php echo $Url::createAdminUrl('sale/customer'); ?>"><?php echo $l('text_customer'); ?></a>
                </li>
                <li>
                    <a href="<?php echo $Url::createAdminUrl('sale/customergroup'); ?>"><?php echo $l('text_customer_group'); ?></a>
                </li>
                <li>
                    <a href="<?php echo $Url::createAdminUrl('sale/balance'); ?>"><?php echo $l('text_balances'); ?></a>
                </li>
                <li>
                    <a href="<?php echo $Url::createAdminUrl('sale/bank'); ?>"><?php echo $l('text_bank'); ?></a>
                </li>
                <li>
                    <a href="<?php echo $Url::createAdminUrl('sale/bank_account'); ?>"><?php echo $l('text_bank_account'); ?></a>
                </li>


                <?php 
                    //TODO: use hooks to show injected menus
                    $menus = $modelExtension->getMenuTemplates('admin', 'admon');
                    foreach ($menus as $tpl_file) {
                        if (file_exists($tpl_file)) {
                            include_once($tpl_file);
                        }
                    }
                ?>

            </ul>
        </li>
        <li>
            <a><i class="fa fa-book"></i>&nbsp;&nbsp;<?php echo $l('tab_tools'); ?><i class="fa fa-arrow-circle-right" style=float:right;margin-top:13px;margin-right:5px;"></i></a>
            <ul>
                <li>
                    <a href="<?php echo $Url::createAdminUrl('tool/backup'); ?>"><?php echo $l('text_backup'); ?></a>
                </li>
                <li>
                    <a href="<?php echo $Url::createAdminUrl('tool/backup'); ?>"><?php echo $l('text_restore'); ?></a>
                </li>
                <li>
                    <a href="<?php echo $Url::createAdminUrl('extension/module'); ?>"><?php echo $l('text_module'); ?></a>
                </li>
                <li>
                    <a href="<?php echo $Url::createAdminUrl('extension/shipping'); ?>"><?php echo $l('text_shipping'); ?></a>
                </li>
                <li>
                    <a href="<?php echo $Url::createAdminUrl('extension/payment'); ?>"><?php echo $l('text_payment'); ?></a>
                </li>
                <li>
                    <a href="<?php echo $Url::createAdminUrl('extension/total'); ?>"><?php echo $l('text_total'); ?></a>
                </li>


                <?php 
                    //TODO: use hooks to show injected menus
                    $menus = $modelExtension->getMenuTemplates('admin', 'tools');
                    foreach ($menus as $tpl_file) {
                        if (file_exists($tpl_file)) {
                            include_once($tpl_file);
                        }
                    }
                ?>
            </ul>
        </li>
        <li>
            <a><i class="fa fa-bar-chart-o"></i>&nbsp;&nbsp;<?php echo $l('tab_report'); ?><i class="fa fa-arrow-circle-right" style=float:right;margin-top:13px;margin-right:5px;"></i></a>
            <ul>
                <li>
                    <a href="<?php echo $Url::createAdminUrl('store/product/see'); ?>"><?php echo $l('text_product'); ?></a>
                </li>
                <li>
                    <a href="<?php echo $Url::createAdminUrl('sale/customer/see'); ?>"><?php echo $l('text_customer'); ?></a>
                </li>
                <li>
                    <a href="<?php echo $Url::createAdminUrl('store/category/see'); ?>"><?php echo $l('text_category'); ?></a>
                </li>
                <li>
                    <a href="<?php echo $Url::createAdminUrl('store/manufacturer/see'); ?>"><?php echo $l('text_manufacturer'); ?></a>
                </li>
                <li>
                    <a href="<?php echo $Url::createAdminUrl('content/page/see'); ?>"><?php echo $l('text_page'); ?></a>
                </li>
                <li>
                    <a href="<?php echo $Url::createAdminUrl('content/post/see'); ?>"><?php echo $l('text_post'); ?></a>
                </li>
                <li>
                    <a href="<?php echo $Url::createAdminUrl('content/post_category/see'); ?>"><?php echo $l('text_post_category'); ?></a>
                </li>

                <?php 
                    $menus = $modelExtension->getMenuTemplates('admin', 'reports');
                    foreach ($menus as $tpl_file) {
                        if (file_exists($tpl_file)) {
                            include_once($tpl_file);
                        }
                    }
                ?>
            </ul>
        </li>
        <li>
            <a><i class="fa fa-book"></i>&nbsp;&nbsp;<?php echo $l('tab_marketing'); ?><i class="fa fa-arrow-circle-right" style=float:right;margin-top:13px;margin-right:5px;"></i></a>
            <ul>
                <li>
                    <a href="<?php echo $Url::createAdminUrl('marketing/contact'); ?>"><?php echo $l('text_contacts'); ?></a>
                </li>
                <li>
                    <a href="<?php echo $Url::createAdminUrl('marketing/list'); ?>"><?php echo $l('text_contacts_lists'); ?></a>
                </li>
                <li>
                    <a href="<?php echo $Url::createAdminUrl('marketing/newsletter'); ?>"><?php echo $l('text_newsletters'); ?></a>
                </li>
                <li>
                    <a href="<?php echo $Url::createAdminUrl('marketing/campaign'); ?>"><?php echo $l('text_email_campaigns'); ?></a>
                </li>
                <li>
                    <a href="<?php echo $Url::createAdminUrl('marketing/message'); ?>"><?php echo $l('text_email_associations'); ?></a>
                </li>
                <li>
                    <a href="<?php echo $Url::createAdminUrl('marketing/mailserver'); ?>"><?php echo $l('SMTP Mail Servers'); ?></a>
                </li>

                <?php 
                    $menus = $modelExtension->getMenuTemplates('admin', 'marketing');
                    foreach ($menus as $tpl_file) {
                        if (file_exists($tpl_file)) {
                            include_once($tpl_file);
                        }
                    }
                ?>
            </ul>
        </li>
        <li>
            <a><i class="fa fa-cloud-upload"></i>&nbsp;&nbsp;CPanel<i class="fa fa-arrow-circle-right" style=float:right;margin-top:13px;margin-right:5px;"></i></a>
            <ul>
                <li>
                    <a href="<?php echo $Url::createAdminUrl('cpanel/email'); ?>"><?php echo $l('Email Accounts'); ?></a>
                </li>
            </ul>
        </li>
        <li>
            <a><i class="fa fa-book"></i>&nbsp;&nbsp;<?php echo $l('tab_style'); ?><i class="fa fa-arrow-circle-right" style=float:right;margin-top:13px;margin-right:5px;"></i></a>
            <ul>
                <li>
                    <a href="<?php echo $Url::createAdminUrl('style/views'); ?>"><?php echo $l('text_views'); ?></a>
                </li>
                <li>
                    <a href="<?php echo $Url::createAdminUrl('style/theme'); ?>"><?php echo $l('text_themes'); ?></a>
                </li>
                <li>
                    <a href="<?php echo $Url::createAdminUrl('style/template'); ?>"><?php echo $l('text_templates'); ?></a>
                </li>
                <li>
                    <a href="<?php echo $Url::createAdminUrl('style/editor'); ?>"><?php echo $l('text_html_editor'); ?></a>
                </li>
                <li>
                    <a href="<?php echo $Url::createAdminUrl('style/widget'); ?>"><?php echo $l('text_widgets'); ?></a>
                </li>


                <?php 
                    $menus = $modelExtension->getMenuTemplates('admin', 'style');
                    foreach ($menus as $tpl_file) {
                        if (file_exists($tpl_file)) {
                            include_once($tpl_file);
                        }
                    }
                ?>
            </ul>
        </li>

                <?php 
                    $menus = $modelExtension->getMenuTemplates('admin', 'custom');
                    foreach ($menus as $tpl_file) {
                        if (file_exists($tpl_file)) {
                            include_once($tpl_file);
                        }
                    }
                ?>

        <li>
            <a><i class="fa fa-book"></i>&nbsp;&nbsp;<?php echo $l('tab_system'); ?><i class="fa fa-arrow-circle-right" style=float:right;margin-top:13px;margin-right:5px;"></i></a>
            <ul>
                <li>
                    <a href="<?php echo $Url::createAdminUrl('setting/setting'); ?>"><?php echo $l('text_setting'); ?></a>
                </li>
                <li>
                    <a href="<?php echo $Url::createAdminUrl('user/user'); ?>"><?php echo $l('text_user'); ?></a>
                </li>
                <li>
                    <a href="<?php echo $Url::createAdminUrl('user/user_permission'); ?>"><?php echo $l('text_user_group'); ?></a>
                </li>
                <li>
                    <a href="<?php echo $Url::createAdminUrl('localisation/language'); ?>"><?php echo $l('text_language'); ?></a>
                </li>
                <li>
                    <a href="<?php echo $Url::createAdminUrl('localisation/currency'); ?>"><?php echo $l('text_currency'); ?></a>
                </li>
                <li>
                    <a href="<?php echo $Url::createAdminUrl('localisation/order_status'); ?>"><?php echo $l('text_order_status'); ?></a>
                </li>
                <li>
                    <a href="<?php echo $Url::createAdminUrl('localisation/stock_status'); ?>"><?php echo $l('text_stock_status'); ?></a>
                </li>
                <li>
                    <a href="<?php echo $Url::createAdminUrl('localisation/tax_class'); ?>"><?php echo $l('text_tax_class'); ?></a>
                </li>
                <li>
                    <a href="<?php echo $Url::createAdminUrl('localisation/geo_zone'); ?>"><?php echo $l('text_geo_zone'); ?></a>
                </li>
                <li>
                    <a href="<?php echo $Url::createAdminUrl('localisation/weight_class'); ?>"><?php echo $l('text_weight_class'); ?></a>
                </li>
                <li>
                    <a href="<?php echo $Url::createAdminUrl('localisation/length_class'); ?>"><?php echo $l('text_length_class'); ?></a>
                </li>
                <li>
                    <a href="<?php echo $Url::createAdminUrl('tool/update'); ?>"><?php echo $l('Updates'); ?></a>
                </li>

                <?php 
                    $menus = $modelExtension->getMenuTemplates('admin', 'system');
                    foreach ($menus as $tpl_file) {
                        if (file_exists($tpl_file)) {
                            include_once($tpl_file);
                        }
                    }
                ?>
            </ul>
        </li>
    </ul>
</div>


<div id="sidr-right" class="sidr right" style="display: none;">
    <div class="center">
        <a id="header_logo" href="<?php echo $Url::createAdminUrl('common/home'); ?>"></a>
    </div>
    
    <div class="clear"></div>
    
    <div class="center">
        <img class="avatar" src="<?php echo HTTP_IMAGE; ?>data/profiles/avatar.png" alt="Me" />
        <p><?php echo $this->user->getUserName(); ?></p>
    </div>
       
    <div class="clear"></div>
    
    <div class="center">
        <div class="grid_1" style="margin:0px;margin-left:15px;">
            <i class="fa fa-envelope fa-3x"></i>
            <span class="numberTop">3</span>
        </div>
        <div class="grid_1" style="margin:0px;">
            <span class="numberTop">3</span>
            <i class="fa fa-bell fa-3x"></i>
        </div>
        <div class="grid_1" style="margin:0px;">
            <span class="numberTop">3</span>
            <i class="fa fa-shopping-cart fa-3x"></i>
        </div>
    </div>
        
    <div class="clear"></div>

    <h2><?php echo $l('tab_menu'); ?></h2>
    <ul class="menu">
        <li>
            <a href="<?php echo $Url::createAdminUrl('common/home'); ?>"><i class="fa fa-home"></i>&nbsp;&nbsp;<?php echo $l('tab_home'); ?></a>
        </li>
        <li>
            <a><i class="fa fa-shopping-cart"></i>&nbsp;&nbsp;<?php echo $l('tab_store'); ?><i class="fa fa-arrow-circle-right" style=float:right;margin-top:13px;margin-right:5px;"></i></a>
            <ul>
                <li>
                    <a href="<?php echo $Url::createAdminUrl('store/category'); ?>"><?php echo $l('text_category'); ?></a>
                </li>
                <li>
                    <a href="<?php echo $Url::createAdminUrl('store/product'); ?>"><?php echo $l('text_product'); ?></a>
                </li>
                <li>
                    <a href="<?php echo $Url::createAdminUrl('store/attribute'); ?>"><?php echo $l('Product Attributes'); ?></a>
                </li>
                <li>
                    <a href="<?php echo $Url::createAdminUrl('store/manufacturer'); ?>"><?php echo $l('text_manufacturer'); ?></a>
                </li>
                <li>
                    <a href="<?php echo $Url::createAdminUrl('store/download'); ?>"><?php echo $l('text_download'); ?></a>
                </li>
                <li>
                    <a href="<?php echo $Url::createAdminUrl('store/review'); ?>"><?php echo $l('text_review'); ?></a>
                </li>
                <li>
                    <a href="<?php echo $Url::createAdminUrl('store/store'); ?>"><?php echo $l('text_shops'); ?></a>
                </li>
            </ul>
        </li>
        <li>
            <a><i class="fa fa-desktop"></i>&nbsp;&nbsp;<?php echo $l('tab_content'); ?><i class="fa fa-arrow-circle-right" style=float:right;margin-top:13px;margin-right:5px;"></i></a>
            <ul>
                <li>
                    <a href="<?php echo $Url::createAdminUrl('content/page'); ?>"><?php echo $l('text_page'); ?></a>
                </li>
                <li>
                    <a href="<?php echo $Url::createAdminUrl('content/post_category'); ?>"><?php echo $l('text_post_category'); ?></a>
                </li>
                <li>
                    <a href="<?php echo $Url::createAdminUrl('content/post'); ?>"><?php echo $l('text_post'); ?></a>
                </li>
                <li>
                    <a href="<?php echo $Url::createAdminUrl('content/menu'); ?>"><?php echo $l('text_menu'); ?></a>
                </li>
                <li>
                    <a href="<?php echo $Url::createAdminUrl('content/banner'); ?>"><?php echo $l('text_banner'); ?></a>
                </li>
                <li>
                    <a href="<?php echo $Url::createAdminUrl('content/file'); ?>"><?php echo $l('text_filemanager'); ?></a>
                </li>
            </ul>
        </li>
        <li>
            <a><i class="fa fa-book"></i>&nbsp;&nbsp;<?php echo $l('tab_admon'); ?><i class="fa fa-arrow-circle-right" style=float:right;margin-top:13px;margin-right:5px;"></i></a>
            <ul>
                <li>
                    <a href="<?php echo $Url::createAdminUrl('sale/sale'); ?>"><?php echo $l('text_sale'); ?></a>
                </li>
                <li>
                    <a href="<?php echo $Url::createAdminUrl('sale/order'); ?>"><?php echo $l('text_order'); ?></a>
                </li>
                <li>
                    <a href="<?php echo $Url::createAdminUrl('sale/payment'); ?>"><?php echo $l('text_payment'); ?></a>
                </li>
                <li>
                    <a href="<?php echo $Url::createAdminUrl('sale/coupon'); ?>"><?php echo $l('text_coupon'); ?></a>
                </li>
                <li>
                    <a href="<?php echo $Url::createAdminUrl('sale/customer'); ?>"><?php echo $l('text_customer'); ?></a>
                </li>
                <li>
                    <a href="<?php echo $Url::createAdminUrl('sale/customergroup'); ?>"><?php echo $l('text_customergroup'); ?></a>
                </li>
                <li>
                    <a href="<?php echo $Url::createAdminUrl('sale/balance'); ?>"><?php echo $l('text_balances'); ?></a>
                </li>
                <li>
                    <a href="<?php echo $Url::createAdminUrl('sale/bank'); ?>"><?php echo $l('text_bank'); ?></a>
                </li>
                <li>
                    <a href="<?php echo $Url::createAdminUrl('sale/bank_account'); ?>"><?php echo $l('text_bank_account'); ?></a>
                </li>
            </ul>
        </li>
        <li>
            <a><i class="fa fa-book"></i>&nbsp;&nbsp;<?php echo $l('tab_tools'); ?><i class="fa fa-arrow-circle-right" style=float:right;margin-top:13px;margin-right:5px;"></i></a>
            <ul>
                <li>
                    <a href="<?php echo $Url::createAdminUrl('tool/backup'); ?>"><?php echo $l('text_backup'); ?></a>
                </li>
                <li>
                    <a href="<?php echo $Url::createAdminUrl('tool/backup'); ?>"><?php echo $l('text_restore'); ?></a>
                </li>
                <li>
                    <a href="<?php echo $Url::createAdminUrl('extension/module'); ?>"><?php echo $l('text_module'); ?></a>
                </li>
                <li>
                    <a href="<?php echo $Url::createAdminUrl('extension/shipping'); ?>"><?php echo $l('text_shipping'); ?></a>
                </li>
                <li>
                    <a href="<?php echo $Url::createAdminUrl('extension/payment'); ?>"><?php echo $l('text_payment'); ?></a>
                </li>
                <li>
                    <a href="<?php echo $Url::createAdminUrl('extension/total'); ?>"><?php echo $l('text_total'); ?></a>
                </li>
            </ul>
        </li>
        <li>
            <a><i class="fa fa-bar-chart-o"></i>&nbsp;&nbsp;<?php echo $l('tab_report'); ?><i class="fa fa-arrow-circle-right" style=float:right;margin-top:13px;margin-right:5px;"></i></a>
            <ul>
                <li>
                    <a href="<?php echo $Url::createAdminUrl('store/product/see'); ?>"><?php echo $l('text_product'); ?></a>
                </li>
                <li>
                    <a href="<?php echo $Url::createAdminUrl('sale/customer/see'); ?>"><?php echo $l('text_customer'); ?></a>
                </li>
                <li>
                    <a href="<?php echo $Url::createAdminUrl('store/category/see'); ?>"><?php echo $l('text_category'); ?></a>
                </li>
                <li>
                    <a href="<?php echo $Url::createAdminUrl('store/manufacturer/see'); ?>"><?php echo $l('text_manufacturer'); ?></a>
                </li>
                <li>
                    <a href="<?php echo $Url::createAdminUrl('content/page/see'); ?>"><?php echo $l('text_page'); ?></a>
                </li>
                <li>
                    <a href="<?php echo $Url::createAdminUrl('content/post/see'); ?>"><?php echo $l('text_post'); ?></a>
                </li>
                <li>
                    <a href="<?php echo $Url::createAdminUrl('content/post_category/see'); ?>"><?php echo $l('text_post_category'); ?></a>
                </li>
            </ul>
        </li>
        <li>
            <a><i class="fa fa-book"></i>&nbsp;&nbsp;<?php echo $l('tab_marketing'); ?><i class="fa fa-arrow-circle-right" style=float:right;margin-top:13px;margin-right:5px;"></i></a>
            <ul>
                <li>
                    <a href="<?php echo $Url::createAdminUrl('marketing/contact'); ?>"><?php echo $l('text_contacts'); ?></a>
                </li>
                <li>
                    <a href="<?php echo $Url::createAdminUrl('marketing/list'); ?>"><?php echo $l('text_contacts_lists'); ?></a>
                </li>
                <li>
                    <a href="<?php echo $Url::createAdminUrl('marketing/newsletter'); ?>"><?php echo $l('text_newsletters'); ?></a>
                </li>
                <li>
                    <a href="<?php echo $Url::createAdminUrl('marketing/campaign'); ?>"><?php echo $l('text_email_campaigns'); ?></a>
                </li>
                <li>
                    <a href="<?php echo $Url::createAdminUrl('marketing/message'); ?>"><?php echo $l('text_email_associations'); ?></a>
                </li>
            </ul>
        </li>
        <li>
            <a><i class="fa fa-book"></i>&nbsp;&nbsp;<?php echo $l('tab_style'); ?><i class="fa fa-arrow-circle-right" style=float:right;margin-top:13px;margin-right:5px;"></i></a>
            <ul>
                <li>
                    <a href="<?php echo $Url::createAdminUrl('style/views'); ?>"><?php echo $l('text_views'); ?></a>
                </li>
                <li>
                    <a href="<?php echo $Url::createAdminUrl('style/theme'); ?>"><?php echo $l('text_themes'); ?></a>
                </li>
                <li>
                    <a href="<?php echo $Url::createAdminUrl('style/template'); ?>"><?php echo $l('text_templates'); ?></a>
                </li>
                <li>
                    <a href="<?php echo $Url::createAdminUrl('style/editor'); ?>"><?php echo $l('text_html_editor'); ?></a>
                </li>
                <li>
                    <a href="<?php echo $Url::createAdminUrl('style/widget'); ?>"><?php echo $l('text_widgets'); ?></a>
                </li>
            </ul>
        </li>
        <li>
            <a><i class="fa fa-book"></i>&nbsp;&nbsp;<?php echo $l('tab_system'); ?><i class="fa fa-arrow-circle-right" style=float:right;margin-top:13px;margin-right:5px;"></i></a>
            <ul>
                <li>
                    <a href="<?php echo $Url::createAdminUrl('setting/setting'); ?>"><?php echo $l('text_setting'); ?></a>
                </li>
                <li>
                    <a href="<?php echo $Url::createAdminUrl('user/user'); ?>"><?php echo $l('text_user'); ?></a>
                </li>
                <li>
                    <a href="<?php echo $Url::createAdminUrl('user/user_permission'); ?>"><?php echo $l('text_user_group'); ?></a>
                </li>
                <li>
                    <a href="<?php echo $Url::createAdminUrl('localisation/language'); ?>"><?php echo $l('text_language'); ?></a>
                </li>
                <li>
                    <a href="<?php echo $Url::createAdminUrl('localisation/currency'); ?>"><?php echo $l('text_currency'); ?></a>
                </li>
                <li>
                    <a href="<?php echo $Url::createAdminUrl('localisation/order_status'); ?>"><?php echo $l('text_order_status'); ?></a>
                </li>
                <li>
                    <a href="<?php echo $Url::createAdminUrl('localisation/stock_status'); ?>"><?php echo $l('text_stock_status'); ?></a>
                </li>
                <li>
                    <a href="<?php echo $Url::createAdminUrl('localisation/tax_class'); ?>"><?php echo $l('text_tax_class'); ?></a>
                </li>
                <li>
                    <a href="<?php echo $Url::createAdminUrl('localisation/geo_zone'); ?>"><?php echo $l('text_geo_zone'); ?></a>
                </li>
                <li>
                    <a href="<?php echo $Url::createAdminUrl('localisation/weight_class'); ?>"><?php echo $l('text_weight_class'); ?></a>
                </li>
                <li>
                    <a href="<?php echo $Url::createAdminUrl('localisation/length_class'); ?>"><?php echo $l('text_length_class'); ?></a>
                </li>
                <li>
                    <a href="<?php echo $Url::createAdminUrl('setting/cache'); ?>"><?php echo $l('Manage Cache'); ?></a>
                </li>
                <li>
                    <a href="<?php echo $Url::createAdminUrl('tool/update'); ?>"><?php echo $l('Updates'); ?></a>
                </li>
            </ul>
        </li>
    </ul>
</div>

<script>
$(document).ready(function() {
    $('#simple-menu').sidr();
    $('#sidr ul ul').slideUp();
    $('#sidr > ul > li a').on('click', function(e){
        $(this).parent('li').find('ul').slideToggle();
    }).children().click(function(e) {
        e.stopPropagation();
    });
    
    $('#right-menu').sidr({
      name: 'sidr-right',
      side: 'right'
    });
});
</script>

<div class="clear"></div>
<?php } ?>