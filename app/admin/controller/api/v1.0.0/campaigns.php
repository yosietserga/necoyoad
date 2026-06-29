<?php

$this->load->auto('marketing/campaign');
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
            'campaign_id'=>'',

            //int indexes
            'newsletter_id'=>'',
            'contact_id'=>'',
            'status'=>'',

            //text filters
            'name'=>'',
            'subject'=>'',
            'from_name'=>'',
            'from_email'=>'',
            'replyto_email'=>'',
            'contact_name'=>'',
            'contact_email'=>'',

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

        $total = $this->modelCampaign->getAllTotal($filters);
        $results = $this->modelCampaign->getAll($filters);

        foreach ($results as $l => $result) {
            $id = $result['campaign_id'];
            $items[$l] = $result;
            $items[$l]['id'] = $id;
        }

        $pagination = new Pagination();
        $pagination->total = $total;
        $pagination->page = $filters['page'];
        $pagination->limit = $filters['limit'];
        $pagination->text = $this->language->get('text_pagination');
        $pagination->url = Url::createAdminUrl('api/v1/campaigns') . $url . '&page={page}';

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

        $id = $this->modelCampaign->add($this->prepareData('campaigns', $this->request->post));

        $return['status'] = array(
            'code'=>200,
            'message'=>'OK'
        );

        $return['error'] = array(
            'code'=>null,
            'message'=>''
        );

        $return['payload'] = array(
            'campaign_id'=>$id,
            'id'=>$id
        );
        break;
    case 'put':

        $query = $this->db->query("SELECT * FROM ". DB_PREFIX ."campaign WHERE campaign_id = '". (int)$this->request->getQuery('id') ."'");
        $query->row['sc'] = $this->request->getQuery('sc');
        $campaign = $query->row;
        $this->request->post = json_decode(file_get_contents('php://input'), true);
        if ($campaign['campaign_id']) {
            $this->modelCampaign->update($campaign['campaign_id'], $this->prepareData('campaigns', $campaign));

            $return['status'] = array(
                'code'=>200,
                'message'=>'OK'
            );

            $return['error'] = array(
                'code'=>null,
                'message'=>''
            );

            $return['payload'] = array(
                'campaign_id'=>$campaign['campaign_id'],
                'id'=>$campaign['campaign_id']
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
            $this->modelCampaign->delete($id);
        }
        break;
}