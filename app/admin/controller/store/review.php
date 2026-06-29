<?php

class ControllerStoreReview extends Controller {

    private $error = [];

    public function index() {
        $this->document->title = $this->language->get('heading_title');
        $this->getList();
    }

    public function insert() {
        $this->document->title = $this->language->get('heading_title');
        if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
            $this->request->post['object_type'] = $this->request->getQuery('object_type');
            $review_id = $this->modelReview->add($this->request->post);

            $this->session->set('success', $this->language->get('text_success'));

            if ($_POST['to'] == "saveAndKeep") {
                $this->redirect(Url::createAdminUrl('store/review/update', array('review_id' => $review_id)));
            } elseif ($_POST['to'] == "saveAndNew") {
                $this->redirect(Url::createAdminUrl('store/review/insert'));
            } else {
                $this->redirect(Url::createAdminUrl('store/review'));
            }
        }

        $this->getForm();
    }

    public function update() {
        $this->document->title = $this->language->get('heading_title');
        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $this->request->post['object_type'] = $this->request->getQuery('object_type');
            $this->modelReview->update($this->request->get['review_id'], $this->request->post);

            $this->session->set('success', $this->language->get('text_success'));

            if ($_POST['to'] == "saveAndKeep") {
                $this->redirect(Url::createAdminUrl('store/review/update', array('review_id' => $this->request->get['review_id'])));
            } elseif ($_POST['to'] == "saveAndNew") {
                $this->redirect(Url::createAdminUrl('store/review/insert'));
            } else {
                $this->redirect(Url::createAdminUrl('store/review'));
            }
        }

        $this->getForm();
    }

    /**
     * ControllerStoreReview::delete()
     * elimina un objeto
     * @return boolean
     * */
    public function delete() {
        $this->load->auto('store/review');
        if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
            foreach ($this->request->post['selected'] as $id) {
                $this->modelReview->delete($id);
            }
        } else {
            $this->modelReview->delete($_GET['id']);
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
            'href' => Url::createAdminUrl('store/review') . $url,
            'text' => $this->language->get('heading_title'),
            'separator' => ' :: '
        );

        $this->data['insert'] = Url::createAdminUrl('store/review/insert') . $url;
        $this->data['delete'] = Url::createAdminUrl('store/review/delete') . $url;

        $this->data['heading_title'] = $this->language->get('heading_title');
        $this->data['button_insert'] = $this->language->get('button_insert');
        $this->data['button_delete'] = $this->language->get('button_delete');

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
        $scripts[] = array('id' => 'reviewList', 'method' => 'function', 'script' =>
            "function activate(e) {
                $.getJSON('" . Url::createAdminUrl("store/review/activate") . "',{
                    id:e
                },function(data){
                    if (data > 0) {
                        $('#img_' + e).attr('src','image/good.png');
                    } else {
                        $('#img_' + e).attr('src','image/minus.png');
                    }
                });
            }
            function deleteAll() {
                if (confirm('\\xbfDesea eliminar todos los objetos seleccionados?')) {
                    $('#gridWrapper').hide();
                    $('#gridPreloader').show();
                    $.post('" . Url::createAdminUrl("store/review/delete") . "',$('#form').serialize(),function(){
                        $('#gridWrapper').load('" . Url::createAdminUrl("store/review/grid") . "',function(){
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
                	$.getJSON('" . Url::createAdminUrl("store/review/delete") . "',{
                        id:e
                    });
                }
                return false;
             }");
        $scripts[] = array('id' => 'sortable', 'method' => 'ready', 'script' =>
            "$('#gridWrapper').load('" . Url::createAdminUrl("store/review/grid") . "',function(e){
                $('#gridPreloader').hide();
                $('#list tbody').sortable({
                    opacity: 0.6, 
                    cursor: 'move',
                    handle: '.move',
                    update: function() {
                        $.ajax({
                            'type':'post',
                            'dateType':'json',
                            'url':'" . Url::createAdminUrl("store/review/sortable") . "',
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
                url:'" . Url::createAdminUrl("store/review/grid") . "',
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

        $template = ($this->config->get('default_admin_view_store_review_list')) ? $this->config->get('default_admin_view_store_review_list') : 'store/review_list.tpl';
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

        $filter_customer_id = isset($this->request->get['filter_customer_id']) ? $this->request->get['filter_customer_id'] : null;
        $filter_author = isset($this->request->get['filter_author']) ? $this->request->get['filter_author'] : null;
        $filter_product = isset($this->request->get['filter_product']) ? $this->request->get['filter_product'] : null;
        $filter_date_start = isset($this->request->get['filter_date_start']) ? $this->request->get['filter_date_start'] : null;
        $filter_date_end = isset($this->request->get['filter_date_end']) ? $this->request->get['filter_date_end'] : null;
        $page = isset($this->request->get['page']) ? $this->request->get['page'] : 1;
        $sort = isset($this->request->get['sort']) ? $this->request->get['sort'] : 'r.date_added';
        $order = isset($this->request->get['order']) ? $this->request->get['order'] : 'ASC';
        $limit = !empty($this->request->get['limit']) ? $this->request->get['limit'] : $this->config->get('config_admin_limit');

        $url = '';

        if (isset($this->request->get['filter_customer_id'])) {
            $url .= '&filter_customer_id=' . $this->request->get['filter_customer_id'];
        }
        if (isset($this->request->get['filter_author'])) {
            $url .= '&filter_author=' . $this->request->get['filter_author'];
        }
        if (isset($this->request->get['filter_product'])) {
            $url .= '&filter_product=' . $this->request->get['filter_product'];
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

        $this->data['reviews'] = [];

        $data = array(
            'filter_customer_id' => $filter_customer_id,
            'filter_author' => $filter_author,
            'filter_product' => $filter_product,
            'filter_date_start' => $filter_date_start,
            'filter_date_end' => $filter_date_end,
            'sort' => $sort,
            'order' => $order,
            'start' => ($page - 1) * $limit,
            'limit' => $limit
        );

        $review_total = $this->modelReview->getAllTotal($data);

        if ($review_total) {
            $results = $this->modelReview->getAll($data);
        $i = str_replace('%theme%',$this->config->get('config_admin_template'),HTTP_ADMIN_THEME_IMAGE);
            foreach ($results as $result) {
                $action = array(
                    'activate' => array(
                        'action' => 'activate',
                        'text' => $this->language->get('text_activate'),
                        'href' => '',
                        'img' => $i .'good.png'
                    ),
                    'edit' => array(
                        'action' => 'edit',
                        'text' => $this->language->get('text_edit'),
                        'href' => Url::createAdminUrl('store/review/update') . '&review_id=' . $result['rid'] . '&amp;object_type=' . $result['object_type'] . $url,
                        'img' =>  $i .'edit.png'
                    ),
                    'delete' => array(
                        'action' => 'delete',
                        'text' => $this->language->get('text_delete'),
                        'href' => '',
                        'img' => $i .'delete.png'
                    )
                );

                if (in_array($result['object_type'], array('product','category','manufacturer','store'))) {
                    $object_url = 'store/'. $result['object_type'] .'/see';
                } elseif (in_array($result['object_type'], array('post','post_category','page'))) {
                    $object_url = 'content/'. $result['object_type'] .'/see';
                } elseif (in_array($result['object_type'], array('customer'))) {
                    $object_url = 'sale/'. $result['object_type'] .'/see';
                } elseif (in_array($result['object_type'], array('user'))) {
                    $object_url = 'user/'. $result['object_type'] .'/see';
                }

                $this->data['reviews'][] = array(
                    'review_id' => $result['rid'],
                    'object_url' => Url::createAdminUrl($object_url) . '&amp;'. $result['object_type'] .'_id=' . $result['object_id'],
                    'object_type' => $result['object_type'],
                    'name' => $result['name'],
                    'author' => $result['author'],
                    'rating' => $result['rating'],
                    'text' => $result['text'],
                    'status' => ($result['rstatus'] == 1) ? $this->language->get('text_enabled') : $this->language->get('text_disabled'),
                    'date_added' => date('d-m-Y h:i:s', strtotime($result['created'])),
                    'selected' => isset($this->request->post['selected']) && in_array($result['rid'], $this->request->post['selected']),
                    'action' => $action
                );
            }
        }
        $this->data['text_no_results'] = $this->language->get('text_no_results');

        $this->data['column_product'] = $this->language->get('column_product');
        $this->data['column_author'] = $this->language->get('column_author');
        $this->data['column_rating'] = $this->language->get('column_rating');
        $this->data['column_status'] = $this->language->get('column_status');
        $this->data['column_date_added'] = $this->language->get('column_date_added');
        $this->data['column_action'] = $this->language->get('column_action');

        $url = '';

        if ($order == 'ASC') {
            $url .= '&order=DESC';
        } else {
            $url .= '&order=ASC';
        }

        if (isset($this->request->get['filter_author'])) {
            $url .= '&filter_author=' . $this->request->get['filter_author'];
        }
        if (isset($this->request->get['filter_product'])) {
            $url .= '&filter_product=' . $this->request->get['filter_product'];
        }
        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }
        if (!empty($this->request->get['limit'])) {
            $url .= '&limit=' . $this->request->get['limit'];
        }

        $this->data['sort_product'] = Url::createAdminUrl('store/review/grid') . '&sort=pd.name' . $url;
        $this->data['sort_author'] = Url::createAdminUrl('store/review/grid') . '&sort=r.author' . $url;
        $this->data['sort_rating'] = Url::createAdminUrl('store/review/grid') . '&sort=r.rating' . $url;
        $this->data['sort_status'] = Url::createAdminUrl('store/review/grid') . '&sort=r.status' . $url;
        $this->data['sort_date_added'] = Url::createAdminUrl('store/review/grid') . '&sort=r.date_added' . $url;

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
        $pagination->total = $review_total;
        $pagination->page = $page;
        $pagination->limit = $limit;
        $pagination->text = $this->language->get('text_pagination');
        $pagination->url = Url::createAdminUrl('store/review/grid') . $url . '&page={page}';

        $this->data['pagination'] = $pagination->render();

        $this->data['sort'] = $sort;
        $this->data['order'] = $order;

        $template = ($this->config->get('default_admin_view_store_review_grid')) ? $this->config->get('default_admin_view_store_review_grid') : 'store/review_grid.tpl';
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_admin_template') . '/'. $template)) {
            $this->template = $this->config->get('config_admin_template') . '/' . $template;
        } else {
            $this->template = 'default/' . $template;
        }

        $this->response->setOutput($this->render(true), $this->config->get('config_compression'));
    }

    private function getForm() {
        $this->data['error_warning'] = ($this->error['warning']) ? $this->error['warning'] : '';
        $this->data['error_product'] = ($this->error['product']) ? $this->error['product'] : '';
        $this->data['error_author'] = ($this->error['author']) ? $this->error['author'] : '';
        $this->data['error_text'] = ($this->error['text']) ? $this->error['text'] : '';
        $this->data['error_rating'] = ($this->error['rating']) ? $this->error['rating'] : '';
        $this->data['error_warning'] = ($this->error['warning']) ? $this->error['warning'] : '';

        $this->document->breadcrumbs = [];

        $this->document->breadcrumbs[] = array(
            'href' => Url::createAdminUrl('common/home'),
            'text' => $this->language->get('text_home'),
            'separator' => false
        );

        $this->document->breadcrumbs[] = array(
            'href' => Url::createAdminUrl('store/review') . $url,
            'text' => $this->language->get('heading_title'),
            'separator' => ' :: '
        );

        if (!isset($this->request->get['review_id'])) {
            $this->data['action'] = Url::createAdminUrl('store/review/insert') . $url;
        } else {
            $this->data['action'] = Url::createAdminUrl('store/review/update') . '&review_id=' . $this->request->get['review_id'] . $url;
        }

        $this->data['cancel'] = Url::createAdminUrl('store/review') . $url;

        if (isset($this->request->get['review_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
            $review_info = $this->modelReview->getById($this->request->get['review_id']);
        }

        $this->setvar('author', $review_info, '');
        $this->setvar('text', $review_info, '');
        $this->setvar('rating', $review_info, '');
        $this->setvar('status', $review_info, '');
        $this->setvar('parent_id', $review_info, '');
        $this->setvar('object_id', $review_info, '');
        $this->setvar('object_type', $review_info, '');
        $this->data['review_id'] = $this->request->get['review_id'];

        $this->data['object_name'] = 'to see '. $review_info['object_type'];

        if (in_array($review_info['object_type'], array('product','category','manufacturer','store'))) {
            $this->data['object_url'] = 'store/'. $review_info['object_type'] .'/see';
        } elseif (in_array($review_info['object_type'], array('post','post_category','page'))) {
            $this->data['object_url'] = 'content/'. $review_info['object_type'] .'/see';
        } elseif (in_array($review_info['object_type'], array('customer'))) {
            $this->data['object_url'] = 'sale/'. $review_info['object_type'] .'/see';
        } elseif (in_array($review_info['object_type'], array('user'))) {
            $this->data['object_url'] = 'user/'. $review_info['object_type'] .'/see';
        }

        if ($this->request->hasQuery('review_id') && $this->request->getQuery('review_id') > 0)
            $this->data['replies'] = $this->modelReview->getReplies($this->request->get['review_id']);

        $template = ($this->config->get('default_admin_view_store_review_form')) ? $this->config->get('default_admin_view_store_review_form') : 'store/review_form.tpl';
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

    public function reply() {
        //Languages
        $this->language->load('store/review');

        //Models
        $this->load->auto('store/review');

        $this->request->post['object_id'] = $this->request->getPost('object_id') ? $this->request->getPost('object_id') : $this->request->getQuery('object_id');
        $this->request->post['object_type'] = $this->request->getPost('object_type') ? $this->request->getPost('object_type') : $this->request->getQuery('object_type');
        $this->request->post['review_id'] = $this->request->getPost('review_id') ? $this->request->getPost('review_id') : $this->request->getQuery('review_id');
        $json = [];
        $json['success'] = 0;
        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateReply()) {

            $text = strip_tags($this->request->post['text']);
            $text = urldecode($text);
            $text = html_entity_decode($text);
            $text = preg_replace('/<head\b[^>]*>(.*?)<\/head>/is', ' [CONTENIDO ELIMINADO POR SEGURIDAD] ', $text);
            $text = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', ' [CONTENIDO ELIMINADO POR SEGURIDAD] ', $text);
            $text = preg_replace('/<iframe\b[^>]*>(.*?)<\/iframe>/is', ' [CONTENIDO ELIMINADO POR SEGURIDAD] ', $text);
            $text = preg_replace('/<object\b[^>]*>(.*?)<\/object>/is', ' [CONTENIDO ELIMINADO POR SEGURIDAD] ', $text);
            $text = preg_replace('/<embed\b[^>]*>(.*?)<\/embed>/is', ' [CONTENIDO ELIMINADO POR SEGURIDAD] ', $text);
            $text = preg_replace('/<applet\b[^>]*>(.*?)<\/applet>/is', ' [CONTENIDO ELIMINADO POR SEGURIDAD] ', $text);
            $text = preg_replace('/<frame\b[^>]*>(.*?)<\/frame>/is', ' [CONTENIDO ELIMINADO POR SEGURIDAD] ', $text);
            $text = preg_replace('/<noscript\b[^>]*>(.*?)<\/noscript>/is', ' [CONTENIDO ELIMINADO POR SEGURIDAD] ', $text);
            $text = preg_replace('/<noembed\b[^>]*>(.*?)<\/noembed>/is', ' [CONTENIDO ELIMINADO POR SEGURIDAD] ', $text);
            $this->request->post['text'] = htmlentities($text);
            $this->modelReview->addReply($this->request->post);

            $json['review_id'] = $json['author'] = $json['product_id'] = $json['text'] = $json['date_added'] = '';
            if ($this->config->get('config_review_approve')) {
                $json['review_id'] = $this->request->post['review_id'];
                $json['author'] = $this->user->getUserName();
                $json['product_id'] = $this->request->post['product_id'];
                $json['text'] = $this->request->post['text'];
                $json['date_added'] = date('d-m-Y');
            }

            $this->notifyReview($this->request->post['product_id']);

            $json['success'] = $this->language->get('text_success');
        }
        $this->load->library('json');
        $this->response->setOutput(Json::encode($json));
    }

    protected function notifyReview($product_id) {
        if (!$product_id)
            return false;
        $this->load->auto('email/mailer');
        $this->load->auto('store/product');
        $this->load->auto('store/review');
        $this->load->auto('content/page');

        $product_info = $this->modelProduct->getById($product_id);
        if ($product_info) {
            $page = $this->modelPage->getPage($this->config->get('config_email_new_comment'));
            if ($page->num_rows) {
                $subject = $page['title'];
                $message = str_replace("{%product_url%}", Url::createUrl('store/product', array('product_id' => $product_id)), $page['description']);
                $message = str_replace("{%product_name%}", $product_info['name'], $message);

                $mailer = new Mailer;
                $reps = $this->modelReview->getAll(array(
                    'object_id'=>$product_id,
                    'object_type'=>'product'
                ));
                foreach ($reps as $k => $v) {
                    $mailer->AddBCC($v['email'], $v['author']);
                }

                if ($this->config->get('config_smtp_method') == 'smtp') {
                    $mailer->IsSMTP();
                    $mailer->Host = $this->config->get('config_smtp_host');
                    $mailer->Username = $this->config->get('config_smtp_username');
                    $mailer->Password = base64_decode($this->config->get('config_smtp_password'));
                    $mailer->Port = $this->config->get('config_smtp_port');
                    $mailer->Timeout = $this->config->get('config_smtp_timeout');
                    $mailer->SMTPSecure = $this->config->get('config_smtp_ssl');
                    $mailer->SMTPAuth = ($this->config->get('config_smtp_auth')) ? true : false;
                } elseif ($this->config->get('config_smtp_method') == 'sendmail') {
                    $mailer->IsSendmail();
                } else {
                    $mailer->IsMail();
                }
                $mailer->IsHTML();
                $mailer->SetFrom($this->config->get('config_email'), $this->config->get('config_name'));
                $mailer->Subject = $subject;
                $mailer->Body = $message;
                $mailer->Send();
            }
        }
    }

    private function validateReply() {
        if (!$this->request->hasPost('object_id') && !$this->request->hasQuery('object_id')) {
            $this->error['message'] = $this->language->get('error_product');
        }

        if (!$this->request->hasPost('review_id') && !$this->request->hasQuery('review_id')) {
            $this->error['message'] = $this->language->get('error_review');
        }

        if (empty($this->request->post['text'])) {
            $this->error['message'] = $this->language->get('error_text');
        }

        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }

    private function validateForm() {
        if (!$this->user->hasPermission('modify', 'store/review')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (!$this->request->post['product_id']) {
            $this->error['product'] = $this->language->get('error_product');
        }

        if (!$this->request->post['author']) {
            $this->error['author'] = $this->language->get('error_author');
        }

        if (!$this->request->post['text']) {
            $this->error['text'] = $this->language->get('error_text');
        }

        if (!$this->request->post['rating']) {
            $this->error['rating'] = $this->language->get('error_rating');
        }

        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }

    private function validateDelete() {
        if (!$this->user->hasPermission('modify', 'store/review')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * ControllerStoreReview::activate()
     * activar o desactivar un objeto accedido por ajax
     * @return boolean
     * */
    public function activate() {
        if (!isset($_GET['id']))
            return false;
        $this->load->auto('store/review');
        $status = $this->modelReview->getById($_GET['id']);
        if ($status) {
            if ($status['status'] == 0) {
                $this->modelReview->activate($_GET['id']);
                echo 1;
            } else {
                $this->modelReview->deactivate($_GET['id']);
                echo -1;
            }
        } else {
            echo 0;
        }
    }

    /**
     * ControllerStoreReview::sortable()
     * ordenar el listado actualizando la posici�n de cada objeto
     * @return boolean
     * */
    public function sortable() {
        if (!isset($_POST['tr']))
            return false;
        $this->load->auto('store/review');
        $result = $this->modelReview->sortReview($_POST['tr']);
        if ($result) {
            echo 1;
        } else {
            echo 0;
        }
    }

}
