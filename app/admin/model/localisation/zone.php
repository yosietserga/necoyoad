<?php

class ModelLocalisationZone extends Model {

    protected string $object_type  = "zone";
    protected string $table        = "zone";
    protected string $pkey         = "zone_id";

    protected array $fields = [
        "code" => [
            "name"      => "code",
            "type"      => "string",
        ],
        "country_id" => [
            "name"      => "country_id",
            "type"      => "int",
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

    public function init() {

        $this->addFilter("query_result", function ($rows) {
            if (!isset($this->modelCountry)) $this->load->model('localisation/country');
            foreach ($rows as $k => $row) {
                $rows[$k]['country'] = $this->modelCountry->getById($row['country_id']);
            }
            return $rows;
        });

        $this->addFilter("join", function ($args) {
            $sql = $args['sql'];
            $data = $args['data'];

            if (isset($data['country_id']) || isset($data['country'])) {
                $sql .= "LEFT JOIN " . DB_PREFIX . "country c ON (t.country_id = c.country_id) ";
                if (isset($data['country'])) {
                    $sql .= "LEFT JOIN " . DB_PREFIX . "description cd ON (c.country_id = cd.object_id) ";
                }
            }

            return ["sql" => $sql, "data" => $data];
        });

        $this->addFilter("where", function ($args) {
            $criteria = $args['criteria'];
            $data = $args['data'];

            if (isset($data['country_id'])) $data['country_id'] = !is_array($data['country_id']) && !empty($data['country_id']) ? array($data['country_id']) : $data['country_id'];

            if (isset($data['country_id']) && !empty($data['country_id'])) {
                $criteria[] = " t.country_id IN (" . implode(', ', $data['country_id']) . ") ";
            }

            if (isset($data['country']) && !empty($data['country'])) {
                $criteria[] = " cd.`object_type` = 'country' ";
                $criteria[] = " LCASE(cd.`title`) LIKE '%" . $this->db->escape(strtolower($data['zone'])) . "%' collate utf8_general_ci ";
            }

            return ["criteria" => $criteria, "data" => $data];
        });
    }
}
