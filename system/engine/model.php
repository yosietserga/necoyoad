<?php

/**
 * Model base class
 */
abstract class Model {
	protected object $registry;
    protected string $namespace = "";
    protected string $table = "";
    protected string $pkey = "";
    protected string $object_type = "";
    protected string $description_object_type = "";
    protected array $fields = [];
    protected array $relations = [];

    /**
     * With new instance it is called Model::init() 
     * Inside Model::init() has to define all events and hooks functions 
     * @return void
     */
	public function __construct($registry) {
		$this->registry = $registry;
        $this->namespace = "model:{$this->table}:". ($this->object_type ? ':'.$this->object_type .':' :'');

        $this->init();
	}

    /**
     * __get
     * 
     * @param  mixed $key
     * 
     * @uses Registry
     * 
     * @return mixed $value
     */
	public function __get($key) {
	   if ($this->registry->has($key)) {
	       return $this->registry->get($key);
	   } 
	}

    /**
     * __set
     *
     * @param  mixed $key
     * @param  mixed $value
     * 
     * @uses Registry
     * 
     * @return void
     */
	public function __set($key, $value) {
		$this->registry->set($key, $value);
	}

    public function __call(string $fn, $values = null) {
        $key = preg_replace('/\B([A-Z])/', '_$1', $fn);
        $key = strtolower($key);
        $key = ltrim($key, 'get_');
        $key = ltrim($key, 'set_');
        
        $action = substr($fn, 0, 3);

        if ($action == "get") {
            return isset($this->$key) ? $this->$key : null;
        } else if ($action == "set") {
            if (isset($this->$key)) $this->$key = $values[0];
        }
    }

    public function __invoke() {
        return $this;
    }

    /**
     * Model::getTable
     *
     * @return string $table
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * Model::getFields
     *
     * @return array $fields
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * Model::getRelations
     *
     * @return array $relations
     */
    public function getRelations()
    {
        return $this->relations;
    }
    
    /**
     * Model::on
     * 
     * Add a function listeners for a event
     * 
     * @param string $ev event name
     * @param callable $fn 
     * 
     * @uses self::$namespace
     * @uses \Events::on
     * 
     * @see url {@link https://www.php.net/manual/en/language.types.callable.php}
     * @see \Events::on
     * 
     * @return void
     */
    public function on(string $ev, callable $fn)
    {
        Events::on($this->namespace . $ev, $fn);
    }

    /**
     * Model::off
     * 
     * Remove all functions listeners for a event
     * 
     * @param string $ev event name
     * 
     * @uses self::$namespace
     * 
     * @see {@link https://www.php.net/manual/en/language.types.callable.php}
     * @see \Events::off
     * 
     * @return void
     */
    public function off(string $ev)
    {
        Events::off($this->namespace . $ev);
    }

    /**
     * Model::trigger
     * 
     * Execute all functions listeners for a event
     * 
     * @param string $ev event name
     * @param mixed $args 
     * 
     * @uses self::$namespace
     * @see Events::emit
     * 
     * @return void
     */
    public function trigger(string $ev, ...$args)
    {
        Events::emit($this->namespace . $ev, $args); //for this model namespace
        Events::emit($ev, $args); //for all models
    }

    /**
     * Model::addFilter
     * 
     * Add a filter function for a hook
     * 
     * @param string $name filter name
     * @param callable $fn 
     * 
     * @uses self::$namespace
     * @see url {@link https://www.php.net/manual/en/language.types.callable.php}
     * 
     * @return void
     */
    public function addFilter(string $name, callable $fn)
    {
        global $hooks;
        $hooks->addFilter($this->namespace . $name, $fn);
    }

    /**
     * Model::applyFilters
     * 
     * Add a filter function for a hook
     * 
     * @param string $name filter name
     * @param array $data to filter
     * 
     * @uses self::$namespace
     * @uses global $hooks 
     * @see \Hooks
     * 
     * @return mixed $data filtered
     */
    public function applyFilters(string $name, $data)
    {
        global $hooks;
        $data = $hooks->applyFilters($name, $data); //for all models
        $data = $hooks->applyFilters($this->namespace . $name, $data); //for this model namespace
        return $data;
    }

    /**
     * Model::addHook
     * 
     * Add a hook listener
     * 
     * @param string $name hook name
     * @param callable $fn function listener
     * 
     * @uses self::$namespace
     * @uses global $hooks 
     * @see \Hooks
     * @see {@link https://www.php.net/manual/en/language.types.callable.php}
     * 
     * @return void
     */
    public function addHook(string $name, callable $fn)
    {
        global $hooks;
        $hooks->addAction($this->namespace . $name, $fn);
    }

    /**
     * Model::runHook
     *
     * Execute all hooks listeners for this event
     * If return any boolean true value, will change or break the sequence stack 
     * 
     * @param string $name hook name 
     * @param mixed $args
     * 
     * @uses self::$namespace
     * @uses global $hooks 
     * @see \Hooks
     * 
     * @return void|boolean|mixed
     */
    public function runHook(string $name, ...$args)
    {
        global $hooks;
        $hooks->run($name, $args); //for all models
        return $hooks->run($this->namespace . $name, $args); //for this model namespace
    }
    
    /**
     * Model::init
     * 
     * This function is called in new instance, add inside all events and hooks handlers and listeners
     *
     * @return void
     */
    public function init() {}

    /**
     * Model::add
     * 
     * This function insert a record
     * 
     * @param  mixed $data Data to insert 
     * 
     * @uses Events::emit insert called before execute query 
     * @uses Events::emit insert {@param $data, @param @object_type, @param @pkey, @param @table}
     * 
     * @uses Hooks::applyFilters insert {@param $data}
     * 
     * @uses Hooks::run insert called before execute query
     * @uses Hooks::run insert {@param $data, @param @object_type, @param @pkey, @param @table}
     * 
     * @uses Events::emit save called after execute query 
     * @uses Events::emit save {@param $id, @param $data, @param @action, @param @query_sql}
     * 
     * @see \Events
     * @see \Hooks
     *
     * @usedby Model::copy 
     * 
     * @return integer $id
     */
    public function add(array $data)
    {
        if (empty($this->table)) {
            //TODO: on debug mode, log error
            //thrown error on strict mode 
            throw new Exception("Must set table name. Class: " . get_class($this) . PHP_EOL);
            exit;
        }

        //trigger events
        $this->trigger("insert", [
            "data" => $data,
            "object_type" => $this->object_type,
            "pkey" => $this->pkey,
            "table" => $this->table,
        ]);

        //apply filters to $data
        $data = $this->applyFilters("insert", $data);

        //do actions for this model 
        $hasToReturn = $this->runHook("insert", [
            "data" => $data,
            "object_type" => $this->object_type,
            "pkey" => $this->pkey,
            "table" => $this->table,
        ]);
        if ($hasToReturn) {
            return $hasToReturn;
        }

        $sql = "INSERT INTO `" . DB_PREFIX . "{$this->table}` SET ";
        
        $sql .= $this->__prepareUpsertSQL((array)$data);
        $this->db->query($sql);
        $id = $this->db->getLastId();
        
        if (!$id) return false;

        if (in_array("descriptions", $this->relations) && is_array($data['descriptions'])) {
            $this->setDescriptions($id, $data['descriptions']);
        }

        if (in_array("stores", $this->relations) && is_array($data['stores'])) {
            $this->setStores($id, $data['stores']);
        }

        if (in_array("categories", $this->relations) && is_array($data['categories'])) {
            $this->setCategories($id, $data['categories']);
        }

        $this->cache->delete("{$this->table}-{$this->object_type}");

        //trigger events
        $this->trigger("save", [
            "id" => $id,
            "data"=> $data,
            "query" => $sql,
            "action" => "insert"
        ]);

        return $id;
    }

    /**
     * Model::update
     * 
     * This function update a record
     * 
     * @uses Events::emit update called before execute query 
     * @uses Events::emit update {@param $id, @param $data, @param @object_type, @param @pkey, @param @table}
     * 
     * @uses Hooks::applyFilters update {@param $data}
     * 
     * @uses Hooks::run update called before execute query
     * @uses Hooks::run update {@param $id, @param $data, @param @object_type, @param @pkey, @param @table}
     * 
     * @uses Events::emit save called after execute query 
     * @uses Events::emit save {@param $id, @param $data, @param @action, @param @query_sql}
     * 
     * @see \Events
     * @see \Hooks
     *
     * @param  mixed $data Data to update 
     * @return integer $affected_rows
     */
    public function update(int $id, array $data)
    {
        if (empty($this->table)) {
            //TODO: on debug mode, log error
            //thrown error on strict mode 
            throw new Exception("Must set table name. Class: " . get_class($this) . PHP_EOL);
            exit;
        }

        //trigger events
        $this->trigger("update", [
            "id" => $id,
            "data" => $data,
            "object_type" => $this->object_type,
            "pkey" => $this->pkey,
            "table" => $this->table,
        ]);

        //apply filters to $data
        $data = $this->applyFilters("update", $data);

        //do actions for this model 
        $hasToReturn = $this->runHook("update", [
            "id" => $id,
            "data" => $data,
            "object_type" => $this->object_type,
            "pkey" => $this->pkey,
            "table" => $this->table,
        ]);
        if ($hasToReturn) {
            return $hasToReturn;
        }
        
        $sql = "UPDATE `" . DB_PREFIX . "{$this->table}` SET ";

        $sql .= $this->__prepareUpsertSQL((array)$data);
        
        $criterias = [];
        $criterias[] = " `{$this->pkey}` = '" . (int)$id . "'";
        
        $sql .= $this->__getCriteriaSQL($criterias);
        
        $this->db->query($sql);
        
        $affetcted_rows = $this->db->countAffected();

        if (in_array("descriptions", $this->relations) && isset($data['descriptions']) && is_array($data['descriptions'])) {
            $this->setDescriptions($id, $data['descriptions']);
        }

        if (in_array("stores", $this->relations) && isset($data['stores']) && is_array($data['stores'])) {
            $this->setStores($id, $data['stores']);
        }

        if (in_array("categories", $this->relations) && isset($data['categories']) && is_array($data['categories'])) {
            $this->setCategories($id, $data['categories']);
        }

        $this->cache->delete("{$this->table}-{$this->object_type}");
        
        //trigger events
        $this->trigger("save", [
            "id" => $id,
            "data" => $data,
            "query" => $sql,
            "action" => "update"
        ]);
        
        return $affetcted_rows;
    }

    /**
     * Model::copy
     * 
     * This function copy or duplicate a record
     * 
     * @param integer $id ID of record to copy 
     * 
     * @uses Hooks::run copy called before process
     * @uses Hooks::run copy {@param $id, @param @object_type, @param @pkey, @param @table}
     * 
     * @uses Events::emit copy called after process 
     * @uses Events::emit copy {@param $from, @param @to, @param $data, @param @object_type, @param @pkey, @param @table}
     * 
     * @see \Events
     * @see \Hooks
     *
     * @return integer|boolean $newID or false
     */
    public function copy(int $id)
    {
        //do actions for this model 
        $hasToReturn = $this->runHook("copy", [
            "id" => $id,
            "object_type" => $this->object_type,
            "pkey" => $this->pkey,
            "table" => $this->table,
        ]);
        if ($hasToReturn) {
            return $hasToReturn;
        }
        
        $query = $this->db->query("SELECT DISTINCT * FROM `" . DB_PREFIX . "{$this->table}` t WHERE t.`{$this->pkey}` = '" . (int) $id . "'");
        
        if ($query->num_rows) {
            $data = $query->row;
            unset($data[$this->pkey]);
            unset($data["date_added"]);
            unset($data["date_modified"]);

            //TODO: add suffix to unique fields 

            if (in_array("descriptions", $this->relations)) {
                $data = array_merge($data, array('descriptions' => $this->getDescriptions($id)));
            }

            if (in_array("stores", $this->relations)) {
                $data = array_merge($data, array('stores' => $this->getStores($id)));
            }

            if (in_array("categories", $this->relations)) {
                $data = array_merge($data, array('categories' => $this->getCategories($id)));
            }

            //apply filters to $data
            $_data = $this->applyFilters("copy", ["id"=>$id, "data"=>$data]);
            if (isset($_data["data"])) $data = $_data["data"];

            $newId = $this->add($data);

            if ($newId) {
                //trigger events
                $this->trigger("copy", [
                    "from" => $id,
                    "to" => $newId,
                    "data" => $data,
                    "object_type" => $this->object_type,
                    "pkey" => $this->pkey,
                    "table" => $this->table,
                ]);
            }
        }
        return false;
    }

    /**
     * Model::delete
     * 
     * This function delete a record
     * 
     * @uses Hooks::run delete called before execute query
     * @uses Hooks::run delete {@param $id,@param @object_type, @param @pkey, @param @table}
     * 
     * @uses Events::emit delete called after execute query 
     * @uses Events::emit delete {@param $id, @param $recordDeleted, @param @object_type, @param @pkey, @param @table}
     * 
     * @see \Events
     * @see \Hooks
     *
     * @param  integer $id ID to delete
     * @return integer $affected_rows
     */
    public function delete(int $id)
    {
        //do actions for this model 
        $hasToReturn = $this->runHook("delete", [
            "id" => $id,
            "object_type" => $this->object_type,
            "pkey" => $this->pkey,
            "table" => $this->table,
        ]);
        if ($hasToReturn) {
            return $hasToReturn;
        }

        $record = $this->getById($id);
        $this->db->query("DELETE FROM `" . DB_PREFIX . "{$this->table}` WHERE `{$this->pkey}` = '" . (int)$id . "'");
        $affetcted_rows = $this->db->countAffected();

        if ($affetcted_rows) {
            $shared_tables = array(
                'object_to_category',
                'object_to_store',
                'property',
                'description',
                'stat',
                'url_alias',
                'review',
            );

            foreach ($shared_tables as $table) {
                $this->db->query("DELETE FROM `" . DB_PREFIX . "{$table}` " .
                "WHERE object_id  = " . (int)$id . " " .
                "AND object_type = '{$this->object_type}'");
            }

            //TODO: check if delete children too is checked, and then delete children too 
            if (isset($this->fields["parent_id"])) {
                $children = $this->db->query("SELECT * FROM `" . DB_PREFIX . "{$this->table}` WHERE `parent_id` = '" . (int) $id . "'");
                if ($children->rows) {
                    if (isset($this->actions["onDelete"]) && strtolower($this->actions["onDelete"]) == "cascade") {
                        foreach ($children->rows as $row) {
                            $this->delete((int)$row[$this->pkey]);
                        }
                    } else {
                        $this->db->query("UPDATE FROM `" . DB_PREFIX . "{$this->table}` SET " .
                            "parent_id = 0 " .
                            "WHERE parent_id = ". (int)$id);
                    }
                }
            }

            $this->cache->delete($this->object_type);

            //trigger events
            $this->trigger("delete", [
                "id" => $id,
                "data"=> $record, //the data of the deleted record inside table, good bye my friend :(
                "object_type" => $this->object_type,
                "pkey" => $this->pkey,
                "table" => $this->table,
            ]);
        }

        return $affetcted_rows;
    }

    /**
     * Model::sortTable
     * 
     * Only for tables with sort_order field, sort records by sort_order
     * 
     * @uses Hooks::run sort called before execute query
     * @uses Hooks::run sort {@param $data, @param @object_type, @param @pkey, @param @table}
     * 
     * @uses Events::emit sort called after execute query 
     * @uses Events::emit sort {@param $data, @param @object_type, @param @pkey, @param @table}
     * 
     * @see \Events
     * @see \Hooks
     *
     * @param array $data IDs [int Id1, int Id2, ...]
     * @return void
     */
    public function sortTable(array $data)
    {
        //do actions for this model 
        $hasToReturn = $this->runHook("sort", [
            "data" => $data,
            "object_type" => $this->object_type,
            "pkey" => $this->pkey,
            "table" => $this->table,
        ]);
        if ($hasToReturn) {
            return $hasToReturn;
        }

        if (!is_array($data))
            return false;

        $pos = 1;
        foreach ($data as $id) {
            $this->db->query("UPDATE `" . DB_PREFIX . "{$this->table}` SET sort_order = '" . (int)$pos . "' WHERE `{$this->pkey}` = '". (int) $id ."'");
            $pos++;
        }

        //trigger events
        $this->trigger("sort", [
            "data" => $data,
            "object_type" => $this->object_type,
            "pkey" => $this->pkey,
            "table" => $this->table,
        ]);
        return true;
    }

    private function __prepareUpsertSQL(array $data) {
        $values = [];
        $sql = "";
        foreach ($this->fields as $field) {
            if (isset($field["required"]) && $field["required"] && (!isset($data[$field["name"]]) || trim($data[$field["name"]]) == "")) {
                //TODO: on debug mode, log error message
                throw new Exception("The field '{$field['name']}' is required, must have a valid value in query for table '{$this->table}'. Class: ". get_class($this) . PHP_EOL);
                exit;
            }

            if (!isset($data[$field["name"]])) {
                if (isset($field["default"])) {
                    $value = $field["default"];
                    $values[$field["name"]] = $field["type"] !== "sql" ? "`{$field["name"]}` = '{$value}' " : "`{$field["name"]}` = {$value} ";
                }
                continue;
            }

            $value = "";
            switch ($field["type"]) {
                case "string":
                case "varchar":
                case "text":
                case "enum":
                default:
                    $value = $this->db->escape($data[$field["name"]]);
                    break;
                case "date":
                case "datetime":
                    //TODO: process date fields correctly
                    $value = $this->db->escape($data[$field["name"]]);
                    break;
                case "int":
                case "bigint":
                case "integer":
                    if (!is_numeric($data[$field["name"]])) {
                        throw new Exception("Invalid type " . gettype($data[$field["name"]]) . " for field {$field["name"]} with value {$data[$field["name"]]}. Type {$field["type"]} is spected!");
                    }

                    $value = intval($data[$field["name"]], 10);

                    break;
                case "decimal":
                case "double":
                case "float":
                    if (!is_numeric($data[$field["name"]])) {
                        throw new Exception("Invalid type " . gettype($data[$field["name"]]) . " for field {$field["name"]} with value {$data[$field["name"]]}. Type {$field["type"]} is spected!");
                    }

                    $value = (float)$data[$field["name"]];
                    break;
                case "boolean":
                    $value = (bool)$data[$field["name"]] === false ? 0 : 1;
                    break;
            }

            $values[$field["name"]] = $field["type"] !== "sql" ? "`{$field["name"]}` = '{$value}' " : "`{$field["name"]}` = {$value} ";
        }
        $sql .= implode(", ", $values);
        
        return $sql;
    }

    /**
     * Model::getByID
     * 
     * Get one record by ID
     * 
     * @uses Model::getAll([ 'id' => $id ])
     *
     * @param integer $id
     * @return array record
     */
    public function getByID($id) {
        $result = $this->getAll([ "id" => $id ]);
        return $result[0];
    }

    /**
     * Model::getAll
     * 
     * Get all records based on array criteria 
     * 
     * The result is saved into files cache to improve performance
     * If user admin has active session, will not use cache
     * 
     * @param array $data array associative of fields and values
     * @param array $options 
     * 
     * @return array of records
     */
    public function getAll(array $data = [], array $options = []) {
        $cache_prefix = "{$this->table}-{$this->object_type}";
        $cachedId = $cache_prefix .
            (int)STORE_ID . "_" .
            serialize($data) .
            $this->config->get('config_language_id') . "." .
            $this->request->getQuery('hl') . "." .
            $this->request->getQuery('cc') . "." .
            $this->config->get('config_currency') . "." .
            (int)$this->config->get('config_store_id');

        $cached = $this->cache->get($cachedId, $cache_prefix);
        if (!$cached || (bool)$this->user->getId()) {
            $sql = "SELECT ";

            //apply filters to $data
            $_data = $this->applyFilters("select", ["sql"=>"*", "data"=>$data]);
            if (isset($_data["sql"])) $sql .= $_data["sql"];

            $sql .= " FROM `" . DB_PREFIX . "{$this->table}` t ";


            $sort_data = isset($options['sort_data']) && is_array($options['sort_data']) && !empty($options['sort_data']) ? $options['sort_data'] : null;
            $sql .= $this->buildSQLQuery($data, $sort_data);
            $query = $this->db->query($sql);
            $result = $query->rows;
            $result = $this->applyFilters("query_result", $result);

            $this->cache->set($cachedId, $result, $cache_prefix);
            return $query->rows;
        } else {
            return $cached;
        }
    }

    /**
     * Model::getAllTotal
     * 
     * Get count of all records based on array criteria 
     * 
     * The result is saved into files cache to improve performance
     * If user admin has active session, will not use cache
     * 
     * @param array $data array associative of fields and values
     * @param array $options 
     * 
     * @return integer count of all records
     */
    public function getAllTotal(array $data = []) {
        $cache_prefix = "{$this->table}-{$this->object_type}-total";
        $cachedId = $cache_prefix .
            (int)STORE_ID . "_" .
            serialize($data) .
            $this->config->get('config_language_id') . "." .
            $this->request->getQuery('hl') . "." .
            $this->request->getQuery('cc') . "." .
            $this->config->get('config_currency') . "." .
            (int)$this->config->get('config_store_id');

        $cached = $this->cache->get($cachedId, $cache_prefix);
        if (!$cached || (bool)$this->user->getId()) {
            $sql = "SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "{$this->table}` t ";
            $sql .= $this->buildSQLQuery($data, null, true);
            $query = $this->db->query($sql);
            $this->cache->set($cachedId, $query->row['total'], $cache_prefix);
            return $query->row['total'];
        } else {
            return $cached;
        }
    }

    /**
     * Model::buildSQLQuery
     * 
     * Helper function to generate SQL QUERY string for Model::getAll and Model::getAllTotal
     * 
     * @uses Model::applyFilters select {@param array $data, @param array $sort_data, @param bool $countAsTotal}
     * @uses Model::applyFilters join {@param string $sql_query, @param array $data}
     * @uses Model::applyFilters where {@param array $criterias, @param array $data}
     * 
     * @usedby Model::getAll
     * @usedby Model::getAllTotal
     * 
     * @param array $data 
     * @param array $sort_data 
     * @param array $countAsTotal 
     * 
     * @return string 
     */
    protected function buildSQLQuery(array $data, $sort_data = null, $countAsTotal = false):string {
        $criteria = [];
        $sql = "";
        $filters = $data;

        //apply filters to $data
        $_data = $this->applyFilters("buildSQLQuery", ["data"=>$data, "sort_data"=>$sort_data, "countAsTotal"=>$countAsTotal]);
        if (!empty($_data["data"])) $data = $_data["data"];

        if (in_array("descriptions", $this->relations)) {
            $sql .= "LEFT JOIN `" . DB_PREFIX . "description` td ON (t.`{$this->pkey}` = td.`object_id`) ";
            $criteria[] = " td.object_type = '" . $this->db->escape($this->object_type) . "' ";
        }

        if (in_array("stores", $this->relations) && isset($data["store_id"])) {
            $data['store_id'] = !is_array($data['store_id']) && !empty($data['store_id']) ? array((int)$data['store_id']) : (!empty($data["store_id"]) ? $data['store_id'] : STORE_ID);
            $sql .= "LEFT JOIN `" . DB_PREFIX . "object_to_store` t2s ON (t.`{$this->pkey}` = t2s.`object_id`) ";
            $criteria[] = " t2s.object_type = '" . $this->db->escape($this->object_type) . "' ";
            $criteria[] = " t2s.store_id IN (" . implode(', ', array_map(fn ($v): int => intval($v, 10), $data['store_id'])) . ") ";
            unset($data['store_id']);
        }

        if (in_array("categories", $this->relations) && isset($data["category_id"])) {
            $data['category_id'] = !is_array($data['category_id']) && !empty($data['category_id']) ? array((int)$data['category_id']) : $data['category_id'];
            $sql .= "LEFT JOIN `" . DB_PREFIX . "object_to_category` t2c ON (t.`{$this->pkey}` = t2c.`object_id`) ";
            $criteria[] = " t2c.object_type = '" . $this->db->escape($this->object_type) . "' ";
            $criteria[] = " t2c.category_id IN (" . implode(', ', array_map(fn ($v): int => intval($v, 10), $data['category_id'])) . ") ";
            unset($data['category_id']);
        }

        if (in_array("settings", $this->relations) && isset($data["settings"])) {
            $sql .= " LEFT JOIN `" . DB_PREFIX . "setting` ss ON (t.store_id = ss.store_id) ";
            foreach ($data['settings'] as $value) {
                $criteria[] = " LCASE(ss.`key`)  LIKE '%" . $this->db->escape(strtolower(str_replace('-', ' ', $value['key']))) . "%' collate utf8_general_ci ";
                $criteria[] = " CONVERT(LCASE(ss.`value`) USING utf8) LIKE '%" . $this->db->escape(strtolower(str_replace('-', ' ', $value['value']))) . "%' ";
            }
            unset($data['settings']);
        }

        if (isset($data['id'])) {
            $data[$this->pkey] = !is_array($data['id']) && !empty($data['id']) ? array($data['id']) : $data['id'];
        } elseif (isset($data[$this->pkey])) {
            $data[$this->pkey] = !is_array($data[$this->pkey]) && !empty($data[$this->pkey]) ? array($data[$this->pkey]) : $data[$this->pkey];
        }

        if (!empty($data[$this->pkey])) {
            $criteria[] = " t.`{$this->pkey}` IN (" . implode(', ', array_map(fn ($v): int => intval($v, 10), $data[$this->pkey])) . ") ";
            unset($data[$this->pkey]);
        }

        if (in_array("descriptions", $this->relations) && isset($data["language_id"])) {
            $data['language_id'] = !is_array($data['language_id']) && !empty($data['language_id']) ? array((int)$data['language_id']) : (array)$data['language_id'];

            if (!empty($data['language_id'])) {
                $criteria[] = " td.language_id IN (" . implode(', ', array_map(fn ($v): int => intval($v, 10), $data['language_id'])) . ") ";
            } else {
                $criteria[] = " td.language_id IN (" . $this->config->get('config_language_id') . ") ";
            }
            unset($data['language_id']);
        }

        if (isset($this->fields["object_type"]) && isset($data["object_type"])) {
            $criteria[] = " t.object_type = '" . $this->db->escape($data["object_type"]) . "' ";
            unset($data['object_type']);
        } elseif (isset($this->fields["object_type"])) {
            $criteria[] = " t.object_type = '" . $this->db->escape($this->object_type) . "' ";
            unset($data['object_type']);
        }

        if (isset($this->fields["parent_id"]) && isset($data["parent_id"])) {
            $data['parent_id'] = !is_array($data['parent_id']) && (!empty($data['parent_id']) || $data['parent_id'] === 0) ? array((int)$data['parent_id']) : $data['parent_id'];
            $criteria[] = " t.parent_id IN (" . implode(', ', $data['parent_id']) . ") ";
            unset($data['parent_id']);
        }

        if (isset($this->fields["publish_date_start"]) && !empty($data['publish_date_start'])) {
            $criteria[] = "publish_date_start <= '" . date('Y-m-d h:i:s', strtotime($data['publish_date_start'])) . "'";
            unset($data['publish_date_start']);
        }

        if (isset($this->fields["publish_date_end"]) && !empty($data['publish_date_end'])) {
            $criteria[] = "publish_date_end >= '" . date('Y-m-d h:i:s', strtotime($data['publish_date_end'])) . "'";
            unset($data['publish_date_end']);
        }

        if (isset($this->fields["date_start"]) && !empty($data['date_start']) && !empty($data['date_end'])) {
            $criteria[] = " t.date_added BETWEEN '" . $this->db->escape($data['date_start']) . "' AND '" . $this->db->escape($data['date_end']) . "'";
            unset($data['date_start']);
            unset($data['date_end']);
        } elseif (isset($this->fields["date_start"]) && !empty($data['date_start']) && empty($data['date_end'])) {
            $criteria[] = " t.date_added BETWEEN '" . $this->db->escape($data['date_start']) . "' AND '" . $this->db->escape(date('Y-m-d h:i:s')) . "'";
            unset($data['date_start']);
        }

        //TODO: add numeric range fields types logic
        foreach ((array)$data as $field_name => $field_value) {
            if (isset($this->fields[$field_name])) {
                switch ($this->fields[$field_name]["type"]) {
                    case "string":
                    case "varchar":
                    case "text":
                    case "enum":
                    default:
                        $value = strtolower($field_value);
                        if ($value !== mb_convert_encoding(mb_convert_encoding($value, 'UTF-32', 'UTF-8'), 'UTF-8', 'UTF-32'))
                        $value = mb_convert_encoding($value, 'UTF-8', mb_detect_encoding($value));
                        $value = htmlentities($value, ENT_NOQUOTES, 'UTF-8');
                        $value = preg_replace('`&([a-z]{1,2})(acute|uml|circ|grave|ring|cedil|slash|tilde|caron|lig);`i', '\1', $value);
                        $value = html_entity_decode($value, ENT_NOQUOTES, 'UTF-8');
                        if (empty($value)) break;
                        $criteria[] = " LCASE(t.`{$field_name}`) LIKE '%" . $this->db->escape($value) . "%' collate utf8_general_ci ";
                        break;
                    case "int":
                    case "bigint":
                    case "integer":
                        if ($field_value === null) break;
                        if (strpos($field_name, "_id")) {
                            $field_value = !is_array($field_value) && (!empty($field_value) || $field_value === 0) ? array((int)$field_value) : (array)$field_value;

                            if (empty($field_value) && $field_value !== 0) break;

                            $criteria[] = " t.{$field_name} IN (" . implode(', ', $field_value) . ") ";
                        } else {
                            $criteria[] = " t.`{$field_name}` = '" . intval($field_value, 10) . "' ";
                        }
                        break;
                    case "decimal":
                    case "double":
                    case "float":
                        $criteria[] = " t.`{$field_name}` = '" . (float)$field_value . "' ";
                        break;
                    case "boolean":
                        $value = (bool)$field_value === false ? 0 : 1;
                        $criteria[] = " t.`{$field_name}` = '" . (int)$value . "' ";
                        break;
                }
                unset($data[$field_name]);
            }
        }

        if (in_array("descriptions", $this->relations) && isset($data['queries']) && is_array($data['queries'])) {
            $search = $search2 = '';
            foreach ($data['queries'] as $key => $value) {
                if (empty($value)) continue;

                if ($value !== mb_convert_encoding(mb_convert_encoding($value, 'UTF-32', 'UTF-8'), 'UTF-8', 'UTF-32'))
                    $value = mb_convert_encoding($value, 'UTF-8', mb_detect_encoding($value));
                $value = htmlentities($value, ENT_NOQUOTES, 'UTF-8');
                $value = preg_replace('`&([a-z]{1,2})(acute|uml|circ|grave|ring|cedil|slash|tilde|caron|lig);`i', '\1', $value);
                $value = html_entity_decode($value, ENT_NOQUOTES, 'UTF-8');

                $search .= " LCASE(td.`title`) LIKE '%" . $this->db->escape(strtolower($value)) . "%' collate utf8_general_ci OR";

                if (isset($data['search_in_description'])) {
                    $search2 .= " LCASE(td.description) LIKE '%" . $this->db->escape(strtolower($value)) . "%' collate utf8_general_ci OR";
                }
            }
            if (!empty($search)) {
                $criteria[] = " (" . rtrim($search, 'OR') . ")";
            }
            if (!empty($search2)) {
                $criteria[] = " (" . rtrim($search2, 'OR') . ")";
            }
        }

        if (in_array("descriptions", $this->relations) && isset($data['title']) && !empty($data['title'])) {
            $value = strtolower($data['title']);
            if ($value !== mb_convert_encoding(mb_convert_encoding($value, 'UTF-32', 'UTF-8'), 'UTF-8', 'UTF-32'))
                $value = mb_convert_encoding($value, 'UTF-8', mb_detect_encoding($value));
            $value = htmlentities($value, ENT_NOQUOTES, 'UTF-8');
            $value = preg_replace('`&([a-z]{1,2})(acute|uml|circ|grave|ring|cedil|slash|tilde|caron|lig);`i', '\1', $value);
            $value = html_entity_decode($value, ENT_NOQUOTES, 'UTF-8');

            $criteria[] = " LCASE(td.`title`) LIKE '%" . $this->db->escape($value) . "%' collate utf8_general_ci ";
        }

        if (!empty($data['properties'])) {
            $sql .= " LEFT JOIN `" . DB_PREFIX . "property` pp ON (t.`{$this->pkey}` = pp.`object_id`) ";
            foreach ($data['properties'] as $key => $v) {
                $value = strtolower(str_replace('-', ' ', $v["key"]));
                if ($value !== mb_convert_encoding(mb_convert_encoding($value, 'UTF-32', 'UTF-8'), 'UTF-8', 'UTF-32'))
                    $value = mb_convert_encoding($value, 'UTF-8', mb_detect_encoding($value));
                $value = htmlentities($value, ENT_NOQUOTES, 'UTF-8');
                $value = preg_replace('`&([a-z]{1,2})(acute|uml|circ|grave|ring|cedil|slash|tilde|caron|lig);`i', '\1', $value);
                $value = html_entity_decode($value, ENT_NOQUOTES, 'UTF-8');

                $criteria[] = " LCASE(pp.`key`) LIKE '%" . $this->db->escape($value) . "%' collate utf8_general_ci ";

                $value = strtolower(str_replace('-', ' ', $v["value"]));
                if ($value !== mb_convert_encoding(mb_convert_encoding($value, 'UTF-32', 'UTF-8'), 'UTF-8', 'UTF-32'))
                    $value = mb_convert_encoding($value, 'UTF-8', mb_detect_encoding($value));
                $value = htmlentities($value, ENT_NOQUOTES, 'UTF-8');
                $value = preg_replace('`&([a-z]{1,2})(acute|uml|circ|grave|ring|cedil|slash|tilde|caron|lig);`i', '\1', $value);
                $value = html_entity_decode($value, ENT_NOQUOTES, 'UTF-8');

                $criteria[] = " CONVERT(LCASE(pp.`value`) USING utf8) LIKE '%" . $this->db->escape($value) . "%' ";

                $criteria[] = " pp.`object_type` = '{$this->object_type}' ";
            }
        }

        //apply filters to $sql
        $_sql = $this->applyFilters("join", ["sql" => $sql, "data"=> $filters]);
        if (!empty($_sql["sql"])) $sql = $_sql["sql"];

        //apply filters to $criteria
        $_criteria = $this->applyFilters("where", ["criteria"=>$criteria, "data"=> $filters]);
        if (is_array($_criteria["criteria"]) && count($_criteria["criteria"])>0) $criteria = $_criteria["criteria"];

        $sql .= $this->__getCriteriaSQL($criteria);

        if (!$countAsTotal) {
            if (isset($sort_data)) {
                $sql .= " GROUP BY t.`{$this->pkey}`";
                if (in_array("descriptions", $this->relations)) {
                    $sql .= $data['sort'] ? " ORDER BY " . $data['sort'] : " ORDER BY td.title";
                } else {
                    $sql .= $data['sort'] ? " ORDER BY " . $data['sort'] : " ORDER BY t.date_added";
                }
                $sql .= ($data['order'] == 'DESC') ? " DESC" : " ASC";
            }

            if (isset($data['start']) && isset($data['limit'])) {
                if ($data['start'] < 0) $data['start'] = 0;
                if (!$data['limit']) $data['limit'] = 24;

                $sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
            } elseif (isset($data['limit'])) {
                if (!$data['limit']) $data['limit'] = 24;

                $sql .= " LIMIT " . (int)$data['limit'];
            }
        }
        return $sql;
    }

    private function __getCriteriaSQL(array $criterias = []): string
    {
        return !empty($criterias) ? " WHERE " . implode(" AND ", $criterias) : "";
    }

    /**
     * Model::getStores
     * 
     * Get related stores from db 
     * 
     * @param int $id 
     * 
     * @return array of records of stores 
     */
    public function getStores($id) {
        $rows = [];
        $sql = "SELECT * FROM `" . DB_PREFIX . "object_to_store`";
        $sql .= " WHERE `object_id` = '" . (int) $id . "' AND `object_type` = '" . $this->db->escape($this->object_type) . "'";
        $query = $this->db->query( $sql );
        foreach ($query->rows as $result) {
            $rows[] = $result['store_id'];
        }
        return $rows;
    }

    public function __setStores($object_type, $id, $data) {
        if (empty($object_type) || !is_numeric($id)/* || empty($id) */) {
            return null;
        }

        $sql = "DELETE FROM `" . DB_PREFIX . "object_to_store` WHERE ";
        $sql .= "`object_type` = '" . $this->db->escape($object_type) . "' AND ";
        $sql .= "`object_id`   = '" . (int) $id . "' ";
        $this->db->query($sql);

        foreach ($data as $store_id) {
            $store_id = is_numeric($store_id) && !empty($store_id) ? $store_id : 0;

            $sql = "REPLACE INTO `" . DB_PREFIX . "object_to_store` SET ";
            $sql .= "`object_type` = '" . $this->db->escape($object_type) . "', ";
            $sql .= "`object_id`   = '" . (int) $id . "', ";
            $sql .= "`store_id`    = '" . (int) $store_id . "', ";

            $sql = rtrim(trim($sql), ',');
            $this->db->query( $sql );

            //trigger events
            $this->trigger("setStore", [
                "object_id" => $id,
                "object_type" => $object_type,
                "pkey" => $this->pkey,
                "table" => $this->table,
                "store_id" => $store_id,
            ]);
        }
    }

    /**
     * Model::getCategories
     * 
     * Get related categories from db 
     * 
     * @param int $id 
     * 
     * @return array of records of categories 
     */
    public function getCategories($id)
    {
        $rows = [];
        $sql = "SELECT * FROM `" . DB_PREFIX . "object_to_category`";
        $sql .= " WHERE `object_id` = '" . (int) $id . "' AND `object_type` = '" . $this->db->escape($this->object_type) . "'";
        $query = $this->db->query($sql);
        foreach ($query->rows as $result) {
            $rows[] = $result['category_id'];
        }
        return $rows;
    }

    /**
     * Model::__setCategories
     * 
     * Set related categories 
     * 
     * @param string $object_type 
     * @param int $id 
     * @param array $data
     * 
     * @return void
     */
    public function __setCategories(string $object_type, int $id, array $data) {
        if (empty($object_type) || !is_numeric($id)/* || empty($id) */) {
            return null;
        }

        $sql = "DELETE FROM `" . DB_PREFIX . "object_to_category` WHERE ";
        $sql .= "`object_type` = '" . $this->db->escape($object_type) . "' AND ";
        $sql .= "`object_id`   = '" . (int) $id . "' ";
        $this->db->query($sql);
        
        foreach ($data as $category_id) {
            $category_id = is_numeric($category_id) && !empty($category_id) ? $category_id : 0;

            $sql = "REPLACE INTO `" . DB_PREFIX . "object_to_category` SET ";
            $sql .= "`object_type` = '" . $this->db->escape($object_type) . "', ";
            $sql .= "`object_id`   = '" . (int) $id . "', ";
            $sql .= "`category_id`    = '" . (int) $category_id . "', ";

            $sql = rtrim(trim($sql), ',');
            $this->db->query( $sql );

            //trigger events
            $this->trigger("setCategory", [
                "object_id" => $id,
                "object_type" => $object_type,
                "pkey" => $this->pkey,
                "table" => $this->table,
                "category_id" => $category_id,
            ]);
        }
    }

    /**
     * Model::__getDescriptions
     * 
     * Get related descriptions from db 
     * 
     * @param string $object_type 
     * @param int $id 
     * @param int $language_id [optional]
     * 
     * @return array of records of descriptions 
     */
    public function __getDescriptions(string $object_type, int $id, int $language_id = null)
    {
        if ($object_type == null || empty($object_type) || !is_numeric($id)/* || empty($id)*/) {
            return null;
        }

        $sql = "";
        $criteria = $rows = [];
        $criteria[] = " `object_type` = '" . $this->db->escape($object_type) . "' ";
        $criteria[] = " `object_id` = '" . (int)$id . "' ";

        if (!is_null($language_id) && is_numeric($language_id) && !empty($language_id)) {
            $criteria[] = " `language_id` = '" . intval($language_id) . "' ";
        }

        $sql .= $this->__getCriteriaSQL($criteria);

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "description " . $sql);

        foreach ($query->rows as $row) {
            $rows[$row['language_id']]['language_id'] = $row['language_id'];
            $rows[$row['language_id']]['title'] = $row['title'];
            $rows[$row['language_id']]['description'] = $row['description'];
            $rows[$row['language_id']]['seo_title'] = $row['seo_title'];
            $rows[$row['language_id']]['meta_keywords'] = $row['meta_keywords'];
            $rows[$row['language_id']]['meta_description'] = $row['meta_description'];
        }

        $keywords = $this->db->query("SELECT * FROM " . DB_PREFIX . "url_alias " . $sql);

        foreach ($keywords->rows as $row) {
            $rows[$row['language_id']]['keyword'] = $row['keyword'];
        }

        return $rows;
    }

    /**
     * Model::__deleteDescriptions
     * 
     * Delete related descriptions from db 
     * 
     * @param string $object_type 
     * @param int $id 
     * @param int $language_id [optional]
     * 
     * @return void
     */
    public function __deleteDescriptions(string $object_type, int $id, int $language_id = null)
    {
        if ($object_type == null || empty($object_type) || !is_numeric($id)/* || empty($id) */) {
            return null;
        }

        $sql = "DELETE FROM `" . DB_PREFIX . "description` ";
        $criteria = [];
        $criteria[] = " `object_type` = '" . $this->db->escape($object_type) . "' ";
        $criteria[] = " `object_id` = '" . (int)$id . "' ";

        if (!is_null($language_id) && is_numeric($language_id) && !empty($language_id)) {
            $criteria[] = " `language_id` = '" . intval($language_id) . "' ";
        }

        $sql .= $this->__getCriteriaSQL($criteria);
        $this->db->query($sql);

        //trigger events
        $this->trigger("deleteDescription", [
            "object_id" => $id,
            "object_type" => $object_type,
            "pkey" => $this->pkey,
            "table" => $this->table,
            "language_id" => $language_id,
        ]);
    }

    /**
     * Model::__setDescriptions
     * 
     * Set related descriptions from db 
     * 
     * @param string $object_type 
     * @param int $id 
     * @param array $data
     * 
     * @return void
     */
    public function __setDescriptions(string $object_type, int $id, array $data) {
        if ($object_type==null || empty($object_type) || !is_numeric($id)/* || empty($id) */) {
            return null;
        }

        foreach ($data as $language_id => $value) {
            $language_id = is_numeric($language_id) && !empty($language_id) ? $language_id : $value['language_id'];
            $this->__deleteDescriptions($object_type, $id, $language_id);
            /*
            $query = $this->db->query("SELECT * FROM `". DB_PREFIX ."description` ".
                "WHERE `object_type` = '". $this->db->escape($object_type) ."' ".
                "AND `object_id`   = '". (int) $id ."' ".
                "AND `language_id` = '". (int) $language_id ."' ");

            if ($query->num_rows) {
                $sql = "UPDATE `" . DB_PREFIX . "description` SET ";
                $criteria = " WHERE `object_type` = '". $this->db->escape($object_type) ."' ".
                "AND `object_id`   = '". (int) $id ."' ".
                "AND `language_id` = '". (int) $language_id ."' ";
            } else {
                */
                $sql = "INSERT INTO `" . DB_PREFIX . "description` SET ";
                $sql .= "`object_type` = '" . $this->db->escape($object_type) . "', ";
                $sql .= "`object_id`   = '" . (int) $id . "', ";
                $sql .= "`language_id` = '" . (int) $language_id . "', ";

                $criteria = "";
            //}

            if (isset($value['title'])) $sql .= "`title` = '" . $this->db->escape($value['title']) . "', ";
            if (isset($value['description'])) $sql .= "`description` = '" . $this->db->escape($value['description']) . "', ";
            if (isset($value['seo_title'])) $sql .= "`seo_title` = '" . $this->db->escape($value['seo_title']) . "', ";
            if (isset($value['meta_description'])) $sql .= "`meta_description` = '" . $this->db->escape($value['meta_description']) . "', ";
            if (isset($value['meta_keywords'])) $sql .= "`meta_keywords` = '" . $this->db->escape($value['meta_keywords']) . "', ";
            if (isset($value['params'])) $sql .= "`params` = '" . $this->db->escape(serialize($value['params'])) . "', ";

            $sql = rtrim(trim($sql), ',');
            $this->db->query($sql . $criteria);

            //trigger events
            $this->trigger("setDescription", [
                "object_id" => $id,
                "object_type" => $object_type,
                "pkey" => $this->pkey,
                "table" => $this->table,
                "language_id" => $language_id,
                "data" => $value,
            ]);

            if (!empty($value['keyword'])) {
                $this->db->query("REPLACE INTO `" . DB_PREFIX . "url_alias` SET ".
                    "language_id = '" . (int) $language_id . "', ".
                    "object_id   = '" . (int) $id . "', ".
                    "object_type = '" . $this->db->escape($object_type) . "', ".
                    "query       = '" . $this->db->escape($object_type) . "_id=" . (int) $id . "', ".
                    "keyword     = '" . $this->db->escape($value['keyword']) . "'");

                //trigger events
                $this->trigger("setUrlAlias", [
                    "object_id" => $id,
                    "object_type" => $object_type,
                    "pkey" => $this->pkey,
                    "table" => $this->table,
                    "language_id" => $language_id,
                    "keyword" => $value['keyword'],
                ]);
            }
        }
    }

    /**
     * Model::__getProperty
     * 
     * Get a related property from db
     *
     * @param  string $object_type
     * @param  int $id
     * @param  string $group
     * @param  string $key
     * @return mixed property value 
     */
    public function __getProperty(string $object_type, int $id, string $group=null, string $key=null, bool $verbose = false) {
        $rows = $this->__getProperties($object_type, $id, $group, $key);
        return count($rows)>0 ? ($verbose ? $rows[0] : $rows[0]['value']) : false;
    }

    /**
     * Model::__getProperties
     *
     * Get all realted properties from db
     * 
     * @param  string $object_type
     * @param  int $id
     * @param  string $group
     * @param  string $key
     * 
     * @return array of properties
     */
    public function __getProperties(string $object_type, int $id, string $group = null, string $key = null) {
        if ($object_type==null || empty($object_type) || !is_numeric($id)/* || empty($id) */) {
            return null;
        }

        $sql = "SELECT * FROM `" . DB_PREFIX . "property` ";
        $criteria = $rows = [];
        $criteria[] = " `object_type` = '" . $this->db->escape($object_type) . "' ";
        $criteria[] = " `object_id` = '" . (int)$id . "' ";

        if (!is_null($group) && !empty($group) && $group != '*') {
            $criteria[] = " `group` = '" . $this->db->escape($group) . "' ";
        }

        if (!is_null($key) && !empty($key) && $key != '*') {
            $criteria[] = " `key` = '" . $this->db->escape($key) . "' ";
        }

        $sql .= $this->__getCriteriaSQL($criteria);

        $query = $this->db->query($sql);

        foreach ($query->rows as $k=>$row) {
            $rows[$k] = $row;
            $rows[$k]['value'] = unserialize(str_replace("\'", "'", $row['value']));
        }

        return $rows;
    }

    /**
     * Model::__setProperty
     *
     * Set a related property to db
     * 
     * @param  string $object_type
     * @param  int $id
     * @param  string $group
     * @param  string $key
     * @param  mixed $value
     * 
     * @return null|void return null on fail params validation
     */
    public function __setProperty(string $object_type, int $id, string $group, string $key, $value) {
        if (is_numeric($object_type)
            || empty($object_type)
            || empty($group)
            || empty($key)
            || !is_numeric($id)
            /* || empty($id) //allow to save non-entity properties */)
        {
            return null;
        }

        $this->__deleteProperties($object_type, $id, $group, $key, $store_id = 0);
        $this->db->query("INSERT INTO `" . DB_PREFIX . "property` SET ".
            "`object_id`    = '" . (int) $id . "',".
            "`store_id`     = '" . (int) $store_id . "',".
            "`object_type`  = '" . $this->db->escape($object_type) . "',".
            "`group`        = '" . $this->db->escape($group) . "',".
            "`key`          = '" . $this->db->escape($key) . "',".
            "`value`        = '" . $this->db->escape(str_replace("'", "\'", serialize($value))) . "'");

        //trigger events
        $this->trigger("setProperty", [
            "object_id" => $id,
            "object_type" => $object_type,
            "pkey" => $this->pkey,
            "table" => $this->table,
            "group" => $group,
            "key" => $key,
            "value" => $value,
        ]);
    }

    /**
     * Model::__deleteProperties
     *
     * Delete related properties from db
     * 
     * @param  string $object_type
     * @param  int $id
     * @param  string $group
     * @param  string $key
     * 
     * @return null|void return null on fail params validation
     */
    public function __deleteProperties(string $object_type, int $id, string $group, string $key=null) {
        if ($object_type==null || empty($object_type) || !is_numeric($id)/* || empty($id) */) {
            return null;
        }
        
        $sql = "DELETE FROM `" . DB_PREFIX . "property` ";
        $criteria = $rows = [];
        $criteria[] = " `object_type` = '" . $this->db->escape($object_type) . "' ";
        $criteria[] = " `object_id` = '" . (int)$id . "' ";
        
        if (!is_null($group) && !empty($group) && $group != '*') {
            $criteria[] = " `group` = '" . $this->db->escape($group) . "' ";
        }
        
        if (!is_null($key) && !empty($key) && $key != '*') {
            $criteria[] = " `key` = '" . $this->db->escape($key) . "' ";
        }

        $sql .= $this->__getCriteriaSQL($criteria);
        $this->db->query($sql);

        //trigger events
        $this->trigger("deleteProperties", [
            "object_id" => $id,
            "object_type" => $this->object_type,
            "pkey" => $this->pkey,
            "table" => $this->table,
            "group" => $group,
            "key" => $key,
        ]);
    }

    /**
     * Model::__setAllProperties
     *
     * Set related properties to db 
     * 
     * @param  string $object_type
     * @param  int $id
     * @param  string $group
     * @param  array $data
     * @param  int $store_id
     * 
     * @return void
     */
    public function __setAllProperties(string $object_type, int $id, string $group, array $data, int $store_id = 0) {
        if (is_array($data) && !empty($data)) {
            $this->__deleteProperties($object_type, $id, $group);
            foreach ($data as $key => $value) {
                $this->__setProperty($id, $group, $key, $value, $store_id);
            }
            //trigger events
            $this->trigger("updateProperties", [
                "object_id" => $id,
                "object_type" => $this->object_type,
                "pkey" => $this->pkey,
                "table" => $this->table,
                "group" => $group,
                "data" => $data,
                "store_id" => $store_id,
            ]);
        }
    }

    /**
     * Model::__activate
     *
     * Set status 1 to table, if table has status field
     * Else throw error
     * 
     * @param  string $object_type
     * @param  int $id
     * 
     * @return void
     */
    public function __activate(string $object_type, int $id) {
        $this->db->query("UPDATE `".DB_PREFIX."$object_type` SET `status` = '1' WHERE `{$object_type}_id` = '" . (int)$id . "'");
        //trigger events
        $this->trigger("activate", [
            "id" => $id,
            "object_type" => $this->object_type,
            "pkey" => $this->pkey,
            "table" => $this->table,
        ]);
    }

    /**
     * Model::__deactivate
     *
     * Set status 0 to table, if table has status field
     * Else throw error
     * 
     * @param  string $object_type
     * @param  int $id
     * 
     * @return void
     */
    public function __deactivate(string $object_type, int $id) {
        $this->db->query("UPDATE `".DB_PREFIX."$object_type` SET `status` = '0' WHERE `{$object_type}_id` = '" . (int)$id . "'");
        //trigger events
        $this->trigger("deactivate", [
            "id" => $id,
            "object_type" => $this->object_type,
            "pkey" => $this->pkey,
            "table" => $this->table,
        ]);
    }

    /**
     * Model::__toggleStatus
     *
     * Toggle status in table, if table has status field 
     * 
     * @param  int $id
     * @param  string $object_type
     * 
     * @return int new status
     */
    public function __toggleStatus(int $id, string $object_type) {
        $result = $this->db->query("SELECT status FROM `".DB_PREFIX."$object_type` WHERE `{$object_type}_id` = '" . (int)$id . "'");
        $status = ($result->row['status']) ? 0 : 1;
        $this->db->query("UPDATE `" . DB_PREFIX . "$object_type` SET `status` = '". (int)$status ."' WHERE `{$object_type}_id` = '" . (int)$id . "'");

        //trigger events
        $this->trigger("toggleStatus", [
            "id" => $id,
            "object_type" => $this->object_type,
            "pkey" => $this->pkey,
            "table" => $this->table,
        ]);

        return $status;
    }

    /**
     * Model::getDescriptions
     *
     * Get all related descriptions from db
     * Pass language_id to get only correct language descriptions
     * 
     * @uses Model::__getDescriptions
     * 
     * @param  int $id
     * @param  int $language_id [optional]
     * 
     * @return void
     */
    public function getDescriptions(int $id, int $language_id = null)
    {
        return $this->__getDescriptions((!empty($this->description_object_type) ? $this->description_object_type : $this->object_type), $id, $language_id);
    }

    /**
     * Model::setDescriptions
     *
     * Set all related descriptions 
     * 
     * @uses Model::__setDescriptions
     * 
     * @param  int $id
     * @param  array $data
     * 
     * @return void
     */
    public function setDescriptions(int $id, array $data)
    {
        return $this->__setDescriptions((!empty($this->description_object_type) ? $this->description_object_type : $this->object_type), $id, $data);
    }

    /**
     * Model::setStores
     *
     * Set all related stores 
     * 
     * @uses Model::__setStores
     * 
     * @param  int $id
     * @param  array $data
     * 
     * @return void
     */
    public function setStores(int $id, array $data)
    {
        return $this->__setStores($this->object_type, $id, $data);
    }

    /**
     * Model::setCategories
     *
     * Set all related categories 
     * 
     * @uses Model::__setCategories
     * 
     * @param  int $id
     * @param  array $data
     * 
     * @return void
     */
    public function setCategories(int $id, array $data)
    {
        return $this->__setCategories($this->object_type, $id, $data);
    }

    /**
     * Model::activate
     *
     * Set status 1 for this model 
     * 
     * @uses Model::__activate
     * 
     * @param  int $id
     * 
     * @return void
     */
    public function activate($id)
    {
        return $this->__activate($this->table, $id);
    }

    /**
     * Model::deactivate
     *
     * Set status 0 for this model 
     * 
     * @uses Model::__deactivate
     * 
     * @param  int $id
     * 
     * @return void
     */
    public function deactivate($id)
    {
        return $this->__deactivate($this->table, $id);
    }

    /**
     * Model::getProperty
     *
     * Get a related property for this model
     * 
     * @uses Model::__getProperty
     * 
     * @param  int $id
     * @param  string $group
     * @param  string $key
     * 
     * @return mixed property value 
     */
    public function getProperty(int $id, string $group, string $key)
    {
        return $this->__getProperty($this->object_type, $id, $group, $key);
    }

    /**
     * Model::setProperty
     *
     * Set a related property for this model
     * 
     * @uses Model::__setProperty
     * 
     * @param  int $id
     * @param  string $group
     * @param  string $key
     * @param  mixed $value
     * 
     * @return void|null 
     */
    public function setProperty(int $id, string $group, string $key, $value)
    {
        return $this->__setProperty($this->object_type, $id, $group, $key, $value);
    }

    /**
     * Model::deleteProperty
     *
     * Delete related property for this model
     * 
     * If only pass ID, will delete all properties for this model
     * If pass group, will delete all properties for this model and this group 
     * If pass key, will delete just one property for this model, this group and this key 
     * 
     * @uses Model::__deleteProperties
     * 
     * @param  int $id
     * @param  string $group [optional] 
     * @param  string $key [optional]
     * 
     * @return void|null 
     */
    public function deleteProperty($id, $group = '*', $key = '*')
    {
        return $this->__deleteProperties($this->object_type, $id, $group, $key);
    }

    /**
     * Model::getAllProperties
     *
     * Get all related properties for this model
     * 
     * If only pass ID, will get all properties for this model
     * If pass group, will get all properties for this model and this group 
     * 
     * @uses Model::__getProperties
     * 
     * @param  int $id
     * @param  string $group [optional] 
     * 
     * @return array of properties from db
     */
    public function getAllProperties(int $id, string $group = '*')
    {
        return $this->__getProperties($this->object_type, $id, $group);
    }

    /**
     * Model::setAllProperties
     *
     * Set all related properties for this model
     * 
     * @uses Model::deleteProperty
     * @uses Model::setProperty
     * 
     * @param  int $id
     * @param  string $group
     * @param  array $data
     * 
     * @return void
     */
    public function setAllProperties(int $id, string $group, array $data)
    {
        if (is_array($data) && !empty($data)) {
            $this->deleteProperty($id, $group);
            foreach ($data as $key => $value) {
                $this->setProperty($id, $group, $key, $value);
            }
        }
    }
}
