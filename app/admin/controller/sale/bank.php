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
class ControllerSaleBank extends Controller {

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
            $bank_id = $this->modelBank->add($this->request->post);
            $this->session->set('success', $this->language->get('text_success'));
            if ($this->request->post['to'] == "saveAndKeep") {
                $this->redirect(Url::createAdminUrl('sale/bank/update', array('bank_id' => $bank_id)));
            } elseif ($this->request->post['to'] == "saveAndNew") {
                $this->redirect(Url::createAdminUrl('sale/bank/insert'));
            } else {
                $this->redirect(Url::createAdminUrl('sale/bank'));
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
            $this->modelBank->update($this->request->get['bank_id'], $this->request->post);
            $this->session->set('success', $this->language->get('text_success'));
            if ($this->request->post['to'] == "saveAndKeep") {
                $this->redirect(Url::createAdminUrl('sale/bank/update', array('bank_id' => $this->request->get['bank_id'])));
            } elseif ($this->request->post['to'] == "saveAndNew") {
                $this->redirect(Url::createAdminUrl('sale/bank/insert'));
            } else {
                $this->redirect(Url::createAdminUrl('sale/bank'));
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
        $this->load->auto('sale/bank');
        if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
            foreach ($this->request->post['selected'] as $id) {
                $this->modelBank->delete($id);
            }
        } else {
            $this->modelBank->delete($_GET['id']);
        }
    }

    /**
     * ControllerMarketingNewsletter::copy()
     * duplicar un objeto
     * @return boolean
     */
    public function copy() {
        $this->load->auto('sale/bank');
        if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
            foreach ($this->request->post['selected'] as $id) {
                $this->modelBank->copy($id);
            }
        } else {
            $this->modelBank->copy($_GET['id']);
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
            'href' => Url::createAdminUrl('sale/bank') . $url,
            'text' => $this->language->get('heading_title'),
            'separator' => ' :: '
        );

        $this->data['insert'] = Url::createAdminUrl('sale/bank/insert') . $url;
        $this->data['delete'] = Url::createAdminUrl('sale/bank/delete') . $url;

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
        $scripts[] = array('id' => 'bankList', 'method' => 'function', 'script' =>
            "function activate(e) {    
            	$.ajax({
            	   'type':'get',
                   'dataType':'json',
                   'url':'" . Url::createAdminUrl("sale/bank/activate") . "&id=' + e,
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
                $.getJSON('" . Url::createAdminUrl("sale/bank/copy") . "&id=' + e, function(data) {
                    $('#gridWrapper').load('" . Url::createAdminUrl("sale/bank/grid") . "',function(response){
                        $('#gridPreloader').hide();
                        $('#gridWrapper').show();
                    });
                });
            }
            function eliminar(e) {
                if (confirm('\\xbfDesea eliminar este objeto?')) {
                    $('#tr_' + e).remove();
                	$.getJSON('" . Url::createAdminUrl("sale/bank/delete") . "',{
                        id:e
                    });
                }
                return false;
             }
            function copyAll() {
                $('#gridWrapper').hide();
                $('#gridPreloader').show();
                $.post('" . Url::createAdminUrl("sale/bank/copy") . "',$('#form').serialize(),function(){
                    $('#gridWrapper').load('" . Url::createAdminUrl("sale/bank/grid") . "',function(){
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
                    $.post('" . Url::createAdminUrl("sale/bank/delete") . "',$('#form').serialize(),function(){
                        $('#gridWrapper').load('" . Url::createAdminUrl("sale/bank/grid") . "',function(){
                            $('#gridWrapper').show();
                            $('#gridPreloader').hide();
                        });
                    });
                }
                return false;
            }");
        $scripts[] = array('id' => 'sortable', 'method' => 'ready', 'script' =>
            "$('#gridWrapper').load('" . Url::createAdminUrl("sale/bank/grid") . "',function(e){
                $('#gridPreloader').hide();
            });
                
            $('#formFilter').ntForm({
                lockButton:false,
                ajax:true,
                type:'get',
                dataType:'html',
                url:'" . Url::createAdminUrl("sale/bank/grid") . "',
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

        $template = ($this->config->get('default_admin_view_sale_bank_list')) ? $this->config->get('default_admin_view_sale_bank_list') : 'sale/bank_list.tpl';
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
        $filter_name = isset($this->request->get['filter_name']) ? $this->request->get['filter_name'] : null;
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

        $this->data['banks'] = [];

        $data = array(
            'filter_name' => $filter_name,
            'filter_date_start' => $filter_date_start,
            'filter_date_end' => $filter_date_end,
            'sort' => $sort,
            'order' => $order,
            'start' => ($page - 1) * $limit,
            'limit' => $limit
        );

        $bank_total = $this->modelBank->getAllTotal($data);
        $results = $this->modelBank->getAll($data);
        $i = str_replace('%theme%',$this->config->get('config_admin_template'),HTTP_ADMIN_THEME_IMAGE);
        foreach ($results as $result) {
            $action = array(
                'edit' => array(
                    'action' => 'edit',
                    'text' => $this->language->get('text_edit'),
                    'href' => Url::createAdminUrl('sale/bank/update') . '&bank_id=' . $result['bank_id'] . $url,
                    'img' =>  $i .'edit.png'
                ),
                'delete' => array(
                    'action' => 'delete',
                    'text' => $this->language->get('text_delete'),
                    'href' => '',
                    'img' => $i .'delete.png'
                )
            );

            if ($result['image'] && file_exists(DIR_IMAGE . $result['image'])) {
                $image = NTImage::resizeAndSave($result['image'], 40, 40);
            } else {
                $image = NTImage::resizeAndSave('no_image.jpg', 40, 40);
            }

            $this->data['banks'][] = array(
                'bank_id' => $result['bank_id'],
                'name' => $result['name'],
                'image' => $image,
                'date_added' => date('d-m-Y', strtotime($result['date_added'])),
                'selected' => isset($this->request->post['selected']) && in_array($result['bank_id'], $this->request->post['selected']),
                'action' => $action
            );
        }

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

        $this->data['sort_name'] = Url::createAdminUrl('sale/bank/grid') . '&sort=name' . $url;
        $this->data['sort_date_added'] = Url::createAdminUrl('sale/bank/grid') . '&sort=date_added' . $url;

        $url = '';

        if (isset($this->request->get['filter_name'])) {
            $url .= '&filter_name=' . $this->request->get['filter_name'];
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
        $pagination->total = $bank_total;
        $pagination->page = $page;
        $pagination->limit = $limit;
        $pagination->text = $this->language->get('text_pagination');
        $pagination->url = Url::createAdminUrl('sale/bank/grid') . $url . '&page={page}';

        $this->data['pagination'] = $pagination->render();

        $this->data['filter_name'] = $filter_name;
        $this->data['filter_date_start'] = $filter_date_start;
        $this->data['filter_date_end'] = $filter_date_end;

        $this->data['sort'] = $sort;
        $this->data['order'] = $order;

        $template = ($this->config->get('default_admin_view_sale_bank_grid')) ? $this->config->get('default_admin_view_sale_bank_grid') : 'sale/bank_grid.tpl';
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
            'href' => Url::createAdminUrl('sale/bank') . $url,
            'text' => $this->language->get('heading_title'),
            'separator' => ' :: '
        );

        if (!isset($this->request->get['bank_id'])) {
            $this->data['action'] = Url::createAdminUrl('sale/bank/insert') . $url;
        } else {
            $this->data['action'] = Url::createAdminUrl('sale/bank/update') . '&bank_id=' . $this->request->get['bank_id'] . $url;
        }

        $this->data['cancel'] = Url::createAdminUrl('sale/bank') . $url;

        if (isset($this->request->get['bank_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
            $bank_info = $this->modelBank->getById($this->request->get['bank_id']);
        }

        $this->setvar('name', $bank_info, '');
        $this->setvar('image', $bank_info, '');
        if (isset($bank_info) && $bank_info['image'] && file_exists(DIR_IMAGE . $bank_info['image'])) {
            $this->data['preview'] = NTImage::resizeAndSave($bank_info['image'], 100, 100);
        } else {
            $this->data['preview'] = NTImage::resizeAndSave('no_image.jpg', 100, 100);
        }

        $this->data['Url'] = new Url;

        $scripts[] = array('id' => 'bankScripts', 'method' => 'ready', 'script' =>
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

        $scripts[] = array('id' => 'bankFunctions', 'method' => 'function', 'script' =>
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

        $template = ($this->config->get('default_admin_view_sale_bank_form')) ? $this->config->get('default_admin_view_sale_bank_form') : 'sale/bank_form.tpl';
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
        if (!$this->user->hasPermission('modify', 'sale/bank')) {
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
     * ControllerSaleBank::validateDelete()
     * 
     * @return
     */
    private function validateDelete() {
        if (!$this->user->hasPermission('delete', 'sale/bank')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }

}
