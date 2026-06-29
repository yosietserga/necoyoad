<?php

/**
 * ControllerSaleCoupon
 * 
 * @package NecoTienda
 * @author Yosiet Serga
 * @copyright Inversiones Necoyoad, C.A.
 * @version 1.0.0
 * @access public
 * @see Controller
 */
class ControllerSaleCoupon extends Controller {

    private $error = [];

    /**
     * ControllerSaleCoupon::index()
     * 
     * @see Load
     * @see Document
     * @see Language
     * @see getList
     * @return void
     */
    public function index() {
        $this->document->title = $this->language->get('heading_title');
        $this->getList();
    }

    /**
     * ControllerSaleCoupon::insert()
     * 
     * @see Load
     * @see Document
     * @see Language
     * @see Session
     * @see Redirect
     * @see Request
     * @see getForm
     * @return void
     */
    public function insert() {
        $this->document->title = $this->language->get('heading_title');
        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {

            $ds = explode("/", $this->request->post['date_start']);
            $this->request->post['date_start'] = $ds[2] ."-". $ds[1] ."-". $ds[0];

            $de = explode("/", $this->request->post['date_end']);
            $this->request->post['date_end'] = $de[2] ."-". $de[1] ."-". $de[0];

            $coupon_id = $this->modelCoupon->add($this->request->post);

            $this->session->set('success', $this->language->get('text_success'));

            $url = '';

            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }

            if (isset($this->request->get['sort'])) {
                $url .= '&sort=' . $this->request->get['sort'];
            }

            if (isset($this->request->get['order'])) {
                $url .= '&order=' . $this->request->get['order'];
            }

            if ($_POST['to'] == "saveAndKeep") {
                $this->redirect(Url::createAdminUrl('sale/coupon/update', array('coupon_id' => $coupon_id)));
            } elseif ($_POST['to'] == "saveAndNew") {
                $this->redirect(Url::createAdminUrl('sale/coupon/insert'));
            } else {
                $this->redirect(Url::createAdminUrl('sale/coupon'));
            }
        }

        $this->getForm();
    }

    /**
     * ControllerSaleCoupon::update()
     * 
     * @see Load
     * @see Document
     * @see Language
     * @see Session
     * @see Redirect
     * @see Request
     * @see getForm
     * @return void
     */
    public function update() {
        $this->document->title = $this->language->get('heading_title');
        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $ds = explode("/", $this->request->post['date_start']);
            $this->request->post['date_start'] = $ds[2] ."-". $ds[1] ."-". $ds[0];

            $de = explode("/", $this->request->post['date_end']);
            $this->request->post['date_end'] = $de[2] ."-". $de[1] ."-". $de[0];
            
            $this->modelCoupon->update($this->request->get['coupon_id'], $this->request->post);

            $this->session->set('success', $this->language->get('text_success'));

            $url = '';

            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }

            if (isset($this->request->get['sort'])) {
                $url .= '&sort=' . $this->request->get['sort'];
            }

            if (isset($this->request->get['order'])) {
                $url .= '&order=' . $this->request->get['order'];
            }


            if ($_POST['to'] == "saveAndKeep") {
                $this->redirect(Url::createAdminUrl('sale/coupon/update', array('coupon_id' => $this->request->get['coupon_id'])));
            } elseif ($_POST['to'] == "saveAndNew") {
                $this->redirect(Url::createAdminUrl('sale/coupon/insert'));
            } else {
                $this->redirect(Url::createAdminUrl('sale/coupon'));
            }
        }

        $this->getForm();
    }

    /**
     * ControllerMarketingNewsletter::delete()
     * elimina un objeto
     * @return boolean
     * */
    public function delete() {
        $this->load->auto('sale/coupon');
        if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
            foreach ($this->request->post['selected'] as $id) {
                $this->modelCoupon->delete($id);
            }
        } else {
            $this->modelCoupon->delete($_GET['id']);
        }
    }

    /**
     * ControllerMarketingNewsletter::copy()
     * duplicar un objeto
     * @return boolean
     */
    public function copy() {
        $this->load->auto('sale/coupon');
        if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
            foreach ($this->request->post['selected'] as $id) {
                $this->modelCoupon->copy($id);
            }
        } else {
            $this->modelCoupon->copy($_GET['id']);
        }
        echo 1;
    }

    /**
     * ControllerSaleCoupon::getById()
     * 
     * @see Load
     * @see Document
     * @see Language
     * @see Session
     * @see Redirect
     * @see Request
     * @return void
     */
    private function getList() {
        $this->document->breadcrumbs = [];

        $this->document->breadcrumbs[] = array(
            'href' => Url::createAdminUrl('common/home'),
            'text' => $this->language->get('text_home'),
            'separator' => false
        );

        $this->document->breadcrumbs[] = array(
            'href' => Url::createAdminUrl('sale/coupon') . $url,
            'text' => $this->language->get('heading_title'),
            'separator' => ' :: '
        );

        $this->data['insert'] = Url::createAdminUrl('sale/coupon/insert') . $url;
        $this->data['delete'] = Url::createAdminUrl('sale/coupon/delete') . $url;

        $this->data['heading_title'] = $this->language->get('heading_title');

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
        $scripts[] = array('id' => 'couponList', 'method' => 'function', 'script' =>
            "function activate(e) {    
            	$.ajax({
            	   'type':'get',
                   'dataType':'json',
                   'url':'" . Url::createAdminUrl("sale/coupon/activate") . "&id=' + e,
                   'success': function(data) {
                        if (data > 0) {
                            $(\"#img_\" + e).attr('src','image/good.png');
                        } else {
                            $(\"#img_\" + e).attr('src','image/minus.png');
                        }
                   }
            	});
             }
            function copy(e) {
                $('#gridWrapper').hide();
                $('#gridPreloader').show();
                $.getJSON('" . Url::createAdminUrl("sale/coupon/copy") . "&id=' + e, function(data) {
                    $('#gridWrapper').load('" . Url::createAdminUrl("sale/coupon/grid") . "',function(response){
                        $('#gridPreloader').hide();
                        $('#gridWrapper').show();
                    });
                });
            }
            function eliminar(e) {
                if (confirm('\\xbfDesea eliminar este objeto?')) {
                    $('#tr_' + e).remove();
                	$.getJSON('" . Url::createAdminUrl("sale/coupon/delete") . "',{
                        id:e
                    });
                }
                return false;
             }
            function editAll() {
                return false;
            } 
            function addToList() {
                return false;
            } 
            function copyAll() {
                $('#gridWrapper').hide();
                $('#gridPreloader').show();
                $.post('" . Url::createAdminUrl("sale/coupon/copy") . "',$('#form').serialize(),function(){
                    $('#gridWrapper').load('" . Url::createAdminUrl("sale/coupon/grid") . "',function(){
                        $('#gridWrapper').show();
                        $('#gridPreloader').hide();
                    });
                });
                return false;
            } 
            function deleteAll() {
                if (confirm('\\xbfDesea eliminar todos los objetos seleccionados?')) {
                    $('#gridWrapper').hide();
                    $('#gridPreloader').show();
                    $.post('" . Url::createAdminUrl("sale/coupon/delete") . "',$('#form').serialize(),function(){
                        $('#gridWrapper').load('" . Url::createAdminUrl("sale/coupon/grid") . "',function(){
                            $('#gridWrapper').show();
                            $('#gridPreloader').hide();
                        });
                    });
                }
                return false;
            }");
        $scripts[] = array('id' => 'sortable', 'method' => 'ready', 'script' =>
            "$('#gridWrapper').load('" . Url::createAdminUrl("sale/coupon/grid") . "',function(){
                $('#gridPreloader').hide();
            });
                
            $('#formFilter').ntForm({
                lockButton:false,
                ajax:true,
                type:'get',
                dataType:'html',
                url:'" . Url::createAdminUrl("sale/coupon/grid") . "',
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

        $template = ($this->config->get('default_admin_view_sale_coupon_list')) ? $this->config->get('default_admin_view_sale_coupon_list') : 'sale/coupon_list.tpl';
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

    /**
     * ControllerSaleCoupon::getById()
     * 
     * @see Load
     * @see Document
     * @see Language
     * @see Session
     * @see Redirect
     * @see Request
     * @return void
     */
    public function grid() {
        $filter_name = isset($this->request->get['filter_name']) ? $this->request->get['filter_name'] : null;
        $filter_product = isset($this->request->get['filter_product']) ? $this->request->get['filter_product'] : null;
        $page = isset($this->request->get['page']) ? $this->request->get['page'] : 1;
        $sort = isset($this->request->get['sort']) ? $this->request->get['sort'] : 'cd.name';
        $order = isset($this->request->get['order']) ? $this->request->get['order'] : 'ASC';
        $limit = !empty($this->request->get['limit']) ? $this->request->get['limit'] : $this->config->get('config_admin_limit');

        $url = '';

        if (isset($this->request->get['filter_name'])) {
            $url .= '&filter_name=' . $this->request->get['filter_name'];
        }
        if (isset($this->request->get['filter_product'])) {
            $url .= '&filter_product=' . $this->request->get['filter_product'];
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

        $this->data['coupons'] = [];

        $data = array(
            'filter_name' => $filter_name,
            'filter_product' => $filter_product,
            'sort' => $sort,
            'order' => $order,
            'start' => ($page - 1) * $limit,
            'limit' => $limit
        );

        $coupon_total = $this->modelCoupon->getAllTotal();

        $results = $this->modelCoupon->getAll($data);

            $i = str_replace('%theme%',$this->config->get('config_admin_template'),HTTP_ADMIN_THEME_IMAGE);
        foreach ($results as $result) {
            $action = array(
                'edit' => array(
                    'action' => 'edit',
                    'text' => $this->language->get('text_edit'),
                    'href' => Url::createAdminUrl('sale/coupon/update') . '&coupon_id=' . $result['coupon_id'] . $url,
                    'img' =>  $i .'edit.png'
                ),
                'delete' => array(
                    'action' => 'delete',
                    'text' => $this->language->get('text_delete'),
                    'href' => '',
                    'img' => $i.'delete.png'
                )
            );

            $this->data['coupons'][] = array(
                'coupon_id' => $result['coupon_id'],
                'name' => $result['name'],
                'code' => $result['code'],
                'discount' => $result['discount'],
                'date_start' => date($this->language->get('date_format_short'), strtotime($result['date_start'])),
                'date_end' => date($this->language->get('date_format_short'), strtotime($result['date_end'])),
                'status' => ($result['status'] ? $this->language->get('text_enabled') : $this->language->get('text_disabled')),
                'selected' => isset($this->request->post['selected']) && in_array($result['coupon_id'], $this->request->post['selected']),
                'action' => $action
            );
        }

        $this->data['text_no_results'] = $this->language->get('text_no_results');

        $this->data['column_name'] = $this->language->get('column_name');
        $this->data['column_code'] = $this->language->get('column_code');
        $this->data['column_discount'] = $this->language->get('column_discount');
        $this->data['column_date_start'] = $this->language->get('column_date_start');
        $this->data['column_date_end'] = $this->language->get('column_date_end');
        $this->data['column_status'] = $this->language->get('column_status');
        $this->data['column_action'] = $this->language->get('column_action');

        $url = '';

        if ($order == 'ASC') {
            $url .= '&order=DESC';
        } else {
            $url .= '&order=ASC';
        }

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        $this->data['sort_name'] = Url::createAdminUrl('sale/coupon/grid') . '&sort=cd.name' . $url;
        $this->data['sort_code'] = Url::createAdminUrl('sale/coupon/grid') . '&sort=c.code' . $url;
        $this->data['sort_discount'] = Url::createAdminUrl('sale/coupon/grid') . '&sort=c.discount' . $url;
        $this->data['sort_date_start'] = Url::createAdminUrl('sale/coupon/grid') . '&sort=c.date_start' . $url;
        $this->data['sort_date_end'] = Url::createAdminUrl('sale/coupon/grid') . '&sort=c.date_end' . $url;
        $this->data['sort_status'] = Url::createAdminUrl('sale/coupon/grid') . '&sort=c.status' . $url;

        $url = '';

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }
        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        $pagination = new Pagination();
        $pagination->total = $coupon_total;
        $pagination->page = $page;
        $pagination->limit = $limit;
        $pagination->text = $this->language->get('text_pagination');
        $pagination->url = Url::createAdminUrl('sale/coupon') . $url . '&page={page}';

        $this->data['pagination'] = $pagination->render();

        $this->data['sort'] = $sort;
        $this->data['order'] = $order;

        $template = ($this->config->get('default_admin_view_sale_coupon_grid')) ? $this->config->get('default_admin_view_sale_coupon_grid') : 'sale/coupon_grid.tpl';
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_admin_template') . '/'. $template)) {
            $this->template = $this->config->get('config_admin_template') . '/' . $template;
        } else {
            $this->template = 'default/' . $template;
        }

        $this->response->setOutput($this->render(true), $this->config->get('config_compression'));
    }

    /**
     * ControllerSaleCoupon::getForm()
     * 
     * @see Load
     * @see Document
     * @see Language
     * @see Session
     * @see Redirect
     * @see Request
     * @return void
     */
    private function getForm() {
        $this->data['error_warning'] = isset($this->error['warning']) ? $this->error['warning'] : '';
        $this->data['error_name'] = isset($this->error['name']) ? $this->error['name'] : '';
        $this->data['error_description'] = isset($this->error['description']) ? $this->error['description'] : '';
        $this->data['error_code'] = isset($this->error['code']) ? $this->error['code'] : '';
        $this->data['error_date_start'] = isset($this->error['date_start']) ? $this->error['date_start'] : '';
        $this->data['error_date_end'] = isset($this->error['date_end']) ? $this->error['date_end'] : '';

        $this->document->breadcrumbs = [];
        $this->document->breadcrumbs[] = array(
            'href' => Url::createAdminUrl('common/home'),
            'text' => $this->language->get('text_home'),
            'separator' => false
        );
        $this->document->breadcrumbs[] = array(
            'href' => Url::createAdminUrl('sale/coupon') . $url,
            'text' => $this->language->get('heading_title'),
            'separator' => ' :: '
        );

        if (!isset($this->request->get['coupon_id'])) {
            $this->data['action'] = Url::createAdminUrl('sale/coupon/insert') . $url;
        } else {
            $this->data['action'] = Url::createAdminUrl('sale/coupon/update') . '&coupon_id=' . $this->request->get['coupon_id'] . $url;
        }

        $this->data['cancel'] = Url::createAdminUrl('sale/coupon') . $url;

        if (isset($this->request->get['coupon_id']) && (!$this->request->server['REQUEST_METHOD'] != 'POST')) {
            $coupon_info = $this->modelCoupon->getById($this->request->get['coupon_id']);
        }

        $this->data['languages'] = $this->modelLanguage->getAll();
        $this->data['stores'] = $this->modelStore->getAll();
        $this->data['_stores'] = $this->modelCoupon->getStores($this->request->get['coupon_id']);

        $this->setvar('code', $coupon_info, '');
        $this->setvar('type', $coupon_info, '');
        $this->setvar('discount', $coupon_info, '');
        $this->setvar('logged', $coupon_info, '');
        $this->setvar('shipping', $coupon_info, '');
        $this->setvar('total', $coupon_info, '');
        $this->setvar('uses_total', $coupon_info, 1);
        $this->setvar('uses_customer', $coupon_info, 1);
        $this->setvar('status', $coupon_info, 1);

        if (isset($this->request->post['coupon_description'])) {
            $this->data['coupon_description'] = $this->request->post['coupon_description'];
        } elseif (isset($this->request->get['coupon_id'])) {
            $this->data['coupon_description'] = $this->modelCoupon->getDescriptions($this->request->get['coupon_id']);
        } else {
            $this->data['coupon_description'] = [];
        }

        if (isset($this->request->post['product'])) {
            $this->data['coupon_product'] = $this->request->post['product'];
        } elseif (isset($coupon_info)) {
            $this->data['coupon_product'] = $this->modelCoupon->getProducts($this->request->get['coupon_id']);
        } else {
            $this->data['coupon_product'] = [];
        }

        $this->data['categories'] = $this->modelCategory->getAll();

        if (isset($this->request->post['date_start'])) {
            $this->data['date_start'] = $this->request->post['date_start'];
        } elseif (isset($coupon_info)) {
            $this->data['date_start'] = date('d/m/Y', strtotime($coupon_info['date_start']));
        } else {
            $this->data['date_start'] = date('d/m/Y', time());
        }

        if (isset($this->request->post['date_end'])) {
            $this->data['date_end'] = $this->request->post['date_end'];
        } elseif (isset($coupon_info)) {
            $this->data['date_end'] = date('d/m/Y', strtotime($coupon_info['date_end']));
        } else {
            $this->data['date_end'] = date('d/m/Y', time());
        }

        $this->data['Url'] = new Url;

        $scripts[] = array('id' => 'couponForm', 'method' => 'ready', 'script' =>
            "$('#addsWrapper').hide();
            
            $('#addsPanel').on('click',function(e){
                var products = $('#addsWrapper').find('.row');
                
                if (products.length == 0) {
                    $.getJSON('" . Url::createAdminUrl("sale/coupon/products") . "',
                        {
                            'coupon_id':'" . $this->request->getQuery('coupon_id') . "'
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
                
            $('#addsPanel').on('click',function(){ $('#addsWrapper').slideToggle() });");

        $this->scripts = array_merge($this->scripts, $scripts);

        $template = ($this->config->get('default_admin_view_sale_coupon_form')) ? $this->config->get('default_admin_view_sale_coupon_form') : 'sale/coupon_form.tpl';
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

    /**
     * ControllerSaleCoupon::validateForm()
     * 
     * @see User
     * @see Language
     * @see Request
     * @return bool
     */
    private function validateForm() {
        if (!$this->user->hasPermission('modify', 'sale/coupon')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        foreach ($this->request->post['coupon_description'] as $language_id => $value) {
            if (empty($value['title'])) {
                $this->error['title'][$language_id] = $this->language->get('error_title');
            }
        }

        if (empty($this->request->post['code'])) {
            $this->error['code'] = $this->language->get('error_code');
        }

        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * ControllerSaleCoupon::category()
     * 
     * @see Load
     * @see Request
     * @see Response
     * @return void
     */
    public function category() {
        $this->load->auto('store/product');

        if (isset($this->request->get['category_id'])) {
            $category_id = $this->request->get['category_id'];
        } else {
            $category_id = 0;
        }

        $product_data = [];

        $results = $this->modelProduct->getAllByCategoryId($category_id);
        $html_output = '';
        foreach ($results as $result) {
            $product_data[] = array(
                'product_id' => $result['product_id'],
                'name' => $result['name'],
                'model' => $result['model']
            );
        }
        if ($results) {
            foreach ($product_data as $product) {
                $html_output .= "<option value='" . $product['product_id'] . "'>" . $product['name'] . "</option>";
            }
        } else {
            $html_output .= 1;
        }
        echo $html_output;
    }

    public function products() {
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . "GMT");
        header("Cache-Control: no-cache, must-revalidate");
        header("Pragma: no-cache");
        header("Content-type: application/json");

        if ($this->request->hasQuery('coupon_id')) {
            $rows = $this->modelCoupon->getProducts($this->request->getQuery('coupon_id'));
            $products_by_coupon = [];
            foreach ($rows as $row) {
                $products_by_coupon[] = $row['product_id'];
            }
        }
        $cache = $this->cache->get("products.for.coupon.form");
        if ($cache) {
            $products = unserialize($cache);
        } else {
            $products = $this->modelProduct->getAll();
            $this->cache->set("products.for.coupon.form", serialize($products));
        }

        $this->data['Image'] = new NTImage();
        $this->data['Url'] = new Url;

        $output = [];

        foreach ($products as $product) {
            if (!empty($products_by_coupon) && in_array($product->product_id, $products_by_coupon)) {
                $output[] = array(
                    'product_id' => $product->product_id,
                    'pimage' => NTImage::resizeAndSave($product->pimage, 50, 50),
                    'pname' => $product->pname,
                    'class' => 'added',
                    'value' => 1
                );
            } else {
                $output[] = array(
                    'product_id' => $product->product_id,
                    'pimage' => NTImage::resizeAndSave($product->pimage, 50, 50),
                    'pname' => $product->pname,
                    'class' => 'add',
                    'value' => 0
                );
            }
        }
        $this->load->auto('json');
        $this->response->setOutput(Json::encode($output), $this->config->get('config_compression'));
    }

    /**
     * ControllerSaleCoupon::validateDelete()
     * 
     * @see User
     * @see Language
     * @see Request
     * @return bool
     */
    private function validateDelete() {
        if (!$this->user->hasPermission('modify', 'sale/coupon')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }

}
