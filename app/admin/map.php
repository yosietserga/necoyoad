<?php

define('UPDATE_STATUS_PACKAGE','stable');
define('NECOTIENDA_API_URL','https://www.necotienda.org/api/');

$registry->set('session', $session);

$loader->auto('user');
$loader->auto('url');
$registry->set('load', $loader);
$registry->set('config', $config);
$registry->set('db', $db);
$registry->set('request', $request);
$registry->set('response', $response); 
$registry->set('cache', new Cache());
$registry->set('document', $document = new Document());
$registry->set('language', $language);
$registry->set('user', new User($registry));

// for background color when it resizes images
if (!defined('IMAGE_BG_COLOR_R')) {
    $config->get('config_image_bg_color_r') ?
        define('IMAGE_BG_COLOR_R', $config->get('config_image_bg_color_r')) : define('IMAGE_BG_COLOR_R', 255);
}
if (!defined('IMAGE_BG_COLOR_G')) {
    $config->get('config_image_bg_color_g') ?
        define('IMAGE_BG_COLOR_G', $config->get('config_image_bg_color_g')) : define('IMAGE_BG_COLOR_G', 255);
}
if (!defined('IMAGE_BG_COLOR_B')) {
    $config->get('config_image_bg_color_b') ?
        define('IMAGE_BG_COLOR_B', $config->get('config_image_bg_color_b')) : define('IMAGE_BG_COLOR_B', 255);
}

$route = strtolower($request->getQuery('r')??"");
switch ($route) {
    case 'common/home':
        //Languages
        $language->load('common/home');
        //Models
        $loader->auto('sale/customer');
    	$loader->auto('sale/order');
    	$loader->auto('store/product');
    	$loader->auto('store/review');
        //Libs
        $loader->auto('currency');
        //Set
        $registry->set('currency', new Currency($registry));
        break;
    case 'common/home/chart':
        $language->load('common/home');
        $loader->auto('currency');
        $loader->auto('json');
        break;
    case 'common/filemanager/uploader':
    case 'common/filemanager/files':
        $loader->auto('image');
        $loader->auto('upload');
        $loader->auto('json');
        $registry->set('upload', new Upload($registry));
        break;
    case 'store/category':
    case 'store/category/grid':
    case 'store/category/delete':
        $language->load('store/category');
        $loader->auto('store/category');
        $loader->auto('pagination');
        break;
    case 'store/category/insert':
    case 'store/category/update':
        $language->load('store/category');
        $loader->auto('store/category');
        $loader->auto('store/store');
        $loader->auto('localisation/language');
        $loader->auto('image');
        $document->addStyle(HTTP_CSS . "fancybox/jquery.fancybox.css", $rel = 'stylesheet', $media = 'screen');
        $document->addScript(HTTP_JS . "jquery/jquery.mousewheel-3.0.6.pack.js");
        $document->addScript(HTTP_JS . "jquery/fancybox/jquery.fancybox.pack.js");
        break;
    case 'store/category/products':
        $language->load('store/category');
        $loader->auto('store/category');
        $loader->auto('store/product');
        $loader->auto('image');
        break;
    case 'store/attribute':
    case 'store/attribute/grid':
    case 'store/attribute/delete':
        $language->load('store/attribute');
        $loader->auto('store/attribute');
        $loader->auto('pagination');
        break;
    case 'store/attribute/insert':
    case 'store/attribute/update':
        $language->load('store/attribute');
        $loader->auto('store/attribute');
        $loader->auto('store/store');
        $loader->auto('store/category');
        break;
    case 'store/download':
    case 'store/download/grid':
    case 'store/download/delete':
        $language->load('store/download');
        $loader->auto('store/download');
        $loader->auto('pagination');
        break;
    case 'store/download/insert':
    case 'store/download/update':
        $language->load('store/download');
        $loader->auto('store/download');
        $loader->auto('store/store');
        $loader->auto('localisation/language');
        break;
    case 'store/store':
    case 'store/store/grid':
    case 'store/store/delete':
        $language->load('store/store');
        $loader->auto('store/store');
        $loader->auto('pagination');
        break;
    case 'store/store/insert':
    case 'store/store/update':
        $language->load('store/store');
        $loader->auto('store/store');
        $loader->auto('localisation/language');
        $loader->auto('localisation/country');
        $loader->auto('localisation/currency');
        $loader->auto('content/page');
        $loader->auto('sale/customergroup');
        $loader->auto('localisation/orderstatus');
        $loader->auto('localisation/stockstatus');
        $loader->auto('image');
        break;
    case 'store/manufacturer':
    case 'store/manufacturer/grid':
    case 'store/manufacturer/delete':
        $language->load('store/manufacturer');
        $loader->auto('store/manufacturer');
        $loader->auto('pagination');
        break;
    case 'store/manufacturer/insert':
    case 'store/manufacturer/update':
        $language->load('store/manufacturer');
        $loader->auto('store/manufacturer');
        $loader->auto('store/store');
        $loader->auto('localisation/language');
        $loader->auto('image');
        break;
    case 'store/product':
    case 'store/product/grid':
    case 'store/product/delete':
    case 'store/product/copy':
        $language->load('store/product');
        $loader->auto('store/product');
        $loader->auto('image');
        $loader->auto('pagination');
        break;
    case 'store/product/see':
    case 'store/product/seeData':
        $language->load('store/product');
        $loader->auto('store/product');
        $loader->auto('image');
        $loader->auto('url');
        break;
    case 'store/product/insert':
    case 'store/product/update':
        $language->load('store/product');
        $loader->auto('store/product');
        $loader->auto('store/manufacturer');
		$loader->auto('store/download');
		$loader->auto('store/category');
        $loader->auto('store/store');
		$loader->auto('localisation/stockstatus');
		$loader->auto('localisation/taxclass');
		$loader->auto('localisation/weightclass');
		$loader->auto('localisation/lengthclass');
        $loader->auto('localisation/language');
		$loader->auto('sale/customergroup');
        $loader->auto('image');
        break;
    case 'store/review':
    case 'store/review/grid':
    case 'store/review/delete':
        $language->load('store/review');
        $loader->auto('store/review');
        $loader->auto('pagination');
        break;
    case 'store/review/insert':
    case 'store/review/update':
        $language->load('store/review');
        $loader->auto('store/review');
        $loader->auto('store/product');
        $loader->auto('store/category');
        break;
    case 'content/page':
    case 'content/page/grid':
    case 'content/page/delete':
        $language->load('content/page');
        $loader->auto('content/page');
        $loader->auto('pagination');
        break;
    case 'content/page/insert':
    case 'content/page/update':
        $language->load('content/page');
        $loader->auto('content/page');
        $loader->auto('sale/customergroup');
        $loader->auto('store/store');
        $loader->auto('localisation/language');
        break;
    case 'content/banner':
    case 'content/banner/grid':
    case 'content/banner/delete':
        $language->load('content/banner');
        $loader->auto('content/banner');
        $loader->auto('pagination');
        $loader->auto('image');
        $loader->auto('url');
        break;
    case 'content/banner/insert':
    case 'content/banner/update':
        $language->load('content/banner');
        $loader->auto('content/banner');
        $loader->auto('store/product');
        $loader->auto('store/category');
		$loader->auto('store/store');
        $loader->auto('localisation/language');
        break;
    case 'content/menu':
    case 'content/menu/grid':
    case 'content/menu/delete':
        $language->load('content/menu');
        $loader->auto('content/menu');
        $loader->auto('pagination');
        break;
    case 'content/menu/insert':
    case 'content/menu/update':
        $language->load('content/menu');
        $loader->auto('url');
        $loader->auto('localisation/language');
        $loader->auto('store/store');
        $loader->auto('content/post_category');
        $loader->auto('store/category');
        $loader->auto('store/manufacturer');
        $loader->auto('content/menu');
        $loader->auto('content/page');
        break;
    case 'content/post_category':
    case 'content/post_category/grid':
    case 'content/post_category/delete':
        $language->load('content/post_category');
        $loader->auto('content/post_category');
        $loader->auto('pagination');
        break;
    case 'content/post_category/insert':
    case 'content/post_category/update':
        $language->load('content/post_category');
        $loader->auto('content/post_category');
        $loader->auto('store/store');
        $loader->auto('localisation/language');
        $loader->auto('image');
        break;
    case 'store/post_category/posts':
        $loader->auto('content/post');
        $loader->auto('content/post_category');
        $loader->auto('url');
        $loader->auto('image');
        break;
    case 'content/post':
    case 'content/post/grid':
    case 'content/post/delete':
        $language->load('content/post');
        $loader->auto('content/post');
        $loader->auto('pagination');
        $loader->auto('url');
        break;
    case 'content/post/insert':
    case 'content/post/update':
        $language->load('content/post');
        $loader->auto('content/post_category');
        $loader->auto('content/post');
        $loader->auto('store/store');
        $loader->auto('sale/customergroup');
        $loader->auto('localisation/language');
        $loader->auto('url');
        $loader->auto('image');
        break;
    case 'extension/module':
    case 'extension/module/grid':
    case 'extension/payment':
    case 'extension/payment/grid':
    case 'extension/feed':
    case 'extension/total':
    case 'extension/total/grid':
    case 'extension/shipping':
    case 'extension/shipping/grid':
    case 'extension/module/install':
    case 'extension/payment/install':
    case 'extension/feed/install':
    case 'extension/total/install':
    case 'extension/shipping/install':
        $loader->auto('setting/extension');
        break;
    case 'extension/module/uninstall':
    case 'extension/payment/uninstall':
    case 'extension/feed/uninstall':
    case 'extension/total/uninstall':
    case 'extension/shipping/uninstall':
        $loader->auto('setting/extension');
        $loader->auto('setting/setting');
        break;
    case 'localisation/order_status':
    case 'localisation/order_status/grid':
    case 'localisation/order_status/delete':
        $language->load('localisation/order_status');
        $loader->auto('localisation/orderstatus');
        $loader->auto('pagination');
        break;
    case 'localisation/order_status/insert':
    case 'localisation/order_status/update':
        $language->load('localisation/order_status');
        $loader->auto('localisation/orderstatus');
		$loader->auto('localisation/language');
        break;
    case 'localisation/order_payment_status':
    case 'localisation/order_payment_status/grid':
    case 'localisation/order_payment_status/delete':
        $language->load('localisation/order_payment_status');
        $loader->auto('localisation/orderpaymentstatus');
        $loader->auto('pagination');
        break;
    case 'localisation/order_payment_status/insert':
    case 'localisation/order_payment_status/update':
        $language->load('localisation/order_payment_status');
        $loader->auto('localisation/orderpaymentstatus');
		$loader->auto('localisation/language');
        break;
    case 'localisation/stock_status':
    case 'localisation/stock_status/grid':
    case 'localisation/stock_status/delete':
        $language->load('localisation/stock_status');
        $loader->auto('localisation/stockstatus');
        $loader->auto('pagination');
        break;
    case 'localisation/stock_status/insert':
    case 'localisation/stock_status/update':
        $language->load('localisation/stock_status');
        $loader->auto('localisation/stockstatus');
		$loader->auto('localisation/language');
        break;
    case 'localisation/language':
    case 'localisation/language/grid':
    case 'localisation/language/delete':
        $language->load('localisation/language');
        $loader->auto('localisation/language');
        $loader->auto('pagination');
        break;
    case 'localisation/language/insert':
    case 'localisation/language/update':
        $language->load('localisation/language');
        $loader->auto('localisation/language');
        break;
    case 'localisation/tax_class':
    case 'localisation/tax_class/grid':
    case 'localisation/tax_class/delete':
        $language->load('localisation/tax_class');
        $loader->auto('localisation/taxclass');
        $loader->auto('pagination');
        break;
    case 'localisation/tax_class/insert':
    case 'localisation/tax_class/update':
        $language->load('localisation/tax_class');
		$loader->auto('localisation/language');
        $loader->auto('localisation/taxclass');
        break;
    case 'localisation/currency':
    case 'localisation/currency/grid':
    case 'localisation/currency/delete':
        $language->load('localisation/currency');
        $loader->auto('localisation/currency');
        $loader->auto('pagination');
        break;
    case 'localisation/currency/insert':
    case 'localisation/currency/update':
        $language->load('localisation/currency');
        $loader->auto('localisation/currency');
        $loader->auto('localisation/language');
        break;
    case 'localisation/weight_class':
    case 'localisation/weight_class/grid':
    case 'localisation/weight_class/delete':
        $language->load('localisation/weight_class');
        $loader->auto('localisation/weightclass');
        $loader->auto('pagination');
        break;
    case 'localisation/weight_class/insert':
    case 'localisation/weight_class/update':
        $language->load('localisation/weight_class');
        $loader->auto('localisation/weightclass');
        $loader->auto('localisation/language');
        break;
    case 'localisation/length_class':
    case 'localisation/length_class/grid':
    case 'localisation/length_class/delete':
        $language->load('localisation/length_class');
        $loader->auto('localisation/lengthclass');
        $loader->auto('pagination');
        break;
    case 'localisation/length_class/insert':
    case 'localisation/length_class/update':
        $language->load('localisation/length_class');
        $loader->auto('localisation/lengthclass');
        $loader->auto('localisation/language');
        break;
    case 'localisation/geo_zone':
    case 'localisation/geo_zone/grid':
    case 'localisation/geo_zone/delete':
        $language->load('localisation/geo_zone');
        $loader->auto('localisation/geozone');
        $loader->auto('pagination');
        break;
    case 'localisation/geo_zone/insert':
    case 'localisation/geo_zone/update':
        $language->load('localisation/geo_zone');
        $loader->auto('localisation/geozone');
        $loader->auto('localisation/language');
        break;
    case 'sale/coupon':
    case 'sale/coupon/grid':
    case 'sale/coupon/delete':
        $language->load('sale/coupon');
        $loader->auto('sale/coupon');
        $loader->auto('pagination');
        break;
    case 'sale/coupon/insert':
    case 'sale/coupon/update':
        $language->load('sale/coupon');
        $loader->auto('sale/coupon');
        $loader->auto('store/store');
		$loader->auto('localisation/language'); 
		$loader->auto('store/category');
        break;
    case 'sale/bank':
    case 'sale/bank/grid':
    case 'sale/bank/delete':
        $language->load('sale/bank');
        $loader->auto('sale/bank');
		$loader->auto('image'); 
        $loader->auto('pagination');
        break;
    case 'sale/bank/insert':
    case 'sale/bank/update':
        $language->load('sale/bank');
        $loader->auto('sale/bank');
		$loader->auto('image'); 
        break;
    case 'sale/payment':
    case 'sale/payment/grid':
    case 'sale/payment/delete':
        $language->load('sale/payment');
        $loader->auto('sale/payment');
        $loader->auto('sale/customer');
        $loader->auto('sale/bank');
        $loader->auto('localisation/orderpaymentstatus');
		$loader->auto('currency'); 
        $loader->auto('pagination');
        
        // registry set
        $registry->set('currency', new Currency($registry));
        break;
    case 'sale/payment/insert':
    case 'sale/payment/update':
        $language->load('sale/payment');
        $loader->auto('sale/payment');
        break;
    case 'sale/balance':
    case 'sale/balance/grid':
    case 'sale/balance/delete':
        $language->load('sale/balance');
        $loader->auto('sale/balance');
        $loader->auto('sale/customer');
		$loader->auto('currency'); 
        $loader->auto('pagination');
        
        // registry set
        $registry->set('currency', new Currency($registry));
        break;
    case 'sale/balance/insert':
    case 'sale/balance/update':
        $language->load('sale/balance');
        $loader->auto('sale/balance');
        break;
    case 'sale/bank_account':
    case 'sale/bank_account/grid':
    case 'sale/bank_account/delete':
        $language->load('sale/bank_account');
        $loader->auto('sale/bank_account');
        $loader->auto('pagination');
        break;
    case 'sale/bank_account/insert':
    case 'sale/bank_account/update':
        $language->load('sale/bank_account');
        $loader->auto('sale/bank');
        $loader->auto('store/store');
        $loader->auto('sale/bank_account');
        break;
    case 'sale/coupon/products':
        $loader->auto('image');
        $loader->auto('sale/coupon');
		$loader->auto('store/product');
        break;
    case 'sale/plan':
    case 'sale/plan/grid':
    case 'sale/plan/delete':
        $language->load('sale/plan');
        $loader->auto('sale/plan');
        $loader->auto('url');
        $loader->auto('image');
        $loader->auto('pagination');
        break;
    case 'sale/plan/insert':
    case 'sale/plan/update':
        $language->load('sale/plan');
        $loader->auto('sale/plan');
        $loader->auto('image');
        break;
    case 'sale/customer':
    case 'sale/customer/grid':
    case 'sale/customer/delete':
        $language->load('sale/customer');
        $loader->auto('sale/customer');
		$loader->auto('sale/customergroup');
        $loader->auto('pagination');
        break;
    case 'sale/customer/insert':
    case 'sale/customer/update':
        $language->load('sale/customer');
        $loader->auto('sale/customer');
        $loader->auto('sale/address');
		$loader->auto('sale/customergroup');
		$loader->auto('localisation/country');		
        break;
    case 'sale/customer/approve':
        $language->load('sale/customer');
        $language->load('mail/customer');
        $loader->auto('sale/customer');	
        $loader->auto('mail');		
        break;
    case 'sale/customergroup':
    case 'sale/customergroup/grid':
    case 'sale/customergroup/delete':
        $language->load('sale/customer_group');
        $loader->auto('sale/customergroup');
		$loader->auto('sale/customer');
        $loader->auto('pagination');
        break;
    case 'sale/customergroup/insert':
    case 'sale/customergroup/update':
        $language->load('sale/customer_group');
		$loader->auto('sale/customergroup');
        $loader->auto('sale/customer');
        break;
    case 'sale/order':
    case 'sale/order/grid':
    case 'sale/order/delete':
        $language->load('sale/order');
		$loader->auto('sale/order');
		$loader->auto('localisation/orderstatus');
        $loader->auto('pagination');
		$loader->auto('currency');
        $registry->set('currency', new Currency($registry));
        break;
    case 'sale/order/insert':
    case 'sale/order/update':
        $language->load('sale/order');
		$loader->auto('sale/order');
		$loader->auto('sale/customergroup');
		$loader->auto('localisation/orderstatus');
		$loader->auto('localisation/country');
		$loader->auto('store/category');
		$loader->auto('store/product');
		$loader->auto('setting/extension');
		$loader->auto('currency');
        $registry->set('currency', new Currency($registry));
        break;
    case 'sale/order/invoice':
        $language->load('sale/order');
		$loader->auto('sale/order');
		$loader->auto('currency');
        $registry->set('currency', new Currency($registry));
        break;
    case 'setting/setting':
        $language->load('setting/setting');
		$loader->auto('setting/setting');
		$loader->auto('content/page');
		$loader->auto('marketing/newsletter');
		$loader->auto('sale/customergroup');
		$loader->auto('localisation/language');
		$loader->auto('localisation/country');
		$loader->auto('localisation/currency');
		$loader->auto('localisation/lengthclass');
		$loader->auto('localisation/weightclass');
		$loader->auto('localisation/orderstatus');
		$loader->auto('localisation/orderpaymentstatus');
		$loader->auto('localisation/stockstatus');
        $loader->auto('image');
        $loader->auto('valid_forms');
        $registry->set('validate_form', new ValidateForms());
        break;
    case 'report/sale':
    case 'report/sale/grid':
		$language->load('report/sale');
		$loader->auto('report/sale');
		$loader->auto('localisation/orderstatus');
        $loader->auto('pagination');
		$loader->auto('currency');
        $registry->set('currency', new Currency($registry));
        break;
    case 'marketing/mailserver':
    case 'marketing/mailserver/grid':
    case 'marketing/mailserver/delete':
        $language->load('marketing/mailserver');
        $loader->auto('setting/setting');
        $loader->auto('pagination');
        break;
    case 'marketing/mailserver/insert':
    case 'marketing/mailserver/update':
        $language->load('marketing/mailserver');
        $loader->auto('setting/setting');
        break;
    case 'marketing/newsletter':
    case 'marketing/newsletter/grid':
    case 'marketing/newsletter/delete':
        $language->load('marketing/newsletter');
        $loader->auto('marketing/newsletter');
        $loader->auto('pagination');
        break;
    case 'marketing/newsletter/insert':
    case 'marketing/newsletter/update':
        $language->load('marketing/newsletter');
        $loader->auto('marketing/newsletter');
        $loader->auto('store/category');
        $loader->library('email/newsletter');
        $loader->library('email/template');
        $registry->set('newsletter', new Newsletter());
        $registry->set('email_template', new EmailTemplate($registry));
        break;
    case 'marketing/message':
        $language->load('marketing/message');
		$loader->auto('setting/setting');
		$loader->auto('content/page');
		$loader->auto('marketing/newsletter');
        $loader->auto('valid_forms');
        $registry->set('validate_form', new ValidateForms());
        break;
    case 'marketing/contact':
    case 'marketing/contact/grid':
    case 'marketing/contact/delete':
        $language->load('marketing/contact');
        $loader->auto('marketing/contact');
        $loader->auto('pagination');
        break;
    case 'marketing/contact/insert':
    case 'marketing/contact/update':
        $language->load('marketing/contact');
        $loader->auto('marketing/contact');
        $loader->auto('marketing/list');
        $loader->auto('sale/customer');
        $loader->library('url');
        break;
    case 'marketing/list':
    case 'marketing/list/grid':
    case 'marketing/list/delete':
        $language->load('marketing/list');
        $loader->auto('marketing/list');
        $loader->auto('marketing/contact');
        $loader->auto('pagination');
        break;
    case 'marketing/list/insert':
    case 'marketing/list/update':
        $language->load('marketing/list');
        $loader->auto('marketing/list');
        $loader->auto('marketing/contact');
        $loader->library('url');
        break;
    case 'marketing/campaign':
    case 'marketing/campaign/grid':
    case 'marketing/campaign/delete':
        $language->load('marketing/campaign');
        $loader->auto('marketing/campaign');
        $loader->auto('marketing/contact');
        $loader->auto('pagination');
        break;
    case 'marketing/campaign/insert':
    case 'marketing/campaign/update':
        $language->load('marketing/campaign');
        $loader->auto('marketing/campaign');
        $loader->auto('marketing/list');
        $loader->auto('marketing/newsletter');
        $loader->auto('marketing/contact');
        $loader->library('url');
        break;
    case 'marketing/campaign/send':
        $language->load('marketing/campaign');
        $loader->auto('marketing/campaign');
        $loader->auto('marketing/list');
        $loader->auto('marketing/newsletter');
        $loader->auto('marketing/contact');
        $loader->library('url');
        break;
    case 'style/widget':
    case 'style/widget/delete':
    case 'style/widget/sortable':
        $language->load('style/widget');
        $loader->auto('image');
		$loader->auto('style/widget');
		$loader->auto('store/store');
		$loader->auto('setting/extension');
        break;
    case 'style/theme':
    case 'style/theme/grid':
    case 'style/theme/delete':
        $language->load('style/theme');
        $loader->auto('style/theme');
        $loader->auto('pagination');
        break;
    case 'style/theme/insert':
    case 'style/theme/update':
    case 'style/theme/save':
        $language->load('style/theme');
        $loader->auto('style/theme');
        $loader->auto('image');
        break;
    case 'tool/backup':
    case 'tool/backup':
    case 'tool/backup/backup':
        $language->load('tool/backup');
		$loader->auto('tool/backup');
        break;
    case 'tool/backup/backup':
		$loader->auto('tool/backup');
        break;
    case 'tool/restore':
        $language->load('tool/restore');
		$loader->auto('tool/backup');
        break;
    case 'user/user':
    case 'user/user/grid':
    case 'user/user/delete':
        $language->load('user/user');
		$loader->auto('user/user');
		$loader->auto('pagination');
        break;
    case 'user/user/insert':
    case 'user/user/update':
        $language->load('user/user');
		$loader->auto('user/user');
		$loader->auto('user/usergroup');
        break;
    case 'user/user_permission':
    case 'user/user_permission/grid':
    case 'user/user_permission/delete':
        $language->load('user/user_group');
		$loader->auto('user/usergroup');
		$loader->auto('pagination');
        break;
    case 'user/user_permission/insert':
    case 'user/user_permission/update':
        $language->load('user/user_group');
		$loader->auto('user/usergroup');
        break;
    default:
    case 'common/home':    
        //Languages
        $language->load('common/home');
        //Models
        $loader->auto('sale/customer');
    	$loader->auto('sale/order');
    	$loader->auto('store/product');
    	$loader->auto('store/review');
        //Libs
        $loader->auto('currency');
        //Set
        $registry->set('currency', new Currency($registry));
        break;
}

// Log 
global $log;
$log = new Log('log.txt');
$registry->set('log', $log);
//listen for errors
Events::on("php:error", function ($error_msg) {
    global $log;
    $log->trace($error_msg);
});

$tpl = $config->get('config_admin_template') ? $config->get('config_admin_template') : 'default';

$javascripts = $styles = $scripts = [];

$cssSharedPath = ($config->get('config_render_css_in_file')) ? DIR_CSS : HTTP_CSS;
$jsSharedPath = ($config->get('config_render_js_in_file')) ? DIR_JS : HTTP_JS;
$jsPath = ($config->get('config_render_js_in_file')) ? DIR_ADMIN_THEME_JS : HTTP_ADMIN_THEME_JS;
$jsAppPath = ($config->get('config_render_js_in_file')) ? DIR_ADMIN_THEME_JS : HTTP_ADMIN_THEME_JS;

if (file_exists(str_replace("%theme%", $tpl, DIR_ADMIN_THEME_JS) . 'deps.php')) {
    require_once(str_replace("%theme%", $tpl, DIR_ADMIN_THEME_JS) . 'deps.php');
    foreach ($js_assets as $i => $routes) {
        if (empty($routes)) continue;
        if (is_array($routes) && in_array($route, $routes) || $routes === '*') {
            array_push($javascripts, $i);
        }
    }

    if (isset($jsx_assets) && $jsx_assets) {
        foreach ($jsx_assets as $i => $routes) {
            if (empty($routes)) continue;
            if (((is_array($routes) && in_array($route, $routes)) || $routes === '*')) {
                array_push($scripts, array(
                    'method'=>'jsx',
                    'id'=>$i,
                    'script'=>file_get_contents($i)
                ));
            }
        }
    }
}

if (file_exists(str_replace("%theme%", $tpl, DIR_ADMIN_THEME_CSS) . 'deps.php')) {
    require_once(str_replace("%theme%", $tpl, DIR_ADMIN_THEME_CSS) . 'deps.php');
    foreach ($css_assets as $i => $asset) {
        if (empty($asset['css'])) continue;
        if (is_array($asset['routes']) && in_array($route, $asset['routes']) || $asset['routes'] === '*') {
            array_push($styles, $asset['css']);
        }
    }
}

$registry->set('javascripts', $javascripts);
$registry->set('styles', $styles);
$registry->set('scripts', $scripts);

$registry->set('hooks', $hooks);
$hooks->run('load', $registry);
Events::emit("load", $registry);