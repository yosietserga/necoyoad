<?php

class ModelStoreStore extends Model {


    protected string $table        = "store";
    protected string $pkey         = "store_id";
    protected string $object_type  = "store";

    protected array $fields = [
        "name" => [
            "name"      => "name",
            "type"      => "string",
        ],
        "folder" => [
            "name"      => "folder",
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
        ],
    ];

    public function init()
    {
        $this->on("save", function ($args) {
            $d = $args[0];
            $id = $d['id'];
            $data = $d['data'];

            foreach ($data as $key => $value) {
                $this->setSetting('config', $key, $value, $id);
            }
        });

        $this->on("delete", function ($args) {
            $d = $args[0];

            $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "setting WHERE store_id = '" . (int) $d['id'] . "' AND `key` = 'config_folder'");

            if (!empty($query->row['config_folder'])) {
                $this->deleteFiles(DIR_ROOT . "app/{$query->row['config_folder']}/*");
                $this->deleteFiles(DIR_ROOT . "web/{$query->row['config_folder']}/*");
            }

            $related_tables = array(
                'bank_account',
                'banner',
                'coupon',
                'customer',
                'download',
                'object',
                'template',
                'theme',
                'category',
                'product',
                'manufacturer',
            );

            foreach ($related_tables as $table) {
                $this->db->query("DELETE FROM " . DB_PREFIX . "{$table}_to_store WHERE store_id = '" . (int)$d['id'] . "' ");
            }

            $drop_tables = array(
                'setting',
                'search',
                'store',
                'stat',
            );

            foreach ($drop_tables as $table) {
                $this->db->query("DELETE FROM " . DB_PREFIX . "{$table} WHERE store_id = '" . (int)$d['id'] . "' ");
            }
        });
    }

    public function saveContent($store_id, $data) {
        if (!empty($data['Products'])) {
            $this->db->query("DELETE FROM " . DB_PREFIX . "object_to_store WHERE store_id = '" . (int) $store_id . "'");
            foreach ($data['Products'] as $result) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "object_to_store SET 
                store_id    = '" . (int) $store_id . "', 
                product_id = '" . (int) $result . "'");
            }
        }

        if (!empty($data['Categories'])) {
            $this->db->query("DELETE FROM " . DB_PREFIX . "object_to_store WHERE store_id = '" . (int) $store_id . "'");
            foreach ($data['Categories'] as $result) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "object_to_store SET 
                store_id    = '" . (int) $store_id . "', 
                category_id = '" . (int) $result . "'");
            }
        }

        if (!empty($data['Manufacturers'])) {
            $this->db->query("DELETE FROM " . DB_PREFIX . "object_to_store WHERE store_id = '" . (int) $store_id . "'");
            foreach ($data['Manufacturers'] as $result) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "object_to_store SET 
                store_id    = '" . (int) $store_id . "', 
                manufacturer_id = '" . (int) $result . "'");
            }
        }

        if (!empty($data['Downloads'])) {
            $this->db->query("DELETE FROM " . DB_PREFIX . "object_to_store WHERE store_id = '" . (int) $store_id . "'");
            foreach ($data['Downloads'] as $result) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "object_to_store SET 
                store_id    = '" . (int) $store_id . "', 
                object_type    = 'download', 
                object_id = '" . (int) $result . "'");
            }
        }

        if (!empty($data['Pages'])) {
            $this->db->query("DELETE FROM " . DB_PREFIX . "object_to_store WHERE store_id = '" . (int) $store_id . "'");
            foreach ($data['Pages'] as $result) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "object_to_store SET 
                store_id    = '" . (int) $store_id . "', 
                object_type    = 'page', 
                object_id = '" . (int) $result . "'");
            }
        }

        if (!empty($data['Posts'])) {
            $this->db->query("DELETE FROM " . DB_PREFIX . "object_to_store WHERE store_id = '" . (int) $store_id . "'");
            foreach ($data['Posts'] as $result) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "object_to_store SET 
                object_type    = 'post', 
                object_id = '" . (int) $result . "'");
            }
        }

        if (!empty($data['PostCategories'])) {
            $this->db->query("DELETE FROM " . DB_PREFIX . "object_to_store WHERE store_id = '" . (int) $store_id . "'");
            foreach ($data['PostCategories'] as $result) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "object_to_store SET 
                object_type    = 'post_category', 
                object_id = '" . (int) $result . "'");
            }
        }

        if (!empty($data['Banners'])) {
            $this->db->query("DELETE FROM " . DB_PREFIX . "object_to_store WHERE store_id = '" . (int) $store_id . "'");
            foreach ($data['Banners'] as $result) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "object_to_store SET 
                object_type    = 'banner', 
                object_id = '" . (int) $result . "'");
            }
        }

        if (!empty($data['Menus'])) {
            $this->db->query("DELETE FROM " . DB_PREFIX . "object_to_store WHERE store_id = '" . (int) $store_id . "'");
            foreach ($data['Menus'] as $result) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "object_to_store SET 
                object_type    = 'menu', 
                object_id = '" . (int) $result . "'");
            }
        }

        if (!empty($data['Coupons'])) {
            $this->db->query("DELETE FROM " . DB_PREFIX . "object_to_store WHERE store_id = '" . (int) $store_id . "'");
            foreach ($data['Coupons'] as $result) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "object_to_store SET 
                object_type    = 'coupon', 
                object_id = '" . (int) $result . "'");
            }
        }

        if (!empty($data['BankAccounts'])) {
            $this->db->query("DELETE FROM " . DB_PREFIX . "object_to_store WHERE store_id = '" . (int) $store_id . "'");
            foreach ($data['BankAccounts'] as $result) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "object_to_store SET 
                object_type    = 'bank_account', 
                object_id = '" . (int) $result . "'");
            }
        }

        if (!empty($data['Customers'])) {
            $this->db->query("DELETE FROM " . DB_PREFIX . "object_to_store WHERE store_id = '" . (int) $store_id . "'");
            foreach ($data['Customers'] as $result) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "object_to_store SET 
                object_type    = 'customer', 
                object_id = '" . (int) $result . "'");
            }
        }

        $this->cache->delete('store');
    }

    public function deleteFiles($folder) {
        if (empty($folder))
            return false;
        $files = glob($folder);
        if ($files) {
            foreach ($files as $file) {
                if (file_exists($file)) {
                    if (is_dir($file)) {
                        $this->deleteFiles($file . "/*");
                    } else {
                        unlink($file);
                    }
                }
            }
        }
    }

    public function editMaintenance($data, $group = 'config') {
        if (!isset($data['store_id'])) return false;
        $this->db->query("UPDATE " . DB_PREFIX . "setting SET `value` = '" . $this->db->escape($data['value']) . "' WHERE `key` = 'config_maintenance' AND store_id = '{$data['store_id']}'");
    }

    public function getSetting($group=null, $key=null, $id=0) {
        if ($group==null || empty($group) || $key==null || empty($key)) {
            return null;
        }

        $rows = $this->getSettings($group, $key, $id);

        return $rows[0]['value'];
    }

    public function getSettings($group=null, $key=null, $id=0) {
        if ($group==null || empty($group)) {
            return null;
        }

        $sql = "SELECT * FROM " . DB_PREFIX . "setting ";
        $criteria = $rows = [];
        $criteria[] = " `store_id` = '" . (int)$id . "' ";

        if (!is_null($group) && !empty($group) && $group != '*') {
            $criteria[] = " `group` = '" . $this->db->escape($group) . "' ";
        }

        if (!is_null($key) && !empty($key) && $key != '*') {
            $criteria[] = " `key` = '" . $this->db->escape($key) . "' ";
        }

        if ($criteria) {
            $sql .= " WHERE " . implode(" AND ",$criteria);
        }

        $query = $this->db->query($sql);

        foreach ($query->rows as $k=>$row) {
            $rows[$k] = $row;
            $rows[$k]['value'] = $row['value'];
        }

        return $rows;
    }

    public function setSetting($group, $key, $value, $id=0) {
        if (empty($group)
            || empty($key)
            || !is_numeric($id))
        {
            return null;
        }

        $this->deleteSettings($group, $key, $id);
        $this->db->query("INSERT INTO " . DB_PREFIX . "setting SET ".
            "`store_id`    = '" . (int) $id . "',".
            "`group`        = '" . $this->db->escape($group) . "',".
            "`key`          = '" . $this->db->escape($key) . "',".
            "`value`        = '" . $this->db->escape($value) . "'");
    }

    public function deleteSettings($group=null, $key=null, $id=0) {
        $sql = "DELETE FROM " . DB_PREFIX . "setting ";
        $criteria = $rows = [];
        $criteria[] = " `store_id` = '" . (int)$id . "' ";
        
        if (!is_null($group) && !empty($group) && $group != '*') {
            $criteria[] = " `group` = '" . $this->db->escape($group) . "' ";
        }
        
        if (!is_null($key) && !empty($key) && $key != '*') {
            $criteria[] = " `key` = '" . $this->db->escape($key) . "' ";
        }
        
        if ($criteria) {
            $sql .= " WHERE " . implode(" AND ",$criteria);
        }
        $this->db->query($sql);
    }
}
