<?php 

class ModelLocalisationTaxClass extends Model {

    protected string $object_type  = "tax_class";
    protected string $table        = "tax_class";
    protected string $pkey         = "tax_class_id";

    protected array $fields = [
        "title" => [
            "name"      => "title",
            "type"      => "string",
        ],
        "description" => [
            "name"      => "description",
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

    protected array $relations = ["stores"];

    public function init()
    {
        $this->on("save", function ($args) {
            $d = $args[0];
            $data = $d['data'];

            if ($d["action"] == "update")
                $this->db->query("DELETE FROM `" . DB_PREFIX . "tax_rate` WHERE `tax_class_id` = '" . (int)$d['id'] . "'");

            if (isset($data['tax_rate'])) {
                foreach ($data['tax_rate'] as $value) {
                    $this->db->query("INSERT INTO `" . DB_PREFIX . "tax_rate` SET " .
                        "`geo_zone_id` = '" . (int)$value['geo_zone_id'] . "', " .
                        "`tax_class_id` = '" . (int)$d['id'] . "', " .
                        "`priority` = '" . (int)$value['priority'] . "', " .
                        "`rate` = '" . (float)$value['rate'] . "', " .
                        "`description` = '" . $this->db->escape($value['description']) . "', " .
                        "`date_added` = NOW()");
                }
            }
        });

        $this->on("delete", function ($args) {
            $data = $args[0];
            $this->db->query("DELETE FROM `" . DB_PREFIX . "tax_rate` WHERE `tax_class_id` = '" . (int)$data['id'] . "'");
        });
    }

	public function getTaxRates($tax_class_id) {
      	$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "tax_rate WHERE tax_class_id = '" . (int)$tax_class_id . "'");
		
		return $query->rows;
	}
			
	public function getAllTotalByGeoZoneId($geo_zone_id) {
      	$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "tax_rate WHERE geo_zone_id = '" . (int)$geo_zone_id . "'");
		
		return $query->row['total'];
	}
}
