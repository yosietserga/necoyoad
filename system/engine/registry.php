<?php
final class Registry {
	private $data = [];

	public function get($key) {
		return (isset($this->data[$key]) ? $this->data[$key] : null);
	}

	public function set($key, $value)
	{
		if (class_exists("Events") && is_callable([Events::class, 'emit'])) {
			Events::emit(strtolower(__CLASS__).":update", $key, $value);
		}
		$this->data[$key] = $value;
	}

	public function has($key) {
    	return isset($this->data[$key]);
  	}	
}
