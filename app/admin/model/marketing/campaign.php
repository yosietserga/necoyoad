<?php
/**
 * ModelMarketingCampaign
 * 
 * @package NecoTienda
 * @author Inversiones Necoyoad, C.A.
 * @copyright 2010
 * @version $Id$
 * @access public
 */
class ModelMarketingCampaign extends Model {

    protected string $object_type  = "campaign";
    protected string $table        = "campaign";
    protected string $pkey         = "campaign_id";

    protected array $fields = [
        "newsletter_id" => [
            "name"      => "newsletter_id",
            "type"      => "int",
        ],
        "name" => [
            "name"      => "name",
            "type"      => "string",
        ],
        "subject" => [
            "name"      => "subject",
            "type"      => "string",
        ],
        "from_name" => [
            "name"      => "from_name",
            "type"      => "string",
        ],
        "from_email" => [
            "name"      => "from_email",
            "type"      => "string",
        ],
        "replyto_email" => [
            "name"      => "replyto_email",
            "type"      => "string",
        ],
        "trace_email" => [
            "name"      => "trace_email",
            "type"      => "string",
        ],
        "trace_click" => [
            "name"      => "trace_click",
            "type"      => "string",
        ],
        "embed_image" => [
            "name"      => "embed_image",
            "type"      => "string",
        ],
        "repeat" => [
            "name"      => "repeat",
            "default"   => 0,
            "type"      => "boolean",
        ],
        "date_start" => [
            "name"      => "date_start",
            "type"      => "date",
        ],
        "date_end" => [
            "name"      => "date_end",
            "type"      => "date",
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
                $this->setContacts($d['id'], $data['contacts']);
            }

            if (isset($data['links'])) {
                foreach ($data['links'] as $link) {
                    $this->addLink($link, $d['id']);
                }
            }
        });

        $this->addFilter("copy", function ($args) {
            $id = $args['id'];
            $data = $args['data'];

            $data['name'] = $data['name'] . " - copy";
            $data = array_merge($data, array('contacts' => $this->getContacts($id)));
            $data = array_merge($data, array('links' => $this->getLinks($id)));

            return ["id" => $id, "data" => $data];
        });

        $this->addHook("delete", function ($data) {
            //TODO: validar que no tenga trabajos de envio pendientes, si es asi mostrar una confirmacion
            $query = $this->db->query(
                "SELECT * FROM `" . DB_PREFIX . "task_exec` te " .
                "LEFT JOIN `" . DB_PREFIX . "task` t ON (te.task_id=t.task_id) " .
                "WHERE object_id = '" . (int)$data['id'] . "' AND object_type = 'campaign'"
            );

            if (!$query->num_rows) {
                //run normal delete process
                parent::delete($data['id']);

                //delete contacts list
                $this->db->query("DELETE FROM " . DB_PREFIX . "campaign_contact WHERE `campaign_id` = '" . (int)$data['id'] . "'");

                //delete task queue 
                $this->db->query("DELETE FROM " . DB_PREFIX . "task_queue WHERE `task_id` IN ( " .
                "SELECT task_id FROM " . DB_PREFIX . "task " .
                    "WHERE object_id = '" . (int)$data['id'] . "' " .
                    "AND object_type = 'campaign'" .
                    ")");

                //delete task 
                $this->db->query("DELETE FROM " . DB_PREFIX ."task " .
                    "WHERE object_id = '" . (int)$data['id'] . "' " .
                    "AND object_type = 'campaign'");
            }

            //return true to prevent delete campaign with active task exec
            return true;
        });

        $this->addFilter("join", function ($args) {
            $sql = $args['sql'];
            $data = $args['data'];


            if (isset($data['contact_id']) || isset($data['contact_name']) || isset($data['contact_email'])) {
                $sql .= " LEFT JOIN " . DB_PREFIX . "campaign_contact cc ON (t.campaign_id = cc.campaign_id) ";
            }


            return ["sql" => $sql, "data" => $data];
        });

        $this->addFilter("where", function ($args) {
            $criteria = $args['criteria'];
            $data = $args['data'];

            $data['contact_id'] = !is_array($data['contact_id']) && !empty($data['contact_id']) ? array($data['contact_id']) : $data['contact_id'];

            if (isset($data['contact_id']) && !empty($data['contact_id'])) {
                $criteria[] = " cc.contact_id IN (" . implode(', ', $data['contact_id']) . ") ";
            }

            if (isset($data['contact_name']) && !empty($data['contact_name'])) {
                $criteria[] = " LCASE(cc.`name`) LIKE '%" . $this->db->escape(strtolower($data['contact_name'])) . "%' collate utf8_general_ci ";
            }

            if (isset($data['contact_email']) && !empty($data['contact_email'])) {
                $criteria[] = " LCASE(cc.`email`) LIKE '%" . $this->db->escape(strtolower($data['contact_email'])) . "%' collate utf8_general_ci ";
            }

            return ["criteria" => $criteria, "data" => $data];
        });
    }
    
    public function setContacts($id, $contacts) {
            $this->db->query("DELETE FROM " . DB_PREFIX . "campaign_contact WHERE campaign_id = '". (int)$id ."'");
            foreach ($contacts as $contact) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "campaign_contact SET 
                  `campaign_id`= '" . (int)$id . "',
                  `contact_id` = '" . (int)$contact['contact_id'] . "',
                  `name`       = '" . $this->db->escape($contact['name']) . "',
                  `email`      = '" . $this->db->escape($contact['email']) . "',
                  `status`     = 1");
            }
    }
    
    public function getContacts($id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "campaign_contact WHERE campaign_id = '" . (int)$id . "'");
        return $query->rows;
    }
    
    public function getLinks($id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "campaign_link WHERE campaign_id = '" . (int)$id . "'");
        return $query->rows;
    }
    
    public function getTasks($id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "task WHERE object_id = '" . (int)$id . "' AND object_type = 'campaign'");
        return $query->rows;
    }
    
    public function getNewsletter($id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "newsletter WHERE newsletter_id = '" . (int)$id . "'");
        return $query->row;
    }
    
	/**
	 * ModelMarketingCampaign::addLink()
     * Registra un enlace a la campa�a para rastrearlo
     * 
     * @see DB::escape()
     * @see DB::query()
     * @see DB::getLastId()
	 * @return int $newsletter_id
	 */
	public function addLink($data,$id) {
      	$this->db->query("INSERT INTO " . DB_PREFIX . "campaign_link SET 
          `campaign_id` = '" . (int)$id . "',
          `url`         = '" . $this->db->escape($data['url']) . "',
          `redirect`    = '" . $this->db->escape($data['redirect']) . "',
          `link`        = '" . $this->db->escape($data['link_index']) . "',
          `date_added`  = NOW()");
	}
}
