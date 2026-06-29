<?php

class ModelAccountBalance extends Model
{
    public function add($data)
    {
        $this->db->query("INSERT INTO `" . DB_PREFIX . "balance` SET
         `customer_id`      = '" . (int)$this->customer->getId() . "',
         `type`             = '" . $this->db->escape($data['type']) . "',
         `description`      = '" . $this->db->escape($data['description']) . "',
         `amount`           = '" . round((float)$data['amount'], 2) . "',
         `amount_available` = '" . round((float)$data['amount_available'], 2) . "',
         `amount_blocked`   = '" . round((float)$data['amount_blocked'], 2) . "',
         `amount_total`     = '" . round((float)$data['amount_total'], 2) . "',
         `date_added`       = NOW()");
        return $this->db->getLastId();
    }

    public function getById($id)
    {
        $sql = "SELECT * FROM `" . DB_PREFIX . "balance` ";

        $criteria = [];

        $criteria[] = " customer_id = '" . (int)$this->customer->getId() . "' ";
        $criteria[] = " balance_id = '" . (int)$id . "' ";
        if ($criteria) {
            $sql .= " WHERE " . implode(" AND ", $criteria);
        }

        $query = $this->db->query($sql);

        return $query->row;
    }

    public function getBalances($data = null)
    {
        if ($data['start'] < 0) $data['start'] = 0;

        $sql = "SELECT * FROM `" . DB_PREFIX . "balance` ";

        $criteria = [];

        $criteria[] = " customer_id = '" . (int)$this->customer->getId() . "' ";
        $criteria[] = " balance_id = '" . (int)$id . "' ";

        if ($criteria) {
            $sql .= " WHERE " . implode(" AND ", $criteria);
        }

        $sql .= "ORDER BY date_added DESC ";

        if ($data['start'] < 0) {
            $data['start'] = 0;
        }

        if (!$data['limit'] || $data['limit'] < 1) {
            $data['limit'] = 50;
        }

        $sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];

        $query = $this->db->query($sql);
        return $query->rows;
    }

    public function getTotalBalances($data = null)
    {
        $sql = "SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "balance` ";

        $criteria = [];

        $criteria[] = " customer_id = '" . (int)$this->customer->getId() . "' ";

        if ($criteria) {
            $sql .= " WHERE " . implode(" AND ", $criteria);
        }

        $query = $this->db->query($sql);

        return $query->row['total'];
    }

    public function getLastBalance()
    {
        $credits = $this->db->query("SELECT * 
        FROM `" . DB_PREFIX . "balance` 
        WHERE customer_id = '" . (int)$this->customer->getId() . "' 
        ORDER BY balance_id DESC 
        LIMIT 1");

        return $credits->row;

    }
}