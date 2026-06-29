<?php

$this->load->auto('sale/order');
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
            'order_id'=>'',

            //int indexes
            'currency_id'=>'',
            'customer_id'=>'',
            'customer_group_id'=>'',
            'shipping_country_id'=>'',
            'payment_country_id'=>'',
            'shipping_zone_id'=>'',
            'payment_zone_id'=>'',
            'order_status_id'=>'',
            'language_id'=>'',
            'coupon_id'=>'',
            'invoice_id'=>'',
            'store_id'=>'',

            //float filters
            'from_total'=>'',
            'to_total'=>'',
            'total'=>'',

            //text filters
            'firstname'=>'',
            'lasttname'=>'',
            'company'=>'',
            'rif'=>'',
            'email'=>'',
            'currency'=>'',
            'ip'=>'',

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

        $total = $this->modelOrder->getAllTotal($filters);
        $results = $this->modelOrder->getAll($filters);

        foreach ($results as $l => $result) {
            $id = $result['order_id'];

            $items[$l] = $result;
            $items[$l]['id'] = $id;
        }

        $pagination = new Pagination();
        $pagination->total = $total;
        $pagination->page = $filters['page'];
        $pagination->limit = $filters['limit'];
        $pagination->text = $this->language->get('text_pagination');
        $pagination->url = Url::createAdminUrl('api/v1/orders') . $url . '&page={page}';

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

        $id = $this->modelOrder->add($this->prepareData('orders', $this->request->post));

        $return['status'] = array(
            'code'=>200,
            'message'=>'OK'
        );

        $return['error'] = array(
            'code'=>null,
            'message'=>''
        );

        $return['payload'] = array(
            'order_id'=>$id,
            'id'=>$id
        );
        break;
    case 'put':

        $query = $this->db->query("SELECT * FROM ". DB_PREFIX ."order WHERE order_id = '". (int)$this->request->getQuery('id') ."'");
        $query->row['sc'] = $this->request->getQuery('sc');
        $order = $query->row;
        $this->request->post = json_decode(file_get_contents('php://input'), true);
        if ($order['order_id']) {
            $this->modelOrder->update($order['order_id'], $this->prepareData('orders', $order));

            $return['status'] = array(
                'code'=>200,
                'message'=>'OK'
            );

            $return['error'] = array(
                'code'=>null,
                'message'=>''
            );

            $return['payload'] = array(
                'order_id'=>$order['order_id'],
                'id'=>$order['order_id']
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
            $this->modelOrder->delete($id);
        }
        break;
}