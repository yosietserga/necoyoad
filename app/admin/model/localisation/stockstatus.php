<?php 

class ModelLocalisationStockStatus extends Model {

    protected string $object_type  = "stock_status";
    protected string $table        = "status";
    protected string $pkey         = "status_id";

    protected array $fields = [
        "ref" => [
            "name"      => "ref",
            "type"      => "string",
            'required'  => true,
        ],
        "object_type" => [
            "name"      => "object_type",
            "default"   => "stock_status",
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
            //TODO: add validations to fields, i.e. maxlength, minlength, patterns, etc.
        ],
    ];

    protected array $relations = ["descriptions", "stores"];
}
