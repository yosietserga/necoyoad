<?php

/**
 * ControllerReportVisited
 *  
 * @package NecoTienda 
 * @author Yosiet Serga
 * @copyright Inversiones Necoyoad, C.A.
 * @version 1.0.0
 * @access public
 * @see Controller
 */
class ControllerReportVisits extends Controller {

    /**
     * ControllerStoreProduct::index()
     * 
     * @see Load
     * @see Language
     * @return void
     */
    public function index() {
        //TODO: mantener y capturar los filtros en todos los enlaces
        $filter_browser = isset($this->request->get['filter_browser']) ? $this->request->get['filter_browser'] : null;
        $filter_version = isset($this->request->get['filter_version']) ? $this->request->get['filter_version'] : null;
        $filter_language = isset($this->request->get['filter_language']) ? $this->request->get['filter_language'] : null;
        $filter_country_id = isset($this->request->get['filter_country_id']) ? $this->request->get['filter_country'] : null;
        $filter_zone_id = isset($this->request->get['filter_zone_id']) ? $this->request->get['filter_zone_id'] : null;
        $filter_customer_id = isset($this->request->get['filter_customer_id']) ? $this->request->get['filter_customer_id'] : null;
        $filter_object_id = isset($this->request->get['filter_object_id']) ? $this->request->get['filter_object_id'] : null;
        $filter_object = isset($this->request->get['filter_object']) ? $this->request->get['filter_object'] : null;
        $filter_store_id = isset($this->request->get['filter_store_id']) ? $this->request->get['filter_store_id'] : null;
        $filter_ref = isset($this->request->get['filter_ref']) ? $this->request->get['filter_ref'] : null;
        $filter_os = isset($this->request->get['filter_os']) ? $this->request->get['filter_os'] : null;
        $filter_ip = isset($this->request->get['filter_ip']) ? $this->request->get['filter_ip'] : null;
        $filter_date_start = isset($this->request->get['filter_date_start']) ? $this->request->get['filter_date_start'] : null;
        $filter_date_end = isset($this->request->get['filter_date_end']) ? $this->request->get['filter_date_end'] : null;

        $url = '';

        if (isset($this->request->get['filter_browser'])) {
            $url .= '&filter_browser=' . $this->request->get['filter_browser'];
        }
        if (isset($this->request->get['filter_version'])) {
            $url .= '&filter_version=' . $this->request->get['filter_version'];
        }
        if (isset($this->request->get['filter_language'])) {
            $url .= '&filter_language=' . $this->request->get['filter_language'];
        }
        if (isset($this->request->get['filter_country_id'])) {
            $url .= '&filter_country_id=' . $this->request->get['filter_country_id'];
        }
        if (isset($this->request->get['filter_zone_id'])) {
            $url .= '&filter_zone_id=' . $this->request->get['filter_zone_id'];
        }
        if (isset($this->request->get['filter_customer_id'])) {
            $url .= '&filter_customer_id=' . $this->request->get['filter_customer_id'];
        }
        if (isset($this->request->get['filter_object_id'])) {
            $url .= '&filter_object_id=' . $this->request->get['filter_object_id'];
        }
        if (isset($this->request->get['filter_object'])) {
            $url .= '&filter_object=' . $this->request->get['filter_object'];
        }
        if (isset($this->request->get['filter_store_id'])) {
            $url .= '&filter_store_id=' . $this->request->get['filter_store_id'];
        }
        if (isset($this->request->get['filter_ref'])) {
            $url .= '&filter_ref=' . $this->request->get['filter_ref'];
        }
        if (isset($this->request->get['filter_os'])) {
            $url .= '&filter_os=' . $this->request->get['filter_os'];
        }
        if (isset($this->request->get['filter_ip'])) {
            $url .= '&filter_ip=' . $this->request->get['filter_ip'];
        }
        if (isset($this->request->get['filter_date_start'])) {
            $url .= '&filter_date_start=' . $this->request->get['filter_date_start'];
        }
        if (isset($this->request->get['filter_date_end'])) {
            $url .= '&filter_date_end=' . $this->request->get['filter_date_end'];
        }

        $this->data['products'] = [];

        $data = array(
            'filter_browser' => $filter_browser,
            'filter_version' => $filter_version,
            'filter_language' => $filter_quantity,
            'filter_country_id' => $filter_country_id,
            'filter_zone_id' => $filter_zone_id,
            'filter_customer_id' => $filter_customer_id,
            'filter_object_id' => $filter_object_id,
            'filter_object' => $filter_object,
            'filter_store_id' => $filter_store_id,
            'filter_ref' => $filter_ref,
            'filter_os' => $filter_os,
            'filter_ip' => $filter_ip,
            'filter_date_start' => $filter_date_start,
            'filter_date_end' => $filter_date_end
        );

        $this->document->title = $this->data['heading_title'] = $this->language->get('heading_see_title');

        $this->document->breadcrumbs = [];

        $this->document->breadcrumbs[] = array(
            'href' => Url::createAdminUrl('common/home'),
            'text' => $this->language->get('text_home'),
            'separator' => false
        );

        $this->document->breadcrumbs[] = array(
            'href' => Url::createAdminUrl('report/visit') . $url,
            'text' => $this->language->get('heading_title'),
            'separator' => ' :: '
        );

        $this->load->auto('stats/traffic');
        $results = $this->modelTraffic->getAll($data);

        $allStock = "[";
        foreach ($this->modelTraffic->getAll($data) as $row) {
            $allStock .= "[" . $row['date_added'] . "," . $row['total'] . "],";
        }
        $allStock = substr($allStock, 0, strlen($allStock) - 1) . "]";

        $productsStock = "[";
        foreach ($this->modelTraffic->getAllProductsForHS() as $row) {
            $productsStock .= "[" . $row['date_added'] . "," . $row['total'] . "],";
        }
        $productsStock = substr($productsStock, 0, strlen($productsStock) - 1) . "]";

        // javascript files
        $javascripts[] = "js/vendor/highstock/highstock.js";
        $javascripts[] = "js/vendor/highstock/modules/exporting.js";
        $javascripts[] = "js/vendor/highcharts/jquery.highchartTable.min.js";
        $this->data['javascripts'] = $this->javascripts = array_merge($javascripts, $this->javascripts);

        // SCRIPTS
        $scripts[] = array('id' => 'seeFunctions', 'method' => 'function', 'script' =>
            "function showTab(a) {
                $('.vtabs_page').hide();
                $($(a).attr('data-target')).show();
            }
            
            function updateCharts(ds,de) {
                if (typeof de == 'undefined' || typeof ds == 'undefined') {
                    alert('No se pudieron cargar todas las estad\xedsticas');
                    return false;
                }
                
                var params = '&ds='+ ds +'&de='+ de;
                
                $('#visitsStats')
                    .html('<img src=\"image/nt_loader.gif\" alt\"Cargando...\" />')
                    .load('" . Url::createAdminUrl("store/product/visits") . "' + params);
                /*
                $('#ordersStats')
                    .delay(600)
                    .html('<img src=\"image/nt_loader.gif\" alt\"Cargando...\" />')
                    .load('" . Url::createAdminUrl("store/product/visits") . "' + params);
                    
                $('#salesStats')
                    .delay(1200)
                    .html('<img src=\"image/nt_loader.gif\" alt\"Cargando...\" />')
                    .load('" . Url::createAdminUrl("store/product/visits") . "' + params);
                    
                $('#commentsStats')
                    .delay(1800)
                    .html('<img src=\"image/nt_loader.gif\" alt\"Cargando...\" />')
                    .load('" . Url::createAdminUrl("store/product/visits") . "' + params);
                */
            }");
        $scripts[] = array('id' => 'seeScripts', 'method' => 'ready', 'script' =>
            "$('#chartVisits').highcharts('StockChart', {
                chart: {
                    events: {
                        load: function(e) {
                            updateCharts(this.xAxis[0].min,this.xAxis[0].max);
                        }
                    }
                },
                rangeSelector : {
    				selected : 1
    			},
    			title : {
    				text : 'Visitas a la Tienda Virtual'
    			},
    			tooltip: {
                    pointFormat: '<span style=\"color:{series.color}\">{series.name}</span>: <b>{point.y}</b><br/>',
        			valueDecimals: 0
    			},
                xAxis: {
                    events: {
                        setExtremes: function(e) {
                            updateCharts(e.min,e.max);
                        }
                    },
                    minRange: 3600000
                },
    			series : 
                [{
        			name : 'Total Visitas',
        			data : $allStock
    			},
                {
        			name : 'Visitas Productos',
                    data : $productsStock
        		}]
    		});
            
            $('#formFilter').ntForm({
                lockButton:false,
                ajax:true,
                type:'get',
                dataType:'html',
                url:'" . Url::createAdminUrl("store/product/seeData") . "',
                beforeSend:function(){
                    $('#gridWrapper').hide();
                    $('#gridPreloader').show();
                },
                success:function(data){
                    $('#gridPreloader').hide();
                    $('#gridWrapper').html(data).show();
                }
            });
            
            $('.vtabs_page').hide();
            $('#tab_visits').show();");

        $this->scripts = array_merge($this->scripts, $scripts);

        $this->template = 'store/product_see.tpl';
        
        $this->children[] = 'common/header';
        $this->children[] = 'common/nav';
        $this->children[] = 'common/footer';
        
        $this->response->setOutput($this->render(true), $this->config->get('config_compression'));
    }

    /**
     * ControllerStoreProduct::visits()
     * 
     * Estadisticas de visitas
     * 
     * @see Load
     * @see Request
     * @see Model
     * @return void
     */
    public function visits() {
        //TODO: mantener y capturar los filtros en todos los enlaces
        $filter_date_start = isset($this->request->get['ds']) ? $this->request->get['ds'] : null;
        $filter_date_end = isset($this->request->get['de']) ? $this->request->get['de'] : null;
        $product_id = isset($this->request->get['product_id']) ? $this->request->get['product_id'] : 0;

        $url = '';

        if (isset($this->request->get['ds'])) {
            $url .= '&ds=' . $this->request->get['ds'];
        }
        if (isset($this->request->get['de'])) {
            $url .= '&de=' . $this->request->get['de'];
        }
        if (isset($this->request->get['product_id'])) {
            $url .= '&product_id=' . $this->request->get['product_id'];
        }

        $data = array(
            'object' => 'product',
            'product_id' => $product_id,
            'filter_date_start' => $filter_date_start,
            'filter_date_end' => $filter_date_end
        );

        $this->load->auto('stats/traffic');
        $this->data['browsers'] = $this->modelTraffic->getAllByBrowser($data);
        $this->data['os'] = $this->modelTraffic->getAllByOS($data);
        $this->data['customers'] = $this->modelTraffic->getAllByCustomer($data);
        $this->data['ips'] = $this->modelTraffic->getAllByIP($data);
        /*
          $this->data['os'] = $this->modelTraffic->getAllByOS($data);
          $this->data['ips'] = $this->modelTraffic->getAllByIp($data);
         */

        $this->data['Url'] = new Url;
        $this->data['params'] = $url;
        $this->template = 'store/product_see_visits.tpl';
        $this->response->setOutput($this->render(true), $this->config->get('config_compression'));
    }

}
