<?php

$this->load->auto('localisation/orderpaymentstatus');
$this->load->auto('json');

$return = [];
$request_type = $this->request->server['REQUEST_METHOD'];

switch(strtolower($request_type)) {
    case 'get':
    default:
        $this->load->auto('pagination');

        $filters = [];
        $items = [];

        $_filters = array(
            //unique indexes
            'id'=>'',
            'order_payment_status_id'=>'',

            //int indexes
            'language_id'=>'',

            //text filters
            'name'=>'',

            //date filters
            'date_start'=>'',
            'date_end'=>'',

            //array filters
            'properties'=>'',

            //not null
            'page'=>1,
            'sort'=>'t.name',
            'order'=>'ASC',
            'limit'=>$this->config->get('config_admin_limit'),
        );

        foreach ($_filters as $k=>$v) {
            $p = $this->request->getQuery($k);

            if (!empty($p)) {
                $filters[$k] = $p;
            } else if (!empty($v)) {
                $filters[$k] = $v;
            }
        }

        $url = '';
        foreach ($filters as $k=>$v) {
            if ($this->request->hasQuery($k) && !empty($v)) $url .= "&{$k}=" . $v;
        }

        $total = $this->modelOrderpaymentstatus->getAllTotal($filters);
        $results = $this->modelOrderpaymentstatus->getAll($filters);

        foreach ($results as $l => $result) {
            $id = $result['order_payment_status_id'];

            $items[$l] = $result;
            $items[$l]['id'] = $id;
        }

        $pagination = new Pagination();
        $pagination->total = $total;
        $pagination->page = $filters['page'];
        $pagination->limit = $filters['limit'];
        $pagination->text = $this->language->get('text_pagination');
        $pagination->url = Url::createAdminUrl('api/v1/payment_statuses') . $url . '&page={page}';

        $return['status'] = array(
            'code'=>200,
            'message'=>'OK'
        );

        $return['error'] = array(
            'code'=>null,
            'message'=>''
        );

        $return['payload'] = array(
            'results'=>$items,
            'filters'=>$filters,
            'pagination'=>$pagination->render(),
            'total'=>$total
        );
    break;

    case 'post':
        $this->request->post = json_decode(file_get_contents('php://input'), true);

        $id = $this->modelOrderpaymentstatus->add($this->prepareData('payment_statuses', $this->request->post));

        $return['status'] = array(
            'code'=>200,
            'message'=>'OK'
        );

        $return['error'] = array(
            'code'=>null,
            'message'=>''
        );

        $return['payload'] = array(
            'order_payment_status_id'=>$id,
            'id'=>$id
        );
        break;
    case 'put':

        $query = $this->db->query("SELECT * FROM ". DB_PREFIX ."order_payment_status WHERE order_payment_status_id = '". (int)$this->request->getQuery('id') ."'");
        $query->row['sc'] = $this->request->getQuery('sc');
        $order_payment_status = $query->row;
        $this->request->post = json_decode(file_get_contents('php://input'), true);
        if ($order_payment_status['order_payment_status_id']) {
            $this->modelOrderpaymentstatus->update($order_payment_status['order_payment_status_id'], $this->prepareData('order_payment_statuss', $order_payment_status));

            $return['status'] = array(
                'code'=>200,
                'message'=>'OK'
            );

            $return['error'] = array(
                'code'=>null,
                'message'=>''
            );

            $return['payload'] = array(
                'order_payment_status_id'=>$order_payment_status['order_payment_status_id'],
                'id'=>$order_payment_status['order_payment_status_id']
            );
        } else {
            $this->error404();
            return;
        }
        break;
    case 'delete':
        $this->request->post = json_decode(file_get_contents('php://input'), true);
        $id = $this->request->hasPost('id') ? $this->request->getPost('id') : $this->request->getQuery('id');
        $ids = (is_array($id)) ? $id : array($id);
        foreach ($ids as $id) {
            $this->modelOrderpaymentstatus->delete($id);
        }
        break;
}