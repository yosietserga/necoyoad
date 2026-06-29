<?php

/**
 * ControllerSalePayment
 * 
 * @package NecoTienda
 * @author Yosiet Serga
 * @copyright Inversiones Necoyoad, C.A.
 * @version 1.0.0
 * @access public
 * @see Controller
 */
class ControllerSalePayment extends Controller {

    private $error = [];

    /**
     * ControllerSalePayment::index()
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
     * ControllerSalePayment::insert()
     * 
     * @see Load
     * @see Document
     * @see Language
     * @see Session
     * @see Redirect
     * @see getForm
     * @return void 
     */
    public function insert() {
        $this->document->title = $this->language->get('heading_title');
        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $payment_id = $this->modelPayment->add($this->request->post);
            $this->session->set('success', $this->language->get('text_success'));
            if ($this->request->post['to'] == "saveAndKeep") {
                $this->redirect(Url::createAdminUrl('sale/payment/update', array('payment_id' => $payment_id)));
            } elseif ($this->request->post['to'] == "saveAndNew") {
                $this->redirect(Url::createAdminUrl('sale/payment/insert'));
            } else {
                $this->redirect(Url::createAdminUrl('sale/payment'));
            }
        }
        $this->getForm();
    }

    /**
     * ControllerSalePayment::update()
     * 
     * @see Load
     * @see Document
     * @see Language
     * @see Session
     * @see Redirect
     * @see getForm
     * @return void 
     */
    public function update() {
        $this->document->title = $this->language->get('heading_title');
        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $this->modelPayment->update($this->request->get['payment_id'], $this->request->post);
            $this->session->set('success', $this->language->get('text_success'));
            if ($this->request->post['to'] == "saveAndKeep") {
                $this->redirect(Url::createAdminUrl('sale/payment/update', array('payment_id' => $this->request->get['payment_id'])));
            } elseif ($this->request->post['to'] == "saveAndNew") {
                $this->redirect(Url::createAdminUrl('sale/payment/insert'));
            } else {
                $this->redirect(Url::createAdminUrl('sale/payment'));
            }
        }
        $this->getForm();
    }

    /**
     * ControllerSalePayment::delete()
     * elimina un objeto
     * @return boolean
     * */
    public function delete() {
        $this->load->auto('sale/payment');
        if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
            foreach ($this->request->post['selected'] as $id) {
                $this->modelPayment->delete($id);
            }
        } else {
            $this->modelPayment->delete($_GET['id']);
        }
    }

    /**
     * ControllerMarketingNewsletter::copy()
     * duplicar un objeto
     * @return boolean
     */
    public function copy() {
        $this->load->auto('sale/payment');
        if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
            foreach ($this->request->post['selected'] as $id) {
                $this->modelPayment->copy($id);
            }
        } else {
            $this->modelPayment->copy($_GET['id']);
        }
        echo 1;
    }

    /**
     * ControllerSalePayment::getById()
     * 
     * @see Load
     * @see Document
     * @see Language
     * @see Session
     * @see Response
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
            'href' => Url::createAdminUrl('sale/payment') . $url,
            'text' => $this->language->get('heading_title'),
            'separator' => ' :: '
        );

        $this->data['insert'] = Url::createAdminUrl('sale/payment/insert') . $url;

        if ($this->session->has('error')) {
            $this->data['error_warning'] = $this->session->get('error');

            $this->session->clear('error');
        } elseif (isset($this->error['warning'])) {
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

        $this->data['order_payment_statuses'] = $this->modelOrderpaymentstatus->getAll();
        $this->data['banks'] = $this->modelBank->getAll();

        // SCRIPTS
        $scripts[] = array('id' => 'paymentList', 'method' => 'function', 'script' =>
            "function activate(e) {    
            	$.ajax({
            	   'type':'get',
                   'dataType':'json',
                   'url':'" . Url::createAdminUrl("sale/payment/activate") . "&id=' + e,
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
                $.getJSON('" . Url::createAdminUrl("sale/payment/copy") . "&id=' + e, function(data) {
                    $('#gridWrapper').load('" . Url::createAdminUrl("sale/payment/grid") . "',function(response){
                        $('#gridPreloader').hide();
                        $('#gridWrapper').show();
                    });
                });
            }
            function eliminar(e) {
                if (confirm('\\xbfDesea eliminar este objeto?')) {
                    $('#tr_' + e).remove();
                	$.getJSON('" . Url::createAdminUrl("sale/payment/delete") . "',{
                        id:e
                    });
                }
                return false;
             }
            function copyAll() {
                $('#gridWrapper').hide();
                $('#gridPreloader').show();
                $.post('" . Url::createAdminUrl("sale/payment/copy") . "',$('#form').serialize(),function(){
                    $('#gridWrapper').load('" . Url::createAdminUrl("sale/payment/grid") . "',function(){
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
                    $.post('" . Url::createAdminUrl("sale/payment/delete") . "',$('#form').serialize(),function(){
                        $('#gridWrapper').load('" . Url::createAdminUrl("sale/payment/grid") . "',function(){
                            $('#gridWrapper').show();
                            $('#gridPreloader').hide();
                        });
                    });
                }
                return false;
            }");
        $scripts[] = array('id' => 'sortable', 'method' => 'ready', 'script' =>
            "$('#gridWrapper').load('" . Url::createAdminUrl("sale/payment/grid") . "',function(e){
                $('#gridPreloader').hide();
            });
                
            $('#formFilter').ntForm({
                lockButton:false,
                ajax:true,
                type:'get',
                dataType:'html',
                url:'" . Url::createAdminUrl("sale/payment/grid") . "',
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

        $template = ($this->config->get('default_admin_view_sale_payment_list')) ? $this->config->get('default_admin_view_sale_payment_list') : 'sale/payment_list.tpl';
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
     * ControllerSalePayment::grid()
     * 
     * @see Load
     * @see Document
     * @see Language
     * @see Session
     * @see Response
     * @see Request     
     * @return void 
     */
    public function grid() {
        $filter_order_id = isset($this->request->get['filter_order_id']) ? $this->request->get['filter_order_id'] : null;
        $filter_payment_id = isset($this->request->get['filter_payment_id']) ? $this->request->get['filter_payment_id'] : null;
        $filter_customer = isset($this->request->get['filter_customer']) ? $this->request->get['filter_customer'] : null;
        $filter_transac_number = isset($this->request->get['filter_transac_number']) ? $this->request->get['filter_transac_number'] : null;
        $filter_status = isset($this->request->get['filter_status']) ? $this->request->get['filter_status'] : null;
        $filter_bank = isset($this->request->get['filter_bank']) ? $this->request->get['filter_bank'] : null;
        $filter_date_start = isset($this->request->get['filter_date_start']) ? $this->request->get['filter_date_start'] : null;
        $filter_date_end = isset($this->request->get['filter_date_end']) ? $this->request->get['filter_date_end'] : null;
        $page = isset($this->request->get['page']) ? $this->request->get['page'] : 1;
        $sort = isset($this->request->get['sort']) ? $this->request->get['sort'] : 'op.date_added';
        $order = isset($this->request->get['order']) ? $this->request->get['order'] : 'DESC';
        $limit = !empty($this->request->get['limit']) ? $this->request->get['limit'] : $this->config->get('config_admin_limit');

        $url = '';

        if (isset($this->request->get['filter_order_id'])) {
            $url .= '&filter_order_id=' . $this->request->get['filter_order_id'];
        }
        if (isset($this->request->get['filter_payment_id'])) {
            $url .= '&filter_payment_id=' . $this->request->get['filter_payment_id'];
        }
        if (isset($this->request->get['filter_customer'])) {
            $url .= '&filter_customer=' . $this->request->get['filter_customer'];
        }
        if (isset($this->request->get['filter_transac_number'])) {
            $url .= '&filter_transac_number=' . $this->request->get['filter_transac_number'];
        }
        if (isset($this->request->get['filter_status'])) {
            $url .= '&filter_status=' . $this->request->get['filter_status'];
        }
        if (isset($this->request->get['filter_bank'])) {
            $url .= '&filter_bank=' . $this->request->get['filter_bank'];
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

        $this->data['payments'] = [];

        $data = array(
            'filter_order_id' => $filter_order_id,
            'filter_payment_id' => $filter_payment_id,
            'filter_customer' => $filter_customer,
            'filter_transac_number' => $filter_transac_number,
            'filter_status' => $filter_status,
            'filter_bank' => $filter_bank,
            'filter_date_start' => $filter_date_start,
            'filter_date_end' => $filter_date_end,
            'sort' => $sort,
            'order' => $order,
            'start' => ($page - 1) * $limit,
            'limit' => $limit
        );

        $payment_total = $this->modelPayment->getTotalAll($data);
        $results = $this->modelPayment->getAll($data);

        foreach ($results as $result) {
            /*
              $action = array(
              'edit'      => array(
              'action'  => 'edit',
              'text'  => $this->language->get('text_see'),
              'href'  =>Url::createAdminUrl('sale/payment/see') . '&order_payment_id=' . $result['order_payment_id'] . $url,
              'img'   => 'search_page.png'
              )
              );
             */
            $this->data['payments'][] = array(
                'order_payment_id' => $result['order_payment_id'],
                'order_id' => $result['order_id'],
                'status' => $result['status'],
                'bank' => $result['bank'],
                'transac_date' => $result['transac_date'],
                'transac_date' => date('d-m-Y', strtotime($result['transac_date'])),
                'transac_number' => $result['transac_number'],
                'payment_method' => $result['payment_method'],
                'customer' => $this->modelCustomer->getCustomer($result['customer_id']),
                'amount' => $this->currency->format($result['amount']),
                'date_added' => date('d-m-Y h:i A', strtotime($result['date_added'])),
                'selected' => isset($this->request->post['selected']) && in_array($result['order_payment_id'], $this->request->post['selected']),
                'action' => $action
            );
            $this->data['total'] += $result['amount'];
        }
        $this->data['total'] = $this->currency->format($this->data['total']);
        $url = '';

        if (isset($this->request->get['filter_name'])) {
            $url .= '&filter_name=' . $this->request->get['filter_name'];
        }

        if ($order == 'ASC') {
            $url .= '&order=DESC';
        } else {
            $url .= '&order=ASC';
        }

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        $this->data['sort_order_payment_id'] = Url::createAdminUrl('sale/payment/grid') . '&sort=op.order_payment_id' . $url;
        $this->data['sort_order_id'] = Url::createAdminUrl('sale/payment/grid') . '&sort=op.order_id' . $url;
        $this->data['sort_payment_method'] = Url::createAdminUrl('sale/payment/grid') . '&sort=op.payment_method' . $url;
        $this->data['sort_bank'] = Url::createAdminUrl('sale/payment/grid') . '&sort=bk.name' . $url;
        $this->data['sort_customer'] = Url::createAdminUrl('sale/payment/grid') . '&sort=customer' . $url;
        $this->data['sort_order_payment_status_id'] = Url::createAdminUrl('sale/payment/grid') . '&sort=op.order_payment_status_id' . $url;
        $this->data['sort_transac_date'] = Url::createAdminUrl('sale/payment/grid') . '&sort=op.transac_date' . $url;
        $this->data['sort_transac_number'] = Url::createAdminUrl('sale/payment/grid') . '&sort=op.transac_number' . $url;
        $this->data['sort_amount'] = Url::createAdminUrl('sale/payment/grid') . '&sort=op.amount' . $url;
        $this->data['sort_date_added'] = Url::createAdminUrl('sale/payment/grid') . '&sort=op.date_added' . $url;

        $url = '';

        if (isset($this->request->get['filter_order_id'])) {
            $url .= '&filter_order_id=' . $this->request->get['filter_order_id'];
        }
        if (isset($this->request->get['filter_payment_id'])) {
            $url .= '&filter_payment_id=' . $this->request->get['filter_payment_id'];
        }
        if (isset($this->request->get['filter_customer'])) {
            $url .= '&filter_customer=' . $this->request->get['filter_customer'];
        }
        if (isset($this->request->get['filter_transac_number'])) {
            $url .= '&filter_transac_number=' . $this->request->get['filter_transac_number'];
        }
        if (isset($this->request->get['filter_status'])) {
            $url .= '&filter_status=' . $this->request->get['filter_status'];
        }
        if (isset($this->request->get['filter_bank'])) {
            $url .= '&filter_bank=' . $this->request->get['filter_bank'];
        }
        if (isset($this->request->get['filter_date_start'])) {
            $url .= '&filter_date_start=' . $this->request->get['filter_date_start'];
        }
        if (isset($this->request->get['filter_date_end'])) {
            $url .= '&filter_date_end=' . $this->request->get['filter_date_end'];
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
        $pagination->total = $payment_total;
        $pagination->page = $page;
        $pagination->limit = $limit;
        $pagination->text = $this->language->get('text_pagination');
        $pagination->url = Url::createAdminUrl('sale/payment/grid') . $url . '&page={page}';

        $this->data['pagination'] = $pagination->render();

        $this->data['filter_name'] = $filter_name;
        $this->data['filter_date_start'] = $filter_date_start;
        $this->data['filter_date_end'] = $filter_date_end;

        $this->data['sort'] = $sort;
        $this->data['order'] = $order;

        $template = ($this->config->get('default_admin_view_sale_payment_grid')) ? $this->config->get('default_admin_view_sale_payment_grid') : 'sale/payment_grid.tpl';
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_admin_template') . '/'. $template)) {
            $this->template = $this->config->get('config_admin_template') . '/' . $template;
        } else {
            $this->template = 'default/' . $template;
        }


        $this->response->setOutput($this->render(true), $this->config->get('config_compression'));
    }

    /**
     * ControllerSalePayment::getForm()
     * 
     * @see Load
     * @see Document
     * @see Language
     * @see Session
     * @see Response
     * @see Request     
     * @return void 
     */
    private function getForm() {
        $this->data['heading_title'] = $this->language->get('heading_title');

        $this->data['text_enabled'] = $this->language->get('text_enabled');
        $this->data['text_disabled'] = $this->language->get('text_disabled');
        $this->data['text_select'] = $this->language->get('text_select');

        $this->data['entry_name'] = $this->language->get('entry_name');
        $this->data['entry_image'] = $this->language->get('entry_image');

        $this->data['help_name'] = $this->language->get('help_name');
        $this->data['help_image'] = $this->language->get('help_image');

        $this->data['button_cancel'] = $this->language->get('button_cancel');
        $this->data['button_save_and_new'] = $this->language->get('button_save_and_new');
        $this->data['button_save_and_exit'] = $this->language->get('button_save_and_exit');
        $this->data['button_save_and_keep'] = $this->language->get('button_save_and_keep');

        $this->data['error_warning'] = ($this->error['warning']) ? $this->error['warning'] : '';
        $this->data['error_name'] = ($this->error['name']) ? $this->error['name'] : '';

        $this->document->breadcrumbs = [];

        $this->document->breadcrumbs[] = array(
            'href' => Url::createAdminUrl('common/home'),
            'text' => $this->language->get('text_home'),
            'separator' => false
        );

        $this->document->breadcrumbs[] = array(
            'href' => Url::createAdminUrl('sale/payment') . $url,
            'text' => $this->language->get('heading_title'),
            'separator' => ' :: '
        );

        if (!isset($this->request->get['payment_id'])) {
            $this->data['action'] = Url::createAdminUrl('sale/payment/insert') . $url;
        } else {
            $this->data['action'] = Url::createAdminUrl('sale/payment/update') . '&payment_id=' . $this->request->get['payment_id'] . $url;
        }

        $this->data['cancel'] = Url::createAdminUrl('sale/payment') . $url;

        if (isset($this->request->get['payment_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
            $payment_info = $this->modelPayment->getById($this->request->get['payment_id']);
        }

        $this->setvar('name', $payment_info, '');
        $this->setvar('image', $payment_info, '');
        if (isset($payment_info) && $payment_info['image'] && file_exists(DIR_IMAGE . $payment_info['image'])) {
            $this->data['preview'] = NTImage::resizeAndSave($payment_info['image'], 100, 100);
        } else {
            $this->data['preview'] = NTImage::resizeAndSave('no_image.jpg', 100, 100);
        }

        $this->data['Url'] = new Url;

        $scripts[] = array('id' => 'paymentScripts', 'method' => 'ready', 'script' =>
            " $('#form').ntForm({
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
            };");

        $scripts[] = array('id' => 'paymentFunctions', 'method' => 'function', 'script' =>
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
            
            function image_delete(field, preview) {
                $('#' + field).val('');
                $('#' + preview).attr('src','" . HTTP_IMAGE . "cache/no_image-100x100.jpg');
            }
            
            function image_upload(field, preview) {
                var height = $(window).height() * 0.8;
                var width = $(window).width() * 0.8;
                
            	$('#dialog').remove();
            	$('.box').prepend('<div id=\"dialog\" style=\"padding: 3px 0px 0px 0px;z-index:10000;\"><iframe src=\"" . Url::createAdminUrl("common/filemanager") . "&field=' + encodeURIComponent(field) + '\" style=\"padding:0; margin: 0; display: block; width: 100%; height: 100%;z-index:10000;\" frameborder=\"no\" scrolling=\"auto\"></iframe></div>');
                
                $('#dialog').dialog({
            		title: '" . $this->data['text_image_manager'] . "',
            		close: function (event, ui) {
            			if ($('#' + field).attr('value')) {
            				$.ajax({
            					url: '" . Url::createAdminUrl("common/filemanager/image") . "',
            					type: 'POST',
            					data: 'image=' + encodeURIComponent($('#' + field).val()),
            					dataType: 'text',
            					success: function(data) {
            						$('#' + preview).replaceWith('<img src=\"' + data + '\" id=\"' + preview + '\" class=\"image\" onclick=\"image_upload(\'' + field + '\', \'' + preview + '\');\">');
            					}
            				});
            			}
            		},	
            		bgiframe: false,
            		width: width,
            		height: height,
            		resizable: false,
            		modal: false
            	});}");

        $this->scripts = array_merge($this->scripts, $scripts);

        $template = ($this->config->get('default_admin_view_sale_payment_form')) ? $this->config->get('default_admin_view_sale_payment_form') : 'sale/payment_form.tpl';
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
     * ControllerSalePayment::validateForm()
     * 
     * @return
     */
    private function validateForm() {
        if (!$this->user->hasPermission('modify', 'sale/payment')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (empty($this->request->post['name'])) {
            $this->error['name'] = $this->language->get('name');
        }

        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * ControllerSalePayment::validateDelete()
     * 
     * @return
     */
    private function validateDelete() {
        if (!$this->user->hasPermission('delete', 'sale/payment')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }

}
