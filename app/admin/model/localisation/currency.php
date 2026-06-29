<?php

class ModelLocalisationCurrency extends Model {

    protected string $object_type  = "currency";
    protected string $table        = "currency";
    protected string $pkey         = "currency_id";

    protected array $fields = [
        "code" => [
            "name"      => "code",
            "type"      => "string",
        ],
        "symbol_left" => [
            "name"      => "symbol_left",
            "type"      => "string",
        ],
        "symbol_right" => [
            "name"      => "symbol_right",
            "type"      => "string",
        ],
        "decimal_place" => [
            "name"      => "decimal_place",
            "type"      => "int",
        ],
        "value" => [
            "name"      => "value",
            "type"      => "float",
        ],
        "status" => [
            "name"      => "status",
            "default"   => 1,
            "type"      => "boolean",
        ],
        "date_modified" => [
            "name"      => "date_modified",
            "default"   => "NOW()",
            "type"      => "sql",
            //TODO: add events dynamic, onInsert, onUpdate, onDelete, ...
            //TODO: add validations to fields, i.e. maxlength, minlength, patterns, etc.
        ],
    ];

    protected array $relations = ["descriptions", "stores"];
	
    	
	/**
	 * Currency::updateAll
     * 
     * Update currencies values from Yahoo! Finance
	 *
	 * @return void
	 */
	public function updateAll() {
		if (extension_loaded('curl')) {
			$data = [];
			
			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "currency WHERE code != '" . $this->db->escape($this->config->get('config_currency')) . "' AND date_modified > '" . date(strtotime('-1 day')) . "'");

			foreach ($query->rows as $result) {
				$data[] = $this->config->get('config_currency') . $result['code'] . '=X';
			}	
			
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, 'https://download.finance.yahoo.com/d/quotes.csv?s=' . implode(',', $data) . '&f=sl1&e=.csv');
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			
			$content = curl_exec($ch);
			
			curl_close($ch);
			
			$lines = explode("\n", trim($content));
				
			foreach ($lines as $line) {
				$currency = substr($line, 4, 3);
				$value = substr($line, 11, 6);
				
				if ((float)$value) {
					$this->db->query("UPDATE " . DB_PREFIX . "currency SET value = '" . (float)$value . "', date_modified = NOW() WHERE code = '" . $this->db->escape($currency) . "'");
				}
			}
			
			$this->db->query("UPDATE " . DB_PREFIX . "currency SET value = '1.00000', date_modified = NOW() WHERE code = '" . $this->db->escape($this->config->get('config_currency')) . "'");
			
			$this->cache->delete('currency');
		}
	}
}