<?php

class ControllerAccountBalance extends Controller {

    public function index() {
        $this->session->clear('object_type');
        $this->session->clear('object_id');
        $this->session->clear('landing_page');

        $Url = new Url($this->registry);
        if (!$this->customer->isLogged()) {
            $this->session->set('redirect', Url::createUrl("account/balance"));
            $this->redirect(Url::createUrl("account/login"));
        }

        $this->language->load('account/balance');

        $this->document->breadcrumbs = [];

        $this->document->breadcrumbs[] = array(
            'href' => $Url::createUrl("common/home"),
            'text' => $this->language->get('text_home'),
            'separator' => false
        );

        $this->document->breadcrumbs[] = array(
            'href' => $Url::createUrl("account/account"),
            'text' => $this->language->get('text_account'),
            'separator' => $this->language->get('text_separator')
        );

        $this->document->breadcrumbs[] = array(
            'href' => $Url::createUrl("account/balance"),
            'text' => $this->language->get('text_history'),
            'separator' => $this->language->get('text_separator')
        );

        $this->document->title = $this->data['heading_title'] = $this->language->get('heading_title');

        $data['page'] = $page = ($this->request->get['page']) ? $this->request->get['page'] : 1;
        $data['sort'] = $sort = ($this->request->get['sort']) ? $this->request->get['sort'] : 'op.date_added';
        $data['order'] = $balance = ($this->request->get['order']) ? $this->request->get['order'] : 'ASC';
        $data['limit'] = $limit = ($this->request->get['limit']) ? $this->request->get['limit'] : 25;
        $data['order_id'] = ($this->request->get['order_id']) ? $this->request->get['order_id'] : null;
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

        $this->load->model('account/balance');
        $balance_total = $this->modelBalance->getTotalBalances($data);

        if ($balance_total) {
            foreach ($this->modelBalance->getBalances($data) as $key => $result) {
                $this->data['balances'][] = array(
                    'order_balance_id' => $result['order_balance_id'],
                    'order_balance_status_id' => $result['order_balance_status_id'],
                    'order_id' => $result['order_id'],
                    'status' => $result['status'],
                    'transac_number' => $result['transac_number'],
                    'amount' => $result['amount'],
                    'balance_method' => $result['balance_method'],
                    'bank_from' => $result['bank_from'],
                    'bank_account_id' => $result['bank_account_id'],
                    'number' => $result['number'],
                    'accountholder' => $result['accountholder'],
                    'date_added' => date('d-m-Y h:i A', strtotime($result['dateAdded']))
                );
            }

            $this->load->library('pagination');
            $pagination = new Pagination(true);
            $pagination->total = $balance_total;
            $pagination->page = $page;
            $pagination->limit = $limit;
            $pagination->text = $this->language->get('text_pagination');
            $pagination->url = Url::createUrl('account/balance') . $url . '&page={page}';
            $this->data['pagination'] = $pagination->render();
        }



        $this->session->set('landing_page','account/balance');
        $this->loadWidgets('featuredContent');
        $this->loadWidgets('main');
        $this->loadWidgets('featuredFooter');

        $this->addChild('account/column_left');
            $this->addChild('common/column_left');
            $this->addChild('common/column_right');
            $this->addChild('common/header');
            $this->addChild('common/footer');



        $template = ($this->config->get('default_view_account_balance')) ? $this->config->get('default_view_account_balance') : 'account/balance.tpl';
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/' . $template)) {
            $this->template = $this->config->get('config_template') . '/' . $template;
        } else {
            $this->template = 'choroni/' . $template;
        }

        $this->response->setOutput($this->render(TRUE), $this->config->get('config_compression'));
    }

    public function report() {
        $this->session->clear('object_type');
        $this->session->clear('object_id');
        $this->session->clear('landing_page');

        if (!$this->customer->isLogged()) {
            $this->session->set('redirect', Url::createUrl("account/balance"));
            $this->redirect(Url::createUrl("account/login"));
        }

        $this->language->load('account/balance_receipt');

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
            'href' => Url::createUrl("account/balance"),
            'text' => $this->language->get('text_history'),
            'separator' => $this->language->get('text_separator')
        );

        $this->document->title = $this->data['heading_title'] = $this->language->get('heading_title');
        if ($this->request->hasQuery('balance_id')) {
            $this->load->model('account/balance');
            $balance = $this->modelBalance->getById($this->request->getQuery('balance_id'));
            $this->setvar('order_balance_id', $balance, '');
            $this->setvar('order_id', $balance, '');
            $this->setvar('amount', $balance, '');
            $this->setvar('transact_number', $balance, '');
            $this->setvar('bank_account_id', $balance, '');
            $this->setvar('bank_from', $balance, '');
            $this->setvar('balance_method', $balance, '');
            $this->setvar('comment', $balance, '');
            $this->setvar('date_added', $balance, '');
            $this->setvar('status', $balance, '');
            $this->setvar('number', $balance, ''); // numbero de cuenta de la tienda
            $this->setvar('accountholder', $balance, '');
            $this->setvar('type', $balance, ''); // tipo de la cuenta bancaria de la tienda
            $this->setvar('total', $balance, '');
            $this->setvar('balance_firstname', $balance, '');
            $this->setvar('balance_lastname', $balance, '');
            $this->setvar('total', $balance, '');
            $this->data['total'] = $this->currency->format($this->data['total']);
            $this->data['amount'] = $this->currency->format($this->data['amount']);
        }



        $this->session->set('landing_page','account/balance/report');
        $this->loadWidgets('featuredContent');
        $this->loadWidgets('main');
        $this->loadWidgets('featuredFooter');

        $this->addChild('account/column_left');
            $this->addChild('common/column_left');
            $this->addChild('common/column_right');
            $this->addChild('common/header');
            $this->addChild('common/footer');



        $template = ($this->config->get('default_view_account_balance_receipt')) ? $this->config->get('default_view_account_balance_receipt') : 'account/balance_receipt.tpl';
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

        $this->load->auto('account/address');
        $address = $this->modelAddress->getAddress($this->customer->getAddressId());
        $method_data = [];
        $results = $this->modelExtension->getExtensions('balance');
        foreach ($results as $result) {
            $this->load->model('balance/' . $result['key']);
            $method = $this->{'model_balance_' . $result['key']}->getMethod($address);
            if ($method) {
                $method_data[$result['key']] = $method;
            }
        }
        $sort_order = [];
        foreach ($method_data as $key => $value) {
            $sort_order[$key] = $value['sort_order'];
        }
        array_multisort($sort_order, SORT_ASC, $method_data);
        $this->data['balance_methods'] = $method_data;

        if ($this->request->hasQuery('order_id')) {
            $this->data['order_id'] = $this->request->getQuery('order_id');
        } elseif ($this->session->data['order_id']) {
            $this->data['order_id'] = $this->session->data['order_id'];
        } else {
            $this->data['order_id'] = 0;
        }

        foreach ($method_data as $key => $value) {
            $this->children[$key] = 'balance/' . $key;
        }

        $csspath = defined('CDN_CSS') ? CDN_CSS : HTTP_CSS;

        $styles[] = array(
            'media' => 'all',
            'href' => $csspath . 'neco.form.css'
        );
        $this->styles = array_merge($styles, $this->styles);



        $this->session->set('landing_page','account/balance/register');
        $this->loadWidgets('featuredContent');
        $this->loadWidgets('main');
        $this->loadWidgets('featuredFooter');

        $this->addChild('account/column_left');
            $this->addChild('common/column_left');
            $this->addChild('common/column_right');
            $this->addChild('common/header');
            $this->addChild('common/footer');



        $template = ($this->config->get('default_view_account_order_balance')) ? $this->config->get('default_view_account_order_balance') : 'account/order_balance.tpl';
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/' . $template)) {
            $this->template = $this->config->get('config_template') . '/' . $template;
        } else {
            $this->template = 'choroni/' . $template;
        }

        $this->response->setOutput($this->render(TRUE), $this->config->get('config_compression'));
    }
}
