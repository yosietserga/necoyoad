<?php

final class Session {

    public $data = [];

    public function __construct() {

        if (!is_writable(session_save_path())) {
            if (!is_dir(DIR_SESSION)) @mkdir(DIR_SESSION, 0755, true);
            @ini_set('session.save_path', realpath(DIR_SESSION));
        }

        if (!session_id() || session_status() !== PHP_SESSION_ACTIVE) {
            header("Set-Cookie: nts_token=". mt_rand() ."; expires=Tue, 06-Jan-" . (date('Y') + 1) . " 23:39:49 GMT; path=/; domain=" . substr($_SERVER['SERVER_NAME'], strpos($_SERVER['SERVER_NAME'], "."), 100));
            ini_set('session.use_cookies', 'On');
            ini_set('session.use_trans_sid', 'Off');
            ini_set('session.cookie_domain', substr($_SERVER['SERVER_NAME'], strpos($_SERVER['SERVER_NAME'], ".") + 1, 100));
            session_set_cookie_params(0, '/', substr($_SERVER['SERVER_NAME'], strpos($_SERVER['SERVER_NAME'], ".") + 1, 100), true, true);
            session_start();
        }
        
        if (!is_writable(session_save_path())) {
           throw new Exception('Session path "' . session_save_path() . '" is not writable for PHP!');
        }

        $this->data = &$_SESSION;
    }

    public function getId() {
        return session_id();
    }

    /**
     * Session::set()
     * Establece una variable de sesi�n
     * @var string $key el nombre de la variable
     * @var mixed $value el valor de la variable
     * @return void
     * */
    public function set($key, $value) {
        //TODO: aceptar arrays para asignaci�n de valores en lote
        $this->data[C_CODE . "_" . $key] = $value;
    }

    /**
     * Session::get()
     * Obtiene una variable de sesi�n
     * @var string $key el nombre de la variable
     * @return mixed el valor de la variable
     * */
    public function get($key, $subkey = false, $skey = false) {
        //TODO: obtener variable dentro de una array con N llaves
        if ($skey) {
            return $this->has($key, $subkey, $skey) ? $this->data[C_CODE . "_" . $key][$subkey][$skey] : null;
        } elseif ($subkey) {
            return $this->has($key, $subkey) ? $this->data[C_CODE . "_" . $key][$subkey] : null;
        } else {
            return $this->has($key) ? $this->data[C_CODE . "_" . $key] : null;
        }
    }

    /**
     * Session::has()
     * Verifica si una variable de sesi�n existe
     * @var string $key el nombre de la variable
     * @return boolean
     * */
    public function has($key, $subkey = false, $skey = false) {
        //TODO: aceptar arrays para comparaci�n de varias variables
        if ($skey) {
            return ((isset($this->data[C_CODE . "_" . $key][$subkey][$skey])) && (!empty($this->data[C_CODE . "_" . $key][$subkey][$skey])));
        } elseif ($subkey) {
            return ((isset($this->data[C_CODE . "_" . $key][$subkey]) && (!empty($this->data[C_CODE . "_" . $key][$subkey]))));
        } else {
            return ((isset($this->data[C_CODE . "_" . $key]) && (!empty($this->data[C_CODE . "_" . $key]))));
        }
    }

    /**
     * Session::clear()
     * Destruye una variable de sesi�n, si no se pasa el nombre elimina toda la sesi�n
     * @var string $key el nombre de la variable
     * @return void
     * */
    public function clear($key = null, $subkey = false) {
        //TODO: aceptar arrays para limpiar lotes de variables
        if (!isset($key)) {
            unset($this->data);
        } elseif ($subkey) {
            unset($this->data[C_CODE . "_" . $key][$subkey]);
        } else {
            unset($this->data[C_CODE . "_" . $key]);
        }
    }

}
