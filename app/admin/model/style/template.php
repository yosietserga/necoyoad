<?php

class ModelStyleTemplate extends Model {

    protected string $object_type  = "template";
    protected string $table        = "template";
    protected string $pkey         = "template_id";

    protected array $fields = [
        "name" => [
            "name"      => "name",
            "type"      => "string",
        ],
        "version" => [
            "name"      => "version",
            "type"      => "string",
        ],
        "for_nt_version" => [
            "name"      => "for_nt_version",
            "type"      => "string",
        ],
        "colors" => [
            "name"      => "colors",
            "type"      => "string",
        ],
        "cols" => [
            "name"      => "cols",
            "type"      => "string",
        ],
        "scheme" => [
            "name"      => "scheme",
            "type"      => "string",
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
}
