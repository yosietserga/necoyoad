<?php

$this->load->auto('sale/coupon');
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
            'coupon_id'=>'',

            //int indexes
            'language_id'=>'',
            'store_id'=>'',
            'status'=>'',
            'search_in_description'=>'',

            //text filters
            'title'=>'',
            'q'=>'',

            //date filters
            'date_start'=>'',
            'date_end'=>'',

            //array filters
            'properties'=>'',

            //not null
            'page'=>1,
            'sort'=>'td.title',
            'order'=>'ASC',
            'limit'=>$this->config->get('config_admin_limit'),
        );

        foreach ($_filters as $k=>$v) {
            $p = $this->request->getQuery($k);

            if ($k==='title' || $k==='q') {
                $t = $this->request->getQuery('title');
                $q = $this->request->getQuery('q');

                if ($t && $q && $t !== $q) {
                    $filters['queries'] = explode(' ',$t .' '. $q);
                } elseif ($q) {
                    $filters['queries'] = explode(' ',$q);
                } elseif ($t) {
                    $filters['queries'] = explode(' ',$t);
                }
            }

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

        $total = $this->modelCoupon->getAllTotal($filters);
        $results = $this->modelCoupon->getAll($filters);

        foreach ($results as $l => $result) {
            $id = $result['coupon_id'];
            $items[$l] = $result;
            $items[$l]['id'] = $id;
            $items[$l]['descriptions'] = $this->modelCoupon->getDescriptions($id);
        }

        $pagination = new Pagination();
        $pagination->total = $total;
        $pagination->page = $filters['page'];
        $pagination->limit = $filters['limit'];
        $pagination->text = $this->language->get('text_pagination');
        $pagination->url = Url::createAdminUrl('api/v1/coupons') . $url . '&page={page}';

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

        $id = $this->modelCoupon->add($this->prepareData('coupons', $this->request->post));

        $return['status'] = array(
            'code'=>200,
            'message'=>'OK'
        );

        $return['error'] = array(
            'code'=>null,
            'message'=>''
        );

        $return['payload'] = array(
            'coupon_id'=>$id,
            'id'=>$id
        );
        break;
    case 'put':

        $query = $this->db->query("SELECT * FROM ". DB_PREFIX ."coupon WHERE coupon_id = '". (int)$this->request->getQuery('id') ."'");
        $query->row['sc'] = $this->request->getQuery('sc');
        $coupon = $query->row;
        $this->request->post = json_decode(file_get_contents('php://input'), true);
        if ($coupon['coupon_id']) {
            $this->modelCoupon->update($coupon['coupon_id'], $this->prepareData('coupons', $coupon));

            $return['status'] = array(
                'code'=>200,
                'message'=>'OK'
            );

            $return['error'] = array(
                'code'=>null,
                'message'=>''
            );

            $return['payload'] = array(
                'coupon_id'=>$coupon['coupon_id'],
                'id'=>$coupon['coupon_id']
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
            $this->modelCoupon->delete($id);
        }
        break;
}