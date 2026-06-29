<?php

$this->load->auto('setting/setting');
$this->load->auto('json');

$return = [];
$request_type = $this->request->server['REQUEST_METHOD'];

switch(strtolower($request_type)) {
    case 'get':
    default:
        $filters = [];
        $items = [];

        $_filters = array(
            //unique indexes
            'id'=>'',
            'setting_id'=>'',

            //int indexes
            'store_id'=>0,

            //text filters
            'group'=>'',
            'key'=>'',

            //not null
            'page'=>1,
            'sort'=>'t.group',
            'order'=>'DESC',
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

        if (!empty($filters['group']) && !empty($filters['key'])) {
            $results = $this->modelSetting->getProperty($filters['group'], $filters['key'], $filters['store_id']);
        } else {
            $results = $this->modelSetting->getSetting($filters['group'], $filters['store_id']);
        }

        $total = count($results);

        foreach ($results as $l => $result) {
            $id = $result['setting_id'];

            $items[$l] = $result;
            $items[$l]['id'] = $id;
        }

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
            'total'=>$total
        );
    break;

    case 'post':
    case 'put':
        $this->request->post = json_decode(file_get_contents('php://input'), true);

        if (!empty($this->request->post['group']) && !empty($this->request->post['key'])) {
            $results = $this->modelSetting->updateProperty($this->request->post['group'], $this->request->post['key'], $this->request->post['value'], $this->request->post['store_id']);
        } else {
            $results = $this->modelSetting->update($this->request->post['group'], $this->request->post['settings'], $this->request->post['store_id']);
        }

        $return['status'] = array(
            'code'=>200,
            'message'=>'OK'
        );

        $return['error'] = array(
            'code'=>null,
            'message'=>''
        );

        $return['payload'] = array(
            'setting_id'=>$id,
            'id'=>$id
        );
        break;
    case 'delete':
        $this->request->post = json_decode(file_get_contents('php://input'), true);

        if (!empty($this->request->post['group']) && !empty($this->request->post['key'])) {
            $results = $this->modelSetting->deleteProperty($this->request->post['group'], $this->request->post['key'], $this->request->post['store_id']);
        } else {
            $results = $this->modelSetting->delete($this->request->post['group'], $this->request->post['store_id']);
        }

        break;
}