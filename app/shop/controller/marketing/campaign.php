<?php

class ControllerMarketingCampaign extends Controller {

    public function index() {
        $this->trace();
        $this->redirect(HTTP_HOME);
    }

    public function trace() {
        header('Cache-Control: no-cache');
        header('Content-type: image/jpeg');
        if ($this->request->hasQuery('campaign_id')) {
            $this->load->model('marketing/campaign');
            $this->modelCampaign->trackEmail($this->request->getQuery('campaign_id'), $this->request->getQuery('contact_id'));
            if ($this->request->hasQuery('sendCampaign')) {
                $this->link2Campaign($this->request->getQuery('sendCampaign'));
            }
        }
        readfile('https://necoyoad.com/web/assets/images/data/firma_necoyoad.jpg');
    }

    public function link() {
        if ($this->request->hasQuery('campaign_id')) {
            $this->load->model('marketing/campaign');
            $this->modelCampaign->trackLink($this->request->getQuery('campaign_id'), $this->request->getQuery('contact_id'), $this->request->getQuery('link_index'));
            if ($this->request->hasQuery('sendCampaign')) {
                $this->link2Campaign($this->request->getQuery('sendCampaign'));
            }
            $redirectTo = $this->modelCampaign->getLink($this->request->getQuery('link_index'));
            if ($redirectTo) {
                $this->redirect($redirectTo);
            } else {
                $this->redirect(HTTP_HOME);
            }
        }
    }

    private function link2Campaign($campaign_id) {
        $this->load->auto('marleting/campaign');
        $this->load->auto('task');
        
        $query = $this->db->query("SELECT * FROM ". DB_PREFIX ."contact c WHERE contact_id = '". (int)$this->request->getQuery('contact_id') ."'");
        $contact_data = $query->row;
        
        $query = $this->db->query("SELECT * FROM ". DB_PREFIX ."campaign c WHERE campaign_id = '". (int)$campaign_id ."'");
        $campaign_data = $query->row;
        if ($campaign_data['newsletter_id']) {
            $campaign_data['name'] .= ' - Campaign Triggered';
            $campaign_data['date_start'] = $this->addMinute(15);
            $campaign_data['date_end'] = $this->addDay(2);
            $campaign_data['repeat'] = "";
            
            $campaign_data['contacts'][] = array(
                'contact_id'=>$contact_data['contact_id'],
                'name'=>$contact_data['name'],
                'email'=>$contact_data['email']
            );
        }
        
        $mail_server_id = $this->modelCampaign->getProperty((int)$campaign_id,'mail_server', 'mail_server_id');
        
        $campaign_id = $this->modelCampaign->add($campaign_data);
        $this->modelCampaign->setProperty($campaign_id,'mail_server', 'mail_server_id', $mail_server_id);
        $params = array(
            'job' => 'send_campaign',
            'campaign_id' => $campaign_id
        );

        $task = new Task($this->registry);

        $task->object_id = (int) $campaign_id;
        $task->object_type = 'campaign';
        $task->task = $campaign_data['name'];
        $task->type = 'send';
        $task->time_exec = date('Y-m-d H:i:s', strtotime($campaign_data['date_start']));
        $task->params = $params;
        $task->time_interval = $campaign_data['repeat'];
        $task->time_last_exec = $campaign_data['time_last_exec'];
        $task->run_once = !(bool) $campaign_data['repeat'];
        $task->status = 1;
        $task->date_start_exec = date('Y-m-d H:i:s', strtotime($campaign_data['date_start']));
        $task->date_end_exec = date('Y-m-d H:i:s', strtotime($campaign_data['date_end']));

        foreach ($campaign_data['contacts'] as $sort_order => $contact) {
            $params = array(
                'contact_id' => $contact['contact_id'],
                'name' => $contact['name'],
                'email' => $contact['email'],
                'campaign_id' => $campaign_id
            );
            $queue = array(
                "params" => $params,
                "status" => 1,
                "time_exec" => date('Y-m-d H:i:s', strtotime($campaign_data['date_start']))
            );

            $task->addQueue($queue);
        }
        $task->createSendTask();
        $this->cache->set("campaign.html.$campaign_id", $htmlbody);
    }
    
    private function addMinute($min=1) {
        $dateTime = new DateTime(date('Y-m-d h:i:s'),new DateTimeZone('America/Caracas'));
        $dateTime->add(new DateInterval('PT'. (int)$min .'M'));
        return $dateTime->format('Y-m-d h:i:s');
    }
    
    private function addDay($day=1) {
        $dateTime = new DateTime(date('Y-m-d h:i:s'),new DateTimeZone('America/Caracas'));
        $dateTime->add(new DateInterval('P'. (int)$day .'D'));
        return $dateTime->format('Y-m-d h:i:s');
    }
}
