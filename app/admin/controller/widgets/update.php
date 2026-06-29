<?php

/**
 * ControllerWidgetsServerStatus
 * 
 * @package NecoTienda
 * @author Yosiet Serga
 * @copyright Inversiones Necoyoad, C.A.
 * @version 1.0.0
 * @access public
 * @see Controller
 */
class ControllerWidgetsServerStatus extends Controller {

    private $error = [];
    private $capi;

    public function init() {}

    /**
     * ControllerWidgetsServerStatus::index()
     * 
     * @return
     */
    public function index() {

        $this->data['orders'] = [];
        for ($i = 0; $i <= 11; $i++) {
            if (isset($query->rows[$i]['month'])) {
                $this->data['orders'][(int) $query->rows[$i]['month']] = (int) $query->rows[$i]['total'];
            } elseif (!isset($this->data['orders'][$i + 1])) {
                $this->data['orders'][$i + 1] = 0;
            }
        }
        
        $this->run_init();
        $params = array('display' => 'bandwidthusage|diskusage|dedicatedip|phpversion|apacheversion|cpanelbuild|cpanelversion|mysqlversion|subdomains|sharedip|emailaccounts');
        $this->data['stats'] = $this->capi->api2_query(CPANEL_USER, "StatsBar", "stat", $params);

        // javascript files
        $javascripts['highcharts'] = "js/vendor/highcharts/highcharts.js";
        $this->javascripts = array_merge($javascripts, $this->javascripts);

        $template = ($this->config->get('default_admin_view_widget_server_status')) ? $this->config->get('default_admin_view_widget_server_status') : 'widget/server_status.tpl';
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_admin_template') . '/'. $template)) {
            $this->template = $this->config->get('config_admin_template') . '/' . $template;
        } else {
            $this->template = 'default/' . $template;
        }

        $this->response->setOutput($this->render(true), $this->config->get('config_compression'));
    }
    
    public function addCron() {
        $this->run_init();
        $params = array('email' => 'inecoyoad@gmail.com');
        $this->data['stats'] = $this->capi->api2_query(CPANEL_USER, "Cron", "set_email", $params);
        
        $params = array(
            'command' => 'php -f /home/'. CPANEL_USER .'/public_html/system/cron/cron.php > /dev/null 2>&1',
            'day'=>'*',
            'hour'=>'*',
            'minute'=>'*',
            'month'=>'*',
            'weekday'=>'*'
        );
        $this->data['stats'] = $this->capi->api2_query(CPANEL_USER, "Cron", "add_line", $params);
    }
    
    public function widget() {
        $this->language->load('widget/server_status');
        
        $counter['product_seo'] = $this->getProductSeoRating();
        $counter['cateogry_seo'] = $this->getCategorySeoRating();
        $counter['manufacturer_seo'] = $this->getManufacturerSeoRating();
        
        $counter['page_seo'] = $this->getPageSeoRating();
        $counter['post_seo'] = $this->getPostSeoRating();
        $counter['post_category_seo'] = $this->getPostCategorySeoRating();
        
        $this->run_init();
        foreach ($this->stats() as $stat) {
            if ((string)$stat->name==='bandwidthusage') {
                $counter['bandwidthusage'] = 100 - (float)$stat->percent;
            }
            if ((string)$stat->name==='diskusage') {
                $counter['diskusage'] = 100 - (float)$stat->percent;
            }
            if ((string)$stat->name==='phpversion') {
                $counter['phpversion'] = (version_compare((string)$stat->value, '5.3.0', '>=')) ? 100 : 0;
            }
            if ((string)$stat->name==='apacheversion') {
                $counter['apacheversion'] = (version_compare((string)$stat->value, '2.2.13', '>=')) ? 100 : 0;
            }
            if ((string)$stat->name==='cpanelversion') {
                $counter['cpanelversion'] = (version_compare((string)$stat->value, '11.30.0', '>=')) ? 100 : 0;
            }
            if ((string)$stat->name==='mysqlversion') {
                $counter['mysqlversion'] = (version_compare((string)$stat->value, '5.5.0', '>=')) ? 100 : 0;
            }
            if ((string)$stat->name==='emailaccounts') {
                if ((int)$stat->_max > 0) {
                    $counter['emailaccounts'] = 100 -((int)$stat->count * 100 / (int)$stat->_max);
                } elseif ((string)$stat->_max === 'unlimited') {
                    $counter['emailaccounts'] = 100;
                } else {
                    $counter['emailaccounts'] = 0;
                }
            }
        }
        
        $this->data['percent'] = array_sum($counter) / count($counter);
        $this->data['percent_diff'] = 100 - $this->data['percent'];
        
        $template = ($this->config->get('default_admin_view_widget_server_status')) ? $this->config->get('default_admin_view_widget_server_status') : 'widget/server_status.tpl';
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
