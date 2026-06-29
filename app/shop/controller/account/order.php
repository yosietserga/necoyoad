<?php

class ControllerAccountOrder extends Controller {

    public function index() {
        $this->session->clear('object_type');
        $this->session->clear('object_id');
        $this->session->clear('landing_page');

        $Url = new Url($this->registry);
        if (!$this->customer->isLogged()) {
            $this->session->set('redirect', Url::createUrl("account/order"));
            $this->redirect(Url::createUrl("account/login"));
        }

        $this->language->load('account/history');

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
            'href' => Url::createUrl("account/order"),
            'text' => $this->language->get('text_history'),
            'separator' => $this->language->get('text_separator')
        );

        $this->document->title = $this->data['heading_title'] = $this->language->get('heading_title');

        $data['page'] = $page = ($this->request->get['page']) ? $this->request->get['page'] : 1;
        $data['order_id'] = $order_id = ($this->request->get['order_id']) ? $this->request->get['order_id'] : null;
        $data['sort'] = $sort = ($this->request->get['sort']) ? $this->request->get['sort'] : 'o.date_end';
        $data['order'] = $order = ($this->request->get['order']) ? $this->request->get['order'] : 'ASC';
        $data['limit'] = $limit = ($this->request->get['limit']) ? $this->request->get['limit'] : 25;
        $data['order_status_id'] = ($this->request->get['status']) ? $this->request->get['status'] : null;
        $data['start'] = ($page - 1) * $limit;

        $url = '';

        if (isset($this->request->get['order_id'])) {
            $url .= '&order_id=' . $this->request->get['order_id'];
        }
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

        $this->load->model('account/order');
        $this->data['statuses'] = $this->modelOrder->getOrderStatuses();
        $order_total = $this->modelOrder->getTotalOrders($data);

        if ($order_total) {
            foreach ($this->modelOrder->getOrders($data) as $key => $result) {
                $this->data['orders'][] = array(
                    'order_id' => $result['order_id'],
                    'name' => $result['firstname'] . ' ' . $result['lastname'],
                    'status' => $result['status'],
                    'status_id' => $result['order_status_id'],
                    'date_added' => date('d-m-Y h:i A', strtotime($result['dateAdded'])),
                    'products' => $this->modelOrder->getTotalOrderProductsByOrderId($result['order_id']),
                    'total' => $this->currency->format($result['total'], $result['currency'], $result['value']),
                    'href' => Url::createUrl("account/invoice", array('order_id' => $result['order_id']))
                );
            }

            $this->load->library('pagination');
            $pagination = new Pagination(true);
            $pagination->total = $order_total;
            $pagination->page = $page;
            $pagination->limit = $limit;
            $pagination->text = $this->language->get('text_pagination');
            $pagination->url = Url::createUrl('account/order') . $url . '&page={page}';
            $this->data['pagination'] = $pagination->render();
        }

        

        $this->session->set('landing_page','account/order');
        $this->loadWidgets('featuredContent');
        $this->loadWidgets('main');
        $this->loadWidgets('featuredFooter');

        $this->addChild('account/column_left');
            $this->addChild('common/column_left');
            $this->addChild('common/column_right');
            $this->addChild('common/header');
            $this->addChild('common/footer');



        $template = ($this->config->get('default_view_account_order')) ? $this->config->get('default_view_account_order') : 'account/order.tpl';
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/' . $template)) {
            $this->template = $this->config->get('config_template') . '/' . $template;
        } else {
            $this->template = 'choroni/' . $template;
        }

        $this->response->setOutput($this->render(TRUE), $this->config->get('config_compression'));
    }

}
