<?php

class ControllerMarketingCampaign extends Controller {

    private $error = [];

    public function index() {
        $this->document->title = $this->language->get('heading_title');
        $this->getList();
    }

    public function insert() {
        $this->document->title = $this->language->get('heading_title');
        if ($this->request->server['REQUEST_METHOD'] == 'POST' && $this->validateForm()) {
            $newsletter = $this->modelNewsletter->getById($this->request->post['newsletter_id']);
            $dom = new DOMDocument;
            $dom->preserveWhiteSpace = false;
            $dom->loadHTML(html_entity_decode($newsletter['htmlbody']));

            if ($this->request->post['embed_image']) {
                $images = $dom->getElementsByTagName('img');
                $total_images = $total_embed_images = 0;
                foreach ($images as $image) {
                    $src = $image->getAttribute('src');
                    $src = str_replace(HTTP_IMAGE, DIR_IMAGE, $src);
                    if (file_exists($src)) {
                        $img = file_get_contents($src);
                        $ext = substr($src, (strrpos($src, '.') + 1));
                        $embed = base64_encode($img);
                        $image->setAttribute('src', "data:image/$ext;base64,$embed");
                        $total_embed_images++;
                    }
                    $total_images++;
                }
            }

            $params = array(
                'contact_id' => '{%contact_id%}',
                'campaign_id' => '{%campaign_id%}'
            );

            if ($this->request->post['trace_email']) {
                $trace_url = Url::createUrl("marketing/campaign/trace", $params, 'NONSSL', HTTP_CATALOG);
                $trackEmail = $dom->createElement('img');
                $trackEmail->setAttribute('src', $trace_url);
                $dom->appendChild($trackEmail);
            }

            if ($this->request->post['trace_click']) {
                $links = $dom->getElementsByTagName('a');
                $total_links = $total_trace_links = 0;
                foreach ($links as $link) {
                    $href = $link->getAttribute('href');
                    $total_links++;
                    if (empty($href) || $href == "#" || strpos($href, "mailto"))
                        continue;

                    //TODO: validar enlaces
                    //TODO: sanitizar enlaces

                    $params['link_index'] = md5(time() . mt_rand(1000000, 9999999) . $href);
                    $_link = Url::createUrl("marketing/campaign/link", $params, 'NONSSL', HTTP_CATALOG);
                    $link->setAttribute('href', $_link);
                    $_links[] = array(
                        "url" => $_link,
                        "redirect" => $href,
                        "link_index" => $params['link_index']
                    );
                    $total_trace_links++;
                    //TODO: agregar valor a la etiqueta title si esta vacia
                }
            }

            $this->request->post['contacts'] = $contacts = $this->modelContact->getAll(array(
                'contact_list_id'=>$this->request->post['contact_list']
            ));

            $to = [];
            foreach ($contacts as $key => $contact) {
                if (in_array($contact['mail'], $to))
                    continue;
                $to[$key] = array(
                    'contact_id' => $contact['contact_id'],
                    'name' => $contact['name'],
                    'email' => $contact['mail']
                );
            }

            if ($this->request->post['repeat'] && $this->request->post['repeat'] == "weekly") {
                $this->request->post['repeat'] = "weekly@" . $this->request->post['repeat_wday'];
            }

            //TODO: verificar si la fecha es mayor al ahora
            $date_start_exec = "";
            $date_start_exec .= $this->request->post['start_year'] . "-";
            $date_start_exec .= $this->request->post['start_month'] . "-";
            $date_start_exec .= $this->request->post['start_day'] . " ";
            $date_start_exec .= $this->request->post['start_hour'] . ":";
            $date_start_exec .= $this->request->post['start_minute'] . ":00 ";
            $date_start_exec .= $this->request->post['start_meridium'];

            //TODO: verificar si la fecha es mayor a la fecha de inicio
            $date_end_exec = "";
            $date_end_exec .= $this->request->post['end_year'] . "-";
            $date_end_exec .= $this->request->post['end_month'] . "-";
            $date_end_exec .= $this->request->post['end_day'] . " ";
            $date_end_exec .= $this->request->post['end_hour'] . ":";
            $date_end_exec .= $this->request->post['end_minute'] . ":00 ";
            $date_end_exec .= $this->request->post['end_meridium'];

            //TODO: agregar info para rastrear con google analytics

            $htmlbody = htmlentities($dom->saveHTML());
            if (file_exists(DIR_SYSTEM . 'library/email/spam_rules.php')) {
                require_once(DIR_SYSTEM . 'library/email/spam_rules.php');
                foreach ($spam_rules as $rule) {
                    if (preg_match($rule[0], $htmlbody)) {
                        $score += $rule[2];
                        $broken_rules[] = array($rule[1], $rule[2]);
                    }
                }
            }

            $email_size = mb_strlen($htmlbody, '8bit') / 1000;
            if ($email_size >= 1000) {
                $size = round(($email_size / 1000), 2) . " MB";
            } else {
                $size = round($email_size, 2) . " KB";
            }

            $this->data['total_embed_images'] = $total_embed_images;
            $this->data['total_trace_links'] = $total_trace_links;
            $this->data['total_images'] = $total_images;
            $this->data['total_links'] = $total_links;
            $this->data['date_start_exec'] = $date_start_exec;
            $this->data['spam_score'] = $score;
            $this->data['broken_rules'] = $broken_rules;
            $this->data['email_size'] = $email_size;
            $this->data['size'] = $size;
            $this->data['contacts'] = count($contacts);

            $this->request->post['date_start'] = date('Y-m-d H:i:s', strtotime($date_start_exec));
            $this->request->post['date_end'] = date('Y-m-d H:i:s', strtotime($date_end_exec));

            $data = array(
                'to' => $to,
                'post' => $this->request->post,
                'links' => $_links
            );

            $this->cache->set("campaign.html.temp", $htmlbody);
            $this->cache->set("campaign.data.temp", serialize($data));

            $this->beforeSend();
        } else {
            $this->getForm();
        }
    }

    public function update() {
        $this->document->title = $this->language->get('heading_title');
        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $this->modelCampaign->update($this->request->get['campaign_id'], $this->request->post);
            
            $this->modelCampaign->setProperty($this->request->get['campaign_id'],'mail_server', 'mail_server_id', $this->request->getPost('mail_server_id'));
            $this->session->set('success', $this->language->get('text_success'));

            if ($this->request->post['to'] == "saveAndKeep") {
                $this->redirect(Url::createAdminUrl('marketing/campaign/update', array('campaign_id' => $this->request->get['campaign_id'])));
            } elseif ($this->request->post['to'] == "saveAndNew") {
                $this->redirect(Url::createAdminUrl('marketing/campaign/insert'));
            } else {
                $this->redirect(Url::createAdminUrl('marketing/campaign'));
            }
        }
        $this->getForm();
    }

    /**
     * ControllerMarketingList::delete()
     * elimina un objeto
     * @return boolean
     * */
    public function delete() {
        //TODO: preguntar si desea eliminar tambien los contactos de la lista
        $this->load->auto('marketing/campaign');
        if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
            foreach ($this->request->post['selected'] as $id) {
                $this->modelCampaign->delete($id);
            }
        } else {
            $this->modelCampaign->delete($_GET['id']);
        }
    }

    /**
     * ControllerMarketingList::copy()
     * duplicar un objeto
     * @return boolean
     */
    public function copy() {
        $this->load->auto('marketing/campaign');
        if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
            foreach ($this->request->post['selected'] as $id) {
                $this->modelCampaign->copy($id);
            }
        } else {
            $this->modelCampaign->copy($_GET['id']);
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
            'href' => Url::createAdminUrl('marketing/campaign'),
            'text' => $this->language->get('heading_title'),
            'separator' => ' :: '
        );

        $this->data['insert'] = Url::createAdminUrl('marketing/campaign/insert');

        $this->document->title = $this->data['heading_title'] = $this->language->get('heading_title');

        $this->data['button_insert'] = $this->language->get('button_insert');

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
        $scripts[] = array('id' => 'campaignList', 'method' => 'function', 'script' =>
            "function activate(e) {    
            	$.ajax({
            	   'type':'get',
                   'dataType':'json',
                   'url':'" . Url::createAdminUrl("marketing/campaign/activate") . "&id=' + e,
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
                $.getJSON('" . Url::createAdminUrl("marketing/campaign/copy") . "&id=' + e, function(data) {
                    $('#gridWrapper').load('" . Url::createAdminUrl("marketing/campaign/grid") . "',function(response){
                        $('#gridPreloader').hide();
                        $('#gridWrapper').show();
                    });
                });
            }
            function eliminar(e) {
                if (confirm('\\xbfDesea eliminar este objeto?')) {
                    $('#tr_' + e).remove();
                	$.getJSON('" . Url::createAdminUrl("marketing/campaign/delete") . "',{
                        id:e
                    });
                }
                return false;
             }
            function copyAll() {
                $('#gridWrapper').hide();
                $('#gridPreloader').show();
                $.post('" . Url::createAdminUrl("marketing/campaign/copy") . "',$('#form').serialize(),function(){
                    $('#gridWrapper').load('" . Url::createAdminUrl("marketing/campaign/grid") . "',function(){
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
                    $.post('" . Url::createAdminUrl("marketing/campaign/delete") . "',$('#form').serialize(),function(){
                        $('#gridWrapper').load('" . Url::createAdminUrl("marketing/campaign/grid") . "',function(){
                            $('#gridWrapper').show();
                            $('#gridPreloader').hide();
                        });
                    });
                }
                return false;
            }");
        $scripts[] = array('id' => 'sortable', 'method' => 'ready', 'script' =>
            "$('#gridWrapper').load('" . Url::createAdminUrl("marketing/campaign/grid") . "',function(e){
                $('#gridPreloader').hide();
                $('#list tbody').sortable({
                    opacity: 0.6, 
                    cursor: 'move',
                    handle: '.move',
                    update: function() {
                        $.ajax({
                            'type':'post',
                            'dateType':'json',
                            'url':'" . Url::createAdminUrl("marketing/campaign/sortable") . "',
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
                url:'" . Url::createAdminUrl("marketing/campaign/grid") . "',
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

        $template = ($this->config->get('default_admin_view_campaign_list')) ? $this->config->get('default_admin_view_campaign_list') : 'marketing/campaign_list.tpl';
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

    private function beforeSend() {
        $this->document->breadcrumbs = [];

        $this->document->breadcrumbs[] = array(
            'href' => Url::createAdminUrl('common/home'),
            'text' => $this->language->get('text_home'),
            'separator' => false
        );

        $this->document->breadcrumbs[] = array(
            'href' => Url::createAdminUrl('marketing/campaign'),
            'text' => $this->language->get('heading_title'),
            'separator' => ' :: '
        );

        $this->data['send'] = Url::createAdminUrl('marketing/campaign/send');
        $this->data['cancel'] = Url::createAdminUrl('marketing/campaign');

        $this->document->title = $this->data['heading_title'] = $this->language->get('heading_title');

        $template = ($this->config->get('default_admin_view_campaign_before_send')) ? $this->config->get('default_admin_view_campaign_before_send') : 'marketing/campaign_before_send.tpl';
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
        $filter_subject = isset($this->request->get['filter_subject']) ? $this->request->get['filter_subject'] : null;
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
        if (isset($this->request->get['filter_subject'])) {
            $url .= '&filter_subject=' . $this->request->get['filter_subject'];
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

        $this->data['campaigns'] = [];

        $data = array(
            'filter_name' => $filter_name,
            'filter_subject' => $filter_subject,
            'filter_date_start' => $filter_date_start,
            'filter_date_end' => $filter_date_end,
            'sort' => $sort,
            'order' => $order,
            'start' => ($page - 1) * $limit,
            'limit' => $limit
        );

        $campaign_total = $this->modelCampaign->getAllTotal($data);
        if ($campaign_total) {
            $results = $this->modelCampaign->getAll($data);
            $i = str_replace('%theme%',$this->config->get('config_admin_template'),HTTP_ADMIN_THEME_IMAGE);
            foreach ($results as $result) {
                $action = [];

                $action['activate'] = array(
                    'action' => 'activate',
                    'text' => $this->language->get('text_activate'),
                    'href' => '',
                    'img' => $i.'good.png'
                );

                $action['edit'] = array(
                    'action' => 'edit',
                    'text' => $this->language->get('text_edit'),
                    'href' => Url::createAdminUrl('marketing/campaign/update') . '&campaign_id=' . $result['campaign_id'] . $url,
                    'img' =>  $i .'edit.png'
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
                    'img' => $i .'delete.png'
                );

                $result['newsletter'] = $this->modelCampaign->getNewsletter($result['newsletter_id']);
                $result['contacts'] = $this->modelCampaign->getContacts($result['newsletter_id']);
                $result['links'] = $this->modelCampaign->getLinks($result['newsletter_id']);
                $result['tasks'] = $this->modelCampaign->getTasks($result['newsletter_id']);

                $result['date_added'] = date('d-m-Y h:i A', strtotime($result['date_added']));
                $result['date_start'] = date('d-m-Y h:i A', strtotime($result['date_start']));
                $result['date_end'] = date('d-m-Y h:i A', strtotime($result['date_end']));
                $result['repeat'] = ($result['repeat']) ? $result['repeat'] : null;
                $result['action'] = $action;

                $this->data['campaigns'][] = $result;
            }
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

        $this->data['sort_name'] = Url::createAdminUrl('marketing/campaign/grid') . '&sort=name' . $url;
        $this->data['sort_subject'] = Url::createAdminUrl('marketing/campaign/grid') . '&sort=subject' . $url;
        $this->data['sort_active'] = Url::createAdminUrl('marketing/campaign/grid') . '&sort=active' . $url;
        $this->data['sort_archive'] = Url::createAdminUrl('marketing/campaign/grid') . '&sort=archive' . $url;
        $this->data['sort_date_added'] = Url::createAdminUrl('marketing/campaign/grid') . '&sort=date_added' . $url;

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
        $pagination->total = $campaign_total;
        $pagination->page = $page;
        $pagination->ajax = true;
        $pagination->ajaxTarget = 'gridWrapper';
        $pagination->limit = $limit;
        $pagination->text = $this->language->get('text_pagination');
        $pagination->url = Url::createAdminUrl('marketing/campaign/grid') . $url . '&page={page}';

        $this->data['pagination'] = $pagination->render();

        $this->data['filter_name'] = $filter_name;
        $this->data['filter_subject'] = $filter_subject;
        $this->data['filter_status'] = $filter_status;
        $this->data['filter_date_start'] = $filter_date_start;

        $this->data['sort'] = $sort;
        $this->data['order'] = $order;

        $template = ($this->config->get('default_admin_view_campaign_grid')) ? $this->config->get('default_admin_view_campaign_grid') : 'marketing/campaign_grid.tpl';
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
            'href' => Url::createAdminUrl('marketing/campaign'),
            'text' => $this->language->get('heading_title'),
            'separator' => ' :: '
        );

        if (isset($this->request->get['campaign_id'])) {
            $this->data['action'] = Url::createAdminUrl('marketing/campaign/update') . "&amp;campaign_id=" . $this->request->get['campaign_id'];
        } else {
            $this->data['action'] = Url::createAdminUrl('marketing/campaign/insert');
        }

        $this->data['cancel'] = Url::createAdminUrl('marketing/campaign');

        $this->data['error_warning'] = isset($this->error['warning']) ? $this->error['warning'] : '';
        $this->data['error_name'] = isset($this->error['name']) ? $this->error['name'] : '';
        $this->data['error_description'] = isset($this->error['description']) ? $this->error['description'] : '';
        $this->data['error_lists'] = isset($this->error['lists']) ? $this->error['lists'] : '';
        $this->data['error_subject'] = isset($this->error['subject']) ? $this->error['subject'] : '';
        $this->data['error_from_name'] = isset($this->error['from_name']) ? $this->error['from_name'] : '';
        $this->data['error_from_email'] = isset($this->error['from_email']) ? $this->error['from_email'] : '';
        $this->data['error_replyto_email'] = isset($this->error['replyto_email']) ? $this->error['replyto_email'] : '';
        $this->data['error_bounce_email'] = isset($this->error['bounce_email']) ? $this->error['bounce_email'] : '';

        if (isset($this->request->get['campaign_id'])) {
            $campaign_info = $this->modelCampaign->getById($this->request->get['campaign_id']);
        } else {
            $campaign_info = null;
        }
        $this->data['lists'] = $this->modelList->getAll();
        $this->data['newsletters'] = $this->modelNewsletter->getAll();
        $this->load->auto('setting/setting');
        foreach ($this->modelSetting->getSetting('mail_server') as $id => $result) {
            $this->data['mail_servers'][$id] = unserialize($result);
        }
        $this->data['mail_server_id'] = $this->modelCampaign->getProperty($this->request->get['campaign_id']??0,'mail_server','mail_server_id');

        $this->setvar('name', $campaign_info, '');
        $this->setvar('subject', $campaign_info, '');
        $this->setvar('from_name', $campaign_info, $this->config->get('config_name'));
        $this->setvar('from_email', $campaign_info, $this->config->get('config_email'));
        $this->setvar('replyto_email', $campaign_info, $this->config->get('config_replyto_email'));
        $this->setvar('bounceto_email', $campaign_info, $this->config->get('config_bounce_email'));

        $dt = new DateTime();
        $dt->setTimezone(new DateTimeZone("America/Caracas"));

        $this->data['start_year'] = $dt->format('Y');
        $this->data['start_month'] = $dt->format('m');
        $this->data['start_day'] = $dt->format('d');
        $this->data['start_hour'] = $dt->format('h');
        $this->data['start_minute'] = $dt->format('i');
        $this->data['start_meridium'] = $dt->format('A');

        $this->data['end_year'] = $dt->format('Y') + 1;
        $this->data['end_month'] = $dt->format('m');
        $this->data['end_day'] = $dt->format('d');
        $this->data['end_hour'] = $dt->format('h');
        $this->data['end_minute'] = $dt->format('i');
        $this->data['end_meridium'] = $dt->format('A');

        $this->data['minutes'] = array('00', '05', '10', '15', '20', '25', '30', '35', '40', '45', '50', '55');

        $scripts[] = array('id' => 'form', 'method' => 'ready', 'script' =>
            "$('#q').on('keyup',function(e){
                var that = this;
                var valor = $(that).val().toLowerCase();
                if (valor.length <= 0) {
                    $('#listsWrapper li').show();
                } else {
                    $('#listsWrapper li b').each(function(){
                        var texto = $(this).text().toLowerCase();
                        if (texto.indexOf( valor ) != -1) {
                            $(this).closest('li').show();
                        } else {
                            $(this).closest('li').hide();
                        }
                    });
                }
            });");

        $this->scripts = array_merge($this->scripts, $scripts);

        $template = ($this->config->get('default_admin_view_campaign_form')) ? $this->config->get('default_admin_view_campaign_form') : 'marketing/campaign_form.tpl';
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
        if (empty($this->request->post['name'])) {
            return false;
        }

        if (empty($this->request->post['subject'])) {
            return false;
        }

        if (!$this->user->hasPermission('modify', 'marketing/campaign')) {
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
        $strProducts = '';
        $products = $this->modelProduct->getAll(array('category_id' => $category_id));
        if ($products) {
            foreach ($products as $product) {
                $strProducts .= "<div id='pid" . $product['product_id'] . "' style='margin:5px;padding:3px;background:#FFF;float:left;border:solid 3px #666;width:150px;height:200px;display:block;text-align:center'>\n";
                $strProducts .= "<p>" . $product['name'] . "</p>";
                if (empty($product['pimage'])) {
                    $strProducts .= "<img src='" . HTTP_IMAGE . "no_image.jpg' width='100' alt='" . $product['name'] . "'>\n";
                } else {
                    $strProducts .= "<img src='" . HTTP_IMAGE . $product['pimage'] . "' width='110' alt='" . $product['name'] . "'>\n";
                }
                $strProducts .= "<input type='hidden' name='" . $product['product_id'] . "' value='" . $product['product_id'] . "'>\n";
                $strProducts .= "<hr><div class='button'><span><b>Arrastrar</b></span></div>";
                $strProducts .= "</div>";
                $strProducts .=
                        "<script>
                $(function() { 
                    $('#pid" . $product['product_id'] . "').draggable({
                        scroll: true,
                        revert: true,
                        start: function() { 
                            $('#pid" . $product['product_id'] . "').after('<input type=\"hidden\" name=\"pid\" id=\"pid\" value=\"" . $product['product_id'] . "\">');
                        },
        				drag: function() {},
        				stop: function() {}
                    });
                });
                </script>";
            }
            echo $strProducts;
        } else {
            echo 'No hay productos en esta categor&iacute;a. <a href="' . HTTP_HOME . 'index.php?r=store/product&token=' . $this->request->get['token'] . '">Le gustar&iacute;a agregar algunos</a>';
        }
    }

    public function send() {
        $htmlbody = html_entity_decode($this->cache->get("campaign.html.temp"));
        $htmlbody = str_replace('%7B', '{', $htmlbody);
        $htmlbody = str_replace('%7D', '}', $htmlbody);
        $data = unserialize($this->cache->get("campaign.data.temp"));

        $to = $data['to'];
        $campaign = $data['post'];
        $links = $data['links'];

        $campaign['contacts'] = $to;

        $campaign_id = $this->modelCampaign->add($campaign);
        $this->modelCampaign->setProperty($campaign_id,'mail_server', 'mail_server_id', $campaign['mail_server_id']);
        $params = array(
            'job' => 'send_campaign',
            'campaign_id' => $campaign_id
        );

        $this->load->library('task');

        $task = new Task($this->registry);

        $task->object_id = (int) $campaign_id;
        $task->object_type = 'campaign';
        $task->task = $campaign['name'];
        $task->type = 'send';
        $task->time_exec = date('Y-m-d H:i:s', strtotime($campaign['date_start']));
        $task->params = $params;
        $task->time_interval = $campaign['repeat'];
        $task->time_last_exec = $row['time_last_exec'];
        $task->run_once = !(bool) $campaign['repeat'];
        $task->status = 1;
        $task->date_start_exec = date('Y-m-d H:i:s', strtotime($campaign['date_start']));
        $task->date_end_exec = date('Y-m-d H:i:s', strtotime($campaign['date_end']));

        foreach ($to as $sort_order => $contact) {
            foreach ($links as $link) {
                if (empty($link['url']) || empty($link['redirect']))
                    continue;
                $link['url'] = str_replace('%7B', '{', $link['url']);
                $link['url'] = str_replace('%7D', '}', $link['url']);
                $link['url'] = str_replace('{%contact_id%}', $contact['contact_id'], $link['url']);
                $link['url'] = str_replace('{%campaign_id%}', $campaign_id, $link['url']);
                $this->modelCampaign->addLink($link, $campaign_id);
            }
            $params = array(
                'contact_id' => $contact['contact_id'],
                'name' => $contact['name'],
                'email' => $contact['email'],
                'campaign_id' => $campaign_id
            );
            $queue = array(
                "params" => $params,
                "status" => 1,
                "time_exec" => date('Y-m-d H:i:s', strtotime($campaign['date_start']))
            );

            $htmlbody = str_replace('{%contact_id%}', $contact['contact_id'], $htmlbody);
            $htmlbody = str_replace('{%campaign_id%}', $campaign_id, $htmlbody);
            $this->cache->set("campaign.html.$campaign_id." . $contact['contact_id'], $htmlbody);

            $task->addQueue($queue);
        }
        $task->createSendTask();
        $this->cache->set("campaign.html.$campaign_id", $htmlbody);

        $this->session->set('success', $this->language->get('text_success'));

        $this->redirect(Url::createAdminUrl("marketing/campaign"));
    }

}
