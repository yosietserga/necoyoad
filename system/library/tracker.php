<?php

class Tracker {

    public $data = [];

    private $load;
    private $db;
    private $config;
    private $session;
    private $customer;

    public function __construct($registry) {
        $this->load = $registry->get('load');
        $this->db = $registry->get('db');
        $this->config = $registry->get('config');
        $this->session = $registry->get('session');
        $this->customer = $registry->get('customer');
    }

    /**
     * Tracker::track()
     * registra la visita del usuario
     * @var string $key el nombre de la variable
     * @var mixed $value el valor de la variable
     * @return void
     * */
    public function track($object_id=null, $object_type=null) {
        if (!$this->config->get('track_visits')) {
            $customer_id = ((int)$this->session->get('ref_cid')) ? $this->session->get('ref_cid') : $this->customer->getId();
            $email = ($this->session->get('ref_email')) ? $this->session->get('ref_email') : $this->customer->getEmail();

            $query = $this->db->query("SELECT * FROM `". DB_PREFIX ."contact` WHERE `email` = '". $this->db->escape($email) ."'");
            if (!$query->row['contact_id']) {
                $this->db->query("INSERT INTO `". DB_PREFIX ."contact` SET
                `email`         = '". $this->db->escape($email) ."',
                `customer_id`   = '" . (int)$customer_id . "',
                `date_added`    = NOW()");
            }

            $this->load->library('browser');
            $browser = new Browser;
            
            $this->db->query("INSERT INTO " . DB_PREFIX . "stat SET ".
            "`object_id`     = '" . (int)$object_id . "', ".
            "`store_id`      = '" . (int)STORE_ID . "', ".
            "`customer_id`   = '" . (int)$customer_id . "', ".
            "`object_type`   = '" . $this->db->escape($object_type) . "', ".
            "`email`         = '" . $this->db->escape($email) . "', ".
            "`server`        = '" . $this->db->escape(serialize($_SERVER)) . "', ".
            "`session`       = '" . $this->db->escape(serialize($_SESSION)) . "', ".
            "`request`       = '" . $this->db->escape(serialize($_REQUEST)) . "', ".
            "`store_url`     = '" . $this->db->escape($_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI']) . "', ".
            "`ref`           = '" . $this->db->escape($_SERVER['HTTP_REFERER']??"") . "', ".
            "`browser`       = '" . $this->db->escape($browser->getBrowser()) . "', ".
            "`browser_version`= '" . $this->db->escape($browser->getVersion()) . "', ".
            "`os`            = '" . $this->db->escape($browser->getPlatform()) . "', ".
            "`ip`            = '" . $this->db->escape($_SERVER['REMOTE_ADDR']) . "', ".
            "`date_added`    = NOW()");
        }
    }
}
