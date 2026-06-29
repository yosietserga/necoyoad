<?php

$this->load->auto('content/banner');
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
            'banner_item_id'=>'',

            //int indexes
            'banner_id'=>'',
            'status'=>'',
            'sort_order'=>'',

            //text filters
            'image'=>'',
            'link'=>'',

            //array filters
            'properties'=>'',

            //not null
            'page'=>1,
            'sort'=>'t.sort_order',
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

            if ($k=='id') {
                if (!empty($p)) {
                    $filters['banner_id'] = $p;
                } else if (!empty($v)) {
                    $filters['banner_id'] = $v;
                }
            }
        }

        $url = '';
        foreach ($filters as $k=>$v) {
            if ($this->request->hasQuery($k) && !empty($v)) $url .= "&{$k}=" . $v;
        }

        $results = $this->modelBanner->getItems($filters);
        $total = $this->modelBanner->getAllItemsTotal($filters);

        foreach ($results as $l => $result) {
            $id = $result['banner_item_id'];
            $items[$l] = $result;
            $items[$l]['id'] = $id;
        }

        $pagination = new Pagination();
        $pagination->total = $total;
        $pagination->page = $filters['page'];
        $pagination->limit = $filters['limit'];
        $pagination->text = $this->language->get('text_pagination');
        $pagination->url = Url::createAdminUrl('api/v1/banner_items') . $url . '&page={page}';

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
        
        if ($this->request->hasQuery('banner_item_id')) {
            $query = $this->db->query("SELECT * FROM ". DB_PREFIX ."banner_item WHERE banner_item_id = '". (int)$this->request->getQuery('banner_item_id') ."'");
            $query->row['sc'] = $this->request->getQuery('sc');
            $banner = $query->row;

            if ($banner['banner_item_id']) {
                $banner['banner_item_id'] = $this->modelBanner->setItem($this->prepareData('banner_items', $banner));

                $return['status'] = array(
                    'code'=>200,
                    'message'=>'OK'
                );

                $return['error'] = array(
                    'code'=>null,
                    'message'=>''
                );

                $return['payload'] = array(
                    'banner_item_id'=>$banner['banner_item_id'],
                    'id'=>$banner['banner_item_id']
                );
            } else {
                $this->error404();
                return;
            }
        } elseif ($this->request->hasQuery('id') || $this->request->hasPost('id')) {
            $id = $this->modelBanner->setItem($this->prepareData('banner_items', $this->request->post));

            $return['status'] = array(
                'code'=>200,
                'message'=>'OK'
            );

            $return['error'] = array(
                'code'=>null,
                'message'=>''
            );

            $return['payload'] = array(
                'banner_item_id'=>$id,
                'id'=>$id
            );
        }
        break;
    case 'put':
        
        break;
    case 'delete':
        $this->request->post = json_decode(file_get_contents('php://input'), true);
        $id = $this->request->hasPost('id') ? $this->request->getPost('id') : $this->request->getQuery('id');
        $ids = (is_array($id)) ? $id : array($id);
        foreach ($ids as $id) {
            $this->modelBanner->deleteItem($id);
        }
        break;
}