<?php
/**
 * ModelToolBackup
 * 
 * @package NecoTienda
 * @author Yosiet Serga
 * @copyright Inversiones Necoyoad, C.A.
 * @version 1.0.0
 * @access public
 * @see Model
 */
class ModelToolBackup extends Model {
	
	/**
	 * ModelToolBackup::restore()
	 * 
	 * @param string $sql
     * @see DB
	 * @return void
	 */
	public function restore($sql) {
		foreach (explode(";\n", $sql) as $sql) {
    		$sql = trim($sql);
    		
			if ($sql) {
      			$this->db->query($sql);
    		}
  		}
	}
	
	/**
	 * ModelToolBackup::getTables()
	 * 
     * @see DB
	 * @return array sql tables
	 */
	public function getTables() {
		$table_data = [];
		
		$query = $this->db->query("SHOW TABLES FROM `" . DB_DATABASE . "`");
		
		foreach ($query->rows as $result) {
			$table_data[] = $result['Tables_in_' . DB_DATABASE];
		}
		
		return $table_data;
	}
	
	/**
	 * ModelToolBackup::backup()
	 * 
	 * @param string $tables
     * @see DB
	 * @return string sql query
	 */
	public function backup($tables) {
		$output = '';

		foreach ((array)$tables as $table) {
			if (DB_PREFIX) {
				if (strpos($table, DB_PREFIX) === false) {
					$status = false;
				} else {
					$status = true;
				}
			} else {
				$status = true;
			}
			
			if ($status) {
				$output .= 'TRUNCATE TABLE `' . $table . '`;' . "\n\n";
			
				$query = $this->db->query("SELECT * FROM `" . $table . "`");
				
				foreach ($query->rows as $result) {
					$fields = '';
					
					foreach (array_keys($result) as $value) {
						$fields .= '`' . $value . '`, ';
					}
					
					$values = '';
					
					foreach (array_values($result) as $value) {
						$value = str_replace(array("\x00", "\x0a", "\x0d", "\x1a"), array('\0', '\n', '\r', '\Z'), $value);
						$value = str_replace(array("\n", "\r", "\t"), array('\n', '\r', '\t'), $value);
						$value = str_replace('\\', '\\\\',	$value);
						$value = str_replace('\'', '\\\'',	$value);
						$value = str_replace('\\\n', '\n',	$value);
						$value = str_replace('\\\r', '\r',	$value);
						$value = str_replace('\\\t', '\t',	$value);			
						
						$values .= '\'' . $value . '\', ';
					}
					
					$output .= 'INSERT INTO `' . $table . '` (' . preg_replace('/, $/', '', $fields) . ') VALUES (' . preg_replace('/, $/', '', $values) . ');' . "\n";
				}
				
				$output .= "\n\n";
			}
		}

		return $output;	
	}
}
