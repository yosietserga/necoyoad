<?php

class ModelUserUserGroup extends Model {

    protected string $object_type  = "user_group";
    protected string $table        = "user_group";
    protected string $pkey         = "user_group_id";

    protected array $fields = [
        "name" => [
            "name"      => "name",
            "type"      => "string",
        ],
        "permission" => [
            "name"      => "permission",
            "type"      => "string", //TODO: add json and serialize fields types and let model class handle all logic 
        ],
    ];

    public function init() {
        $this->addFilter("insert", function ($data) {
            $data['permission'] = isset($data['permission']) ? serialize($data['permission']) : '';
            return $data;
        });

        $this->addFilter("update", function ($data) {
            $data['permission'] = isset($data['permission']) ? serialize($data['permission']) : '';
            return $data;
        });

        $this->addFilter("query_result", function ($results) {
            foreach ($results as $k => $row) {
                $results[$k]['permission'] = unserialize($row['permission']);
            }
            return $results;
        });

        $this->on("delete", function ($args) {
            $d = $args[0];
            $id = $d['id'];
            $this->db->query("UPDATE " . DB_PREFIX . "user SET user_group_id = 0 WHERE user_group_id = '" . (int) $id . "'");
        });
    }

    public function addPermission($user_id, $type, $page) {
        $user_query = $this->db->query("SELECT DISTINCT user_group_id FROM " . DB_PREFIX . "user WHERE user_id = '" . (int) $user_id . "'");

        if ($user_query->num_rows) {
            $user_group_query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "user_group WHERE user_group_id = '" . (int) $user_query->row['user_group_id'] . "'");

            if ($user_group_query->num_rows) {
                $data = unserialize($user_group_query->row['permission']);

                $data[$type][] = $page;

                $this->db->query("UPDATE " . DB_PREFIX . "user_group SET permission = '" . serialize($data) . "' WHERE user_group_id = '" . (int) $user_query->row['user_group_id'] . "'");
            }
        }
    }
}
