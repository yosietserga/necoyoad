<?php

final class Cart {
    private $data = [];

    public function __construct($registry) {
        
        $this->config   = $registry->get('config');
        $this->customer = $registry->get('customer');
        $this->session  = $registry->get('session');
        $this->cache    = $registry->get('cache');
        $this->db       = $registry->get('db');
        $this->tax      = $registry->get('tax');
        $this->weight   = $registry->get('weight');
        $this->load     = $registry->get('load');

        $this->cacheNameForDatable = "shopping_cart_checkout." . ($this->customer->getId() ?? $this->session->getId()) . ".table_data";
        $this->cacheNameForData = "cart." . ($this->customer->getId() ?? $this->session->getId()) . ".products";

        if (!is_array($this->get('cart'))) {
            $this->set('cart', []);
        }
    }

    public function __set($k, $v) {
        $this->data[$k] = $v;
    }

    public function __get($k) {
        return $this->data[$k] ?? null;
    }

    public function removeCartCache() {
        $this->cache->delete($this->cacheNameForDatable, $this->cacheNameForDatable);
        $this->cache->delete($this->cacheNameForData, $this->cacheNameForData);
    }

    public function get($k) {
        $ssid = $this->customer->getId() ?? $this->session->getId();
        $prefix = "_shop_" . C_CODE . "_";
        return $this->cache->get($ssid . $prefix . $k, $prefix);
    }

    public function set($k, $v) {
        $ssid = $this->customer->getId() ?? $this->session->getId();
        $prefix = "_shop_" . C_CODE . "_";
        $this->cache->set($ssid . $prefix . $k, $v, $prefix);
    }
    
    public function getProducts($data=null) {
        $modelProduct = $this->load->model('store/product', true);
        $product_data = [];
        $results      = [];

        if (isset($data['start'])) {
            $a = $this->get('cart');
            $limit = abs($data['limit'] - count($a));
            $results = array_slice($a, $data['start'], $limit, true);
        } else {
            $results = $this->get('cart');
        }
        
        if (!$results) return [];

        $cached = $this->cache->get($this->cacheNameForData, $this->cacheNameForData);
        if ($cached) {
            return $cached;
        } else {
            foreach ($results as $key => $value) {
                $array = explode(':', $key);
                $product_id = $array[0];
                $quantity = $value;
                $stock = true;

                if (isset($array[1])) {
                    $options = explode('.', $array[1]);
                } else {
                    $options = [];
                }

                $product = $modelProduct->getById( (int)$product_id );

                if ($product) {
                    $option_price = 0;

                    $option_data = [];

                    foreach ($options as $product_option_value_id) {
                        $option_value_query = $this->db->query("SELECT pov.product_option_id, povd.name, pov.price, pov.quantity, pov.subtract, pov.prefix
                        FROM " . DB_PREFIX . "product_option_value pov 
                            LEFT JOIN " . DB_PREFIX . "product_option_value_description povd ON (pov.product_option_value_id = povd.product_option_value_id) 
                        WHERE pov.product_option_value_id = '" . (int)$product_option_value_id . "' 
                            AND pov.product_id = '" . (int)$product_id . "' 
                            AND povd.language_id = '" . (int)$this->config->get('config_language_id') . "' 
                        ORDER BY pov.sort_order");

                        if ($option_value_query->num_rows) {
                            $option_query = $this->db->query("SELECT pod.name
                            FROM " . DB_PREFIX . "product_option po 
                                LEFT JOIN " . DB_PREFIX . "product_option_description pod ON (po.product_option_id = pod.product_option_id) 
                            WHERE po.product_option_id = '" . (int)$option_value_query->row['product_option_id'] . "' 
                                AND po.product_id = '" . (int)$product_id . "' 
                                AND pod.language_id = '" . (int)$this->config->get('config_language_id') . "' 
                            ORDER BY po.sort_order");

                            if ($option_value_query->row['prefix'] == '+') {
                                $option_price = $option_price + $option_value_query->row['price'];
                            } elseif ($option_value_query->row['prefix'] == '-') {
                                $option_price = $option_price - $option_value_query->row['price'];
                            }

                            $option_data[] = array(
                                'product_option_value_id' => $product_option_value_id,
                                'name' => $option_query->row['name'],
                                'value' => $option_value_query->row['name'],
                                'prefix' => $option_value_query->row['prefix'],
                                'price' => $option_value_query->row['price']
                            );

                            if ($option_value_query->row['subtract'] && (!$option_value_query->row['quantity'] || ($option_value_query->row['quantity'] < $quantity))) {
                                $stock = false;
                            }
                        }
                    }

                    if ($this->customer->isLogged()) {
                        $customer_group_id = $this->customer->getCustomerGroupId();
                    } else {
                        $customer_group_id = $this->config->get('config_customer_group_id');
                    }

                    $discount_quantity = 0;
                    foreach ($this->get('cart') as $k => $v) {
                        $array2 = explode(':', $k);
                        if ($array2[0] == $product_id) {
                            $discount_quantity += $v;
                        }
                    }

                    $product_discount_query = $this->db->query("SELECT price
                    FROM " . DB_PREFIX . "product_discount 
                    WHERE product_id = '" . (int)$product_id . "' 
                        AND customer_group_id = '" . (int)$customer_group_id . "' 
                        AND quantity <= '" . (int)$discount_quantity . "' 
                        AND ((date_start = '0000-00-00' OR date_start < NOW()) 
                        AND (date_end = '0000-00-00' OR date_end> NOW())) 
                    ORDER BY quantity DESC, priority ASC, price ASC 
                    LIMIT 1");

                    if ($product_discount_query->num_rows) {
                        $price = $product_discount_query->row['price'];
                    } else {
                        $product_special_query = $this->db->query("SELECT price
                        FROM " . DB_PREFIX . "product_special 
                        WHERE product_id = '" . (int)$product_id . "' 
                            AND customer_group_id = '" . (int)$customer_group_id . "' 
                            AND ((date_start = '0000-00-00' OR date_start < NOW()) 
                            AND (date_end = '0000-00-00' OR date_end> NOW())) 
                        ORDER BY priority ASC, price ASC 
                        LIMIT 1");

                        if ($product_special_query->num_rows) {
                            $price = $product_special_query->row['price'];
                        } else {
                            $price = $product['price'];
                        }
                    }

                    $download_data = [];

                    $download_query = $this->db->query("SELECT *
                    FROM " . DB_PREFIX . "product_to_download p2d 
                        LEFT JOIN " . DB_PREFIX . "download d ON (p2d.download_id = d.download_id) 
                        LEFT JOIN " . DB_PREFIX . "description dd ON (d.download_id = dd.object_id AND dd.object_type = 'download') 
                    WHERE p2d.product_id = '" . (int)$product_id . "' 
                        AND dd.language_id = '" . (int)$this->config->get('config_language_id') . "'");

                    foreach ($download_query->rows as $download) {
                        $download_data[] = array(
                            'download_id' => $download['download_id'],
                            'name' => $download['name'],
                            'filename' => $download['filename'],
                            'mask' => $download['mask'],
                            'remaining' => $download['remaining']
                        );
                    }

                    if (!$product['quantity'] || ($product['quantity'] < $quantity)) {
                        $stock = false;
                    }
                    $attributes = $modelProduct->getAllProperties($product['product_id'], 'attribute');
                    $product_data[$key] = array(
                        'key'           => $key,
                        'product_id'    => $product['product_id'],
                        'name'          => $product['title'],
                        'model'         => $product['model'],
                        'shipping'      => $product['shipping'],
                        'image'         => $product['image'],
                        'option'        => $option_data,
                        'attributes'    => $attributes,
                        'download'      => $download_data,
                        'quantity'      => $quantity,
                        'minimum'       => $product['minimum'],
                        'stock'         => $stock,
                        'price'         => ($price + $option_price),
                        'total'         => ($price + $option_price) * $quantity,
                        'tax_class_id'  => $product['tax_class_id'],
                        'weight'        => $product['weight'],
                        'weight_class'  => $product['weight_class'],
                        'length'        => $product['length'],
                        'width'         => $product['width'],
                        'height'        => $product['height'],
                        'length_class'  => $product['length_class']??""
                    );
                } else {
                    $this->remove($key);
                }
            }
            $this->cache->set($this->cacheNameForData, $product_data, $this->cacheNameForData);
            return $product_data;
        }
    }

    public function add(int $product_id, int $qty = 1, array $options = []) {
        //remove cache for cart data
        $this->removeCartCache();

        if (!$options) {
            $key = $product_id;
        } else {
            $key = $product_id . ':' . implode('.', $options);
        }

        $a = $this->get('cart');
        if ((int)$qty && ((int)$qty > 0)) {
            if (!isset($a[$key])) {
                $a[$key] = (int)$qty;
            } else {
                $a[$key] += (int)$qty;
            }
        }
        $this->set('cart', $a);
        $this->setMinQty();
    }

    public function update($key, $qty) {
        //remove cache for cart data
        $this->removeCartCache();

        if ((int)$qty && ((int)$qty > 0)) {
            $a = $this->get('cart');
            if (isset($a[$key])) {
                $a[$key] = (int)$qty;
            }
            $this->set('cart', $a);
        } else {
            $this->remove($key);
        }
        //TODO: agregar actividad del carrito en bd para CRM 
        $this->setMinQty();
    }

    public function remove($key) {
        //remove cache for cart data
        $this->removeCartCache();

        $a = $this->get('cart');
        if (isset($a[$key])) {
            unset($a[$key]);
        }
        $this->set('cart', $a);
    }

    public function clear() {
        //remove cache for cart data
        $this->removeCartCache();

        $this->set('cart', []);
    }

    public function getWeight() {
        $weight = 0;

        foreach ($this->getProducts() as $product) {
            if ($product['shipping']) {
                $weight += $this->weight->convert($product['weight'] * $product['quantity'], $product['weight_class'], $this->config->get('config_weight_class'));
            }
        }

        return $weight;
    }

    public function setMinQty() {
        $a = $this->get('cart');
        foreach ($this->getProducts() as $product) {
            if ($product['quantity'] < $product['minimum']) {
                if (isset($a[$product['key']])) {
                    $a[$product['key']] = $product['minimum'];
                }
            }
        }
        $this-> set('cart', $a);
        //remove cache for cart data
        $this->removeCartCache();
    }

    public function getSubTotal() {
        $total = 0;

        foreach ($this->getProducts() as $product) {
            $total += $product['total'];
        }

        return $total;
    }

    public function getTaxes() {
        $taxes = [];

        foreach ($this->getProducts() as $product) {
            if ($product['tax_class_id']) {
                if (!isset($taxes[$product['tax_class_id']])) {
                    $taxes[$product['tax_class_id']] = $product['total'] / 100 * $this->tax->getRate($product['tax_class_id']);
                } else {
                    $taxes[$product['tax_class_id']] += $product['total'] / 100 * $this->tax->getRate($product['tax_class_id']);
                }
            }
        }

        return $taxes;
    }

    public function getTotal() {
        $total = 0;

        foreach ($this->getProducts() as $product) {
            $total += $this->tax->calculate($product['total'], $product['tax_class_id'], $this->config->get('config_tax'));
        }

        return $total;
    }

    public function countProducts() {
        $a = $this->get('cart');
        return array_sum($a);
    }

    public function hasProducts() {
        $a = $this->get('cart');
        return count($a);
    }

    public function hasStock() {
        $stock = true;

        foreach ($this->getProducts() as $product) {
            if (!$product['stock']) {
                $stock = false;
            }
        }

        return $stock;
    }

    public function hasShipping() {
        $shipping = false;

        foreach ($this->getProducts() as $product) {
            if ($product['shipping']) {
                $shipping = true;
                break;
            }
        }

        return $shipping;
    }

    public function hasDownload() {
        $download = false;

        foreach ($this->getProducts() as $product) {
            if ($product['download']) {
                $download = true;

                break;
            }
        }

        return $download;
    }

    public function getProperty($id, $group=null, $key=null) {
        $rows = $this->getProperties($id, $group, $key);
        //return $rows[0];
        return $rows[0]['value'];
    }

    public function getProperties($id, $group=null, $key=null) {
        if (!is_numeric($id) || empty($id)) {
            return null;
        }

        $sql = "SELECT * FROM " . DB_PREFIX . "property ";
        $criteria = $rows = [];
        $criteria[] = " `object_type` = 'product' ";
        $criteria[] = " `object_id` = '" . (int)$id . "' ";

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
            $rows[$k]['value'] = unserialize(str_replace("\'", "'", $row['value']));
        }

        return $rows;
    }
}
