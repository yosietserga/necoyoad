<?php   
class ControllerCommonCallback extends Controller {
    /**
     * @param $public_key
     * API key p�blica para ser utilizada desde cualquier sitio en internet, con esta llave se autentifica
     * el usuario que est� accediendo desde afuera, se verifica el status del usuario y se registra para hacer seguimientos
     * user tracker
     * */
	private $public_key = null;
    
    /**
     * @param $private_key
     * API key privada para ser utilizada solo por el usuario, esta llave ser� utilizada para mostrar informaci�n privada del usuario
     * y alterar los registros del usuario desde afuera, de tal manera que se puedan manipular objetos a trav�s de otros sitios
     * */
	private $private_key = null;
    
    /**
     * @param $action
     * Es el nombre de la acci�n que se va a ejecutar, b�sicamente es el nombre del archivo que contiene las funciones
     * necesarias para manipular la informaci�n
     * */
	private $action;
    
    /**
     * @param $method
     * Es el m�todo que se va a utilizar para hacer el llamado de la funci�n, si es una clase o un conjunto de funciones
     * */
	private $method = "class";
    
    /**
     * @param $funcname
     * Es el nombre de la funci�n que se va a utilizar
     * */
	private $funcname = "default";
    
    /**
     * @param $funcargs
     * Son los par�metros o los argumentos que se van a pasar a la funci�n
     * */
	private $funcargs = [];
    
    /**
     * @param $r_method
     * Es el m�todo en el que se va a retornar la informaci�n
     * */
	private $r_method ="json";
    
    public function index() {
	   /**
        * 1. verificar las api keys con el usuario
        * 2. verificar si la ip o el dominio no est� baneado
        * 3. verificar que el archivo exista
        * 4. verificar que la funci�n o el m�todo exista
        * 5. limpiar todos los datos pasado
        * 6. generar el bloque de c�digo que procesar� todo (return array)
        * 7. limpiar datos y devolver a trav�s de json
        * */
  	}
	
    private function modules() {
		$this->data['extensions'] = [];
		$extensions = $this->modelExtension->getInstalled('module');
		$files = glob(DIR_APPLICATION . 'controller/module/*.php');
		
		if ($files) {
            $i = str_replace('%theme%',$this->config->get('config_admin_template'),HTTP_ADMIN_THEME_IMAGE);
			foreach ($files as $file) {
				$extension = basename($file, '.php');
				$this->load->language('module/' . $extension);
				$action = [];
                
				if (in_array($extension, $extensions)) {
					$action[] = array(
						'action' => 'edit',
						'img' =>  $i .'edit.png',
						'text' => $this->language->get('text_edit'),
						'href' => Url::createAdminUrl('module/' . $extension . '')
					);		
					$action[] = array(
						'action' => 'install',
						'img' => $i .'uninstall.png',
						'text' => $this->language->get('text_uninstall'),
						'href' => Url::createAdminUrl('extension/module/uninstall') . '&extension=' . $extension
					);
				}
							
				$this->data['extensions'][] = array(
					'extension'   => $extension,
					'name'        => $this->language->get('heading_title'),
					'desc'        => $this->language->get('overview'),
					'action'      => $action
				);
			}
		}
    }
}