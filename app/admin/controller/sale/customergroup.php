<?php

/**
 * ControllerSaleCustomerGroup
 * 
 * @package NecoTienda
 * @author Yosiet Serga
 * @copyright Inversiones Necoyoad, C.A.
 * @version 1.0.0
 * @access public
 * @see Controller
 */
class ControllerSaleCustomerGroup extends Controller {

    private $error = [];

    /**
     * ControllerSaleCustomerGroup::index()
     * 
     * @see Load
     * @see Language
     * @see getList
     * @return void
     */
    public function index() {
        $this->document->title = $this->language->get('heading_title');
        $this->getList();
    }

    /**
     * ControllerSaleCustomerGroup::insert()
     * 
     * @see Load
     * @see Language
     * @see Redirect
     * @see Session
     * @see getForm
     * @return void
     */
    public function insert() {
        $this->document->title = $this->language->get('heading_title');
        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $this->request->post['params'] = serialize($this->request->post['Params']);
            $customer_group_id = $this->modelCustomergroup->add($this->request->post);

            $this->session->set('success', $this->language->get('text_success'));

            if ($_POST['to'] == "saveAndKeep") {
                $this->redirect(Url::createAdminUrl('sale/customergroup/update', array('customer_group_id' => $customer_group_id)));
            } elseif ($_POST['to'] == "saveAndNew") {
                $this->redirect(Url::createAdminUrl('sale/customergroup/insert'));
            } else {
                $this->redirect(Url::createAdminUrl('sale/customergroup'));
            }
        }

        $this->getForm();
    }

    /**
     * ControllerSaleCustomerGroup::update()
     * 
     * @see Load
     * @see Language
     * @see Redirect
     * @see Session
     * @see getForm
     * @return void
     */
    public function update() {
        $this->document->title = $this->language->get('heading_title');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $this->request->post['params'] = serialize($this->request->post['Params']);
            $this->modelCustomergroup->update($this->request->get['customer_group_id'], $this->request->post);

            $this->session->set('success', $this->language->get('text_success'));

            if ($_POST['to'] == "saveAndKeep") {
                $this->redirect(Url::createAdminUrl('sale/customergroup/update', array('customer_group_id' => $this->request->get['customer_group_id'])));
            } elseif ($_POST['to'] == "saveAndNew") {
                $this->redirect(Url::createAdminUrl('sale/customergroup/insert'));
            } else {
                $this->redirect(Url::createAdminUrl('sale/customergroup'));
            }
        }

        $this->getForm();
    }

    /**
     * ControllerSaleCustomerGroup::getById()
     * 
     * @see Load
     * @see Language
     * @see Response
     * @see Session
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
            'href' => Url::createAdminUrl('sale/customergroup') . $url,
            'text' => $this->language->get('heading_title'),
            'separator' => ' :: '
        );

        $this->data['insert'] = Url::createAdminUrl('sale/customergroup/insert') . $url;
        $this->data['delete'] = Url::createAdminUrl('sale/customergroup/delete') . $url;

        $this->data['heading_title'] = $this->language->get('heading_title');

        $this->data['error_warning'] = ($this->error['warning']) ? $this->error['warning'] : '';

        if ($this->session->has('success')) {
            $this->data['success'] = $this->session->get('success');
            $this->session->clear('success');
        } else {
            $this->data['success'] = '';
        }

        // SCRIPTS
        $scripts[] = array('id' => 'customergroupList', 'method' => 'function', 'script' =>
            "function activate(e) {
                $.getJSON('" . Url::createAdminUrl("sale/customergroup/activate") . "',{
                    id:e
                },function(data){
                    if (data > 0) {
                        $('#img_' + e).attr('src','image/good.png');
                    } else {
                        $('#img_' + e).attr('src','image/minus.png');
                    }
                });
            }
            
            function eliminar(e) {
                if (confirm('\\xbfDesea eliminar este objeto?')) {
                    if (e != '" . $this->config->get('config_customer_group_id') . "') {
                        $('#tr_' + e).remove();
                    	$.getJSON('" . Url::createAdminUrl("sale/customergroup/delete") . "',{
                            id:e
                        });
                    } else {
                        alert('No puede eliminar el grupo de clientes predeterminado.');
                    }
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
                $.post('" . Url::createAdminUrl("sale/customergroup/copy") . "',$('#form').serialize(),function(){
                    $('#gridWrapper').load('" . Url::createAdminUrl("sale/customergroup/grid") . "',function(){
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
                    $.post('" . Url::createAdminUrl("sale/customergroup/delete") . "',$('#form').serialize(),function(){
                        $('#gridWrapper').load('" . Url::createAdminUrl("sale/customergroup/grid") . "',function(){
                            $('#gridWrapper').show();
                            $('#gridPreloader').hide();
                        });
                    });
                }
                return false;
            }");

        $scripts[] = array('id' => 'sortable', 'method' => 'ready', 'script' =>
            "$('#gridWrapper').load('" . Url::createAdminUrl("sale/customergroup/grid") . "',function(e){
                $('#gridPreloader').hide();
                $('#list tbody').sortable({
                    opacity: 0.6, 
                    cursor: 'move',
                    handle: '.move',
                    update: function() {
                        $.ajax({
                            'type':'post',
                            'dateType':'json',
                            'url':'" . Url::createAdminUrl("sale/customergroup/sortable") . "',
                            'data': $(this).sortable('serialize'),
                            'success': function(data) {
                                if (data > 0) {
                                    var msj = '<div class=\"messagesuccess\">Se han ordenado los objetos correctamente</div>';
                                } else {
                                    var msj = '<div class=\"messagewarning\">Hubo un error al intentar ordenar los objetos, por favor intente m&aacute;s tarde</div>';
                                }
                                $('#msg').fadeIn().append(msj).delay(3600).fadeOut();
                            }
                        });
                    }
                }).disableSelection();
                $('.move').css('cursor','move');
            });
                
            $('#formFilter').ntForm({
                lockButton:false,
                ajax:true,
                type:'get',
                dataType:'html',
                url:'" . Url::createAdminUrl("sale/customergroup/grid") . "',
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

        $template = ($this->config->get('default_admin_view_sale_customer_group_list')) ? $this->config->get('default_admin_view_sale_customer_group_list') : 'sale/customer_group_list.tpl';
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
     * ControllerSaleCustomerGroup::grid()
     * 
     * @see Load
     * @see Language
     * @see Response
     * @see Session
     * @see Request
     * @return void
     */
    public function grid() {
        $filter_name = isset($this->request->get['filter_name']) ? $this->request->get['filter_name'] : null;
        $filter_customer = isset($this->request->get['filter_customer']) ? $this->request->get['filter_customer'] : null;
        $filter_date_start = isset($this->request->get['filter_date_start']) ? $this->request->get['filter_date_start'] : null;
        $filter_date_end = isset($this->request->get['filter_date_end']) ? $this->request->get['filter_date_end'] : null;
        $page = isset($this->request->get['page']) ? $this->request->get['page'] : 1;
        $sort = isset($this->request->get['sort']) ? $this->request->get['sort'] : 'name';
        $order = isset($this->request->get['order']) ? $this->request->get['order'] : 'ASC';
        $limit = !empty($this->request->get['limit']) ? $this->request->get['limit'] : $this->config->get('config_admin_limit');

        $url = '';

        if (isset($this->request->get['filter_name'])) {
            $url .= '&filter_name=' . $this->request->get['filter_name'];
        }
        if (isset($this->request->get['filter_customer'])) {
            $url .= '&filter_customer=' . $this->request->get['filter_customer'];
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

        $this->data['customer_groups'] = [];

        $data = array(
            'filter_name' => $filter_name,
            'filter_customer' => $filter_customer,
            'filter_date_start' => $filter_date_start,
            'filter_date_end' => $filter_date_end,
            'sort' => $sort,
            'order' => $order,
            'start' => ($page - 1) * $limit,
            'limit' => $limit
        );

        $customer_group_total = $this->modelCustomergroup->getAllTotal();

        $results = $this->modelCustomergroup->getAll($data);

        $i = str_replace('%theme%',$this->config->get('config_admin_template'),HTTP_ADMIN_THEME_IMAGE);
        foreach ($results as $result) {

            $action = array(
                'activate' => array(
                    'action' => 'activate',
                    'text' => $this->language->get('text_activate'),
                    'href' => '',
                    'img' => ($result['status'] == 1) ? $i .'good.png' : $i .'minus.png'
                ),
                'edit' => array(
                    'action' => 'edit',
                    'text' => $this->language->get('text_edit'),
                    'href' => Url::createAdminUrl('sale/customergroup/update') . '&customer_group_id=' . $result['customer_group_id'] . $url,
                    'img' =>  $i .'edit.png'
                ),
                'delete' => array(
                    'action' => 'delete',
                    'text' => $this->language->get('text_delete'),
                    'href' => '',
                    'img' => $i .'delete.png'
                )
            );

            $customers = $this->modelCustomer->getAllTotal(array('customer_group_id'=>$result['customer_group_id']));
            $this->data['customer_groups'][] = array(
                'customer_group_id' => $result['customer_group_id'],
                'params' => unserialize($result['params']),
                'qty_customers' => (int) $customers,
                'name' => $result['name'] . (($result['customer_group_id'] == $this->config->get('config_customer_group_id')) ? $this->language->get('text_default') : null),
                'selected' => isset($this->request->post['selected']) && in_array($result['customer_group_id'], $this->request->post['selected']),
                'action' => $action
            );
        }

        $url = '';

        if ($order == 'ASC') {
            $url .= '&order=DESC';
        } else {
            $url .= '&order=ASC';
        }

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }
        $this->data['sort_name'] = Url::createAdminUrl('sale/customergroup/grid') . '&sort=name' . $url;

        $url = '';

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }
        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        $pagination = new Pagination();
        $pagination->ajax = true;
        $pagination->ajaxTarget = "gridWrapper";
        $pagination->total = $customer_group_total;
        $pagination->page = $page;
        $pagination->limit = $limit;
        $pagination->text = $this->language->get('text_pagination');
        $pagination->url = Url::createAdminUrl('sale/customergroup/grid') . $url . '&page={page}';

        $this->data['pagination'] = $pagination->render();

        $this->data['sort'] = $sort;
        $this->data['order'] = $order;

        $template = ($this->config->get('default_admin_view_sale_customer_group_grid')) ? $this->config->get('default_admin_view_sale_customer_group_grid') : 'sale/customer_group_grid.tpl';
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_admin_template') . '/'. $template)) {
            $this->template = $this->config->get('config_admin_template') . '/' . $template;
        } else {
            $this->template = 'default/' . $template;
        }

        $this->response->setOutput($this->render(true), $this->config->get('config_compression'));
    }

    /**
     * ControllerSaleCustomerGroup::getForm()
     * 
     * @see Load
     * @see Language
     * @see Response
     * @see Session
     * @see Request
     * @return void
     */
    private function getForm() {
        $this->data['heading_title'] = $this->language->get('heading_title');

        $this->data['error_warning'] = ($this->error['warning']) ? $this->error['warning'] : '';
        $this->data['error_name'] = ($this->error['name']) ? $this->error['name'] : '';

        $this->document->breadcrumbs = [];

        $this->document->breadcrumbs[] = array(
            'href' => Url::createAdminUrl('common/home'),
            'text' => $this->language->get('text_home'),
            'separator' => false
        );

        $this->document->breadcrumbs[] = array(
            'href' => Url::createAdminUrl('sale/customergroup') . $url,
            'text' => $this->language->get('heading_title'),
            'separator' => ' :: '
        );

        if (!isset($this->request->get['customer_group_id'])) {
            $this->data['action'] = Url::createAdminUrl('sale/customergroup/insert') . $url;
        } else {
            $this->data['action'] = Url::createAdminUrl('sale/customergroup/update') . '&customer_group_id=' . $this->request->get['customer_group_id'] . $url;
        }

        $this->data['cancel'] = Url::createAdminUrl('sale/customergroup') . $url;

        if (isset($this->request->get['customer_group_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
            $customer_group_info = $this->modelCustomergroup->getById($this->request->get['customer_group_id']);
        }

        $this->setvar('name', $customer_group_info, '');
        $this->setvar('params', $customer_group_info, array());

        if (!is_array($this->data['params'])) {
            $this->data['params'] = unserialize($this->data['params']);
        }

        $this->data['Url'] = new Url;

        $scripts[] = array('id' => 'form', 'method' => 'ready', 'script' =>
            "$('#q').on('change',function(e){
                var that = this;
                var valor = $(that).val().toLowerCase();
                if (valor.length <= 0) {
                    $('#customersWrapper li').show();
                } else {
                    $('#customersWrapper li b').each(function(){
                        if ($(this).text().toLowerCase().indexOf( valor ) != -1) {
                            $(this).closest('li').show();
                        } else {
                            $(this).closest('li').hide();
                        }
                    });
                }
            }); 
                            
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
            };");

        $scripts[] = array('id' => 'Functions', 'method' => 'function', 'script' =>
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
            }");

        $this->scripts = array_merge($this->scripts, $scripts);

        $template = ($this->config->get('default_admin_view_sale_customer_group_form')) ? $this->config->get('default_admin_view_sale_customer_group_form') : 'sale/customer_group_form.tpl';
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
     * ControllerSaleCustomerGroup::validateForm()
     * 
     * @see User
     * @see Language
     * @see Request
     * @return bool
     */
    private function validateForm() {
        if (!$this->user->hasPermission('modify', 'sale/customergroup')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if ((strlen(utf8_decode($this->request->post['name'])) < 3) || (strlen(utf8_decode($this->request->post['name'])) > 64)) {
            $this->error['name'] = $this->language->get('error_name');
        }

        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * ControllerSaleCustomerGroup::validateDelete()
     * 
     * @see User
     * @see Language
     * @see Request
     * @return bool
     */
    private function validateDelete() {
        if (!$this->user->hasPermission('modify', 'sale/customer_group')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        $this->load->auto('sale/customer');

        foreach ($this->request->post['selected'] as $customer_group_id) {
            if ($this->config->get('config_customer_group_id') == $customer_group_id) {
                $this->error['warning'] = $this->language->get('error_default');
            }

            $customer_total = $this->modelCustomer->getAllTotalByCustomerGroupId($customer_group_id);

            if ($customer_total) {
                $this->error['warning'] = sprintf($this->language->get('error_customer'), $customer_total);
            }
        }

        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * ControllerSaleCategory::activate()
     * activar o desactivar un objeto accedido por ajax
     * @return boolean
     * */
    public function activate() {
        if (!isset($_GET['id']))
            return false;
        $this->load->auto('sale/customergroup');
        $status = $this->modelCustomergroup->getById($_GET['id']);
        if ($status) {
            if ($status['status'] == 0) {
                $this->modelCustomergroup->activate($_GET['id']);
                echo 1;
            } else {
                $this->modelCustomergroup->deactivate($_GET['id']);
                echo -1;
            }
        } else {
            echo 0;
        }
    }

    /**
     * ControllerMarketingNewsletter::copy()
     * duplicar un objeto
     * @return boolean
     */
    public function copy() {
        $this->load->auto('sale/customergroup');
        if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
            foreach ($this->request->post['selected'] as $id) {
                $this->modelCustomergroup->copy($id);
            }
        } else {
            $this->modelCustomergroup->copy($_GET['id']);
        }
        echo 1;
    }

    /**
     * ControllerMarketingNewsletter::delete()
     * elimina un objeto
     * @return boolean
     * */
    public function delete() {
        $this->load->auto('sale/customergroup');
        if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
            foreach ($this->request->post['selected'] as $id) {
                if ($id == $this->config->get('config_customer_group_id'))
                    return false;
                $this->modelCustomergroup->delete($id);
            }
        } else {
            if ($_GET['id'] == $this->config->get('config_customer_group_id'))
                return false;
            $this->modelCustomergroup->delete($_GET['id']);
        }
    }

}
