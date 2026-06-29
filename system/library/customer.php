<?php

final class Customer {

    private $customer_id;
    private $firstname;
    private $lastname;
    private $email;
    private $rif;
    private $company;
    private $photo;
    private $birthday;
    private $blog;
    private $website;
    private $telephone;
    private $profesion;
    private $titulo;
    private $msn;
    private $gmail;
    private $yahoo;
    private $skype;
    private $facebook;
    private $twitter;
    private $complete;
    private $sex;
    private $customer_group_id;
    private $address_id;
    private $key = "946VBA4kg84tbsdowyJF63KGUn4f3mj32nci34JQ53Ejnejs";
    private $registry;
    public $skey; //utilizado para verificar la sesi�n del usuario

    public function __construct($registry) {
        $this->config = $registry->get('config');
        $this->db = $registry->get('db');
        $this->request = $registry->get('request');
        $this->session = $registry->get('session');
        $this->registry = $registry;
        $this->skey = md5($this->session->get('token')) . $this->key . "_" . $this->getId();
        //if ($this->validSession()) { 
        $customer_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer WHERE customer_id = '" . (int) $this->session->get('customer_id') . "' AND status = '1'");

        if ($customer_query->num_rows) {
            $this->customer_id = $customer_query->row['customer_id'];
            $this->firstname = $customer_query->row['firstname'];
            $this->lastname = $customer_query->row['lastname'];
            $this->email = $customer_query->row['email'];
            $this->rif = $customer_query->row['rif'];
            $this->company = $customer_query->row['company'];
            $this->photo = $customer_query->row['photo'];
            $this->birthday = $customer_query->row['birthday'];
            $this->blog = $customer_query->row['blog'];
            $this->website = $customer_query->row['website'];
            $this->telephone = $customer_query->row['telephone'];
            $this->profesion = $customer_query->row['profesion'];
            $this->titulo = $customer_query->row['titulo'];
            $this->msn = $customer_query->row['msn'];
            $this->gmail = $customer_query->row['gmail'];
            $this->yahoo = $customer_query->row['yahoo'];
            $this->skype = $customer_query->row['skype'];
            $this->facebook = $customer_query->row['facebook'];
            $this->twitter = $customer_query->row['twitter'];
            $this->complete = $customer_query->row['complete'];
            $this->sex = $customer_query->row['sex'];
            $this->customer_group_id = $customer_query->row['customer_group_id'];
            $this->address_id = $customer_query->row['address_id'];

            $this->skey = $this->session->get('skey');
        } else {
            $this->logout();
        }
        //}
    }

    public function validSession() {
        if (!$this->session->has('customer_id') || !$this->session->has('skey') || ($this->session->get('skey') != $this->skey))
            return false;
        //TODO: verificar la llave de la sesi�n del usuario
        $customer_id = substr($this->session->get('skey'), strpos($this->session->get('skey'), '_') + 1);
        if ($this->session->get('customer_id') != $customer_id)
            return false;
        return true;
    }

    public function setRefCustomer($email) {
        $customer_ref = $this->getByEmail($email);
        $this->session->set('ref_email', $email); // email of the current visitor
        if ($customer_ref) {
            $this->session->set('ref_cid', $customer_ref['customer_id']);
        } else {
            $this->session->set('ref_cid', 0);
        }
    }

    public function setRefByCustomer($email) {
        $customer_ref = $this->getByEmail($email);
        $this->session->set('refby_email', $email); // email of the current visitor
        if ($customer_ref) {
            $this->session->set('refby_cid', $customer_ref['customer_id']);
        } else {
            $this->session->set('refby_cid', 0);
        }
    }

    private function getByEmail($email) {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer WHERE email = '" . $this->db->escape($email) . "'");
        return $query->row;
    }

    public function login($email, $password, $hash = true) {
        if (empty($email) || empty($password))
            return false;

        if ($hash) {
            $password = md5($password);
        }

        $query = $this->db->query("SELECT `password` FROM " . DB_PREFIX . "customer WHERE `email` = '" . $this->db->escape($email) . "'");

        list($pass, $suffix) = explode(':', $query->row['password']);

        if (!$suffix) {
            $suffix = md5(base_convert(rand(10e16, 10e20), 10, 36) . time());
            $this->db->query("UPDATE " . DB_PREFIX . "customer SET 
              `password`    = '" . $this->db->escape(md5($password . $suffix) . ':' . $suffix) . "' 
              WHERE `email` = '" . $this->db->escape($email) . "'");
        }

        if (!$this->config->get('config_customer_approval')) {
            $customer_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer WHERE 
            email = '" . $this->db->escape($email) . "' 
            AND password = '" . $this->db->escape(md5($password . $suffix) . ':' . $suffix) . "' 
            AND status = '1'
            AND banned = '0'");
        } else {
            $customer_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer WHERE 
            email = '" . $this->db->escape($email) . "' 
            AND password = '" . $this->db->escape(md5($password . $suffix) . ':' . $suffix) . "' 
            AND status = '1' 
            AND banned = '0'
            AND approved = '1'");
        }

        if ($customer_query->num_rows) {
            $this->loginProcess($customer_query);
            return true;
        } else {
            return false;
        }
    }

    public function loginWithGoogle($data) {
        if (empty($data['google_oauth_id']) || empty($data['email']))
            return false;
        $sql = "SELECT * "
        ."FROM " . DB_PREFIX . "customer "
        ."WHERE email = '" . $this->db->escape($data['email']) . "' "
            //. "AND google_oauth_id = '" . $this->db->escape($data['google_oauth_id']) . "' "
            . "AND status = '1' "
            . "AND banned = '0'";

        $customer_query = $this->db->query($sql);

        if ($customer_query->num_rows) {
            if (!$customer_query->row['google_oauth_id']) {
                $customer_query = $this->db->query("UPDATE " . DB_PREFIX . "customer SET "
                        . "`google_oauth_id`    = '" . $this->db->escape($data['google_oauth_id']) . "', "
                        . "`google_oauth_token` = '" . $this->db->escape($data['google_oauth_token']) . "', "
                        . "`google_oauth_refresh` = '" . $this->db->escape($data['google_oauth_refresh']) . "', "
                        . "`google_code` = '" . $this->db->escape($data['google_code']) . "' "
                        . "WHERE email = '" . $this->db->escape($data['email']) . "' ");
            }
            if (!$customer_query->row['photo'] && isset($data['photo'])) {
                $customer_query = $this->db->query("UPDATE " . DB_PREFIX . "customer SET "
                        . "`photo` = '" . $this->db->escape($data['photo']) . "' "
                        . "WHERE email = '" . $this->db->escape($data['email']) . "' ");
            }
            $this->loginProcess($customer_query);
            return true;
        } else {
            return false;
        }
    }

    public function loginWithLive($data) {
        if (empty($data['live_oauth_id']) || empty($data['email']))
            return false;
        $sql = "SELECT * "
        ."FROM " . DB_PREFIX . "customer "
        ."WHERE email = '" . $this->db->escape($data['email']) . "' "
            //. "AND live_oauth_id = '" . $this->db->escape($data['live_oauth_id']) . "' "
            . "AND status = '1' "
            . "AND banned = '0'";

        $customer_query = $this->db->query($sql);

        if ($customer_query->num_rows) {
            if (!$customer_query->row['live_oauth_id']) {
                $customer_query = $this->db->query("UPDATE " . DB_PREFIX . "customer SET "
                        . "`photo`       = '" . $this->db->escape($data['photo']) . "', "
                        . "`live_oauth_id`    = '" . $this->db->escape($data['live_oauth_id']) . "', "
                        . "`live_oauth_token` = '" . $this->db->escape($data['live_oauth_token']) . "', "
                        //. "`live_oauth_refresh` = '" . $this->db->escape($data['live_oauth_refresh']) . "', "
                        . "`live_code` = '" . $this->db->escape($data['live_code']) . "' "
                        . "WHERE email = '" . $this->db->escape($data['email']) . "' ");
            }
            if (!$customer_query->row['photo'] && isset($data['photo'])) {
                $customer_query = $this->db->query("UPDATE " . DB_PREFIX . "customer SET "
                        . "`photo` = '" . $this->db->escape($data['photo']) . "' "
                        . "WHERE email = '" . $this->db->escape($data['email']) . "' ");
            }
            $this->loginProcess($customer_query);
            return true;
        } else {
            return false;
        }
    }

    public function loginWithTwitter($data) {
        $customer_query = $this->db->query("SELECT * 
        FROM " . DB_PREFIX . "customer 
        WHERE twitter_oauth_id = '" . $this->db->escape($data['oauth_id']) . "' 
        AND twitter_oauth_token_secret = '" . $this->db->escape($data['oauth_token_secret']) . "' 
        AND company = '" . $this->db->escape($data['company']) . "' 
        AND status = '1'
        AND banned = '0'");

        if ($customer_query->num_rows) {
            $this->loginProcess($customer_query);
            return true;
        } else {
            return false;
        }
    }

    public function loginWithFacebook($data) {
        if (empty($data['facebook_oauth_id']) || empty($data['email']))
            return false;
        $sql = "SELECT * "
                . "FROM " . DB_PREFIX . "customer "
                . "WHERE email = '" . $this->db->escape($data['email']) . "' "
                //. "AND facebook_oauth_id = '" . $this->db->escape($data['facebook_oauth_id']) . "' "
                . "AND status = '1' "
                . "AND banned = '0'";

        $customer_query = $this->db->query($sql);

        if ($customer_query->num_rows) {
            if (!$customer_query->row['facebook_oauth_id']) {
                $customer_query = $this->db->query("UPDATE " . DB_PREFIX . "customer SET "
                        . "`facebook_oauth_id`    = '" . $this->db->escape($data['facebook_oauth_id']) . "', "
                        . "`facebook_oauth_token` = '" . $this->db->escape($data['facebook_oauth_token']) . "', "
                        //. "`facebook_oauth_refresh` = '" . $this->db->escape($data['facebook_oauth_refresh']) . "', "
                        . "`facebook_code` = '" . $this->db->escape($data['facebook_code']) . "' "
                        . "WHERE email = '" . $this->db->escape($data['email']) . "' ");
            }
            if (!$customer_query->row['photo'] && $data['photo']) {
                $customer_query = $this->db->query("UPDATE " . DB_PREFIX . "customer SET "
                        . "`photo` = '" . $this->db->escape($data['photo']) . "' "
                        . "WHERE email = '" . $this->db->escape($data['email']) . "' ");
            }
            $this->loginProcess($customer_query);
            return true;
        } else {
            return false;
        }
    }

    public function loginWithMeli($data) {
        if (empty($data['meli_oauth_id']) || empty($data['email']))
            return false;echo __LINE__ .' - '. __FILE__.'<br />';
        $sql = "SELECT * "
                . "FROM " . DB_PREFIX . "customer "
                . "WHERE email = '" . $this->db->escape($data['email']) . "' "
                . "AND status = '1' "
                . "AND banned = '0'";
echo __LINE__ .' - '. __FILE__.'<br />';
        $customer_query = $this->db->query($sql);
echo __LINE__ .' - '. __FILE__.'<br />';
        if ($customer_query->num_rows) {echo __LINE__ .' - '. __FILE__.'<br />';
        /*
            $this->db->query("DELETE FROM " . DB_PREFIX . "customer_property WHERE `group` = 'meli' AND customer_id = '". (int)$customer_query['customer_id'] ."'");echo __LINE__ .' - '. __FILE__.'<br />';
            $this->db->query("INSERT INTO " . DB_PREFIX . "customer_property SET "
                    . "`customer_id`    = '" . (int)$customer_query['customer_id'] . "', "
                    . "`group` = 'meli', "
                    . "`key` = 'meli_oauth_id', "
                    . "`value` = '" . serialize($this->db->escape($data['meli_oauth_id'])) . "'");
                 echo __LINE__ .' - '. __FILE__.'<br />';   
            $this->db->query("INSERT INTO " . DB_PREFIX . "customer_property SET "
                    . "`customer_id`    = '" . (int)$customer_query['customer_id'] . "', "
                    . "`group` = 'meli', "
                    . "`key` = 'meli_oauth_token', "
                    . "`value` = '" . serialize($this->db->escape($data['meli_oauth_token'])) . "'");
                    echo __LINE__ .' - '. __FILE__.'<br />';
            $this->db->query("INSERT INTO " . DB_PREFIX . "customer_property SET "
                    . "`customer_id`    = '" . (int)$customer_query['customer_id'] . "', "
                    . "`group` = 'meli', "
                    . "`key` = 'meli_oauth_refresh', "
                    . "`value` = '" . serialize($this->db->escape($data['meli_oauth_refresh'])) . "'");
                    echo __LINE__ .' - '. __FILE__.'<br />';
            $this->db->query("INSERT INTO " . DB_PREFIX . "customer_property SET "
                    . "`customer_id`    = '" . (int)$customer_query['customer_id'] . "', "
                    . "`group` = 'meli', "
                    . "`key` = 'meli_oauth_expire', "
                    . "`value` = '" . serialize($this->db->escape($data['meli_oauth_expire'])) . "'");
                    echo __LINE__ .' - '. __FILE__.'<br />';
            $this->db->query("INSERT INTO " . DB_PREFIX . "customer_property SET "
                    . "`customer_id`    = '" . (int)$customer_query['customer_id'] . "', "
                    . "`group` = 'meli', "
                    . "`key` = 'meli_code', "
                    . "`value` = '" . serialize($this->db->escape($data['meli_code'])) . "'");
                    echo __LINE__ .' - '. __FILE__.'<br />';
                    */
            $this->loginProcess($customer_query);echo __LINE__ .' - '. __FILE__.'<br />';
            return true;
        } else {echo __LINE__ .' - '. __FILE__.'<br />';
            return false;
        }
    }

    protected function loginProcess($customer_query) {
        $this->session->set('customer_id', $customer_query->row['customer_id']);
        $tk = $this->session->has('token') ? $this->session->get('token') : strtotime(date('d-m-Y h:i:s')) . mt_rand(1000000000, 9999999999);
        $this->session->set('token', $tk);
        $this->skey = md5($this->session->get('token')) . $this->key . "_" . $customer_query->row['customer_id'];
        $this->session->set('skey', $this->skey);

        if (($customer_query->row['cart']) && (is_string($customer_query->row['cart']))) {
            $cart = unserialize($customer_query->row['cart']);

            foreach ($cart as $key => $value) {
                if (!array_key_exists($key, $this->session->get('cart'))) {
                    $this->session->data['cart'][$key] = $value;
                } else {
                    $this->session->data['cart'][$key] += $value;
                }
            }
        }

        $this->customer_id = $customer_query->row['customer_id'];
        $this->firstname = $customer_query->row['firstname'];
        $this->lastname = $customer_query->row['lastname'];
        $this->email = $customer_query->row['email'];
        $this->rif = $customer_query->row['rif'];
        $this->company = $customer_query->row['company'];
        $this->photo = $customer_query->row['photo'];
        $this->birthday = $customer_query->row['birthday'];
        $this->blog = $customer_query->row['blog'];
        $this->website = $customer_query->row['website'];
        $this->profesion = $customer_query->row['profesion'];
        $this->titulo = $customer_query->row['titulo'];
        $this->msn = $customer_query->row['msn'];
        $this->gmail = $customer_query->row['gmail'];
        $this->yahoo = $customer_query->row['yahoo'];
        $this->skype = $customer_query->row['skype'];
        $this->facebook = $customer_query->row['facebook'];
        $this->twitter = $customer_query->row['twitter'];
        $this->complete = $customer_query->row['complete'];
        $this->telephone = $customer_query->row['telephone'];
        $this->sex = $customer_query->row['sex'];
        $this->newsletter = $customer_query->row['newsletter'];
        $this->customer_group_id = $customer_query->row['customer_group_id'];
        $this->address_id = $customer_query->row['address_id'];
    }

    public function logout() {
        $this->session->clear('customer_id');
        $this->session->clear('skey');
        $this->session->clear('token');
        $this->session->clear('ltoken');
        $this->session->clear('lcode');
        $this->session->clear('liveAccessToken');
        $this->session->clear('action');
        $this->session->clear('gtoken');
        $this->session->clear('gcode');
        //$this->google->revokeToken($this->google->getAccessToken()); //TODO: declarar esta variable en el index

        $this->customer_id = '';
        $this->firstname = '';
        $this->lastname = '';
        $this->email = '';
        $this->rif = '';
        $this->company = '';
        $this->photo = '';
        $this->birthday = '';
        $this->blog = '';
        $this->website = '';
        $this->profesion = '';
        $this->titulo = '';
        $this->msn = '';
        $this->gmail = '';
        $this->yahoo = '';
        $this->skype = '';
        $this->facebook = '';
        $this->twitter = '';
        $this->telephone = '';
        $this->newsletter = '';
        $this->customer_group_id = '';
        $this->address_id = '';
    }

    public function isLogged() {
        return $this->customer_id;
    }

    public function activateUser($codigo) {
        $result = $this->db->query("UPDATE " . DB_PREFIX . "customer SET approved = '1' WHERE codigo = '" . $this->db->escape($codigo) . "'");
        return $result;
    }

    public function setComplete() {
        $result = $this->db->query("UPDATE " . DB_PREFIX . "customer SET complete= '1' WHERE customer_id = '" . (int) $this->getId() . "'");
        return $result;
    }

    public function getId() {
        return $this->customer_id;
    }

    public function getFirstName() {
        return $this->firstname;
    }

    public function getLastName() {
        return $this->lastname;
    }

    public function getEmail() {
        return $this->email;
    }

    public function getRif() {
        return $this->rif;
    }

    public function getCompany() {
        return $this->company;
    }

    public function getFoto() {
        return $this->photo;
    }

    public function getNacimiento() {
        return $this->birthday;
    }

    public function getBlog() {
        return $this->blog;
    }

    public function getWebsite() {
        return $this->website;
    }

    public function getProfesion() {
        return $this->profesion;
    }

    public function getTitulo() {
        return $this->titulo;
    }

    public function getMsn() {
        return $this->msn;
    }

    public function getGmail() {
        return $this->gmail;
    }

    public function getYahoo() {
        return $this->yahoo;
    }

    public function getSkype() {
        return $this->skype;
    }

    public function getFacebook() {
        return $this->facebook;
    }

    public function getTwitter() {
        return $this->twitter;
    }

    public function getComplete() {
        return $this->complete;
    }

    public function getTelephone() {
        return $this->telephone;
    }

    public function emailExist($email) {
        $correo = $this->db->query("SELECT * FROM `" . DB_PREFIX . "customer` WHERE email = '" . $this->db->escape($email) . "'");
        return $correo;
    }

    public function getNewsletter() {
        return $this->newsletter;
    }

    public function getCustomerGroupId() {
        return $this->customer_group_id;
    }

    public function getAddressId() {
        return $this->address_id;
    }

}
