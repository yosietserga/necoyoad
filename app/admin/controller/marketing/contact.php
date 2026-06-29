<?php

class ControllerMarketingContact extends Controller {

    private $error = [];

    public function index() {
        $this->document->title = $this->language->get('heading_title');

        $this->getList();
    }

    public function insert() {
        $this->load->language('marketing/contact');

        $this->document->title = $this->language->get('heading_title');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {

            $contact_id = $this->modelContact->add($this->request->post);
            $this->session->set('success', $this->language->get('text_success'));

            if ($this->request->post['to'] == "saveAndKeep") {
                $this->redirect(Url::createAdminUrl('marketing/contact/update', array('contact_id' => $contact_id, 'menu' => 'mercadeo')));
            } elseif ($this->request->post['to'] == "saveAndNew") {
                $this->redirect(Url::createAdminUrl('marketing/contact/insert', array('menu' => 'mercadeo')));
            } else {
                $this->redirect(Url::createAdminUrl('marketing/contact', array('menu' => 'mercadeo')));
            }
        }

        $this->getForm();
    }

    public function update() {
        $this->load->language('marketing/contact');

        $this->document->title = $this->language->get('heading_title');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {

            $contact_id = $this->modelContact->update($this->request->getQuery('contact_id'), $this->request->post);
            $this->session->set('success', $this->language->get('text_success'));

            if ($this->request->post['to'] == "saveAndKeep") {
                $this->redirect(Url::createAdminUrl('marketing/contact/update', array('contact_id' => $contact_id, 'menu' => 'mercadeo')));
            } elseif ($this->request->post['to'] == "saveAndNew") {
                $this->redirect(Url::createAdminUrl('marketing/contact/insert', array('menu' => 'mercadeo')));
            } else {
                $this->redirect(Url::createAdminUrl('marketing/contact', array('menu' => 'mercadeo')));
            }
        }

        $this->getForm();
    }

    /**
     * ControllerMarketingContact::delete()
     * elimina un objeto
     * @return boolean
     * */
    public function delete() {
        if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
            foreach ($this->request->post['selected'] as $id) {
                $this->modelContact->delete($id);
            }
        } else {
            $this->modelContact->delete($_GET['id']);
        }
    }

    /**
     * ControllerMarketingContact::addToList()
     * asocia un contacto a una lista
     * @return boolean
     * */
    public function addToList() {
        $this->load->model('marketing/list');
        if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
            foreach ($this->request->post['selected'] as $id) {
                $this->modelList->addContact($this->request->getQuery('contact_list_id'), $id);
            }
        } else {
            $this->modelList->addContact($this->request->getQuery('contact_list_id'), $_GET['id']);
        }
    }

    public function exportThis() {
        $dir_vcards = opendir($_SERVER['DOCUMENT_ROOT']);
        while ($file_vcf = readdir($dir_vcards) !== false) {
            if (!is_dir($_SERVER['DOCUMENT_ROOT'] . $file_vcf)) {
                $path_vcf = pathinfo($_SERVER['DOCUMENT_ROOT'] . $file_vcf);
                if ($path_vcf['extension'] === 'vcf') {
                    unlink($path_vcf['dirname'] . '/' . $path_vcf['basename'] . '.' . $path_vcf['extension']);
                }
            }
        }
        $contact_info = $this->modelemail_contact->getContactExport($this->request->get['contact_id']);
        if ($contact_info) {
            $rand = rand();
            $this->vcard->vCard($this->config, $_SERVER['DOCUMENT_ROOT']);
            $this->vcard->deleteOldFiles();
            $this->vcard->card_filename = $contact_info['firstname'] . '_' . $contact_info['lastname'] . '_' . $rand . '.vcf';
            $this->vcard->setFirstName($contact_info['firstname']);
            $this->vcard->setLastName($contact_info['lastname']);
            $this->vcard->setNickname($contact_info['firstname'] . '_' . $contact_info['lastname']);
            $this->vcard->setCompany($contact_info['company']);
            $this->vcard->setOrganisation($contact_info['company']);
            $this->vcard->setDepartment($contact_info['profesion']);
            $this->vcard->setJobTitle($contact_info['titulo']);
            $this->vcard->setTelephoneWork1($contact_info['telephone']);
            $this->vcard->setTelephoneWork2($contact_info['telephone']);
            $this->vcard->setTelephoneHome1($contact_info['telephone']);
            $this->vcard->setTelephoneHome2($contact_info['telephone']);
            $this->vcard->setCellphone($contact_info['telephone']);
            $this->vcard->setCarphone($contact_info['telephone']);
            $this->vcard->setPager($contact_info['telephone']);
            $this->vcard->setAdditionalTelephone($contact_info['telephone']);
            $this->vcard->setFaxWork($contact_info['fax']);
            $this->vcard->setFaxHome($contact_info['fax']);
            $this->vcard->setPreferredTelephone($contact_info['telephone']);
            $this->vcard->setTelex($contact_info['telephone']);
            $this->vcard->setWorkStreet($contact_info['address_1'] . ', ' . $contact_info['city']);
            $this->vcard->setHomeStreet($contact_info['address_1'] . ', ' . $contact_info['city']);
            $this->vcard->setPostalStreet($contact_info['address_1'] . ', ' . $contact_info['city']);
            $this->vcard->setURLWork($contact_info['website']);
            $this->vcard->setEMail($contact_info['email']);
            $this->vcard->writeCardFile();
            header('Location: https://' . $_SERVER['SERVER_NAME'] . '/' . $contact_info['firstname'] . '_' . $contact_info['lastname'] . '_' . $rand . '.vcf');
        }
        $dir_vcards = opendir($_SERVER['DOCUMENT_ROOT']);
        while ($file_vcf = readdir($dir_vcards) !== false) {
            if (!is_dir($_SERVER['DOCUMENT_ROOT'] . $file_vcf)) {
                $path_vcf = pathinfo($_SERVER['DOCUMENT_ROOT'] . $file_vcf);
                if ($path_vcf['extension'] === 'vcf') {
                    unlink($path_vcf['dirname'] . '/' . $path_vcf['basename'] . '.' . $path_vcf['extension']);
                }
            }
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
            'href' => Url::createAdminUrl('marketing/contact', array('menu' => 'mercadeo')) . $url,
            'text' => $this->language->get('heading_title'),
            'separator' => ' :: '
        );

        $this->data['insert'] = Url::createAdminUrl('marketing/contact/insert', array('menu' => 'mercadeo')) . $url;
        $this->data['import'] = Url::createAdminUrl('marketing/contact/import', array('menu' => 'mercadeo'));
        $this->data['export'] = Url::createAdminUrl('marketing/contact/export', array('menu' => 'mercadeo'));

        $this->data['heading_title'] = $this->document->title = $this->language->get('heading_title');

        $this->data['button_import'] = $this->language->get('button_import');
        $this->data['button_insert'] = $this->language->get('button_insert');
        $this->data['button_export'] = $this->language->get('button_export');

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
        $scripts[] = array('id' => 'list', 'method' => 'function', 'script' =>
            "function editAll() {
                return false;
            } 
            function addToList() {
                $('#temp').dialog({
                    height: 300,
                    width: 350,
                    buttons: {
                        '" . $this->language->get('button_accept') . "': function() {
                            if ( $('#contact_list_id').val() ) {
                                $.post('" . Url::createAdminUrl("marketing/contact/addtolist") . "&contact_list_id='+ $('#contact_list_id').val(),$('#form').serialize());
                                $('input[type=\'checkbox\']').attr('checked', null);
                                $( this ).dialog('close');
                            }
                        },
                        Cancel: function() {
                            $( this ).dialog('close');
                        }
                    },
                    close: function() {
                        
                    }
                });
                
                return false;
            }
            
            function deleteAll() {
                if (confirm('\\xbfDesea eliminar todos los objetos seleccionados?')) {
                    $('#gridWrapper').hide();
                    $('#gridPreloader').show();
                    $.post('" . Url::createAdminUrl("marketing/contact/delete") . "',$('#form').serialize(),function(){
                        $('#gridWrapper').load('" . Url::createAdminUrl("marketing/contact/grid") . "',function(){
                            $('#gridWrapper').show();
                            $('#gridPreloader').hide();
                        });
                    });
                }
                return false;
            } 
            function eliminar(e) {
                if (confirm('\\xbfDesea eliminar este objeto?')) {
                    $('#tr_' + e).remove();
                	$.getJSON('" . Url::createAdminUrl("marketing/contact/delete") . "',{
                        id:e
                    });
                }
                return false;
             }");
        $scripts[] = array('id' => 'sortable', 'method' => 'ready', 'script' =>
            "$('#gridWrapper').load('" . Url::createAdminUrl("marketing/contact/grid") . "',function(e){
                $('#gridPreloader').hide();
            });
                
            $('#formFilter').ntForm({
                lockButton:false,
                ajax:true,
                type:'get',
                dataType:'html',
                url:'" . Url::createAdminUrl("marketing/contact/grid") . "',
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

        $template = ($this->config->get('default_admin_view_contact_list')) ? $this->config->get('default_admin_view_contact_list') : 'marketing/contact_list.tpl';
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
        $filter_email = isset($this->request->get['filter_email']) ? $this->request->get['filter_email'] : null;
        $filter_status = isset($this->request->get['filter_status']) ? $this->request->get['filter_status'] : null;
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
        if (isset($this->request->get['filter_email'])) {
            $url .= '&filter_email=' . $this->request->get['filter_email'];
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

        $data = array(
            'name' => $filter_name,
            'email' => $filter_email,
            'date_start' => $filter_date_start,
            'date_end' => $filter_date_end,
            'sort' => $sort,
            'order' => $order,
            'start' => ($page - 1) * $limit,
            'limit' => $limit
        );

        $this->load->model('marketing/list');
        $this->data['lists'] = $this->modelList->getAll();

        $contact_total = $this->modelContact->getAllTotal($data);

        if ($contact_total) {
            $results = $this->modelContact->getAll($data);
            $i = str_replace('%theme%',$this->config->get('config_admin_template'),HTTP_ADMIN_THEME_IMAGE);

            foreach ($results as $result) {
                $action = [];

                $action['edit'] = array(
                    'action' => 'edit',
                    'text' => $this->language->get('text_edit'),
                    'href' => Url::createAdminUrl('marketing/contact/update', array('menu' => 'mercadeo')) . '&contact_id=' . $result['contact_id'] . $url,
                    'img' =>  $i  .'edit.png'
                );

                $action['delete'] = array(
                    'action' => 'delete',
                    'text' => $this->language->get('text_delete'),
                    'href' => '',
                    'img' => $i .'delete.png'
                );

                $this->data['contacts'][] = array(
                    'contact_id' => $result['contact_id'],
                    'customer_id' => $result['customer_id'],
                    'name' => !empty($result['name']) ? $result['name'] : $result['firstname'] .' '. $result['lastname'],
                    'firstname' => $result['firstname'],
                    'lastname' => $result['lastname'],
                    'telephone' => ($result['telephone']) ? $result['telephone'] : 'N/A',
                    'email' => is_numeric($result['mail']) ? '<a href="https://www.facebook.com/' . $result['mail'] . '" tagret="_blank">[Perfil Facebook]</a>' : '<a href="mailto:' . $result['mail'] . '">[' . $result['mail'] . ']</a>',
                    'date_added' => date('d-m-Y h:i:s', strtotime($result['created'])),
                    'selected' => isset($this->request->post['selected']) && in_array($result['customer_id'], $this->request->post['selected']),
                    'action' => $action
                );
            }
            /* //TODO: download vcard of each contact
              $this->data['content_vcard'] =
              "BEGIN:VCARD
              VERSION:2.1
              N;ENCODING=QUOTED-PRINTABLE:".$result['lastname'].";".$result['firstname'].";;
              FN;ENCODING=QUOTED-PRINTABLE:".$result['firstname']."  ".$result['lastname']."
              NICKNAME;ENCODING=QUOTED-PRINTABLE:".$result['firstname']."_".$result['lastname']."
              ORG;LANGUAGE=es;ENCODING=QUOTED-PRINTABLE:".$result['company'].";".$result['profesion']."
              TITLE;LANGUAGE=es;ENCODING=QUOTED-PRINTABLE:".$result['titulo']."
              TEL;WORK;VOICE:".$result['telephone']."
              TEL;WORK;VOICE:".$result['telephone']."
              TEL;HOME;VOICE:".$result['telephone']."
              TEL;CELL;VOICE:".$result['telephone']."
              TEL;CAR;VOICE:".$result['telephone']."
              TEL;VOICE:".$result['telephone']."
              TEL;PAGER;VOICE:".$result['telephone']."
              TEL;WORK;FAX:".$result['fax']."
              TEL;HOME:".$result['telephone']."
              TEL;PREF:".$result['telephone']."
              ADR;WORK:;".$result['address_1'].", ".$result['city'].";;;;
              LABEL;WORK;ENCODING=QUOTED-PRINTABLE:".$result['company']."=0D=0A".$result['address_1'].", ".$result['city']." =0D=0A,  =0D=0A
              ADR;HOME;;".$result['address_1'].", ".$result['city']." ;;;;
              LABEL;WORK;ENCODING=QUOTED-PRINTABLE:".$result['address_1'].", ".$result['city']." =0D=0A,  =0D=0A
              ADR;POSTAL;;".$result['address_1'].", ".$result['city']." ;;;;
              LABEL;POSTAL;ENCODING=QUOTED-PRINTABLE:".$result['address_1'].", ".$result['city']." =0D=0A,  =0D=0A
              URL;WORK:".$result['website']."
              EMAIL;PREF;INTERNET:".$result['email']."
              EMAIL;TLX:+581234567890
              REV:08102010T103800Z
              END:VCARD";
             */
        }

        $url = '';

        if (isset($this->request->get['filter_name'])) {
            $url .= '&filter_name=' . $this->request->get['filter_name'];
        }
        if (isset($this->request->get['filter_email'])) {
            $url .= '&filter_email=' . $this->request->get['filter_email'];
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

        if ($order == 'ASC') {
            $url .= '&order=DESC';
        } else {
            $url .= '&order=ASC';
        }

        $this->data['sort_name'] = Url::createAdminUrl('marketing/contact/grid') . '&sort=name' . $url;
        $this->data['sort_email'] = Url::createAdminUrl('marketing/contact/grid') . '&sort=email' . $url;
        $this->data['sort_date_added'] = Url::createAdminUrl('marketing/contact/grid') . '&sort=date_start' . $url;
        $this->data['sort_date_end'] = Url::createAdminUrl('marketing/contact/grid') . '&sort=date_end' . $url;

        $url = '';

        if (isset($this->request->get['filter_name'])) {
            $url .= '&filter_name=' . $this->request->get['filter_name'];
        }
        if (isset($this->request->get['filter_email'])) {
            $url .= '&filter_email=' . $this->request->get['filter_email'];
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
        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }
        if (isset($this->request->get['limit'])) {
            $url .= '&limit=' . $this->request->get['limit'];
        }

        $pagination = new Pagination();
        $pagination->ajax = true;
        $pagination->ajaxTarget = "gridWrapper";
        $pagination->total = $contact_total;
        $pagination->page = $page;
        $pagination->limit = $limit;
        $pagination->text = $this->language->get('text_pagination');
        $pagination->url = Url::createAdminUrl('marketing/contact/grid') . $url . '&page={page}';

        $this->data['pagination'] = $pagination->render();

        $this->data['filter_name'] = $filter_name;
        $this->data['filter_email'] = $filter_email;
        $this->data['filter_date_star'] = $filter_date_start;
        $this->data['filter_date_end'] = $filter_date_end;

        $this->data['sort'] = $sort;
        $this->data['order'] = $order;

        $template = ($this->config->get('default_admin_view_contact_grid')) ? $this->config->get('default_admin_view_contact_grid') : 'marketing/contact_grid.tpl';
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_admin_template') . '/'. $template)) {
            $this->template = $this->config->get('config_admin_template') . '/' . $template;
        } else {
            $this->template = 'default/' . $template;
        }

        $this->response->setOutput($this->render(true), $this->config->get('config_compression'));
    }

    public function getForm() {
        $this->load->language('marketing/contact');

        $this->document->title = $this->data['heading_title'] = $this->language->get('heading_title');

        $this->document->breadcrumbs = [];
        $this->document->breadcrumbs[] = array(
            'href' => Url::createAdminUrl('common/home'),
            'text' => $this->language->get('text_home'),
            'separator' => false
        );
        $this->document->breadcrumbs[] = array(
            'href' => Url::createAdminUrl('marketing/contact', array('menu' => 'mercadeo')),
            'text' => $this->language->get('heading_title'),
            'separator' => ' :: '
        );


        $this->data['error_email'] = isset($this->error['email']) ? $this->error['email'] : '';
        $this->data['error_warning'] = isset($this->error['warning']) ? $this->error['warning'] : '';

        $this->data['cancel'] = Url::createAdminUrl('marketing/contact', array('menu' => 'mercadeo'));

        $contact_info = [];
        if ($this->request->hasQuery('contact_id')) {
            $contact_info = $this->modelContact->getById($this->request->getQuery('contact_id'));

            $lists = $this->modelList->getAll(array(
                'contact_id'=>$this->request->getQuery('contact_id')
            ));
            $l = [];
            foreach ($lists as $list) {
                $l[] = $list['contact_list_id'];
            }
            $this->data['contact_lists'] = $l;
        }

        $this->setvar('name', $contact_info, "");
        $this->setvar('email', $contact_info, "");
        $this->setvar('customer_id', $contact_info, "");

        $this->data['lists'] = $this->modelList->getAll();

        $scripts[] = array('id' => 'form', 'method' => 'ready', 'script' =>
            "$('#form').ntForm({
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
            
             var cache = {};
            $.getJSON( '" . Url::createAdminUrl("sale/customer/callback") . "', function( data ) {
                $.each(data,function(i,item){
                    $(document.createElement('option'))
                    .val( item.id )
                    .text( item.label )
                    .attr( 'data-customer',item.value )
                    .appendTo('#_email');
                });
            });
            
            /* custom widget */
            $('#_email').emailCombobox();
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

        $template = ($this->config->get('default_admin_view_contact_form')) ? $this->config->get('default_admin_view_contact_form') : 'marketing/contact_form.tpl';
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
        if (!$this->user->hasPermission('modify', 'sale/customer')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (empty($this->request->post['email'])) {
            $this->error['email'] = $this->language->get('error_email');
        }

        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }

    public function import() {
        $this->document->title = $this->data['heading_title'] = "Importar Contactos";

        $this->document->breadcrumbs = [];
        $this->document->breadcrumbs[] = array(
            'href' => Url::createAdminUrl("common/home"),
            'text' => $this->language->get('text_home'),
            'separator' => false
        );
        $this->document->breadcrumbs[] = array(
            'href' => Url::createAdminUrl("marketing/contact", array('menu' => 'mercadeo')),
            'text' => "Contactos",
            'separator' => ' :: '
        );
        $this->document->breadcrumbs[] = array(
            'href' => Url::createAdminUrl("marketing/contact/import", array('menu' => 'mercadeo')),
            'text' => "Importar Contactos",
            'separator' => ' :: '
        );

        $scripts[] = array('id' => 'form', 'method' => 'ready', 'script' =>
            "$('#gridWrapper').load('" . Url::createAdminUrl("marketing/contact/importwizard", array('step' => 1)) . "',function(e){
                $('#gridPreloader').hide();
                $('#q').on('keyup',function(e){
                    var that = this;
                    var valor = $(that).val().toLowerCase();
                    if (valor.length <= 0) {
                        $('#listsWrapper li').show();
                    } else {
                        $('#listsWrapper li b').each(function(){
                            if ($(this).text().toLowerCase().indexOf( valor ) != -1) {
                                $(this).closest('li').show();
                            } else {
                                $(this).closest('li').hide();
                            }
                        });
                    }
                }); 
                
            });");

        $scripts[] = array('id' => 'importFunctions', 'method' => 'function', 'script' =>
            "function file_delete(field, preview) {
                $('#' + field).val('');
                $('#' + preview).parent('.row').find('.clear').remove();
                $('#' + preview).replaceWith('<a class=\"button\" id=\"'+ preview +'\" onclick=\"file_upload(\\'file_to_import\\', \\'preview\\');\">Seleccionar Archivo</a>');
            }
            
            function file_upload(field, preview) {
                var height = $(window).height() * 0.8;
                var width = $(window).width() * 0.8;
            	$('#dialog').remove();
            	$('#form').prepend('<div id=\"dialog\" style=\"padding: 3px 0px 0px 0px;z-index:10000;\"><iframe src=\"" . Url::createAdminUrl("common/filemanager") . "&field=' + encodeURIComponent(field) + '\" style=\"padding:0; margin: 0; display: block; width: 100%; height: 100%;z-index:10000;\" frameborder=\"no\" scrolling=\"auto\"></iframe></div>');
                
                $('#dialog').dialog({
            		title: '" . $this->data['text_image_manager'] . "',
            		close: function (event, ui) {
            			var csv = $('#' + field).val();
            			if (csv) {
            				$('#' + preview).replaceWith('<input type=\"text\" value=\"' + csv.replace('data/','') + '\" id=\"' + preview + '\" disabled=\"disabled\" /><div class=\"clear\"></div>');
            			}
            		},	
            		bgiframe: false,
            		width: width,
            		height: height,
            		resizable: false,
            		modal: false
            	});}");

        $this->scripts = array_merge($this->scripts, $scripts);

        $template = ($this->config->get('default_admin_view_contact_import')) ? $this->config->get('default_admin_view_contact_import') : 'marketing/contact_import.tpl';
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

    public function importwizard() {
        $this->data['Url'] = new Url;
        switch ((int) $_GET['step']) {
            case 1:
            default:
                $this->load->auto("marketing/list");
                $this->data['lists'] = $this->modelList->getAll();
                $template = ($this->config->get('default_admin_view_contact_import_1')) ? $this->config->get('default_admin_view_contact_import_1') : 'marketing/contact_import_1.tpl';
                if (file_exists(DIR_TEMPLATE . $this->config->get('config_admin_template') . '/'. $template)) {
                    $this->template = $this->config->get('config_admin_template') . '/' . $template;
                } else {
                    $this->template = 'default/' . $template;
                }
                $this->response->setOutput($this->render(true), $this->config->get('config_compression'));
                break;
            case 2:
                $data = unserialize(file_get_contents(DIR_CACHE . "temp_contact_data.csv"));

                $handle = fopen(DIR_IMAGE . $data['file'], "r+");
                $this->data['header'] = fgetcsv($handle, 1000, $data['separator'], $data['enclosure']);
                $this->data['fields']['Contacto'] = array(
                    'name' => 'Nombre Completo',
                    'email' => 'Email'
                );
                /* TODO: agregar clientes si no existen
                  $this->data['fields']['Clientes'] = array(
                  'customer_id'   =>'Cliente ID',
                  'firstname'     =>'Primer Nombre',
                  'lastname'      =>'Apellidos',
                  'telephone'     =>'Tel&eacut;fono'
                  );
                 */
                $template = ($this->config->get('default_admin_view_contact_import_2')) ? $this->config->get('default_admin_view_contact_import_2') : 'marketing/contact_import_2.tpl';
                if (file_exists(DIR_TEMPLATE . $this->config->get('config_admin_template') . '/'. $template)) {
                    $this->template = $this->config->get('config_admin_template') . '/' . $template;
                } else {
                    $this->template = 'default/' . $template;
                }

                $this->response->setOutput($this->render(true), $this->config->get('config_compression'));
                break;
            case 3:
                $template = ($this->config->get('default_admin_view_contact_import_3')) ? $this->config->get('default_admin_view_contact_import_3') : 'marketing/contact_import_3.tpl';
                if (file_exists(DIR_TEMPLATE . $this->config->get('config_admin_template') . '/'. $template)) {
                    $this->template = $this->config->get('config_admin_template') . '/' . $template;
                } else {
                    $this->template = 'default/' . $template;
                }

                $this->response->setOutput($this->render(true), $this->config->get('config_compression'));
                break;
        }
    }

    public function importprocess() {
        switch ($_GET['step']) {
            case 2:
                $data = [];
                if (isset($this->request->post['contact_lists'])) {
                    $data['contact_lists'] = serialize($this->request->post['contact_lists']);
                }
                $data['file'] = ($this->request->post['file']) ? $this->request->post['file'] : '';
                $data['separator'] = ($this->request->post['separator']) ? $this->request->post['separator'] : ";";
                $data['enclosure'] = ($this->request->post['enclosure'] && $this->request->post['enclosure'] != '&quote;') ? $this->request->post['enclosure'] : '"';
                $data['escape'] = ($this->request->post['escape']) ? $this->request->post['escape'] : '\\';
                $data['update'] = (int) $this->request->post['update'];
                $data['header'] = (int) $this->request->post['header'];

                $handle = fopen(DIR_IMAGE . $data['file'], "r+");
                $handle2 = fopen(DIR_CACHE . "temp_contact_data.csv", "w+");
                $handle3 = fopen(DIR_CACHE . "temp_contact_header.csv", "w+");
                fputcsv($handle3, (fgetcsv($handle, 1000, $data['separator'], $data['enclosure'])), $data['separator'], $data['enclosure']);
                fclose($handle3);

                fputs($handle2, serialize($data));
                fclose($handle2);

                fclose($handle);
                unset($handle, $handle2, $handle3);
                break;
            case 3:
                header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
                header("Last-Modified: " . gmdate("D, d M Y H:i:s") . "GMT");
                header("Cache-Control: no-cache, must-revalidate");
                header("Pragma: no-cache");
                header("Content-type: application/json");

                $return = [];
                $data = unserialize(file_get_contents(DIR_CACHE . "temp_contact_data.csv"));
                $handle = fopen(DIR_IMAGE . $data['file'], "r+");
                $handle2 = fopen(DIR_CACHE . "temp_contact_header.csv", "r+");

                if ($data['header'])
                    $header = fgetcsv($handle2, 1000, $data['separator'], $data['enclosure']);

                $keys = [];
                if (!in_array('email', $this->request->post['Header'])) {
                    $return['error'] = 1;
                    $return['msg'] = "Debe seleccionar el campo correspondiente al email del contacto, de lo contrario no se podr&aacute;n cargar los contactos";
                }

                if (!$return['error']) {
                    $contact = array(
                        'name',
                        'email'
                    );
                    //TODO: agregar array para importar clientes tambi�n

                    $d = $data;
                    $new = $updated = $bad = $total = 1;
                    $headers = $this->request->post['Header'];
                    while ($data = fgetcsv($handle, 1000, $d['separator'], $d['enclosure'])) {
                        $contact_id = $model = $forceUpdate = null;
                        if ($data == $header && $d['header'])
                            continue;
                        $return['total'] = $total++;

                        if ($d['update']) {
                            $sql = "UPDATE " . DB_PREFIX . "contact SET ";
                            //$sql_customer   = "UPDATE ". DB_PREFIX ."customer SET ";
                            //$sql_address = "UPDATE ". DB_PREFIX ."address SET ";
                        } else {
                            $sql = "INSERT INTO " . DB_PREFIX . "contact SET ";
                            //$sql_customer   = "INSERT INTO ". DB_PREFIX ."customer SET ";
                            //$sql_address = "INSERT INTO ". DB_PREFIX ."address SET ";
                        }

                        foreach ($header as $key => $col) { //$key = 0; $col = 'Nombre'
                            $data[$key] = preg_replace('/<\s*html.*?>/', '', $data[$key]);
                            $data[$key] = preg_replace('/<\s*\/\s*html\s*.*?>/', '', $data[$key]);
                            $data[$key] = preg_replace('@<head[^>]*?>.*?</head>@siu', '', $data[$key]);
                            $data[$key] = preg_replace('@<style[^>]*?>.*?</style>@siu', '', $data[$key]);
                            $data[$key] = preg_replace('@<script[^>]*?.*?</script>@siu', '', $data[$key]);
                            $data[$key] = preg_replace('@<object[^>]*?.*?</object>@siu', '', $data[$key]);
                            $data[$key] = preg_replace('@<embed[^>]*?.*?</embed>@siu', '', $data[$key]);
                            $data[$key] = preg_replace('@<applet[^>]*?.*?</applet>@siu', '', $data[$key]);
                            $data[$key] = preg_replace('@<iframe[^>]*?.*?</iframe>@siu', '', $data[$key]);
                            $data[$key] = preg_replace('@<noframes[^>]*?.*?</noframes>@siu', '', $data[$key]);
                            $data[$key] = preg_replace('@<noscript[^>]*?.*?</noscript>@siu', '', $data[$key]);
                            $data[$key] = preg_replace('@<noembed[^>]*?.*?</noembed>@siu', '', $data[$key]);
                            foreach ($headers as $column => $field) {//$column = 'Nombre'; $field = 'name'; <select name="Header[name]">
                                $col = str_replace(" ", "_", $col);

                                //TODO: validar cada campo de acuerdo a su tipo y longitud para evitar la insercion de datos basura
                                if (!empty($field) && $col == $column) {
                                    if (in_array($field, $contact)) {
                                        $keys[$key] = $field;
                                        $sql .= "`$field`='" . $this->db->escape($data[$key]) . "',";
                                    }
                                }
                            }
                        }

                        $idx = array_search('email', $keys);

                        if (!array_search('date_added', $keys))
                            $sql .= "`date_added`=NOW(),";

                        if (!$idx) {
                            $return['error'] = 1;
                            $return['msg'] = "Debe especificar el email del contacto";
                            break;
                        }

                        if ($idx) {
                            $email = $data[$idx];
                        }

                        $forceUpdate = false;
                        if (!empty($email)) {
                            $res = $this->db->query("SELECT * FROM " . DB_PREFIX . "contact WHERE email='" . $this->db->escape($email) . "'");
                            if ($res->num_rows && !$d['update']) {
                                continue;
                            } elseif ($res->num_rows && $d['update']) {
                                $forceUpdate = true;
                            }
                        }

                        $sql = substr($sql, 0, (strlen($sql) - 1));

                        if ($d['update']) {
                            if (!$forceUpdate) {
                                $sql = str_replace("UPDATE", "INSERT INTO", $sql);
                                $insert = true;
                            } else {
                                $sql = str_replace("INSERT INTO", "UPDATE", $sql) . " WHERE `email` = '" . $this->db->escape($email) . "'";
                            }
                            $result = $this->db->query($sql);
                            if (!$forceUpdate)
                                $contact_id = $this->db->getLastId();

                            if ($result && isset($insert)) {
                                $return['nuevo'] = $new++;
                            } elseif ($result && !isset($insert)) {
                                $return['updated'] = $updated++;
                            } else {
                                $return['bad'] = $bad++;
                            }
                        } else {
                            $result = $this->db->query($sql);
                            $contact_id = $this->db->getLastId();

                            if ($result) {
                                $return['nuevo'] = $new++;
                            } else {
                                $return['bad'] = $bad++;
                            }
                        }

                        $customer_info = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "customer WHERE email = '" . $this->db->escape($email) . "'");
                        if ($customer_info->row) {
                            $this->db->query("UPDATE " . DB_PREFIX . "contact SET customer_id = '" . (int) $customer_info->row['customer_id'] . "' WHERE contact_id = '" . (int) $contact_id . "'");
                        }

                        //TODO: asociar las listas a cada contacto
                    }
                }
                unlink(DIR_CACHE . "temp_contact_header.csv");
                unlink(DIR_CACHE . "temp_contact_data.csv");
                unlink(DIR_CACHE . "temp_contact_lists.csv");
                $this->load->library('json');
                $this->response->setOutput(Json::encode($return), $this->config->get('config_compression'));
                break;
        }
    }

}
