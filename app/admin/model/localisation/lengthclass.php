<?php

class ModelLocalisationLengthClass extends Model {

    protected string $object_type  = "length_class";
    protected string $table        = "length_class";
    protected string $pkey         = "length_class_id";

    protected array $fields = [
        "value" => [
            "name"      => "value",
            "type"      => "float",
        ],
    ];

    protected array $relations = ["descriptions", "stores"];
}
