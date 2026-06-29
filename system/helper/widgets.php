<?php

final class NecoWidget {

    /**
     * @param $user_banned
     * Utilizado para saber cuando usuario est� bloqueado
     * */
    private $user_banned = false;
    private $db;
    private $hooks;
    private $user;
    private $customer;
    private $resgistry;
    private $widgets;
    private $data = [];
    private $result = [];

    //TODO: construir un mapa de todas las funciones llamables a trav�s de ajax
    public function __construct($registry, $route = "all", $app = 'shop') {
        $this->landing_page = $route;
        $this->app = $app;
        $this->registry = $registry;
        $this->config = $this->registry->get('config');
        $this->customer = $this->registry->get('customer');
        $this->user = $this->registry->get('user');
        $this->cache = $this->registry->get('cache');
        $this->db = $this->registry->get('db');
        $this->hooks = $this->registry->get('hooks');
        $this->ip = $_SERVER['REMOTE_ADDR'];
    }

    public function getRoutes() {
        $widgetsRoutes = array(
            'Home'=>array(
                'text_home' => 'common/home'
            ),

            'Account'=>array(
                'text_account_login' => 'account/login',
                'text_account_logout' => 'account/logout',
                'text_account_register' => 'account/register',
                'text_account_forgotten' => 'account/forgotten',
                'text_account_success' => 'account/success',
                'text_account_account' => 'account/account',
                'text_account_address' => 'account/address',
                'text_account_address_insert' => 'account/address/insert',
                'text_account_address_update' => 'account/address/update',
                'text_account_balance' => 'account/balance',
                'text_account_download' => 'account/download',
                'text_account_edit' => 'account/edit',
                'text_account_history' => 'account/history',
                'text_account_invoice' => 'account/invoice',
                'text_account_message' => 'account/message',
                'text_account_newsletter' => 'account/newsletter',
                'text_account_order' => 'account/order',
                'text_account_password' => 'account/password',
                'text_account_payment' => 'account/payment',
                'text_account_register' => 'account/register',
                'text_account_review' => 'account/review'
            ),

            'Cart / Checkout'=>array(
                'text_cart_success' => 'checkout/success',
                'text_cart' => 'checkout/cart'
            ),

            'Store Manufacturers'=>array(
                'text_manufacturer' => 'store/manufacturer/index',
                'Manufacturer Not Found' => 'store/manufacturer/error404',
                'text_manufacturers' => 'store/manufacturer/all',
            ),

            'Store Categories'=>array(
                'text_category' => 'store/category/index',
                'Category Not Found' => 'store/category/error404',
                'text_categories' => 'store/category/all',
            ),

            'Store Products'=>array(
                'text_product' => 'store/product/index',
                'Product Quick View' => 'store/product/quickviewjson',
                'Product Not Found' => 'store/product/error404',
                'text_products' => 'store/product/all',
            ),

            'Store Listing'=>array(
                'text_search' => 'store/search',
            ),

            'Store Specials'=>array(
                'text_special' => 'store/special',
            ),

            'Content Pages'=>array(
                'text_page' => 'content/page/index',
                'Page Not Found' => 'content/page/error404',
            ),

            'Content Post Categories'=>array(
                'text_post_category' => 'content/category/index',
                'Post Category Not Found' => 'content/category/error404',
                'text_post_categories' => 'content/category/all',
            ),

            'Content Posts'=>array(
                'text_post' => 'content/post/index',
                'Post Not Found' => 'content/post/error404',
                'text_posts' => 'content/post/all',
            ),

            'Pre-built Pages'=>array(
                'text_sitemap' => 'page/sitemap',
                'text_deprecated' => 'page/deprecated',
            ),
        );

        $widgetsRoutes = $this->hooks->applyFilters("widgetsRoutes", $widgetsRoutes);
        
        return $widgetsRoutes;
    }

    public function getWidget($name, $useCache = true) {
        $prefix = "widgets.widget.".$name;

        $cachedId = $prefix .'.'.
            (int)STORE_ID ."_".
            (int)$this->config->get('config_store_id');

        $cached = $this->cache->get($cachedId, $prefix);
        if (!$cached || !$useCache || (bool)$this->user->getId()) {
            $sql = "SELECT * FROM `" . DB_PREFIX . "widget` w ";

            $criteria[] = " w.`name` = '" . $this->db->escape($name) . "' ";

            if (isset($criteria)) {
                $sql .= " WHERE " . implode(" AND ", $criteria);
            }

            $q = $this->db->query($sql);

            $this->cache->set($cachedId, $q->row, $prefix);
            return $q->row;
        } else {
            return $cached;
        }
    }

    public function getWidgets($position = array(), $useCache = true) {
        $data = is_array($position) ? $position : array('position' => $position);

        $data['landing_page'] = isset($data['landing_page']) ? $data['landing_page'] : $this->landing_page;
        $data['app'] = isset($data['app']) ? $data['app'] : $this->app;
        $data['store_id'] = isset($data['store_id']) ? $data['store_id'] : STORE_ID;
        $data['object_type'] = isset($data['object_type']) ? $data['object_type'] : $this->object_type;
        $data['object_id'] = isset($data['object_id']) ? $data['object_id'] : $this->object_id;

        $prefix = "widgets.widgets.".
                $data['app'] .' '.
                $data['position'] .' '.
                $data['landing_page'] .' '.
                $data['store_id'] .' '.
                $data['object_type'] .' '.
                $data['object_id'];

        $cachedId = $prefix .'.'.
            (int)STORE_ID ."_".
            serialize($data).
            (int)$data['store_id'];

        $cached = $this->cache->get($cachedId, $prefix);
        if (!$cached || !$useCache || (bool)$this->user->getId()) {
            $sql = "SELECT * FROM `" . DB_PREFIX . "widget` w ";

            $position = isset($data['position']) && !empty($data['position']) ? $data['position'] : "main";

            $criteria[] = " w.`position` = '" . $this->db->escape($position) . "' ";
            $criteria[] = " w.`app` = '" . $this->db->escape($this->app) . "' ";

            if (isset($data['row_id']) && !empty($data['row_id'])) {
                $criteria[] = " `settings` LIKE '%" . $this->db->escape($data['row_id']) . "%' ";
            }

            if (isset($data['col_id']) && !empty($data['col_id'])) {
                $criteria[] = " `settings` LIKE '%" . $this->db->escape($data['col_id']) . "%' ";
            }

            if (isset($data['show_in_mobile']) && !empty($data['show_in_mobile'])) {
                $criteria[] = " `settings` LIKE '%showonmobile%' ";
            }

            if (isset($data['show_in_tablet']) && !empty($data['show_in_tablet'])) {
                $criteria[] = " `settings` LIKE '%showontablet%' ";
            }

            if (isset($data['show_in_facebook']) && !empty($data['show_in_facebook'])) {
                $criteria[] = " `settings` LIKE '%showonfacebook%' ";
            }

            if (isset($data['show_in_desktop']) && !empty($data['show_in_desktop'])) {
                $criteria[] = " `settings` LIKE '%showondesktop%' ";
            }
            /*
            if (isset($data['customer_session_mode']) && $data['customer_session_mode']=="logon") {
                $criteria[] = " (`settings` LIKE '%customer_session_mode=logon%' OR `settings` LIKE '%customer_session_mode=any%') ";
            } else if (isset($data['customer_session_mode']) && $data['customer_session_mode']=="logoff") {
                $criteria[] = " (`settings` LIKE '%customer_session_mode=logoff%' OR `settings` LIKE '%customer_session_mode=any%') ";
            } else { 
                $criteria[] = " `settings` LIKE '%customer_session_mode=any%' ";
            }
            */

            if (isset($data['async']) && !empty($data['async'])) {
                $criteria[] = " `settings` LIKE '%async=on%' ";
            }

            $lp = " (`settings` LIKE '%landing_page=all%' ";
            if (isset($data['landing_page']) && !empty($data['landing_page'])) {
                $lp .= " OR `settings` LIKE '%landing_page=" . $this->db->escape($data['landing_page']) . "%' ";
            }
            $lp .= ")";
            $criteria[] = $lp;

            if (isset($data['object_type']) && $data['object_type'] == 'banner_item') {
                $criteria[] = " w.`store_id` = 0 ";
            } else {
                $criteria[] = " w.`store_id` = '" . (int)$data['store_id'] . "' ";
            }

            if (isset($data['object_type']) && !empty($data['object_type']) && $data['object_type'] !== 'widget') {
                $criteria[] = " `settings` LIKE '%object_type=" . $this->db->escape($data['object_type']) . "%' ";
            } else {
                $criteria[] = " `settings` NOT LIKE '%object_type%' ";
            }

            if (isset($data['object_type']) && !empty($data['object_type']) && isset($data['object_id']) && !empty($data['object_id']) && $data['object_type'] !== 'widget') {
                $criteria[] = " `settings` LIKE '%object_id=" . intval($data['object_id']) . "%' ";
            } else {
                $criteria[] = " `settings` NOT LIKE '%object_id%' ";
            }

            if (isset($criteria)) {
                $sql .= " WHERE " . implode(" AND ", $criteria);
            }

            $sql .= " ORDER BY `order` ASC";

            $results = $this->db->query($sql);

            $this->widgets = $results->rows;
            $this->cache->set($cachedId, $this->widgets, $prefix);
            return $this->widgets;
        } else {
            return $cached;
        }
    }

    public function getRows($data, $useCache = true) {
        $prefix = "widgets.rows.".
                (isset($data['app']) ? $data['app'] .' ' : '').
                $data['landing_page'] .' '.
                $data['store_id'] .' '.
                (isset($data['object_type']) ? $data['object_type'] .' ' : '').
                (isset($data['object_id']) ? $data['object_id'] .' ' : '');

        $cachedId = $prefix .".".
            (int)STORE_ID ."_".
            serialize($data).
            (int)$this->config->get('config_store_id');

        $cached = $this->cache->get($cachedId, $prefix);
        if (!$cached || !$useCache || (bool)$this->user->getId()) {
            $sql = "SELECT * FROM `" . DB_PREFIX . "property` p ";

            $data['store_id'] = isset($data['store_id']) ? $data['store_id'] : STORE_ID;
            $data['object_type'] = isset($data['object_type']) ? $data['object_type'] : $this->object_type;
            $data['object_id'] = isset($data['object_id']) ? $data['object_id'] : $this->object_id;

            $criteria[] = " p.`store_id` = '" . (int)$data['store_id'] . "' ";
            $criteria[] = " p.`object_type` = 'widget_rows' ";

            if (isset($data['position']) && !empty($data['position'])) {
                $criteria[] = " p.`group` = '" . $this->db->escape($data['position']) . "' ";
            }

            if (isset($data['row_id']) && !empty($data['row_id'])) {
                $criteria[] = " p.`key` = '" . $this->db->escape($data['row_id']) . "' ";
            }

            $lp = " (p.`value` LIKE '%landing_page=all%' ";
            if (isset($data['landing_page']) && !empty($data['landing_page'])) {
                $lp .= " OR p.`value` LIKE '%landing_page=" . $this->db->escape($data['landing_page']) . "%' ";
            }
            $lp .= ")";
            $criteria[] = $lp;

            if (isset($data['app']) && !empty($data['app'])) {
                $criteria[] = " p.`value` LIKE '%app=" . $this->db->escape($data['app']) . "%' ";
            }

            if (isset($data['show_in_mobile']) && !empty($data['show_in_mobile'])) {
                $criteria[] = " p.`value` LIKE '%show_in_mobile=on%' ";
            }

            if (isset($data['show_in_tablet']) && !empty($data['show_in_tablet'])) {
                $criteria[] = " p.`value` LIKE '%show_in_tablet=on%' ";
            }

            if (isset($data['show_in_facebook']) && !empty($data['show_in_facebook'])) {
                $criteria[] = " p.`value` LIKE '%show_in_facebook=on%' ";
            }

            if (isset($data['show_in_desktop']) && !empty($data['show_in_desktop'])) {
                $criteria[] = " p.`value` LIKE '%show_in_desktop=on%' ";
            }

            if (isset($data['customer_session_mode']) && $data['customer_session_mode']) {
                $criteria[] = " (`value` LIKE '%customer_session_mode=logon%' OR `value` LIKE '%customer_session_mode=any%') ";
            } else if (isset($data['customer_session_mode']) && !$data['show_in_desktop']) {
                $criteria[] = " (`value` LIKE '%customer_session_mode=logoff%' OR `value` LIKE '%customer_session_mode=any%') ";
            }

            if (isset($data['async']) && !empty($data['async'])) {
                $criteria[] = " p.`value` LIKE '%async=on%' ";
            }

            if (isset($data['object_type']) && !empty($data['object_type']) && $data['object_type'] !== 'widget') {
                $criteria[] = " p.`value` LIKE '%object_type=" . $this->db->escape($data['object_type']) . "%' ";
            } else {
                $criteria[] = " p.`value` NOT LIKE '%object_type%' ";
            }

            if (isset($data['object_type']) && !empty($data['object_type']) && isset($data['object_id']) && !empty($data['object_id']) && $data['object_type'] !== 'widget') {
                $criteria[] = " p.`value` LIKE '%object_id=" . intval($data['object_id']) . "%' ";
            } else {
                $criteria[] = " p.`value` NOT LIKE '%object_id%' ";
            }

            if (isset($criteria)) {
                $sql .= " WHERE " . implode(" AND ", $criteria);
            }

            $sql .= " ORDER BY `order` ASC";
            $result = $this->db->query($sql);
            if (isset($data['full_tree'])) {
                foreach ($result->rows as $k => $row) {

                    $row_settings = (array)unserialize($row['value']);

                    //conditions to show or hide
                    if (isset($row_settings['conditional_logic_when_route_contains'])) {
                        $r_conditions = explode(",", $row_settings['conditional_logic_when_route_contains']);
                    }
                    $row_to_be_removed = isset($row_settings['conditional_logic_action']);

                    if ($row_to_be_removed && !empty($r_conditions)) {
                        foreach ($r_conditions as $word) {
                            $word = trim($word);
                            if (empty($word)) {
                                $row_to_be_removed = false;
                                continue;
                            }
                            if ($word == "any") {
                                $row_to_be_removed = false;
                                continue;
                            }
                            //only show hen this condition is satisfied
                            if ($row_to_be_removed
                                && isset($row_settings['conditional_logic_action']) 
                                && $row_settings['conditional_logic_action'] == 'show' 
                                && strpos($this->landing_page, strtolower($word)) !== false 
                            ) {
                                $row_to_be_removed = false;
                            }

                            //only hide hen this condition is satisfied
                            if ($row_to_be_removed
                                && isset($row_settings['conditional_logic_action']) 
                                && $row_settings['conditional_logic_action'] == 'hide' 
                                && strpos($this->landing_page, strtolower($word)) === false 
                            ) {
                                $row_to_be_removed = false;
                            }
                        }

                        if (!empty($r_conditions) && $row_to_be_removed) {
                            //has to hide for this route
                            unset($result->rows[$k]);
                            continue;
                            //TODO: log this 
                        }
                    }
                    
                    $col_data = [];
                    $col_data['row_id'] = $row['key'];
                    if (isset($data['position']) && !empty($data['position'])) $col_data['position'] = $data['position'];
                    if (isset($data['store_id'])) $col_data['store_id'] = $data['store_id'];
                    if (isset($data['object_type']) && !empty($data['object_type'])) $col_data['object_type'] = $data['object_type'];
                    if (isset($data['object_id']) && !empty($data['object_id'])) $col_data['object_id'] = $data['object_id'];
                    if (isset($data['landing_page']) && !empty($data['landing_page'])) $col_data['landing_page'] = $data['landing_page'];
                    if (isset($data['app']) && !empty($data['app'])) $col_data['app'] = $data['app'];
                    if (isset($data['show_in_mobile']) && !empty($data['show_in_mobile'])) $col_data['show_in_mobile'] = $data['show_in_mobile'];
                    if (isset($data['show_in_tablet']) && !empty($data['show_in_tablet'])) $col_data['show_in_tablet'] = $data['show_in_tablet'];
                    if (isset($data['show_in_facebook']) && !empty($data['show_in_facebook'])) $col_data['show_in_facebook'] = $data['show_in_facebook'];
                    if (isset($data['show_in_desktop']) && !empty($data['show_in_desktop'])) $col_data['show_in_desktop'] = $data['show_in_desktop'];
                    if (isset($data['async']) && !empty($data['async'])) $col_data['async'] = $data['async'];
                    if (isset($data['full_tree'])) $col_data['full_tree'] = $data['full_tree'];

                    $result->rows[$k]['columns'] = $this->getCols($col_data);

                    $_children = $_widget = [];
                    foreach ($result->rows[$k]['columns'] as $p => $col) {

                        $col_settings = (array)unserialize($col['value']);

                        //conditions to show or hide
                        if (isset($col_settings['conditional_logic_when_route_contains'])) {
                            $c_conditions = explode(",", $col_settings['conditional_logic_when_route_contains']);
                        }
                        $col_to_be_removed = isset($col_settings['conditional_logic_action']);
                        
                        if ($col_to_be_removed && !empty($c_conditions)) {
                            foreach ($c_conditions as $word) {
                                $word = trim($word);
                                if (empty($word)) {
                                    $col_to_be_removed = false;
                                    continue;
                                }
                                if ($word == "any") {
                                    $col_to_be_removed = false;
                                    continue;
                                }
                                //only show hen this condition is satisfied
                                if ($col_to_be_removed
                                    && isset($col_settings['conditional_logic_action']) 
                                    && $col_settings['conditional_logic_action'] == 'show' 
                                    && strpos($this->landing_page, strtolower($word)) !== false 
                                ) {
                                    $col_to_be_removed = false;
                                }

                                //only hide hen this condition is satisfied
                                if ($col_to_be_removed
                                    && isset($col_settings['conditional_logic_action']) 
                                    && $col_settings['conditional_logic_action'] == 'hide' 
                                    && strpos($this->landing_page, strtolower($word)) === false 
                                ) {
                                    $col_to_be_removed = false;
                                }
                            }
                            if (!empty($c_conditions) && $col_to_be_removed) {
                                //has to hide for this route
                                unset($result->rows[$k]['columns'][$p]);
                                continue;
                                //TODO: log this 
                            }
                        }
                        
                        foreach ($col['widgets'] as $l => $w) {
                            $settings = (array)unserialize($w['settings']);
                            //check if the customer has to be logged or not
                            if (isset($settings['customer_session_mode']) && $settings['customer_session_mode'] == 'logon' && is_callable(array($this->customer, 'isLogged'), true) && !$this->customer->isLogged()) {
                                //customer needs to log in first
                                unset($result->rows[$k]['columns'][$p]['widgets'][$l]);
                                continue;
                                //TODO: log this 
                            }

                            if (isset($settings['customer_session_mode']) && $settings['customer_session_mode'] == 'logoff' && is_callable(array($this->customer, 'isLogged'), true) && $this->customer->isLogged()) {
                                //customer needs to log out first
                                unset($result->rows[$k]['columns'][$p]['widgets'][$l]);
                                continue;
                                //TODO: log this 
                            }

                            //conditions to show or hide
                            if (isset($settings['conditional_logic_when_route_contains'])) {
                                $conditions = explode(",", $settings['conditional_logic_when_route_contains']);
                            }
                            $needs_to_be_removed = isset($settings['conditional_logic_action']);
                            if ($needs_to_be_removed && !empty($conditions)) {
                                foreach ($conditions as $word) {
                                    $word = trim($word);
                                    if (empty($word)) {
                                        $needs_to_be_removed = false;
                                        continue;
                                    }
                                    if ($word == "any") {
                                        $needs_to_be_removed = false;
                                        continue;
                                    }
                                    //only show hen this condition is satisfied
                                    if ($needs_to_be_removed
                                        && isset($settings['conditional_logic_action']) 
                                        && $settings['conditional_logic_action'] == 'show' 
                                        && strpos($this->landing_page, strtolower($word)) !== false 
                                    ) {
                                        $needs_to_be_removed = false;
                                    }

                                    //only hide hen this condition is satisfied
                                    if ($needs_to_be_removed
                                        && isset($settings['conditional_logic_action']) 
                                        && $settings['conditional_logic_action'] == 'hide' 
                                        && strpos($this->landing_page, strtolower($word)) === false 
                                    ) {
                                        $needs_to_be_removed = false;
                                    }
                                }
                                if (!empty($conditions) && $needs_to_be_removed) {
                                    //has to hide for this route
                                    unset($result->rows[$k]['columns'][$p]['widgets'][$l]);
                                    continue;
                                    //TODO: log this 
                                }
                            }
                                
                            if (isset($settings['autoload']) && $settings['autoload']) {
                                //trigger events
                                Events::emit("loadWidget", $w, $settings);
                                $_children[$w['name']] = $settings['route'];
                                $_widget[$w['name']] = $w;
                            }
                        }
                        $result->rows[$k]['children'] = $_children;
                        $result->rows[$k]['widget'] = $_widget;
                    }
                }
            }

            $this->cache->set($cachedId, $result->rows, $prefix);
            return $result->rows;
        } else {
            return $cached;
        }
    }

    public function getCols($data, $useCache = true) {
            $prefix = "widgets.cols.".
                (isset($data['app']) ? $data['app'] .' ' : '').
                $data['landing_page'] .' '.
                $data['store_id'] .' '.
                (isset($data['object_type']) ? $data['object_type'] .' ' : '').
                (isset($data['object_id']) ? $data['object_id'] .' ' : '');

        $cachedId = $prefix .".".
            (int)STORE_ID ."_".
            serialize($data).
            (int)$this->config->get('config_store_id');

        $cached = $this->cache->get($cachedId, $prefix);
        if (!$cached || !$useCache || (bool)$this->user->getId()) {
            $sql = "SELECT * FROM `" . DB_PREFIX . "property` p ";

            $data['store_id'] = isset($data['store_id']) ? $data['store_id'] : STORE_ID;
            $data['object_type'] = isset($data['object_type']) ? $data['object_type'] : $this->object_type;
            $data['object_id'] = isset($data['object_id']) ? $data['object_id'] : $this->object_id;

            $criteria[] = " p.`store_id` = '" . intval($data['store_id']) . "' ";
            $criteria[] = " p.`object_type` = 'widget_cols' ";

            if (isset($data['position']) && !empty($data['position'])) {
                $criteria[] = " p.`group` = '" . $this->db->escape($data['position']) . "' ";
            }

            if (isset($data['col_id']) && !empty($data['col_id'])) {
                $criteria[] = " p.`key` = '" . $this->db->escape($data['col_id']) . "' ";
            }

            if (isset($data['row_id']) && !empty($data['row_id'])) {
                $criteria[] = " p.`value` LIKE '%row_id=" . $this->db->escape($data['row_id']) . "%' ";
            }

            $lp = " (p.`value` LIKE '%landing_page=all%' ";
            if (isset($data['landing_page']) && !empty($data['landing_page'])) {
                $lp .= " OR p.`value` LIKE '%landing_page=" . $this->db->escape($data['landing_page']) . "%' ";
            }
            $lp .= ")";
            $criteria[] = $lp;

            if (isset($data['app']) && !empty($data['app'])) {
                $criteria[] = " p.`value` LIKE '%app=" . $this->db->escape($data['app']) . "%' ";
            }

            if (isset($data['show_in_mobile']) && !empty($data['show_in_mobile'])) {
                $criteria[] = " p.`value` LIKE '%show_in_mobile=on%' ";
            }

            if (isset($data['show_in_tablet']) && !empty($data['show_in_tablet'])) {
                $criteria[] = " p.`value` LIKE '%show_in_tablet=on%' ";
            }

            if (isset($data['show_in_facebook']) && !empty($data['show_in_facebook'])) {
                $criteria[] = " p.`value` LIKE '%show_in_facebook=on%' ";
            }

            if (isset($data['show_in_desktop']) && !empty($data['show_in_desktop'])) {
                $criteria[] = " p.`value` LIKE '%show_in_desktop=on%' ";
            }

            if (isset($data['customer_session_mode']) && $data['customer_session_mode']) {
                $criteria[] = " (`value` LIKE '%customer_session_mode=logon%' OR `value` LIKE '%customer_session_mode=any%') ";
            } else if (isset($data['customer_session_mode']) && !$data['show_in_desktop']) {
                $criteria[] = " (`value` LIKE '%customer_session_mode=logoff%' OR `value` LIKE '%customer_session_mode=any%') ";
            }
            
            if (isset($data['async']) && !empty($data['async'])) {
                $criteria[] = " p.`value` LIKE '%async=on%' ";
            }

            if (isset($data['object_type']) && !empty($data['object_type']) && $data['object_type'] !== 'widget') {
                $criteria[] = " p.`value` LIKE '%object_type=" . $this->db->escape($data['object_type']) . "%' ";
            } else {
                $criteria[] = " p.`value` NOT LIKE '%object_type%' ";
            }

            if (isset($data['object_type']) && !empty($data['object_type']) && isset($data['object_id']) && !empty($data['object_id']) && $data['object_type'] !== 'widget') {
                $criteria[] = " p.`value` LIKE '%object_id=" . intval($data['object_id']) . "%' ";
            } else {
                $criteria[] = " p.`value` NOT LIKE '%object_id%' ";
            }

            if (isset($criteria)) {
                $sql .= " WHERE " . implode(" AND ", $criteria);
            }

            $sql .= " ORDER BY `order` ASC";

            $result = $this->db->query($sql);

            if (isset($data['full_tree'])) {
                foreach ($result->rows as $k => $col) {
                    $widget_data = [];
                    $widget_data['col_id'] = $col['key'];
                    if (isset($data['position']) && !empty($data['position'])) $widget_data['position'] = $data['position'];
                    if (isset($data['store_id'])) $widget_data['store_id'] = $data['store_id'];
                    if (isset($data['object_type']) && !empty($data['object_type'])) $widget_data['object_type'] = $data['object_type'];
                    if (isset($data['object_id']) && !empty($data['object_id'])) $widget_data['object_id'] = $data['object_id'];
                    if (isset($data['landing_page']) && !empty($data['landing_page'])) $widget_data['landing_page'] = $data['landing_page'];
                    if (isset($data['app']) && !empty($data['app'])) $widget_data['app'] = $data['app'];
                    if (isset($data['show_in_mobile']) && !empty($data['show_in_mobile'])) $widget_data['show_in_mobile'] = $data['show_in_mobile'];
                    if (isset($data['show_in_tablet']) && !empty($data['show_in_tablet'])) $widget_data['show_in_tablet'] = $data['show_in_tablet'];
                    if (isset($data['show_in_facebook']) && !empty($data['show_in_facebook'])) $widget_data['show_in_facebook'] = $data['show_in_facebook'];
                    if (isset($data['show_in_desktop']) && !empty($data['show_in_desktop'])) $widget_data['show_in_desktop'] = $data['show_in_desktop'];
                    if (isset($data['async']) && !empty($data['async'])) $widget_data['async'] = $data['async'];
                    if (isset($data['full_tree'])) $widget_data['full_tree'] = $data['full_tree'];

                    $result->rows[$k]['widgets'] = $this->getWidgets($widget_data);
                }
            }

            $this->cache->set($cachedId, $result->rows, $prefix);
            return $result->rows;
        } else {
            return $cached;
        }
    }

    public function save($data) {
        if (!isset($data['name']) && !isset($data['position']))
            return false;

        $result = $this->db->query("SELECT *,COUNT(*) AS total 
                FROM `" . DB_PREFIX . "widget` w 
                WHERE w.`name` = '" . $this->db->escape($data['name']) . "'");
        if ($result->row['total']) {
            $data['order'] = $data['order'] ?? 0;
            $return = $this->db->query("UPDATE `" . DB_PREFIX . "widget` SET 
                `position` = '" . $this->db->escape($data['position']) . "',
                `order` = '" . intval($data['order']) . "',
                `status` = '1',
                `settings` = '" . $this->db->escape(serialize($data['settings'])) . "'
                WHERE `name` = '" . $this->db->escape($data['name']) . "'");
            if (!empty($data['landing_page'])) {
                $this->db->query("DELETE FROM " . DB_PREFIX . "widget_landing_page WHERE `widget_id` = '" . intval($result->row['widget_id']) . "'");
                foreach ($data['landing_page'] as $landing_page) {
                    $this->db->query("INSERT INTO " . DB_PREFIX . "widget_landing_page SET
                    `widget_id` = '" . intval($result->row['widget_id']) . "',
                    landing_page = '" . $this->db->escape($landing_page) . "'");
                }
            }
        } else {
            $return = $this->db->query("INSERT INTO `" . DB_PREFIX . "widget` SET 
                `name` = '" . $this->db->escape($data['name']) . "',
                `code` = '{%" . $this->db->escape($data['name']) . "%}',
                `position` = '" . $this->db->escape($data['position']) . "',
                `extension` = '" . $this->db->escape($data['extension']) . "',
                `app` = '" . $this->db->escape($data['app']) . "',
                `order` = '" . intval($data['order']) . "',
                `store_id` = '" . intval($data['store_id']) . "',
                `status` = '1',
                `settings` = '" . $this->db->escape(serialize($data['settings'])) . "'");
            $widget_id = $this->db->getLastId();

            $this->db->query("INSERT INTO `" . DB_PREFIX . "widget_landing_page` SET 
                `widget_id` = '" . intval($widget_id) . "',
                `landing_page` = '" . $this->db->escape($data['landing_page']) . "'");
        }

        $prefix = 
                (isset($data['app'])? $data['app']:"") .' '.
                (isset($data['position']) ? $data['position'] : "") .' '.
                (isset($data['landing_page']) ? $data['landing_page'] : "") .' '.
                (isset($data['store_id']) ? $data['store_id'] : "") .' '.
                (isset($data['object_type']) ? $data['object_type'] : "") .' '.
                (isset($data['object_id']) ? $data['object_id'] : "");
        $this->cache->delete("widgets.widgets.". $prefix);

        $prefix =
            (isset($data['app']) ? $data['app'] : "") . ' ' .
            (isset($data['landing_page']) ? $data['landing_page'] : "") . ' ' .
            (isset($data['store_id']) ? $data['store_id'] : "") . ' ' .
            (isset($data['object_type']) ? $data['object_type'] : "") . ' ' .
            (isset($data['object_id']) ? $data['object_id'] : "");
        $this->cache->delete("widgets.rows.". $prefix);
        $this->cache->delete("widgets.cols.". $prefix);
        return $return;
    }

    public function saveRow($data) {
        if (!isset($data['row_id']) && !isset($data['position']))
            return false;

        $this->db->query("DELETE FROM `" . DB_PREFIX . "property` ".
            "WHERE `key` = '" . $this->db->escape($data['row_id']) . "' ".
            "AND `object_type` = 'widget_rows' ");

        $this->db->query("INSERT INTO `" . DB_PREFIX . "property` SET ".
            "`store_id` = '" . intval($data['store_id']) . "',".
            "`object_id` = '" . $this->db->escape(mt_rand(1,99999999)) . "',".
            "`object_type` = 'widget_rows',".
            "`group` = '" . $this->db->escape($data['position']) . "',".
            "`key` = '" . $this->db->escape($data['row_id']) . "',".
            "`order` = '" . intval($data['order']) . "',".
            "`value` = '" . str_replace("'","\'",$this->db->escape(serialize($data['settings']))) . "'");

        $prefix =
            (isset($data['app']) ? $data['app'] : "") . ' ' .
            (isset($data['landing_page']) ? $data['landing_page'] : "") . ' ' .
            (isset($data['store_id']) ? $data['store_id'] : "") . ' ' .
            (isset($data['object_type']) ? $data['object_type'] : "") . ' ' .
            (isset($data['object_id']) ? $data['object_id'] : "");
        $this->cache->delete("widgets.rows.". $prefix);
    }

    public function saveCol($data) {
        if (!isset($data['row_id']) && !isset($data['col_id']) && !isset($data['position']))
            return false;

        $this->db->query("DELETE FROM `" . DB_PREFIX . "property` ".
                "WHERE `key` = '" . $this->db->escape($data['col_id']) . "' ".
                "AND `object_type` = 'widget_cols' ");

        $this->db->query("INSERT INTO `" . DB_PREFIX . "property` SET ".
            "`store_id` = '" . intval($data['store_id']) . "',".
            "`object_id` = '" . $this->db->escape(mt_rand(1,99999999)) . "',".
            "`object_type` = 'widget_cols',".
            "`group` = '" . $this->db->escape($data['position']) . "',".
            "`key` = '" . $this->db->escape($data['col_id']) . "',".
            "`order` = '" . intval($data['order']) . "',".
            "`value` = '" . str_replace("'","\'",$this->db->escape(serialize($data['settings']))) . "'");

        $prefix =
            (isset($data['app']) ? $data['app'] : "") . ' ' .
            (isset($data['landing_page']) ? $data['landing_page'] : "") . ' ' .
            (isset($data['store_id']) ? $data['store_id'] : "") . ' ' .
            (isset($data['object_type']) ? $data['object_type'] : "") . ' ' .
            (isset($data['object_id']) ? $data['object_id'] : "");
        $this->cache->delete("widgets.rows.". $prefix);
        $this->cache->delete("widgets.cols.". $prefix);
    }

    public function __get($key) {
        return isset($this->data[$key]) ? $this->data[$key] : null;
    }

    public function __set($key, $value) {
        $this->data[$key] = $value;
    }

    public function __isset($key) {
        return isset($this->data[$key]);
    }

}
