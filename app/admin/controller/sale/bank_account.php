<?php

/**
 * ControllerSaleBank
 * 
 * @package NecoTienda
 * @author Yosiet Serga
 * @copyright Inversiones Necoyoad, C.A.
 * @version 1.0.0
 * @access public
 * @see Controller
 */
class ControllerSaleBankAccount extends Controller {

    private $error = [];

    /**
     * ControllerSaleBank::index()
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
     * ControllerSaleBank::insert()
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
            $bank_account_id = $this->modelBank_account->add($this->request->post);
            $this->session->set('success', $this->language->get('text_success'));
            if ($this->request->post['to'] == "saveAndKeep") {
                $this->redirect(Url::createAdminUrl('sale/bank_account/update', array('bank_account_id' => $bank_account_id)));
            } elseif ($this->request->post['to'] == "saveAndNew") {
                $this->redirect(Url::createAdminUrl('sale/bank_account/insert'));
            } else {
                $this->redirect(Url::createAdminUrl('sale/bank_account'));
            }
        }
        $this->getForm();
    }

    /**
     * ControllerSaleBank::update()
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
            $this->modelBank_account->update($this->request->get['bank_account_id'], $this->request->post);
            $this->session->set('success', $this->language->get('text_success'));
            if ($this->request->post['to'] == "saveAndKeep") {
                $this->redirect(Url::createAdminUrl('sale/bank_account/update', array('bank_account_id' => $this->request->get['bank_account_id'])));
            } elseif ($this->request->post['to'] == "saveAndNew") {
                $this->redirect(Url::createAdminUrl('store/categorysale/bank_account/insert'));
            } else {
                $this->redirect(Url::createAdminUrl('sale/bank_account'));
            }
        }
        $this->getForm();
    }

    /**
     * ControllerSaleBank::delete()
     * elimina un objeto
     * @return boolean
     * */
    public function delete() {
        $this->load->auto('sale/bank_account');
        if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
            foreach ($this->request->post['selected'] as $id) {
                $this->modelBank_account->delete($id);
            }
        } else {
            $this->modelBank_account->delete($_GET['id']);
        }
    }

    /**
     * ControllerMarketingNewsletter::copy()
     * duplicar un objeto
     * @return boolean
     */
    public function copy() {
        $this->load->auto('sale/bank_account');
        if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
            foreach ($this->request->post['selected'] as $id) {
                $this->modelBank_account->copy($id);
            }
        } else {
            $this->modelBank_account->copy($_GET['id']);
        }
        echo 1;
    }

    /**
     * ControllerSaleBank::getById()
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
            'href' => Url::createAdminUrl('sale/bank_account') . $url,
            'text' => $this->language->get('heading_title'),
            'separator' => ' :: '
        );

        $this->data['insert'] = Url::createAdminUrl('sale/bank_account/insert') . $url;
        $this->data['delete'] = Url::createAdminUrl('sale/bank_account/delete') . $url;

        $this->data['heading_title'] = $this->language->get('heading_title');

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
        $scripts[] = array('id' => 'bank_accountList', 'method' => 'function', 'script' =>
            "function activate(e) {    
            	$.ajax({
            	   'type':'get',
                   'dataType':'json',
                   'url':'" . Url::createAdminUrl("sale/bank_account/activate") . "&id=' + e,
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
                $.getJSON('" . Url::createAdminUrl("sale/bank_account/copy") . "&id=' + e, function(data) {
                    $('#gridWrapper').load('" . Url::createAdminUrl("sale/bank_account/grid") . "',function(response){
                        $('#gridPreloader').hide();
                        $('#gridWrapper').show();
                    });
                });
            }
            function eliminar(e) {
                if (confirm('\\xbfDesea eliminar este objeto?')) {
                    $('#tr_' + e).remove();
                	$.getJSON('" . Url::createAdminUrl("sale/bank_account/delete") . "',{
                        id:e
                    });
                }
                return false;
             }
            function copyAll() {
                $('#gridWrapper').hide();
                $('#gridPreloader').show();
                $.post('" . Url::createAdminUrl("sale/bank_account/copy") . "',$('#form').serialize(),function(){
                    $('#gridWrapper').load('" . Url::createAdminUrl("sale/bank_account/grid") . "',function(){
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
                    $.post('" . Url::createAdminUrl("sale/bank_account/delete") . "',$('#form').serialize(),function(){
                        $('#gridWrapper').load('" . Url::createAdminUrl("sale/bank_account/grid") . "',function(){
                            $('#gridWrapper').show();
                            $('#gridPreloader').hide();
                        });
                    });
                }
                return false;
            }");
        $scripts[] = array('id' => 'sortable', 'method' => 'ready', 'script' =>
            "$('#gridWrapper').load('" . Url::createAdminUrl("sale/bank_account/grid") . "',function(e){
                $('#gridPreloader').hide();
            });
                
            $('#formFilter').ntForm({
                lockButton:false,
                ajax:true,
                type:'get',
                dataType:'html',
                url:'" . Url::createAdminUrl("sale/bank_account/grid") . "',
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

        $template = ($this->config->get('default_admin_view_sale_bank_account_list')) ? $this->config->get('default_admin_view_sale_bank_account_list') : 'sale/bank_account_list.tpl';
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
     * ControllerSaleBank::grid()
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
        $filter_number = isset($this->request->get['filter_number']) ? $this->request->get['filter_number'] : null;
        $filter_bank = isset($this->request->get['filter_bank']) ? $this->request->get['filter_bank'] : null;
        $filter_date_start = isset($this->request->get['filter_date_start']) ? $this->request->get['filter_date_start'] : null;
        $filter_date_end = isset($this->request->get['filter_date_end']) ? $this->request->get['filter_date_end'] : null;
        $page = isset($this->request->get['page']) ? $this->request->get['page'] : 1;
        $sort = isset($this->request->get['sort']) ? $this->request->get['sort'] : 'name';
        $order = isset($this->request->get['order']) ? $this->request->get['order'] : 'ASC';
        $limit = !empty($this->request->get['limit']) ? $this->request->get['limit'] : $this->config->get('config_admin_limit');

        $url = '';

        if (isset($this->request->get['filter_number'])) {
            $url .= '&filter_number=' . $this->request->get['filter_number'];
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

        $this->data['bank_accounts'] = [];

        $data = array(
            'filter_number' => $filter_number,
            'filter_bank' => $filter_bank,
            'filter_date_start' => $filter_date_start,
            'filter_date_end' => $filter_date_end,
            'sort' => $sort,
            'order' => $order,
            'start' => ($page - 1) * $limit,
            'limit' => $limit
        );

        $bank_account_total = $this->modelBank_account->getAllTotal($data);
        $results = $this->modelBank_account->getAll($data);
        $i = str_replace('%theme%',$this->config->get('config_admin_template'),HTTP_ADMIN_THEME_IMAGE);
        foreach ($results as $result) {
            $action = array(
                'edit' => array(
                    'action' => 'edit',
                    'text' => $this->language->get('text_edit'),
                    'href' => Url::createAdminUrl('sale/bank_account/update') . '&bank_account_id=' . $result['bank_account_id'] . $url,
                    'img' =>  $i .'edit.png'
                ),
                'delete' => array(
                    'action' => 'delete',
                    'text' => $this->language->get('text_delete'),
                    'href' => '',
                    'img' => $i .'delete.png'
                )
            );

            $this->data['bank_accounts'][] = array(
                'bank_account_id' => $result['bank_account_id'],
                'bank_id' => $result['bank_id'],
                'bank' => $result['bank'],
                'number' => $result['number'],
                'date_added' => date('d-m-Y', strtotime($result['dateAdded'])),
                'selected' => isset($this->request->post['selected']) && in_array($result['bank_account_id'], $this->request->post['selected']),
                'action' => $action
            );
        }
        
        $pagination = new Pagination();
        $pagination->ajax = true;
        $pagination->ajaxTarget = "gridWrapper";
        $pagination->total = $bank_account_total;
        $pagination->page = $page;
        $pagination->limit = $limit;
        $pagination->text = $this->language->get('text_pagination');
        $pagination->url = Url::createAdminUrl('sale/bank_account/grid') . $url . '&page={page}';

        $this->data['pagination'] = $pagination->render();

        $template = ($this->config->get('default_admin_view_sale_bank_account_grid')) ? $this->config->get('default_admin_view_sale_bank_account_grid') : 'sale/bank_account_grid.tpl';
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_admin_template') . '/'. $template)) {
            $this->template = $this->config->get('config_admin_template') . '/' . $template;
        } else {
            $this->template = 'default/' . $template;
        }

        $this->response->setOutput($this->render(true), $this->config->get('config_compression'));
    }

    /**
     * ControllerSaleBank::getForm()
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
        $this->data['error_warning'] = ($this->error['warning']) ? $this->error['warning'] : '';
        $this->data['error_accountholder'] = ($this->error['accountholder']) ? $this->error['accountholder'] : '';
        $this->data['error_number'] = ($this->error['number']) ? $this->error['number'] : '';
        $this->data['error_email'] = ($this->error['email']) ? $this->error['email'] : '';
        $this->data['error_rif'] = ($this->error['rif']) ? $this->error['rif'] : '';
        $this->data['error_type'] = ($this->error['type']) ? $this->error['type'] : '';
        $this->data['error_bank'] = ($this->error['bank']) ? $this->error['bank'] : '';

        $this->document->breadcrumbs = [];
        $this->document->breadcrumbs[] = array(
            'href' => Url::createAdminUrl('common/home'),
            'text' => $this->language->get('text_home'),
            'separator' => false
        );
        $this->document->breadcrumbs[] = array(
            'href' => Url::createAdminUrl('sale/bank_account') . $url,
            'text' => $this->language->get('heading_title'),
            'separator' => ' :: '
        );

        if (!isset($this->request->get['bank_account_id'])) {
            $this->data['action'] = Url::createAdminUrl('sale/bank_account/insert') . $url;
        } else {
            $this->data['action'] = Url::createAdminUrl('sale/bank_account/update') . '&bank_account_id=' . $this->request->get['bank_account_id'] . $url;
        }

        $this->data['cancel'] = Url::createAdminUrl('sale/bank_account') . $url;

        if (isset($this->request->get['bank_account_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
            $bank_account_info = $this->modelBank_account->getById($this->request->get['bank_account_id']);
        }

        $this->data['stores'] = $this->modelStore->getAll();
        $this->data['_stores'] = $this->modelBank_account->getStores($this->request->get['bank_account_id']);

        $this->setvar('accountholder', $bank_account_info, '');
        $this->setvar('number', $bank_account_info, '');
        $this->setvar('email', $bank_account_info, '');
        $this->setvar('rif', $bank_account_info, '');
        $this->setvar('type', $bank_account_info, '');
        $this->setvar('bank_id', $bank_account_info, '');

        $this->data['banks'] = $this->modelBank->getAll();

        $template = ($this->config->get('default_admin_view_sale_bank_account_form')) ? $this->config->get('default_admin_view_sale_bank_account_form') : 'sale/bank_account_form.tpl';
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
     * ControllerSaleBank::validateForm()
     * 
     * @return
     */
    private function validateForm() {
        if (!$this->user->hasPermission('modify', 'sale/bank_account')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (empty($this->request->post['accountholder'])) {
            $this->error['accountholder'] = $this->language->get('error_accountholder');
        }

        if (empty($this->request->post['number'])) {
            $this->error['number'] = $this->language->get('error_number');
        }

        if (empty($this->request->post['email'])) {
            $this->error['email'] = $this->language->get('error_email');
        }

        if (empty($this->request->post['rif'])) {
            $this->error['rif'] = $this->language->get('error_rif');
        }

        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * ControllerSaleBank::validateDelete()
     * 
     * @return
     */
    private function validateDelete() {
        if (!$this->user->hasPermission('delete', 'sale/bank_account')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }

}
