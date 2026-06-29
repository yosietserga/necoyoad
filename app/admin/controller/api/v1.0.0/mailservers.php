<?php

$this->load->auto('setting/setting');
$this->load->auto('json');

$return = [];
$request_type = $this->request->server['REQUEST_METHOD'];

switch(strtolower($request_type)) {
    case 'get':
    default:
        $this->load->auto('pagination');

        $filters = [];
        $items = [];

        $results = $this->modelSetting->getSetting('mail_server');
        $total = count($results);

        foreach ($results as $l => $result) {
            $id = $l;

            $items[$l] = unserialize($result);
            $items[$l]['id'] = $id;
        }

        $pagination = new Pagination();
        $pagination->total = $total;
        $pagination->page = $filters['page'];
        $pagination->limit = $filters['limit'];
        $pagination->text = $this->language->get('text_pagination');
        $pagination->url = Url::createAdminUrl('api/v1/mailservers') . $url . '&page={page}';

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

        $mail_server_id = substr(md5(mt_rand(10000, 99999) . time()),0,10);

        $this->modelSetting->updateProperty('mail_server', $mail_server_id, serialize($this->request->post));

        $return['status'] = array(
            'code'=>200,
            'message'=>'OK'
        );

        $return['error'] = array(
            'code'=>null,
            'message'=>''
        );

        $return['payload'] = array(
            'mail_server_id'=>$id,
            'id'=>$id
        );
        break;
    case 'put':
        $this->request->post = json_decode(file_get_contents('php://input'), true);
        $query->row['sc'] = $this->request->getQuery('sc');
        $this->modelSetting->updateProperty('mail_server', $this->request->getQuery('id'),
            serialize($this->request->post));

        $return['status'] = array(
            'code'=>200,
            'message'=>'OK'
        );

        $return['error'] = array(
            'code'=>null,
            'message'=>''
        );

        $return['payload'] = array(
            'mail_server_id'=>$this->request->getQuery('id'),
            'id'=>$this->request->getQuery('id')
        );
        break;
    case 'delete':
        $this->request->post = json_decode(file_get_contents('php://input'), true);
        $id = $this->request->hasPost('id') ? $this->request->getPost('id') : $this->request->getQuery('id');
        $ids = (is_array($id)) ? $id : array($id);
        foreach ($ids as $id) {
            $this->modelSetting->deleteProperty('mail_server', $id);
        }
        break;
}