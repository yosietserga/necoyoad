<?php

class ModelLocalisationGeoZone extends Model {

    protected string $object_type  = "geo_zone";
    protected string $table        = "geo_zone";
    protected string $pkey         = "geo_zone_id";

    protected array $fields = [
        "name" => [
            "name"      => "name",
            "type"      => "string",
        ],
        "description" => [
            "name"      => "description",
            "type"      => "string",
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

    protected array $relations = ["stores"];

    public function init() {
        $this->on("save", function ($args) {
            $d = $args[0];
            $data = $d['data'];

            if ($d["action"] == "update")
                $this->db->query("DELETE FROM `" . DB_PREFIX . "zone_to_geo_zone` WHERE geo_zone_id = '" . (int)$d['id'] . "'");

            $memoItems = [];
            if (isset($data['zone_to_geo_zone'])) {
                foreach ($data['zone_to_geo_zone'] as $value) {
                    if (in_array((int)$d['id'] . (int)$value['zone_id'] . (int)$value['country_id'], $memoItems)) continue;
                    $memoItems[] = (int)$d['id'] . (int)$value['zone_id'] . (int)$value['country_id'];
                    $this->db->query("INSERT INTO `" . DB_PREFIX . "zone_to_geo_zone` SET 
                    country_id = '"  . (int)$value['country_id'] . "', 
                    zone_id = '"  . (int)$value['zone_id'] . "', 
                    geo_zone_id = '"  . (int)$d['id'] . "', 
                    date_added = NOW()");
                }
            }
        });

        $this->on("delete", function ($args) {
            $data = $args[0];
            $this->db->query("DELETE FROM `" . DB_PREFIX . "zone_to_geo_zone` WHERE `geo_zone_id` = '" . (int)$data['id'] . "'");
        });

        $this->addFilter("insert", function ($data) {

            return $data;
        });

        $this->addFilter("join", function($args) {
            $sql = $args['sql'];
            $data = $args['data'];

            if (
                isset($data['zone_id'])
                || isset($data['zone'])
                || isset($data['country_id'])
                || isset($data['country'])
            ) {
                $sql .= "LEFT JOIN " . DB_PREFIX . "zone_to_geo_zone t2 ON (t.geo_zone_id = t2.geo_zone_id) ";
            }

            if (isset($data['zone']) && !empty($data['zone'])) {
                $sql .= "LEFT JOIN `" . DB_PREFIX . "zone` z ON (z.zone_id = t2.zone_id) ";
                $sql .= "LEFT JOIN `" . DB_PREFIX . "description` zd ON (z.zone_id = zd.object_id) ";
            }

            if (isset($data['country']) && !empty($data['country'])) {
                $sql .= "LEFT JOIN " . DB_PREFIX . "country c ON (c.country_id = t2.country_id) ";
                $sql .= "LEFT JOIN " . DB_PREFIX . "description cd ON (c.country_id = cd.object_id) ";
            }

            return ["sql"=>$sql, "data"=>$data];
        });

        $this->addFilter("where", function($args) {
            $criteria = $args['criteria'];
            $data = $args['data'];

            if (isset($data['zone_id'])) $data['zone_id'] = !is_array($data['zone_id']) && !empty($data['zone_id']) ? array($data['zone_id']) : $data['zone_id'];
            if (isset($data['country_id'])) $data['country_id'] = !is_array($data['country_id']) && !empty($data['country_id']) ? array($data['country_id']) : $data['country_id'];

            if (isset($data['zone_id']) && !empty($data['zone_id'])) {
                $criteria[] = " t2.zone_id IN (" . implode(', ', $data['zone_id']) . ") ";
            }

            if (isset($data['country_id']) && !empty($data['country_id'])) {
                $criteria[] = " t2.country_id IN (" . implode(', ', $data['country_id']) . ") ";
            }

            if (isset($data['zone']) && !empty($data['zone'])) {
                $criteria[] = " zd.`object_type` = 'zone' ";
                $criteria[] = " LCASE(zd.`title`) LIKE '%" . $this->db->escape(strtolower($data['zone'])) . "%' collate utf8_general_ci ";
            }

            if (isset($data['country']) && !empty($data['country'])) {
                $criteria[] = " cd.`object_type` = 'country' ";
                $criteria[] = " LCASE(cd.`title`) LIKE '%" . $this->db->escape(strtolower($data['zone'])) . "%' collate utf8_general_ci ";
            }

            return ["criteria" => $criteria, "data" => $data];
        });
    }
    
    public function getZoneToGeoZones($geo_zone_id) {   
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$geo_zone_id . "'");
        return $query->rows;    
    }       

    public function getAllTotalByGeoZoneId($geo_zone_id) {
        return $this->getAllTotal(array(
            'geo_zone_id'=>$geo_zone_id
        ));
    }
    
    public function getAllTotalByCountryId($country_id) {
        return $this->getAllTotal(array(
            'country_id'=>$country_id
        ));
    }   
    
    public function getAllTotalByZoneId($zone_id) {
        return $this->getAllTotal(array(
            'zone_id'=>$zone_id
        ));
    }   
}
