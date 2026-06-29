<?php

$this->load->auto('sale/customer');
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
            'customer_id'=>'',

            //int indexes
            'customer_group_id'=>'',
            'address_id'=>'',
            'country_id'=>'',
            'zone_id'=>'',
            'store_id'=>'',
            'status'=>'',
            'approved'=>'',

            //text filters
            'firstname'=>'',
            'lastname'=>'',
            'email'=>'',
            'company'=>'',
            'telephone'=>'',
            'rif'=>'',

            //date filters
            'date_start'=>'',
            'date_end'=>'',

            //array filters
            'properties'=>'',

            //not null
            'page'=>1,
            'sort'=>'t.firstname',
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

        $total = $this->modelCustomer->getAllTotal($filters);
        $results = $this->modelCustomer->getAll($filters);

        foreach ($results as $l => $result) {
            $id = $result['customer_id'];
            $items[$l] = $result;
            $items[$l]['id'] = $id;
        }

        $pagination = new Pagination();
        $pagination->total = $total;
        $pagination->page = $filters['page'];
        $pagination->limit = $filters['limit'];
        $pagination->text = $this->language->get('text_pagination');
        $pagination->url = Url::createAdminUrl('api/v1/customers') . $url . '&page={page}';

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

        $id = $this->modelCustomer->add($this->prepareData('customers', $this->request->post));

        $return['status'] = array(
            'code'=>200,
            'message'=>'OK'
        );

        $return['error'] = array(
            'code'=>null,
            'message'=>''
        );

        $return['payload'] = array(
            'customer_id'=>$id,
            'id'=>$id
        );
        break;
    case 'put':

        $query = $this->db->query("SELECT * FROM ". DB_PREFIX ."customer WHERE customer_id = '". (int)$this->request->getQuery('id') ."'");
        $query->row['sc'] = $this->request->getQuery('sc');
        $customer = $query->row;
        $this->request->post = json_decode(file_get_contents('php://input'), true);
        if ($customer['customer_id']) {
            $this->modelCustomer->update($customer['customer_id'], $this->prepareData('customers', $customer));

            $return['status'] = array(
                'code'=>200,
                'message'=>'OK'
            );

            $return['error'] = array(
                'code'=>null,
                'message'=>''
            );

            $return['payload'] = array(
                'customer_id'=>$customer['customer_id'],
                'id'=>$customer['customer_id']
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
            $this->modelCustomer->delete($id);
        }
        break;
}