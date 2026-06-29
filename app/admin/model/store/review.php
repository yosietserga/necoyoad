<?php

class ModelStoreReview extends Model {

    protected string $object_type  = "review";
    protected string $table        = "review";
    protected string $pkey         = "review_id";

    protected array $fields = [
        "object_id" => [
            "name"      => "object_id",
            "required"  => true,
            "type"      => "integer",
        ],
        "store_id" => [
            "name"      => "store_id",
            "required"  => true,
            "type"      => "integer",
        ],
        "customer_id" => [
            "name"      => "customer_id",
            "default"   => 0,
            "type"      => "integer",
        ],
        "parent_id" => [
            "name"      => "parent_id",
            "default"   => 0,
            "type"      => "integer",
        ],
        "object_type" => [
            "name"      => "object_type",
            "required"  => true,
            "type"      => "string",
        ],
        "author" => [
            "name"      => "author",
            "type"      => "string",
        ],
        "text" => [
            "name"      => "text",
            "type"      => "string",
        ],
        "rating" => [
            "name"      => "rating",
            "type"      => "integer",
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
        ],
    ];

    protected array $relations = ["descriptions", "stores"];

    public function init()
    {
        $this->addHook("delete", function ($args) {
            $id = $args['id'];

            $this->db->query("DELETE FROM " . DB_PREFIX . "review_likes WHERE review_id IN (" .
                "SELECT review_id FROM " . DB_PREFIX . "review WHERE review_id = " . (int)$id  . " OR parent_id = " . (int)$id  . " "
            . ")");
        });

        $this->addFilter("select", function ($args) {
            $sql = $args['sql'];
            $data = $args['data'];

            $sql .= " *, " .
                "t.status AS rstatus, " .
                "t.date_added AS created, " .
                "t.review_id AS rid ";

            return ["sql" => $sql, "data" => $data];
        });

        $this->addFilter("join", function ($args) {
            $sql = $args['sql'];
            $data = $args['data'];

            if (isset($data['customer_name']) || isset($data['customer_email'])) {
                $sql .= "LEFT JOIN " . DB_PREFIX . "customer c ON (t.customer_id = c.customer_id) ";
            }

            if (isset($data['title']) && isset($data['object_type'])) {
                $sql .= "LEFT JOIN `" . DB_PREFIX . "{$data['object_type']}` o ON (t.object_id = o.{$data['object_type']}_id) ";
                $sql .= "LEFT JOIN `" . DB_PREFIX . "description` od ON (o.{$data['object_type']}_id = od.object_id) ";
            }

            return ["sql" => $sql, "data" => $data];
        });

        $this->addFilter("where", function ($args) {
            $criteria = $args['criteria'];
            $data = $args['data'];

            if (isset($data['language_id'])) $data['language_id'] = !is_array($data['language_id']) && !empty($data['language_id']) ? array($data['language_id']) : $data['language_id'];

            if (isset($data['customer_name']) && !empty($data['customer_name'])) {
                $criteria[] = " LCASE(CONCAT(c.firstname, ' ', c.lastname)) LIKE '%" . $this->db->escape(strtolower($data['customer_name'])) . "%' collate utf8_general_ci ";
            }

            if (isset($data['customer_email']) && !empty($data['customer_email'])) {
                $criteria[] = " LCASE(c.`email`) LIKE '%" . $this->db->escape(strtolower($data['customer_email'])) . "%' collate utf8_general_ci ";
            }

            if (isset($data['title']) && !empty($data['title']) && isset($data['object_type'])) {
                $criteria[] = " LCASE(od.`title`) LIKE '%" . $this->db->escape(strtolower($data['title'])) . "%' collate utf8_general_ci ";
                $criteria[] = " od.object_type = '" . $this->db->escape(strtolower($data['object_type'])) . "' ";

                if (isset($data['language_id']) && !empty($data['language_id'])) {
                    $criteria[] = " od.language_id IN (" . implode(', ', $data['language_id']) . ") ";
                } else {
                    $criteria[] = " od.language_id IN (" . $this->config->get('config_language_id') . ") ";
                }
            }

            if (isset($data['from_rating']) || isset($data['to_rating'])) {

                if (isset($data['from_rating']) && !empty($data['from_rating'])) {
                    $criteria[] = " t.`rating` >= '" . $this->db->escape((float)$data['from_rating']) . "' ";
                }

                if (isset($data['to_rating']) && !empty($data['to_rating'])) {
                    $criteria[] = " t.`rating` <= '" . $this->db->escape((float)$data['to_rating']) . "' ";
                }
            } elseif (isset($data['rating']) && !empty($data['rating'])) {
                $criteria[] = " t.`rating` = '" . $this->db->escape((float)$data['rating']) . "' ";
            } 

            return ["criteria" => $criteria, "data" => $data];
        });
    }

	public function addReply($data) {
        if (!(int)$data['review_id'] && !(int)$data['object_id']) return false;
        $review = $this->getById($data['review_id']);
       
		$this->db->query("INSERT INTO " . DB_PREFIX . "review SET ".
        "object_id = '" . (int)$review['object_id'] . "', ".
        "object_type = '" . $this->db->escape($review['object_type']) . "', ".
        "parent_id   = '". (int)$data['review_id'] ."', ".
        "customer_id = '" . (int)$data['object_id'] . "', ".
        "author      = '". $this->db->escape($this->user->getUserName()) ."', ".
        "text        = '". $this->db->escape(strip_tags($data['text'])) ."', ".
        "rating      = '0',".
        "status      = '1', ".
        "date_added  = NOW()");
	}
	
    public function getReplies($review_id) {
        return $this->getAll(array(
            'parent_id'=>$review_id
        ));
	}
    
    public function getLikesTotal($review_id) {
        return $this->getAllLikes(array(
            'review_id'=>$review_id
        ));
	}
    
	public function getAllTotalAwaitingApproval() {
        return $this->getAllTotal(array(
            'status'=>0
        ));
	}

    public function getAllAvg($data) {
            $cache_prefix = "admin.reviews.avg";
        $cachedId = $cache_prefix.
            (int)STORE_ID ."_".
            serialize($data).
            $this->config->get('config_language_id') . "." .
            $this->request->getQuery('hl') . "." .
            $this->request->getQuery('cc') . "." .
            $this->config->get('config_currency') . "." .
            (int)$this->config->get('config_store_id');

        $cached = $this->cache->get($cachedId, $cache_prefix);
        if (!$cached || (bool)$this->user->getId()) {
            $sql = "SELECT AVG(rating) AS total FROM " . DB_PREFIX . "review t ";

            $sql .= $this->buildSQLQuery($data, null, true);

            $query = $this->db->query($sql);

            $this->cache->set($cachedId, $query->row['total'],$cache_prefix);

            return $query->row['total'];
        } else {
            return $cached;
        }
    }

    public function getAll(array $data = [], array $options = []) {
        $cache_prefix = "admin.reviews";
        $cachedId = $cache_prefix.
            (int)STORE_ID ."_".
            serialize($data).
            $this->config->get('config_language_id') . "." .
            $this->request->getQuery('hl') . "." .
            $this->request->getQuery('cc') . "." .
            $this->config->get('config_currency') . "." .
            (int)$this->config->get('config_store_id');

        $cached = $this->cache->get($cachedId, $cache_prefix);
        if (!$cached || (bool)$this->user->getId()) {
            $sql = "SELECT *, ".
            "t.status AS rstatus, ".
            "t.date_added AS created, ".
            "t.review_id AS rid ".
            "FROM " . DB_PREFIX . "review t ";

            if (!isset($sort_data)) {
                $sort_data = array(
                    'author',
                    'rating',
                    't.status',
                    't.date_added'
                );
            }
            $sql .= $this->buildSQLQuery($data, $sort_data);
            $query = $this->db->query($sql);
            $return = [];
            foreach ($query->rows as $row) {
            	$likes = $this->getAllLikes($data, $sort_data);
            	$row['likes'] = $likes['total_likes'];
            	$row['dislikes'] = $likes['total_dislikes'];
            	$return[] = $row;
            }
            $this->cache->set($cachedId, $return);
            return $return;
        } else {
            return $cached;
        }
    }

    public function getAllTotal($data=[]) {
        $cache_prefix = "admin.reviews.total";
        $cachedId = $cache_prefix.
            (int)STORE_ID ."_".
            serialize($data).
            $this->config->get('config_language_id') . "." .
            $this->request->getQuery('hl') . "." .
            $this->request->getQuery('cc') . "." .
            $this->config->get('config_currency') . "." .
            (int)$this->config->get('config_store_id');

        $cached = $this->cache->get($cachedId, $cache_prefix);
        if (!$cached || (bool)$this->user->getId()) {
            $sql = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "review t ";
            $sql .= $this->buildSQLQuery($data, null, true);
            $query = $this->db->query($sql);
            $this->cache->set($cachedId, $query->row['total']);

            return $query->row['total'];
        } else {
            return $cached;
        }
    }

    public function getAllLikes($data=[], $sort_data = null) {
        $cache_prefix = "admin.reviews_likes.total";
        $cachedId = $cache_prefix.
            (int)STORE_ID ."_".
            serialize($data).
            $this->config->get('config_language_id') . "." .
            $this->request->getQuery('hl') . "." .
            $this->request->getQuery('cc') . "." .
            $this->config->get('config_currency') . "." .
            (int)$this->config->get('config_store_id');

        $cached = $this->cache->get($cachedId, $cache_prefix);
        if (!$cached || (bool)$this->user->getId()) {
            $sql = "SELECT *, SUM(`like`) AS total_likes, SUM(`dislike`) AS total_dislikes FROM " . DB_PREFIX . "review_likes t ";

            $sql .= $this->buildLikesSQLQuery($data, $sort_data);
            $query = $this->db->query($sql);
            $this->cache->set($cachedId, $query->rows);
            return $query->rows;
        } else {
            return $cached;
        }
    }

    private function buildLikesSQLQuery($data, $sort_data = null) {
        $criteria = [];
        $sql = "";

        $criteria[] = " t.object_type = '". $this->db->escape(strtolower($data['object_type'])) ."' ";

        $data['review_id'] = !is_array($data['review_id']) && !empty($data['review_id']) ? array($data['review_id']) : $data['review_id'];
        $data['object_id'] = !is_array($data['object_id']) && !empty($data['object_id']) ? array($data['object_id']) : $data['object_id'];
        $data['store_id'] = !is_array($data['store_id']) && !empty($data['store_id']) ? array($data['store_id']) : $data['store_id'];
        $data['customer_id'] = !is_array($data['customer_id']) && !empty($data['customer_id']) ? array($data['customer_id']) : $data['customer_id'];
        $data['language_id'] = !is_array($data['language_id']) && !empty($data['language_id']) ? array($data['language_id']) : $data['language_id'];

        if (isset($data['customer_name']) || isset($data['customer_email'])) {
            $sql .= "LEFT JOIN " . DB_PREFIX . "customer c ON (t.customer_id = c.customer_id) ";
        }

        if (isset($data['object_title'])) {
            $sql .= "LEFT JOIN `" . DB_PREFIX . "{$data['object_type']}` o ON (t.object_id = o.{$data['object_type']}_id) ";
            $sql .= "LEFT JOIN `" . DB_PREFIX . "description` od ON (o.{$data['object_type']}_id = od.object_id) ";
        	$criteria[] = " od.object_type = '". $this->db->escape(strtolower($data['object_type'])) ."' ";

	        if (isset($data['language_id']) && !empty($data['language_id'])) {
	            $criteria[] = " od.language_id IN (" . implode(', ', $data['language_id']) . ") ";
	        } else {
                $criteria[] = " od.language_id IN (" . $this->config->get('config_language_id') . ") ";
            }
        }

        if (isset($data['store_id']) && !empty($data['store_id'])) {
            $criteria[] = " t.store_id IN (" . implode(', ', $data['store_id']) . ") ";
        }

        if (isset($data['review_id']) && !empty($data['review_id'])) {
            $criteria[] = " t.review_id IN (" . implode(', ', $data['review_id']) . ") ";
        }

        if (isset($data['object_id']) && !empty($data['object_id'])) {
            $criteria[] = " t.object_id IN (" . implode(', ', $data['object_id']) . ") ";
        }

        if (isset($data['customer_name']) && !empty($data['customer_name'])) {
            $criteria[] = " LCASE(CONCAT(c.firstname, ' ', c.lastname)) LIKE '%" . $this->db->escape(strtolower($data['customer_name'])) . "%' collate utf8_general_ci ";
        }

        if (isset($data['customer_email']) && !empty($data['customer_email'])) {
            $criteria[] = " LCASE(c.`email`) LIKE '%" . $this->db->escape(strtolower($data['customer_email'])) . "%' collate utf8_general_ci ";
        }

        if (isset($data['object_title']) && !empty($data['object_title'])) {
            $criteria[] = " LCASE(od.`title`) LIKE '%" . $this->db->escape(strtolower($data['object_title'])) . "%' collate utf8_general_ci ";
        }

        if (!empty($data['date_start']) && !empty($data['date_end'])) {
            $criteria[] = " t.date_added BETWEEN '". $this->db->escape($data['date_start']) ."' AND '". $this->db->escape($data['date_end']) ."'";
        } elseif (!empty($data['date_start']) && empty($data['date_end'])) {
            $criteria[] = " t.date_added BETWEEN '". $this->db->escape($data['date_start']) ."' AND '". $this->db->escape(date('Y-m-d h:i:s')) ."'";
        }

        if ($criteria) {
            $sql .= " WHERE " . implode(" AND ",$criteria);
        }

        if (isset($sort_data)) {
            $sql .= " GROUP BY t.review_id";
            $sql .= (in_array($data['sort'], $sort_data)) ? " ORDER BY " . $data['sort'] : " ORDER BY t.date_added";
            $sql .= ($data['order'] == 'DESC') ? " DESC" : " ASC";
        }

        if ($data['start'] && $data['limit']) {
            if ($data['start'] < 0) $data['start'] = 0;
            if (!$data['limit']) $data['limit'] = 24;

            $sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
        } elseif ($data['limit']) {
            if (!$data['limit']) $data['limit'] = 24;

            $sql .= " LIMIT ". (int)$data['limit'];
        }
        
        return $sql;
    }
}
