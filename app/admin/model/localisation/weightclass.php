<?php

class ModelLocalisationWeightClass extends Model {

    protected string $object_type  = "weight_class";
    protected string $table        = "weight_class";
    protected string $pkey         = "weight_class_id";

    protected array $fields = [
        "value" => [
            "name"      => "value",
            "type"      => "float",
        ],
    ];

    protected array $relations = ["descriptions", "stores"];
}
