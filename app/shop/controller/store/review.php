<?php

class ControllerStoreReview extends Controller {

    public function index() {
        $Url = new Url($this->registry);

        //Models
        $this->load->auto('store/review');

        //Libs
        $this->load->auto('pagination');

        $data['review_id'] = $this->request->hasQuery('review_id') ? $this->request->getQuery('review_id') : null;
        $data['parent_id'] = $this->request->hasQuery('parent_id') ? $this->request->getQuery('parent_id') : null;
        $data['object_id'] = $this->request->hasQuery('object_id') ? $this->request->getQuery('object_id') : null;
        $data['object_type'] = $this->request->hasQuery('ot') ? $this->request->getQuery('ot') : null;
        $data['start'] = $this->request->hasQuery('page') ? $this->request->getQuery('page') : 1;
        $data['limit'] = $this->request->hasQuery('limit') ? $this->request->getQuery('limit') : 5;

        $this->data['widgetName'] = $this->request->hasQuery('wid') ? $this->request->getQuery('wid') : null;

        $this->data['reviews'] = [];
        $review_total = $this->modelReview->getAllTotal($data);
        if ($review_total) {
            $results = $this->modelReview->getAll($data);
            foreach ($results as $result) {
                $this->data['reviews'][] = array(
                    'review_id' => $result['review_id'],
                    'product_id' => $result['product_id'],
                    'author' => $result['author'],
                    'rating' => $result['rating'],
                    'likes' => $result['likes'],
                    'dislikes' => $result['dislikes'],
                    'text' => $this->escape($result['text']),
                    'replies' => $this->modelReview->getAll(array('parent_id'=>$result['review_id'])),
                    'isOwner' => ($this->customer->getId() == $result['customer_id']) ? true : null,
                    'stars' => sprintf($this->language->get('text_stars'), $result['rating']),
                    'date_added' => date($this->language->get('date_format_short'), strtotime($result['dateAdded']))
                );
            }
            $this->data['isLogged'] = $this->customer->isLogged();

            $pagination = new Pagination();
            $pagination->total = $review_total;
            $pagination->ajax = true;
            $pagination->ajaxTarget = 'review';
            $pagination->page = $data['start'];
            $pagination->limit = 5;
            $pagination->text = $this->language->get('text_pagination');
            $pagination->url = $Url::createUrl('store/review', array(
                'object_id' => $data['object_id'],
                'object_type' => $data['object_type'],
                'page' => '{page}'
            ));

            $this->data['pagination'] = $pagination->render();
        }

        $template = ($this->config->get('default_view_product_review')) ? $this->config->get('default_view_product_review') : 'store/review.tpl';
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/' . $template)) {
            $this->template = $this->config->get('config_template') . '/' . $template;
        } else {
            $this->template = 'choroni/' . $template;
        }

        $this->response->setOutput($this->render(true), $this->config->get('config_compression'));
    }

    public function comment() {
        $this->data['review_status'] = $this->config->get('config_review');
        $this->data['islogged'] = (bool)$this->customer->islogged();
        $this->data['object_id'] = $this->request->getQuery('object_id');
        $this->data['object_type'] = $this->request->getQuery('ot');
        $this->data['widgetName'] = $this->request->hasQuery('wid') ? $this->request->getQuery('wid') : null;

        $template = ($this->config->get('default_view_product_comment')) ? $this->config->get('default_view_product_comment') : 'store/comment.tpl';
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/' . $template)) {
            $this->template = $this->config->get('config_template') . '/' . $template;
        } else {
            $this->template = 'choroni/' . $template;
        }

        $this->response->setOutput($this->render(true), $this->config->get('config_compression'));
    }

    public function deleteReview() {
        $this->load->auto('store/review');

        $review_id = $this->request->getPost('review_id') ? $this->request->getPost('review_id') : $this->request->getQuery('review_id');
        if ($this->request->server['REQUEST_METHOD'] == 'POST' && $this->customer->islogged() && $review_id) {
            $review = $this->modelReview->getAll(array(
                'review_id'=>$review_id,
                'customer_id'=>$this->customer->getId()
            ));
            $this->modelReview->deleteReview($review[0]['review_id']);
        }
    }

    public function likeReview() {
        $this->load->auto('store/review');

        $review_id = $this->request->getPost('review_id') ? $this->request->getPost('review_id') : $this->request->getQuery('review_id');
        $object_id = $this->request->getPost('object_id') ? $this->request->getPost('object_id') : $this->request->getQuery('object_id');
        $object_type = $this->request->getPost('ot') ? $this->request->getPost('ot') : $this->request->getQuery('ot');

        if ($this->customer->islogged() && $review_id && $object_id) {
            $result = $this->modelReview->likeReview($review_id, $object_id, $object_type);
            $json['likes'] = $result['likes'];
            $json['dislikes'] = $result['dislikes'];
            $json['success'] = 1;
        }
        //TODO: registrar y enviar notificacion de que le gusta 
        $this->load->library('json');
        $this->response->setOutput(Json::encode($json));
    }

    public function dislikeReview() {
        //Models
        $this->load->auto('store/review');

        $review_id = $this->request->getPost('review_id') ? $this->request->getPost('review_id') : $this->request->getQuery('review_id');
        $object_id = $this->request->getPost('object_id') ? $this->request->getPost('object_id') : $this->request->getQuery('object_id');
        $object_type = $this->request->getPost('ot') ? $this->request->getPost('ot') : $this->request->getQuery('ot');

        if ($this->customer->islogged() && $review_id && $object_id) {
            $result = $this->modelReview->dislikeReview($review_id, $object_id, $object_type);
            $json['likes'] = $result['likes'];
            $json['dislikes'] = $result['dislikes'];
            $json['success'] = 1;
        }
        //TODO: registrar y enviar notificacion de que no le gusta 
        $this->load->library('json');
        $this->response->setOutput(Json::encode($json));
    }

    public function write() {
        $this->load->auto('store/review');

        $object_id = $this->request->getPost('object_id') ? $this->request->getPost('object_id') : $this->request->getQuery('object_id');
        $object_type = $this->request->getPost('ot') ? $this->request->getPost('ot') : $this->request->getQuery('ot');
        $json = [];

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {

            $this->request->post['text'] = $this->escape($this->request->post['text']);
            $this->request->post['status'] = (int)$this->config->get('config_review_approve');

            $review_id = $this->modelReview->addReview($object_id, $this->request->post, $object_type);

            $json['review_id'] = $json['author'] = $json['product_id'] = $json['text'] = $json['customer_id'] = $json['date_added'] = '';

            if ($this->config->get('config_review_approve')) {
                $json['review_id'] = $review_id;
                $json['author'] = $this->customer->getFirstName() . " " . $this->customer->getLastName();
                $json['object_id'] = $object_id;
                $json['object_type'] = $object_type;
                $json['text'] = $this->request->post['text'];
                $json['rating'] = $this->request->post['rating'];
                $json['customer_id'] = $this->customer->getId();
                $json['date_added'] = date('d-m-Y h:i A');
                $json['show'] = 1;
            }

            $this->notifyReview($object_id, $object_type);
            $json['success'] = 1;
        } else {
            $json['error'] = 1;
        }
        $this->load->library('json');
        $this->response->setOutput(Json::encode($json));
    }

    public function reply() {
        $this->load->auto('store/review');

        $review_id = $this->request->getPost('review_id') ? $this->request->getPost('review_id') : $this->request->getQuery('review_id');
        $object_id = $this->request->getPost('object_id') ? $this->request->getPost('object_id') : $this->request->getQuery('object_id');
        $object_type = $this->request->getPost('ot') ? $this->request->getPost('ot') : $this->request->getQuery('ot');

        $this->request->post['object_id'] = $object_id;
        $this->request->post['review_id'] = $review_id;
        $json = [];
        $json['success'] = 0;

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateReply()) {
            $this->request->post['text'] = $this->escape($this->request->post['text']);
            $this->request->post['status'] = intval($this->config->get('config_review_approve'));

            $this->modelReview->addReply($this->request->post, $object_type);

            $json['review_id'] = $json['author'] = $json['product_id'] = $json['text'] = $json['customer_id'] = $json['date_added'] = '';
            if ($this->config->get('config_review_approve')) {
                $json['review_id'] = $this->request->post['review_id'];
                $json['author'] = $this->customer->getFirstName() . " " . $this->customer->getLastName();
                $json['object_id'] = $object_id;
                $json['object_type'] = $object_type;
                $json['text'] = $this->request->post['text'];
                $json['customer_id'] = $this->customer->getId();
                $json['date_added'] = date('d-m-Y');
                $json['show'] = 1;
            }

            $this->notifyReview($object_id, $object_type);
            $this->notifyReply($review_id, $object_id, $object_type);

            $json['success'] = 1;
        } else {
            $json['error'] = 1;
        }
        $this->load->library('json');
        $this->response->setOutput(Json::encode($json));
    }

    protected function notifyReview($object_id, $object_type) {
        if (!$object_id || !$object_type) return false;

        $Url = new Url($this->registry);

        $this->load->auto('email/mailer');
        $this->load->auto('store/review');
        $this->load->auto('marketing/newsletter');

        if ($object_type == 'product') {
            $this->load->auto('store/product');
            $object_info = $this->modelProduct->getById($object_id);
            $object_url = $Url::createUrl('store/product', array('product_id' => $object_id));
            $object_name = $object_info['pname'];
        }

        if ($object_type == 'category') {
            $this->load->auto('store/category');
            $object_info = $this->modelCategory->getById($object_id);
            $object_url = $Url::createUrl('store/category', array('category_id' => $object_id));
            $object_name = $object_info['cname'];
        }

        if ($object_type == 'manufacturer') {
            $this->load->auto('store/manufacturer');
            $object_info = $this->modelManufacturer->getManufacturer($object_id);
            $object_url = $Url::createUrl('store/manufacturer', array('manufacturer_id' => $object_id));
            $object_name = $object_info['name'];
        }

        if ($this->config->get('marketing_email_new_comment') && $object_info) {
            $this->load->model("marketing/newsletter");
            $this->load->library('email/mailer');
            $this->load->library('BarcodeQR');

            $mailer = new Mailer;
            $qr = new BarcodeQR;

            $qrStore = "cache/" . md5($this->config->get('config_owner')) . '.png';
            $qrObject = "cache/" . md5($object_url) . '.png';
            $qrStoreImg = $qrObjectImg = '';

            if (!file_exists(DIR_IMAGE . $qrStore)) {
                $qr->url(HTTP_HOME);
                $qr->draw(150, DIR_IMAGE . $qrStore);
                $qrStoreImg = '<img src="' . HTTP_IMAGE . $qrStore . '" alt="QR Code" />';
            }

            if (!file_exists(DIR_IMAGE . $qrObject)) {
                $qr->url($object_url);
                $qr->draw(150, DIR_IMAGE . $qrObject);
                $qrObjectImg = '<img src="' . HTTP_IMAGE . $qrObject . '" alt="QR Code" />';
            }

            $result = $this->modelNewsletter->getById($this->config->get('marketing_email_new_comment'));

            $this->prepareHTMLBody($result['htmlbody'], array(
                'object_url' => $object_url,
                'object_name' => $object_name,
                'object_qrcode' => $qrObjectImg,
                'store_qrcode' => $qrStoreImg,
                'customer_firstname' => $result['firstname'],
                'customer_lastname' => $result['lastname'],
                'customer_company' => $result['company'],
                'customer_email' => $result['email']
            ));

            $subject = $this->config->get('config_owner') . " " . $this->language->get('New Comment');
            $message = $result['htmlbody'];

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

            $reps = $this->modelReview->getAll(array(
                'object_id'=>$object_id,
                'object_type'=>$object_type,
                'groupBy'=>'r.customer_id'
            ));
            $this->load->library('validar');
            $validate = new Validar;
            foreach ($reps as $k => $v) {
                if (!$validate->validEmail($v['email']))
                    continue;
                $mailer->AddBCC($v['email'], $v['author']);
            }

            $mailer->AddBCC($this->config->get('config_email'), $this->config->get('config_name'));
            $mailer->SetFrom($this->config->get('config_email'), $this->config->get('config_name'));
            $mailer->Subject = $subject;
            $mailer->Body = html_entity_decode($message);
            $mailer->Send();
        }
    }

    protected function notifyReply($review_id, $object_id, $object_type) {
        $Url = new Url($this->registry);

        if (!$review_id) return false;

        $this->load->auto('store/review');

        $review_info = $this->modelReview->getById($review_id);

        if ($object_type == 'product') {
            $this->load->auto('store/product');
            $object_info = $this->modelProduct->getById($object_id);
            $object_url = $Url::createUrl('store/product', array('product_id' => $object_id));
            $object_name = $object_info['pname'];
        }

        if ($object_type == 'category') {
            $this->load->auto('store/category');
            $object_info = $this->modelCategory->getById($object_id);
            $object_url = $Url::createUrl('store/category', array('category_id' => $object_id));
            $object_name = $object_info['cname'];
        }

        if ($object_type == 'manufacturer') {
            $this->load->auto('store/manufacturer');
            $object_info = $this->modelManufacturer->getById($object_id);
            $object_url = $Url::createUrl('store/manufacturer', array('manufacturer_id' => $object_id));
            $object_name = $object_info['name'];
        }


        if ($this->config->get('marketing_email_new_reply') && $review_info) {
            $this->load->model("marketing/newsletter");
            $this->load->library('email/mailer');
            $this->load->library('BarcodeQR');
            $this->load->library('Barcode39');
            $mailer = new Mailer;
            $qr = new BarcodeQR;

            $qrStore = "cache/" . md5($this->config->get('config_owner')) . '.png';
            $qrObject = "cache/" . md5($object_url) . '.png';
            $qrStoreImg = $qrObjectImg = '';

            if (!file_exists(DIR_IMAGE . $qrStore)) {
                $qr->url(HTTP_HOME);
                $qr->draw(150, DIR_IMAGE . $qrStore);
                $qrStoreImg = '<img src="' . HTTP_IMAGE . $qrStore . '" alt="QR Code" />';
            }

            if (!file_exists(DIR_IMAGE . $qrObject)) {
                $qr->url($object_url);
                $qr->draw(150, DIR_IMAGE . $qrObject);
                $qrObjectImg = '<img src="' . HTTP_IMAGE . $qrObject . '" alt="QR Code" />';
            }

            $result = $this->modelNewsletter->getById($this->config->get('marketing_email_new_reply'));

            $this->prepareHTMLBody($result['htmlbody'], array(
                'object_url' => $object_url,
                'object_name' => $object_name,
                'object_qrcode' => $qrObjectImg,
                'store_qrcode' => $qrStoreImg,
                'customer_firstname' => $review_info['firstname'],
                'customer_lastname' => $review_info['lastname'],
                'customer_company' => $review_info['company'],
                'customer_email' => $review_info['email']
            ));

            $subject = $this->config->get('config_owner') . " " . $this->language->get('New Reply');
            $message = $result['htmlbody'];

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
            $mailer->AddAddress($review_info['email'], $review_info['firstname'] . " " . $review_info['lastname']);
            $mailer->AddBCC($this->config->get('config_email'), $this->config->get('config_name'));
            $mailer->SetFrom($this->config->get('config_email'), $this->config->get('config_name'));
            $mailer->Subject = $subject;
            $mailer->Body = html_entity_decode($message);
            $mailer->Send();
        }
    }

    private function prepareHTMLBody(&$message, $data)
    {
        $Url = new Url($this->registry);

        $message = str_replace("{%store_logo%}", '<img src="' . HTTP_IMAGE . $this->config->get('config_logo') . '" alt="' . $this->config->get('config_name') . '" />', $message);
        $message = str_replace("{%store_url%}", HTTP_HOME, $message);
        $message = str_replace("{%store_owner%}", $this->config->get('config_owner'), $message);
        $message = str_replace("{%store_name%}", $this->config->get('config_name'), $message);
        $message = str_replace("{%store_rif%}", $this->config->get('config_rif'), $message);
        $message = str_replace("{%store_email%}", $this->config->get('config_email'), $message);
        $message = str_replace("{%store_telephone%}", $this->config->get('config_telephone'), $message);
        $message = str_replace("{%store_address%}", $this->config->get('config_address'), $message);
        $message = str_replace("{%product_url%}", $data['object_url'], $message);
        $message = str_replace("{%url_account%}", $Url::createUrl('account/review'), $message);
        $message = str_replace("{%product_name%}", $data['object_name'], $message);
        $message = str_replace("{%fullname%}", $data['customer_firstname'] . " " . $data['customer_lastname'], $message);
        $message = str_replace("{%company%}", $data['customer_company'], $message);
        $message = str_replace("{%email%}", $data['customer_email'], $message);
        $message = str_replace("{%qr_code_store%}", $data['store_qrcode'], $message);
        $message = str_replace("{%qr_code_product%}", $data['object_qrcode'], $message);
        $message = str_replace("{%barcode_39_order_id%}", $data['order_barcode_39'], $message);

        $message .= "<p style=\"text-align:center\">Powered By <a href=\"https://necotienda.necoyoad.com\">Necotienda</a>&reg; " . date('Y') . "</p>";
    }

    private function validate() {
        if (!$this->customer->islogged()) {
            $this->error['message'] = $this->language->get('error_login');
        }

        if (!$this->request->hasPost('object_id') && !$this->request->hasQuery('object_id')) {
            $this->error['message'] = $this->language->get('error_product');
        }

        if (!$this->request->hasPost('ot') && !$this->request->hasQuery('ot')) {
            $this->error['message'] = $this->language->get('error_object_type');
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

    private function validateReply() {
        if (!$this->customer->islogged()) {
            $this->error['message'] = $this->language->get('error_login');
        }

        if (!$this->request->hasPost('object_id') && !$this->request->hasQuery('object_id')) {
            $this->error['message'] = $this->language->get('error_product');
        }

        if (!$this->request->hasPost('ot') && !$this->request->hasQuery('ot')) {
            $this->error['message'] = $this->language->get('error_object_type');
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

    private function escape($text) {
        $text = strip_tags($text);
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
        $text = html_entity_decode($text);
        $text = htmlentities($text);
        return $text;
    }

}
