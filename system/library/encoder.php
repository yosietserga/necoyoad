<?php

/**
 * @package php encoder
 * @author Yosiet Serga
 * @copyright 2011
 * 
 * Programa para codificar los códigos de php
 * y proteger las aplicaciones de plagios y robos
 * 
 */

final class Encoder
{
    /**
     * Ruta hacia el archivo o la carpeta donde se encuentran los archivos que se quieren
     * condificar
     * @param string $path
     * */
    var $path;

    /**
     * llave para encriptación 
     * @param string $key
     * */
    var $key = CRYPT_KEY;

    /**
     * licencia que indica que dominios son permitidos para utilizar el sistema
     * @param string $license
     * */
    var $license;

    function __construct()
    {
        $this->createIV();
    }

    function createIV()
    {
        $this->IV = mcrypt_create_iv(mcrypt_get_block_size(MCRYPT_TripleDES,
            MCRYPT_MODE_CBC), MCRYPT_DEV_RANDOM);

    }
    
    /**
     * Encripta un texto con mcrypt_cbc utilizando una llave estandar
     * @param string $plaintext
     * @return string texto codificado
     * */
    function encrypt($plaintext)
    {
        $cipherText = mcrypt_cbc(MCRYPT_TripleDES, $this->key, $plaintext,
            MCRYPT_ENCRYPT, $this->IV);
        return base64_encode($cipherText); // Converting to base64 to make it readable
    }
    
    /**
     * Desencripta un texto con mcrypt_cbc utilizando una llave estandar
     * @param string $cipherText codificado
     * @return string texto decodificado
     * */
    function decrypt($cipherText)
    {
        $text = base64_decode($cipherText);
        return mcrypt_cbc(MCRYPT_TripleDES, $this->key, $text, MCRYPT_DECRYPT, $this->
            IV);
    }
    
    /**
     * Encripta un texto utilizando una llave estandar
     * @param string $string
     * @return string texto codificado
     * */
    function _encrypt($string)
    {
        $result = '';
        for ($i = 0; $i < strlen($string); $i++) {
            $char = substr($string, $i, 1);
            $keychar = substr($this->key, ($i % strlen($this->key)) - 1, 1);
            $char = chr(ord($char) + ord($keychar));
            $result .= $char;
        }

        return base64_encode($result);
    }
    
    /**
     * Desencripta un texto utilizando una llave estandar
     * @param string $string
     * @return string texto decodificado
     * */
    function _decrypt($string)
    {
        $result = '';
        $string = base64_decode($string);

        for ($i = 0; $i < strlen($string); $i++) {
            $char = substr($string, $i, 1);
            $keychar = substr($this->key, ($i % strlen($this->key)) - 1, 1);
            $char = chr(ord($char) - ord($keychar));
            $result .= $char;
        }

        return $result;
    }
}

