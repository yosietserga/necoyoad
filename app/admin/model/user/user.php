<?php

class ModelUserUser extends Model {

    protected string $object_type  = "user";
    protected string $table        = "user";
    protected string $pkey         = "user_id";

    protected array $fields = [
        "username" => [
            "name"      => "username",
            "type"      => "string",
        ],
        "password" => [
            "name"      => "password",
            "type"      => "string",
        ],
        "firstname" => [
            "name"      => "firstname",
            "type"      => "string",
        ],
        "lastname" => [
            "name"      => "lastname",
            "type"      => "string",
        ],
        "email" => [
            "name"      => "email",
            "type"      => "string",
        ],
        "image" => [
            "name"      => "image",
            "type"      => "string",
        ],
        "user_group_id" => [
            "name"      => "user_group_id",
            "type"      => "integer",
        ],
        "status" => [
            "name"      => "status",
            "default"   => 1,
            "type"      => "boolean",
        ],
        "date_added" => [
            "name"      => "date_added",
            "default"   => "NOW()",
            "type"      => "sql",
        ],
        "date_modified" => [
            "name"      => "date_modified",
            "default"   => "NOW()",
            "type"      => "sql",
            //TODO: add events dynamic, onInsert, onUpdate, onDelete, ...
        ],
    ];

    protected array $relations = ["stores"];

    public function init() {
        $this->on("save", function ($args) {
            $d = $args[0];
            $id = $d['id'];
            $data = $d['data'];
            $action = $d['action'];

            if ($action == "update") {
                if (!empty($data['password'])) {
                    //TODO: improve encrypting 
                    $this->db->query("UPDATE `" . DB_PREFIX . "user` SET password = '" . $this->db->escape(md5($data['password'])) . "' WHERE user_id = '" . (int)$id . "'");
                }
            }

            if (isset($data['image'])) {
                $this->setProperty($id, 'user', 'image', $data['image']);
            }
        });

        $this->addFilter("update", function ($data) {
            if (isset($data['password']) && empty($data['password'])) unset($data['password']);
            return $data;
        });

        $this->on("delete", function ($args) {
            $d = $args[0];
            $id = $d['id'];
            $this->db->query("DELETE FROM `" . DB_PREFIX . "user_activity` WHERE user_id = '" . (int)$id . "'");
        });

        $this->addFilter("join", function ($args) {
            $sql = $args['sql'];
            $data = $args['data'];

            $sql .= " LEFT JOIN `" . DB_PREFIX . "user_group` ug ON (t.user_group_id = ug.user_group_id) ";

            return ["sql" => $sql, "data" => $data];
        });

        $this->addFilter("where", function ($args) {
            $criteria = $args['criteria'];
            $data = $args['data'];

            if (isset($data['user_group']) && !empty($data['user_group'])) {
                $criteria[] = " LCASE(ug.`name`) LIKE '%" . $this->db->escape(strtolower($data['user_group'])) . "%' collate utf8_general_ci ";
            }

            return ["criteria" => $criteria, "data" => $data];
        });
    }
}
