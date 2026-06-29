<?php

class ModelMarketingBounce extends Model {

    public function addList($data) {
        $subscribe_count = sizeof($data['customer_id']);
        $query = $this->db->query("INSERT INTO " . DB_PREFIX . "email_lists SET 
        `name` = '" . $this->db->escape($data['name']) . "',
        `bounce_email` = '" . $this->db->escape($data['bounce_email']) . "',
        `replyto_email` = '" . $this->db->escape($data['replyto_email']) . "',
        `format` = '" . $this->db->escape($data['format']) . "',
        `notify` = '" . $this->db->escape($data['notify']) . "',
        `bounce_server` = '" . $this->db->escape($data['bounce_server']) . "',
        `bounce_username` = '" . $this->db->escape($data['bounce_username']) . "',
        `bounce_password` = '" . base64_encode($data['bounce_password']) . "',
        `extra_mail_settings` = '" . $this->db->escape($data['extra_mail_settings']) . "',
        `imap_account` = '" . $this->db->escape($data['imap_account']) . "',
        `process_bounce` = '" . $this->db->escape($data['process_bounce']) . "',
        `date_added` = 'now()',
        `subscribe_count` = '" . (int) $subscribe_count . "'");

        $list_id = $this->db->getLastId();

        if (isset($data['customer_id'])) {
            foreach ($data['customer_id'] as $key => $customer_id) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "email_list_members SET 
                list_id = '" . (int) $list_id . "', 
                customer_id = '" . (int) $customer_id . "',
                email = '" . $this->db->escape($data['email'][$key]) . "',
                subscribed = 1,
                date_added = now()");
            }
        }
        if (isset($data['product_category'])) {
            foreach ($data['product_category'] as $category_id) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "email_list_interes SET 
                list_id = '" . (int) $list_id . "', 
                category_id = '" . (int) $category_id . "',
                date_added = now()");
            }
        }
    }

    public function editList($list_id, $data) {
        $subscribe_count = sizeof($data['customer_id']);
        $this->db->query("UPDATE `" . DB_PREFIX . "email_lists` SET 
        `name` = '" . $this->db->escape($data['name']) . "',
        `bounce_email` = '" . $this->db->escape($data['bounce_email']) . "',
        `replyto_email` = '" . $this->db->escape($data['replyto_email']) . "',
        `format` = '" . $this->db->escape($data['format']) . "',
        `notify` = '" . $this->db->escape($data['notify']) . "',
        `bounce_server` = '" . $this->db->escape($data['bounce_server']) . "',
        `bounce_username` = '" . $this->db->escape($data['bounce_username']) . "',
        `bounce_password` = '" . base64_encode($data['bounce_password']) . "',
        `extra_mail_settings` = '" . $this->db->escape($data['extra_mail_settings']) . "',
        `imap_account` = '" . $this->db->escape($data['imap_account']) . "',
        `process_bounce` = '" . $this->db->escape($data['process_bounce']) . "',
        `date_modified` = 'now()',
        `subscribe_count` = '" . (int) $subscribe_count . "'
        WHERE `list_id` = '" . (int) $list_id . "'");


        $this->db->query("DELETE FROM `" . DB_PREFIX . "email_list_members` WHERE `list_id` = '" . (int) $list_id . "'");
        foreach ($data['customer_id'] as $key => $customer_id) {
            $this->db->query("INSERT INTO " . DB_PREFIX . "email_list_members SET 
            list_id = '" . (int) $list_id . "', 
            customer_id = '" . (int) $customer_id . "',
            email = '" . $this->db->escape($data['email'][$key]) . "',
            subscribed = 1,
            date_added = now(),
            date_modified = now()");
        }

        $this->db->query("DELETE FROM " . DB_PREFIX . "email_list_interes WHERE `list_id` = " . (int) $list_id);
        foreach ($data['product_category'] as $category_id) {
            $this->db->query("INSERT INTO " . DB_PREFIX . "email_list_interes SET 
            list_id = '" . (int) $list_id . "', 
            category_id = '" . (int) $category_id . "',
            date_added = now(),
            date_modified = now()");
        }
    }

    public function getLists($data = array()) {
        $sql = "SELECT * FROM " . DB_PREFIX . "email_lists ";


        if (isset($data['filter_list_id']) && !is_null($data['filter_list_id'])) {
            $sql .= " AND list_id = '" . (int) $data['filter_list_id'] . "'";
        }

        if (isset($data['filter_name']) && !is_null($data['filter_name'])) {
            $sql .= " AND name LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
        }

        if (isset($data['filter_date_added']) && !is_null($data['filter_date_added'])) {
            $sql .= " AND DATE(date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
        }

        if (isset($data['filter_subscribe_count']) && !is_null($data['filter_subscribe_count'])) {
            $sql .= " AND subscribe_count = '" . (float) $data['filter_subscribe_count'] . "'";
        }

        $sort_data = array(
            'list_id',
            'name',
            'date_added',
            'subscribe_count',
        );

        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $sql .= " ORDER BY " . $data['sort'];
        } else {
            $sql .= " ORDER BY list_id";
        }

        if (isset($data['order']) && ($data['order'] == 'DESC')) {
            $sql .= " DESC";
        } else {
            $sql .= " ASC";
        }

        if (isset($data['start']) || isset($data['limit'])) {
            if ($data['start'] < 0) {
                $data['start'] = 0;
            }

            if ($data['limit'] < 1) {
                $data['limit'] = 20;
            }

            $sql .= " LIMIT " . (int) $data['start'] . "," . (int) $data['limit'];
        }

        $query = $this->db->query($sql);

        return $query->rows;
    }

    public function getList($list_id) {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "email_lists WHERE `list_id` = " . (int) $list_id);
        return $query->row;
    }

    public function getMembers($list_id) {
        $customers_data = [];

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "email_list_members WHERE `list_id` = '" . (int) $list_id . "'");

        foreach ($query->rows as $result) {
            $customers_data[] = $result['customer_id'];
        }

        return $customers_data;
    }

    public function getIntereses($list_id) {
        $category_data = [];

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "email_list_interes WHERE `list_id` = '" . (int) $list_id . "'");

        foreach ($query->rows as $result) {
            $category_data[] = $result['category_id'];
        }

        return $category_data;
    }

    public function getTotalLists($data = array()) {
        $sql = "SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "email_lists`";

        if (isset($data['filter_list_id']) && !is_null($data['filter_list_id'])) {
            $sql .= " AND list_id = '" . (int) $data['filter_list_id'] . "'";
        }

        if (isset($data['filter_name']) && !is_null($data['filter_name'])) {
            $sql .= " AND name LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
        }

        if (isset($data['filter_date_added']) && !is_null($data['filter_date_added'])) {
            $sql .= " AND DATE(date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
        }

        if (isset($data['subscribe_count']) && !is_null($data['subscribe_count'])) {
            $sql .= " AND subscribe_count = '" . (float) $data['subscribe_count'] . "'";
        }

        $query = $this->db->query($sql);

        return $query->row['total'];
    }

}

?>