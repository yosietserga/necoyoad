<?php

class ControllerMarketingNewsletter extends Controller {

    private $error = [];

    public function index() {
        $this->document->title = $this->language->get('heading_title');
        $this->getList();
    }

    public function insert() {
        $this->document->title = $this->language->get('heading_title');
        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $dom = new DOMDocument;
            $dom->preserveWhiteSpace = false;
            $dom->loadHTML(html_entity_decode($this->request->post['htmlbody']));
            $images = $dom->getElementsByTagName('img');
            foreach ($images as $image) {
                $src = $image->getAttribute('src');
                if (preg_match('/data:([^;]*);base64,(.*)/', $src)) {
                    list($type, $img) = explode(",", $src);
                    $type = trim(substr($type, strpos($type, "/") + 1, 3));

                    //TODO: validar imagenes
                    //TODO: agregar valor a la etieuta alt si esta vacia

                    $str = $this->config->get('config_name');
                    if ($str !== mb_convert_encoding(mb_convert_encoding($str, 'UTF-32', 'UTF-8'), 'UTF-8', 'UTF-32'))
                        $str = mb_convert_encoding($str, 'UTF-8', mb_detect_encoding($str));
                    $str = htmlentities($str, ENT_NOQUOTES, 'UTF-8');
                    $str = preg_replace('`&([a-z]{1,2})(acute|uml|circ|grave|ring|cedil|slash|tilde|caron|lig);`i', '\1', $str);
                    $str = html_entity_decode($str, ENT_NOQUOTES, 'UTF-8');
                    $str = preg_replace(array('`[^a-z0-9]`i', '`[-]+`'), '-', $str);
                    $str = strtolower(trim($str, '-'));

                    $filename = uniqid($str . "-") . "_" . time() . "." . $type;
                    $fp = fopen(DIR_IMAGE . "data/" . $filename, 'wb');
                    fwrite($fp, base64_decode($img));
                    fclose($fp);
                    $image->setAttribute('src', HTTP_IMAGE . "data/" . $filename);
                }
            }

            //TODO: agregar imagen transparente para el rastreo del correo
            //TODO: si est� configurado google analytics, agregar info para rastrear con google

            $htmlbody = htmlentities($dom->saveHTML());
            $this->request->post['htmlbody'] = $htmlbody;

            $newsletter_id = $this->modelNewsletter->add($this->request->post);
            $this->session->set('success', $this->language->get('text_success'));

            if ($this->request->post['to'] == "saveAndKeep") {
                $this->redirect(Url::createAdminUrl('marketing/newsletter/update', array('newsletter_id' => $newsletter_id)));
            } elseif ($this->request->post['to'] == "saveAndNew") {
                $this->redirect(Url::createAdminUrl('marketing/newsletter/insert'));
            } else {
                $this->redirect(Url::createAdminUrl('marketing/newsletter'));
            }
        }

        $this->getForm();
    }

    public function update() {
        $this->document->title = $this->language->get('heading_title');
        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $dom = new DOMDocument;
            $dom->preserveWhiteSpace = false;
            $dom->loadHTML(html_entity_decode($this->request->post['htmlbody']));
            $images = $dom->getElementsByTagName('img');
            foreach ($images as $image) {
                $src = $image->getAttribute('src');

                if (preg_match('/data:([^;]*);base64,(.*)/', $src)) {
                    list($type, $img) = explode(",", $src);
                    $type = trim(substr($type, strpos($type, "/") + 1, 3));

                    //TODO: validar imagenes

                    $str = $this->config->get('config_name');
                    if ($str !== mb_convert_encoding(mb_convert_encoding($str, 'UTF-32', 'UTF-8'), 'UTF-8', 'UTF-32'))
                        $str = mb_convert_encoding($str, 'UTF-8', mb_detect_encoding($str));
                    $str = htmlentities($str, ENT_NOQUOTES, 'UTF-8');
                    $str = preg_replace('`&([a-z]{1,2})(acute|uml|circ|grave|ring|cedil|slash|tilde|caron|lig);`i', '\1', $str);
                    $str = html_entity_decode($str, ENT_NOQUOTES, 'UTF-8');
                    $str = preg_replace(array('`[^a-z0-9]`i', '`[-]+`'), '-', $str);
                    $str = strtolower(trim($str, '-'));

                    $filename = uniqid($str . "-") . "_" . time() . "." . $type;
                    $fp = fopen(DIR_IMAGE . "data/" . $filename, 'wb');
                    fwrite($fp, base64_decode($img));
                    fclose($fp);
                    $image->setAttribute('src', HTTP_IMAGE . "data/" . $filename);
                }
            }
            $htmlbody = htmlentities($dom->saveHTML());
            $this->request->post['htmlbody'] = $htmlbody;

            $this->modelNewsletter->update($this->request->get['newsletter_id'], $this->request->post);
            $this->session->set('success', $this->language->get('text_success'));

            if ($this->request->post['to'] == "saveAndKeep") {
                $this->redirect(Url::createAdminUrl('marketing/newsletter/update', array('newsletter_id' => $newsletter_id)));
            } elseif ($this->request->post['to'] == "saveAndNew") {
                $this->redirect(Url::createAdminUrl('marketing/newsletter/insert'));
            } else {
                $this->redirect(Url::createAdminUrl('marketing/newsletter'));
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
        //TODO: preguntar si desea eliminar tambien los contactos de la lista
        $this->load->auto('marketing/newsletter');
        if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
            foreach ($this->request->post['selected'] as $id) {
                $this->modelNewsletter->delete($id);
            }
        } else {
            $this->modelNewsletter->delete($_GET['id']);
        }
    }

    /**
     * ControllerMarketingNewsletter::copy()
     * duplicar un objeto
     * @return boolean
     */
    public function copy() {
        $this->load->auto('marketing/newsletter');
        if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
            foreach ($this->request->post['selected'] as $id) {
                $this->modelNewsletter->copy($id);
            }
        } else {
            $this->modelNewsletter->copy($_GET['id']);
        }
        echo 1;
    }

    /**
     * ControllerMarketingNewsletter::activate()
     * duplicar un objeto
     * @return boolean
     */
    public function activate() {
        $result = 1;
        $this->load->auto('marketing/newsletter');
        if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
            foreach ($this->request->post['selected'] as $id) {
                $this->modelNewsletter->activate($id);
            }
        } else {
            $result = $this->modelNewsletter->toggleStatus($_GET['id']);
        }
        echo $result;
    }

    /**
     * ControllerMarketingNewsletter::deactivate()
     * duplicar un objeto
     * @return boolean
     */
    public function deactivate() {
        $this->load->auto('marketing/newsletter');
        if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
            foreach ($this->request->post['selected'] as $id) {
                $this->modelNewsletter->deactivate($id);
            }
        } else {
            $this->modelNewsletter->toggleStatus($_GET['id']);
        }
        echo 1;
    }

    private function getList() {
        $this->document->breadcrumbs = [];

        $this->document->breadcrumbs[] = array(
            'href' => Url::createAdminUrl('common/home'),
            'text' => $this->language->get('text_home'),
            'separator' => false
        );

        $this->document->breadcrumbs[] = array(
            'href' => Url::createAdminUrl('marketing/newsletter'),
            'text' => $this->language->get('heading_title'),
            'separator' => ' :: '
        );

        $this->data['insert'] = Url::createAdminUrl('marketing/newsletter/insert');

        $this->document->title = $this->data['heading_title'] = $this->language->get('heading_title');

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
                return false;
            } 
            function activateAll() {
                $('#gridWrapper').hide();
                $('#gridPreloader').show();
                $.post('" . Url::createAdminUrl("marketing/newsletter/activate") . "',$('#form').serialize(),function(){
                    $('#gridWrapper').load('" . Url::createAdminUrl("marketing/newsletter/grid") . "',function(){
                        $('#gridWrapper').show();
                        $('#gridPreloader').hide();
                    });
                });
                return false;
            } 
            function deactivateAll() {
                $('#gridWrapper').hide();
                $('#gridPreloader').show();
                $.post('" . Url::createAdminUrl("marketing/newsletter/deactivate") . "',$('#form').serialize(),function(){
                    $('#gridWrapper').load('" . Url::createAdminUrl("marketing/newsletter/grid") . "',function(){
                        $('#gridWrapper').show();
                        $('#gridPreloader').hide();
                    });
                });
                return false;
            } 
            function copyAll() {
                $('#gridWrapper').hide();
                $('#gridPreloader').show();
                $.post('" . Url::createAdminUrl("marketing/newsletter/copy") . "',$('#form').serialize(),function(){
                    $('#gridWrapper').load('" . Url::createAdminUrl("marketing/newsletter/grid") . "',function(){
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
                    $.post('" . Url::createAdminUrl("marketing/newsletter/delete") . "',$('#form').serialize(),function(){
                        $('#gridWrapper').load('" . Url::createAdminUrl("marketing/newsletter/grid") . "',function(){
                            $('#gridWrapper').show();
                            $('#gridPreloader').hide();
                        });
                    });
                }
                return false;
            } 
            function copy(e) {
                $('#gridWrapper').hide();
                $('#gridPreloader').show();
                $.getJSON('" . Url::createAdminUrl("marketing/newsletter/copy") . "&id=' + e, function(data) {
                    $('#gridWrapper').load('" . Url::createAdminUrl("marketing/newsletter/grid") . "',function(response){
                        $('#gridPreloader').hide();
                        $('#gridWrapper').show();
                    });
                });
            }
            function activate(e) {
                $.getJSON('" . Url::createAdminUrl("marketing/newsletter/activate") . "',{
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
                    $('#tr_' + e).remove();
                	$.getJSON('" . Url::createAdminUrl("marketing/newsletter/delete") . "',{ id:e });
                }
             }");
        $scripts[] = array('id' => 'sortable', 'method' => 'ready', 'script' =>
            "$('#gridWrapper').load('" . Url::createAdminUrl("marketing/newsletter/grid") . "',function(e){
                $('#gridPreloader').hide();
                $('#list tbody').sortable({
                    opacity: 0.6, 
                    cursor: 'move',
                    handle: '.move',
                    update: function() {
                        $.ajax({
                            'type':'post',
                            'dateType':'json',
                            'url':'" . Url::createAdminUrl("marketing/newsletter/sortable") . "',
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
                url:'" . Url::createAdminUrl("marketing/newsletter/grid") . "',
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

        $template = ($this->config->get('default_admin_view_newsletter_list')) ? $this->config->get('default_admin_view_newsletter_list') : 'marketing/newsletter_list.tpl';
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

        $this->data['newsletters'] = [];

        $data = array(
            'filter_name' => $filter_name,
            'filter_date_start' => $filter_date_start,
            'filter_date_end' => $filter_date_end,
            'sort' => $sort,
            'order' => $order,
            'start' => ($page - 1) * $this->config->get('config_admin_limit'),
            'limit' => $limit
        );

        $newsletter_total = $this->modelNewsletter->getAllTotal($data);
        $results = $this->modelNewsletter->getAll($data);
            $i = str_replace('%theme%',$this->config->get('config_admin_template'),HTTP_ADMIN_THEME_IMAGE);
        foreach ($results as $result) {
            $action = [];

            $action['activate'] = array(
                'action' => 'activate',
                'text' => $this->language->get('text_activate'),
                'href' => '',
                'img' => ($result['status']) ? $i.'good.png' : $i.'minus.png'
            );

            $action['edit'] = array(
                'action' => 'edit',
                'text' => $this->language->get('text_edit'),
                'href' => Url::createAdminUrl('marketing/newsletter/update') . '&newsletter_id=' . $result['newsletter_id'] . $url,
                'img' =>  $i.'edit.png'
            );

            $action['duplicate'] = array(
                'action' => 'duplicate',
                'text' => $this->language->get('text_copy'),
                'href' => '',
                'img' => $i.'copy.png'
            );

            $action['delete'] = array(
                'action' => 'delete',
                'text' => $this->language->get('text_delete'),
                'href' => '',
                'img' => $i.'delete.png'
            );

            $this->data['newsletters'][] = array(
                'newsletter_id' => $result['newsletter_id'],
                'name' => $result['name'],
                'textbody' => $result['textbody'],
                'htmlbody' => $result['htmlbody'],
                'date_added' => date('d-m-Y h:i', strtotime($result['date_added'])),
                'action' => $action
            );
        }

        $url = '';

        if (isset($this->request->get['filter_name'])) {
            $url .= '&filter_name=' . $this->request->get['filter_name'];
        }
        if (isset($this->request->get['filter_status'])) {
            $url .= '&filter_status=' . $this->request->get['filter_status'];
        }
        if (isset($this->request->get['filter_date_added'])) {
            $url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
        }

        if ($order == 'ASC') {
            $url .= '&order=DESC';
        } else {
            $url .= '&order=ASC';
        }

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        $this->data['sort_name'] = Url::createAdminUrl('marketing/newsletter/grid') . '&sort=name' . $url;
        $this->data['sort_subject'] = Url::createAdminUrl('marketing/newsletter/grid') . '&sort=subject' . $url;
        $this->data['sort_active'] = Url::createAdminUrl('marketing/newsletter/grid') . '&sort=active' . $url;
        $this->data['sort_archive'] = Url::createAdminUrl('marketing/newsletter/grid') . '&sort=archive' . $url;
        $this->data['sort_date_added'] = Url::createAdminUrl('marketing/newsletter/grid') . '&sort=date_added' . $url;

        $url = '';

        if (isset($this->request->get['filter_name'])) {
            $url .= '&filter_name=' . $this->request->get['filter_name'];
        }
        if (isset($this->request->get['filter_subject'])) {
            $url .= '&filter_subject=' . $this->request->get['filter_subject'];
        }
        if (isset($this->request->get['filter_status'])) {
            $url .= '&filter_status=' . $this->request->get['filter_status'];
        }
        if (isset($this->request->get['filter_date_added'])) {
            $url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
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
        $pagination->total = $newsletter_total;
        $pagination->page = $page;
        $pagination->limit = $limit;
        $pagination->text = $this->language->get('text_pagination');
        $pagination->url = Url::createAdminUrl('marketing/newsletter/grid') . $url . '&page={page}';

        $this->data['pagination'] = $pagination->render();

        $this->data['filter_name'] = $filter_name;
        $this->data['filter_subject'] = $filter_subject;
        $this->data['filter_status'] = $filter_status;
        $this->data['filter_date_added'] = $filter_date_added;

        $this->data['sort'] = $sort;
        $this->data['order'] = $order;

        $template = ($this->config->get('default_admin_view_newsletter_grid')) ? $this->config->get('default_admin_view_newsletter_grid') : 'marketing/newsletter_grid.tpl';
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_admin_template') . '/'. $template)) {
            $this->template = $this->config->get('config_admin_template') . '/' . $template;
        } else {
            $this->template = 'default/' . $template;
        }

        $this->response->setOutput($this->render(true), $this->config->get('config_compression'));
    }

    public function getForm() {
        $this->data['Url'] = new Url;
        $this->data['heading_title'] = $this->language->get('heading_title');

        $this->data['entry_name'] = $this->language->get('entry_name');
        $this->data['entry_description'] = $this->language->get('entry_description');
        $this->data['entry_template'] = $this->language->get('entry_template');
        $this->data['entry_text_content'] = $this->language->get('entry_text_content');
        $this->data['entry_html_content'] = $this->language->get('entry_html_content');
        $this->data['entry_category'] = $this->language->get('entry_category');

        $this->data['button_save'] = $this->language->get('button_save');
        $this->data['button_save_and_new'] = $this->language->get('button_save_and_new');
        $this->data['button_save_and_exit'] = $this->language->get('button_save_and_exit');
        $this->data['button_save_and_keep'] = $this->language->get('button_save_and_keep');
        $this->data['button_cancel'] = $this->language->get('button_cancel');

        $this->document->breadcrumbs = [];
        $this->document->breadcrumbs[] = array(
            'href' => Url::createAdminUrl('common/home'),
            'text' => $this->language->get('text_home'),
            'separator' => false
        );
        $this->document->breadcrumbs[] = array(
            'href' => Url::createAdminUrl('marketing/newsletter'),
            'text' => $this->language->get('heading_title'),
            'separator' => ' :: '
        );

        if (isset($this->request->get['newsletter_id'])) {
            $this->data['action'] = Url::createAdminUrl('marketing/newsletter/update') . "&amp;newsletter_id=" . $this->request->get['newsletter_id'];
        } else {
            $this->data['action'] = Url::createAdminUrl('marketing/newsletter/insert');
        }

        $this->data['cancel'] = Url::createAdminUrl('marketing/newsletter');

        $this->data['error_warning'] = isset($this->error['warning']) ? $this->error['warning'] : '';
        $this->data['error_name'] = isset($this->error['name']) ? $this->error['name'] : '';
        $this->data['error_description'] = isset($this->error['description']) ? $this->error['description'] : '';
        $this->data['error_lists'] = isset($this->error['lists']) ? $this->error['lists'] : '';
        $this->data['error_subject'] = isset($this->error['subject']) ? $this->error['subject'] : '';
        $this->data['error_from_name'] = isset($this->error['from_name']) ? $this->error['from_name'] : '';
        $this->data['error_from_email'] = isset($this->error['from_email']) ? $this->error['from_email'] : '';
        $this->data['error_replyto_email'] = isset($this->error['replyto_email']) ? $this->error['replyto_email'] : '';
        $this->data['error_bounce_email'] = isset($this->error['bounce_email']) ? $this->error['bounce_email'] : '';

        if (isset($this->request->get['newsletter_id'])) {
            $newsletter_info = $this->modelNewsletter->getById($this->request->get['newsletter_id']);
        } else {
            $newsletter_info = [];
        }

        $this->setvar('name', $newsletter_info, '');
        $this->setvar('htmlbody', $newsletter_info, '');
        $this->setvar('textbody', $newsletter_info, 'Para poder ver este email, debes utilizar un cliente de correo compatible con vistas HTML.');

        $this->data['templates'] = $this->email_template->getPremadeTemplateList(DIR_EMAIL_TEMPLATE, true);
        $this->data['categories'] = $this->modelCategory->getAll();

        $scripts[] = array('id' => 'form', 'method' => 'ready', 'script' =>
            "CKEDITOR.replace('htmlbody', {
           	    filebrowserBrowseUrl: '" . Url::createAdminUrl("common/filemanager") . "',
                filebrowserImageBrowseUrl: '" . Url::createAdminUrl("common/filemanager") . "',
                filebrowserFlashBrowseUrl: '" . Url::createAdminUrl("common/filemanager") . "',
                filebrowserUploadUrl: '" . Url::createAdminUrl("common/filemanager") . "',
                filebrowserImageUploadUrl: '" . Url::createAdminUrl("common/filemanager") . "',
                filebrowserFlashUploadUrl: '" . Url::createAdminUrl("common/filemanager") . "',
                height:600
            });");

        $scripts[] = array('id' => 'functions', 'method' => 'function', 'script' =>
            "function readPremadeTemplate() {
            	$.ajax({
            		type: 'GET',
            		url: '" . Url::createAdminUrl("marketing/newsletter/readPremadeTemplate") . "&template=' + encodeURIComponent($('select[name=\'email_template\']').val()),
            		beforeSend: function() {},
            		success: function(data){
            			CKEDITOR.instances.htmlbody.setData(data);
            			$(\"#htmlbody_\").val(data);
            			CKEDITOR.config.fullPage = true;
            		}
            	});	
            }
            function getAll() {
            	$('#products').load('" . Url::createAdminUrl("marketing/newsletter/products") . "&category_id=' + encodeURIComponent($('select[name=\'category\']').val()));
            }");

        $this->scripts = array_merge($this->scripts, $scripts);

        /* feedback form values */
        $this->data['domain'] = HTTP_HOME;
        $this->data['account_id'] = C_CODE;
        $this->data['local_ip'] = $_SERVER['SERVER_ADDR'];
        $this->data['remote_ip'] = $_SERVER['REMOTE_ADDR'];
        $this->data['server'] = serialize($_SERVER); //TODO: encriptar todos estos datos con una llave que solo yo poseo

        $template = ($this->config->get('default_admin_view_newsletter_form')) ? $this->config->get('default_admin_view_newsletter_form') : 'marketing/newsletter_form.tpl';
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

    private function validateForm() {
        if (!$this->user->hasPermission('modify', 'marketing/newsletter')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }

    public function products() {
        $category_id = $this->request->get['category_id'];
        $this->load->auto('store/product');
        $strProducts = '<div class="clear"></div>';
        $products = $this->modelProduct->getAllByCategoryId($category_id);
        if ($products) {
            $this->load->auto('image');
            foreach ($products as $product) {
                $strProducts .= '<div id="pid' . $product["product_id"] . '" class="dragMe productNewsletter">';
                $strProducts .= "<p>" . $product['name'] . "</p>";
                if (empty($product['image'])) {
                    $strProducts .= "<img src='" . NTImage::resizeAndSave(no_image . jpg, 50, 50) . "' alt='" . $product['name'] . "'>\n";
                } else {
                    $strProducts .= "<img src='" . NTImage::resizeAndSave($product['image'], 50, 50) . "' alt='" . $product['name'] . "'>\n";
                }
                $strProducts .= "<input type='hidden' name='" . $product['product_id'] . "' value='" . $product['product_id'] . "'>\n";
                $strProducts .= '<div class="clear"></div>';
                $strProducts .= "<div class='button'>Arrastrar</div>";
                $strProducts .= "</div>";
            }
            $strProducts .=
                    "<script type=\"text/javascript\">
                    $('.dragMe').draggable({
                        scroll: true,
                        iframeFix: true,
                        helper:'clone',
                        start: function() { 
                            $('#pid" . $product['product_id'] . "').after('<input type=\"hidden\" name=\"pid\" id=\"pid\" value=\"" . $product['product_id'] . "\">');
                        }
                    });
                    
                    $('#cke_contents_htmlbody').droppable({
                        accept:'.dragMe',
                        hoverClass:'ui-state-active',
                		drop: function(event, ui) {
                			$.ajax({
                				type: 'GET',
                				url: '" . Url::createAdminUrl("marketing/newsletter/getProduct") . "&product_id=' + encodeURIComponent($('input[name=\'pid\']').val()),
                				success: function(data){
                					CKEDITOR.instances.htmlbody.insertHtml(data);
                				}
                			});	
                		}
                	});
                    
                	$('#textbody').droppable({
                        accept:'.dragMe',
                		drop: function(event, ui) {
                			$.ajax({
                				type: 'GET',
                				url: '" . Url::createAdminUrl("marketing/newsletter/getProduct") . "&format=t&product_id=' + encodeURIComponent($('input[name=\'pid\']').val()),
                				success: function(data){
                					$('#textbody').val(jQuery('#textbody').val()+data);
                				}
                			});	
                		}
                	});
                </script>";
        } else {
            $strProducts = 'No hay productos en esta categor&iacute;a. <a href="' . HTTP_HOME . 'index.php?r=store/product&token=' . $this->request->get['token'] . '">Le gustar&iacute;a agregar algunos</a>';
        }
        $this->response->setOutput($strProducts);
    }

    public function template() {
        $template = basename($this->request->get['template']);
        $template = str_replace('-', '/', $template);
        $template = str_replace('&amp;', '&', $template);
        if (file_exists(DIR_EMAIL_TEMPLATE . $template . '/preview.gif')) {
            $image = HTTP_EMAIL_TPL_IMAGE . $template . '/preview.gif';
        } else {
            $image = HTTP_IMAGE . 'no_image.jpg';
        }
        $image2 = HTTP_EMAIL_TPL_IMAGE . $template . '/preview.gif';
        $this->response->setOutput('<img src="' . $image . '" style="border: 2px solid #EEE;" />');
    }

    public function getById() {
        $product_id = $this->request->get['product_id'];
        $this->load->auto('store/product');
        $this->load->auto('image');
        $this->load->auto('tax');
        $this->load->auto('currency');
        $tax = new Tax($this->registry);
        $currency = new Currency($this->registry);
        $strProducts = '';
        $product = $this->modelProduct->getById($product_id);
        $tags = $this->modelProduct->getTags($product_id);
        if (isset($this->request->get['format']) && !empty($this->request->get['format'])) {
            $strProducts .= "Producto: " . $product['name'] . "\n";
            $strProducts .= "Precio: " . $currency->format($tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax'))) . "\n"; //TODO: cureency format
            $strProducts .= "URL: " . Url::createUrl("product/product", array("product_id" => $product['product_id']), "NONSSL", HTTP_CATALOG . "/");
            if ($tags) {
                foreach ($tags as $key => $tag) {
                    $ntag = $key + 1;
                    $strProducts .= "\nTag " . $ntag . ": " . Url::createUrl("product/search", array("keyword" => $tag['tag']), "NONSSL", HTTP_CATALOG . "/") . "\n";
                }
            }
            $strProducts .= "\n";
            echo $strProducts;
        } else {
            $strProducts .= "<div style='margin:5px;padding:3px;background:#FFF;float:left;border:dotted 1px #ccc;width:100px;display:block;text-align:center'>";
            $strProducts .= "<br><p><a href='" . Url::createUrl("product/product", array("product_id" => $product['product_id']), "NONSSL", HTTP_CATALOG . "/") . "'>" . $product['name'] . "</a></p>";
            if (empty($product['image'])) {
                $strProducts .= "<a href='" . Url::createUrl("product/product", array("product_id" => $product['product_id']), "NONSSL", HTTP_CATALOG . "/") . "'>";
                $strProducts .= "<img src='" . NTImage::resizeAndSave('no_image.jpg', 50, 50) . "' alt='" . $product['name'] . "'>";
                $strProducts .= "</a>";
            } else {
                $strProducts .= "<a href='" . Url::createUrl("product/product", array("product_id" => $product['product_id'])) . "'>";
                $strProducts .= "<img src='" . NTImage::resizeAndSave($product['image'], 50, 50) . "' alt='" . $product['name'] . "'>";
                $strProducts .= "</a>";
            }
            $strProducts .= "<input type='hidden' name='" . $product['product_id'] . "' value='" . $product['product_id'] . "'>";
            $strProducts .= "<br><b>" . $currency->format($tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax'))) . "</b><br>";
            if ($tags) {
                foreach ($tags as $key => $tag) {
                    $strProducts .= "&nbsp;&nbsp;<a href='" . Url::createUrl("product/search", array("keyword" => $tag['tag']), "NONSSL", HTTP_CATALOG . "/") . "' style='font:normal 9px verdana'>" . $tag['tag'] . "</a>&nbsp;&nbsp;";
                }
            }
            $strProducts .= "<br></div>";
            echo $strProducts;
        }
    }

    public function readPremadeTemplate() {
        $this->load->library("email/template");
        $template = new EmailTemplate($this->registry);
        $templaname = basename($this->request->get['template']);
        $templaname = str_replace('.', '/', $templaname);
        $templaname = str_replace('&amp;', '&', $templaname);
        $templaname = str_replace('%20', ' ', $templaname);
        $templaname = str_replace('%2F', '/', $templaname);
        $tpl_content = $template->readPremadeTemplate($templaname);
        echo "$tpl_content";
    }

}
