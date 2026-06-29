<?php
final class Newsletter {
  	private $directory;
	private $data = [];
 
	public function __construct() {
	}
	
  	public function get($key) {
   		return (isset($this->data[$key]) ? $this->data[$key] : $key);
  	}
    
    public function getFormatList($format = 'a') {
        $strFormat = '';
        $arrFormat = array(
            'a' => 'HTML y Texto (Recomendado)',
            't' => 'Texto',
            'h' => 'HTML'
        );
        foreach($arrFormat as $key => $value) {
            if ($key == $format) {
                $strFormat .= "<option value='$key' selected='selected'>$value</option>\n";
            } else {
                $strFormat .= "<option value='$key'>$value</option>\n";
            }
        }
        return $strFormat;
  	}
	
	public function load($filename) {
		$_ = [];
		
		$default = DIR_LANGUAGE . 'spanish/' . $filename . '.php';
		
		if (file_exists($default)) {
			require($default);
		}
		
		$file = DIR_LANGUAGE . $this->directory . '/' . $filename . '.php';

    	if (file_exists($file) && $file != $default) {
	  		require($file);
		}
	  	
	  	if (empty($_)) { 
	  		//echo 'Error: Could not load language ' . $filename . '!';
	  	} else {
		  	$this->data = array_merge($this->data, $_);
		}
		
		return $this->data;
  	}
}
