<?php

/**
 * ModelStoreProduct
 * 
 * @package NecoTienda
 * @author Yosiet Serga
 * @copyright Inversiones Necoyoad, C.A.
 * @version 1.0.0
 * @access public
 * @see Model
 */
class ModelStoreProduct extends Model {

    protected string $object_type  = "product";
    protected string $table        = "product";
    protected string $pkey         = "product_id";

    protected array $fields = [
        "model" => [
            "name"      => "model",
            "required"  => true,
            "type"      => "string",
        ],
        "weight_class_id" => [
            "name"      => "weight_class_id",
            "type"      => "integer",
        ],
        "length_class_id" => [
            "name"      => "length_class_id",
            "type"      => "integer",
        ],
        "tax_class_id" => [
            "name"      => "tax_class_id",
            "type"      => "integer",
        ],
        "owner_id" => [
            "name"      => "owner_id",
            "type"      => "integer",
        ],
        "manufacturer_id" => [
            "name"      => "manufacturer_id",
            "type"      => "integer",
        ],
        "stock_status_id" => [
            "name"      => "stock_status_id",
            "type"      => "integer",
        ],
        "sku" => [
            "name"      => "sku",
            "type"      => "string",
        ],
        "location" => [
            "name"      => "location",
            "type"      => "string",
        ],
        "quantity" => [
            "name"      => "quantity",
            "type"      => "integer",
        ],
        "type" => [
            "name"      => "type",
            "type"      => "string",
        ],
        "image" => [
            "name"      => "image",
            "type"      => "string",
        ],
        "shipping" => [
            "name"      => "shipping",
            "type"      => "integer",
        ],
        "price" => [
            "name"      => "price",
            "type"      => "float",
        ],
        "weight" => [
            "name"      => "weight",
            "type"      => "float",
        ],
        "length" => [
            "name"      => "length",
            "type"      => "float",
        ],
        "width" => [
            "name"      => "width",
            "type"      => "float",
        ],
        "height" => [
            "name"      => "height",
            "type"      => "float",
        ],
        "viewed" => [
            "name"      => "viewed",
            "type"      => "integer",
        ],
        "subtract" => [
            "name"      => "subtract",
            "type"      => "integer",
        ],
        "minimum" => [
            "name"      => "minimum",
            "type"      => "integer",
        ],
        "cost" => [
            "name"      => "cost",
            "type"      => "float",
        ],
        "sort_order" => [
            "name"      => "sort_order",
            "type"      => "integer",
        ],
        "date_available" => [
            "name"      => "date_available",
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
        ],
    ];

    protected array $relations = ["categories", "descriptions", "stores"];

    public function init()
    {
        $this->on("save", function ($args) {
            $d = $args[0];
            $id = $d['id'];
            $data = $d['data'];
            $action = $d['action'];

            if ($action == "update") {
                $this->deleteSpecials($id);
                $this->deleteImages($id);
                $this->deleteDownloads($id);
                $this->deleteDiscounts($id);
                $this->deleteOptions($id);
                $this->deleteRelated($id);
            }

            if (isset($data['attributes'])) {
                $this->setAttributes(array(
                    'product_id' => $id,
                    'attributes' => $data['attributes'],
                ));
            }

            if (isset($data['options'])) {
                foreach ($data['options'] as $product_option) {
                    $product_option['product_id'] = $id;
                    $this->setOption($product_option);
                }
            }

            if (isset($data['discounts'])) {
                foreach ($data['discounts'] as $value) {
                    $value['product_id'] = $id;
                    $this->setDiscount($value);
                }
            }

            if (isset($data['specials'])) {
                foreach ($data['specials'] as $value) {
                    $value['product_id'] = $id;
                    $this->setSpecial($value);
                }
            }

            if (isset($data['images'])) {
                foreach ($data['images'] as $value) {
                    $this->setImage(array(
                        'product_id' => $id,
                        'image' => $value
                    ));
                }
            }

            if (isset($data['downloads'])) {
                foreach ($data['downloads'] as $download_id) {
                    $this->setDownload(array(
                        'product_id' => $id,
                        'download_id' => $download_id
                    ));
                }
            }

            if (isset($data['related'])) {
                foreach ($data['related'] as $related_id) {
                    $this->setRelated(array(
                        'product_id' => $id,
                        'related_id' => $related_id
                    ));
                }
            }

            $this->cache->delete('product');
            $this->cache->delete('products');
        });
    
        $this->on("delete", function ($args) {
            $d = $args[0];
            $id = $d['id'];

            $product_tables = array(
                'option',
                'option_description',
                'option_value',
                'option_value_description',
                'discount',
                'image',
                'related',
                'to_download',
                'tags',
            );

            foreach ($product_tables as $table) {
                $this->db->query("DELETE FROM " . DB_PREFIX . "product_{$table} WHERE product_id = '" . (int) $id . "' ");
            }

            $this->cache->delete('product');
            $this->cache->delete('products');
        });

        $this->addFilter("select", function ($args) {
            $sql = $args['sql'];
            $data = $args['data'];
            $sql = "  *, " .
                    "t.product_id AS pid," .
                    "td.description AS description," .
                    "td.title AS title, " .
                    "t.image AS image, " .
                    "t.status AS status, " .
                    "t.date_added AS date_added, " .
                    "scd.title AS stock_status, " .
                    "m.name AS manufacturer, " .
                    "tc.title AS tax_class, " .
                    "wcd.title AS weight_class, " .
                    //"lcd.title AS length_class, ".
                    "t.viewed AS viewed, " .
                    "t.sort_order AS sort_order ";
            return ["sql" => $sql, "data" => $data];
        });

        $this->addFilter("join", function ($args) {
            $sql = $args['sql'];
            $data = $args['data'];

            $sql .= "LEFT JOIN `" . DB_PREFIX . "status` ss ON (t.stock_status_id = ss.status_id AND ss.object_type = 'stock_status') ";
            $sql .= "LEFT JOIN `" . DB_PREFIX . "description` scd ON (ss.status_id = scd.object_id AND scd.object_type = 'stock_status') ";
            $sql .= "LEFT JOIN `" . DB_PREFIX . "manufacturer` m ON (t.manufacturer_id = m.manufacturer_id) ";
            $sql .= "LEFT JOIN `" . DB_PREFIX . "tax_class` tc ON (t.tax_class_id = tc.tax_class_id) ";
            $sql .= "LEFT JOIN `" . DB_PREFIX . "weight_class` wc ON (t.weight_class_id = wc.weight_class_id) ";
            $sql .= "LEFT JOIN `" . DB_PREFIX . "description` wcd ON (wc.weight_class_id = wcd.object_id AND wcd.object_type = 'weight_class') ";

            return ["sql" => $sql, "data" => $data];
        });

        $this->addFilter("where", function ($args) {
            $criteria = $args['criteria'];
            $data = $args['data'];

            if (isset($data['language_id'])) $data['language_id'] = !is_array($data['language_id']) && !empty($data['language_id']) ? array($data['language_id']) : $data['language_id'];

            if (isset($data['language_id']) && !empty($data['language_id'])) {
                $criteria[] = " td.language_id IN (" . implode(', ', $data['language_id']) . ") ";
                //$criteria[] = " wcd.language_id IN (" . implode(', ', $data['language_id']) . ") ";
                $criteria[] = " scd.language_id IN (" . implode(', ', $data['language_id']) . ") ";
            } else {
                $criteria[] = " td.language_id IN (" . $this->config->get('config_language_id') . ") ";
                //$criteria[] = " wcd.language_id IN (" . $this->config->get('config_language_id') . ") ";
                $criteria[] = " scd.language_id IN (" . $this->config->get('config_language_id') . ") ";
            }

            if (isset($data['from_price']) || isset($data['to_price'])) {

                if (isset($data['from_price']) && !empty($data['from_price'])) {
                    $criteria[] = " t.`price` >= '" . $this->db->escape((float)$data['from_price']) . "' ";
                }

                if (isset($data['to_price']) && !empty($data['to_price'])) {
                    $criteria[] = " t.`price` <= '" . $this->db->escape((float)$data['to_price']) . "' ";
                }
            } elseif (isset($data['price']) && !empty($data['price'])) {
                $criteria[] = " t.`price` = '" . $this->db->escape((float)$data['price']) . "' ";
            }

            if (isset($data['from_quantity']) || isset($data['to_quantity'])) {

                if (isset($data['from_quantity']) && !empty($data['from_quantity'])) {
                    $criteria[] = " t.`quantity` >= '" . $this->db->escape((int)$data['from_quantity']) . "' ";
                }

                if (isset($data['to_quantity']) && !empty($data['to_quantity'])) {
                    $criteria[] = " t.`quantity` <= '" . $this->db->escape((int)$data['to_quantity']) . "' ";
                }
            } elseif (isset($data['quantity']) && !empty($data['quantity'])) {
                $criteria[] = " t.`quantity` = '" . $this->db->escape((float)$data['quantity']) . "' ";
            }

            if (!empty($data['publish_date_start'])) {
                $criteria[] = "date_available <= '" . date('Y-m-d h:i:s', strtotime($data['publish_date_start'])) . "'";
            }

            if (!empty($data['publish_date_end'])) {
                $criteria[] = "date_available >= '" . date('Y-m-d h:i:s', strtotime($data['publish_date_end'])) . "'";
            }

            return ["criteria" => $criteria, "data" => $data];
        });
    }
    
    public function setAttributes($data) {
        $this->deleteProperty($data['product_id'], 'attribute');
        $this->deleteProperty($data['product_id'], 'attributes');
        $this->deleteProperty($data['product_id'], 'attribute_group');

        $attribute_group_ids = [];
        foreach ($data['attributes'] as $attribute_group_id => $attributes) {
            $attribute_group_ids[] = $attribute_group_id;
            foreach ($attributes as $key => $value) {
                $this->setProperty($data['product_id'], 'attribute', $key .":". $attribute_group_id, $value);
            }
        }

        $this->setProperty($data['product_id'], 'attributes', 'admin_attributes', $data['attributes']);
        $this->setProperty($data['product_id'], 'attribute_group', 'attribute_group_id', $attribute_group_ids);
    }

    public function setRelated($data) {
        $this->db->query("INSERT INTO " . DB_PREFIX . "product_related SET 
        product_id = '" . (int) $data['product_id'] . "', 
        related_id = '" . (int) $data['related_id'] . "'");
        $this->db->query("INSERT INTO " . DB_PREFIX . "product_related SET 
        product_id = '" . (int) $data['related_id'] . "', 
        related_id = '" . (int) $data['product_id'] . "'");
    }

    public function setDownload($data) {
        $this->db->query("INSERT INTO " . DB_PREFIX . "product_to_download SET 
            product_id = '" . (int)$data['product_id'] . "',
            download_id = '" . (int)$data['download_id'] . "'");
    }

    public function setImage($data) {
        $this->db->query("INSERT INTO " . DB_PREFIX . "product_image SET 
            product_id = '" . (int) $data['product_id'] . "', 
            image = '" . $this->db->escape($data['image']) . "'");

        return $this->db->getLastId();
    }

    public function setSpecial($data) {
        $this->db->query("INSERT INTO " . DB_PREFIX . "product_special SET 
            product_id = '" . (int) $data['product_id'] . "', 
            customer_group_id = '" . (int) $data['customer_group_id'] . "', 
            priority = '" . (int) $data['priority'] . "', 
            price = '" . (float) $data['price'] . "', 
            date_start = '" . $this->db->escape($data['date_start']) . "', 
            date_end = '" . $this->db->escape($data['date_end']) . "'");

        return $this->db->getLastId();
    }

    public function setDiscount($data) {
        $this->db->query("INSERT INTO " . DB_PREFIX . "product_discount SET 
            product_id = '" . (int) $data['product_id'] . "', 
            customer_group_id = '" . (int) $data['customer_group_id'] . "', 
            quantity = '" . (int) $data['quantity'] . "', 
            priority = '" . (int) $data['priority'] . "', 
            price = '" . (float) $data['price'] . "', 
            date_start = '" . $this->db->escape($data['date_start']) . "', 
            date_end = '" . $this->db->escape($data['date_end']) . "'");

        return $this->db->getLastId();
    }

    public function setOption($data) {
        $this->db->query("INSERT INTO " . DB_PREFIX . "product_option SET 
        product_id = '" . (int) $data['product_id'] . "', 
        sort_order = '" . (int) $data['sort_order'] . "'");

        $product_option_id = $this->db->getLastId();

        foreach ($data['language'] as $language_id => $language) {
            $this->db->query("INSERT INTO " . DB_PREFIX . "product_option_description SET 
            product_option_id = '" . (int) $product_option_id . "', 
            language_id = '" . (int) $language_id . "', 
            product_id = '" . (int) $data['product_id'] . "', 
            name = '" . $this->db->escape(str_replace('.', '', $language['name'])) . "'");
        }

        if (isset($data['product_option_value'])) {
            foreach ($data['product_option_value'] as $product_option_value) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "product_option_value SET 
                product_option_id = '" . (int) $product_option_id . "', 
                product_id = '" . (int) $data['product_id'] . "', 
                quantity = '" . (int) $product_option_value['quantity'] . "', 
                subtract = '" . (int) $product_option_value['subtract'] . "', 
                price = '" . (float) $product_option_value['price'] . "', 
                prefix = '" . $this->db->escape($product_option_value['prefix']) . "', 
                sort_order = '" . (int) $product_option_value['sort_order'] . "'");

                $product_option_value_id = $this->db->getLastId();

                foreach ($product_option_value['language'] as $language_id => $language) {
                    $this->db->query("INSERT INTO " . DB_PREFIX . "product_option_value_description SET 
                    product_option_value_id = '" . (int) $product_option_value_id . "', 
                    language_id = '" . (int) $language_id . "', 
                    product_id = '" . (int) $data['product_id'] . "', 
                    name = '" . $this->db->escape($language['name']) . "'");
                }
            }
        }
        return $product_option_id;
    }

    public function deleteOptions($product_id) {        
        $this->db->query("DELETE FROM " . DB_PREFIX . "product_option WHERE product_id = '" . (int) $product_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "product_option_description WHERE product_id = '" . (int) $product_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "product_option_value WHERE product_id = '" . (int) $product_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "product_option_value_description WHERE product_id = '" . (int) $product_id . "'");
    }
    
    public function deleteStores($product_id) {
        $this->db->query("DELETE FROM " . DB_PREFIX . "object_to_store WHERE product_id = '" . (int) $product_id . "' AND object_type = 'product'");
    }

    public function deleteDiscounts($product_id) {        
        $this->db->query("DELETE FROM " . DB_PREFIX . "product_discount WHERE product_id = '" . (int) $product_id . "'");
    }

    public function deleteSpecials($product_id) {        
        $this->db->query("DELETE FROM " . DB_PREFIX . "product_special WHERE product_id = '" . (int) $product_id . "'");
    }

    public function deleteImages($product_id) {
        $this->db->query("DELETE FROM " . DB_PREFIX . "product_image WHERE product_id = '" . (int) $product_id . "'");
    }

    public function deleteDownloads($product_id) {
        $this->db->query("DELETE FROM " . DB_PREFIX . "product_to_download WHERE product_id = '" . (int) $product_id . "'");
    }

    public function deleteCategories($product_id) {
        $this->db->query("DELETE FROM " . DB_PREFIX . "object_to_category WHERE object_id = '" . (int) $product_id . "' AND object_type = 'product'");
    }

    public function deleteRelated($product_id) {
        $this->db->query("DELETE FROM " . DB_PREFIX . "product_related WHERE product_id = '" . (int) $product_id . "' OR related_id = '" . (int) $product_id . "'");
    }

    public function copy($product_id) {
        $query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "product p WHERE p.product_id = '" . (int) $product_id . "' ");

        if ($query->num_rows) {
            $data = [];

            $data = $query->row;

            $data = array_merge($data, array('product_description' => $this->getDescriptions($product_id)));
            $data = array_merge($data, array('product_option' => $this->getOptions($product_id)));

            foreach ($data['product_description'] as $k => $v) {
                $data['product_description'][$k]['keyword'] = $v['keyword'] . uniqid("-");
            }

            $data['model'] = $data['model'] . uniqid("-");

            $data['product_image'] = [];

            $results = $this->getImages($product_id);

            foreach ($results as $result) {
                $data['product_image'][] = $result['image'];
            }

            $data = array_merge($data, array('product_discount' => $this->getDiscounts($product_id)));
            $data = array_merge($data, array('product_special' => $this->getSpecials($product_id)));
            $data = array_merge($data, array('product_download' => $this->getDownloads($product_id)));
            $data = array_merge($data, array('product_category' => $this->getCategories($product_id)));
            $data = array_merge($data, array('product_related' => $this->getRelated($product_id)));
            $data = array_merge($data, array('product_tags' => $this->getTags($product_id)));
            $data = array_merge($data, array('stores' => $this->getStores($product_id)));

            $this->add($data);
        }
    }

    public function getAllByKeyword($keyword) {
        return $this->getAll(array(
            'queries'=>explode(' ', $keyword),
            'model'=>$keyword,
        ));
    }

    public function getAllByStoreId($id) {
        return $this->getAll(array(
            'store_id'=>$id
        ));
    }

    public function getAllByCategoryId($id) {
        return $this->getAll(array(
            'category_id'=>$id
        ));
    }

    public function getAllByManufacturerId($id) {
        return $this->getAll(array(
            'manufacturer_id'=>$id
        ));
    }

    public function getOptions($product_id) {
        $product_option_data = [];

        $product_option = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_option WHERE product_id = '" . (int) $product_id . "' ORDER BY sort_order");

        foreach ($product_option->rows as $product_option) {
            $product_option_value_data = [];

            $product_option_value = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_option_value WHERE product_option_id = '" . (int) $product_option['product_option_id'] . "' ORDER BY sort_order");

            foreach ($product_option_value->rows as $product_option_value) {
                $product_option_value_description_data = [];

                $product_option_value_description = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_option_value_description WHERE product_option_value_id = '" . (int) $product_option_value['product_option_value_id'] . "'");

                foreach ($product_option_value_description->rows as $result) {
                    $product_option_value_description_data[$result['language_id']] = array('name' => $result['name']);
                }

                $product_option_value_data[] = array(
                    'product_option_value_id' => $product_option_value['product_option_value_id'],
                    'language' => $product_option_value_description_data,
                    'quantity' => $product_option_value['quantity'],
                    'subtract' => $product_option_value['subtract'],
                    'price' => $product_option_value['price'],
                    'prefix' => $product_option_value['prefix'],
                    'sort_order' => $product_option_value['sort_order']
                );
            }

            $product_option_description_data = [];

            $product_option_description = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_option_description WHERE product_option_id = '" . (int) $product_option['product_option_id'] . "'");

            foreach ($product_option_description->rows as $result) {
                $product_option_description_data[$result['language_id']] = array('name' => $result['name']);
            }

            $product_option_data[] = array(
                'product_option_id' => $product_option['product_option_id'],
                'language' => $product_option_description_data,
                'product_option_value' => $product_option_value_data,
                'sort_order' => $product_option['sort_order']
            );
        }

        return $product_option_data;
    }

    public function getImages($product_id) {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_image WHERE product_id = '" . (int) $product_id . "'");

        return $query->rows;
    }

    public function getDiscounts($product_id) {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_discount WHERE product_id = '" . (int) $product_id . "' ORDER BY quantity, priority, price");

        return $query->rows;
    }

    public function getSpecials($product_id) {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_special WHERE product_id = '" . (int) $product_id . "' ORDER BY priority, price");

        return $query->rows;
    }

    public function getDownloads($product_id) {
        $product_download_data = [];

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_to_download WHERE product_id = '" . (int) $product_id . "'");

        foreach ($query->rows as $result) {
            $product_download_data[] = $result['download_id'];
        }

        return $product_download_data;
    }

    public function getCategories($product_id) {
        $product_category_data = [];

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "object_to_category WHERE object_id = '" . (int) $product_id . "' AND object_type = 'product'");

        foreach ($query->rows as $result) {
            $product_category_data[] = $result['category_id'];
        }

        return $product_category_data;
    }

    public function getRelated($product_id) {
        $product_related_data = [];

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_related WHERE product_id = '" . (int) $product_id . "'");

        foreach ($query->rows as $result) {
            $product_related_data[] = $result['related_id'];
        }

        return $product_related_data;
    }

    public function getTags($product_id) {
        $query = $this->db->query("SELECT * 
            FROM " . DB_PREFIX . "product_tags 
            WHERE product_id = '" . (int) $product_id . "'");
        $product_tags_data = [];
        foreach ($query->rows as $result) {
            $query2 = $this->db->query("SELECT tag 
                FROM " . DB_PREFIX . "product_tags 
                WHERE product_id = '" . (int) $product_id . "' 
                AND language_id = '" . (int) $result['language_id'] . "'");

            $product_tags_data[$result['language_id']] = array(
                'tag' => implode(",", $query2->rows)
            );
        }

        return $product_tags_data;
    }

    public function getAllTotalByStockStatusId($id) {
        return $this->getAllTotal(array(
            'stock_status_id'=>$id
        ));
    }

    public function getAllTotalByTaxClassId($id) {
        return $this->getAllTotal(array(
            'tax_class_id'=>$id
        ));
    }

    public function getAllTotalByWeightClassId($id) {
        return $this->getAllTotal(array(
            'weight_class_id'=>$id
        ));
    }

    public function getAllTotalByLengthClassId($id) {
        return $this->getAllTotal(array(
            'length_class_id'=>$id
        ));
    }

    public function getAllTotalByOptionId($option_id) {
        $query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product_to_option WHERE option_id = '" . (int) $option_id . "'");

        return $query->row['total'];
    }

    public function getAllTotalByDownloadId($download_id) {
        $query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product_to_download WHERE download_id = '" . (int) $download_id . "'");

        return $query->row['total'];
    }

    public function getAllTotalByManufacturerId($id) {
        return $this->getAllTotal(array(
            'manufacturer_id'=>$id
        ));
    }

    public function getSeoTitleRating() {
        $query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "description` WHERE CHAR_LENGTH(`title`) NOT BETWEEN 8 AND 55 AND object_type = 'product' ");
        $query2 = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "description` WHERE object_type = 'product'");
        return $query->row['total'] * 100 / $query2->row['total'];
    }

    public function getSeoMetaDescripionRating() {
        $query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "description` WHERE CHAR_LENGTH(`meta_description`) NOT BETWEEN 8 AND 155 AND object_type = 'product'");
        $query2 = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "description` WHERE object_type = 'product'");
        return $query->row['total'] * 100 / $query2->row['total'];
    }

    public function getSeoDescriptionRating() {
        $query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "description` WHERE CHAR_LENGTH(`description`) < 150 AND object_type = 'product'");
        $query2 = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "description` WHERE object_type = 'product'");
        return $query->row['total'] * 100 / $query2->row['total'];
    }

    public function getSeoUrlRating() {
        $query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "product` WHERE product_id NOT IN (SELECT `object_id` FROM `" . DB_PREFIX . "url_alias` WHERE `object_type` = 'product')");
        $query2 = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "product`");
        return $query->row['total'] * 100 / $query2->row['total'];
    }

    public function getCategoriesByAttributeGroupId($id) {
        if (is_array($id)) {
            $query = $this->db->query("SELECT category_id FROM " . DB_PREFIX . "object_to_category ".
                "WHERE object_id IN ('" . implode("','", $id) . "') AND object_type = 'attribute'");
        } else {
            $query = $this->db->query("SELECT category_id FROM " . DB_PREFIX . "object_to_category ".
            "WHERE object_id = '" . (int)$id . "' AND object_type = 'attribute'");
        }

        foreach ($query->rows as $row) {
            $return[] = $row['category_id'];
        }

        return $return;
    }

    public function getAttributeGroupsByCategoriesId($id) {
        if (is_array($id)) {
            $query = $this->db->query("SELECT object_id FROM " . DB_PREFIX . "object_to_category ".
            "WHERE category_id IN ('" . implode("','", $id) . "') AND object_type = 'attribute'");
        } else {
            $query = $this->db->query("SELECT object_id FROM " . DB_PREFIX . "object_to_category ".
                "WHERE category_id = '" . (int)$id . "' AND object_type = 'attribute'");
        }

        foreach ($query->rows as $row) {
            $return[] = $row['object_id'];
        }

        return $return;
    }
}
