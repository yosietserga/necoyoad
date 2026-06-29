<?php
final class DB {
	private $driver;

	public function __construct($driver, $hostname, $username, $password, $database) {
		if (file_exists(DIR_DATABASE . $driver . '.php')) {
			require_once(DIR_DATABASE . $driver . '.php');
		} else {
			exit('Error: Could not load database file ' . $driver . '!');
		}
        
		$this->driver = new $driver($hostname, $username, $password, $database);
	}

  	public function query($sql) {
		return $this->driver->query($sql);
  	}

	public function escape($value) {
		return $this->driver->escape($value);
	}
	
  	public function countAffected() {
		return $this->driver->countAffected();
  	}

  	public function getLastId() {
		return $this->driver->getLastId();
  	}

  	public function getVersion() {
		return $this->driver->getVersion();
  	}

	public function getError()
	{
	}

	public function checkDbScheme()
	{
	}

	public function repairDbScheme() {

	}

    public function getTables($pattern=null) {
        $sql = "SHOW TABLES FROM `". DB_DATABASE ."`";
        if (!empty($pattern)) {
            $sql .= " LIKE '%$pattern%'";
        }

        $query = $this->driver->query($sql);

        foreach ($query->rows as $result) {
            $table_data[] = $result['Tables_in_' . DB_DATABASE];
        }

        return $table_data;
    }

    public function getTableFields($table, $pattern=null) {
        if (empty($table)) return false;
        $sql = "SHOW COLUMNS FROM `". $table ."` FROM `". DB_DATABASE ."`";
        if (!empty($pattern)) {
            $sql .= " LIKE '%$table%'";
        }
        return $this->driver->query($sql);
    }
}
