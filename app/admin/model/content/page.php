<?php

/**
 * ModelContentPage
 *
 * @package NecoTienda
 * @author Yosiet Serga
 * @copyright Inversiones Necoyoad, C.A.
 * @version 1.0.0
 * @access public
 * @see Model
 */
class ModelContentPage extends Model {
    protected string $table        = "post";
    protected string $pkey         = "post_id";
    protected string $object_type  = "page";

    protected array $fields = [
        "parent_id"=>[
            "name"      => "parent_id",
            "default"   => 0,
            "required"  => true,
            "type"      => "integer",
        ],
        "image"=>[
            "name"      => "image",
            "type"      => "string",
        ],
        "publish"=>[
            "name"      => "publish",
            "default"   => 1,
            "type"      => "boolean",
        ],
        "allow_reviews"=>[
            "name"      => "allow_reviews",
            "default"   => 1,
            "type"      => "boolean",
        ],
        "date_publish_start"=>[
            "name"      => "date_publish_start",
            "required"  => true,
            "type"      => "date",
        ],
        "date_publish_end"=>[
            "name"      => "date_publish_end",
            "type"      => "date",
        ],
        "template"=>[
            "name"      => "template",
            "type"      => "string",
        ],
        "post_type"=>[
            "name"      => "post_type",
            "default"   => "page",
            "type"      => "string",
        ],
        "status"=>[
            "name"      => "status",
            "default"   => 1,
            "type"      => "boolean",
        ],
        "sort_order"=>[
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
}
