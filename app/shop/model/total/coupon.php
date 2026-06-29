<?php

class ModelTotalCoupon extends Model {

    public function getTotal(&$total_data, &$total, &$taxes) {
        if ($this->session->has('coupon') && ($this->session->get('coupon_token') == md5($this->session->get('coupon') . CRYPT_KEY))) {
            $this->load->model('checkout/coupon');
            
            $coupon = $this->modelCoupon->getCoupon($this->session->get('coupon'));
            
            if ($coupon) {
                $discount_total = 0;
                
                if (!$coupon['product']) {
                    $coupon_total = $this->cart->getSubTotal();
                } else {
                    $coupon_total = 0;
                    
                    foreach ($this->cart->getProducts() as $product) {
                        if (in_array($product['product_id'], $coupon['product'])) {
                            $coupon_total += $product['total'];
                        }
                    }
                }

                if ($coupon['type'] == 'F') {
                    $coupon['discount'] = min($coupon['discount'], $coupon_total);
                }
                
                foreach ($this->cart->getProducts() as $product) {
                    $discount = 0;
                    
                    if (!$coupon['product']) {
                        $status = true;
                    } else {
                        if (in_array($product['product_id'], $coupon['product'])) {
                            $status = true;
                        } else {
                            $status = false;
                        }
                    }
                    
                    if ($status) {
                        if ($coupon['type'] == 'F') {
                            $discount = $coupon['discount'] * ($product['total'] / $coupon_total);
                        } elseif ($coupon['type'] == 'P') {
                            $discount = $product['total'] / 100 * $coupon['discount'];
                        }

                        if ($product['tax_class_id']) {
                            $taxes[$product['tax_class_id']] -= ($product['total'] / 100 * $this->tax->getRate($product['tax_class_id'])) - (($product['total'] - $discount) / 100 * $this->tax->getRate($product['tax_class_id']));
                        }
                    }
                    
                    $discount_total += $discount;
                }
                
                if ($coupon['shipping'] && $this->session->has('shipping_method')) {
                    if ($this->session->has('shipping_method', 'tax_class_id')) {
                        $tax = $this->session->get('shipping_method', 'tax_class_id') - ($this->session->get('shipping_method', 'cost') / 100 * $this->tax->getRate($this->session->get('shipping_method', 'tax_class_id')));
                        $taxes[$tax];
                    }

                    $discount_total += $this->session->get('shipping_method', 'cost');
                }
                
                $total_data[] = array(
                    'title' => 'Coupon ('.$coupon['name'] . '):',
                    'text' => '-' . $this->currency->format($discount_total),
                    'value' => -$discount_total,
                    'sort_order' => $this->config->get('coupon_sort_order')
                );
                
                $total -= $discount_total;
            }
        }
    }

    public function getCoupon($code)
    {
        $status = true;

        $coupon_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "coupon` WHERE code = '" . $this->db->escape($code) . "' AND ((date_start = '0000-00-00' OR date_start < NOW()) AND (date_end = '0000-00-00' OR date_end > NOW())) AND status = '1'");

        if ($coupon_query->num_rows) {
            if ($coupon_query->row['total'] > $this->cart->getSubTotal()) {
                $status = false;
            }

            $coupon_history_query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "coupon_history` ch WHERE ch.coupon_id = '" . (int)$coupon_query->row['coupon_id'] . "'");

            if ($coupon_query->row['uses_total'] > 0 && ($coupon_history_query->row['total'] >= $coupon_query->row['uses_total'])) {
                $status = false;
            }

            if ($coupon_query->row['logged'] && !$this->customer->getId()) {
                $status = false;
            }

            if ($this->customer->getId()) {
                $coupon_history_query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "coupon_history` ch WHERE ch.coupon_id = '" . (int)$coupon_query->row['coupon_id'] . "' AND ch.customer_id = '" . (int)$this->customer->getId() . "'");

                if ($coupon_query->row['uses_customer'] > 0 && ($coupon_history_query->row['total'] >= $coupon_query->row['uses_customer'])) {
                    $status = false;
                }
            }

            // Products
            $coupon_product_data = [];

            $coupon_product_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "coupon_product` WHERE coupon_id = '" . (int)$coupon_query->row['coupon_id'] . "'");

            foreach ($coupon_product_query->rows as $product) {
                $coupon_product_data[] = $product['product_id'];
            }

            // Categories
            $coupon_category_data = [];

            $coupon_category_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "coupon_category` cc LEFT JOIN `" . DB_PREFIX . "category` cp ON (cc.category_id = cp.category_id) WHERE cc.coupon_id = '" . (int)$coupon_query->row['coupon_id'] . "'");

            foreach ($coupon_category_query->rows as $category) {
                $coupon_category_data[] = $category['category_id'];
            }

            $product_data = [];

            if ($coupon_product_data || $coupon_category_data) {
                foreach ($this->cart->getProducts() as $product) {
                    if (in_array($product['product_id'], $coupon_product_data)) {
                        $product_data[] = $product['product_id'];

                        continue;
                    }

                    foreach ($coupon_category_data as $category_id) {
                        $coupon_category_query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "object_to_category` WHERE `object_id` = '" . (int)$product['product_id'] . "' AND category_id = '" . (int)$category_id . "' AND object_type = 'product'");

                        if ($coupon_category_query->row['total']) {
                            $product_data[] = $product['product_id'];

                            continue;
                        }
                    }

                }

                if (!$product_data) {
                    $status = false;
                }
            }
        } else {
            $status = false;
        }

        if ($status) {
            return array(
                'coupon_id' => $coupon_query->row['coupon_id'],
                'code' => $coupon_query->row['code'],
                'name' => $coupon_query->row['name'],
                'type' => $coupon_query->row['type'],
                'discount' => $coupon_query->row['discount'],
                'shipping' => $coupon_query->row['shipping'],
                'total' => $coupon_query->row['total'],
                'product' => $product_data,
                'date_start' => $coupon_query->row['date_start'],
                'date_end' => $coupon_query->row['date_end'],
                'uses_total' => $coupon_query->row['uses_total'],
                'uses_customer' => $coupon_query->row['uses_customer'],
                'status' => $coupon_query->row['status'],
                'date_added' => $coupon_query->row['date_added']
            );
        }
    }

}
