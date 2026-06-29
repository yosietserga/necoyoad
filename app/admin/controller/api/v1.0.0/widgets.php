<?php

$this->load->auto('style/widget');
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
            'widget_id'=>'',
            'name'=>'',

            //int indexes
            'store_id'=>'',
            'status'=>'',

            //text filters
            'row_id'=>'',
            'col_id'=>'',
            'show_in_mobile'=>'',
            'show_in_desktop'=>'',
            'async'=>'',
            'object_type'=>'',
            'object_id'=>'',
            'position'=>'',
            'extension'=>'',
            'app'=>'',
            'landing_page'=>'',

            //not null
            'page'=>1,
            'sort'=>'order',
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

        $results = $this->modelWidget->getWidgets($filters);

        foreach ($results as $l => $result) {
            $id = $result['widget_id'];
            $items[$l] = $result;
            $items[$l]['id'] = $id;
            $items[$l]['settings'] = unserialize($result['settings']);
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
        $this->request->post = json_decode(file_get_contents('php://input'), true);

        $id = $this->modelWeightclass->add($this->prepareData('weight_classes', $this->request->post));

        $return['status'] = array(
            'code'=>200,
            'message'=>'OK'
        );

        $return['error'] = array(
            'code'=>null,
            'message'=>''
        );

        $return['payload'] = array(
            'weight_class_id'=>$id,
            'id'=>$id
        );
        break;
    case 'put':

        $query = $this->db->query("SELECT * FROM ". DB_PREFIX ."weight_class WHERE weight_class_id = '". (int)$this->request->getQuery('id') ."'");
        $query->row['sc'] = $this->request->getQuery('sc');
        $weight_class = $query->row;
        $this->request->post = json_decode(file_get_contents('php://input'), true);
        if ($weight_class['weight_class_id']) {
            $this->modelWeightclass->update($weight_class['weight_class_id'], $this->prepareData('weight_classes', $weight_class));

            $return['status'] = array(
                'code'=>200,
                'message'=>'OK'
            );

            $return['error'] = array(
                'code'=>null,
                'message'=>''
            );

            $return['payload'] = array(
                'weight_class_id'=>$weight_class['weight_class_id'],
                'id'=>$weight_class['weight_class_id']
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
            $this->modelWeightclass->delete($id);
        }
        break;
}