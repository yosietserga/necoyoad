<?php

class ModelLocalisationLanguage extends Model {

    protected string $table        = "language";
    protected string $pkey         = "language_id";
    protected string $object_type  = "language";

    protected array $fields = [
        "name" => [
            "name"      => "name",
            "type"      => "string",
        ],
        "code" => [
            "name"      => "code",
            "type"      => "string",
        ],
        "locale" => [
            "name"      => "locale",
            "type"      => "string",
        ],
        "directory" => [
            "name"      => "directory",
            "type"      => "string",
        ],
        "filename" => [
            "name"      => "filename",
            "type"      => "string",
        ],
        "image" => [
            "name"      => "image",
            "type"      => "string",
        ],
        "status" => [
            "name"      => "status",
            "default"   => 1,
            "type"      => "boolean",
        ],
        "sort_order" => [
            "name"      => "sort_order",
            "default"   => 0,
            "type"      => "integer",
        ],
    ];

    public function init()
    {
        $this->on("save", function ($args) {
            $data = $args[0];
            if ($data["action"] != "insert") return;
            $tables = array(
                'description',
            );

            foreach ($tables as $table_name) {

                $query = $this->db->query("SELECT * FROM " . DB_PREFIX . $table_name . " WHERE language_id = '" . (int)$this->config->get('config_language_id') . "'");

                foreach ($query->rows as $value) {
                    $keys = array_keys($value);
                    $values = array_values($value);
                    if (strpos($keys[0], 'description_id') > -1) {
                        array_shift($keys);
                        array_shift($values);
                    }

                    $s = '';
                    foreach ($keys as $k => $v) {
                        if ($keys[$k] == 'language_id')
                            $s .= " `$keys[$k]` = '{$data["id"]}', ";
                        elseif ($keys[$k] == 'date_added')
                            $s .= " `$keys[$k]` = NOW(), ";
                        else
                            $s .= " `$keys[$k]` = '{$this->db->escape($values[$k])}', ";
                    }
                    $s = rtrim($s, ', ');
                    $q = "REPLACE INTO " . DB_PREFIX . $table_name . " SET " . $s;
                    $this->db->query($q);
                }
                $this->cache->delete(str_replace('_description', '', $table_name));
            }
        });

        $this->on("delete", function ($args) {
            $data = $args[0];
            $tables = array(
                'status',
                'description',
            );

            foreach ($tables as $table_name) {
                $q = "DELETE FROM " . DB_PREFIX . $table_name . " WHERE language_id = '" . (int)$data["id"] . "'";
                $this->db->query($q);
                $this->cache->delete($table_name);
            }
        });
    }
}
