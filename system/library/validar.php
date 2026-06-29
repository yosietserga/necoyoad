<?php
/**
 * @author Inversiones Necoyoad, C.A.
 * @copyright 2010
 */
final class Validar 
{
    private $error                          = '<ul>';
    private $esValido                       = true;
    private $patSoloTexto                   = '/^\D+$/'; // return true
    private $patSoloTextoSinEspacios        = '/^\D\S+$/'; // return true
    private $patSinEspaciosSinCharEspeciales= '/.[\ !"#\$%&\/\(\)=\?\|°¿\'\*¨´\+\}\{\^`\\\-_]/'; // return false
    private $patSinCharEspeciales           = '/.[!"#\$%&\/\(\)=\?\|°¿\'\*¨´\+\}\{\^`\\\-_]/'; // return false
    private $patSoloNumeros                 = '/^\d+$/'; // return true
    private $patSoloNumerosConDecimales     = '/^\d*[0-9],\d{1,2}$/'; // return true 1234567890,12 - los decimales son obligatorios
    private $patFechaCorta                  = '/^(0[1-9]|[12][0-9]|3[01])+[\-\/]+(0[1-9]|1[012])+[\-\/]+(19|20)[0-9]{2}/'; // dd/mm/yyyy return true
    private $patFechaLarga                  = '/(0[1-9]|[12][0-9]|3[01])+[\-\/]+(0[1-9]|1[012])+[\-\/]+((19|20)[0-9]{2})+[\ ]+(0[1-9]{1}|1[012]{1})+[\:]+(0[0-9]{1}|1[0-9]{1}|2[0-9]{1}|3[0-9]{1}|4[0-9]{1}|5[0-9]{1})+[\:]+(0[0-9]{1}|1[0-9]{1}|2[0-9]{1}|3[0-9]{1}|4[0-9]{1}|5[0-9]{1})+[\ ]+([AaPp]\.m\.)/'; // dd/mm/yyyy hh:ii:ss return true
    private $patTelefonoLocal               = '/\(?\b([0-9]{3})\)?[ ]?([0-9]{3})[-. ]?([0-9]{2})[-. ]?([0-9]{2})\b/'; // return true (243)245.16.01 | (243)245-16-01 | (243)245 16 01
    private $patTelefonoGlobal              = '/^\+[0-9]{12}+$/'; // return true +584128976447
    private $patPassword                    = '/^.*(?=.{6,})(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).*$/'; // return true al menos una minúscula, una mayúscula, un caracter especial y un numero con un mínimo de 6 caracteres /\A(?=[\x20-\x7E]*?[A-Z])(?=[\x20-\x7E]*?[a-z])(?=[\x20-\x7E]*?[0-9])[\x20-\x7E]{6,}\z/
    private $patNoTabCrtl                   = '/\p{Cc}/'; // no se pueden presionar tacles como Ctrl Alt Tab
    private $patCedula                      = '/\b[VE]-[0-9]{2}\.[0-9]{3}\.[0-9]{3}\b/'; // return true V-16.865.920
    private $patCedulaSinEspacios           = '/\b[VE][0-9]{8}\b/'; // return true V16865920
    private $patRif                         = '/\b[JGVE]-[0-9]{8}-[0-9]{1}\b/'; // return true J-29818520-1
    private $patRifSinEspacios              = '/\b[JGVE][0-9]{9}\b/'; // return true J298185201
    private $patMsn                         = '/^[A-Za-z0-9._%+-]+@(hotmail|msn)+\.[A-Za-z]{2,3}$/'; // correo@hotmail.com
    private $patGmail                       = '/^[A-Za-z0-9._%+-]+@(gmail)+\.[A-Za-z]{2,3}$/'; // correo@gmail.com
    private $patYahoo                       = '/^[A-Za-z0-9._%+-]+@(yahoo)+\.[A-Za-z]{2,3}$/'; // correo@yahoo.com
    private $patCorreoPopular               = '/^[A-Za-z0-9._%+-]+@(yahoo|hotmail|msn|gmail)+\.[A-Za-z]{2,3}$/'; 
    private $pattern;
    
    
    public function mostrarError() 
    {
        $this->error .= '</ul>';
        if (!$this->esValido){
           return "
            <script type='text/javascript'>
            jQuery(function(){
                Sexy.error('<h1>Error al intentar procesar el formulario</h1><p>$this->error</p>');
            })
            </script>";
            /* // TODO: revisar por qué no se activa automáticamente
            return "
            <p id='errorValidacion' style='left:-9999em;position:absolute;'></p>
            <script type='text/javascript'>
            jQuery(function(){
                jQuery('#errorValidacion').trigger('click');
                jQuery('#errorValidacion').bind('click', function() {
                    Sexy.error('<h1>Error al intentar procesar el formulario</h1><p>$this->error</p>');
                    return false;
                });
            })
            </script>";
            */
            return $this->esValido = true;
        }
    }
    
    public function esMayorDeEdad($datFecha,$strCampo) 
    {
        $ahora = getdate();
        $datCurrentYear = $ahora['year'];
        $datHace18anos  = $datCurrentYear - 18;
        $datMes         = $ahora['mon'];
        $datDia         = $ahora['mday'];
        if ($this->esFechaCorta($datFecha,$strCampo)) {
            $datFecha = str_replace('-','/',$datFecha);
            $arrFecha = explode('/',$datFecha);
            if ($arrFecha[2] < $datHace18anos) {
                return $this->esValido;
            } elseif ($arrFecha[2] = $datHace18anos && $arrFecha[1] < $datMes) {
                return $this->esValido;
            } elseif ($arrFecha[2] = $datHace18anos && $arrFecha[1] = $datMes && $arrFecha[0] <= $datDia) {
                return $this->esValido;
            } else {
                $this->esValido = false;
                $this->error .= "<p>Lo siento, Debe ser mayor de edad.</p>";
            }
        } /* elseif ($this->esFechaLarga($datFecha,$strCampo)) { //comprobar si es una fecha larga
            $datFecha = str_replace('-','/',$datFecha);
            $arrFecha = explode('/',$datFecha);
            $arrFecha[2] = substr($arrFecha,0,4);
            if ($arrFecha[2] < $datHace18anos) {
                return $this->esValido;
            } elseif ($arrFecha[2] = $datHace18anos && $arrFecha[1] < $datMes) {
                return $this->esValido;
            } elseif ($arrFecha[2] = $datHace18anos && $arrFecha[1] = $datMes && $arrFecha[0] <= $datDia) {
                return $this->esValido;
            } else {
                $this->esValido = false;
                $this->error .= "<p>Lo siento, Debe ser mayor de edad.</p>";
            }
        } */ else {
            $this->esValido = false;
            $this->error .= "<li>Lo siento, Debe ser mayor de edad.</li>";
        }
    }
        
    public function longitudMinMax($strTexto,$intMin,$intMax,$strCampo) 
    {
        $longitud = strlen($strTexto);
        if (($longitud < $intMin) || ($longitud> $intMax) || ($intMin> $intMax)) {
            $this->esValido = false;
            $this->error .= "<li>El campo <b>$strCampo</b> debe poseer una longitud entre <b>$intMin</b> y <b>$intMax</b> caracteres.</li>";
        }
        return $this->esValido;
    }
    
    public function longitudMax($strTexto,$intMax,$strCampo) 
    {
        $longitud = strlen($strTexto);
        if ($longitud> $intMax) {
            $this->esValido = false;
            $this->error .= "<li>El campo <b>$strCampo</b> debe poseer una longitud menor de <b>$intMax</b> caracteres.</li>";
        }
        return $this->esValido;
    }
    
    public function longitudMin($strTexto,$intMin,$strCampo) 
    {
        $longitud = strlen($strTexto);
        if ($longitud < $intMin) {
            $this->esValido = false;
            $this->error .= "<li>El campo <b>$strCampo</b> debe poseer una longitud mayor a <b>$intMin</b> caracteres.</li>";
        }
        return $this->esValido;
    }
    
    public function validEmail($email) 
    {
           $atIndex = strrpos($email, "@");
           if (is_bool($atIndex) && !$atIndex)
           {
              $this->esValido = false;
              $this->error .= "<li>El email es incorrecto.</li>";
           }
           else
           {
              $domain = substr($email, $atIndex+1);
              $local = substr($email, 0, $atIndex);
              $localLen = strlen($local);
              $domainLen = strlen($domain);
              if ($localLen < 1 || $localLen> 64)
              {
                 $this->esValido = false;
                 $this->error .= "<li>La longitud del email es incorrecta.</li>";
              }
              else if ($domainLen < 1 || $domainLen> 255)
              {
                 $this->esValido = false;
                 $this->error .= "<li>La longitud del email es incorrecta.</li>";
              }
              else if ($local[0] == '.' || $local[$localLen-1] == '.')
              {
                 $this->esValido = false;
                 $this->error .= "<li>El email es incorrecto.</li>";
              }
              else if (preg_match('/\\.\\./', $local))
              {
                 $this->esValido = false;
                 $this->error .= "<li>El email es incorrecto.</li>";
              }
              else if (!preg_match('/^[A-Za-z0-9\\-\\.]+$/', $domain))
              {
                 $this->esValido = false;
                 $this->error .= "<li>El email es incorrecto.</li>";
              }
              else if (preg_match('/\\.\\./', $domain))
              {
                 $this->esValido = false;
                 $this->error .= "<li>El email es incorrecto.</li>";
              }
              else if
        (!preg_match('/^(\\\\.|[A-Za-z0-9!#%&`_=\\/$\'*+?^{}|~.-])+$/',
                         str_replace("\\\\","",$local)))
              {
                 if (!preg_match('/^"(\\\\"|[^"])+"$/',
                     str_replace("\\\\","",$local)))
                 {
                    $this->esValido = false;
                    $this->error .= "<li>El email es incorrecto.</li>";
                 }
              }
              /*
              if ($this->esValido && !(checkdnsrr($domain,"MX") || checkdnsrr($domain,"A")))
              {
                 $this->esValido = false;
                 $this->error .= "<li>El dominio del email no es válido.</li>";
              }
              */
           }
           return $this->esValido;
    }
    
    public function custom($strErrorMsg,$strCampo="",$strPattern="/^\D+$/")  
    {
        if (preg_match($strPattern,$strCampo)) {
            return $this->esValido;
        }
        $this->esValido = false;
        $this->error .= "$strErrorMsg";
    }
    
    public function esMsn($strEmail)  
    {
        if (preg_match($this->patMsn,$strEmail)) {
            return $this->esValido;
        }
        $this->esValido = false;
        $this->error .= "<li>No es un correo de MSN</li>";
    }
    
    public function esGmail($strEmail)  
    {
        if (preg_match($this->patGmail,$strEmail)) {
            return $this->esValido;
        }
        $this->esValido = false;
        $this->error .= "<li>No es un correo de Gmail</li>";
    }
    
    public function esYahoo($strEmail)  
    {
        if (preg_match($this->patYahoo,$strEmail)) {
            return $this->esValido;
        }
        $this->esValido = false;
        $this->error .= "<li>No es un correo de Yahoo</li>";
    }
        
    public function esSoloTexto($strTexto,$strCampo)
    {
        if (preg_match($this->patSoloTexto,$strTexto)) {
            return $this->esValido;
        }
        $this->esValido = false;
        $this->error .= "<li>En el campo <b>$strCampo</b> deben ser solo caracteres alfabéticos.</li>";
    }
    
    public function esSoloTextoSinEspacios($strTexto,$strCampo)
    {
        if (preg_match($this->patSoloTextoSinEspacios,$strTexto)) {
            return $this->esValido;
        }
        $this->esValido = false;
        $this->error .= "<li>No se permiten espacios en blanco en el campo <b>$strCampo</b>.</li>";
    }
    
    public function esSinCharEspeciales($strTexto,$strCampo)
    {
        if (!preg_match($this->patSinCharEspeciales,$strTexto)) {
            return $this->esValido;
        }
        $this->esValido = false;
        $this->error .= "<li>No se permiten caracteres especiales en el campo <b>$strCampo</b>.</li>";
    }
    
    public function esSinEspaciosSinCharEspeciales($strTexto,$strCampo)
    {
        if (!preg_match($this->patSinEspaciosSinCharEspeciales,$strTexto)) {
            return $this->esValido;
        }
        $this->esValido = false;
        $this->error .= "<li>No se permiten caracteres especiales ni espacios en blanco en el campo <b>$strCampo</b>.</li>";
    }
    
    public function esSoloNumeros($strTexto,$strCampo)
    {
        if (preg_match($this->patSoloNumeros,$strTexto)) {
            return $this->esValido;
        }
        $this->esValido = false;
        $this->error .= "<li>En el campo <b>$strCampo</b> deben ser solo números enteros.</li>";
    }
    
    public function esSoloNumerosConDecimales($strTexto,$strCampo)
    {
        if (preg_match($this->patSoloNumerosConDecimales,$strTexto)) {
            return $this->esValido;
        }
        $this->esValido = false;
        $this->error .= "<li>En el campo <b>$strCampo</b> deben ser solo números con dos decimales máximos.</li>";
    }
    
    public function esFechaCorta($datTexto,$strCampo)
    {
        if (preg_match($this->patFechaCorta,$datTexto)) {
            return $this->esValido;
        }
        $this->esValido = false;
        $this->error .= "<li>El formato de la fecha del campo <b>$strCampo</b> debe ser dd/mm/yyyy.</li>";
    }
    
    public function esFechaLarga($datTexto,$strCampo)
    {
        if (preg_match($this->patFechaLarga,$datTexto)) {
            return $this->esValido;
        }
        $this->esValido = false;
        $this->error .= "<li>El formato de la fecha del campo <b>$strCampo</b> debe ser dd/mm/yyyy hh:mm:ss a.m. ó dd/mm/yyyy hh:mm:ss p.m.</li>";
    }    
    
    public function esTelefonoLocal($strTexto)
    {
        if (preg_match($this->patTelefonoLocal,$strTexto)) {
            return $this->esValido;
        }
        $this->esValido = false;
        $this->error .= "<li>El formato del teléfono debe ser (000)000-00-00, donde el código de área debe ir entre los paréntesis.</li>";
    }
    
    public function esTelefonoGlobal ($strTexto)
    {
        if (preg_match($this->patTelefonoGlobal,$strTexto)) {
            return $this->esValido;
        }
        $this->esValido = false;
        $this->error .= "<li>El formato del teléfono debe ser internacional (p.ej. +584240000000)</li>";
    }
    
    public function esPassword($strTexto)
    {
        if (preg_match($this->patPassword,$strTexto)) {
            return $this->esValido;
        }
        $this->esValido = false;
        $this->error .= "<li>La contrase&ntilde;a debe tener un m&iacute;nimo de 6 caracteres, contener al menos una may&uacute;scula, una min&uacute;scula, un n&uacute;mero y un caracter especial (#$%&@-_+*).</li>";
    }
    
    public function esNoTabCrtl($strTexto,$strCampo)
    {
        // TODO: integrar con javascript para detectar el trigger y ejecutar una acción
        if (preg_match($this->patNoTabCrtl,$strTexto)) {
            return $this->esValido;
        }
        $this->esValido = false;
        $this->error .= "<li>Por su seguridad no están permitidas las teclas Ctrl, Alt y Tab.</li>";
    }
    
    public function esCedula($strTexto)
    {
        if (preg_match($this->patCedula,$strTexto)) {
            return $this->esValido;
        }
        $this->esValido = false;
        $this->error .= "<li>La cédula debe tener el formato V-00.000.000</li>";
    }
    
    public function esCedulaSinEspacios($strTexto)
    {
        if (preg_match($this->patCedulaSinEspacios,$strTexto)) {
            return $this->esValido;
        }
        $this->esValido = false;
        $this->error .= "<li>La cédula debe tener el formato V00000000</li>";
    }
    
    public function esRif($strTexto)
    {
        if (preg_match($this->patRif,$strTexto)) {
            return $this->esValido;
        }
        $this->esValido = false;
        $this->error .= "<li>Solo se aceptan n&uacute;meros en campo del RIF</li>";
    }
    
    public function esRifSinEspacios($strTexto)
    {
        if (preg_match($this->patRifSinEspacios,$strTexto)) {
            return $this->esValido;
        }
        $this->esValido = false;
        $this->error .= "<li>El RIF debe tener el formato J000000000</li>";
    }
}