<?php
/**
 * 1. cargar la configuracion
 * 2. cargar las librer�as necesarias
 * 3. inicializar las clases necesarias
 * 4. crear cron files para cada tipo de tarea
*/
echo "Begin cron process\n";
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'app/admin/config_cron.php');
require_once(DIR_SYSTEM . 'startup.php');

require_once dirname(__FILE__) . '/api/send.php';       //Gestiona y envia las campanas de email marketing
require_once dirname(__FILE__) . '/api/birthday.php';   //Detecta a los cumpleaneros y crea una tarea para felicitarlos
require_once dirname(__FILE__) . '/api/promoter.php';   //Detecta las visitas de los clientes, prepara newsletters y crea las tareas
/*
require_once dirname(__FILE__) . '/api/seller.php';       //Gestiona las preventas para convertirlas en ventas seguras, basado en fases o pasos predefinidos
require_once dirname(__FILE__) . '/api/bounce.php';     //Gestiona y elimina los correos basura de las cuentas configuradas
require_once dirname(__FILE__) . '/api/maintenance.php';//Gestiona y ejecuta los mantenimientos a la bd
require_once dirname(__FILE__) . '/api/seo.php';        //Evalua y corrige los alias de url y dem�s campos para el SEO
require_once dirname(__FILE__) . '/api/update.php';     //Gestiona y ejecuta las actualizaciones del core, los modulos y plantillas
require_once dirname(__FILE__) . '/api/order.php';      //Gestiona los pedidos y pagos pendientes u olvidados
require_once dirname(__FILE__) . '/api/task.php';       //Gestiona las tareas programadas de los modulos
require_once dirname(__FILE__) . '/api/report.php';     //Analiza y crea reportes de las operaciones del sitio y las envia por email
require_once dirname(__FILE__) . '/api/backup.php';     //Gestiona y ejecuta los respaldos
*/

class Cron {
    /**
     * @var $registry
     * */
    protected $registry;
    
    /**
     * @var $loader
     * */
    protected $loader;
    
    /**
     * @var $config
     * */
    protected $config;
    
    /**
     * @var $db
     * */
    protected $db;
    
    /**
     * @var $cache
     * */
    protected $cache;
    
    /**
     * @var $request
     * */
    protected $request;
    
    /**
     * @var $session
     * */
    protected $session;
    
    /**
     * @var $customer
     * */
    protected $customer;
    
    /**
     * @var $timeZone
     * */
    protected $timeZone = "America/Caracas";
    
    /**
     * @var $tasks
     * */
    private $tasks = [];
    
    public function __construct() {
        $this->registry = new Registry();
        $this->load     = new Loader($this->registry);
        $this->config   = new Config();
        $this->cache    = new Cache();
        $this->session  = new Session();
        $this->request  = new Request();
        $this->db       = new DB(DB_DRIVER, DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
        
        $this->registry->set('db', $this->db);
        $this->registry->set('load', $this->load);
        $this->registry->set('config', $this->config);
        $this->registry->set('cache', $this->cache);
        $this->registry->set('session', $this->session);
        $this->registry->set('request', $this->request);
        
        $this->load->library('customer');
        $this->customer = new Customer($this->registry);
        $this->registry->set('customer', $this->customer);
        
        $this->load->library('task');
        $this->load->library('email/mailer');
        $this->load->library('email/pop3');
        $this->load->library('email/smtp');
        $this->load->library('email/utf8');
        
        $this->mailer       = new Mailer();
        $this->smtp         = new SMTP();
        $this->utf8         = new utf8();

        $this->initConfig();
        $this->initMailer();

        $this->registry->set('mailer', $this->mailer);

        $this->cronSend     = new CronSend($this->registry);
        $this->cronBirthday = new CronBirthday($this->registry);
        $this->cronPromoter  = new CronPromoter($this->registry);
        /*
        $this->cronSale     = new CronSale($this->registry);
        $this->cronEnquiry  = new CronEnquiry($this->registry);
        $this->cronReport   = new CronReport($this->registry);
        $this->cronBackup   = new CronBackup($this->registry);
        $this->cronMaintenance = new CronMaintenance($this->registry);
        */
        
        $this->dt = new DateTime;
        $this->dt->setTimezone(new DateTimeZone($this->timeZone));
        
        $query = $this->db->query("SELECT * ".
        " FROM ". DB_PREFIX ."task ".
            " WHERE date_start_exec <= NOW() ".
            " AND time_exec <= NOW() ".
            " AND status = 1 ".
            " ORDER BY sort_order ASC, time_exec ASC");

        foreach ($query->rows as $key => $row) {
            $limit = "";

            $qry = $this->db->query("SELECT *
            FROM ". DB_PREFIX ."task_queue t
            WHERE task_id = '". (int)$row['task_id'] ."'
            AND status = 1
            ORDER BY sort_order ASC, time_exec ASC
            $limit");

            $task = new Task($this->registry);
            if ($qry->num_rows) {
                $task->task_id          = $row['task_id'];
                $task->object_id        = $row['object_id'];
                $task->object_type      = $row['object_type'];
                $task->task             = $row['task'];
                $task->type             = $row['type'];
                $task->time_exec        = $row['time_exec'];
                $task->params           = unserialize($row['params']);
                $task->time_interval    = $row['time_interval'];
                $task->time_last_exec   = $row['time_last_exec'];
                $task->run_once         = $row['run_once'];
                $task->status           = $row['status'];
                $task->sort_order       = $row['sort_order'];
                $task->date_start_exec  = $row['date_start_exec'];
                $task->date_end_exec    = $row['date_end_exec'];

                foreach ($qry->rows as $queue) {
                    $task->addQueue($queue);
                }
                $this->tasks[$task->task_id] = $task;
            } else {
                $task->setTaskDone($row['task_id']);
            }
        }
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
    
    private function initConfig() {
        //TODO: condicionar de que store obtiene la configuracion
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "setting WHERE store_id = 0");
        foreach ($query->rows as $setting) {
        	$this->config->set($setting['key'], $setting['value']);
        }
    }
    
    private function initMailer() {
        if ($this->config->get('config_smtp_method')=='smtp') {
            $this->mailer->IsSMTP();
            $this->mailer->Host = $this->config->get('config_smtp_host');
            $this->mailer->Username = $this->config->get('config_smtp_username');
            $this->mailer->Password = base64_decode($this->config->get('config_smtp_password'));
            $this->mailer->Port     = $this->config->get('config_smtp_port');
            $this->mailer->Timeout  = $this->config->get('config_smtp_timeout');
            $this->mailer->SMTPSecure = $this->config->get('config_smtp_ssl');
            $this->mailer->SMTPAuth = ($this->config->get('config_smtp_auth')) ? true : false;
         } elseif ($this->config->get('config_smtp_method')=='sendmail') {
            $this->mailer->IsSendmail();
         } else {
            $this->mailer->IsMail();
         }
    }
    
    public function run() {
        foreach ($this->tasks as $key => $task) {
            if ($task->type=='send') {
                $sendTasks[$key] = $task;
            }
            if (strpos($task->type, 'sale')) {
                $saleTasks[$key] = $task;
            }
            if (strpos($task->type, 'enquiry')) {
                $enquiryTasks[$key] = $task;
            }
            if (strpos($task->type, 'report')) {
                $reportTasks[$key] = $task;
            }
            if (strpos($task->type, 'backup')) {
                $backupTasks[$key] = $task;
            }
            if (strpos($task->type, 'maintenance')) {
                $maintenanceTasks[$key] = $task;
            }
            
        }

        $this->cronPromoter->run();
        
        if ($sendTasks) $this->cronSend->run($sendTasks);

        /*
        $this->cronReport->run();
        $this->cronBackup->run();
        $this->cronMaintenance->run();
        $this->cronSale->run();
        $this->cronEnquiry->run();
        */
    }
}

$cron = new Cron;
$cron->run();