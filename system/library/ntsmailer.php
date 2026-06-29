<?php
/**
 * Backup
 *
 * @package NecoTienda Standalone
 * @author Yosiet Serga
 * @copyright NecoTienda
 * @version 2012
 * @access public
 */

if (file_exists((dirname(__FILE__) .'/email/mailer.php'))) {
    require_once(dirname(__FILE__) . '/email/mailer.php');
    require_once(dirname(__FILE__) . '/email/smtp.php');
} else {
    exit('TCPDF Class is neccesary to use with NTSPDF Class');
}

class ntsMailer {

    public $data = [];
    protected $mailer;
    protected $load;
    protected $config;
    protected $IsHTML;
    protected $IsSMTP;
    protected $IsSendmail;
    protected $IsMail;
    protected $SMTPServer;

    public function __construct($registry) {
        $this->data = $registry;
        $this->mailer = new Mailer();
        $this->load = $registry->get('load');
        $this->config = $registry->get('config');
        
        if ($this->config->get('config_smtp_method') === 'smtp') {
            $this->IsSMTP();
            $this->setSMTPServer($this->config->get('config_mail_server_id'));
        } elseif ($this->config->get('config_smtp_method') === 'sendmail') {
            $this->IsSendmail();
        } else {
            $this->IsMail();
        }
    }

    public function __get($k) {
        return $this->data[$k];
    }

    public function __set($k, $v) {
        return $this->data[$k] = $v;
    }

    public function __isset($k) {
        return isset($this->data[$k]);
    }

    public function Send() {
        $this->mailer->Send();
    }

    public function SetBody($body) {
        if ($this->IsHTML()) {
            $this->mailer->Body = html_entity_decode($body);
        } else {
            $this->mailer->Body = strip_tags(html_entity_decode($body, ENT_QUOTES, 'UTF-8'));
        }
    }

    public function SetSubject($subject = 'NecoTienda Message') {
        $this->mailer->Subject = $subject;
    }

    public function SetFrom($email, $name='NecoTienda App') {
        $this->mailer->SetFrom($email, $name);
    }

    public function AddAddress($email, $name='') {
        $this->mailer->AddAddress($email, $name);
    }

    public function IsHTML($v=null) {
        if (!$v) return $this->IsHTML;
        $this->IsHTML = true;
        $this->mailer->IsHTML(true);
    }

    public function IsSMTP() {
        $this->IsSMTP = true;
        $this->mailer->IsSMTP();
    }

    public function IsSendmail() {
        $this->IsSendmail = true;
        $this->mailer->IsSendmail();
    }

    public function IsMail() {
        $this->IsSendmail = true;
        $this->mailer->IsMail();
    }

    public function setSMTPServer($id) {
        $this->SMTPServer = unserialize($this->config->get('mail_server', $id));
        
        $this->mailer->Host = $this->SMTPServer['server'];
        $this->mailer->Username = $this->SMTPServer['username'];
        $this->mailer->Password = base64_decode($this->SMTPServer['password']);
        $this->mailer->Port = $this->SMTPServer['port'];
        $this->mailer->Timeout = $this->SMTPServer['timeout'];
        $this->mailer->SMTPSecure = $this->SMTPServer['security'];
        $this->mailer->SMTPAuth = ($this->SMTPServer['auth']) ? true : false;
    }

    public function getServer() {
        if (isset($this->SMTPServer['server'])) {
            return $this->SMTPServer['server'];
        } else {
            return 'localhost';
        }
    }

    public function getUser() {
        if (isset($this->SMTPServer['username'])) {
            return $this->SMTPServer['username'];
        } else {
            return '';
        }
    }

    public function getPassword() {
        if (isset($this->SMTPServer['password'])) {
            return $this->SMTPServer['password'];
        } else {
            return '';
        }
    }

    public function getPort() {
        if (isset($this->SMTPServer['port'])) {
            return $this->SMTPServer['port'];
        } elseif ($this->getSecurity()) {
            return 465;
        } else {
            return 25;
        }
    }

    public function getSecurity() {
        if (isset($this->SMTPServer['security'])) {
            return $this->SMTPServer['security'];
        } else {
            return null;
        }
    }

    public function testSMTPConnection() {
        $this->IsSMTP();

        if (!$this->SMTPServer) $this->setSMTPServer($this->config->get('config_mail_server_id'));

        return $this->mailer->SmtpConnect();
    }
}