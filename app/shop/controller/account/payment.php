<?php

class ControllerAccountPayment extends Controller {

    public function index() {
        $this->session->clear('object_type');
        $this->session->clear('object_id');
        $this->session->clear('landing_page');

        $Url = new Url($this->registry);
        if (!$this->customer->isLogged()) {
            $this->session->set('redirect', Url::createUrl("account/payment"));
            $this->redirect(Url::createUrl("account/login"));
        }

        $this->language->load('account/payment');

        $this->document->breadcrumbs = [];

        $this->document->breadcrumbs[] = array(
            'href' => Url::createUrl("common/home"),
            'text' => $this->language->get('text_home'),
            'separator' => false
        );

        $this->document->breadcrumbs[] = array(
            'href' => Url::createUrl("account/account"),
            'text' => $this->language->get('text_account'),
            'separator' => $this->language->get('text_separator')
        );

        $this->document->breadcrumbs[] = array(
            'href' => Url::createUrl("account/payment"),
            'text' => $this->language->get('text_history'),
            'separator' => $this->language->get('text_separator')
        );

        $this->document->title = $this->data['heading_title'] = $this->language->get('heading_title');

        $data['page'] = $page = ($this->request->get['page']) ? $this->request->get['page'] : 1;
        $data['sort'] = $sort = ($this->request->get['sort']) ? $this->request->get['sort'] : 'op.date_added';
        $data['order'] = $payment = ($this->request->get['order']) ? $this->request->get['order'] : 'ASC';
        $data['limit'] = $limit = ($this->request->get['limit']) ? $this->request->get['limit'] : 25;
        $data['order_payment_id'] = ($this->request->get['payment_id']) ? $this->request->get['payment_id'] : null;
        $data['order_id'] = ($this->request->get['order_id']) ? $this->request->get['order_id'] : null;
        $data['order_payment_status_id'] = ($this->request->get['status']) ? $this->request->get['status'] : null;
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
        if (isset($this->request->get['status'])) {
            $url .= '&status=' . $this->request->get['status'];
        }
        if (isset($this->request->get['order_id'])) {
            $url .= '&order_id=' . $this->request->get['order_id'];
        }
        if (isset($this->request->get['payment_id'])) {
            $url .= '&payment_id=' . $this->request->get['payment_id'];
        }

        $this->load->model('account/payment');
        $this->data['statuses'] = $this->modelPayment->getPaymentStatuses();
        $payment_total = $this->modelPayment->getTotalPayments($data);

        if ($payment_total) {
            foreach ($this->modelPayment->getPayments($data) as $key => $result) {
                $this->data['payments'][] = array(
                    'order_payment_id' => $result['order_payment_id'],
                    'order_payment_status_id' => $result['order_payment_status_id'],
                    'order_id' => $result['order_id'],
                    'status' => $result['status'],
                    'transac_number' => $result['transac_number'],
                    'amount' => $result['amount'],
                    'payment_method' => $result['payment_method'],
                    'bank_from' => $result['bank_from'],
                    'bank_account_id' => $result['bank_account_id'],
                    'number' => $result['number'],
                    'accountholder' => $result['accountholder'],
                    'date_added' => date('d-m-Y h:i A', strtotime($result['dateAdded']))
                );
            }

            $this->load->library('pagination');
            $pagination = new Pagination(true);
            $pagination->total = $payment_total;
            $pagination->page = $page;
            $pagination->limit = $limit;
            $pagination->text = $this->language->get('text_pagination');
            $pagination->url = Url::createUrl('account/payment') . $url . '&page={page}';
            $this->data['pagination'] = $pagination->render();
        }

        

        $this->session->set('landing_page','account/payment');
        $this->loadWidgets('featuredContent');
        $this->loadWidgets('main');
        $this->loadWidgets('featuredFooter');

        $this->addChild('account/column_left');
            $this->addChild('common/column_left');
            $this->addChild('common/column_right');
            $this->addChild('common/header');
            $this->addChild('common/footer');



        $template = ($this->config->get('default_view_account_payment')) ? $this->config->get('default_view_account_payment') : 'account/payment.tpl';
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/' . $template)) {
            $this->template = $this->config->get('config_template') . '/' . $template;
        } else {
            $this->template = 'choroni/' . $template;
        }

        $this->response->setOutput($this->render(TRUE), $this->config->get('config_compression'));
    }

    public function receipt() {
        $this->session->clear('object_type');
        $this->session->clear('object_id');
        $this->session->clear('landing_page');

        if (!$this->customer->isLogged()) {
            $this->session->set('redirect', Url::createUrl("account/payment"));
            $this->redirect(Url::createUrl("account/login"));
        }

        $this->language->load('account/payment_receipt');

        $this->document->breadcrumbs = [];

        $this->document->breadcrumbs[] = array(
            'href' => Url::createUrl("common/home"),
            'text' => $this->language->get('text_home'),
            'separator' => false
        );

        $this->document->breadcrumbs[] = array(
            'href' => Url::createUrl("account/account"),
            'text' => $this->language->get('text_account'),
            'separator' => $this->language->get('text_separator')
        );

        $this->document->breadcrumbs[] = array(
            'href' => Url::createUrl("account/payment"),
            'text' => $this->language->get('text_history'),
            'separator' => $this->language->get('text_separator')
        );

        $this->document->title = $this->data['heading_title'] = $this->language->get('heading_title');
        if ($this->request->hasQuery('payment_id')) {
            $this->load->model('account/payment');
            $payment = $this->modelPayment->getById($this->request->getQuery('payment_id'));
            $this->setvar('order_payment_id', $payment, '');
            $this->setvar('order_id', $payment, '');
            $this->setvar('amount', $payment, '');
            $this->setvar('transact_number', $payment, '');
            $this->setvar('bank_account_id', $payment, '');
            $this->setvar('bank_from', $payment, '');
            $this->setvar('payment_method', $payment, '');
            $this->setvar('comment', $payment, '');
            $this->setvar('date_added', $payment, '');
            $this->setvar('status', $payment, '');
            $this->setvar('number', $payment, ''); // numbero de cuenta de la tienda
            $this->setvar('accountholder', $payment, '');
            $this->setvar('type', $payment, ''); // tipo de la cuenta bancaria de la tienda
            $this->setvar('total', $payment, '');
            $this->setvar('payment_firstname', $payment, '');
            $this->setvar('payment_lastname', $payment, '');
            $this->setvar('total', $payment, '');
            $this->data['total'] = $this->currency->format($this->data['total']);
            $this->data['amount'] = $this->currency->format($this->data['amount']);
        }

        

        $this->session->set('landing_page','account/payment/receipt');
        $this->loadWidgets('featuredContent');
        $this->loadWidgets('main');
        $this->loadWidgets('featuredFooter');

        $this->addChild('account/column_left');
        $this->addChild('common/column_left');
        $this->addChild('common/column_right');
        $this->addChild('common/footer');
        $this->addChild('common/header');


        $template = ($this->config->get('default_view_account_payment_receipt')) ? $this->config->get('default_view_account_payment_receipt') : 'account/payment_receipt.tpl';
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/' . $template)) {
            $this->template = $this->config->get('config_template') . '/' . $template;
        } else {
            $this->template = 'choroni/' . $template;
        }

        $this->response->setOutput($this->render(TRUE), $this->config->get('config_compression'));
    }

    public function register() {
        $this->session->clear('object_type');
        $this->session->clear('object_id');
        $this->session->clear('landing_page');

        $this->load->model('checkout/extension');
        $this->language->load('account/payment');
        $this->document->title = $this->data['heading_title'] = $this->language->get('heading_register_title');
        $this->load->auto('account/address');
        $address = $this->modelAddress->getAddress($this->customer->getAddressId());
        $method_data = [];
        $results = $this->modelExtension->getExtensions('payment');
        foreach ($results as $result) {
            $this->load->model('payment/' . $result['key']);
            $this->language->load('payment/' . $result['key']);
            $method = $this->{'model_payment_' . $result['key']}->getMethod($address);
            if ($method) {
                $method_data[$result['key']] = $method;
            }
        }
        $sort_order = [];
        foreach ($method_data as $key => $value) {
            $sort_order[$key] = $value['sort_order'];
        }
        array_multisort($sort_order, SORT_ASC, $method_data);
        $this->data['payment_methods'] = $method_data;

        if ($this->request->hasQuery('order_id')) {
            $this->data['order_id'] = $this->request->getQuery('order_id');
        } elseif ($this->session->data['order_id']) {
            $this->data['order_id'] = $this->session->data['order_id'];
        } else {
            $this->data['order_id'] = 0;
        }

        foreach ($method_data as $key => $value) {
            $this->children[$key] = 'payment/' . $key;
        }        

        $this->session->set('landing_page','account/payment/register');
        $this->loadWidgets('featuredContent');
        $this->loadWidgets('main');
        $this->loadWidgets('featuredFooter');

        $this->addChild('account/column_left');
        $this->addChild('common/column_left');
        $this->addChild('common/column_right');
        $this->addChild('common/footer');
        $this->addChild('common/header');


        $template = ($this->config->get('default_view_account_order_payment')) ? $this->config->get('default_view_account_order_payment') : 'account/order_payment.tpl';
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/' . $template)) {
            $this->template = $this->config->get('config_template') . '/' . $template;
        } else {
            $this->template = 'choroni/' . $template;
        }

        $this->response->setOutput($this->render(TRUE), $this->config->get('config_compression'));
    }
}
