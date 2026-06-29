<?php
class ModelContentPost extends Model
{
    public string $post_type       = "post";
    public string $object_type     = "post";

    protected string $table        = "post";
    protected string $pkey         = "post_id";

    protected array $fields = [
        "parent_id" => [
            "name"      => "parent_id",
            "default"   => 0,
            "type"      => "integer",
        ],
        "post_type" => [
            "name"      => "post_type",
            "default"   => "post",
            "required"  => true,
            "type"      => "string",
        ],
        "image" => [
            "name"      => "image",
            "type"      => "string",
        ],
        "publish" => [
            "name"      => "publish",
            "default"   => 1,
            "type"      => "boolean",
        ],
        "allow_reviews" => [
            "name"      => "allow_reviews",
            "default"   => 1,
            "type"      => "boolean",
        ],
        "date_publish_start" => [
            "name"      => "date_publish_start",
            "required"  => true,
            "type"      => "date",
        ],
        "date_publish_end" => [
            "name"      => "date_publish_end",
            "type"      => "date",
        ],
        "template" => [
            "name"      => "template",
            "type"      => "string",
        ],
        "post_type" => [
            "name"      => "post_type",
            "default"   => "page",
            "type"      => "string",
        ],
        "status" => [
            "name"      => "status",
            "default"   => 1,
            "type"      => "boolean",
        ],
        "sort_order" => [
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

    protected array $relations = ["descriptions", "stores", "categories"];

    public function getRandomPost($data = array()) {
        $data['rand'] = mt_rand();
        $data['sort'] = 'RAND()';
        return $this->getAll($data);
    }

    public function getLatestPost($data = array()) {
        $data['order'] = 'DESC';
        $data['sort'] = 'p.date_added';
        return $this->getAll($data);
    }

    public function getRecommendedPost($data) {
        $query = $this->db->query("SELECT DISTINCT object_id ".
            "FROM " . DB_PREFIX . "stat s ".
            "WHERE s.object_type = 'post' ".
            "AND s.customer_id = '" . (int) $this->customer->getId() . "' ".
            "GROUP BY object_id, object_type ".
            "ORDER BY s.date_added DESC ".
            "LIMIT " . (int)$data['limit']);

        foreach ($query->rows as $k=>$v) {
            $data['post_id'][$k] = $v['object_id'];
        }

        return $this->getAll($data);
    }

    public function getTotalRecommendedPost($data) {
        $query = $this->db->query("SELECT DISTINCT object_id ".
            "FROM " . DB_PREFIX . "stat s ".
            "WHERE s.object_type = 'post' ".
            "AND s.customer_id = '" . (int) $this->customer->getId() . "' ");

        foreach ($query->rows as $k=>$v) {
            $data['post_id'][$k] = $v['object_id'];
        }

        return $this->getAllTotal($data);
    }

    public function getPopularPost($data) {
        $query = $this->db->query("SELECT DISTINCT object_id, COUNT(*) AS total ".
            "FROM " . DB_PREFIX . "stat s ".
            "WHERE s.object_type = 'post' ".
            "GROUP BY (object_id) ".
            "ORDER BY total DESC ".
            "LIMIT " . (int)$data['limit']);

        foreach ($query->rows as $k=>$v) {
            $data['post_id'][$k] = $v['object_id'];
        }

        return $this->getAll($data);
    }

    public function getTotalPopularPost($data) {
        $query = $this->db->query("SELECT DISTINCT object_id, COUNT(*) AS total ".
            "FROM " . DB_PREFIX . "stat s ".
            "WHERE s.object_type = 'post' ".
            "GROUP BY (object_id) ".
            "ORDER BY total DESC ".
            "LIMIT " . (int)$data['limit']);

        foreach ($query->rows as $k=>$v) {
            $data['post_id'][$k] = $v['object_id'];
        }

        return $this->getAll($data);
    }

    public function getPostRelated($id, $data) {
        $data['post_id'] = $id;
        $data['related'] = true;
        return $this->getAll($data);
    }

    public function getTotalPostRelated($id, $data) {
        $data['post_id'] = $id;
        $data['related'] = true;
        return $this->getAllTotal($data);
    }

    public function getAllByCategoryId($data) {
        return $this->getAll($data);
    }

    public function getTotalByCategoryId($data) {
        return $this->getAllTotal($data);
    }

    public function updateStats($id) {
        $this->db->query("UPDATE " . DB_PREFIX . "post SET viewed = (viewed + 1) WHERE post_id = '" . (int) $id . "'");
    }
}
