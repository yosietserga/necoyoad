<?php

$this->load->auto('sale/bank_account');
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
            'bank_account_id'=>'',

            //int indexes
            'bank_id'=>'',
            'store_id'=>'',
            'status'=>'',

            //text filters
            'accountholder'=>'',
            'email'=>'',
            'number'=>'',

            //date filters
            'date_start'=>'',
            'date_end'=>'',

            //array filters
            'properties'=>'',

            //not null
            'page'=>1,
            'sort'=>'t.accountholder',
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

        $total = $this->modelBank_account->getAllTotal($filters);
        $results = $this->modelBank_account->getAll($filters);

        foreach ($results as $l => $result) {
            $id = $result['bank_account_id'];
            $items[$l] = $result;
            $items[$l]['id'] = $id;
        }

        $pagination = new Pagination();
        $pagination->total = $total;
        $pagination->page = $filters['page'];
        $pagination->limit = $filters['limit'];
        $pagination->text = $this->language->get('text_pagination');
        $pagination->url = Url::createAdminUrl('api/v1/bank_accounts') . $url . '&page={page}';

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

        $id = $this->modelBank_account->add($this->prepareData('bank_accounts', $this->request->post));

        $return['status'] = array(
            'code'=>200,
            'message'=>'OK'
        );

        $return['error'] = array(
            'code'=>null,
            'message'=>''
        );

        $return['payload'] = array(
            'bank_account_id'=>$id,
            'id'=>$id
        );
        break;
    case 'put':

        $query = $this->db->query("SELECT * FROM ". DB_PREFIX ."bank_account WHERE bank_account_id = '". (int)$this->request->getQuery('id') ."'");
        $query->row['sc'] = $this->request->getQuery('sc');
        $bank_account = $query->row;
        $this->request->post = json_decode(file_get_contents('php://input'), true);
        if ($bank_account['bank_account_id']) {
            $this->modelBank_account->update($bank_account['bank_account_id'], $this->prepareData('bank_accounts', $bank_account));

            $return['status'] = array(
                'code'=>200,
                'message'=>'OK'
            );

            $return['error'] = array(
                'code'=>null,
                'message'=>''
            );

            $return['payload'] = array(
                'bank_account_id'=>$bank_account['bank_account_id'],
                'id'=>$bank_account['bank_account_id']
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
            $this->modelBank_account->delete($id);
        }
        break;
}