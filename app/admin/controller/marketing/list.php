<?php

class ControllerMarketingList extends Controller {

    private $error = [];

    public function index() {
        $this->document->title = $this->language->get('heading_title');

        $this->getList();
    }

    public function insert() {
        $this->document->title = $this->language->get('heading_title');
        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {

            $contact_list_id = $this->modelList->add($this->request->post);
            $this->session->set('success', $this->language->get('text_success'));

            if ($this->request->post['to'] == "saveAndKeep") {
                $this->redirect(Url::createAdminUrl('marketing/list/update', array('contact_list_id' => $contact_list_id)));
            } elseif ($this->request->post['to'] == "saveAndNew") {
                $this->redirect(Url::createAdminUrl('marketing/list/insert'));
            } else {
                $this->redirect(Url::createAdminUrl('marketing/list'));
            }
        }

        $this->getForm();
    }

    public function update() {
        $this->document->title = $this->language->get('heading_title');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->modelList->update($this->request->get['contact_list_id'], $this->request->post);
            $this->session->set('success', $this->language->get('text_success'));

            if ($this->request->post['to'] == "saveAndKeep") {
                $this->redirect(Url::createAdminUrl('marketing/list/update', array('contact_list_id' => $contact_list_id)));
            } elseif ($this->request->post['to'] == "saveAndNew") {
                $this->redirect(Url::createAdminUrl('marketing/list/insert'));
            } else {
                $this->redirect(Url::createAdminUrl('marketing/list'));
            }
        }

        $this->getForm();
    }

    private function getList() {
        $this->document->breadcrumbs = [];

        $this->document->breadcrumbs[] = array(
            'href' => Url::createAdminUrl('common/home'),
            'text' => $this->language->get('text_home'),
            'separator' => false
        );

        $this->document->breadcrumbs[] = array(
            'href' => Url::createAdminUrl('marketing/list') . $url,
            'text' => $this->language->get('heading_title'),
            'separator' => ' :: '
        );

        $this->data['insert'] = Url::createAdminUrl('marketing/list/insert') . $url;

        $this->data['heading_title'] = $this->language->get('heading_title');

        $this->data['button_insert'] = $this->language->get('button_insert');

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
        $scripts[] = array('id' => 'contactList', 'method' => 'function', 'script' =>
            "function activate(e) {    
            	$.ajax({
            	   'type':'get',
                   'dataType':'json',
                   'url':'" . Url::createAdminUrl("marketing/list/activate") . "&id=' + e,
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
                $.getJSON('" . Url::createAdminUrl("marketing/list/copy") . "&id=' + e, function(data) {
                    $('#gridWrapper').load('" . Url::createAdminUrl("marketing/list/grid") . "',function(response){
                        $('#gridPreloader').hide();
                        $('#gridWrapper').show();
                    });
                });
            }
            function eliminar(e) {
                if (confirm('\\xbfDesea eliminar este objeto?')) {
                    $('#tr_' + e).remove();
                	$.getJSON('" . Url::createAdminUrl("marketing/list/delete") . "',{
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
                $.post('" . Url::createAdminUrl("marketing/list/copy") . "',$('#form').serialize(),function(){
                    $('#gridWrapper').load('" . Url::createAdminUrl("marketing/list/grid") . "',function(){
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
                    $.post('" . Url::createAdminUrl("marketing/list/delete") . "',$('#form').serialize(),function(){
                        $('#gridWrapper').load('" . Url::createAdminUrl("marketing/list/grid") . "',function(){
                            $('#gridWrapper').show();
                            $('#gridPreloader').hide();
                        });
                    });
                }
                return false;
            }");
        $scripts[] = array('id' => 'sortable', 'method' => 'ready', 'script' =>
            "$('#gridWrapper').load('" . Url::createAdminUrl("marketing/list/grid") . "',function(e){
                $('#gridPreloader').hide();
            });
                
            $('#formFilter').ntForm({
                lockButton:false,
                ajax:true,
                type:'get',
                dataType:'html',
                url:'" . Url::createAdminUrl("marketing/list/grid") . "',
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

        $template = ($this->config->get('default_admin_view_list_list')) ? $this->config->get('default_admin_view_list_list') : 'marketing/list_list.tpl';
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
        $filter_contacts = isset($this->request->get['filter_contacts']) ? $this->request->get['filter_contacts'] : null;
        $filter_status = isset($this->request->get['filter_status']) ? $this->request->get['filter_status'] : null;
        $filter_date_start = isset($this->request->get['filter_date_start']) ? $this->request->get['filter_date_start'] : null;
        $filter_date_end = isset($this->request->get['filter_date_end']) ? $this->request->get['filter_date_end'] : null;
        $page = isset($this->request->get['page']) ? $this->request->get['page'] : 1;
        $sort = isset($this->request->get['sort']) ? $this->request->get['sort'] : 'title';
        $order = isset($this->request->get['order']) ? $this->request->get['order'] : 'ASC';
        $limit = !empty($this->request->get['limit']) ? $this->request->get['limit'] : $this->config->get('config_admin_limit');

        $url = '';

        if (isset($this->request->get['filter_name'])) {
            $url .= '&filter_name=' . $this->request->get['filter_name'];
        }
        if (isset($this->request->get['filter_contacts'])) {
            $url .= '&filter_contacts=' . $this->request->get['filter_contacts'];
        }
        if (isset($this->request->get['filter_status'])) {
            $url .= '&filter_status=' . $this->request->get['filter_status'];
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

        $this->data['lists'] = [];

        $data = array(
            'filter_name' => $filter_name,
            'filter_contacts' => $filter_contacts,
            'filter_date_start' => $filter_date_start,
            'filter_date_end' => $filter_date_end,
            'sort' => $sort,
            'order' => $order,
            'start' => ($page - 1) * $this->config->get('config_admin_limit'),
            'limit' => $limit
        );
        $lists_total = $this->modelList->getAllTotal($data);
        $results = $this->modelList->getAll($data);

            $i = str_replace('%theme%',$this->config->get('config_admin_template'),HTTP_ADMIN_THEME_IMAGE);
        foreach ($results as $result) {
            $action = [];

            $action['activate'] = array(
                'action' => 'activate',
                'text' => $this->language->get('text_activate'),
                'href' => '',
                'img' => $i .'good.png'
            );

            $action['edit'] = array(
                'action' => 'edit',
                'text' => $this->language->get('text_edit'),
                'href' => Url::createAdminUrl('marketing/list/update') . '&contact_list_id=' . $result['contact_list_id'] . $url,
                'img' =>  $i .'edit.png'
            );

            $action['duplicate'] = array(
                'action' => 'duplicate',
                'text' => $this->language->get('text_copy'),
                'href' => '',
                'img' => $i .'copy.png'
            );

            $action['delete'] = array(
                'action' => 'delete',
                'text' => $this->language->get('text_delete'),
                'href' => '',
                'img' => $i .'delete.png'
            );

            $this->data['lists'][] = array(
                'contact_list_id' => $result['contact_list_id'],
                'name' => $result['name'],
                'total_contacts' => (int) $result['total_contacts'],
                'date_added' => date('d-m-Y h:i:s', strtotime($result['date_added'])),
                'action' => $action
            );
        }

        $this->data['text_no_results'] = $this->language->get('text_no_results');

        $this->data['column_name'] = $this->language->get('column_name');
        $this->data['column_date_added'] = $this->language->get('column_date_added');
        $this->data['column_contacts'] = $this->language->get('column_contacts');
        $this->data['column_action'] = $this->language->get('column_action');

        $url = '';

        if (isset($this->request->get['filter_name'])) {
            $url .= '&filter_name=' . $this->request->get['filter_name'];
        }
        if (isset($this->request->get['filter_contacts'])) {
            $url .= '&filter_contacts=' . $this->request->get['filter_contacts'];
        }
        if (isset($this->request->get['filter_status'])) {
            $url .= '&filter_status=' . $this->request->get['filter_status'];
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
        if (!empty($this->request->get['limit'])) {
            $url .= '&limit=' . $this->request->get['limit'];
        }

        if ($order == 'ASC') {
            $url .= '&order=DESC';
        } else {
            $url .= '&order=ASC';
        }

        $this->data['sort_contacts'] = Url::createAdminUrl('marketing/list/grid') . '&sort=contacts' . $url;
        $this->data['sort_name'] = Url::createAdminUrl('marketing/list/grid') . '&sort=name' . $url;
        $this->data['sort_date_added'] = Url::createAdminUrl('marketing/list/grid') . '&sort=date_added' . $url;

        $url = '';

        if (isset($this->request->get['filter_name'])) {
            $url .= '&filter_name=' . $this->request->get['filter_name'];
        }
        if (isset($this->request->get['filter_contacts'])) {
            $url .= '&filter_contacts=' . $this->request->get['filter_contacts'];
        }
        if (isset($this->request->get['filter_status'])) {
            $url .= '&filter_status=' . $this->request->get['filter_status'];
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
        if (!empty($this->request->get['limit'])) {
            $url .= '&limit=' . $this->request->get['limit'];
        }

        $pagination = new Pagination();
        $pagination->ajax = true;
        $pagination->ajaxTarget = "gridWrapper";
        $pagination->total = $lists_total;
        $pagination->page = $page;
        $pagination->limit = $limit;
        $pagination->text = $this->language->get('text_pagination');
        $pagination->url = Url::createAdminUrl('marketing/list/grid') . $url . '&page={page}';

        $this->data['pagination'] = $pagination->render();

        $this->data['filter_contacts'] = $filter_contacts;
        $this->data['filter_name'] = $filter_name;
        $this->data['filter_date_start'] = $filter_date_start;
        $this->data['filter_date_end'] = $filter_date_end;

        $this->data['sort'] = $sort;
        $this->data['order'] = $order;

        $template = ($this->config->get('default_admin_view_list_grid')) ? $this->config->get('default_admin_view_list_grid') : 'marketing/list_grid.tpl';
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_admin_template') . '/'. $template)) {
            $this->template = $this->config->get('config_admin_template') . '/' . $template;
        } else {
            $this->template = 'default/' . $template;
        }

        $this->response->setOutput($this->render(true), $this->config->get('config_compression'));
    }

    public function getForm() {
        $this->document->breadcrumbs = [];

        $this->document->breadcrumbs[] = array(
            'href' => Url::createAdminUrl('common/home'),
            'text' => $this->language->get('text_home'),
            'separator' => false
        );
        $this->document->breadcrumbs[] = array(
            'href' => Url::createAdminUrl('marketing/list'),
            'text' => $this->language->get('heading_title'),
            'separator' => ' :: '
        );

        $this->data['error_name'] = isset($this->error['name']) ? $this->error['name'] : '';
        $this->data['error_warning'] = isset($this->error['warning']) ? $this->error['warning'] : '';

        if (isset($this->request->get['contact_list_id'])) {
            $this->data['action'] = Url::createAdminUrl('marketing/list/update') . '&list_id=' . $this->request->get['contact_list_id'];
        } else {
            $this->data['action'] = Url::createAdminUrl('marketing/list/insert');
        }

        $this->data['cancel'] = Url::createAdminUrl('marketing/list');
        $this->data['contact_list_id'] = $this->request->get['contact_list_id'];

        if (isset($this->request->post['contact_list'])) {
            $this->data['contacts_list'] = $this->request->post['contact_list'];
        } elseif (isset($this->request->get['contact_list_id'])) {
            $list_info = $this->modelList->getById($this->request->get['contact_list_id']);

            $contacts = $this->modelContact->getAll(array(
                'contact_list_id'=>$this->request->get['contact_list_id']
            ));
            $c = [];
            foreach ($contacts as $contact) {
                $c[] = $contact['contact_id'];
            }
            $this->data['contacts_list'] = $c;
        } else {
            $list_info = [];
        }

        $this->setvar('contact_list_id', $list_info, '');
        $this->setvar('name', $list_info, '');

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

        $scripts[] = array('id' => 'form', 'method' => 'ready', 'script' =>
            "$('#form').ntForm({lockButton:false});
            $('textarea').ntTextArea();
            
            var form_clean = $('#form').serialize();  
            
            window.onbeforeunload = function (e) {
                var form_dirty = $('#form').serialize();
                if(form_clean != form_dirty) {
                    return 'There is unsaved form data.';
                }
            };
            
            $('#q').on('keyup',function(e){
                var that = this;
                var valor = $(that).val().toLowerCase();
                if (valor.length <= 0) {
                    $('#contactsWrapper li').show();
                } else {
                    $('#contactsWrapper li b').each(function(){
                        var texto = $(this).text().toLowerCase();
                        if (texto.indexOf( valor ) != -1) {
                            $(this).closest('li').show();
                        } else {
                            $(this).closest('li').hide();
                        }
                    });
                }
            }); 
            
            $('#contactsWrapper li').on('click',function(e){
                var \$checkbox = $(this).find(':checkbox');
                \$checkbox.attr('checked', !\$checkbox.attr('checked'));
                $(this).toggleClass('selected');
            });
            
            $('.sidebar .tab').on('click',function(){
                $(this).closest('.sidebar').addClass('show').removeClass('hide').animate({'right':'0px'});
            });
            $('.sidebar').mouseenter(function(){
                clearTimeout($(this).data('timeoutId'));
            }).mouseleave(function(){
                var e = this;
                var timeoutId = setTimeout(function(){
                    if ($(e).hasClass('show')) {
                        $(e).removeClass('show').addClass('hide').animate({'right':'-400px'});
                    }
                }, 600);
                $(this).data('timeoutId', timeoutId); 
            });");

        $scripts[] = array('id' => 'functions', 'method' => 'function', 'script' =>
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

        $template = ($this->config->get('default_admin_view_list_form')) ? $this->config->get('default_admin_view_list_form') : 'marketing/list_form.tpl';
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

    private function validate() {
        if (empty($this->request->post['name'])) {
            $this->error['name'] = $this->language->get('error_name');
        }

        if (!$this->user->hasPermission('modify', 'marketing/list')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * ControllerMarketingList::delete()
     * elimina un objeto
     * @return boolean
     * */
    public function delete() {
        //TODO: preguntar si desea eliminar tambien los contactos de la lista
        $this->load->auto('marketing/list');
        if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
            foreach ($this->request->post['selected'] as $id) {
                $this->modelList->delete($id);
            }
        } else {
            $this->modelList->delete($_GET['id']);
        }
    }

    /**
     * ControllerMarketingList::copy()
     * duplicar un objeto
     * @return boolean
     */
    public function copy() {
        $this->load->auto('marketing/list');
        if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
            foreach ($this->request->post['selected'] as $id) {
                $this->modelList->copy($id);
            }
        } else {
            $this->modelList->copy($_GET['id']);
        }
        echo 1;
    }

}
