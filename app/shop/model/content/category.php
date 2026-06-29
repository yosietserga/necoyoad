<?php

class ModelContentCategory extends Model
{

    protected string $object_type  = "post_category";
    protected string $table        = "category";
    protected string $pkey         = "category_id";

    protected array $fields = [
        "parent_id" => [
            "name"      => "parent_id",
            "default"   => 0,
            "type"      => "integer",
        ],
        "object_type" => [
            "name"      => "object_type",
            "default"   => "post_category",
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

    protected array $relations = ["descriptions", "stores"];

    public function updateStats($id) {
        $this->db->query("UPDATE " . DB_PREFIX . "category SET viewed = (viewed + 1) WHERE category_id = '" . (int) $id . "'");
    }
}
