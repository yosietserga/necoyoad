<?php
class CronBirthday {
    
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
    
    public function __construct($registry) {
        $this->registry   = $registry;
        $this->load   = $registry->get('load');
        $this->config = $registry->get('config');
        $this->db     = $registry->get('db');
        
        $this->run();
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

    public function run() {
        $this->db->query("UPDATE ". DB_PREFIX ."customer c ".
            " SET congrats = 1 ".
            " WHERE DAY(birthday) <> '". $this->db->escape(date('d')) ."' ".
            " AND MONTH(birthday) <> '". $this->db->escape(date('m')) ."'");
        
        $query = $this->db->query("SELECT * FROM ". DB_PREFIX ."customer c ".
            " WHERE DAY(birthday) = '". $this->db->escape(date('d')) ."' ".
            " AND MONTH(birthday) = '". $this->db->escape(date('m')) ."' ".
            " AND congrats = 1");
        
        if ($query->num_rows && (int)$this->config->get('marketing_email_happy_birthday')) {
            $params = array(
                'job'=>'send_birthday',
                'newsletter_id'=>(int)$this->config->get('marketing_email_happy_birthday')
            );
            
            $this->load->library('task');
            $task = new Task($this->registry);
            
            $task->object_id        = (int)$this->config->get('marketing_email_happy_birthday');
            $task->object_type      = 'newsletter';
            $task->task             = 'happy_birthday';
            $task->type             = 'send';
            $task->time_exec        = date('Y-m-d') . ' 08:00:00';
            $task->params           = $params;
            $task->time_interval    = "";
            $task->time_last_exec   = "";
            $task->run_once         = true;
            $task->status           = 1;
            $task->date_start_exec  = date('Y-m-d') . ' 08:00:00';
            $task->date_end_exec    = date('Y-m-d') . ' 23:00:00';
            
            foreach ($query->rows as $customer) {
                $params = array(
                    'customer_id'=>$customer['customer_id'],
                    'fullname'  =>$customer['firstname'] ." ". $customer['lastname'],
                    'company'   =>$customer['company'],
                    'rif'       =>$customer['rif'],
                    'telephone' =>$customer['telephone'],
                    'email'     =>$customer['email']
                );
                $queue = array(
                    "params"    =>$params,
                    "status"    =>1,
                    "time_exec" =>date('Y-m-d') . ' 08:00:00'
                );
                $task->addQueue($queue);
                $this->db->query("UPDATE ". DB_PREFIX ."customer c SET congrats = 0 WHERE customer_id = '". (int)$customer['customer_id'] ."'");
            }
            $task->createSendTask();
        }
    }
}