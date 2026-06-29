<?php

/**
 * ControllerWidgetsOrderStats
 * 
 * @package NecoTienda
 * @author Yosiet Serga
 * @copyright Inversiones Necoyoad, C.A.
 * @version 1.0.0
 * @access public
 * @see Controller
 */
class ControllerWidgetsOrderStats extends Controller {
   
    public function widget() {
        $this->language->load('widget/server_status');
        /*
        $counter['product_seo'] = $this->getAllVisitsGroupedByMonth();
        $counter['product_seo'] = $this->getAllAmountOrdersGroupedByMonth();
        $counter['product_seo'] = $this->getAllOrdersGroupedByMonth();
         * 
         */
        
        $query = $this->db->query("SELECT MONTH(date_added) AS month, COUNT(*) AS total 
            FROM `" . DB_PREFIX . "order` 
            WHERE order_status_id > '0' 
                AND YEAR(date_added) = '" . date('Y') . "'
            GROUP BY MONTH(date_added)
            ORDER BY MONTH(date_added) ASC");
        
        $this->data['orders'] = [];
        for ($i = 0; $i <= 11; $i++) {
            if (isset($query->rows[$i]['month'])) {
                $this->data['orders'][(int)$query->rows[$i]['month']] = (int)$query->rows[$i]['total'];
            } elseif (!isset($this->data['orders'][$i + 1])) {
                $this->data['orders'][$i + 1] = 0;
            }
        }
        ksort($this->data['orders']);
        
        $query = $this->db->query("SELECT MONTH(date_added) AS month, SUM(total) AS total 
            FROM `" . DB_PREFIX . "order` 
            WHERE (order_status_id = '7' OR invoice_id > 0)
                AND YEAR(date_added) = '" . date('Y') . "'
            GROUP BY MONTH(date_added)
            ORDER BY MONTH(date_added) ASC");
        
        $this->data['sales'] = [];
        for ($i = 0; $i <= 11; $i++) {
            if (isset($query->rows[$i]['month'])) {
                $this->data['sales'][(int)$query->rows[$i]['month']] = (int)$query->rows[$i]['total'];
            } elseif (!isset($this->data['sales'][$i + 1])) {
                $this->data['sales'][$i + 1] = 0;
            }
        }
        ksort($this->data['sales']);
        
        $query = $this->db->query("SELECT MONTH(date_added) AS month, COUNT(*) AS total 
            FROM `" . DB_PREFIX . "stat` 
            WHERE YEAR(date_added) = '" . date('Y') . "'
            GROUP BY MONTH(date_added)
            ORDER BY MONTH(date_added) ASC");
        
        $this->data['visits'] = [];
        for ($i = 0; $i <= 11; $i++) {
            if (isset($query->rows[$i]['month'])) {
                $this->data['visits'][(int)$query->rows[$i]['month']] = (int)$query->rows[$i]['total'];
            } elseif (!isset($this->data['visits'][$i + 1])) {
                $this->data['visits'][$i + 1] = 0;
            }
        }
        ksort($this->data['visits']);
        
        $template = ($this->config->get('default_admin_view_widget_order_area')) ? $this->config->get('default_admin_view_widget_order_area') : 'widget/order_area.tpl';
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_admin_template') . '/'. $template)) {
            $this->template = $this->config->get('config_admin_template') . '/' . $template;
        } else {
            $this->template = 'default/' . $template;
        }
        
        $this->response->setOutput($this->render(true), $this->config->get('config_compression'));
    }
    
    public function lastsales() {
        $this->language->load('widget/server_status');
        $query = $this->db->query("SELECT DAY(date_added) AS day, SUM(total) AS total 
            FROM `" . DB_PREFIX . "order` 
            WHERE (order_status_id = '7' OR invoice_id > 0)
                AND YEAR(date_added) = '" . date('Y') . "'
                AND MONTH(date_added) = '" . date('m') . "'
            GROUP BY DAY(date_added)
            ORDER BY DAY(date_added) ASC");
            
        $this->data['sales'] = [];
        for ($i = 0; $i <= 30; $i++) {
            if (isset($query->rows[$i]['day'])) {
                $this->data['sales'][(int)$query->rows[$i]['day']] = (int)$query->rows[$i]['total'];
            } elseif (!isset($this->data['sales'][$i + 1])) {
                $this->data['sales'][$i + 1] = 0;
            }
        }
        ksort($this->data['sales']);
        
        $template = ($this->config->get('default_admin_view_widget_last_sales_bar')) ? $this->config->get('default_admin_view_widget_last_sales_bar') : 'widget/last_sales_bar.tpl';
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_admin_template') . '/'. $template)) {
            $this->template = $this->config->get('config_admin_template') . '/' . $template;
        } else {
            $this->template = 'default/' . $template;
        }

        $this->response->setOutput($this->render(true), $this->config->get('config_compression'));
    }
    
    public function lastorders() {
        $this->language->load('widget/server_status');
        $query = $this->db->query("SELECT DAY(date_added) AS day, COUNT(*) AS total 
            FROM `" . DB_PREFIX . "order` 
            WHERE order_status_id > 0
                AND YEAR(date_added) = '" . date('Y') . "'
                AND MONTH(date_added) = '" . date('m') . "'
            GROUP BY DAY(date_added)
            ORDER BY DAY(date_added) ASC");
            
        $this->data['orders'] = [];
        for ($i = 0; $i <= 30; $i++) {
            if (isset($query->rows[$i]['day'])) {
                $this->data['orders'][(int)$query->rows[$i]['day']] = (int)$query->rows[$i]['total'];
            } elseif (!isset($this->data['orders'][$i + 1])) {
                $this->data['orders'][$i + 1] = 0;
            }
        }
        ksort($this->data['orders']);
        
        $template = ($this->config->get('default_admin_view_widget_last_orders_bar')) ? $this->config->get('default_admin_view_widget_last_orders_bar') : 'widget/last_orders_bar.tpl';
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_admin_template') . '/'. $template)) {
            $this->template = $this->config->get('config_admin_template') . '/' . $template;
        } else {
            $this->template = 'default/' . $template;
        }

        $this->response->setOutput($this->render(true), $this->config->get('config_compression'));
    }
    
    public function lastvisits() {
        $this->language->load('widget/server_status');
        $query = $this->db->query("SELECT DAY(date_added) AS day, SUM(total) AS total 
            FROM `" . DB_PREFIX . "order` 
            WHERE (order_status_id = '7' OR invoice_id > 0)
                AND YEAR(date_added) = '" . date('Y') . "'
                AND MONTH(date_added) = '" . date('m') . "'
            GROUP BY DAY(date_added)
            ORDER BY DAY(date_added) ASC");
            
        $this->data['sales'] = [];
        for ($i = 0; $i <= 30; $i++) {
            if (isset($query->rows[$i]['day'])) {
                $this->data['sales'][(int)$query->rows[$i]['day']] = (int)$query->rows[$i]['total'];
            } elseif (!isset($this->data['sales'][$i + 1])) {
                $this->data['sales'][$i + 1] = 0;
            }
        }
        ksort($this->data['sales']);
        
        $template = ($this->config->get('default_admin_view_widget_last_sales_bar')) ? $this->config->get('default_admin_view_widget_last_sales_bar') : 'widget/last_sales_bar.tpl';
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_admin_template') . '/'. $template)) {
            $this->template = $this->config->get('config_admin_template') . '/' . $template;
        } else {
            $this->template = 'default/' . $template;
        }

        $this->response->setOutput($this->render(true), $this->config->get('config_compression'));
    }
    
    protected function run_init() {
        $this->load->library('cpxmlapi');
        $this->capi = new xmlapi(CPANEL_HOST);
        $this->capi->set_port(CPANEL_PORT);
        $this->capi->password_auth(CPANEL_USER, CPANEL_PWD);
        $this->capi->set_debug(0);
    }
    
    protected function stats() {
        $params = array('display' => 'bandwidthusage|diskusage|dedicatedip|phpversion|apacheversion|cpanelbuild|cpanelversion|mysqlversion|subdomains|sharedip|emailaccounts');
        return $this->capi->api2_query(CPANEL_USER, "StatsBar", "stat", $params);
    }
    
    protected function getProductSeoRating() {
        $this->load->auto('store/product');
        
        $r['title'] = $this->modelProduct->getSeoTitleRating();
        $r['overview'] = $this->modelProduct->getSeoMetaDescripionRating();
        $r['description'] = $this->modelProduct->getSeoDescriptionRating();
        $r['urlAlias'] = $this->modelProduct->getSeoUrlRating();
        
        return array_sum($r) / count($r);
        
                
        //4. que las palabras del título con más de tres letras se repitan al menos 2 veces en el contenido
        //5. que en el contenido al menos haya una imagen con titulo coherente con el título del contenido
        //6. que todas las imágenes tengan título o texto alternativo
    }
    
    protected function getCategorySeoRating() {
        $this->load->auto('store/category');
        
        $r['title'] = $this->modelCategory->getSeoTitleRating();
        $r['overview'] = $this->modelCategory->getSeoMetaDescripionRating();
        $r['description'] = $this->modelCategory->getSeoDescriptionRating();
        $r['urlAlias'] = $this->modelCategory->getSeoUrlRating();
        
        return array_sum($r) / count($r);
    }
    
    protected function getManufacturerSeoRating() {
        $this->load->auto('store/manufacturer');
        
        $r['urlAlias'] = $this->modelManufacturer->getSeoUrlRating();
        
        return array_sum($r) / count($r);
    }
    
    protected function getPageSeoRating() {
        $this->load->auto('content/page');
        
        $r['title'] = $this->modelPage->getSeoTitleRating();
        $r['overview'] = $this->modelPage->getSeoMetaDescripionRating();
        $r['description'] = $this->modelPage->getSeoDescriptionRating();
        $r['urlAlias'] = $this->modelPage->getSeoUrlRating();
        
        return array_sum($r) / count($r);
    }
    
    protected function getPostCategorySeoRating() {
        $this->load->auto('content/post_category');
        
        $r['title'] = $this->modelPost_category->getSeoTitleRating();
        $r['overview'] = $this->modelPost_category->getSeoMetaDescripionRating();
        $r['description'] = $this->modelPost_category->getSeoDescriptionRating();
        $r['urlAlias'] = $this->modelPost_category->getSeoUrlRating();
        
        return array_sum($r) / count($r);
    }
    
    protected function getPostSeoRating() {
        $this->load->auto('content/post');
        
        $r['urlAlias'] = $this->modelPost->getSeoUrlRating();
        
        return array_sum($r) / count($r);
    }
    
}
