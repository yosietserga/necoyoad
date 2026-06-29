<?php

class ModelStoreReview extends Model
{
    public function addReview($object_id, $data, $object_type = 'product')
    {
        if (!$object_type) return false;

        $this->db->query("INSERT INTO " . DB_PREFIX . "review SET 
        author      = '" . $this->db->escape($this->customer->getFirstName() . " " . $this->customer->getLastName()) . "', 
        customer_id = '" . (int)$this->customer->getId() . "', 
        store_id   = '" . (int)STORE_ID . "', 
        object_id   = '" . (int)$object_id . "', 
        object_type = '" . $this->db->escape($object_type) . "', 
        text        = '" . $this->db->escape(strip_tags($data['text'])) . "', 
        rating      = '" . (int)$data['rating'] . "', 
        status      = '" . (int)$data['status'] . "', 
        date_added  = NOW()");
        return $this->db->getLastId();
    }

    public function addReply($data, $object_type = 'product')
    {
        if (!(int)$data['review_id'] || !(int)$data['object_id'] || !$object_type) return false;
        $this->db->query("INSERT INTO " . DB_PREFIX . "review SET 
        author      = '" . $this->db->escape($this->customer->getFirstName() . " " . $this->customer->getLastName()) . "', 
        parent_id   = '" . (int)$data['review_id'] . "', 
        customer_id = '" . (int)$this->customer->getId() . "', 
        store_id   = '" . (int)STORE_ID . "', 
        object_id   = '" . (int)$data['object_id'] . "', 
        object_type = '" . $this->db->escape($object_type) . "', 
        text        = '" . $this->db->escape(strip_tags($data['text'])) . "', 
        rating      = '0', 
        status      = '" . (int)$data['status'] . "', 
        date_added  = NOW()");
    }

    public function likeReview($review_id = null, $object_id = null, $object_type = 'product')
    {
        if (!$review_id || !$object_id || !$object_type) return false;

        $result = $this->db->query("SELECT * 
           FROM " . DB_PREFIX . "review_likes 
           WHERE review_id = '" . (int)$review_id . "' 
           AND customer_id = '" . (int)$this->customer->getId() . "'");

        if ($result->num_rows) {
            if ($result->row['like'] == 0) {
                $this->db->query("UPDATE " . DB_PREFIX . "review_likes SET 
                `like`        = 1,
                `dislike`     = 0, 
                `date_added`  = NOW()
                WHERE review_id = '" . (int)$review_id . "' 
                AND customer_id = '" . (int)$this->customer->getId() . "'
                AND object_id   = '" . (int)$object_id . "'
                AND object_type = '" . $this->db->escape($object_type) . "'");
            }
        } else {
            $this->db->query("INSERT INTO " . DB_PREFIX . "review_likes SET  
            `review_id`   = '" . (int)$review_id . "', 
            `customer_id` = '" . (int)$this->customer->getId() . "', 
            `object_id`   = '" . (int)$object_id . "', 
            `object_type` = '" . $this->db->escape($object_type) . "', 
            `store_id`    = '" . (int)STORE_ID . "', 
            `like`        = 1, 
            `dislike`     = 0, 
            `date_added`  = NOW()");
        }
        $result = $this->db->query("SELECT SUM(`like`) AS likes, SUM(dislike) AS dislikes FROM " . DB_PREFIX . "review_likes WHERE review_id = '" . (int)$review_id . "'");
        return $result->row;
    }

    public function dislikeReview($review_id = null, $object_id = null, $object_type = 'product')
    {
        if (!$review_id || !$object_id || !$object_type) return false;

        $result = $this->db->query("SELECT * 
           FROM " . DB_PREFIX . "review_likes 
           WHERE review_id = '" . (int)$review_id . "' 
           AND customer_id = '" . (int)$this->customer->getId() . "'");

        if ($result->num_rows) {
            if ($result->row['dislike'] == 0) {
                $this->db->query("UPDATE " . DB_PREFIX . "review_likes SET 
                `like`        = 0,
                `dislike`     = 1, 
                `date_added`  = NOW()
                WHERE review_id = '" . (int)$review_id . "' 
                AND customer_id = '" . (int)$this->customer->getId() . "'
                AND object_id   = '" . (int)$object_id . "'
                AND object_type = '" . $this->db->escape($object_type) . "'");
            }
        } else {
            $this->db->query("INSERT INTO " . DB_PREFIX . "review_likes SET  
            `review_id`   = '" . (int)$review_id . "', 
            `customer_id` = '" . (int)$this->customer->getId() . "', 
            `object_id`   = '" . (int)$object_id . "', 
            `object_type` = '" . $this->db->escape($object_type) . "', 
            `store_id`    = '" . (int)STORE_ID . "', 
            `dislike`     = 1, 
            `like`        = 0, 
            `date_added`  = NOW()");
        }
        $result = $this->db->query("SELECT SUM(`like`) AS likes, SUM(dislike) AS dislikes FROM " . DB_PREFIX . "review_likes WHERE review_id = '" . (int)$review_id . "'");
        return $result->row;
    }

    public function deleteReview($id)
    {
        $this->db->query("DELETE FROM " . DB_PREFIX . "review WHERE review_id = '" . (int)$id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "review WHERE parent_id = '" . (int)$id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "review_likes WHERE review_id = '" . (int)$id . "'");
    }

    public function getAll(array $data = [], array $options = [])
    {
            $cache_prefix = "shop.reviews";
        $cachedId = $cache_prefix.
            (int)STORE_ID ."_".
            serialize($data).
            $this->config->get('config_language_id') . "." .
            $this->request->getQuery('hl') . "." .
            $this->request->getQuery('cc') . "." .
            $this->customer->getId() . "." .
            $this->config->get('config_currency') . "." .
            (int)$this->config->get('config_store_id');

        $cached = $this->cache->get($cachedId, $cache_prefix);
        if (!$cached || (bool)$this->user->getId()) {
            $sql = "SELECT *, 
            r.review_id AS review_id, 
            r.object_id AS object_id, 
            r.object_type AS object_type, 
            r.date_added AS dateAdded, 
            SUM(rl.like) AS likes, 
            SUM(rl.dislike) AS dislikes ".
                "FROM " . DB_PREFIX . "review r ".
                "LEFT JOIN " . DB_PREFIX . "review_likes rl ON (r.review_id = rl.review_id) ";


            if (!isset($sort_data)) {
                $sort_data = array(
                    'r.sort_order',
                    'r.date_added'
                );
            }

            $sql .= $this->buildSQLQuery($data, $sort_data);

            $query = $this->db->query($sql);
            $this->cache->set($cachedId, $query->rows, $cache_prefix);
            return $query->rows;
        } else {
            return $cached;
        }
    }

    public function getAllAvg($data) {
            $cache_prefix = "shop.reviews.avg";
        $cachedId = $cache_prefix.
            (int)STORE_ID ."_".
            serialize($data).
            $this->config->get('config_language_id') . "." .
            $this->request->getQuery('hl') . "." .
            $this->request->getQuery('cc') . "." .
            $this->customer->getId() . "." .
            $this->config->get('config_currency') . "." .
            (int)$this->config->get('config_store_id');

        $cached = $this->cache->get($cachedId, $cache_prefix);
        if (!$cached || (bool)$this->user->getId()) {
            $sql = "SELECT AVG(rating) AS total ".
                "FROM " . DB_PREFIX . "review r ";

            $sql .= $this->buildSQLQuery($data, null, true);

            $query = $this->db->query($sql);

            $this->cache->set($cachedId, $query->row['total'],$cache_prefix);

            return $query->row['total'];
        } else {
            return $cached;
        }
    }

    public function getAllTotal(array $data = []) {
            $cache_prefix = "shop.reviews.total";
        $cachedId = $cache_prefix.
            (int)STORE_ID ."_".
            serialize($data).
            $this->config->get('config_language_id') . "." .
            $this->request->getQuery('hl') . "." .
            $this->request->getQuery('cc') . "." .
            $this->customer->getId() . "." .
            $this->config->get('config_currency') . "." .
            (int)$this->config->get('config_store_id');

        $cached = $this->cache->get($cachedId, $cache_prefix);
        if (!$cached || (bool)$this->user->getId()) {
            $sql = "SELECT COUNT(*) AS total ".
                "FROM " . DB_PREFIX . "review r ";

            $sql .= $this->buildSQLQuery($data, null, true);

            $query = $this->db->query($sql);

            $this->cache->set($cachedId, $query->row['total'],$cache_prefix);

            return $query->row['total'];
        } else {
            return $cached;
        }
    }

    protected function buildSQLQuery(array $data, $sort_data = null, $countAsTotal = false):string {
        $criteria = [];
        $sql = "";

        $sql .= " LEFT JOIN " . DB_PREFIX . "customer cu ON (r.customer_id = cu.customer_id) ";

        if (isset($data['id'])) {
            $data['review_id'] = !is_array($data['id']) && !empty($data['id']) ? array($data['id']) : $data['id'];
        } elseif (isset($data['review_id'])) {
            $data['review_id'] = !is_array($data['review_id']) && !empty($data['review_id']) ? array($data['review_id']) : $data['review_id'];
        }

        if (isset($data['customer_id'])) $data['customer_id'] = !is_array($data['customer_id']) && !empty($data['customer_id']) ? array($data['customer_id']) : $data['customer_id'];
        if (isset($data['object_id'])) $data['object_id'] = !is_array($data['object_id']) && !empty($data['object_id']) ? array($data['object_id']) : $data['object_id'];
        if (isset($data['object_type'])) $data['object_type'] = isset($data['object_type']) && !empty($data['object_type']) ? $data['object_type'] : '';
        if (isset($data['manufacturer_id'])) $data['manufacturer_id'] = !is_array($data['manufacturer_id']) && !empty($data['manufacturer_id']) ? array($data['manufacturer_id']) : $data['manufacturer_id'];
        if (isset($data['product_id'])) $data['product_id'] = !is_array($data['product_id']) && !empty($data['product_id']) ? array($data['product_id']) : $data['product_id'];
        if (isset($data['category_id'])) $data['category_id'] = !is_array($data['category_id']) && !empty($data['category_id']) ? array($data['category_id']) : $data['category_id'];
        if (isset($data['parent_id'])) $data['parent_id'] = !is_array($data['parent_id']) && (!empty($data['parent_id']) || $data['parent_id'] === 0) ? array($data['parent_id']) : $data['parent_id'];
        if (isset($data['stores'])) $data['stores'] = !is_array($data['stores']) && !empty($data['stores']) ? array($data['stores']) : $data['stores'];
        if (isset($data['customers'])) $data['customers'] = !is_array($data['customers']) && !empty($data['customers']) ? array($data['customers']) : $data['customers'];
        if (isset($data['sellers'])) $data['sellers'] = !is_array($data['sellers']) && !empty($data['sellers']) ? array($data['sellers']) : $data['sellers'];
        if (isset($data['post_id'])) $data['post_id'] = !is_array($data['post_id']) && !empty($data['post_id']) ? array($data['post_id']) : $data['post_id'];
        if (isset($data['page_id'])) $data['page_id'] = !is_array($data['page_id']) && !empty($data['page_id']) ? array($data['page_id']) : $data['page_id'];
        if (isset($data['category_id'])) $data['category_id'] = !is_array($data['category_id']) && !empty($data['category_id']) ? array($data['category_id']) : $data['category_id'];
        if (isset($data['status'])) $data['status'] = isset($data['status']) && is_numeric($data['status']) ? $data['status'] : 1;

        if (isset($data['review_id']) && !empty($data['review_id'])) {
            $criteria[] = " r.review_id IN (" . implode(', ', $data['review_id']) . ") ";
        }

        if (isset($data['customer_id']) && !empty($data['customer_id'])) {
            $criteria[] = " r.customer_id IN (" . implode(', ', $data['customer_id']) . ") ";
        }

        if (isset($data['object_id']) && !empty($data['object_id'])) {
            $criteria[] = " r.object_id IN (" . implode(', ', $data['object_id']) . ") ";
        }

        if (isset($data['parent_id'])) {
            $criteria[] = " r.parent_id IN (" . implode(', ', $data['parent_id']) . ") ";
        }

        if (isset($data['status']) && !empty($data['status'])) {
            $criteria[] = " r.status = '" . intval($data['status']) . "' ";
        }

        if (!empty($data['object_type'])) {
                $data['object_Type'] = strtolower($data['object_type']);
                $criteria['object_type'] = " r.object_type = '". $this->db->escape($data['object_type']) ."' ";
                $sql .= " LEFT JOIN `" . DB_PREFIX . $data['object_type'] ."` t2 ON (r.`object_id` = t2.`". $data['object_type'] ."_id`) ";
        }

        if (isset($data['post_id']) && !empty($data['post_id']) || isset($data['object_type']) && $data['object_type'] === 'post') {
            $sql .= " LEFT JOIN " . DB_PREFIX . "post po ON (r.object_id = po.post_id) ";
            $criteria['object_type'] = " r.object_type = 'post' ";
        }

        if (isset($data['page_id']) && !empty($data['page_id']) || isset($data['object_type']) && $data['object_type'] === 'page') {
            $sql .= " LEFT JOIN " . DB_PREFIX . "post pa ON (r.object_id = pa.post_id) ";
            $criteria['object_type'] = " r.object_type = 'page' ";
        }

        if (isset($data['manufacturer_id']) && !empty($data['manufacturer_id'])) {
            $criteria[] = " r.object_id IN (" . implode(', ', $data['manufacturer_id']) . ") ";
        }

        if (isset($data['category_id']) && !empty($data['category_id'])) {
            $criteria[] = " r.object_id IN (" . implode(', ', $data['category_id']) . ") ";
        }

        if (isset($data['product_id']) && !empty($data['product_id'])) {
            $criteria[] = " r.object_id IN (" . implode(', ', $data['product_id']) . ") ";
        }

        if (isset($data['post_id']) && !empty($data['post_id'])) {
            $criteria[] = " r.object_id IN (" . implode(', ', $data['post_id']) . ") ";
            $criteria[] = isset($data['post_type']) && !empty($data['post_type']) ? " po.post_type = '{$this->db->escape($data['post_type'])}' " : " po.post_type = 'post' ";
        }

        if (isset($data['page_id']) && !empty($data['page_id'])) {
            $criteria[] = " r.object_id IN (" . implode(', ', $data['page_id']) . ") ";
            $criteria[] = " po.post_type = 'page' ";
        }

        if (isset($data['stores']) && !empty($data['stores'])) {
            $criteria[] = " r.object_id IN (" . implode(', ', $data['stores']) . ") ";
        }

        if (isset($data['customers']) && !empty($data['customers'])) {
            $criteria[] = " r.object_id IN (" . implode(', ', $data['customers']) . ") ";
        }

        if (isset($data['sellers']) && !empty($data['sellers'])) {
            $criteria[] = " r.object_id IN (" . implode(', ', $data['sellers']) . ") ";
        }

        if (!empty($data['store_id']) && is_numeric($data['store_id'])) {
            $criteria[] = " r.store_id = '". intval($data['store_id']) ."' ";
        } elseif (!empty($data['store_id']) && is_array($data['store_id'])) {
            $criteria[] = " r.store_id IN ('" . implode("','", $data['store_id']) . "') ";
        } else {
            $criteria[] = " r.store_id = '". (int)STORE_ID ."' ";
        }

        if ($criteria) {
            $sql .= " WHERE " . implode(" AND ",$criteria);
        }

        if (!$countAsTotal) {
            if (isset($data['groupBy']) && !empty($data['groupBy'])) {
                $sql .= " GROUP BY {$this->db->escape($data['groupBy'])} ";
            } else {
                $sql .= " GROUP BY r.review_id";
            }

            if (isset($sort_data)) {
                $sql .= (in_array($data['sort'], $sort_data)) ? " ORDER BY " . $data['sort'] : " ORDER BY r.date_added";
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
        }

        return $sql;
    }

    public function getReviewsByProductId($object_id, $start = 0, $limit = 20)
    {
        return $this->getAll(array(
            'product_id'=>$object_id,
            'start' => $start,
            'limit' => $limit,
            'groupBy' => 'r.review_id'
        ));
    }

    public function getCustomersReviewsByProductId($object_id)
    {
        return $this->getAll(array(
            'product_id'=>$object_id,
            'groupBy' => 'r.customer_id'
        ));
    }

    public function getReviewsByPostId($object_id, $start = 0, $limit = 20)
    {
        return $this->getAll(array(
            'post_id'=>$object_id,
            'start' => $start,
            'limit' => $limit,
            'groupBy' => 'r.review_id'
        ));
    }

    public function getCustomersReviewsByPostId($object_id)
    {
        return $this->getAll(array(
            'post_id'=>$object_id,
            'groupBy' => 'r.customer_id'
        ));
    }

    public function getReviewsByPageId($object_id, $start = 0, $limit = 20)
    {
        return $this->getAll(array(
            'page_id'=>$object_id,
            'start' => $start,
            'limit' => $limit,
            'groupBy' => 'r.review_id'
        ));
    }

    public function getCustomersReviewsByPageId($object_id)
    {
        return $this->getAll(array(
            'post_id'=>$object_id,
            'groupBy' => 'r.customer_id'
        ));
    }

    public function getReplies($review_id)
    {
        return $this->getAll(array(
            'parent_id'=>$review_id
        ));
    }

    public function getAverageRating($object_id, $object_type = 'product') {
        return $this->getAllAvg(array(
            'object_id'=>$object_id,
            'object_type'=>$object_type
        ));
    }

    public function getTotalReviewsByProductId($object_id)
    {
        return $this->getAllTotal(array(
            'product_id'=>$object_id
        ));
    }

    public function getTotalReviewsByPostId($object_id)
    {
        return $this->getAllTotal(array(
            'post_id'=>$object_id
        ));
    }

    public function getTotalReviewsByPageId($object_id)
    {
        return $this->getAllTotal(array(
            'page_id'=>$object_id
        ));
    }

    public function getById($id)
    {
        return $this->getAll(array(
            'review_id'=>$id
        ));
    }

    public function getAllByCustomerTotal($id)
    {
        return $this->getAllTotal(array(
            'customer_id'=>$id
        ));
    }

    public function getAllByCustomer($id)
    {
        return $this->getAll(array(
            'customer_id'=>$id
        ));
    }


    public function getProperty($id, $group, $key) {
        return $this->__getProperty('review', $id, $group, $key);
    }
    public function getAllProperties($id, $group = '*') {
        return $this->__getProperties('review', $id, $group);
    }
}