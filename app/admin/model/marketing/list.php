<?php 

class ModelMarketingList extends Model {

    protected string $object_type  = "contact_list";
    protected string $table        = "contact_list";
    protected string $pkey         = "contact_list_id";

    protected array $fields = [
        "name" => [
            "name"      => "name",
            "type"      => "int",
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

    public function init()
    {
        $this->on("save", function ($args) {
            $d = $args[0];
            $data = $d['data'];

            if (isset($data['contacts'])) {
                foreach ($data['contacts'] as $contact_id) {
                    $this->addContact($d['id'], $contact_id);
                }
            }
        });

        $this->on("delete", function ($args) {
            $d = $args[0];
            $this->db->query("DELETE FROM " . DB_PREFIX . "contact_to_list WHERE contact_list_id = '" . (int)$d['id'] . "'");
        });

        $this->addFilter("copy", function ($args) {
            $id = $args['id'];
            $data = $args['data'];

            $data['name'] = $data['name'] . " - copy";
            $data = array_merge($data, array('contact_list' => $this->getContacts(array($id))));

            return ["id" => $id, "data" => $data];
        });

        $this->addFilter("select", function ($args) {
            $sql = $args['sql'];
            $data = $args['data'];
            $sql = " *, (SELECT COUNT(*) FROM " . DB_PREFIX . "contact_to_list c2ll WHERE c2ll.contact_list_id = t.contact_list_id) AS total_contacts ";
            return ["sql" => $sql, "data" => $data];
        });

        $this->addFilter("join", function ($args) {
            $sql = $args['sql'];
            $data = $args['data'];

            if (isset($data['contact_id']) || isset($data['contact_name']) || isset($data['contact_email'])) {
                $sql .= " LEFT JOIN " . DB_PREFIX . "contact_to_list c2l ON (c2l.contact_list_id = t.contact_list_id) ";
            }

            if (isset($data['contact_name']) || isset($data['contact_email'])) {
                $sql .= " LEFT JOIN " . DB_PREFIX . "contact co ON (c2l.contact_id = co.contact_id) ";
            }

            return ["sql" => $sql, "data" => $data];
        });

        $this->addFilter("where", function ($args) {
            $criteria = $args['criteria'];
            $data = $args['data'];

            $data['contact_id'] = !is_array($data['contact_id']) && !empty($data['contact_id']) ? array($data['contact_id']) : $data['contact_id'];

            if (isset($data['contact_id']) && !empty($data['contact_id'])) {
                $criteria[] = " c2l.contact_id IN (" . implode(', ', $data['contact_id']) . ") ";
            }

            if (isset($data['contact_name']) && !empty($data['contact_name'])) {
                $criteria[] = " LCASE(co.`name`) LIKE '%" . $this->db->escape(strtolower($data['contact_name'])) . "%' collate utf8_general_ci ";
            }

            if (isset($data['contact_email']) && !empty($data['contact_email'])) {
                $criteria[] = " LCASE(co.`email`) LIKE '%" . $this->db->escape(strtolower($data['contact_email'])) . "%' collate utf8_general_ci ";
            }

            return ["criteria" => $criteria, "data" => $data];
        });
    }

    public function addContact($id,$contact_id) {
        $this->db->query("REPLACE INTO " . DB_PREFIX . "contact_to_list SET 
            `contact_id` = '" . (int)$contact_id . "',
            `contact_list_id` = '" . (int)$id . "',
            `date_added` = NOW()");
    }
    
    public function getContacts($data) {
        if ($data) {
    		$sql = "SELECT DISTINCT * FROM " . DB_PREFIX . "contact_to_list c2l ".
            "LEFT JOIN " . DB_PREFIX . "contact co ON (c2l.contact_id=co.contact_id) ".
            "WHERE c2l.contact_list_id IN (". implode(",",$data) .")";
    		$query = $this->db->query($sql);
    		
            foreach ($query->rows as $row) {
                $return[] = array(
                    "contact_id"=>$row['contact_id'],
                    "email"=>$row['email'],
                    "name"=>$row['name']
                );
            }
        }
		return $return;
	}
}
