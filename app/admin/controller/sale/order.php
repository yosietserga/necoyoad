<?php

/**
 * ControllerSaleOrder
 *
 * @package NecoTienda
 * @author Yosiet Serga
 * @copyright Inversiones Necoyoad, C.A.
 * @version 1.0.0
 * @access public
 */
class ControllerSaleOrder extends Controller {

    private $error = [];

    public function index() {
        $this->document->title = $this->language->get('heading_title');
        $this->getList();
    }

    public function insert() {
        $this->document->title = $this->language->get('heading_title');
        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $this->load->auto('sale/customer');
            $customer_info = $this->modelCustomer->getCustomer($this->request->post['customer']);
            if ($customer_info) {
                $this->request->post['firstname'] = $customer_info['firstname'];
                $this->request->post['lastname'] = $customer_info['lastname'];
            }
            $this->modelOrder->add($this->request->post);

            $this->session->set('success', $this->language->get('text_success'));

            $this->redirect(Url::createAdminUrl('sale/order'));
        }

        $this->getForm();
    }

    public function update() {
        $this->document->title = $this->language->get('heading_title');
        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $this->modelOrder->update($this->request->get['order_id'], $this->request->post);

            $this->session->set('success', $this->language->get('text_success'));

            $this->redirect(Url::createAdminUrl('sale/order'));
        }

        $this->getForm();
    }

    /**
     * ControllerMarketingNewsletter::delete()
     * elimina un objeto
     * @return boolean
     * */
    public function delete() {
        $this->load->auto('sale/order');
        if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
            foreach ($this->request->post['selected'] as $id) {
                $this->modelOrder->delete($id);
            }
        } else {
            $this->modelOrder->delete($_GET['id']);
        }
    }

    private function getList() {
        $this->document->breadcrumbs = [];

        $this->document->breadcrumbs[] = array(
            'href' => Url::createAdminUrl('common/home'),
            'text' => $this->language->get('text_home'),
            'separator' => false
        );

        $this->document->breadcrumbs[] = array(
            'href' => Url::createAdminUrl('sale/order'),
            'text' => $this->language->get('heading_title'),
            'separator' => ' :: '
        );

        $this->data['invoice'] = Url::createAdminUrl('sale/order/invoice');
        $this->data['insert'] = Url::createAdminUrl('sale/order/insert');
        $this->data['delete'] = Url::createAdminUrl('sale/order/delete');

        $this->data['heading_title'] = $this->language->get('heading_title');

        $this->data['button_invoices'] = $this->language->get('button_invoices');
        $this->data['button_insert'] = $this->language->get('button_insert');
        $this->data['button_delete'] = $this->language->get('button_delete');
        $this->data['button_filter'] = $this->language->get('button_filter');

        if (isset($this->error['warning'])) {
            $this->data['error_warning'] = $this->error['warning'];
        } else {
            $this->data['error_warning'] = '';
        }

        if ($this->session->has('success')) {
            $this->data['success'] = $this->session->get('success');

            $this->session->clear('success');
        } else {
            $this->data['success'] = '';
        }

        // SCRIPTS
        $scripts[] = array('id' => 'orderList', 'method' => 'function', 'script' =>
            "function eliminar(e) {
                if (confirm('\\xbfDesea eliminar este objeto?')) {
                    $('#tr_' + e).remove();
                	$.getJSON('" . Url::createAdminUrl("sale/order/delete") . "',{
                        id:e
                    });
                }
                return false;
             }
            function deleteAll() {
                if (confirm('\\xbfDesea eliminar todos los objetos seleccionados?')) {
                    $('#gridWrapper').hide();
                    $('#gridPreloader').show();
                    $.post('" . Url::createAdminUrl("sale/order/delete") . "',$('#form').serialize(),function(){
                        $('#gridWrapper').load('" . Url::createAdminUrl("sale/order/grid") . "',function(){
                            $('#gridWrapper').show();
                            $('#gridPreloader').hide();
                        });
                    });
                }
                return false;
            }");
        $scripts[] = array('id' => 'sortable', 'method' => 'ready', 'script' =>
            "$('#gridWrapper').load('" . Url::createAdminUrl("sale/order/grid") . "',function(){
                $('#gridPreloader').hide();
            });
                
            $('#formFilter').ntForm({
                lockButton:false,
                ajax:true,
                type:'get',
                dataType:'html',
                url:'" . Url::createAdminUrl("sale/order/grid") . "',
                beforeSend:function(){
                    $('#gridWrapper').hide();
                    $('#gridPreloader').show();
                },
                success:function(data){
                    $('#gridPreloader').hide();
                    $('#gridWrapper').html(data).show();
                }
            });
            $('#formFilter').on('keyup', function(e){
                var code = e.keyCode || e.which;
                if (code == 13){
                    $('#formFilter').ntForm('submit');
                }
            });");

        $this->scripts = array_merge($this->scripts, $scripts);

        $template = ($this->config->get('default_admin_view_sale_order_list')) ? $this->config->get('default_admin_view_sale_order_list') : 'sale/order_list.tpl';
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_admin_template') . '/'. $template)) {
            $this->template = $this->config->get('config_admin_template') . '/' . $template;
        } else {
            $this->template = 'default/' . $template;
        }

        $this->children[] = 'common/header';
        $this->children[] = 'common/nav';
        $this->children[] = 'common/footer';

        $this->response->setOutput($this->render(true), $this->config->get('config_compression'));
    }

    public function grid() {
        $filter_name = isset($this->request->get['filter_name']) ? $this->request->get['filter_name'] : null;
        $filter_order_id = isset($this->request->get['filter_order_id']) ? $this->request->get['filter_order_id'] : null;
        $filter_order_status_id = isset($this->request->get['filter_order_status_id']) ? $this->request->get['filter_order_status_id'] : null;
        $filter_total = isset($this->request->get['filter_total']) ? $this->request->get['filter_total'] : null;
        $filter_date_start = isset($this->request->get['filter_date_start']) ? $this->request->get['filter_date_start'] : null;
        $filter_date_end = isset($this->request->get['filter_date_end']) ? $this->request->get['filter_date_end'] : null;
        $page = isset($this->request->get['page']) ? $this->request->get['page'] : 1;
        $sort = isset($this->request->get['sort']) ? $this->request->get['sort'] : 'o.order_id';
        $corder = isset($this->request->get['order']) ? $this->request->get['order'] : 'DESC';
        $limit = !empty($this->request->get['limit']) ? $this->request->get['limit'] : $this->config->get('config_admin_limit');

        $url = '';

        if (isset($this->request->get['filter_name'])) {
            $url .= '&filter_name=' . $this->request->get['filter_name'];
        }
        if (isset($this->request->get['filter_order_id'])) {
            $url .= '&filter_order_id=' . $this->request->get['filter_order_id'];
        }
        if (isset($this->request->get['filter_order_status_id'])) {
            $url .= '&filter_order_status_id=' . $this->request->get['filter_order_status_id'];
        }
        if (isset($this->request->get['filter_total'])) {
            $url .= '&filter_total=' . $this->request->get['filter_total'];
        }
        if (isset($this->request->get['filter_date_start'])) {
            $url .= '&filter_date_start=' . $this->request->get['filter_date_start'];
        }
        if (isset($this->request->get['filter_date_end'])) {
            $url .= '&filter_date_end=' . $this->request->get['filter_date_end'];
        }
        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }
        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }
        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }
        if (!empty($this->request->get['limit'])) {
            $url .= '&limit=' . $this->request->get['limit'];
        }

        $this->data['orders'] = [];

        $data = array(
            'filter_order_id' => $filter_order_id,
            'filter_name' => $filter_name,
            'filter_order_status_id' => $filter_order_status_id,
            'filter_date_start' => $filter_date_start,
            'filter_date_end' => $filter_date_end,
            'filter_total' => $filter_total,
            'sort' => $sort,
            'order' => $corder,
            'start' => ($page - 1) * $limit,
            'limit' => $limit
        );

        $order_total = $this->modelOrder->getAllTotal($data);
        $results = $this->modelOrder->getAll($data);
        $this->data['order_statuses'] = $this->modelOrderstatus->getAll();

            $i = str_replace('%theme%',$this->config->get('config_admin_template'),HTTP_ADMIN_THEME_IMAGE);
        foreach ($results as $result) {

            $action = array(
                'print' => array(
                    'action' => 'print',
                    'text' => $this->language->get('button_invoice'),
                    'href' => Url::createAdminUrl('sale/order/invoice') . '&order_id=' . $result['order_id'],
                    'img' => $i.'print.png'
                ),
                'edit' => array(
                    'action' => 'edit',
                    'text' => $this->language->get('text_edit'),
                    'href' => Url::createAdminUrl('sale/order/update') . '&order_id=' . $result['order_id'] . $url,
                    'img' =>  $i .'edit.png'
                ),
                'delete' => array(
                    'action' => 'delete',
                    'text' => $this->language->get('text_delete'),
                    'href' => '',
                    'img' => $i.'delete.png'
                )
            );

            $this->data['orders'][] = array(
                'order_id' => $result['order_id'],
                'name' => $result['name'],
                'status' => $result['status'],
                'invoice_id' => $result['invoice_prefix'] . $result['invoice_id'],
                'store_name' => $result['store_name'],
                'store_url' => $result['store_url'],
                'telephone' => $result['telephone'],
                'fax' => $result['fax'],
                'email' => $result['email'],
                'shipping_address' => $result['shipping_address_1'] . ", " . $result['shipping_city'] . ". " . $result['shipping_zone'] . " - " . $result['shipping_country'],
                'shipping_method' => $result['shipping_method'],
                'payment_address' => $result['payment_address_1'] . ", " . $result['payment_city'] . ". " . $result['payment_zone'] . " - " . $result['payment_country'],
                'payment_method' => $result['payment_method'],
                'currency' => $result['currency'],
                'ip' => $result['ip'],
                'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
                'date_modified' => date($this->language->get('date_format_short'), strtotime($result['date_modified'])),
                'fdate_added' => $result['date_added'],
                'fdate_modified' => $result['date_modified'],
                'total' => $this->currency->format($result['total'], $result['currency'], $result['value']),
                'selected' => isset($this->request->post['selected']) && in_array($result['order_id'], $this->request->post['selected']),
                'action' => $action
            );
        }

        $this->data['text_no_results'] = $this->language->get('text_no_results');
        $this->data['text_missing_orders'] = $this->language->get('text_missing_orders');

        $this->data['column_order'] = $this->language->get('column_order');
        $this->data['column_name'] = $this->language->get('column_name');
        $this->data['column_status'] = $this->language->get('column_status');
        $this->data['column_date_added'] = $this->language->get('column_date_added');
        $this->data['column_total'] = $this->language->get('column_total');
        $this->data['column_action'] = $this->language->get('column_action');

        $url = '';

        if (isset($this->request->get['filter_order_id'])) {
            $url .= '&filter_order_id=' . $this->request->get['filter_order_id'];
        }
        if (isset($this->request->get['filter_name'])) {
            $url .= '&filter_name=' . $this->request->get['filter_name'];
        }
        if (isset($this->request->get['filter_order_status_id'])) {
            $url .= '&filter_order_status_id=' . $this->request->get['filter_order_status_id'];
        }
        if (isset($this->request->get['filter_date_start'])) {
            $url .= '&filter_date_start=' . $this->request->get['filter_date_start'];
        }
        if (isset($this->request->get['filter_date_end'])) {
            $url .= '&filter_date_end=' . $this->request->get['filter_date_end'];
        }
        if (isset($this->request->get['filter_total'])) {
            $url .= '&filter_total=' . $this->request->get['filter_total'];
        }
        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        if ($corder == 'ASC') {
            $url .= '&order=DESC';
        } else {
            $url .= '&order=ASC';
        }


        $this->data['sort_order'] = Url::createAdminUrl('sale/order/grid') . '&sort=o.order_id' . $url;
        $this->data['sort_name'] = Url::createAdminUrl('sale/order/grid') . '&sort=name' . $url;
        $this->data['sort_status'] = Url::createAdminUrl('sale/order/grid') . '&sort=status' . $url;
        $this->data['sort_date_added'] = Url::createAdminUrl('sale/order/grid') . '&sort=o.date_added' . $url;
        $this->data['sort_total'] = Url::createAdminUrl('sale/order/grid') . '&sort=o.total' . $url;

        $url = '';

        if (isset($this->request->get['filter_order_id'])) {
            $url .= '&filter_order_id=' . $this->request->get['filter_order_id'];
        }
        if (isset($this->request->get['filter_name'])) {
            $url .= '&filter_name=' . $this->request->get['filter_name'];
        }
        if (isset($this->request->get['filter_order_status_id'])) {
            $url .= '&filter_order_status_id=' . $this->request->get['filter_order_status_id'];
        }
        if (isset($this->request->get['filter_date_added'])) {
            $url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
        }
        if (isset($this->request->get['filter_total'])) {
            $url .= '&filter_total=' . $this->request->get['filter_total'];
        }
        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }
        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        $pagination = new Pagination();
        $pagination->ajax = true;
        $pagination->ajaxTarget = "gridWrapper";
        $pagination->total = $order_total;
        $pagination->page = $page;
        $pagination->limit = $limit;
        $pagination->text = $this->language->get('text_pagination');
        $pagination->url = Url::createAdminUrl('sale/order/grid') . $url . '&page={page}';

        $this->data['pagination'] = $pagination->render();

        $this->data['filter_order_id'] = $filter_order_id;
        $this->data['filter_name'] = $filter_name;
        $this->data['filter_order_status_id'] = $filter_order_status_id;
        $this->data['filter_date_start'] = $filter_date_start;
        $this->data['filter_date_end'] = $filter_date_end;
        $this->data['filter_total'] = $filter_total;

        $this->data['sort'] = $sort;
        $this->data['corder'] = $corder;

        $template = ($this->config->get('default_admin_view_sale_order_grid')) ? $this->config->get('default_admin_view_sale_order_grid') : 'sale/order_grid.tpl';
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_admin_template') . '/'. $template)) {
            $this->template = $this->config->get('config_admin_template') . '/' . $template;
        } else {
            $this->template = 'default/' . $template;
        }


        $this->response->setOutput($this->render(true), $this->config->get('config_compression'));
    }

    public function getForm() {
        $order_id = $this->request->hasQuery('order_id') ? $this->request->getQuery('order_id') : 0;
        if ($order_id) {
            $order_info = $this->modelOrder->getById($order_id);
        }

        $this->document->title = $this->language->get('heading_title');

        $this->data['heading_title'] = $this->language->get('heading_title');

        $this->document->breadcrumbs = [];
        $this->document->breadcrumbs[] = array(
            'href' => Url::createAdminUrl('common/home'),
            'text' => $this->language->get('text_home'),
            'separator' => false
        );

        $this->document->breadcrumbs[] = array(
            'href' => Url::createAdminUrl('sale/order'),
            'text' => $this->language->get('heading_title'),
            'separator' => ' :: '
        );

        if (!$this->request->hasQuery('order_id') && ($this->request->server['REQUEST_METHOD'] == 'POST')) {
            $order_info = [];
        }


        if ($this->request->hasQuery('order_id')) {
            $this->data['invoice'] = Url::createAdminUrl('sale/order/invoice') . '&order_id=' . (int) $this->request->get['order_id'];
            $this->data['order_id'] = $this->request->get['order_id'];
            $this->data['action'] = Url::createAdminUrl('sale/order/update') . '&order_id=' . $this->request->get['order_id'];
        } else {
            $this->data['invoice'] = false;
            $this->data['order_id'] = 0;
            $this->data['action'] = Url::createAdminUrl('sale/order/insert');
        }

        $this->data['cancel'] = Url::createAdminUrl('sale/order');

        $this->data['order_id'] = $this->request->get['order_id'];

        if ($order_info['invoice_id']) {
            $this->data['invoice_id'] = $order_info['invoice_prefix'] . $order_info['invoice_id'];
        } else {
            $this->data['invoice_id'] = '';
        }

        // These only change for insert, not edit. To be added later
        $this->data['ip'] = $order_info['ip'];
        $this->data['store_name'] = $order_info['store_name'];
        $this->data['store_url'] = $order_info['store_url'];
        $this->data['comment'] = nl2br($order_info['comment']);
        $this->data['firstname'] = $order_info['firstname'];
        $this->data['lastname'] = $order_info['lastname'];
        $this->data['company'] = $order_info['payment_company'];
        //


        if ($order_info['customer_id']) {
            $this->data['customer'] = Url::createAdminUrl('sale/customer/update') . '&customer_id=' . $order_info['customer_id'];
        } else {
            $this->data['customer'] = '';
        }
        $customer_group_info = $this->modelCustomergroup->getById($order_info['customer_group_id']);

        if ($customer_group_info) {
            $this->data['customer_group'] = $customer_group_info['name'];
        } else {
            $this->data['customer_group'] = '';
        }

        $this->setvar('email', $order_info, '');
        $this->setvar('telephone', $order_info, '');
        $this->setvar('fax', $order_info, '');
        $this->setvar('shipping_method', $order_info, '');
        $this->setvar('shipping_firstname', $order_info, '');
        $this->setvar('shipping_lastname', $order_info, '');
        $this->setvar('shipping_company', $order_info, '');
        $this->setvar('shipping_address_1', $order_info, '');
        $this->setvar('shipping_address_2', $order_info, '');
        $this->setvar('shipping_city', $order_info, '');
        $this->setvar('shipping_postcode', $order_info, '');
        $this->setvar('shipping_zone', $order_info, '');
        $this->setvar('shipping_zone_id', $order_info, '');
        $this->setvar('shipping_country', $order_info, '');
        $this->setvar('shipping_country_id', $order_info, '');
        $this->setvar('payment_method', $order_info, '');
        $this->setvar('payment_firstname', $order_info, '');
        $this->setvar('payment_lastname', $order_info, '');
        $this->setvar('payment_company', $order_info, '');
        $this->setvar('payment_address_1', $order_info, '');
        $this->setvar('payment_address_2', $order_info, '');
        $this->setvar('payment_city', $order_info, '');
        $this->setvar('payment_postcode', $order_info, '');
        $this->setvar('payment_zone', $order_info, '');
        $this->setvar('payment_zone_id', $order_info, '');
        $this->setvar('payment_country', $order_info, '');
        $this->setvar('payment_country_id', $order_info, '');

        if (isset($this->request->post['date_added'])) {
            $this->data['date_added'] = $this->request->post['date_added'];
        } elseif (isset($order_info['date_added'])) {
            $this->data['date_added'] = date($this->language->get('date_format_short'), strtotime($order_info['date_added']));
        } else {
            $this->data['date_added'] = date($this->language->get('date_format_short'), time());
        }

        // Not Shown variable, but needed for totals
        if (isset($order_info['currency'])) {
            $this->data['currency'] = $order_info['currency'];
        } else {
            $this->data['currency'] = $this->config->get('config_currency');
        }

        // Not Shown variable, but needed for totals
        if (isset($order_info['value'])) {
            $this->data['value'] = $order_info['value'];
        } else {
            $this->data['value'] = '1.0000';
        }

        $order_status_info = $this->modelOrderstatus->getById($order_info['order_status_id']);

        if ($order_status_info) {
            $this->data['order_status'] = $order_status_info['name'];
        } else {
            $this->data['order_status'] = 0;
        }

        if (isset($this->request->post['total'])) {
            $this->data['total'] = $this->request->post['total'];
        } elseif (isset($order_info['total'])) {
            $this->data['total'] = $this->currency->format($order_info['total'], $this->data['currency'], $this->data['value']);
        } else {
            $this->data['total'] = '';
        }

        $this->load->auto('sale/customer');

        $this->data['customers'] = $this->modelCustomer->getAll();
        $this->data['countries'] = $this->modelCountry->getAll();
        $this->data['categories'] = $this->modelCategory->getAll();
        $this->data['products'] = $this->modelProduct->getAll();
        $this->data['order_products'] = [];

        if (isset($this->request->get['order_id'])) {
            $order_products = $this->modelOrder->getProducts($this->request->get['order_id']);
        } else {
            $order_products = [];
        }

        foreach ($order_products as $order_product) {
            $option_data = [];

            $options = $this->modelOrder->getOptions($this->request->get['order_id'], $order_product['order_product_id']);

            foreach ($options as $option) {
                $option_data[] = array(
                    'name' => $option['name'],
                    'value' => $option['value']
                );
            }

            $this->data['order_products'][] = array(
                'product_id' => $order_product['product_id'],
                'name' => $order_product['name'],
                'model' => $order_product['model'],
                'option' => $option_data,
                'quantity' => $order_product['quantity'],
                'price' => $this->currency->format($order_product['price'], $order_info['currency'], $order_info['value']),
                'total' => $this->currency->format($order_product['total'], $order_info['currency'], $order_info['value']),
                'href' => Url::createAdminUrl('store/product/update') . '&product_id=' . $order_product['product_id']
            );
        }

        if (isset($this->request->get['order_id'])) {
            $this->data['totals'] = $this->modelOrder->getTotals($this->request->get['order_id']);
        } else {
            $this->data['totals'] = [];
        }

        $this->data['histories'] = [];

        if (isset($this->request->get['order_id'])) {
            $results = $this->modelOrder->getHistory($this->request->get['order_id']);
        } else {
            $results = [];
        }

        foreach ($results as $result) {
            $this->data['histories'][] = array(
                'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
                'status' => $result['status'],
                'comment' => nl2br($result['comment']),
                'notify' => $result['notify'] ? $this->language->get('text_yes') : $this->language->get('text_no')
            );
        }

        $this->data['downloads'] = [];

        if (isset($this->request->get['order_id'])) {
            $results = $this->modelOrder->getDownloads($this->request->get['order_id']);
        } else {
            $results = [];
        }

        foreach ($results as $result) {
            $this->data['downloads'][] = array(
                'name' => $result['name'],
                'filename' => $result['mask'],
                'remaining' => $result['remaining']
            );
        }

        $this->data['order_statuses'] = $this->modelOrderstatus->getAll();

        if (isset($order_info['order_status_id'])) {
            $this->data['order_status_id'] = $order_info['order_status_id'];
        } else {
            $this->data['order_status_id'] = 0;
        }

        $payment_methods = $this->modelExtension->getInstalled('payment');

        foreach ($payment_methods as $payment_method) {
            $this->load->language('payment/' . $payment_method);
            $this->data['payment_methods'][] = array(
                'code' => $payment_method,
                'name' => $this->language->get('heading_title')
            );
        }

        $shipping_methods = $this->modelExtension->getInstalled('shipping');

        foreach ($shipping_methods as $shipping_method) {
            $this->load->language('shipping/' . $shipping_method);
            $this->data['shipping_methods'][] = array(
                'code' => $shipping_method,
                'name' => $this->language->get('heading_title')
            );
        }

        $this->data['Url'] = new Url;
        //TODO: cambiar la manera en la que se agregan productos al pedido #addsPanel
        $scripts[] = array('id' => 'orderScripts', 'method' => 'ready', 'script' =>
            "$('#addsWrapper').hide();

        $('#addsPanel').on('click',function(e){
            var products = $('#addsWrapper').find('.row');

            if (products.length == 0) {
                $.getJSON('" . Url::createAdminUrl("sale/order/products") . "',
                    {
                        'order_id':'" . $this->request->getQuery('order_id') . "'
                    }, function(data) {

                        $('#addsWrapper').html('<div class=\"row\"><label for=\"q\" style=\"float:left\">Filtrar listado de productos:</label><input type=\"text\" value=\"\" name=\"q\" id=\"q\" placeholder=\"Filtrar Productos\" /></div><div class=\"clear\"></div><br /><ul id=\"adds\"></ul>');

                        $.each(data, function(i,item){
                            $('#adds').append('<li><img src=\"' + item.pimage + '\" alt=\"' + item.pname + '\" /><b class=\"' + item.class + '\">' + item.pname + '</b><input type=\"hidden\" name=\"Products[' + item.product_id + ']\" value=\"' + item.value + '\" /></li>');

                        });

                        $('#q').on('change',function(e){
                            var that = this;
                            var valor = $(that).val().toLowerCase();
                            if (valor.length <= 0) {
                                $('#adds li').show();
                            } else {
                                $('#adds li b').each(function(){
                                    if ($(this).text().toLowerCase().indexOf( valor ) > 0) {
                                        $(this).closest('li').show();
                                    } else {
                                        $(this).closest('li').hide();
                                    }
                                });
                            }
                        });

                        $('li').on('click',function() {
                            var b = $(this).find('b');
                            if (b.hasClass('added')) {
                                b.removeClass('added').addClass('add');
                                $(this).find('input').val(0);
                            } else {
                                b.removeClass('add').addClass('added');
                                $(this).find('input').val(1);
                            }
                        });
                });
            }
        });

        $('#addsPanel').on('click',function(){ $('#addsWrapper').slideToggle() });

        $('#form').ntForm({
            submitButton:false,
            cancelButton:false,
            lockButton:false
        });
        $('textarea').ntTextArea();

        var form_clean = $('#form').serialize();

        window.onbeforeunload = function (e) {
            var form_dirty = $('#form').serialize();
            if(form_clean != form_dirty) {
                return 'There is unsaved form data.';
            }
        };

        $('.vtabs_page').hide();
        $('#tab_order').show();");

        $scripts[] = array('id' => 'orderFunctions', 'method' => 'function', 'script' =>
            "function saveAndExit() {
            window.onbeforeunload = null;
            $('#form').append(\"<input type='hidden' name='to' value='saveAndExit'>\").submit();
        }

        function saveAndKeep() {
            window.onbeforeunload = null;
            $('#form').append(\"<input type='hidden' name='to' value='saveAndKeep'>\").submit();
        }

        function saveAndNew() {
            window.onbeforeunload = null;
            $('#form').append(\"<input type='hidden' name='to' value='saveAndNew'>\").submit();
        }

        function showTab(a) {
            $('.vtabs_page').hide();
            $($(a).attr('data-target')).show();
        }");
        
        $this->scripts = array_merge($this->scripts, $scripts);

        $template = ($this->config->get('default_admin_view_sale_order_form')) ? $this->config->get('default_admin_view_sale_order_form') : 'sale/order_form.tpl';
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_admin_template') . '/'. $template)) {
            $this->template = $this->config->get('config_admin_template') . '/' . $template;
        } else {
            $this->template = 'default/' . $template;
        }

        $this->children[] = 'common/header';
        $this->children[] = 'common/nav';
        $this->children[] = 'common/footer';

        $this->response->setOutput($this->render(true), $this->config->get('config_compression'));
    }

    public function generate() {
        $this->load->auto('sale/order');

        $json = [];

        if (isset($this->request->get['order_id'])) {
            $json['invoice_id'] = $this->modelOrder->generateInvoiceId($this->request->get['order_id']);
        }

        $this->load->library('json');

        $this->response->setOutput(Json::encode($json));
    }

    public function history() {
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . "GMT");
        header("Cache-Control: no-cache, must-revalidate");
        header("Pragma: no-cache");
        header("Content-type: application/json");

        $this->language->load('sale/order');
        $this->load->auto('sale/order');

        $json = [];

        if (!$this->user->hasPermission('modify', 'sale/order')) {
            $json['error'] = $this->language->get('error_permission');
        } else {
            $this->modelOrder->addHistory($this->request->get['order_id'], $this->request->post);


        if ($data['notify']) {
            $order = $this->modelOrder->getById($order_id);

            if ($order_query->num_rows) {
                $language = new Language($order_query->row['directory']);
                $language->load($order_query->row['filename']);
                //$language->load('mail/order');
                //TODO: cargar la plantilla de email asociada con esta accion

                $subject = sprintf($language->get('text_subject'), $order_query->row['store_name'], $order_id);
    
                $message  = "<p><b>". $language->get('text_order') . ' ' . $order_id ."</b></p>";
                $message .= "<p>". $language->get('text_date_added') . ' ' . date('d-m-Y', strtotime($order_query->row['date_added'])) ."</p>";
                $message .= "<p>". $language->get('text_order_status') . '&nbsp;<b>' . $order_query->row['status'] ."</b></p>";
                $message .= "<p>". $language->get('text_invoice') ."</p>";
                $message .= "<a href=\"". html_entity_decode($order_query->row['store_url'] . 'index.php?r=account/invoice&order_id=' . $order_id, ENT_QUOTES, 'UTF-8') . "\">Ver Pedido</a>";
                
                if ($data['comment']) { 
                    $message .= "<br /><p>". $language->get('text_comment') . "</p>";
                    $message .= "<br /><p>". strip_tags(html_entity_decode($data['comment'], ENT_QUOTES, 'UTF-8')) . "</p>";
                }
                
                $message .= $language->get('text_footer');

                $this->load->library('email/mailer');
                $mailer = new Mailer;
                    if ($this->config->get('config_smtp_method')=='smtp') {
                        $mailer->IsSMTP();
                        $mailer->Host = $this->config->get('config_smtp_host');
                        $mailer->Username = $this->config->get('config_smtp_username');
                        $mailer->Password = base64_decode($this->config->get('config_smtp_password'));
                        $mailer->Port     = $this->config->get('config_smtp_port');
                        $mailer->Timeout  = $this->config->get('config_smtp_timeout');
                        $mailer->SMTPSecure = $this->config->get('config_smtp_ssl');
                        $mailer->SMTPAuth = ($this->config->get('config_smtp_auth')) ? true : false;
                        
                    } elseif ($this->config->get('config_smtp_method')=='sendmail') {
                        $mailer->IsSendmail();
                    } else {
                        $mailer->IsMail();
                    }
                    $mailer->IsHTML();
                    $mailer->AddAddress($order_query->row['email'],$order_query->row['payment_firstname']);
                    $mailer->SetFrom($this->config->get('config_email'),$this->config->get('config_name'));
                    $mailer->Subject = $subject;
                    $mailer->Body = $message;
                    $mailer->Send();
            }
        }





            $json['success'] = $this->language->get('text_success');
            $json['date_added'] = date($this->language->get('date_format_short'));

            $this->load->auto('localisation/orderstatus');

            $order_status_info = $this->modelOrderstatus->getById($this->request->post['order_status_id']);

            $json['order_status'] = ($order_status_info) ? $order_status_info['name'] : '';
            $json['notify'] = ($this->request->post['notify']) ? $this->language->get('text_yes') : $this->language->get('text_no');
            $json['comment'] = isset($this->request->post['comment']) ? $this->request->post['comment'] : '';
        }

        $this->load->library('json');
        $this->response->setOutput(Json::encode($json));
    }

    private function validateForm() {
        if (!$this->user->hasPermission('modify', 'sale/order')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }

    private function validateDelete() {
        if (!$this->user->hasPermission('modify', 'sale/order')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }

    public function invoice() {
        $this->data['title'] = $this->language->get('heading_title');

        if (isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) {
            $this->data['base'] = HTTP_HOME;
        } else {
            $this->data['base'] = HTTP_HOME;
        }

        $this->data['direction'] = $this->language->get('direction');
        $this->data['language'] = $this->language->get('code');

        $this->data['text_invoice'] = $this->language->get('text_invoice');

        $this->data['text_order_id'] = $this->language->get('text_order_id');
        $this->data['text_invoice_id'] = $this->language->get('text_invoice_id');
        $this->data['text_date_added'] = $this->language->get('text_date_added');
        $this->data['text_telephone'] = $this->language->get('text_telephone');
        $this->data['text_fax'] = $this->language->get('text_fax');
        $this->data['text_to'] = $this->language->get('text_to');
        $this->data['text_ship_to'] = $this->language->get('text_ship_to');

        $this->data['column_product'] = $this->language->get('column_product');
        $this->data['column_model'] = $this->language->get('column_model');
        $this->data['column_quantity'] = $this->language->get('column_quantity');
        $this->data['column_price'] = $this->language->get('column_price');
        $this->data['column_total'] = $this->language->get('column_total');
        $this->data['column_comment'] = $this->language->get('column_comment');

        $this->data['logo'] = DIR_IMAGE . $this->config->get('config_logo');

        $this->data['orders'] = [];

        $orders = [];

        if (isset($this->request->post['selected'])) {
            $orders = $this->request->post['selected'];
        } elseif (isset($this->request->get['order_id'])) {
            $orders[] = $this->request->get['order_id'];
        }

        foreach ($orders as $order_id) {
            $order_info = $this->modelOrder->getOrder($order_id);

            if ($order_info) {
                if ($order_info['invoice_id']) {
                    $invoice_id = $order_info['invoice_prefix'] . $order_info['invoice_id'];
                } else {
                    $invoice_id = '';
                }

                if ($order_info['shipping_address_format']) {
                    $format = $order_info['shipping_address_format'];
                } else {
                    $format = '{firstname} {lastname}' . "\n" . '{company}' . "\n" . '{address_1}' . "\n" . '{address_2}' . "\n" . '{city} {postcode}' . "\n" . '{zone}' . "\n" . '{country}';
                }

                $find = array(
                    '{firstname}',
                    '{lastname}',
                    '{company}',
                    '{address_1}',
                    '{address_2}',
                    '{city}',
                    '{postcode}',
                    '{zone}',
                    '{zone_code}',
                    '{country}'
                );

                $replace = array(
                    'firstname' => $order_info['shipping_firstname'],
                    'lastname' => $order_info['shipping_lastname'],
                    'company' => $order_info['shipping_company'],
                    'address_1' => $order_info['shipping_address_1'],
                    'address_2' => $order_info['shipping_address_2'],
                    'city' => $order_info['shipping_city'],
                    'postcode' => $order_info['shipping_postcode'],
                    'zone' => $order_info['shipping_zone'],
                    'zone_code' => $order_info['shipping_zone_code'],
                    'country' => $order_info['shipping_country']
                );

                $shipping_address = str_replace(array("\r\n", "\r", "\n"), '<br />', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '<br />', trim(str_replace($find, $replace, $format))));

                if ($order_info['payment_address_format']) {
                    $format = $order_info['payment_address_format'];
                } else {
                    $format = '{firstname} {lastname}' . "\n" . '{company}' . "\n" . '{address_1}' . "\n" . '{address_2}' . "\n" . '{city} {postcode}' . "\n" . '{zone}' . "\n" . '{country}';
                }

                $find = array(
                    '{firstname}',
                    '{lastname}',
                    '{company}',
                    '{address_1}',
                    '{address_2}',
                    '{city}',
                    '{postcode}',
                    '{zone}',
                    '{zone_code}',
                    '{country}'
                );

                $replace = array(
                    'firstname' => $order_info['payment_firstname'],
                    'lastname' => $order_info['payment_lastname'],
                    'company' => $order_info['payment_company'],
                    'address_1' => $order_info['payment_address_1'],
                    'address_2' => $order_info['payment_address_2'],
                    'city' => $order_info['payment_city'],
                    'postcode' => $order_info['payment_postcode'],
                    'zone' => $order_info['payment_zone'],
                    'zone_code' => $order_info['payment_zone_code'],
                    'country' => $order_info['payment_country']
                );

                $payment_address = str_replace(array("\r\n", "\r", "\n"), '<br />', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '<br />', trim(str_replace($find, $replace, $format))));

                $product_data = [];

                $products = $this->modelOrder->getProducts($order_id);

                foreach ($products as $product) {
                    $option_data = [];

                    $options = $this->modelOrder->getOptions($order_id, $product['order_product_id']);

                    foreach ($options as $option) {
                        $option_data[] = array(
                            'name' => $option['name'],
                            'value' => $option['value']
                        );
                    }

                    $product_data[] = array(
                        'name' => $product['name'],
                        'model' => $product['model'],
                        'option' => $option_data,
                        'quantity' => $product['quantity'],
                        'price' => $this->currency->format($product['price'], $order_info['currency'], $order_info['value']),
                        'total' => $this->currency->format($product['total'], $order_info['currency'], $order_info['value'])
                    );
                }

                $total_data = $this->modelOrder->getTotals($order_id);

                $this->data['orders'][] = array(
                    'order_id' => $order_id,
                    'invoice_id' => $invoice_id,
                    'date_added' => date($this->language->get('date_format_short'), strtotime($order_info['date_added'])),
                    'store_name' => $order_info['store_name'],
                    'store_url' => rtrim($order_info['store_url'], '/'),
                    'address' => nl2br($this->config->get('config_address')),
                    'telephone' => $this->config->get('config_telephone'),
                    'fax' => $this->config->get('config_fax'),
                    'email' => $this->config->get('config_email'),
                    'shipping_address' => $shipping_address,
                    'payment_address' => $payment_address,
                    'customer_email' => $order_info['email'],
                    'ip' => $order_info['ip'],
                    'customer_telephone' => $order_info['telephone'],
                    'comment' => $order_info['comment'],
                    'product' => $product_data,
                    'total' => $total_data
                );
            }
        }

        $template = ($this->config->get('default_admin_view_sale_order_invoice')) ? $this->config->get('default_admin_view_sale_order_invoice') : 'sale/order_invoice.tpl';
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_admin_template') . '/'. $template)) {
            $this->template = $this->config->get('config_admin_template') . '/' . $template;
        } else {
            $this->template = 'default/' . $template;
        }

        $this->response->setOutput($this->render(true), $this->config->get('config_compression'));
    }

    public function category() {
        $this->load->auto('store/product');

        if (isset($this->request->get['category_id'])) {
            $category_id = $this->request->get['category_id'];
        } else {
            $category_id = 0;
        }

        $product_data = [];

        $results = $this->modelProduct->getAllByCategoryId($category_id);

        foreach ($results as $result) {
            $product_data[] = array(
                'product_id' => $result['product_id'],
                'title' => $result['pname'],
                'price' => $result['price'],
                'model' => $result['model']
            );
        }

        $this->load->library('json');

        $this->response->setOutput(Json::encode($product_data));
    }

    public function zone() {
        $output = '<select name="' . $this->request->get['type'] . '_id">';

        $this->load->auto('localisation/zone');

        $results = $this->modelZone->getAll(array(
            'country_id'=>$this->request->get['country_id'],
            'language_id'=>$this->config->get('config_language_id')
        ));

        $selected_name = '';

        foreach ($results as $result) {
            $output .= '<option value="' . $result['zone_id'] . '"';

            if (isset($this->request->get['zone_id']) && ($this->request->get['zone_id'] == $result['zone_id'])) {
                $output .= ' selected="selected"';
                $selected_name = $result['zone'];
            }

            $output .= '>' . $result['zone'] . '</option>';
        }

        if (!$results) {
            $output .= '<option value="0">' . $this->language->get('text_none') . '</option>';
        }

        $output .= '</select>';
        $output .= '<input type="hidden" id="' . $this->request->get['type'] . '_name" name="' . $this->request->get['type'] . '" value="' . $selected_name . '" />';

        $this->response->setOutput($output, $this->config->get('config_compression'));
    }

    /**
     * ControllerSaleOrder::eliminar()
     * activar o desactivar un objeto accedido por ajax
     * @return boolean
     * */
    public function eliminar() {
        if (!isset($_GET['id']))
            return false;
        $this->load->auto('sale/order');
        $result = $this->modelOrder->getOrder($_GET['id']);
        if ($result) {
            $this->modelOrder->delete($_GET['id']);
            echo 1;
        } else {
            echo 0;
        }
    }

    /**
     * ControllerSaleOrder::searchCustomer()
     * activar o desactivar un objeto accedido por ajax
     * @return boolean
     * */
    public function searchCustomer() {
        $this->load->auto('sale/customer');
        $this->data['customers'] = $this->modelCustomer->getAll();

        $template = ($this->config->get('default_admin_view_sale_order_form_customers')) ? $this->config->get('default_admin_view_sale_order_form_customers') : 'sale/order_form_customers.tpl';
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_admin_template') . '/'. $template)) {
            $this->template = $this->config->get('config_admin_template') . '/' . $template;
        } else {
            $this->template = 'default/' . $template;
        }

        $this->response->setOutput($this->render(true), $this->config->get('config_compression'));
    }
}
