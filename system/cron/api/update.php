<?php
require_once(dirname(__FILE__) . '/../../library/update.php');
require_once(dirname(__FILE__) . '/../../library/backup.php');
class CronUpdate {
    
    /**
     * @var $registry
     * */
    protected $registry;
    
    /**
     * @var $load
     * */
    protected $load;
    
    /**
     * @var $config
     * */
    protected $config;
    
    /**
     * @var $db
     * */
    protected $db;
    
    /**
     * @var $mailer
     * */
    protected $mailer;
    
    /**
     * @var $cache
     * */
    protected $cache;
    
    /**
     * @var $update
     * */
    protected $update;
    
    /**
     * @var $backup
     * */
    protected $backup;
    
    public function __construct($registry) {
        $this->registry   = $registry;
        $this->load   = $registry->get('load');
        $this->mailer = $registry->get('mailer');
        $this->config = $registry->get('config');
        $this->cache  = $registry->get('cache');
        $this->db     = $registry->get('db');
        $this->update = new Update($registry);
        $this->backup = new Backup($registry);
    }
    
	public function __get($key) {
		return $this->registry->get($key);
	}

	public function __set($key, $value) {
		$this->registry->set($key, $value);
	}

	public function __isset($key) {
		return $this->registry->has($key);
	}

    public function run($tasks) {
        foreach ($tasks as $key => $task) {
            if (isset($task->params['job']) && $task->params['job'] == 'update') {
                $this->runUpdate($task);
            }
        }
    }
    
    public function runUpdate($task) {
        if ($this->isLocked('update',$task->task_id)) {
            $task->addMinute(15);
        } else {
            $task->start();
            
            $this->data['update_info'] = $this->update->getInfo();
            
            if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
                $this->modelSetting->editMaintenance(1);
                $backup = new Backup($this->registry);
                $backup->run();
                $this->update->run();
                $this->modelSetting->editMaintenance(0);
            }
        
            foreach ($task->getTaskQueue() as $key => $queue) {
                if ($count >= 50) {
                    break;
                }
                
                $params = unserialize($queue['params']);
                
                $cached = $this->cache->get("campaign.html.{$params['campaign_id']}.{$params['contact_id']}");
                if ($cached) {
                    $htmlbody = html_entity_decode($cached);
                } else {
                    
                    $htmlbody = str_replace("%7B","{",$htmlbody);
                    $htmlbody = str_replace("%7D","}",$htmlbody);
                    $htmlbody = str_replace("{%contact_id%}",$params['contact_id'],$htmlbody);
                    $htmlbody = str_replace("{%campaign_id%}",$params['campaign_id'],$htmlbody);
                    $htmlbody = str_replace("{%fullname%}",$params['name'],$htmlbody);
                    $htmlbody = str_replace("{%rif%}",$params['rif'],$htmlbody);
                    $htmlbody = str_replace("{%company%}",$params['company'],$htmlbody);
                    $htmlbody = str_replace("{%email%}",$params['email'],$htmlbody);
                    $htmlbody = str_replace("{%telephone%}",$params['telephone'],$htmlbody);
                    $htmlbody = $this->prepareTemplate($htmlbody,$params);
                     
                    $dom = new DOMDocument;
                    $dom->preserveWhiteSpace = false;
                    $dom->loadHTML($htmlbody);
                    /*
                    if ($params['embed_image']) {
                        $images = $dom->getElementsByTagName('img');
                        foreach ($images as $image) {
                            $src = $image->getAttribute('src');
                            $src = str_replace(HTTP_IMAGE,DIR_IMAGE,$src);
                            if (file_exists($src)) {
                                $img    = file_get_contents($src);
                                $ext    = substr($src,(strrpos($src,'.')+1));
                                $embed  = base64_encode($img); 
                                $image->setAttribute('src',"data:image/$ext;base64,$embed");
                                $total_embed_images++;
                            }
                            $total_images++;
                        }
                    }
                    */
                    $vars = array(
                        'contact_id'=>$params['contact_id'],
                        'campaign_id'=>$params['campaign_id'],
                        'referencedBy'=>$params['email'],
                    );
                    
                    /* trace the email */
                    $trace_url  = Url::createUrl("marketing/campaign/trace",$vars,'NONSSL',HTTP_HOME);
                    $trackEmail = $dom->createElement('img');
                    $trackEmail->setAttribute('src',$trace_url);
                    $dom->appendChild($trackEmail);
                    
                    /* trace the clicks */
                    $links = $dom->getElementsByTagName('a');
                    foreach ($links as $link) {
                        $href = $link->getAttribute('href');
                        if (empty($href) || $href == "#" || strpos($href,"mailto:") || strpos($href,"callto:") || strpos($href,"skype:") || strpos($href,"tel:")) continue;
                                
                        //TODO: validar enlaces
                        //TODO: sanitizar enlaces
                                
                        $vars['link_index'] = $link_index = md5(time().mt_rand(1000000,9999999).$href);
                        $_link = Url::createUrl("marketing/campaign/link",$vars,'NONSSL',HTTP_HOME);
                        
                      	$this->db->query("INSERT INTO " . DB_PREFIX . "campaign_link SET 
                          `campaign_id` = '" . (int)$params['campaign_id'] . "',
                          `url`         = '" . $this->db->escape($_link) . "',
                          `redirect`    = '" . $this->db->escape($href) . "',
                          `link`        = '" . $this->db->escape($link_index) . "',
                          `date_added`  = NOW()");
          
                        $link->setAttribute('href',$_link);
                        
                        //TODO: agregar valor a la etiqueta title si esta vacia
                    }
                    
                    $htmlbody = html_entity_decode(htmlentities($dom->saveHTML()));
                }
                
                $this->mailer->AddAddress($params['email'],$params['name']);
                $this->mailer->IsHTML();
                $this->mailer->SetFrom($campign_info['from_email'],$campign_info['from_name']);
                $this->mailer->AddReplyTo($campign_info['replyto_email'],$campign_info['from_name']);
                $this->mailer->Subject = $campign_info['subject'];
                $this->mailer->Body = $htmlbody;
                $this->mailer->Send();
                $this->mailer->ClearAllRecipients();
                
                $task->setQueueDone($key);
                $count++;
            }
            
            if (count($task->getTaskDos($task->task_id))) {
                $task->addMinute(15);
            } else {
                $task->setTaskDone();
            }
        }
        
        $task->update();
    }
    
    private function isLocked($job,$current_task_id) {
        $query = $this->db->query("SELECT * FROM ". DB_PREFIX ."task_exec WHERE `type` = '". $this->db->escape($job) ."'");
        if (count($query->rows)) {
            $queue = $this->db->query("SELECT COUNT(*) AS total FROM ". DB_PREFIX ."task_queue WHERE `status` = '1' AND task_id = '". (int)$query->row['task_id'] ."'");
            if ($queue->row['total'] > 0) {
                if ($current_task_id == $query->row['task_id']) {
                    return false;
                } else {
                    return true;
                }
            } else {
                $queue = $this->db->query("DELETE FROM ". DB_PREFIX ."task_exec WHERE `type` = '". $this->db->escape($job) ."'");
                return false;
            }
        }
        return false;
    }
}