<?php
/**
 * CronSeller
 * 
 * Clase que asiste en los contactos iniciales y preventas brindando
 * un vendedor automatico que cumplira con las fases establecidas en el proceso
 * de venta, viene siendo como un consultor CRM automatico
 * */
class CronSeller {
    
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
    
    public function __construct($registry) {
        $this->registry   = $registry;
        $this->load   = $registry->get('load');
        $this->mailer = $registry->get('mailer');
        $this->config = $registry->get('config');
        $this->cache  = $registry->get('cache');
        $this->db     = $registry->get('db');
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
            if (isset($task->params['job']) && $task->params['job'] == 'send_campaign') {
                $this->sendCampaign($task);
            }
        }
        /**
         * 
         * array (
         * send_campaign,               // enviar campaña de email marketing
         * send_birthday,               // enviar felicitaciones de cumpleaños a todos los clientes que cumplan año
         * send_new_products,           // enviar boletín de productos nuevos
         * send_products_of_interest,   // enviar productos de interés para el cliente
         * send_special,                // enviar boletín con los productos en ofertas
         * send_new_private_sales       // enviar boletín con las nuevas ventas privadas
         * send_open_orders             // enviar notificación con todas las órdenes que no se han concretado o pedidos abiertos
         * send_inactive_customers      // enviar notificación a los clientes que están inactivos
         * send_unapproved_customers    // enviar notificación a los clientes que están pendientes por verificación
         * )
         * - 
         * */
    }
    
    private function isLocked($job) {
        $query = $this->db->query("SELECT * FROM ". DB_PREFIX ."task_exec WHERE `type` = '". $this->db->escape($job) ."'");
        if (count($query->rows)) {
            return true;
        } else {
            return false;
        }
    }
}