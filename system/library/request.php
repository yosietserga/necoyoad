<?php

final class Request {

    public $get = [];
    public $post = [];
    public $cookie = [];
    public $files = [];
    public $server = [];

    private string $method = 'aes-256-ctr';

    public function __construct() {
        $_GET = $this->clean($_GET);
        $_POST = $this->clean($_POST);
        $_REQUEST = $this->clean($_REQUEST);
        $_COOKIE = $this->clean($_COOKIE);
        $_FILES = $this->clean($_FILES);
        $_SERVER = $this->clean($_SERVER);

        $this->get = $_GET;
        $this->post = $_POST;
        $this->request = $_REQUEST;
        $this->cookie = $_COOKIE;
        $this->files = $_FILES;
        $this->server = $_SERVER;
    }

    public function clean($data) {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                unset($data[$key]);

                $data[$this->clean($key)] = $this->clean($value);
            }
        } else {
            $data = htmlspecialchars($data, ENT_COMPAT);
        }

        return $data;
    }

    public function setQuery($key, $value) {
        $this->get[$key] = $value;
    }

    public function getQuery($key) {
        return ($this->hasQuery($key)) ? trim($this->get[$key]) : null;
    }

    public function hasQuery($key) {
        return !empty($this->get[$key]);
    }

    public function setCookie($key, $value) {
        $_key = md5(CRYPT_KEY . $key);
        $_value = $this->encrypt_string($value);
        $this->cookie[$_key] = $_value;

        // Save in cookie using cookie defaults
        setcookie($_key, $_value, time() + 60 * 60 * 24 * 30, '/', $this->server['HTTP_HOST']);
    }

    public function getCookie($key) {
        $_key = md5(CRYPT_KEY . $key);
        return $this->decrypt_string( $this->cookie[$_key] );
    }

    public function hasCookie($key) {
        $_key = md5(CRYPT_KEY . $key);
        return isset($this->cookie[$_key]);
    }

    public function setPost($key, $value) {
        $this->post[$key] = $value;
    }

    public function getPost($key=false) {
        if ($key !== false && !empty($key)) return $this->post[$key];
        else return $this->post;
    }

    public function hasPost($key) {
        return isset($this->post[$key]);
    }

    public function encrypt_string($message) {
        $nonceSize = openssl_cipher_iv_length($this->method);
        $nonce = openssl_random_pseudo_bytes($nonceSize);

        try {
            $ciphertext = openssl_encrypt(
                $message,
                $this->method,
                CRYPT_KEY,
                OPENSSL_RAW_DATA,
                $nonce
            );

            $sendEncrypt=$nonce.'&&'.$ciphertext;
         
            if(!$ciphertext) throw new Exception('Unable To Encrypt Data');

            return base64_encode($sendEncrypt);
        } catch(Exception $e) {
            throw new Exception($e->getMessage());
        }

    }

    public function decrypt_string($message) {
        try{
            $message = base64_decode($message, true);
            if ($message === false) {
                throw new Exception('Unable To Decrypt Data');
            } else {
                list($nonce, $ciphertext) = explode('&&', $message);
                return openssl_decrypt(
                    $ciphertext,
                    $this->method,
                    CRYPT_KEY,
                    OPENSSL_RAW_DATA,
                    $nonce
                );
            }
        } catch(Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
}
