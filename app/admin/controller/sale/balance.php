<?php

/**
 * ControllerSaleBalance
 * 
 * @package NecoTienda
 * @author Yosiet Serga
 * @copyright Inversiones Necoyoad, C.A.
 * @version 1.0.0
 * @access public
 * @see Controller
 */
class ControllerSaleBalance extends Controller {

    private $error = [];

    /**
     * ControllerSaleBalance::index()
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
     * ControllerSaleBalance::insert()
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
            $balance_id = $this->modelBalance->add($this->request->post);
            $this->session->set('success', $this->language->get('text_success'));
            if ($this->request->post['to'] == "saveAndKeep") {
                $this->redirect(Url::createAdminUrl('sale/balance/update', array('balance_id' => $balance_id)));
            } elseif ($this->request->post['to'] == "saveAndNew") {
                $this->redirect(Url::createAdminUrl('sale/balance/insert'));
            } else {
                $this->redirect(Url::createAdminUrl('sale/balance'));
            }
        }
        $this->getForm();
    }

    /**
     * ControllerSaleBalance::update()
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
            $this->modelBalance->update($this->request->get['balance_id'], $this->request->post);
            $this->session->set('success', $this->language->get('text_success'));
            if ($this->request->post['to'] == "saveAndKeep") {
                $this->redirect(Url::createAdminUrl('sale/balance/update', array('balance_id' => $this->request->get['balance_id'])));
            } elseif ($this->request->post['to'] == "saveAndNew") {
                $this->redirect(Url::createAdminUrl('sale/balance/insert'));
            } else {
                $this->redirect(Url::createAdminUrl('sale/balance'));
            }
        }
        $this->getForm();
    }

    /**
     * ControllerSaleBalance::delete()
     * elimina un objeto
     * @return boolean
     * */
    public function delete() {
        $this->load->auto('sale/balance');
        if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
            foreach ($this->request->post['selected'] as $id) {
                $this->modelBalance->delete($id);
            }
        } else {
            $this->modelBalance->delete($_GET['id']);
        }
    }

    /**
     * ControllerMarketingNewsletter::copy()
     * duplicar un objeto
     * @return boolean
     */
    public function copy() {
        $this->load->auto('sale/balance');
        if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
            foreach ($this->request->post['selected'] as $id) {
                $this->modelBalance->copy($id);
            }
        } else {
            $this->modelBalance->copy($_GET['id']);
        }
        echo 1;
    }

    /**
     * ControllerSaleBalance::getById()
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
            'href' => Url::createAdminUrl('sale/balance') . $url,
            'text' => $this->language->get('heading_title'),
            'separator' => ' :: '
        );

        $this->data['insert'] = Url::createAdminUrl('sale/balance/insert') . $url;

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

        // SCRIPTS
        $scripts[] = array('id' => 'balanceList', 'method' => 'function', 'script' =>
            "function activate(e) {    
            	$.ajax({
            	   'type':'get',
                   'dataType':'json',
                   'url':'" . Url::createAdminUrl("sale/balance/activate") . "&id=' + e,
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
                $.getJSON('" . Url::createAdminUrl("sale/balance/copy") . "&id=' + e, function(data) {
                    $('#gridWrapper').load('" . Url::createAdminUrl("sale/balance/grid") . "',function(response){
                        $('#gridPreloader').hide();
                        $('#gridWrapper').show();
                    });
                });
            }
            function eliminar(e) {
                if (confirm('\\xbfDesea eliminar este objeto?')) {
                    $('#tr_' + e).remove();
                	$.getJSON('" . Url::createAdminUrl("sale/balance/delete") . "',{
                        id:e
                    });
                }
                return false;
             }
            function copyAll() {
                $('#gridWrapper').hide();
                $('#gridPreloader').show();
                $.post('" . Url::createAdminUrl("sale/balance/copy") . "',$('#form').serialize(),function(){
                    $('#gridWrapper').load('" . Url::createAdminUrl("sale/balance/grid") . "',function(){
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
                    $.post('" . Url::createAdminUrl("sale/balance/delete") . "',$('#form').serialize(),function(){
                        $('#gridWrapper').load('" . Url::createAdminUrl("sale/balance/grid") . "',function(){
                            $('#gridWrapper').show();
                            $('#gridPreloader').hide();
                        });
                    });
                }
                return false;
            }");
        $scripts[] = array('id' => 'sortable', 'method' => 'ready', 'script' =>
            "$('#gridWrapper').load('" . Url::createAdminUrl("sale/balance/grid") . "',function(e){
                $('#gridPreloader').hide();
            });
                
            $('#formFilter').ntForm({
                lockButton:false,
                ajax:true,
                type:'get',
                dataType:'html',
                url:'" . Url::createAdminUrl("sale/balance/grid") . "',
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

        $template = ($this->config->get('default_admin_view_sale_balance_list')) ? $this->config->get('default_admin_view_sale_balance_list') : 'sale/balance_list.tpl';
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
     * ControllerSaleBalance::grid()
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
        $filter_balance_id = ($this->request->hasQuery('filter_balance_id')) ? $this->request->get['filter_balance_id'] : null;
        $filter_customer = ($this->request->hasQuery('filter_customer')) ? $this->request->get['filter_customer'] : null;
        $filter_type = ($this->request->hasQuery('filter_type')) ? $this->request->get['filter_type'] : null;
        $filter_amount_start = ($this->request->hasQuery('filter_amount_start')) ? $this->request->get['filter_amount_start'] : null;
        $filter_amount_end = ($this->request->hasQuery('filter_amount_end')) ? $this->request->get['filter_amount_end'] : null;
        $filter_date_start = ($this->request->hasQuery('filter_date_start')) ? $this->request->get['filter_date_start'] : null;
        $filter_date_end = ($this->request->hasQuery('filter_date_end')) ? $this->request->get['filter_date_end'] : null;
        $page = ($this->request->hasQuery('page')) ? $this->request->get['page'] : 1;
        $sort = ($this->request->hasQuery('sort')) ? $this->request->get['sort'] : 'b.date_added';
        $order = ($this->request->hasQuery('order')) ? $this->request->get['order'] : 'DESC';
        $limit = !empty($this->request->get['limit']) ? $this->request->get['limit'] : $this->config->get('config_admin_limit');

        $url = '';

        if ($this->request->hasQuery('filter_balance_id')) {
            $url .= '&filter_balance_id=' . $this->request->get['filter_balance_id'];
        }
        if ($this->request->hasQuery('filter_customer')) {
            $url .= '&filter_customer=' . $this->request->get['filter_customer'];
        }
        if ($this->request->hasQuery('filter_type')) {
            $url .= '&filter_type=' . $this->request->get['filter_type'];
        }
        if ($this->request->hasQuery('filter_amount_start')) {
            $url .= '&filter_amount_start=' . $this->request->get['filter_amount_start'];
        }
        if ($this->request->hasQuery('filter_amount_end')) {
            $url .= '&filter_amount_end=' . $this->request->get['filter_amount_end'];
        }
        if ($this->request->hasQuery('filter_date_start')) {
            $url .= '&filter_date_start=' . $this->request->get['filter_date_start'];
        }
        if ($this->request->hasQuery('filter_date_end')) {
            $url .= '&filter_date_end=' . $this->request->get['filter_date_end'];
        }
        if ($this->request->hasQuery('page')) {
            $url .= '&page=' . $this->request->get['page'];
        }
        if ($this->request->hasQuery('sort')) {
            $url .= '&sort=' . $this->request->get['sort'];
        }
        if ($this->request->hasQuery('order')) {
            $url .= '&order=' . $this->request->get['order'];
        }
        if (!empty($this->request->get['limit'])) {
            $url .= '&limit=' . $this->request->get['limit'];
        }

        $this->data['balances'] = [];

        $data = array(
            'filter_balance_id' => $filter_balance_id,
            'filter_customer' => $filter_customer,
            'filter_type' => $filter_type,
            'filter_amount_start' => $filter_amount_start,
            'filter_amount_end' => $filter_amount_end,
            'filter_date_start' => $filter_date_start,
            'filter_date_end' => $filter_date_end,
            'sort' => $sort,
            'order' => $order,
            'start' => ($page - 1) * $limit,
            'limit' => $limit
        );

        $balance_total = $this->modelBalance->getAllTotal($data);
        $results = $this->modelBalance->getAll($data);

        foreach ($results as $result) {
            $this->data['balances'][] = array(
                'balance_id' => $result['balance_id'],
                'customer_id' => $result['customer_id'],
                'type' => $result['type'],
                'customer' => $this->modelCustomer->getById($result['customer_id']),
                'description' => strip_tags($result['description']),
                'amount' => $this->currency->format($result['amount']),
                'amount_available' => $this->currency->format($result['amount_available']),
                'amount_blocked' => $this->currency->format($result['amount_blocked']),
                'amount_total' => $this->currency->format($result['amount_total']),
                'date_added' => date('d-m-Y h:i A', strtotime($result['dateAdded'])),
                'selected' => isset($this->request->post['selected']) && in_array($result['order_balance_id'], $this->request->post['selected']),
                'action' => $action
            );
        }

        $url = '';

        if ($this->request->hasQuery('filter_balance_id')) {
            $url .= '&filter_balance_id=' . $this->request->get['filter_balance_id'];
        }
        if ($this->request->hasQuery('filter_customer')) {
            $url .= '&filter_customer=' . $this->request->get['filter_customer'];
        }
        if ($this->request->hasQuery('filter_type')) {
            $url .= '&filter_type=' . $this->request->get['filter_type'];
        }
        if ($this->request->hasQuery('filter_amount_start')) {
            $url .= '&filter_amount_start=' . $this->request->get['filter_amount_start'];
        }
        if ($this->request->hasQuery('filter_amount_end')) {
            $url .= '&filter_amount_end=' . $this->request->get['filter_amount_end'];
        }
        if ($this->request->hasQuery('filter_date_start')) {
            $url .= '&filter_date_start=' . $this->request->get['filter_date_start'];
        }
        if ($this->request->hasQuery('filter_date_end')) {
            $url .= '&filter_date_end=' . $this->request->get['filter_date_end'];
        }
        if ($this->request->hasQuery('page')) {
            $url .= '&page=' . $this->request->get['page'];
        }
        if (!empty($this->request->get['limit'])) {
            $url .= '&limit=' . $this->request->get['limit'];
        }

        if ($order == 'ASC') {
            $url .= '&order=DESC';
        } else {
            $url .= '&order=ASC';
        }

        $this->data['sort_balance_id'] = Url::createAdminUrl('sale/balance/grid') . '&sort=b.order_balance_id' . $url;
        $this->data['sort_type'] = Url::createAdminUrl('sale/balance/grid') . '&sort=b.type' . $url;
        $this->data['sort_customer'] = Url::createAdminUrl('sale/balance/grid') . '&sort=customer' . $url;
        $this->data['sort_amount'] = Url::createAdminUrl('sale/balance/grid') . '&sort=b.amount' . $url;
        $this->data['sort_amount_available'] = Url::createAdminUrl('sale/balance/grid') . '&sort=b.amount_available' . $url;
        $this->data['sort_amount_blocked'] = Url::createAdminUrl('sale/balance/grid') . '&sort=b.amount_blocked' . $url;
        $this->data['sort_amount_total'] = Url::createAdminUrl('sale/balance/grid') . '&sort=b.amount_total' . $url;
        $this->data['sort_date_added'] = Url::createAdminUrl('sale/balance/grid') . '&sort=b.date_added' . $url;

        $url = '';

        if ($this->request->hasQuery('filter_balance_id')) {
            $url .= '&filter_balance_id=' . $this->request->get['filter_balance_id'];
        }
        if ($this->request->hasQuery('filter_customer')) {
            $url .= '&filter_customer=' . $this->request->get['filter_customer'];
        }
        if ($this->request->hasQuery('filter_type')) {
            $url .= '&filter_type=' . $this->request->get['filter_type'];
        }
        if ($this->request->hasQuery('filter_amount_start')) {
            $url .= '&filter_amount_start=' . $this->request->get['filter_amount_start'];
        }
        if ($this->request->hasQuery('filter_amount_end')) {
            $url .= '&filter_amount_end=' . $this->request->get['filter_amount_end'];
        }
        if ($this->request->hasQuery('filter_date_start')) {
            $url .= '&filter_date_start=' . $this->request->get['filter_date_start'];
        }
        if ($this->request->hasQuery('filter_date_end')) {
            $url .= '&filter_date_end=' . $this->request->get['filter_date_end'];
        }
        if ($this->request->hasQuery('sort')) {
            $url .= '&sort=' . $this->request->get['sort'];
        }
        if ($this->request->hasQuery('order')) {
            $url .= '&order=' . $this->request->get['order'];
        }

        $pagination = new Pagination();
        $pagination->ajax = true;
        $pagination->ajaxTarget = "gridWrapper";
        $pagination->total = $balance_total;
        $pagination->page = $page;
        $pagination->limit = $limit;
        $pagination->text = $this->language->get('text_pagination');
        $pagination->url = Url::createAdminUrl('sale/balance/grid') . $url . '&page={page}';

        $this->data['pagination'] = $pagination->render();

        $this->data['filter_date_start'] = $filter_date_start;
        $this->data['filter_date_end'] = $filter_date_end;

        $this->data['sort'] = $sort;
        $this->data['order'] = $order;

        $template = ($this->config->get('default_admin_view_sale_balance_grid')) ? $this->config->get('default_admin_view_sale_balance_grid') : 'sale/balance_grid.tpl';
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_admin_template') . '/'. $template)) {
            $this->template = $this->config->get('config_admin_template') . '/' . $template;
        } else {
            $this->template = 'default/' . $template;
        }

        $this->response->setOutput($this->render(true), $this->config->get('config_compression'));
    }

    /**
     * ControllerSaleBalance::getForm()
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
            'href' => Url::createAdminUrl('sale/balance') . $url,
            'text' => $this->language->get('heading_title'),
            'separator' => ' :: '
        );

        if (!$this->request->hasQuery('balance_id')) {
            $this->data['action'] = Url::createAdminUrl('sale/balance/insert') . $url;
        } else {
            $this->data['action'] = Url::createAdminUrl('sale/balance/update') . '&balance_id=' . $this->request->get['balance_id'] . $url;
        }

        $this->data['cancel'] = Url::createAdminUrl('sale/balance') . $url;

        if ($this->request->hasQuery('balance_id') && $this->request->server['REQUEST_METHOD'] != 'POST') {
            $balance_info = $this->modelBalance->getById($this->request->get['balance_id']);
        }

        $this->setvar('name', $balance_info, '');
        $this->setvar('image', $balance_info, '');
        if (isset($balance_info) && $balance_info['image'] && file_exists(DIR_IMAGE . $balance_info['image'])) {
            $this->data['preview'] = NTImage::resizeAndSave($balance_info['image'], 100, 100);
        } else {
            $this->data['preview'] = NTImage::resizeAndSave('no_image.jpg', 100, 100);
        }

        $this->data['Url'] = new Url;

        $scripts[] = array('id' => 'balanceScripts', 'method' => 'ready', 'script' =>
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

        $scripts[] = array('id' => 'balanceFunctions', 'method' => 'function', 'script' =>
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

        $template = ($this->config->get('default_admin_view_sale_balance_form')) ? $this->config->get('default_admin_view_sale_balance_form') : 'sale/balance_form.tpl';
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
     * ControllerSaleBalance::validateForm()
     * 
     * @return
     */
    private function validateForm() {
        if (!$this->user->hasPermission('modify', 'sale/balance')) {
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
     * ControllerSaleBalance::validateDelete()
     * 
     * @return
     */
    private function validateDelete() {
        if (!$this->user->hasPermission('delete', 'sale/balance')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }

}
