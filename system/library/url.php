<?php

class Url {

    private static $db;
    private static $config;
    private static $customer;

    public function __construct($registry = null) {
        if ($registry) {
            self::$db = $registry->get('db');
            self::$config = $registry->get('config');
            self::$customer = $registry->get('customer');
        }
    }

    static public function createUrl($route, $params = null, $connection = 'NONSSL', $base = null) {
        if (empty($route))
            return false;

        if ($route === 'common/home') {
            if (isset($base)) {
                $url = $base;
            } else {
                $url = ($connection == 'SSL') ? HTTPS_HOME : HTTP_HOME;
            }
        } else {
            if (isset($base)) {
                $url = $base . "index.php?r=" . $route;
            } else {
                $url = ($connection == 'SSL') ? HTTPS_HOME . "index.php?r=" . $route : HTTP_HOME . "index.php?r=" . $route;
            }
        }
        
        if (isset($params)) {
            if (is_array($params)) {
                foreach ($params as $key => $value) {
                    if (empty($key)) continue;
                    $url .= "&" . trim($key) . "=" . trim($value);
                }
            } else {
                $url .= trim("&" . $params);
            }
        }
        
        // para habilitar el editor de temas en todas las url
        if (isset($_GET['theme_editor'])) {
            $url .= "&theme_editor=1";
        }

        if (isset($_GET['theme_id'])) {
            $url .= "&theme_id=" . urlencode(trim((int) $_GET['theme_id']));
        }

        if (isset($_GET['template'])) {
            $url .= "&template=" . urlencode(trim($_GET['template']));
        }
        
        if (isset(self::$config) && isset(self::$db)) {
            if (isset(self::$customer)) {
                if (self::$customer->isLogged()) {
                    $str = self::$customer->getFirstName() . self::$customer->getLastName();
                    if (empty($str)) {
                        $str = self::$customer->getCompany();
                    }
                    if ($str !== mb_convert_encoding(mb_convert_encoding($str, 'UTF-32', 'UTF-8'), 'UTF-8', 'UTF-32'))
                        $str = mb_convert_encoding($str, 'UTF-8', mb_detect_encoding($str));
                    $str = htmlentities($str, ENT_NOQUOTES, 'UTF-8');
                    $str = preg_replace('`&([a-z]{1,2})(acute|uml|circ|grave|ring|cedil|slash|tilde|caron|lig);`i', '\1', $str);
                    $str = html_entity_decode($str, ENT_NOQUOTES, 'UTF-8');
                    $str = preg_replace(array('`[^a-z0-9]`i', '`[-]+`'), '-', $str);
                    $str = strtolower(trim($str, '-'));
                    $profile = $str;
                } else {
                    $profile = 'profile';
                }
            }
            
            if (self::$config->get('config_seo_url') && $route !== 'common/home') {
                $url_data = parse_url(str_replace('&amp;', '&', $url));
                $url_ = '';
                $data = [];
                parse_str($url_data['query'], $data);
                $d = $data;
                foreach ($data as $key => $value) {
                    if (!isset($data['r'])) continue;
                    if (($key == 'product_id' && $data['r'] == 'store/product') ||
                    ($key == 'category_id' && $data['r'] == 'store/category') ||
                    ($key == 'manufacturer_id' && $data['r'] == 'store/manufacturer') ||
                    ($key == 'post_id' && $data['r'] == 'content/post') ||
                    ($key == 'category_id' && $data['r'] == 'content/category')) {
                        $query = self::$db->query("SELECT * FROM `" . DB_PREFIX . "url_alias` WHERE `query` = '" . self::$db->escape($key . '=' . (int) $value) . "' AND `language_id` = '". (int)self::$config->get('config_language_id') ."'");
                        if ($query->num_rows) {
                            $url_ .= '/' . $query->row['keyword'];
                            unset($data[$key]);
                        }
                    } elseif ($key == 'path' && $data['r'] == 'store/category') {
                        $categories = explode('_', $value);
                        $cid = array_pop($categories);
                        $qry = self::$db->query("SELECT * FROM " . DB_PREFIX . "url_alias WHERE `query` = 'category_id=" . (int) $cid . "' AND language_id = '". (int)self::$config->get('config_language_id') ."'");

                        if ($qry->num_rows) {
                            foreach ($categories as $category) {
                                $query = self::$db->query("SELECT * FROM " . DB_PREFIX . "url_alias WHERE `query` = 'category_id=" . (int) $category . "' AND language_id = '". (int)self::$config->get('config_language_id') ."'");

                                if ($query->num_rows) {
                                    $url_ .= '/' . $query->row['keyword'];
                                }
                            }
                            $url_ .= '/' . $qry->row['keyword'];
                            unset($data[$key]);
                        }
                    } elseif ($key == 'path' && $data['r'] == 'content/category') {
                        $categories = explode('_', $value);

                        $cid = array_pop($categories);
                        $qry = self::$db->query("SELECT * FROM " . DB_PREFIX . "url_alias WHERE `query` = 'category_id=" . (int) $cid . "' AND language_id = '". (int)self::$config->get('config_language_id') ."'");

                        if ($qry->num_rows) {
                            foreach ($categories as $category) {
                                $query = self::$db->query("SELECT * FROM " . DB_PREFIX . "url_alias WHERE `query` = 'category_id=" . (int) $category . "' AND language_id = '". (int)self::$config->get('config_language_id') ."'");

                                if ($query->num_rows) {
                                    $url_ .= '/' . $query->row['keyword'];
                                }
                            }
                            unset($data[$key]);
                        }
                    } elseif ($key == 'page_id' && $data['r'] == 'content/page') {
                        $pages = explode('_', $value);
                        foreach ($pages as $page) {
                            $query = self::$db->query("SELECT * FROM " . DB_PREFIX . "url_alias WHERE `query` = 'page_id=" . (int) $page . "' AND language_id = '". (int)self::$config->get('config_language_id') ."'");

                            if ($query->num_rows) {
                                $url_ .= '/' . $query->row['keyword'];
                            }
                        }
                        unset($data[$key]);
                    } elseif ($data['r'] == 'api/facebook') {
                        $url_ .= '/api/facebook';
                        unset($data[$key]);
                    } elseif ($data['r'] == 'api/twitter') {
                        $url_ .= '/api/twitter';
                        unset($data[$key]);
                    } elseif ($data['r'] == 'api/google') {
                        $url_ .= '/api/google';
                        unset($data[$key]);
                    } elseif ($data['r'] == 'api/live') {
                        $url_ .= '/api/live';
                        unset($data[$key]);
                    } elseif ($data['r'] == 'page/sitemap') {
                        $url_ .= '/sitemap';
                        unset($data[$key]);
                    } elseif ($data['r'] == 'store/special') {
                        $url_ .= '/ofertas';
                        unset($data[$key]);
                    } elseif (!isset($d['category_id']) && $data['r'] == 'content/category') {
                        $url_ .= '/blog';
                        unset($data[$key]);
                    } elseif (!isset($d['post_id']) && $data['r'] == 'content/post/all') {
                        $url_ .= '/posts';
                        unset($data[$key]);
                    } elseif (!isset($d['page_id']) && $data['r'] == 'content/page/all') {
                        $url_ .= '/paginas';
                        unset($data[$key]);
                    } elseif (!isset($d['product_id']) && $data['r'] == 'store/product/all') {
                        $url_ .= '/productos';
                    } elseif (!isset($d['manufacturer_id']) && $data['r'] == 'store/manufacturer/all') {
                        $url_ .= '/fabricantes';
                        unset($data[$key]);
                    } elseif (!isset($d['path']) && $data['r'] == 'store/category/all') {
                        $url_ .= '/categorias';
                        unset($data[$key]);
                    } elseif ($data['r'] == 'store/search') {
                        $url_ .= '/buscar';
                        unset($data[$key]);
                    } elseif ($data['r'] == 'account/login') {
                        $url_ .= "/login";
                        unset($data[$key]);
                    } elseif ($data['r'] == 'account/register') {
                        $url_ .= "/register";
                        unset($data[$key]);
                    } elseif ($data['r'] == 'account/order') {
                        $url_ .= "/$profile/pedidos";
                        unset($data[$key]);
                    } elseif ($data['r'] == 'account/payment') {
                        $url_ .= "/$profile/pagos";
                        unset($data[$key]);
                    } elseif ($data['r'] == 'account/message') {
                        $url_ .= "/$profile/mensajes";
                        unset($data[$key]);
                    } elseif ($data['r'] == 'account/account') {
                        $url_ .= "/$profile";
                        unset($data[$key]);
                    } elseif ($data['r'] == 'account/review') {
                        $url_ .= "/$profile/comentarios";
                        unset($data[$key]);
                    } elseif ($data['r'] == 'checkout/cart') {
                        $url_ .= '/carrito';
                        unset($data[$key]);
                    }
                }
                
                if ($url_) {
                    
                    unset($data['r']);
                    $query = '';
                    if ($data) {
                        foreach ($data as $key => $value) {
                            $query .= '&' . $key . '=' . $value;
                        }
                        if ($query) {
                            $query = '?' . trim($query, '&');
                        }
                    }
                    //return $url_data['scheme'] . '://' . $url_data['host'] . (isset($url_data['port']) ? ':' . $url_data['port'] : '') . str_replace(array('/web','/index.php'), '', $url_data['path']) . $url_ . $query;
                    return $url_data['scheme'] . '://' . $url_data['host'] . (isset($url_data['port']) ? ':' . $url_data['port'] : '') . str_replace('/index.php', '', $url_data['path']) . $url_ . $query;
                } else {
                    
                    //return str_replace(array('/web'), '', $url);
                    return $url;
                }
                
            }
            
        }
        
        return $url;
    }

    static public function rewrite($url) {
        // para habilitar el editor de temas en todas las url
        if (isset($_GET['theme_editor'])) {
            $url .= "&theme_editor=1";
        }

        if (isset($_GET['theme_id'])) {
            $url .= "&theme_id=" . urlencode(trim((int) $_GET['theme_id']));
        }

        if (isset($_GET['template'])) {
            $url .= "&template=" . urlencode(trim($_GET['template']));
        }

        if (isset(self::$config) && isset(self::$db)) {
            if (isset(self::$customer)) {
                if (self::$customer->isLogged()) {
                    $str = self::$customer->getFirstName() . self::$customer->getLastName();
                    if (empty($str)) {
                        $str = self::$customer->getCompany();
                    }
                    if ($str !== mb_convert_encoding(mb_convert_encoding($str, 'UTF-32', 'UTF-8'), 'UTF-8', 'UTF-32'))
                        $str = mb_convert_encoding($str, 'UTF-8', mb_detect_encoding($str));
                    $str = htmlentities($str, ENT_NOQUOTES, 'UTF-8');
                    $str = preg_replace('`&([a-z]{1,2})(acute|uml|circ|grave|ring|cedil|slash|tilde|caron|lig);`i', '\1', $str);
                    $str = html_entity_decode($str, ENT_NOQUOTES, 'UTF-8');
                    $str = preg_replace(array('`[^a-z0-9]`i', '`[-]+`'), '-', $str);
                    $str = strtolower(trim($str, '-'));
                    $profile = $str;
                } else {
                    $profile = 'profile';
                }
            }
            if (self::$config->get('config_seo_url')) {
                $url_data = parse_url(str_replace('&amp;', '&', $url));
                $url_ = '';
                $data = [];
                parse_str($url_data['query'], $data);
                foreach ($data as $key => $value) {
                    if (
                        ($key == 'product_id' && $data['r'] == 'store/product') ||
                        ($key == 'category_id' && $data['r'] == 'store/category') ||
                        ($key == 'manufacturer_id' && $data['r'] == 'store/manufacturer') ||
                        ($key == 'post_id' && $data['r'] == 'content/post') ||
                        ($key == 'category_id' && $data['r'] == 'content/category')
                    ) {

                        $query = self::$db->query("SELECT * FROM " . DB_PREFIX . "url_alias WHERE `query` = '" . self::$db->escape($key . '=' . (int) $value) . "' AND language_id = '". (int)self::$config->get('config_language_id') ."'");
                        if ($query->num_rows) {
                            $url_ .= '/' . $query->row['keyword'];
                            unset($data[$key]);
                        }
                    } elseif ($key == 'path' && $data['r'] == 'store/category') {
                        $categories = explode('_', $value);
                        foreach ($categories as $category) {
                            $query = self::$db->query("SELECT * FROM " . DB_PREFIX . "url_alias WHERE `query` = 'category_id=" . (int) $category . "' AND language_id = '". (int)self::$config->get('config_language_id') ."'");

                            if ($query->num_rows) {
                                $url_ .= '/' . $query->row['keyword'];
                            }
                        }
                        unset($data[$key]);
                    } elseif ($key == 'path' && $data['r'] == 'content/category') {
                        $categories = explode('_', $value);
                        foreach ($categories as $category) {
                            $query = self::$db->query("SELECT * FROM " . DB_PREFIX . "url_alias WHERE `query` = 'category_id=" . (int) $category . "' AND language_id = '". (int)self::$config->get('config_language_id') ."'");

                            if ($query->num_rows) {
                                $url_ .= '/' . $query->row['keyword'];
                            }
                        }
                        unset($data[$key]);
                    } elseif ($key == 'page_id' && $data['r'] == 'content/page') {
                        $pages = explode('_', $value);
                        foreach ($pages as $page) {
                            $query = self::$db->query("SELECT * FROM " . DB_PREFIX . "url_alias WHERE `query` = 'page_id=" . (int) $page . "' AND language_id = '". (int)self::$config->get('config_language_id') ."'");

                            if ($query->num_rows) {
                                $url_ .= '/' . $query->row['keyword'];
                            }
                        }
                        unset($data[$key]);
                    } elseif ($data['r'] == 'page/sitemap') {
                        $url_ .= '/sitemap';
                        unset($data[$key]);
                        unset($data[$key]);
                    } elseif ($data['r'] == 'store/special') {
                        $url_ .= '/ofertas';
                        unset($data[$key]);
                    } elseif ($key != 'category_id' && $data['r'] == 'content/category') {
                        $url_ .= '/blog';
                        unset($data[$key]);
                    } elseif ($key != 'page_id' && $data['r'] == 'content/page/all') {
                        $url_ .= '/paginas';
                        unset($data[$key]);
                    } elseif ($key != 'product_id' && $data['r'] == 'store/product/all') {
                        $url_ .= '/productos';
                        unset($data[$key]);
                    } elseif ($key != 'path' && $data['r'] == 'store/category/all') {
                        $url_ .= '/categorias';
                        unset($data[$key]);
                    } elseif ($data['r'] == 'store/search') {
                        $url_ .= '/buscar';
                        unset($data[$key]);
                    } elseif ($data['r'] == 'account/order') {
                        $url_ .= "/$profile/pedidos";
                        unset($data[$key]);
                    } elseif ($data['r'] == 'account/payment') {
                        $url_ .= "/$profile/pagos";
                        unset($data[$key]);
                    } elseif ($data['r'] == 'account/message') {
                        $url_ .= "/$profile/mensajes";
                        unset($data[$key]);
                    } elseif ($data['r'] == 'account/account') {
                        $url_ .= "/$profile";
                        unset($data[$key]);
                    } elseif ($data['r'] == 'account/review') {
                        $url_ .= "/$profile/comentarios";
                        unset($data[$key]);
                    } elseif ($data['r'] == 'checkout/cart') {
                        $url_ .= '/carrito';
                        unset($data[$key]);
                    }
                }

                if ($url_) {
                    unset($data['r']);
                    $query = '';
                    if ($data) {
                        foreach ($data as $key => $value) {
                            $query .= '&' . $key . '=' . $value;
                        }
                        if ($query) {
                            $query = '?' . trim($query, '&');
                        }
                    }
                    return $url_data['scheme'] . '://' . $url_data['host'] . (isset($url_data['port']) ? ':' . $url_data['port'] : '') . str_replace(array('/web','/index.php'), '', $url_data['path']) . $url_ . $query;
                } else {
                    return $url;
                }
            }
        }
        return $url;
    }

    static public function createAdminUrl($route, $params = array(), $connection = 'NONSSL', $base = null) {
        $token = isset($_SESSION[C_CODE . '_ukey']) ? $_SESSION[C_CODE . '_ukey'] : (isset($_GET['token']) && !empty($_GET["token"]) ? $_GET["token"] : "");
        $params = is_array($params) ? array_merge(array('token' => $token), $params) : '&token=' . $token . $params;
        return self::createUrl($route, $params, $connection, $base);
    }

}
