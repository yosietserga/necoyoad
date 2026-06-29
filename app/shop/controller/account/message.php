<?php

class ControllerAccountMessage extends Controller {

    private $error = [];

    public function index() {
        $this->session->clear('object_type');
        $this->session->clear('object_id');
        $this->session->clear('landing_page');

        $Url = new Url($this->registry);
        if (!$this->customer->isLogged()) {
            $this->session->set('redirect', $Url::createUrl("account/message"));
            $this->redirect($Url::createUrl("account/login"));
        }

        $this->language->load('account/message');
        $this->load->model('account/message');

        $this->document->breadcrumbs = [];
        $this->document->breadcrumbs[] = array(
            'href' => $Url::createUrl("common/home"),
            'text' => $this->language->get('text_home'),
            'separator' => false
        );
        $this->document->breadcrumbs[] = array(
            'href' => $Url::createUrl("account/message"),
            'text' => $this->language->get('text_message'),
            'separator' => $this->language->get('text_separator')
        );
        $this->data['breadcrumbs'] = $this->document->breadcrumbs;

        $this->document->title = $this->data['heading_title'] = $this->language->get('heading_title_inbounce');

        $page = ($this->request->get['page']) ? $this->request->get['page'] : 1;
        $data['sort'] = $sort = ($this->request->get['sort']) ? $this->request->get['sort'] : 'm.date_added';
        $data['order'] = $order = ($this->request->get['order']) ? $this->request->get['order'] : 'ASC';
        $data['limit'] = $limit = ($this->request->get['limit']) ? $this->request->get['limit'] : 25;
        $data['keyword'] = ($this->request->get['keyword']) ? $this->request->get['keyword'] : null;
        $data['letter'] = ($this->request->get['letter']) ? $this->request->get['letter'] : null;
        $data['status'] = ($this->request->get['status']) ? $this->request->get['status'] : null;
        $data['start'] = ($page - 1) * $limit;

        $url = '';

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }
        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }
        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }
        if (isset($this->request->get['limit'])) {
            $url .= '&limit=' . $this->request->get['limit'];
        }

        $this->data['letters'] = range('A', 'Z');

        $message_total = $this->modelMessage->getTotalInbounceMessagesByCustomerId($this->customer->getId(), $data);

        if ($message_total) {

            $messages = $this->modelMessage->getInbounceMessagesByCustomerId($this->customer->getId(), $data);
            foreach ($messages as $key => $value) {
                if ($value['mstatus'] == 1) {
                    $status == 'Le&iacute;do';
                } elseif ($value['mstatus'] == -1) {
                    $status == 'No Deseado';
                } else {
                    $status == 'No Le&iacute;do';
                }
                $this->data['messages'][] = array(
                    'message_id' => $value['message_id'],
                    'subject' => $value['subject'],
                    'company' => $value['company'],
                    'status' => $status,
                    'date_added' => date('d/m/Y h:i A', strtotime($value['created'])),
                    'message' => substr($value['message'], 0, 130) . "...",
                    'href' => Url::createUrl("account/message/read", array("message_id" => $value['message_id']))
                );
            }

            $this->load->library('pagination');
            $pagination = new Pagination(true);
            $pagination->total = $message_total;
            $pagination->page = $page;
            $pagination->limit = $limit;
            $pagination->text = $this->language->get('text_pagination');
            $pagination->url = Url::createUrl('account/message') . $url . '&page={page}';
            $this->data['pagination'] = $pagination->render();
        }
        if (isset($this->error['warning'])) {
            $this->data['error_warning'] = $this->error['warning'];
        } else {
            $this->data['error_warning'] = '';
        }

        $this->data['action'] = Url::createUrl("account/message");

        $this->session->set('landing_page','account/message');
        $this->loadWidgets('featuredContent');
        $this->loadWidgets('main');
        $this->loadWidgets('featuredFooter');

        $this->addChild('account/column_left');
            $this->addChild('common/column_left');
            $this->addChild('common/column_right');
            $this->addChild('common/header');
            $this->addChild('common/footer');


        $template = ($this->config->get('default_view_account_message')) ? $this->config->get('default_view_account_message') : 'account/message.tpl';
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/' . $template)) {
            $this->template = $this->config->get('config_template') . '/' . $template;
        } else {
            $this->template = 'choroni/' . $template;
        }

        $this->response->setOutput($this->render(TRUE), $this->config->get('config_compression'));
    }

    public function sent() {
        $Url = new Url($this->registry);
        $this->session->clear('object_type');
        $this->session->clear('object_id');
        $this->session->clear('landing_page');

        if (!$this->customer->isLogged()) {
            $this->session->set('redirect', Url::createUrl("account/message"));
            $this->redirect(Url::createUrl("account/login"));
        }

        $this->language->load('account/message');
        $this->load->model('account/message');

        $this->document->breadcrumbs = [];

        $this->document->breadcrumbs[] = array(
            'href' => Url::createUrl("common/home"),
            'text' => $this->language->get('text_home'),
            'separator' => false
        );

        $this->document->breadcrumbs[] = array(
            'href' => Url::createUrl("account/message"),
            'text' => $this->language->get('text_message'),
            'separator' => $this->language->get('text_separator')
        );

        $this->document->breadcrumbs[] = array(
            'href' => Url::createUrl("account/message/sent"),
            'text' => $this->language->get('text_message'),
            'separator' => $this->language->get('text_separator')
        );

        $this->document->title = $this->data['heading_title'] = $this->language->get('heading_title_outbounce');

        $page = ($this->request->get['page']) ? $this->request->get['page'] : 1;
        $data['sort'] = $sort = ($this->request->get['sort']) ? $this->request->get['sort'] : 'm.date_added';
        $data['order'] = $order = ($this->request->get['order']) ? $this->request->get['order'] : 'ASC';
        $data['limit'] = $limit = ($this->request->get['limit']) ? $this->request->get['limit'] : 25;
        $data['keyword'] = ($this->request->get['keyword']) ? $this->request->get['keyword'] : null;
        $data['letter'] = ($this->request->get['letter']) ? $this->request->get['letter'] : null;
        $data['start'] = ($page - 1) * $limit;

        $url = '';

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }
        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }
        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }
        if (isset($this->request->get['limit'])) {
            $url .= '&limit=' . $this->request->get['limit'];
        }

        $this->data['letters'] = range('A', 'Z');

        $message_total = $this->modelMessage->getTotalOutbounceMessagesByCustomerId($this->customer->getId(), $data);

        if ($message_total) {

            $messages = $this->modelMessage->getOutbounceMessagesByCustomerId($this->customer->getId(), $data);
            foreach ($messages as $key => $value) {
                if ($value['mstatus'] == 1) {
                    $status == 'Le&iacute;do';
                } elseif ($value['mstatus'] == -1) {
                    $status == 'No Deseado';
                } else {
                    $status == 'No Le&iacute;do';
                }
                $this->data['messages'][] = array(
                    'message_id' => $value['message_id'],
                    'subject' => $value['subject'],
                    'company' => $value['company'],
                    'date_added' => date('d/m/Y h:i A', strtotime($value['created'])),
                    'message' => substr($value['message'], 0, 130) . "...",
                    'href' => Url::createUrl("account/message/read", array("message_id" => $value['message_id']))
                );
            }

            $this->load->library('pagination');
            $pagination = new Pagination(true);
            $pagination->total = $message_total;
            $pagination->page = $page;
            $pagination->limit = $limit;
            $pagination->text = $this->language->get('text_pagination');
            $pagination->url = Url::createUrl('account/message/sent') . $url . '&page={page}';
            $this->data['pagination'] = $pagination->render();
        }
        if (isset($this->error['warning'])) {
            $this->data['error_warning'] = $this->error['warning'];
        } else {
            $this->data['error_warning'] = '';
        }

        $this->data['action'] = $Url::createUrl("account/message");

        $this->session->set('landing_page','account/message/sent');
        $this->loadWidgets('featuredContent');
        $this->loadWidgets('main');
        $this->loadWidgets('featuredFooter');

        $this->addChild('account/column_left');
            $this->addChild('common/column_left');
            $this->addChild('common/column_right');
            $this->addChild('common/header');
            $this->addChild('common/footer');



        $template = ($this->config->get('default_view_account_message_sent')) ? $this->config->get('default_view_account_message_sent') : 'account/message_sent.tpl';
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/' . $template)) {
            $this->template = $this->config->get('config_template') . '/' . $template;
        } else {
            $this->template = 'choroni/' . $template;
        }

        $this->response->setOutput($this->render(TRUE), $this->config->get('config_compression'));
    }

    public function upload() {
        //TODO: attachments
    }

    /**
     * ControllerStoreCategory::delete()
     * elimina un objeto
     * @return boolean
     * */
    public function delete() {
        $this->load->auto('account/message');
        if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
            foreach ($this->request->post['selected'] as $id) {
                $this->modelMessage->delete($id, $this->customer->getId());
            }
        } else {
            $this->modelMessage->delete($_GET['id'], $this->customer->getId());
        }
    }

    public function create() {
        $this->session->clear('object_type');
        $this->session->clear('object_id');
        $this->session->clear('landing_page');

        if (!$this->customer->isLogged()) {
            $this->session->set('redirect', Url::createUrl("account/message"));
            $this->redirect(Url::createUrl("account/login"));
        }

        $this->language->load('account/message');
        $this->load->model('account/message');

        $this->document->breadcrumbs = [];
        $this->document->breadcrumbs[] = array(
            'href' => Url::createUrl("common/home"),
            'text' => $this->language->get('text_home'),
            'separator' => false
        );
        $this->document->breadcrumbs[] = array(
            'href' => Url::createUrl("account/message"),
            'text' => $this->language->get('text_message'),
            'separator' => $this->language->get('text_separator')
        );
        $this->document->breadcrumbs[] = array(
            'href' => Url::createUrl("account/message/create"),
            'text' => $this->language->get('text_create_message'),
            'separator' => $this->language->get('text_separator')
        );

        $this->document->title = $this->data['heading_title'] = $this->language->get('heading_title_create');

        $this->data['entry_to'] = $this->language->get('entry_to');
        $this->data['entry_subject'] = $this->language->get('entry_subject');
        $this->data['entry_message'] = $this->language->get('entry_message');

        $this->data['action'] = Url::createUrl("account/message/send");

        $this->session->set('landing_page','account/message/create');
        $this->loadWidgets('featuredContent');
        $this->loadWidgets('main');
        $this->loadWidgets('featuredFooter');

        $this->addChild('account/column_left');
            $this->addChild('common/column_left');
            $this->addChild('common/column_right');
            $this->addChild('common/header');
            $this->addChild('common/footer');



        $template = ($this->config->get('default_view_account_message_create')) ? $this->config->get('default_view_account_message_create') : 'account/message_create.tpl';
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/' . $template)) {
            $this->template = $this->config->get('config_template') . '/' . $template;
        } else {
            $this->template = 'choroni/' . $template;
        }

        $this->response->setOutput($this->render(TRUE), $this->config->get('config_compression'));
    }

    public function read() {
        $this->session->clear('object_type');
        $this->session->clear('object_id');
        $this->session->clear('landing_page');

        if (!$this->customer->isLogged()) {
            $this->session->set('redirect', Url::createUrl("account/message"));
            $this->redirect(Url::createUrl("account/login"));
        }

        $this->language->load('account/message');
        $this->load->model('account/message');
        $this->load->model('account/customer');
        $this->load->library('url');
        $this->data['Url'] = new Url($this->registry);

        $this->document->breadcrumbs = [];

        $this->document->breadcrumbs[] = array(
            'href' => Url::createUrl("common/home"),
            'text' => $this->language->get('text_home'),
            'separator' => false
        );

        $this->document->breadcrumbs[] = array(
            'href' => Url::createUrl("account/message"),
            'text' => $this->language->get('text_message'),
            'separator' => $this->language->get('text_separator')
        );

        $this->document->breadcrumbs[] = array(
            'href' => Url::createUrl("account/message/read", array("message_id" => $this->request->get['message_id'])),
            'text' => $this->language->get('text_message') . $this->request->get['message_id'],
            'separator' => $this->language->get('text_separator')
        );

        $this->document->title = $this->data['heading_title'] = $this->language->get('heading_title');

        $message_id = $this->request->get['message_id'];

        if ((int) $message_id) {
            $this->data['heading_title'] = "Mensaje #" . $message_id;
            $this->data['message'] = $this->modelMessage->getInbounceMessagesById($message_id);
            $this->data['entry_to'] = $this->language->get('entry_to');
            $this->data['entry_subject'] = $this->language->get('entry_subject');
            $this->data['entry_message'] = $this->language->get('entry_message');
        }

        $this->data['action'] = Url::createUrl("account/message/reply");

        $this->session->set('landing_page','account/message/read');
        $this->loadWidgets('featuredContent');
        $this->loadWidgets('main');
        $this->loadWidgets('featuredFooter');

        $this->addChild('account/column_left');
            $this->addChild('common/column_left');
            $this->addChild('common/column_right');
            $this->addChild('common/header');
            $this->addChild('common/footer');



        $template = ($this->config->get('default_view_account_message_read')) ? $this->config->get('default_view_account_message_read') : 'account/message_read.tpl';
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/' . $template)) {
            $this->template = $this->config->get('config_template') . '/' . $template;
        } else {
            $this->template = 'choroni/' . $template;
        }

        $this->response->setOutput($this->render(TRUE), $this->config->get('config_compression'));
    }

    public function reply() {
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . "GMT");
        header('Cache-Control: no-cache, must-revalidate');
        header("Pragma: no-cache");
        header("Content-type: application/json");

        $this->load->model('account/message');
        $this->load->library('json');
        $this->session->set("success", "Su mensaje ha sido enviado");
        $data = [];
        if ($this->request->server['REQUEST_METHOD'] == 'POST' && $this->validateReply()) {
            $this->load->model('account/message');
            $this->modelMessage->setFrom($this->customer->getId());
            $this->modelMessage->setParentId($this->request->post['message_id']);
            $this->modelMessage->setSubject($this->request->post['subject']);
            $this->modelMessage->setMessage($this->request->post['message']);
            $recipients = explode(";", $this->request->post['to']);
            foreach ($recipients as $to) {
                $this->modelMessage->setTo((int) $to);
            }
            $data['success'] = $this->modelMessage->save();
        }
        if (!$data['success']) {
            $data['error'] = 1;
            $data['msg'] = '<div class="warning">No se pudo enviar el mensaje, por favor intente mas tarde!</div>';
        }
        $this->load->library('json');
        $this->response->setOutput(Json::encode($data), $this->config->get('config_compression'));
    }

    public function send() {
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . "GMT");
        header('Cache-Control: no-cache, must-revalidate');
        header("Pragma: no-cache");
        header("Content-type: application/json");

        $this->load->model('account/customer');
        $this->load->library('json');
        $this->session->set("success", "Su mensaje ha sido enviado");
        $data = [];
        if ($this->request->server['REQUEST_METHOD'] == 'POST' && $this->validateCreate()) {
            $this->load->model('account/message');
            $this->modelMessage->setFrom($this->customer->getId());
            $this->modelMessage->setSubject($this->request->post['subject']);
            $this->modelMessage->setMessage($this->request->post['message']);
            $recipients = explode(";", $this->request->post['to']);
            foreach ($recipients as $to) {
                $this->modelMessage->setTo((int) $to);
            }
            $data['success'] = $this->modelMessage->save();
        }
        if (!$data['success']) {
            $data['error'] = 1;
            $data['msg'] = '<div class="warning">No se pudo enviar el mensaje, por favor intente mas tarde!</div>';
        }
        $this->load->library('json');
        $this->response->setOutput(Json::encode($data), $this->config->get('config_compression'));
    }

    public function getCustomers() {
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . "GMT");
        header('Cache-Control: no-cache, must-revalidate');
        header("Pragma: no-cache");
        header("Content-type: application/json");

        $this->load->model('account/customer');
        $this->load->library('json');

        $data = [];

        $name = $this->request->get['term'];
        $result = $this->modelCustomer->getAll($name);
        if (!$result) {
            $data['error'] = 1;
        } else {
            foreach ($result as $key => $value) {
                $data[] = array(
                    'id' => $value['customer_id'],
                    'label' => $value['company'],
                    'value' => $value['company'],
                );
            }
        }
        $this->load->library('json');
        $this->response->setOutput(Json::encode($data), $this->config->get('config_compression'));
    }

    private function validateCreate() {

        if (!$this->request->post['subject']) {
            $this->error['subject'] = $this->language->get('error_firstname');
        }

        if (!$this->request->post['message']) {
            $this->error['message'] = $this->language->get('error_message');
        }

        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }

    private function validateReply() {

        if (!$this->request->post['subject']) {
            $this->error['subject'] = $this->language->get('error_firstname');
        }

        if (!$this->request->post['message']) {
            $this->error['message'] = $this->language->get('error_message');
        }

        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }
}
