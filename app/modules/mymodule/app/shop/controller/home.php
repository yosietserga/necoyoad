<?php

class ControllerHome extends Controller {

    public function index() {
        $this->document->title = $this->language->get('heading_title');
        $this->document->breadcrumbs = [];
        $this->document->breadcrumbs[] = array(
            'href' => Url::createAdminUrl('common/home'),
            'text' => $this->language->get('text_home'),
            'separator' => false
        );

        $_12_months_ago = (date('m')-12);
        if ($_12_months_ago == 0) $_12_months_ago = 12;
        if ($_12_months_ago < 0) $_12_months_ago = $_12_months_ago * -1;
        if (strlen($_12_months_ago) < 2) $_12_months_ago = '0'.$_12_months_ago;
        
        $_1_year_ago = ($_12_months_ago == 12) ? date('Y') : date('Y') - 1;

        $this->data['token'] = $this->session->get('ukey');

        $this->data['orders'] = [];

        $data = array(
            'sort' => 'o.date_added',
            'order' => 'DESC',
            'start' => 0,
            'limit' => 10
        );


            $this->template = 'home.php';
            $this->templatePath = dirname(__FILE__) . "/../view/template/";
             var_dump($this->templatePath);


        $this->children[] = 'common/header';
        $this->children[] = 'common/footer';

        $this->response->setOutput($this->render(true), $this->config->get('config_compression'));
    }

    public function ping() {
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . "GMT");
        header("Cache-Control: no-cache, must-revalidate");
        header("Pragma: no-cache");
        header("Content-type: application/json");
        header('HTTP/1.1 200 OK');
        $this->load->library('json');
        $this->response->setOutput(Json::encode(array('msg'=>'OK')), $this->config->get('config_compression'));
    }

    public function login() {
        if (!$this->user->validSession() && ($this->request->getQuery('r') != 'common/login/login') && ($this->request->getQuery('r') != 'common/login/recover')) {
            $this->user->logout();
            return $this->forward('common/login');
        }

        if (isset($this->request->get['r']) && !isset($this->request->get['token'])) {
            $route = '';
            $part = explode('/', $this->request->get['r']);

            if (isset($part[0])) {
                $route .= $part[0];
            }

            if (isset($part[1])) {
                $route .= '/' . $part[1];
            }

            $ignore = array(
                'common/login',
                'common/login/login',
                'common/login/recover',
                'common/login/ping',
                'common/logout',
                'error/not_found',
                'error/permission'
            );

            $config_ignore = [];

            if ($this->config->get('config_token_ignore')) {
                $config_ignore = unserialize($this->config->get('config_token_ignore'));
            }

            $ignore = array_merge($ignore, $config_ignore);

            if (!in_array($route, $ignore)) {
                if (!isset($this->request->get['token']) || !$this->user->validSession() || ($this->request->get['token'] != $this->session->get('ukey'))) {
                    $this->user->logout();
                    return $this->forward('common/login');
                }
            }
        }

        if (substr_count($_SERVER['QUERY_STRING'], 'token') >= 2) {
            foreach ($_GET as $arg => $value) {
                if ($arg == 'token')
                    unset($_GET[$arg]);
            }
            $this->user->logout();
            return $this->forward('common/login');
        }
        if (!isset($this->request->get['token'])) {
            $this->user->logout();
            return $this->forward('common/login');
        }
        if ($this->request->get['token'] != $this->session->get('ukey')) {
            $this->user->logout();
            return $this->forward('common/login');
        }
    }
    
    public function permission() {
        if (isset($this->request->get['r'])) {
            $route = '';

            $part = explode('/', $this->request->get['r']);

            if (isset($part[0])) {
                $route .= $part[0];
            }

            if (isset($part[1])) {
                $route .= '/' . $part[1];
            }

            if (isset($part[2]) && $part[0] == 'module') {
                $route .= '/' . $part[2];
            }

            $ignore = array(
                'common/home',
                'common/login/login',
                'common/login/recover',
                'common/login/ping',
                'common/login',
                'common/logout',
                'error/not_found',
                'error/permission',
                'error/token'
            );

            if (!in_array($route, $ignore)) {
                if (!$this->user->hasPermission('access', $route)) {
                    return $this->forward('error/permission');
                }
            }
        }
    }

    public function slug() {
        $str = $_GET['slug'];
        if (isset($str)) {
            if ($str !== mb_convert_encoding(mb_convert_encoding($str, 'UTF-32', 'UTF-8'), 'UTF-8', 'UTF-32'))
                $str = mb_convert_encoding($str, 'UTF-8', mb_detect_encoding($str));
            $str = htmlentities($str, ENT_NOQUOTES, 'UTF-8');
            $str = preg_replace('`&([a-z]{1,2})(acute|uml|circ|grave|ring|cedil|slash|tilde|caron|lig);`i', '\1', $str);
            $str = html_entity_decode($str, ENT_NOQUOTES, 'UTF-8');
            $str = preg_replace(array('`[^a-z0-9]`i', '`[-]+`'), '-', $str);
            $str = strtolower(trim($str, '-'));

            $avoid = array(
                'profile',
                'products',
                'productos',
                'categories',
                'categorias',
                'carrito',
                'cart',
                'sitemap',
                'contact',
                'contacto',
                'special',
                'ofertas',
                'blog',
                'pages',
                'paginas',
                'buscar',
                'search',
                'pedidos',
                'orders',
                'mensajes',
                'pagos',
                'payments',
                'comentarios',
                'login',
                'logout',
                'reviews'
            );

            if (in_array($str, $avoid)) {
                $str .= '-2';
            }

            $slug = false;
            $count = 2;
            while ($slug === false) {
                $query = $this->db->query("SELECT *, COUNT(*) AS total FROM " . DB_PREFIX . "url_alias WHERE `keyword` = '" . $str . "' AND language_id = '". (int)$_GET['language_id'] ."'");
                if ($query->row['total'] && $query->row['query'] != html_entity_decode($_GET['query'])) {
                    $str .= $count;
                    $count++;
                    $slug = false;
                } else {
                    $slug = true;
                }
            }

            $slug = false;
            $count = 2;
            while ($slug === false) {
                $query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "store WHERE `folder` = '" . $str . "'");
                if ($query->row['total']) {
                    $str .= '-' . $count;
                    $count++;
                    $slug = false;
                } else {
                    $slug = true;
                }
            }

            $return['slug'] = $str;
        } else {
            $return['error'] = 1;
        }
        $this->load->auto('json');
        print Json::encode($return);
    }

    public function loadiframe() {
        $url = $this->request->getQuery('url');
        $this->load->library('xhttp/xhttp');

        $resp = xhttp::fetch( urldecode($url) );
        if (!$resp['body']) {
            echo file_get_contents( urldecode($url) );
        } else {
            echo $resp['body'];
        }
    }
}
