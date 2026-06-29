<?php

class ModelLocalisationCountry extends Model {

    protected string $object_type  = "country";
    protected string $table        = "country";
    protected string $pkey         = "country_id";

    protected array $fields = [
        "name" => [
            "name"      => "name",
            "type"      => "string",
        ],
        "iso_code_2" => [
            "name"      => "iso_code_2",
            "type"      => "string",
        ],
        "iso_code_3" => [
            "name"      => "iso_code_3",
            "type"      => "string",
        ],
        "address_format" => [
            "name"      => "address_format",
            "type"      => "string",
        ],
        "status" => [
            "name"      => "status",
            "default"   => 1,
            "type"      => "boolean",
        ],
    ];

    protected array $relations = ["descriptions"];
}