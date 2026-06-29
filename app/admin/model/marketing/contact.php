<?php

class ModelMarketingContact extends Model {

    protected string $object_type  = "contact";
    protected string $table        = "contact";
    protected string $pkey         = "contact_id";

    protected array $fields = [
        "customer_id" => [
            "name"      => "customer_id",
            "type"      => "int",
        ],
        "name" => [
            "name"      => "name",
            "type"      => "string",
        ],
        "email" => [
            "name"      => "email",
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

    public function init()
    {
        $this->on("save", function ($args) {
            $d = $args[0];
            $data = $d['data'];

            if (isset($data['contact_lists'])) {
                $this->setList($d['id'], $data['contact_lists']);
            }
        });

        $this->on("delete", function ($args) {
            $d = $args[0];
            $this->db->query("DELETE FROM " . DB_PREFIX . "contact_to_list WHERE contact_id = '" . (int)$d['id'] . "'");
        });

        $this->addFilter("select", function ($args) {
            $sql = $args['sql'];
            $data = $args['data'];
            $sql = " *, t.date_Added AS created, t.email AS mail ";
            return ["sql" => $sql, "data" => $data];
        });

        $this->addFilter("join", function ($args) {
            $sql = $args['sql'];
            $data = $args['data'];

            $sql .= "LEFT JOIN " . DB_PREFIX . "customer c ON (c.customer_id = t.customer_id) ";

            if (isset($data['contact_list_id'])) {
                $sql .= "LEFT JOIN " . DB_PREFIX . "contact_to_list c2l ON (t.contact_id=c2l.contact_id) ";
            }

            if (isset($data['export'])) {
                $sql .= "LEFT JOIN " . DB_PREFIX . "address a ON (a.address_id = c.address_id) ";
            }


            return ["sql" => $sql, "data" => $data];
        });

        $this->addFilter("where", function ($args) {
            $criteria = $args['criteria'];
            $data = $args['data'];

            $data['contact_list_id'] = !is_array($data['contact_list_id']) && !empty($data['contact_list_id']) ? array($data['contact_list_id']) : $data['contact_list_id'];

            if (isset($data['contact_list_id']) && !empty($data['contact_list_id'])) {
                $criteria[] = " c2l.contact_list_id IN (" . implode(', ', $data['contact_list_id']) . ") ";
            }

            if (isset($data['customer_email']) && !empty($data['customer_email'])) {
                $criteria[] = " LCASE(c.`email`) LIKE '%" . $this->db->escape(strtolower($data['customer_email'])) . "%' collate utf8_general_ci ";
            }

            if (isset($data['customer_name']) && !empty($data['customer_name'])) {
                $criteria[] = " LCASE(CONCAT(c.`firstname`, ' ', c.`lastname`)) LIKE '%" . $this->db->escape(strtolower($data['customer_name'])) . "%' collate utf8_general_ci ";
            }

            return ["criteria" => $criteria, "data" => $data];
        });
    }
    
	public function setList($contact_id, $lists) {
        $this->db->query("DELETE FROM ". DB_PREFIX ."contact_to_list WHERE contact_id = '". (int)$contact_id ."'");
        foreach ($lists as $id) {
            $this->db->query("INSERT INTO ". DB_PREFIX ."contact_to_list SET 
            contact_list_id = '". (int)$id ."',
            contact_id      = '". (int)$contact_id ."',
            date_added      = NOW()");
        }
	}
}
