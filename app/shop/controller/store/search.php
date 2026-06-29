<?php
class ControllerStoreSearch extends Controller {

    public function index() {
        $this->session->clear('object_type');
        $this->session->clear('object_id');
        $this->session->clear('landing_page');

        $criteria = [];
        $criteria['page'] = $this->request->hasQuery('page') ? $this->request->getQuery('page') : 1;
        $criteria['sort'] = $this->request->hasQuery('sort') ? $this->request->getQuery('sort') : 'td.title';
        $criteria['order'] = $this->request->hasQuery('order') ? $this->request->getQuery('order') : 'ASC';
        $criteria['limit'] = $this->request->hasQuery('limit') ? $this->request->getQuery('limit') : $this->config->get('config_catalog_limit');
        $criteria['start'] = ($criteria['page'] - 1) * $criteria['limit'];

        $this->data['urlQuery'] = [];
        $this->data['urlQuery']['page'] = ($this->request->hasQuery('page')) ? '&page=' . $this->request->getQuery('page') : '';
        $this->data['urlQuery']['sort'] = ($this->request->hasQuery('sort')) ? '&sort=' . $this->request->getQuery('sort') : '';
        $this->data['urlQuery']['order'] = ($this->request->hasQuery('order')) ? '&order=' . $this->request->getQuery('order') : '';
        $this->data['urlQuery']['limit'] = ($this->request->hasQuery('limit')) ? '&limit=' . $this->request->getQuery('limit') : '';

        if ($this->config->get('config_seo_url')) {
            $this->data['urlBase'] = HTTP_HOME . 'buscar/' . $_GET['q'];
            $this->data['urlSearch'] = HTTP_HOME . 'buscar/' . $_GET['q'] . '?' . implode('', $this->data['urlQuery']);
        } else {
            $this->data['urlBase'] = HTTP_HOME . 'index.php?r=store/search&q=' . $_GET['q'];
            $this->data['urlSearch'] = HTTP_HOME . 'index.php?r=store/search&q=' . $_GET['q'] . '&' . implode('', $this->data['urlQuery']);
        }

        //tracker
        $this->tracker->track(0, 'search_page');

        if ($this->session->has('ref_email') && !$this->session->has('ref_cid')) {
            $this->data['show_register_form_invitation'] = true;
        }

        $cacheId = 'html-search_page_' . md5($this->data['urlSearch']) .
            serialize($this->request->get).
            $this->config->get('config_language_id') . "." .
            $this->request->getQuery('hl') . "." .
            $this->request->getQuery('cc') . "." .
            $this->customer->getId() . "." .
            $this->config->get('config_currency') . "." .
            (int) $this->config->get('config_store_id');

        $this->load->library('user');
        $cached = $this->cache->get($cacheId);
        if ($cached && !$this->user->isLogged()) {
            $this->response->setOutput($cached, $this->config->get('config_compression'));
        } else {
            $this->language->load('store/search');
            $this->load->model("store/product");

            $this->document->breadcrumbs = [];
            $this->document->breadcrumbs[] = array(
                'href' => Url::createUrl("common/home"),
                'text' => $this->language->get('text_home'),
                'separator' => false
            );

            $this->document->breadcrumbs[] = array(
                'href' => $this->data['urlSearch'],
                'text' => $this->language->get('Search') .' '. $_GET['q'],
                'separator' => false
            );

            $this->data['urlCriterias'] = [];

            list($keyword) = explode('_', $_GET['q']);
            $params = explode('_', strtolower($_GET['q']));
            $queries[1] = $queries[2] = trim(trim($params[0], '-'));

            $this->data['urlCriterias']['forCategories'] = $this->data['urlCriterias']['forZones'] = $this->data['urlCriterias']['forSellers'] = $this->data['urlCriterias']['forManufacturers'] = $this->data['urlCriterias']['forStores'] = $this->data['urlCriterias']['forPrices'] = $this->data['urlCriterias']['forShipping'] = $this->data['urlCriterias']['forPayments'] = $this->data['urlCriterias']['forStatus'] = $this->data['urlCriterias']['forStockStatus'] = $this->data['urlCriterias']['forDates'] = $this->data['urlCriterias']['forAttributes'] = $queries[1];

            $this->document->title = $this->data['heading_title'] = $this->language->get('heading_title') . ' ' . str_replace('-', ' ', $keyword);

            $criteria = $this->generateCriterias($params);
            $criteria['queries'] = array_unique($queries); 

            $criteria['page'] = isset($this->request->get['page']) ? $this->request->get['page'] : 1;
            $criteria['sort'] = isset($this->request->get['sort']) ? $this->request->get['sort'] : 'pd.title';
            $criteria['order'] = isset($this->request->get['order']) ? $this->request->get['order'] : 'ASC';
            $criteria['limit'] = !empty($this->request->get['limit']) ? $this->request->get['limit'] : $this->config->get('config_catalog_limit');
            $criteria['start'] = $criteria['limit'] * ($criteria['page'] - 1);

            $this->load->model('store/search');
            $total = $this->modelProduct->getAllTotal($criteria);
            if ($total) {
                $this->load->auto('store/review');

                $this->modelSearch->add();
                unset( $this->data['urlQuery']['page']);
                $this->cache->set('products_searched', $this->modelProduct->getAll($criteria));
                $this->cache->set('total_products_searched', $total);
                $this->cache->set('criteria_products_searched', $criteria);
                $this->cache->set('url_products_searched', $this->data['urlBase'] . '?page={page}' . implode('', $this->data['urlQuery']));
            } else {
                $this->data['noResults'] = true;
            }

            $this->data['breadcrumbs'] = $this->document->breadcrumbs;

            // SCRIPTS
            $scripts[] = array('id' => 'search-1', 'method' => 'ready', 'script' =>
                "$('#content_search input').keydown(function(e) {
                   	if (e.keyCode == 13 && $(this).val().length > 0) {
                  		contentSearch();
                   	}
                });
                if (window.location.hash.length > 0) {
                    $('#products').load('" . Url::createUrl("store/search") . "&q='+ window.location.hash.replace('#', ''));
                }");

            $this->session->set('landing_page','store/search');
            $this->loadWidgets('featuredContent');
            $this->loadWidgets('main');
            $this->loadWidgets('featuredFooter');

            $this->addChild('common/column_left');
            $this->addChild('common/column_right');
            $this->addChild('common/header');
            $this->addChild('common/footer');

            if (!$this->user->isLogged()) {
                $this->cacheId = $cacheId;
            }

            if ($scripts)
                $this->scripts = array_merge($this->scripts, $scripts);

            $template = ($this->config->get('default_view_search')) ? $this->config->get('default_view_search') : 'store/search.tpl';
            if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/' . $template)) {
                $this->template = $this->config->get('config_template') . '/' . $template;
            } else {
                $this->template = 'choroni/' . $template;
            }

            $this->response->setOutput($this->render(true), $this->config->get('config_compression'));
        }
    }

    private function generateCriterias($params) {
        $vars = array(
            array(
                'param'=>'estado',
                'model'=>'localisation/zone',
                'criteria'=>'zone'
            ),
            array(
                'param'=>'vendedor',
                'criteria'=>'seller'
            ),
            array(
                'param'=>'cat',
                'criteria'=>'category'
            ),
            array(
                'param'=>'marca',
                'criteria'=>'manufacturer'
            ),
            array(
                'param'=>'tienda',
                'criteria'=>'stores'
            ),
            array(
                'param'=>'envio',
                'criteria'=>'shipping_method'
            ),
            array(
                'param'=>'pago',
                'criteria'=>'payment_method'
            ),
            array(
                'param'=>'disp',
                'criteria'=>'stock_status'
            ),
            array(
                'param'=>'status',
                'criteria'=>'product_status'
            ),
            array(
                'param'=>'precio',
                'criteria'=>'price'
            ),
            array(
                'param'=>'fecha',
                'criteria'=>'date'
            ),
            array(
                'param'=>'filtro',
                'criteria'=>'properties'
            )
        );

        $criterias = array(
                'category'=>'forCategories',
                'seller'=>'forSellers',
                'zone'=>'forZones',
                'manufacturer'=>'forManufacturers',
                'stores'=>'forStores',
                'shipping_method'=>'forShipping',
                'payment_method'=>'forPayments',
                'status'=>'forStatus',
                'stock_status'=>'forStockStatus',

                'date'=>'forDates',
                'price'=>'forPrices',
                'properties'=>'forAttributes'
        );

        $criteria = [];

        foreach ($vars as $v) {

            if (in_array($v['param'], $params)) {
                if (isset($v['model'])) $this->load->model($v['model']);
                foreach ($params as $key => $value) {
                    if ($value == $v['param']) {
                        if ($v['param']==='filtro') {
                            $name = str_replace(' ', '+', trim($params[$key + 1]));
                            list($property_key, $property_value) = explode('+', $name);
                            if (!empty($property_value)) {
                                $criteria['properties'][$key]['key'] = $property_key;
                                $criteria['properties'][$key]['value'] = $property_value;
                            }
                        } else {
                            $name = $params[$key + 1];
                        }

                        unset($params[$key], $params[$key + 1]);
                    }
                }
                //TODO: clear the query
                if ($v['param']==='precio') {
                    list($criteria['price_start'], $criteria['price_end']) = explode('-', $name);
                } elseif ($v['param']==='fecha') {
                    $name = str_replace(' ', '+', trim($name));
                    list($criteria['date_start'], $criteria['date_end']) = explode('+', $name);
                } elseif ($v['param']!=='filtro') {
                    $criteria[$v['criteria']] = str_replace('-', ' ', $name);
                }

                foreach ($criterias as $c) {
                    $this->data['urlCriterias'][$c] .= '_'. strtoupper($v['param']) .'_'. $name;
                }
            }
        }

        foreach ($vars as $v) {
            if ($v['criteria']==='date' && (isset($criteria['date_start']) || isset($criteria['date_end']))) {
                if (isset($criteria['date_start']) && isset($criteria['date_end'])) {
                    $this->data['filters']['date'] = array(
                        'name' => $criteria['date_start'] . ' / ' . $criteria['date_end'],
                        'href' => rtrim($this->data['urlCriterias']['forDates'] . '?' . implode('', $this->data['urlQuery']), '?')
                    );
                } elseif (isset($criteria['date_start'])) {
                    $this->data['filters']['date'] = array(
                        'name' => $criteria['date_start'] . ' / ' . date('d-m-Y'),
                        'href' => rtrim($this->data['urlCriterias']['forDates'] . '?' . implode('', $this->data['urlQuery']), '?')
                    );
                } elseif (isset($criteria['date_end'])) {
                    $this->data['filters']['date'] = array(
                        'name' => date('d-m-Y') . ' / ' . $criteria['date_end'],
                        'href' => rtrim($this->data['urlCriterias']['forDates'] . '?' . implode('', $this->data['urlQuery']), '?')
                    );
                }
            } elseif ($v['criteria']==='price' && (isset($criteria['price_start']) || isset($criteria['price_end']))) {
                $this->data['filters']['price'] = array(
                    'name' => $this->currency->format($this->tax->calculate($criteria['price_start'])) . ' - ' .
                    $this->currency->format($this->tax->calculate($criteria['price_end'])),
                    'href' => rtrim($this->data['urlCriterias']['forPrices'] . '?' . implode('', $this->data['urlQuery']), '?')
                );
            } elseif (isset($criteria[$v['criteria']])) {
                if ($v['criteria']==='properties') {
                    foreach ($criteria['properties'] as $key => $value) {
                        $this->data['filters']['properties'][$key] = array(
                            'name' => $value['value'],
                            'href' => rtrim($this->data['urlCriterias']['forAttributes'] . '?' . implode('', $this->data['urlQuery']), '?')
                        );
                    }
                } else {
                    $this->data['filters'][$v['criteria']] = array(
                        'name' => $criteria[$v['criteria']],
                        'href' => rtrim($this->data['urlCriterias'][$criterias[$v['criteria']]] . '?' . implode('', $this->data['urlQuery']), '?')
                    );
                }
            }
        }

        return $criteria;
    }
}